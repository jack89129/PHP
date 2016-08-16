<?php

    class Purchases_IndexController extends Jaycms_Controller_Action {

        private static $PURCHASE_LIST_TABS = array();
        private static $PURCHASE_TOTALS = array();

        public function init(){
            parent::init();

            self::$PURCHASE_LIST_TABS = array(  'all' => _t('Alle inkoopfacturen'),
                                                'outstanding' => _t('Openstaand'),
                                                'late' => _t('Te laat'),
                                                'paid' => _t('Betaald')
                                             );

            self::$PURCHASE_TOTALS['outstanding'] = _t('Openstaand');
            self::$PURCHASE_TOTALS['paid'] = _t('Betaald');
            self::$PURCHASE_TOTALS['late'] = _t('Verlopen');

            $this->view->assign('page_title', _t("Inkoop"));
            $this->view->assign('page_sub_title', _t("Overzicht, inkoopfacturen en meer..."));
            $this->view->assign('current_module', "purchases");
        }

        public function activeTabAction(){
            $session = new Zend_Session_Namespace('purchases-tab');

            if( !array_key_exists($this->_getParam('tab'), self::$PURCHASE_LIST_TABS) ){
                throw new Exception(_t('Tab does not exists!'));
            }

            $session->tab = $this->_getParam('tab');
            $this->_helper->json(array('tab' => $session->tab));
        }

        public function indexAction(){
            $contactId = (int) $this->_getParam('contact_id');
            $pages = (array) $this->_getParam('pages', array());
            $date_from =  strtotime($this->_getParam('date_from', 0));
            $date_to =  strtotime($this->_getParam('date_to', 0));
            $date = (string) $this->_getParam('date', '');
            $per_page = 20;

            Utils::activity('index', 'purchase');

            if( !Utils::user()->can('purchase_view') ){
                throw new Exception(_t('Access denied!'));
            }


            if( $date ){
                list($date_from, $date_to) = Utils::name2date($date);
            }

            $session = new Zend_Session_Namespace('purchases-tab');
            $tab = $this->_getParam('tab');
            $tab = $tab ? $tab : $session->tab;
            $tab = $tab ? $tab : reset(array_keys(self::$PURCHASE_LIST_TABS));

            $contactModel = new WholesalerModel();
            $contacts = $contactModel->findAll();

            $cacheId = md5('purchases_list_' . $tab . $per_page . implode('', $pages));
            $cache = Zend_Registry::get('cache');
            $totals = array();

            $cache->clean();
            if( ($tabs = $cache->load($cacheId)) === false ){
                $purchaseModel = new PurchaseModel();
                $tabs = array();

                foreach( self::$PURCHASE_LIST_TABS as $key => $label ){
                    $tabs[$key] = array( 'label' => $label, 'result' => array(), 'sum' => 0, 'sum_no_vat' => 0);
                    $page = (int) array_key_exists($key, $pages) ? $pages[$key] : 0 ;
                    $tabs[$key]['result'] = $purchaseModel->findPurchases($key, $contactId, $date_from, $date_to, $tabs[$key]['total'], $tabs[$key]['sum'], $tabs[$key]['sum_no_vat'], $per_page, $page);
                    $tabs[$key]['page'] = $page;
                    $tabs[$key]['per_page'] = $per_page;
                }

                foreach( self::$PURCHASE_TOTALS as $key => $label ){
                    if( array_key_exists($key, $tabs) ){
                        $totals[$key] = array('label' => $label, 'sum' => $tabs[$key]['sum'], 'sum_no_vat' => $tabs[$key]['sum_no_vat']);
                    }
                }

                $cache->save($tabs, null, array(), 60*2);
            }             
            
            $purchase_curnum = SettingsModel::getPurchaseCurrentNum();
            $result = $purchaseModel->fetchAll();
            $is_new = false;
            if ( $purchase_curnum == 1 && count($result) == 0 ) $is_new = true;

            $this->view->is_new = $is_new;
            $this->view->assign('contacts', $contacts);
            $this->view->assign('tab', $tab);
            $this->view->assign('tabs', $tabs);
            $this->view->assign('totals', $totals);
            $this->view->assign('date_from', $date_from ? date(Constants::DATE_FORMAT, $date_from) : '');
            $this->view->assign('date_to', $date_to ? date(Constants::DATE_FORMAT, $date_to) : '');
        }


        public function viewAction(){

            $id = (int) $this->_getParam('id');

            $purchase = new Purchase($id);

            if( !$purchase->exists() ){
                throw new Exception(_t("Purchase not found!"));
            }

            if( !Utils::user()->can('offer_view') ){
                throw new Exception(_t("Access denied!"));
            }

            Utils::activity('view', 'purchase', $purchase->id);

            $this->view->assign('purchase', $purchase);
            $this->view->assign('hltc', (int) $this->_getParam('hltc', -1));
        }

        public function pdfAction(){
            $id = $this->_getParam('id');
            $purchase = new Purchase($id);
            Utils::activity('pdf', 'purchase', $id);
            $this->generatePDF($id, $purchase->number . '.pdf', 'D');
            die();
        }

        protected function pdf($id){
            $name = $this->pdfName($id);
            $this->generatePDF($id, $name, 'F');
            return $name;
        }

        protected function pdfName($id){
            $purchase = new Purchase($id);                 
            $name = dirname($file) . '/' . $purchase->number . '.pdf';
            if( !rename($file, $name) ){
                $name = $file;
            }

            return $name;
        }

        protected function generatePDF($id, $name, $destination){
            require_once('MPDF/mpdf.php');
            $this->_helper->layout()->disableLayout();

            $purchase = new Purchase($id);

            if( !$purchase->exists() ){
                throw new Exception(_t("Purchase not found!"));
            }

            $this->view->assign('purchase', $purchase);

            $content = $this->view->render('index/pdf.phtml');

            $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 30, 9, 9, 'P');
            $mpdf->WriteHTML($content);

            $mpdf->Output($name, $destination);
        }

        public function newAction()
        {
            $id = (int) $this->_getParam('id');
            $contact_id = (int) $this->_getParam('contact_id');

            $purchase = new Purchase($id);
            $products = new Product();
            $products = $products->findAll(array(array('deleted = ?', '0')));
            $contacts = new Wholesaler();
            $contacts = $contacts->findAll();

            if( !Utils::user()->can('purchase_edit') ){
                throw new Exception(_t("Access denied!"));
            }

            if( $purchase->exists() ){
                Utils::activity('edit', 'purchase', $purchase->id);

            }else{
                $purchase->contact_id = $contact_id;
                $purchase->create();

                Utils::activity('new', 'purchase', $purchase->id);
                $this->_redirect('/purchases/index/edit/id/' . $purchase->id);
            }

            if( !$purchase->products ){
                $product = new PurchaseProduct();
                $purchase->addProduct($product);
            }

            $this->view->assign('purchase', $purchase);
            $this->view->assign('contacts', $contacts);
            $this->view->assign('products', $products);
        }

        public function contactChangedAction(){
            $id = $this->_getParam('id');
            $number = _t('Kies crediteur');
            $contact = new Wholesaler($id);

            if( $contact->exists() ){
                $number = $contact->number;
            }

            $this->_helper->json(array('number'=>$number));
        }

        public function productChangedAction(){
            $id = $this->_getParam('id');
            $index = $this->_getParam('index');

            $product = new Product($id);

            if( !$product->exists() ){
                throw new Exception(_t("Product not found!"));
            }

            $this->_helper->json(array('product' => (object) $product->data(), 'index' => $index));
        }

        public function addRowAction(){
            $this->_helper->layout()->disableLayout();
            $index = $this->_getParam('index');
            $purchaseProduct = new PurchaseProduct();

            $products = new Product();
            $products = $products->findAll(array(array('deleted=?', 0)));

            $this->view->assign('product', $purchaseProduct);
            $this->view->assign('products', $products);
            $this->view->assign('purchase_row_index', $index);
            $this->renderScript('index/new/purchase-row.phtml');
        }
        
        public function permissionUpdateAction(){
            $pm = SettingsModel::getPermissionPurchase();
            $pm = $pm ? 0 : 1;
            $settingModel = new SettingsModel();
            $settingModel->setSetting(SettingsModel::getPermissionPurchaseKey(), $pm);
            $result = array();
            $result['message'] = $pm ? "U heeft nu een betaling gesimuleerd voor de inkoop module! U kunt deze nu gebruiken!" : "U heeft nu de stopzetting van deze module gesimuleerd! Deze kunt u nu niet meer gebruiken!";
            $result['button_text'] = $pm ? "Module deactiveren" : "Module activeren";
            $this->_helper->json($result);
        }

        public function productAutocompleteAction(){
            $productModel = new ProductModel();
            $products = $productModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
            $this->_helper->json($products);
        }

        public function categoryAutocompleteAction(){
            $tagModel = new TagModel();
            $tags = $tagModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'), TagCategoryModel::TYPE_INVOICE);
            $this->_helper->json($tags);
        }

        public function saveAction(){
            $purchaseParam = $this->_getParam('purchase');
            $purchaseParam['invoice_time'] = date(Constants::MYSQL_DAY_FORMAT, strtotime($purchaseParam['invoice_time']));
            $purchaseParam['expire_time'] = date(Constants::MYSQL_DATE_FORMAT, strtotime($purchaseParam['expire_time']));
            $vatIncludedParam = $this->_getParam('vat_included', 0) ? true : false ;
            $productParam = $this->_getParam('product', array());
            $contactParam = $this->_getParam('contact', array());
            $ordParam = $this->_getParam('ord', array());

            $orderedProducts = array();
            foreach( $ordParam as $ord ){
                $orderedProducts[] = $productParam[$ord];
            }


            $purchase = new Purchase($purchaseParam['id']);

            $purchase->load($purchaseParam);
            $purchase->total_sum = 0;
            $purchase->vat_sum = 0;

            $newProducts = array();

            $purchase_total_excl_vat = 0.0;
            $purchase_total_incl_vat = 0.0;

            foreach( $orderedProducts as $product ){
                $product['price'] = str_replace('.', '', $product['price']);
                $product['price'] = str_replace(',', '.', $product['price']);
                $product['qty'] = str_replace('.', '', $product['qty']);
                $product['qty'] = str_replace(',', '.', $product['qty']);
                if( $product['qty'] < 1 || !$product['description'] ){
                    continue;
                }

                if( $vatIncludedParam ){
                    $product['price'] = Utils::removeVAT($product['price'], $product['vat']);
                }

                $product['id'] = 0;
                $product['total_sum'] = $product['qty'] * $product['price'];

                $purchase_total_incl_vat += $product['total_sum'] + $product['total_sum'] * ($product['vat']/100);
                $purchase_total_excl_vat += $product['total_sum'] - $product['total_sum'] * ($product['discount']/100) ;

                $purchase->vat_sum += $product['total_sum'] * ($product['vat']/100);
                $purchase->discount_sum += $product['total_sum'] * ($product['discount']/100);

                $product['total_sum'] -= $product['total_sum'] * ($product['discount']/100);
                $product['description'] = Utils::strip_bad_tags($product['description']);
                $product['description'] = str_replace('&nbsp;', '', $product['description']);
                $product['description'] = str_replace('<br>', '', $product['description']);

                $newProducts[] = (object) $product;
            }

            $purchase->discount_sum += $purchase_total_excl_vat * ($purchase->discount/100);
            $purchase->total_sum = $purchase_total_incl_vat - $purchase->discount_sum;
            $purchase->total_excl_vat = $purchase_total_excl_vat;

            $products = $purchase->products;

            foreach( $products as $index => $product ){
                if( array_key_exists($index, $newProducts) ){
                    $newProducts[$index]->id = $product->id;
                    $product = new PurchaseProduct();
                    $product->load($newProducts[$index]);
                    $products[$index] = $product;
                    unset($newProducts[$index]);
                }else{
                    $product->delete();
                    unset($products[$index]);
                }
            }

            foreach( $newProducts as $product ){
                $newProduct = new PurchaseProduct();
                $newProduct->load($product);
                $products[] = $newProduct;
            }


            $purchase->model()->getAdapter()->beginTransaction();

            try {

                if( ($this->_getParam('save_contact') && $purchase->contact_id) || !$purchase->contact_id ){                 
                    $contact = new Wholesaler($purchase->contact_id);
                    $contact->load($contactParam);
                    $contact->id = $purchase->contact_id ? $purchase->contact_id : null ;
                    $contact->save();
                    $purchase->contact_id = $contact->id;
                }             

                $purchase->save();

                foreach( $products as $product ){
                    $product->purchase_id = $purchase->id;
                    $product->save();
                }

                $purchase->model()->getAdapter()->commit();
            }catch( Exception $e ){
                $purchase->model()->getAdapter()->rollBack();
                throw $e;
            }

            $this->_helper->json((object) $purchase->data());
        }

        public function editAction(){
            $this->_forward('new');
        }

        public function duplicateAction(){
            $id = $this->_getParam('id');
            $purchase = $this->duplicate($id);
            $this->_helper->json(array('redirect' => $this->view->baseUrl() . '/purchases/index/new/id/' . $purchase->id));
        }

        protected function duplicate($id){
            $purchase = new Purchase($id);

            if( !$purchase->exists() ){
                throw new Exception(_t("Purchase not found!"));
            }

            Utils::activity('duplicate', 'purchase', $purchase->id);

            $purchaseProduct = new PurchaseProduct();
            $purchaseProducts = $purchaseProduct->findAll(array(array('purchase_id = ?', $purchase->id)));

            $purchase->model()->getAdapter()->beginTransaction();

            try {

                $purchase->id = null;
                $purchase->number = null;
                $purchase->paid_time = '0000-00-00 00:00:00';
                $purchase->create();

                foreach( $purchaseProducts as $product ){
                    $product->id = null;
                    $product->purchase_id = $purchase->id;
                    $product->save();
                }

                $purchase->model()->getAdapter()->commit();
            }catch(Exception $e){
                $purchase->model()->getAdapter()->rollBack();
                throw $e;
            }

            return $purchase;
        }

        public function deleteAction(){
            $id = $this->_getParam('id');
            $this->delete($id);
            $this->_helper->json(array('redirect' => '/purchases/'));
        }

        protected function delete($id){

            $purchase = new Purchase($id);

            if( !$purchase->exists() ){
                throw new Exception(_t("Purchase not found!"));
            }

            if( !Utils::user()->can('purchase_delete') ){
                throw new Exception(_t('Access denied!'));
            }

            /*if( ((int) $purchase->paid_time) ){
                throw new Exception(_t("You can't delete paid purchase!"));
            }*/

            Utils::activity('delete', 'purchase', $purchase->id);

            $purchaseProduct = new PurchaseProduct();
            $purchaseProducts = $purchaseProduct->findAll(array(array('purchase_id = ?', $purchase->id)));


            $purchase->model()->getAdapter()->beginTransaction();

            try {

                foreach( $purchaseProducts as $product ){
                    $product->delete();
                }

                $purchase->delete();
                $purchase->model()->getAdapter()->commit();

            }catch(Exception $e){
                $purchase->model()->getAdapter()->rollBack();
                throw $e;
            }
        }

        public function bulkPdfAction(){
            Utils::activity('bulk-pdf', 'purchase');
            $purchases = $this->_getParam('purchases', array());

            $file = tempnam(sys_get_temp_dir(), 'inkoop-archive-');
            $name = dirname($file) . '/inkoop-archive-' . date('d-m-Y') . '.zip';
            if( !rename($file, $name) ){
                $name = $file;
            }


            $files = array();

            foreach( $purchases as $id ){
                $purchase = new Purchase($id);

                if( !$purchase->exists() ){
                    continue;
                }

                $pdfFilename = $this->pdfName($id);
                $this->generatePDF($id, $pdfFilename, 'F');
                $files[$purchase->number . '.pdf'] = $pdfFilename;

                $index = 0;
                foreach( $purchase->attachments as $attachment ){
                    $index++;
                    $files[ $purchase->number . '_BIJLAGE' . $index . '_' . pathinfo(preg_replace('@[^a-z0-9\.\-\_]@i', '', $attachment->name), PATHINFO_FILENAME) . '.' . pathinfo($attachment->toPath(), PATHINFO_EXTENSION)] = $attachment->toPath();
                }
            }

            $zip = new ZipArchive();

            if( $zip->open($name, ZIPARCHIVE::CREATE) !== true ) {
                throw new Exception(_t("Can't create archive!"));
            }

            foreach ($files as $filename => $file) {
                if( !$zip->addFile($file, $filename) ){
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

        public function bulkDuplicateAction(){
            $purchases = $this->_getParam('purchases', array());

            foreach( $purchases as $id ){
                $this->duplicate($id);
            }

            $this->_helper->json(array('reload' => 1));
        }

        public function bulkDeleteAction(){
            $purchases = $this->_getParam('purchases', array());

            foreach( $purchases as $id ){
                $this->delete($id);
            }

            $this->_helper->json(array('reload' => 1));
        }

        public function validateAttachmentFileAction(){
            $filename = $this->_getParam('filename');

            if( !PurchaseAttachment::isAllowed($filename) ){
                throw new Exception(_t("File type not allowed!"));
            }

            $this->_helper->json(array('success'=>1));
        }

        public function createAttachmentPurchaseAction(){
            $purchaseId = (int) $this->_getParam('id', 0);

            $purchase = new Purchase($purchaseId);                  
            if( !$purchase->exists() ){
                $purchase->create();
            }

            $purchase->addAttachment($_FILES['attachment']);                  

            Utils::activity('attachment', 'purchase', $purchase->id);

            $response = array();
            $response['success'] = 1;
            $response['message'] = _t('Succes!');
            echo '<div id="response">' . Zend_Json::encode($response) . '</div>';
            die();
        }

        public function paymentDialogPurchasesListAction(){
            $purchasesParam = $this->_getParam('purchases');

            if( !$purchasesParam ){
                $purchaseModel = new PurchaseModel();
                $purchasesParam = array();
                $purchases = $purchaseModel->findUnpaidPurchases();

                foreach( $purchases as $purchase ){
                    $purchasesParam[] = $purchase->id;
                }
            }

            $purchases = array();
            foreach( $purchasesParam as $id ){
                $purchase = new Purchase($id);

                if( !$purchase->exists() || $purchase->paid_time ){
                    continue;
                }

                $purchases[] = $purchase;
            }

            $result = array();
            $purchase = reset($purchases);
            $result['purchases_list'] = $this->view->partial('index/_partials/purchase-payment/purchases-list.phtml', array('purchases' => $purchases, 'purchase' => $purchase));
            $result['purchase'] = $purchase ? $this->view->partial('index/_partials/purchase-payment/purchase.phtml', array('purchase' => $purchase)) : '';
            $this->_helper->json($result);
        }

        public function paymentDialogPurchaseLoadAction(){
            $id = (int) $this->_getParam('id', 0);

            $purchase = new Purchase($id);

            if( !$purchase->exists() ){
                throw new Exception(_t('Purchase not found!'));
            }

            if( $purchase->paid_time ){
                throw new Exception(_t('Purchase already paid!'));
            }

            $result = array();
            $result['purchase'] = $this->view->partial('index/_partials/purchase-payment/purchase.phtml', array('purchase' => $purchase));
            $this->_helper->json($result);
        }

        public function purchasePaymentPayAction(){
            $purchaseParam = $this->_getParam('purchase');
            $paymentParam = $this->_getParam('payment');

            $purchase = new Purchase($purchaseParam['id']);

            if( !$purchase->exists() ){
                throw new Exception(_t("Purchase not found!"));
            }                                                          

            try {
                $purchase->addPayment($paymentParam['amount'], strtotime($paymentParam['paid_time']), $paymentParam['payment_method']);
                Utils::activity('payment', 'purchase', $purchase->id);   
            }catch(Exception $e){                                 
                throw $e;
            }

            $this->_helper->json(array('success' => 1));
        }

        public function contactAutocompleteAction(){
            $contactModel = new WholesalerModel();
            $contacts = $contactModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'), $this->_getParam('field'));
            $this->_helper->json($contacts);
        }

        public function attachmentsReloadAction(){
            $id = (int) $this->_getParam('id', 0);
            $purchase = new Purchase($id);

            if( !$purchase->exists() ){
                throw new Exception(_t('Purchase not found!'));
            }

            $result = array();
            $result['attachments'] = $this->view->partial('index/_partials/purchase-attachment-sidebar.phtml', array('purchase' => $purchase));
            $this->_helper->json($result);
        }

        public function attachmentEditAction(){
            $id = (int) $this->_getParam('id', 0);
            $purchaseAttachment = new PurchaseAttachment($id);

            if( !$purchaseAttachment->exists() ){
                throw new Exception(_t("Purchase attachment not found!"));
            }

            $result = array();
            $result['id'] = $purchaseAttachment->id;
            $result['attachment_edit'] = $this->view->partial('index/_partials/purchase-attachment-edit.phtml', array('attachment' => $purchaseAttachment));
            $this->_helper->json($result);
        }

        public function attachmentSaveAction(){
            $id = (int) $this->_getParam('id', 0);
            $name = $this->_getParam('name', '');
            $purchaseAttachment = new PurchaseAttachment($id);

            if( !$purchaseAttachment->exists() ){
                throw new Exception(_t("Purchase attachment not found!"));
            }

            if( !$name ){
                throw new Exception(_t('Fill name field!'));
            }

            if( $purchaseAttachment->name != $name ){
                $log = new Log();
                $log->source_type = LogModel::SOURCE_TYPE_PURCHASE;
                $log->source_id = $purchaseAttachment->purchase_id;
                $log->data = $name;
                $log->event = LogModel::EVENT_PURCHASE_ATTACHMENT_EDIT;
                $log->save();
            }

            $purchaseAttachment->name = $name;
            $purchaseAttachment->changeFilename($name);
            $purchaseAttachment->save();
            $this->_helper->json(array('success' => '1'));
        }

        public function attachmentDeleteAction(){
            $id = (int) $this->_getParam('id', 0);
            $purchaseAttachment = new PurchaseAttachment($id);

            if( !$purchaseAttachment->exists() ){
                throw new Exception(_t("Purchase attachment not found!"));
            }

            $purchaseAttachment->delete();
            $this->_helper->json(array('success' => 1));
        }

        public function attachmentsAction(){
            $purchaseId = (int) $this->_getParam('id', 0);
            $attachmentId = (int) $this->_getParam('attachment', 0);

            $purchase = new Purchase($purchaseId);

            if( !$purchase->exists() ){
                throw new Exception(_t("Purchase not found!"));
            }

            $index = 0;
            $attachment = null;
            foreach( $purchase->attachments as $attachment ){
                if( !$attachmentId ){
                    break;
                }

                if( $attachmentId == $attachment->id ){
                    break;
                }

                $index++;
            }

            $result = array();
            $result['attachment'] = '';

            if( $attachment ){
                $result['attachment'] = $this->view->partial('index/show-attachment.phtml', array('attachment' => $attachment, 'all' => $purchase->attachments, 'index' => $index));
            }

            $this->_helper->json($result);
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