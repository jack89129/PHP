<?php

    class Offers_IndexController extends Jaycms_Controller_Action {

        public function init()
        {
            parent::init();
        }

        public function indexAction(){
            $this->getRequest()->setParam('proforma', 1);
            $this->_forward('index', 'index', 'invoices');
        }

        public function newAction(){
            $this->getRequest()->setParam('proforma', 1);
            $this->_forward('new', 'index', 'invoices');
        }

        public function viewAction(){
            $this->getRequest()->setParam('proforma', 1);
            $this->_forward('view', 'index', 'invoices');
        }

        public function editAction(){
            $this->getRequest()->setParam('proforma', 1);
            $this->_forward('edit', 'index', 'invoices');
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