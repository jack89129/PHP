<?php

    class Settings_EmployeesController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();
            $this->view->page_title = _t("Werknemers");
            $this->view->page_sub_title = _t("Overzicht, werknemers en meer...");
            $this->view->current_module = "settings";
        }

        public function indexAction(){
            $employee = new Employee();
            $employees = $employee->findAll(array(), array('firstname ASC'));

            $this->view->employees = $employees;
        }

        public function rightsAction(){
            $employeeId = $this->_getParam('employee_id', 0);

            $employee = new Employee($employeeId);

            if( !$employee->exists() ){
                throw new Exception(_t("Employee not found!"));
            }

            $right = new Right();
            $rights = $right->findAll(array(array('`key` != ?', 'special')), array('key ASC', 'action DESC'));

            $this->view->employee = $employee;
            $this->view->rights = $rights;
        }

        public function saveRightsAction(){
            $employeeId = (int) $this->_getParam('employee_id', 0);
            $rightsParam = (array) $this->_getParam('rights', array());

            $employee = new Employee($employeeId);

            if( !$employee->exists() ){
                throw new Exception(_t("Employee not found!"));
            }

            $employee->model()->getAdapter()->beginTransaction();

            try {

                foreach( $employee->rights as $right ){
                    if( !array_key_exists($right->id, $rightsParam) ){
                        $rightEmployee = new RightEmployeeMap();
                        $rightEmployee = reset($rightEmployee->findAll(array(array( 'right_id = ?', $right->id,
                            'employee_id = ?', $employee->id))));
                        if( $rightEmployee ){
                            $rightEmployee->delete();
                        }
                    }else{
                        unset($rightsParam[$right->id]);
                    }
                }

                foreach( $rightsParam as $rightId => $on ){
                    $rightEmployee = new RightEmployeeMap();
                    $rightEmployee->right_id = $rightId;
                    $rightEmployee->employee_id = $employee->id;
                    $rightEmployee->save();
                }

                $employee->model()->getAdapter()->commit();

            }catch( Exception $e ){

                $employee->model()->getAdapter()->rollBack();
                throw $e;
            }

            $this->_redirect('/settings/employees/rights?employee_id=' . $employee->id);
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