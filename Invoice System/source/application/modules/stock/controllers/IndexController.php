<?php

    class Stock_IndexController extends Jaycms_Controller_Action {

        public function init(){
            parent::init();
            $this->view->page_title = _t("Voorraadbeheer");
            $this->view->page_sub_title = _t("Overzicht, voorraden en meer...");
            $this->view->current_module = "stock";
        }

        public function indexAction(){
            $productId = (int) $this->_getParam('product_id', 0);
            if( !Utils::user()->can('stock_view') ){
                throw new Exception(_t('Access denied!'));
            }

            Utils::activity('index', 'stock');

            $groups = ProductGroup::all();

            $product = new Product($productId);
            $product = $product->exists() ? $product : null ;
            $this->view->groups = $groups;
            $this->view->product = $product;

//			$this->checkMinimumStock();
        }

        public function checkMinimumStock(){
            $products = Product::all(array(array('min_stock > ?', 0), array('min_stock >= stock',''), array('deleted = ?', 0)));

            if( $products ){
                $this->view->placeholder('stock-bottom-window')->append($this->view->partial('/index/_partials/stock-bottom-window.phtml', array('products' => $products)));
            }
        }

        public function addGroupAction(){
            $name = $this->_getParam('name');

            if( !Utils::user()->can('stock_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            if( !$name ){
                throw new Exception(_t('Invalid group name!'));
            }

            $group = new ProductGroup();
            $group->name = $name;
            $group->save();

            Utils::activity('add-group', 'stock');

            $this->_helper->json((object) $group->data());
        }

        public function updateGroupsAction(){
            $group = new ProductGroup();
            $groups = $group->findAll(array(array('deleted=?', 0)));

            $result = array();
            $result['groups'] = $this->view->partial('index/_partials/groups-list.phtml', array('groups' => $groups));

            $this->_helper->json($result);
        }

        public function updateProductsAction(){
            $id = (int) $this->_getParam('id');
            $productId = (int) $this->_getParam('product');

            $group = new ProductGroup($id);

            if( !$group->exists() ){
                throw new Exception(_t('Product group not found!'));
            }

            $products = array();
            foreach( Utils::user()->products as $product ){
                if( $product->product_group_id == $group->id ){
                    $products[] = $product;
                }
            }
            
            $product = new Product($productId);

            if( !$product->exists() ){
                $product = reset($products);
            }

            $result = array();
            $result['products'] = $this->view->partial('index/_partials/products-list.phtml', array('products' => $products));
            $result['product'] = $this->view->partial('index/_partials/product-view.phtml', array('product' => $product));
            $this->_helper->json($result);
        }

        public function updateProductAction(){
            $id = (int) $this->_getParam('id');

            $product = new Product($id);

            if( !$product->exists() ){
                throw new Exception(_t('Product not found!'));
            }

            $result = array();
            $result['product'] = $this->view->partial('index/_partials/product-view.phtml', array('product' => $product));
            $this->_helper->json($result);
        }

        public function deleteGroupsAction(){
            $groups = (array) $this->_getParam('groups');

            if( !Utils::user()->can('stock_delete') ){
                throw new Exception(_t('Access denied!'));
            }

            $groupsModel = new ProductGroupModel();
            $groupsModel->getAdapter()->beginTransaction();

            try {
                foreach( $groups as $id ){
                    $group = new ProductGroup($id);

                    if( !$group->exists() ){
                        continue;
                    }

                    if( $group->products ){
                        throw new Exception(_t('Group have products! Delete all products to be able to delete this group!'));
                    }

                    Utils::activity('delete-group', 'stock');

                    $group->deleted = 1;
                    $group->save();
                }

                $groupsModel->getAdapter()->commit();
            }catch(Exception $e){
                $groupsModel->getAdapter()->rollBack();
                throw $e;
            }

            $this->_helper->json(array('success' => '1'));
        }

        public function deleteProductsAction(){
            $products = (array) $this->_getParam('products');

            if( !Utils::user()->can('stock_delete') ){
                throw new Exception(_t('Access denied!'));
            }

            $productModel = new ProductModel();
            $productModel->getAdapter()->beginTransaction();

            try {
                foreach( $products as $id ){
                    $product = new Product($id);

                    if( !$product->exists() ){
                        continue;
                    }

                    Utils::activity('delete-product', 'stock', $product->id);

                    $product->deleted = 1;
                    $product->delete();//$product->save();
                }

                $productModel->getAdapter()->commit();
            }catch(Exception $e){
                $productModel->getAdapter()->rollBack();
                throw $e;
            }

            $this->_helper->json(array('success' => '1'));
        }

        public function editProductAction(){
            $id = (int) $this->_getParam('id');
            $group = (int) $this->_getParam('group');

            if( !Utils::user()->can('stock_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            $product = new Product($id);
            $group = new ProductGroup($group);

            if( $id && !$product->exists() ){
                throw new Exception(_t('Product not found!'));
            }

            $groups = new ProductGroup();
            $groups = $groups->findAll(array(array('deleted=?',0)));

            $wholesalers = new Wholesaler();
            $wholesalers = $wholesalers->findAll(array(), array('company_name ASC'));

            $tag = new Tag();
            /*$categoryModel = new TagCategoryModel();
            $income_tags = array();
            $expense_tags= array();

            foreach( $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_INVOICE) as $category ){
                foreach( $category->tags as $tag ){
                    $income_tags[] = $tag;
                }
            }

            foreach( $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_PURCHASE) as $category ){
                foreach( $category->tags as $tag ){
                    $expense_tags[] = $tag;
                }
            }*/
            
            $categoryModel = new TagCategoryModel();
            $income_tags = array();
            $expense_tags= array();
            
            $test = $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_INVOICE);

            foreach( $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_INVOICE) as $category ){
                foreach( $category->tags as $tag ){
                    $income_tags[] = $tag;
                }
            }

            foreach( $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_PURCHASE) as $category ){
                foreach( $category->tags as $tag ){
                    $expense_tags[] = $tag;
                }
            }

            $result = array();
            $result['product_edit'] = $this->view->partial('index/_partials/product-edit.phtml', array('product' => $product, 'groups' => $groups, 'group' => $group, 'income_tags' => $income_tags, 'expense_tags' => $expense_tags, 'wholesalers' => $wholesalers));
            $this->_helper->json($result);
        }

        public function saveProductAction(){
            $productParam = (array) $this->_getParam('product', array());
            $has_webshop = $this->_getParam('product_has_webshop');
            $has_new_price = $this->_getParam('has_new_price');
            
            $productParam['has_webshop'] = 0;
            if ( !empty($has_webshop) ) {
                $productParam['has_webshop'] = 1;
            }
            
            $productParam['has_new_price'] = 0;
            if ( !empty($has_new_price) ) {
                $productParam['has_new_price'] = 1;
            }

            if( !Utils::user()->can('stock_edit') ){
                throw new Exception(_t('Access denied!'));
            }

            $product = new Product($productParam['id']);
            $product->load($productParam);

            if( !$product->name ){
                throw new Exception(_t("Product name is required field!"));
            }

            $product->save();

            if( $productParam['id'] ){
                Utils::activity('edit-product', 'stock', $product->id);
            }else{
                Utils::activity('add-product', 'stock', $product->id);
            }

            $this->_helper->json((object) $product->data());
        }

        public function categoryAutocompleteAction(){
            $tagModel = new TagModel();
            $tags = $tagModel->autocomplete($this->_getParam('term'), $this->_getParam('limit'));
            $this->_helper->json($tags);
        }

        public function updateEmployeesAction(){
            $productId = (int) $this->_getParam('product', 0);

            $product = new Product($productId);

            if( !$product->exists() ){
                throw new Exception(_t("Product not found!"));
            }

            $employee = new Employee();
            $employees = $employee->findAll(array(), array('firstname ASC'));

            $result = array();
            $result['employees'] = $this->view->partial('index/_partials/product-employees.phtml', array('product' => $product, 'employees' => $employees));
            $this->_helper->json($result);
        }

        public function addEmployeeToProductAction(){
            $productId = (int) $this->_getParam('product_id', 0);
            $employeeId = (int) $this->_getParam('employee_id', 0);

            $map = new EmployeeProductMap();
            $maps = $map->findAll(array(array('employee_id = ?', $employeeId), array('product_id = ?', $productId)));

            if( $maps ){
                throw new Exception(_t('Employee already added to this product!'));
            }

            $map = new EmployeeProductMap();
            $map->product_id = $productId;
            $map->employee_id = $employeeId;
            $map->save();

            $this->_setParam('product', $productId);
            $this->_forward('update-employees');
        }
        
        public function uploadAction(){
            $productParam = (array) $this->_getParam('product', array());
            if (!empty($_FILES)) {
                $product = new Product($productParam['id']);

                if( !$product->exists() ){
                    throw new Exception(_t("Product not found!"));
                }
                
                if ( $_FILES['product_main_img']['tmp_name'] != "" ) {
                    $tempFile = $_FILES['product_main_img']['tmp_name'];
                    $filename = $_FILES['product_main_img']['name'];
                    $main_img_path = $this->upload($tempFile, $filename);
                    $product->main_img = $main_img_path;
                }
                
                if ( $_FILES['product_sub_img1']['tmp_name'] != "" ) {
                    $tempFile = $_FILES['product_sub_img1']['tmp_name'];
                    $filename = $_FILES['product_sub_img1']['name'];
                    $sub_img_path = $this->upload($tempFile, $filename);
                    $product->sub_img1 = $sub_img_path;
                }
                
                if ( $_FILES['product_sub_img2']['tmp_name'] != "" ) {
                    $tempFile = $_FILES['product_sub_img2']['tmp_name'];
                    $filename = $_FILES['product_sub_img2']['name'];
                    $sub_img_path = $this->upload($tempFile, $filename);
                    $product->sub_img2 = $sub_img_path;
                }
                
                if ( $_FILES['product_sub_img3']['tmp_name'] != "" ) {
                    $tempFile = $_FILES['product_sub_img3']['tmp_name'];
                    $filename = $_FILES['product_sub_img3']['name'];
                    $sub_img_path = $this->upload($tempFile, $filename);
                    $product->sub_img3 = $sub_img_path;
                }
                
                $product->save();
            }
            $this->_redirect('/stock/index/index');
        }
        
        private function upload($tempFile, $filename){
            $basePath = realpath(APPLICATION_PATH . '/../public/');
            $imgPath = $basePath;
            $imgPath .= DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'product';
            $savePath = DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'product';

            $imgPath = str_replace('//', '/', $imgPath);
            $imgPath = str_replace('\\\\', '\\', $imgPath);
            $savePath = str_replace('//', '/', $savePath);
            $savePath = str_replace('\\\\', '\\', $savePath);

            if(!is_dir($imgPath)) mkdir($imgPath);

            date_default_timezone_set('Asia/Hong_Kong');

            $targetPath = $imgPath . DIRECTORY_SEPARATOR . date('Y-m-d');
            $savePath = $savePath . DIRECTORY_SEPARATOR . date('Y-m-d');

            if(!is_dir($targetPath)) mkdir($targetPath);

            $targetFile =  $targetPath . DIRECTORY_SEPARATOR . $filename;
            $savePath = $savePath . DIRECTORY_SEPARATOR . $filename;

            if(file_exists($targetFile)){
                $path_parts = pathinfo($targetFile);
                $pos = strrpos($path_parts['basename'], ".");
                if ($pos === false) {
                    $fname = $path_parts['basename'] . '_bak';
                } else {
                    $fname = substr($path_parts['basename'], 0, $pos) . '_bak.' . $path_parts['extension'];
                }
                $bakFile = $path_parts['dirname'] . DIRECTORY_SEPARATOR . $fname;
                rename($targetFile, $bakFile);
            }
            move_uploaded_file($tempFile, $targetFile);
            return $savePath;
        }

        public function removeEmployeeFromProductAction(){
            $productId = (int) $this->_getParam('product_id', 0);
            $employeeId = (int) $this->_getParam('employee_id', 0);

            $map = new EmployeeProductMap();
            $maps = $map->findAll(array(array('employee_id = ?', $employeeId), array('product_id = ?', $productId)));
            $map = reset($maps);

            if( !$map || !$map->exists() ){
                throw new Exception(_t('Employee not added to this product!'));
            }

            $map->delete();

            $this->_setParam('product', $productId);
            $this->_forward('update-employees');
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
		
		public function updateMinStockBoxAction() {
			$results = Product::all(array(array('min_stock > ?', 0), array('min_stock >= stock',''), array('deleted = ?', 0)));
			
			if ($results) {
				$minstock_products = new Zend_Session_Namespace('min_stock_products');
				if (!$minstock_products->id_list) $minstock_products->id_list = array();
				
				foreach ($results as $product) {
					if (in_array($product->id, $minstock_products->id_list)) continue;
					array_push($minstock_products->id_list, $product->id);
				}
			}
			die();
		}
    }