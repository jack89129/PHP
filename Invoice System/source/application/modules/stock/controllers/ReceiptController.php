<?php

class Stock_ReceiptController extends Jaycms_Controller_Action {

    private static $RECEIPT_LIST_TABS = array();

    public function init(){
        parent::init();

        self::$RECEIPT_LIST_TABS = array('all' => 'Alle');

        $this->view->page_title = _t("Voorraadbeheer");
        $this->view->page_sub_title = _t("Overzicht, voorraden en meer...");
        $this->view->current_module = "stock";
    }

    public function activeTabAction(){
        $session = new Zend_Session_Namespace('receipts-tab');
        $session->tab = $this->_getParam('tab');
        $this->_helper->json(array('tab' => $session->tab));
    }

    public function indexAction(){
        $pages = (array) $this->_getParam('pages', array());
        $proforma = (int) $this->getRequest()->getParam('proforma', 0);
        $employeeId = (int) $this->_getParam('employee_id', 0);
        $contactId = (int) $this->_getParam('contact_id', 0);
        $per_page = 20;

        if( !Utils::user()->can('stock_receipt_view') ){
            throw new Exception(_t('Access denied!'));
        }

        Utils::activity('index', 'stock-receipt');

        $session = new Zend_Session_Namespace('receipts-tab');
        $tab = $this->_getParam('tab');
        $tab = $tab ? $tab : $session->tab;
        $tab = $tab ? $tab : reset(array_keys(self::$RECEIPT_LIST_TABS));

        $tabs = array();
        $cacheId = md5('receipts_list_' . $tab . $per_page . implode('', $pages));
        $cache = Zend_Registry::get('cache');

        $cache->clean();
        if( ($tabs = $cache->load($cacheId)) === false ){
            $receiptModel = new ReceiptModel();
            $tabs = array();

            foreach( self::$RECEIPT_LIST_TABS as $key => $label ){
                $tabs[$key] = array( 'label' => $label, 'result' => array());
                $page = (int) array_key_exists($key, $pages) ? $pages[$key] : 0 ;
                $tabs[$key]['result'] = $receiptModel->findReceipts($key, $employeeId, $contactId, $tabs[$key]['total'], $per_page, $page);
                $tabs[$key]['page'] = $page;
                $tabs[$key]['per_page'] = $per_page;
            }

            $cache->save($tabs, null, array(), 60*2);
        }

        $contact = new Contact();
        $contacts = $contact->findAll(array(), array('firstname ASC'));

        $employee = new Employee();
        $employees = $employee->findAll(array(), array('firstname ASC'));

        $this->view->tab = $tab;
        $this->view->tabs = $tabs;
        $this->view->employees = $employees;
        $this->view->contacts = $contacts;
        $this->view->employee_id = $employeeId;
        $this->view->contact_id = $contactId;
    }

    public function viewAction(){
        $id = (int) $this->_getParam('id', 0);
        $contactId = $this->_getParam('contact_id', '');
        $readonly = $this->_getParam('read_only') || !Utils::user()->can('stock_receipt_edit');

        if( !Utils::user()->can('stock_receipt_view') ){
            throw new Exception(_t('Access denied!'));
        }

        $receipt = new Receipt($id);

        if( $receipt->exists() ){
            // do nothing
            Utils::activity('view', 'stock-receipt', $receipt->id);
        }else{
            $receipt->number = $receipt->formatNumber($receipt->nextNumber());
            $receipt->contact_id = $contactId ? $contactId : 0;
            $receipt->created_time = time();
            $receipt->created_by = Utils::user()->id;
            $receipt->delivery_date = date('Y-m-d', time() + 7 * 24 * 60 * 60);
            $receipt->save();

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_RECEIPT;
            $log->source_id = $receipt->id;
            $log->event = LogModel::EVENT_RECEIPT_CREATED;
            $log->save();

            Utils::activity('new', 'stock-receipt', $receipt->id);

            $this->_redirect("/stock/receipt/edit/id/" . $receipt->id);
        }

        $readonly = $receipt->status == ReceiptModel::STATUS_FINAL ? true : $readonly;

        if( !$receipt->products ){
            $product = new ReceiptProduct();
            $receipt->addProduct($product);
        }

        $contacts = new Contact();
        $contacts = $contacts->findAll(array(), array('firstname ASC'));

        $employee = new Employee();
        $employees = $employee->findAll(array(), array('firstname ASC'));

        $this->view->receipt = $receipt;
        $this->view->employees = $employees;
        $this->view->contacts = $contacts;
        $this->view->readonly = $readonly;
    }

    public function contactAutocompleteAction(){
        $contactModel = new ContactModel();
        $contacts = $contactModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
        $this->_helper->json($contacts);
    }

    public function pdfTestAction(){
        $this->_helper->layout()->disableLayout();
        $id = (int) $this->_getParam('id', 0);

        $receipt = new Receipt($id);
        if( !$receipt->exists() ){
            throw new Exception(_t("Receipt not found!"));
        }

        $this->view->receipt = $receipt;
    }

    public function newAction(){
        $this->_forward('view');
    }

    public function editAction(){
        $this->getRequest()->setParam('edit', 1);
        $this->_forward('view');
    }

    public function saveAction(){
        $receiptParam = $this->_getParam('receipt');
        $id = (int) !empty($receiptParam['id']) ? $receiptParam['id'] : 0;
        $productParam = $this->_getParam('product', array());
        $contactParam = $this->_getParam('contact', array());
        $ordParam = $this->_getParam('ord', array());

        if( !Utils::user()->can('stock_receipt_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $orderedProducts = array();
        foreach( $ordParam as $ord ){
            $orderedProducts[] = $productParam[$ord];
        }


        $receipt = new Receipt($id);
        $receipt->number = $receiptParam['number'];
        $receipt->delivery_date = date('Y-m-d', strtotime($receiptParam['delivery_date']));

        if( $receipt->status != ReceiptModel::STATUS_FINAL ){
            $receipt->contact_id = $receiptParam['contact_id'];
            $receipt->employee_id = $receiptParam['employee_id'];
        }

        $receipt->info = $receiptParam['info'];

        $newProducts = array();

        foreach( $orderedProducts as $key => $product ){
            if( $product['qty'] <= 0 && !$product['description'] ){
                continue;
            }

            $product['id'] = 0;
            $newProducts[] = (object) $product;
        }


        $products = $receipt->products;

        foreach( $products as $index => $product ){
            if( array_key_exists($index, $newProducts) ){
                $newProducts[$index]->id = $product->id;
                $product = new ReceiptProduct();
                $product->load($newProducts[$index]);
                $products[$index] = $product;
                unset($newProducts[$index]);
            }else{
                $product->delete();
                unset($products[$index]);
            }
        }

        foreach( $newProducts as $product ){
            $newProduct = new ReceiptProduct();
            $newProduct->load($product);
            $products[] = $newProduct;
        }

        $receipt->model()->getAdapter()->beginTransaction();

        try {

            if( $this->_getParam('save_contact') ){
                $contact = new Contact();
                $contact->load($contactParam);
                $contact->id = $receipt->contact_id ? $receipt->contact_id : null ;
                $contact->save();
                $receipt->contact_id = $contact->id;
            }

            $receipt->save();

            foreach( $products as $product ){
                $product->receipt_id = $receipt->id;
                $product->save();
            }

            $receipt->model()->getAdapter()->commit();
        }catch( Exception $e ){
            $receipt->model()->getAdapter()->rollBack();
            throw $e;
        }

        $this->_helper->json((object) $receipt->data());
    }

    public function finalAction(){
        $id = (int) $this->_getParam('id', 0);
        $this->finalize($id);
        $this->_helper->json(array('redirect' => '/stock/receipt/view/id/' . $id));
    }

    private function finalize($id){
        $receipt = new Receipt($id);

        if( !$receipt->exists() ){
            throw new Exception(_t("Receipt not found!"));
        }

        if( $receipt->status == ReceiptModel::STATUS_FINAL ){
            throw new Exception(_t("Receipt already final!"));
        }

        if( !$receipt->employee ){
            throw new Exception(_t("Choose employee before finalize receipt!"));
        }

        $receipt->model()->getAdapter()->beginTransaction();

        try {

            foreach( $receipt->products as $receiptProduct ){
                if( !$receiptProduct->product || !$receiptProduct->qty ){
                    continue;
                }

                $receipt->employee->addStock($receiptProduct->product->id, $receiptProduct->qty, Employee::PRODUCT_RESERVATION);
            }

            $receipt->status = ReceiptModel::STATUS_FINAL;
            $receipt->save('status');

            $receipt->model()->getAdapter()->commit();
        }catch( Exception $e ){
            $receipt->model()->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function deleteAction(){
        $id = (int) $this->_getParam('id');
        $this->delete($id);
        $this->_helper->json(array('redirect' => $this->view->baseUrl() . "/stock/receipt"));
    }

    public function deleteBulkAction(){
        $receipts = $this->_getParam('receipts');

        foreach( $receipts as $id ){
            $this->delete($id);
        }

        $this->_helper->json(array('reload' => 1));
    }

    private function delete($id){
        $receipt = new Receipt($id);

        if( !Utils::user()->can('stock_receipt_delete') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$receipt->exists() ){
            throw new Exception(_t('Receipt not found!'));
        }

        /*if( $receipt->status == ReceiptModel::STATUS_FINAL ){
            throw new Exception(_t("Receipt is final you cannot delete it!"));
        }*/

        Utils::activity('delete', 'stock-receipt', $receipt->id);

        $receipt->model()->getAdapter()->beginTransaction();

        try {

            foreach( $receipt->products as $receiptProduct ){
                if( !$receiptProduct->product || !$receiptProduct->qty ){
                    continue;
                }

                if ( !empty($receipt->employee) )
                    $receipt->employee->addStock($receiptProduct->product->id, -$receiptProduct->qty, Employee::PRODUCT_RESERVATION);
            }

            $receipt->delete();

            $receipt->model()->getAdapter()->commit();
        }catch(Exception $e){
            $receipt->model()->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function addRowAction(){
        $this->_helper->layout()->disableLayout();
        $index = $this->_getParam('index');

        if( !Utils::user()->can('stock_receipt_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $product = new ReceiptProduct();

        $this->view->product = $product;
        $this->view->index = $index;
        $this->renderScript('receipt/_partials/receipt-row.phtml');
    }

    public function productAutocompleteAction(){
        $productModel = new ProductModel();
        $products = $productModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
        $this->_helper->json($products);
    }

    public function pdfAction(){
        $id = $this->_getParam('id');
        Utils::activity('pdf', 'stock-receipt', $id);
        $this->generatePDF($id, 'bestel-bon-' . $id . '.pdf', 'D');
        die();
    }

    protected function generatePDF($id, $name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $receipt = new Receipt($id);

        if( !$receipt->exists() ){
            throw new Exception(_t("Receipt not found!"));
        }


        $this->view->receipt = $receipt;
        $content = $this->view->render('receipt/pdf.phtml');

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
        $mpdf->WriteHTML($content);

        $mpdf->Output($name, $destination);
    }

    public function bulkPdfAction(){
        Utils::activity('bulk-pdf', 'stock-receipt');
        $receipts = $this->_getParam('receipts', array());

        $file = tempnam(sys_get_temp_dir(), 'bestel-bon-archive-');
        $name = dirname($file) . '/bestel-bon-archive-' . date('d-m-Y') . '.zip';
        if( !rename($file, $name) ){
            $name = $file;
        }


        $files = array();

        foreach( $receipts as $id ){
            $receipt = new Receipt($id);

            if( !$receipt->exists() ){
                continue;
            }

            $pdfFilename = $this->pdfName($id);
            $this->generatePDF($id, $pdfFilename, 'F');
            $files[] = $pdfFilename;
        }

        $zip = new ZipArchive();

        if( $zip->open($name, ZIPARCHIVE::CREATE) !== true ) {
            throw new Exception(_t("Can't create archive!"));
        }

        foreach ($files as $file) {
            if( !$zip->addFile($file, basename($file)) ){
                throw new Exception(_t("Can't add file to archive!"));
            }
        }

        $zip->close();

        if( headers_sent() ){
            throw new Exception(_t("Can't send archive. Headers already sent!"));
        }

        header('Content-Type: application/zip');
        header('Content-Length: ' . filesize($name));
        header('Content-Disposition: attachment; filename=' . basename($name));

        fpassthru(fopen($name, 'rb'));
        die();
    }

    protected function pdfName($id){
        $file = tempnam(sys_get_temp_dir(), 'bestel-bon-');
        $name = dirname($file) . '/bestel-bon-' . $id . '-' . time() . '.pdf';
        if( !rename($file, $name) ){
            $name = $file;
        }

        return $name;
    }

    public function invoiceAction(){
        $id = (int) $this->_getParam('id', 0);
        $receipts = (array) $this->_getParam('receipts', array());

        if( $id ){
            $receipts[] = $id;
        }

        $id = Receipt::receiptsToInvoice($receipts);
        $this->_helper->json(array('redirect' => $this->view->baseUrl() . "/invoices/index/edit/id/" . $id));
    }

    public function packAction(){
        $id = (int) $this->_getParam('id', 0);
        $receipts = (array) $this->_getParam('receipts', array());

        if( $id ){
            $receipts[] = $id;
        }

        $id = Receipt::receiptsToPack($receipts);
        $this->_helper->json(array('redirect' => $this->view->baseUrl() . "/employees/pack/edit/id/" . $id));
    }
    
	public function preDispatch() {
		$results = Product::all(array(array('min_stock > ?', 0), array('min_stock >= stock',''), array('deleted = ?', 0)));
		
		if ($results) {
			$products = array();
			$minstock_products = new Zend_Session_Namespace('min_stock_products');
			if (!$minstock_products->id_list) $minstock_products->id_list = array();
			
			foreach ($results as $product) {
				if (in_array($product->id, $minstock_products->id_list)) continue;
				$products[] = $product;
			}
			
			if ($products) $this->view->show_box = true;
			
			$this->view->minstock_products = $results;
		}
	}
}