<?php

    class Search_IndexController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Zoeken");
            $this->view->page_sub_title = _t("Zoeken facturen, offertes, contacten en en meer...");
            $this->view->current_module = "search";
        }

        public function indexAction(){
            $search = $this->_getParam('search', '');

            $result = array();

            $invoiceModel = new InvoiceModel();
            $purchaseModel = new PurchaseModel();
            $productModel = new ProductModel();
            $contactModel = new ContactModel();
            $wholesalerModel = new WholesalerModel();
            $employeeModel = new EmployeeModel();
            $kasboekModel = new KasboekModel();

            $credit_invoices = $invoiceModel->search($search, 0, 1);
            $result[] = array('type' => 'invoice', 'label' => _t('Credit Facturen'), 'count' => count($credit_invoices), 'result' => $credit_invoices);
            
            $invoices = $invoiceModel->search($search, 0);
            $result[] = array('type' => 'invoice', 'label' => _t('Facturen'), 'count' => count($invoices), 'result' => $invoices);

            $offers = $invoiceModel->search($search, 1);
            $result[] = array('type' => 'offer', 'label' => _t('Offertes'), 'count' => count($offers), 'result' => $offers);

            $purchases = $purchaseModel->search($search, 1);
            $result[] = array('type' => 'purchase', 'label' => _t('Inkoop'), 'count' => count($purchases), 'result' => $purchases);

            $products = $productModel->search($search);
            $result[] = array('type' => 'product', 'label' => _t('Producten'), 'count' => count($products), 'result' => $products);

            $contacts = $contactModel->search($search);
            $result[] = array('type' => 'contact', 'label' => _t('Contacten'), 'count' => count($contacts), 'result' => $contacts);

            $wholesalers = $wholesalerModel->search($search);
            $result[] = array('type' => 'wholesaler', 'label' => _t('Leveranciers'), 'count' => count($wholesalers), 'result' => $wholesalers);

            $employees = $employeeModel->search($search);
            $result[] = array('type' => 'employee',  'label' => _t('Werknemers'), 'count' => count($employees), 'result' => $employees);
            
            $kasboek = $kasboekModel->searchKasboek($search);
            $result[] = array('type' => 'kasboek',  'label' => _t('Bankboek'), 'count' => count($kasboek), 'result' => $kasboek);

            $order = array();
            $totalCount = 0;
            foreach( $result as $key => $row ){
                $order[$row['type']] = $row['count'];
                $totalCount += $row['count'];
            }

            asort($order, SORT_NUMERIC);
            $order = array_reverse($order, true);

            $sorted = array();
            foreach( $order as $type => $count ){
                if( $count < 1 ){
                    continue;
                }

                foreach( $result as $row ){
                    if( $row['type'] == $type ){
                        $sorted[] = $row;
                    }
                }
            }

			//var_dump($totalCount);
			//var_dump($search);
			//var_dump($sorted);die();
            $this->view->result = $sorted;
            $this->view->search = $search;
            $this->view->total = $totalCount;
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