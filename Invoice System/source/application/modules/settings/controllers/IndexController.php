<?php

    class Settings_IndexController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Settings");
            $this->view->page_sub_title = _t("Overzicht, settings en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){            
            Utils::activity('index', 'settings'); 
            $this->_redirect('/settings/contact/index');             
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