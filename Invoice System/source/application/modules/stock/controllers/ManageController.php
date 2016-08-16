<?php

    class Stock_ManageController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();

            $this->view->page_title = _t("Voorraadbeheer");
            $this->view->page_sub_title = _t("Overzicht, voorraden en meer...");
            $this->view->current_module = "stock";
        }

        public function indexAction(){

            if( !Utils::user()->can('stock_manage_view') ){
                throw new Exception(_t('Access denied!'));
            }

            Utils::activity('index', 'stock-manage');

            $groupId = (int) $this->_getParam('group', 0);

            $groups = ProductGroup::all();
            $products = Product::all();

            $this->view->group = $groupId;
            $this->view->groups = $groups;
            $this->view->products = $products;
            $this->view->readonly  = !Utils::user()->can('stock_manage_edit');
        }

        public function saveAction(){
            $products = (array) $this->_getParam('products', array());

            $productModel = new ProductModel();

            $productModel->getAdapter()->beginTransaction();

            try {
                foreach( $products as $product ){
                    $update = array();
                    $update['stock'] = new Zend_Db_Expr('(stock + ' . ((int) $product['add']) . ')' .
                                                              ' - ' . ((int) $product['remove']));

                    if( $product['add'] ){
                        $prod = new Product($product['id']);

                        if( !$prod->exists() ){
                            throw new Exception(_t("Product not found!"));
                        }

                        $update['last_stock'] = $prod->stock_all_count + $product['add'];
                    }

                    $update['min_stock'] = (int) $product['min_stock'];

                    if( $product['add'] ){
                        Utils::activity('add-to-product', 'stock-manage', $product['id']);
                    }

                    if( $product['remove'] ){
                        Utils::activity('remove-from-product', 'stock-manage', $product['id']);
                    }
                    
                    $productModel->updateById($update, $product['id']);
                    
                    $empProductMap = new EmployeeProductMap();
                    $empProductMap->employee_id = Utils::user()->id;
                    $empProductMap->product_id = $product['id'];
                    $empProductMap->create();
                }

                $productModel->getAdapter()->commit();
            }catch(Exception $e){
                $productModel->getAdapter()->rollBack();
                throw $e;
            }

            Utils::activity('edit', 'stock-manage');

            $this->_helper->json(array('redirect' => $this->view->baseUrl() . '/stock/manage?group=' . (int) $this->_getParam('group', 0)));
        }

        public function pdfAction() {
        	require_once('MPDF/mpdf.php');
			$this->_helper->layout()->disableLayout();
			
			$productModel = new ProductModel();
			$usage = $productModel->getUsageResult();
			
            $data = array();
			
            $last_usage = null;
			
            foreach ($usage as $res) {
				
				if (($last_usage === null) ||
					($last_usage === 0 && $res['cnt_usage'] <= 25 && $res['cnt_usage'] > 0) ||
					($last_usage <= 25 && $res['cnt_usage'] > 25)
				) {
					$row = array();
					$name = 'Minimum voorraad bereikt';
					$name = $last_usage === 0 ? 'Voorraad op 25% van beginwaarde' : $name ;
					$name = $last_usage > 0 ? 'Voorraad voldoende' : $name;
					
					$row[] = "h";
					$row[] = $name;
					$row[] = _t('Totaal');
					$row[] = _t('Magazijn');
					$row[] = _t('Bestellingen');
					$row[] = _t('In transport');
					
					$data[] = $row;
					$last_usage = $res['cnt_usage'];
				}
				
				$row = array();
				$row[] = "";
				$row[] = $res['name'];
				$row[] = $res['cnt_all'];
				$row[] = $res['cnt_stock'];
				$row[] = $res['cnt_reservation'];
				$row[] = $res['cnt_transit'];
				$data[] = $row;
			}
			
			//$date = date("d-m-Y");
			//$time = date("H:i");
			//$this->view->createdDate = _t("Lijst opgemaakt op ") . $date . _t(" om ") . $time;
			$this->view->createdDate = _t("Lijst opgemaakt op ") . date("d-m-Y H:i");
			$this->view->data = $data;
			$content = $this->view->render('manage/pdf.phtml');
			//echo $content;die();
			$mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 20, 20, 9, 9, 'P');
			$mpdf->WriteHTML($content);
			
			$mpdf->Output("voorraadlijst.pdf", "D");
			die();
		}
		/*
		public function pdfAction(){
            $productModel = new ProductModel();
            $usage = $productModel->getUsageResult();

            $data = array();

            $last_usage = null;

            foreach( $usage as $res ){

                if( ($last_usage === null) ||
                    ($last_usage === 0 && $res['cnt_usage'] <= 25 && $res['cnt_usage'] > 0) ||
                    ($last_usage <= 25 && $res['cnt_usage'] > 25)
                ){
                    $data[] = array();
                    $row = array();
                    $style = array();
                    $name = 'Minimum';
                    $name = $last_usage === 0 ? '25%' : $name ;
                    $name = $last_usage > 0 ? 'Normal' : $name;

                    $row[] = $name;                 $style[] = '-btb';
                    $row[] = _t('Overall Stock');       $style[] = '-btbar';
                    $row[] = _t('Stock in depot');      $style[] = '-btbar';
                    $row[] = _t('Stock reservated');    $style[] = '-btbar';
                    $row[] = _t('Stock in transit');    $style[] = '-btbar';
                    $data[] = implode("|", $style);
                    $data[] = $row;
                    $data[] = array();
                    $last_usage = $res['cnt_usage'];
                }

                $row = array();
                $style = array();
                $row[] = $res['name'];              $style[] = '';
                $row[] = $res['cnt_all'];           $style[] = 'ar';
                $row[] = $res['cnt_stock'];         $style[] = 'ar';
                $row[] = $res['cnt_reservation'];   $style[] = 'ar';
                $row[] = $res['cnt_transit'];       $style[] = 'ar';
                $data[] = implode('|', $style);
                $data[] = $row;
            }

            $excel = Export::excelCreate($data, 'xlsx');
            Export::excelOutput($excel, 'voorraad', 'xlsx');
        }
		*/
		
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