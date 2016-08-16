<?php

    class Settings_ActivityController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Activiteit");
            $this->view->page_sub_title = _t("Overzicht, activiteit en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            $session = new Zend_Session_Namespace('activity');

            if( $session->secret != md5(SettingsModel::getActivityLogSecret()) ){
                $this->_forward('secret');
                return;
            }

            $employee_id = (int) $this->_getParam('employee_id', 0);
            $target = $this->_getParam('target', '');
            $page = (int) $this->_getParam('page');
            $date_from = strtotime($this->_getParam('date_from'));
            $date_to = strtotime($this->_getParam('date_to'));
            $total = 0;
            $per_page = 20;

            $employee = new Employee();
            $employees = $employee->findAll(array(), array('firstname ASC'));

            $activityLogModel = new ActivityLogModel();
            $result = $activityLogModel->findLogs($target, $employee_id, $date_from, $date_to, $total, $per_page, $page);
            $modules = $activityLogModel->directSql('SELECT DISTINCT target FROM activity_log ORDER BY target ASC');

            $this->view->modules = $modules;
            $this->view->target = $target;
            $this->view->date_from = $date_from;
            $this->view->date_to = $date_to;
            $this->view->employees = $employees;
            $this->view->employee_id = $employee_id;
            $this->view->per_page = $per_page;
            $this->view->total = $total;
            $this->view->page = $page;
            $this->view->result = $result;
        }

        public function secretAction(){
            $secret = trim($this->_getParam('secret'));
            if( $secret && $secret == SettingsModel::getActivityLogSecret() ){
                $session = new Zend_Session_Namespace('activity');
                $session->secret = md5($secret);
                $this->_forward('index');
            }

            if( !empty($_POST) ){
                $this->view->error = "Invalid secret!";
            }
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