<?php

class Employees_PackController extends Jaycms_Controller_Action {

    private static $PACK_LIST_TABS = array();

    public function init(){
        parent::init();

        self::$PACK_LIST_TABS = array('all' => 'Alle');

        $this->view->page_title = _t("Paklijst");
        $this->view->page_sub_title = _t("Overzicht, paklijst en meer...");
        $this->view->current_module = "employees";
    }

    public function activeTabAction(){
        $session = new Zend_Session_Namespace('packs-tab');
        $session->tab = $this->_getParam('tab');
        $this->_helper->json(array('tab' => $session->tab));
    }

    public function indexAction(){
        $pages = (array) $this->_getParam('pages', array());
        $employeeId = (int) $this->_getParam('employee_id', 0);
        $per_page = 20;

        if( !Utils::user()->can('employee_pack_view') ){
            throw new Exception(_t('Access denied!'));
        }

        Utils::activity('index', 'employee-pack');

        $session = new Zend_Session_Namespace('packs-tab');
        $tab = $this->_getParam('tab');
        $tab = $tab ? $tab : $session->tab;
        $tab = $tab ? $tab : reset(array_keys(self::$PACK_LIST_TABS));

        $tabs = array();
        $cacheId = md5('packs_list_' . $tab . $per_page . implode('', $pages));
        $cache = Zend_Registry::get('cache');

        $cache->clean();
        if( ($tabs = $cache->load($cacheId)) === false ){
            $packModel = new PackModel();
            $tabs = array();

            foreach( self::$PACK_LIST_TABS as $key => $label ){
                $tabs[$key] = array( 'label' => $label, 'result' => array());
                $page = (int) array_key_exists($key, $pages) ? $pages[$key] : 0 ;
                $tabs[$key]['result'] = $packModel->findPacks($key, $employeeId, $tabs[$key]['total'], $per_page, $page);
                $tabs[$key]['page'] = $page;
                $tabs[$key]['per_page'] = $per_page;
            }

            $cache->save($tabs, null, array(), 60*2);
        }

        $employee = new Employee();
        $employees = $employee->findAll(array(), array('firstname ASC'));

        $this->view->tab = $tab;
        $this->view->tabs = $tabs;
        $this->view->employees = $employees;
        $this->view->employee_id = $employeeId;
    }

    public function viewAction(){
        $id = (int) $this->_getParam('id', 0);
        $employeeId = $this->_getParam('employee_id', '');
        $readonly = !Utils::user()->can('employee_pack_edit');

        if( !Utils::user()->can('employee_pack_view') ){
            throw new Exception(_t('Access denied!'));
        }

        $pack = new Pack($id);

        if( $pack->exists() ){
            // do nothing
            Utils::activity('view', 'employee-pack', $pack->id);
        }else{
            $pack->number = $pack->formatNumber($pack->nextNumber());
            $pack->employee_id = $employeeId ? $employeeId : 0;
            $pack->created_time = time();
            $pack->delivery_date = date('Y-m-d', time() + 7 * 24 * 60 * 60);
            $pack->save();

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_PACK;
            $log->source_id = $pack->id;
            $log->event = LogModel::EVENT_PACK_CREATED;
            $log->save();

            Utils::activity('new', 'employee-pack', $pack->id);

            $this->_redirect("/employees/pack/edit/id/" . $pack->id);
        }

        $readonly = $pack->status == PackModel::STATUS_FINAL ? true : $readonly;

        if( !$pack->products ){
            $product = new PackProduct();
            $pack->addProduct($product);
        }

        $employee = new Employee();
        $employees = $employee->findAll(array(), array('firstname ASC'));

        $this->view->pack = $pack;
        $this->view->employees = $employees;
        $this->view->readonly = $readonly;
    }

    public function pdfTestAction(){
        $this->_helper->layout()->disableLayout();
        $id = (int) $this->_getParam('id', 0);

        $pack = new Pack($id);
        if( !$pack->exists() ){
            throw new Exception(_t("Pack not found!"));
        }

        $this->view->pack = $pack;
    }

    public function newAction(){
        $this->_forward('view');
    }

    public function editAction(){
        $this->getRequest()->setParam('edit', 1);
        $this->_forward('view');
    }

    public function saveAction(){
        $packParam = $this->_getParam('pack');
        $id = (int) !empty($packParam['id']) ? $packParam['id'] : 0;
        $productParam = $this->_getParam('product', array());
        $ordParam = $this->_getParam('ord', array());

        if( !Utils::user()->can('employee_pack_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $orderedProducts = array();
        foreach( $ordParam as $ord ){
            $orderedProducts[] = $productParam[$ord];
        }


        $pack = new Pack($id);
        $pack->number = $packParam['number'];
        if( $pack->status != PackModel::STATUS_FINAL ){
            $pack->employee_id = $packParam['employee_id'];
        }
        $pack->info = $packParam['info'];
        $pack->delivery_date = date('Y-m-d', strtotime($packParam['delivery_date']));

        $newProducts = array();

        foreach( $orderedProducts as $key => $product ){
            if( $product['qty'] <= 0 && !$product['description'] ){
                continue;
            }

            $product['id'] = 0;
            $newProducts[] = (object) $product;
        }


        $products = $pack->products;

        foreach( $products as $index => $product ){
            if( array_key_exists($index, $newProducts) ){
                $newProducts[$index]->id = $product->id;
                $product = new PackProduct();
                $product->load($newProducts[$index]);
                $products[$index] = $product;
                unset($newProducts[$index]);
            }else{
                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_PACK;
                $log->source_id = $pack->id;
                $log->event = LogModel::EVENT_PACK_PRODUCT_REMOVED;
                $log->data = $product->product_id;
                $log->save();
                $product->delete();
                unset($products[$index]);
            }
        }

        foreach( $newProducts as $product ){
            $newProduct = new PackProduct();
            $newProduct->load($product);
            $products[] = $newProduct;
        }

        $pack->model()->getAdapter()->beginTransaction();

        try {
            $pack->save();

            foreach( $products as $product ){
                $product->pack_id = $pack->id;

                if( !$product->id ){
                    $log = new Log();
                    $log->source_type = LogModel::SOURCE_TYPE_PACK;
                    $log->source_id = $pack->id;
                    $log->event = LogModel::EVENT_PACK_PRODUCT_ADDED;
                    $log->data = $product->product_id;
                    $log->save();
                }

                $product->save();
            }

            $pack->model()->getAdapter()->commit();
        }catch( Exception $e ){
            $pack->model()->getAdapter()->rollBack();
            throw $e;
        }

        $this->_helper->json((object) $pack->data());
    }

    public function finalAction(){
        $id = (int) $this->_getParam('id', 0);
        $this->finalize($id);
        $this->_helper->json(array('redirect' => '/employees/pack/view/id/' . ((int) $id)));
    }

    private function finalize($id){
        $pack = new Pack($id);

        if( !$pack->exists() ){
            throw new Exception(_t("Packing list not found!"));
        }

        if( $pack->status == PackModel::STATUS_FINAL ){
            throw new Exception(_t("Packing list already final!"));
        }

        if( !$pack->employee ){
            throw new Exception(_t("Select employee before finalize packing list!"));
        }

        $pack->model()->getAdapter()->beginTransaction();

        try {
            foreach( $pack->products as $packProduct ){
                if( $packProduct->product && $packProduct->qty ){
                    $pack->employee->addStock($packProduct->product->id, $packProduct->qty, Employee::PRODUCT_TRANSIT);
                }
            }

            $pack->status = PackModel::STATUS_FINAL;
            $pack->save('status');

            $log = new Log();
            $log->source_type = LogModel::SOURCE_TYPE_PACK;
            $log->source_id = $pack->id;
            $log->event = LogModel::EVENT_PACK_FINAL;
            $log->save();

            $pack->model()->getAdapter()->commit();
        }catch(Exception $e){
            $pack->model()->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function deleteAction(){
        $id = (int) $this->_getParam('id');
        $this->delete($id);
        $this->_helper->json(array('redirect' => $this->view->baseUrl() . '/employees/pack'));
    }

    public function deleteBulkAction(){
        $packs = $this->_getParam('packs');

        foreach( $packs as $id ){
            $this->delete($id);
        }

        $this->_helper->json(array('reload' => 1));
    }

    private function delete($id){
        $pack = new Pack($id);

        if( !Utils::user()->can('employee_pack_delete') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$pack->exists() ){
            throw new Exception(_t('Pack not found!'));
        }

        /*if( $pack->status == PackModel::STATUS_FINAL ){
            throw new Exception(_t("Packing list is final you cannot delete it!"));
        }*/

        foreach( $pack->receipts as $receipt ){
            if( $receipt->invoice && $receipt->invoice->status == InvoiceModel::STATUS_FINAL ){
                throw new Exception(_t("Invoice created for this packing list you cannot delete it!"));
            }
        }

        Utils::activity('delete', 'employee-pack', $pack->id);

        $pack->model()->getAdapter()->beginTransaction();

        try {

            $pack->delete();

            foreach( $pack->receipts as $receipt ){
                $receipt->pack_id = 0;
                $receipt->save();
            }

            $pack->model()->getAdapter()->commit();
        }catch(Exception $e){
            $pack->model()->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function addRowAction(){
        $this->_helper->layout()->disableLayout();
        $index = $this->_getParam('index');

        if( !Utils::user()->can('employee_pack_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $product = new PackProduct();

        $this->view->product = $product;
        $this->view->index = $index;
        $this->renderScript('pack/_partials/pack-row.phtml');
    }

    public function productAutocompleteAction(){
        $productModel = new ProductModel();
        $products = $productModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
        $this->_helper->json($products);
    }

    public function pdfAction(){
        $id = $this->_getParam('id');
        Utils::activity('pdf', 'employee-pack', $id);
        $this->generatePDF($id, 'paklijst-' . $id . '.pdf', 'D');
        die();
    }

    protected function generatePDF($id, $name, $destination){
        require_once('MPDF/mpdf.php');
        $this->_helper->layout()->disableLayout();

        $pack = new Pack($id);

        if( !$pack->exists() ){
            throw new Exception(_t("Pack not found!"));
        }


        $this->view->pack = $pack;
        $content = $this->view->render('pack/pdf.phtml');

        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
        $mpdf->WriteHTML($content);

        $mpdf->Output($name, $destination);
    }

    public function bulkPdfAction(){
        Utils::activity('bulk-pdf', 'employee-pack');
        $packs = $this->_getParam('packs', array());

        $file = tempnam(sys_get_temp_dir(), 'paklijst-archive-');
        $name = dirname($file) . '/paklijst-archive-' . date('d-m-Y') . '.zip';
        if( !rename($file, $name) ){
            $name = $file;
        }


        $files = array();

        foreach( $packs as $id ){
            $pack = new Pack($id);

            if( !$pack->exists() ){
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
        $file = tempnam(sys_get_temp_dir(), 'paklijst-');
        $name = dirname($file) . '/paklijst-' . $id . '-' . time() . '.pdf';
        if( !rename($file, $name) ){
            $name = $file;
        }

        return $name;
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