<?php

class Wholesalers_IndexController extends Jaycms_Controller_Action
{

    private static $CSV_FIELDS = array('company_name', 'email_address', 'phone', 'address', 'postcode', 'city', 'country', 'role');

 	public function init()
    {	
    	parent::init();
    	$this->view->page_title = _t("Crediteuren");
    	$this->view->page_sub_title = _t("Overzicht, Crediteur informatie en meer...");
    	$this->view->current_module = "wholesalers";
    }

    public function indexAction()
    {
        $wholesalerId = (int) $this->_getParam('wholesaler_id');

        if( !Utils::user()->can('wholesaler_view') ){
            throw new Exception(_t('Access denied!'));
        }

        Utils::activity('index', 'wholesaler');
		$groups = WholesalerGroup::all();
        
        $products = new Product();
        $products = $products->findAll(array(array('deleted = ?', '0')));

        $wholesalers = new Wholesaler();
        $wholesalers = $wholesalers->findAll(array(), array('company_name ASC'));     

        $wholesaler = new Wholesaler($wholesalerId);

		$this->view->wholesaler = $wholesaler->exists() ? $wholesaler : reset($wholesalers);
        $this->view->groups = $groups;
        $this->view->wholesalers = $wholesalers;
        $this->view->products = $products;
    }

    public function viewWholesalerAction(){
        $id = (int) $this->_getParam('id');
        $wholesaler = new Wholesaler($id);

        if( !Utils::user()->can('wholesaler_view') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$wholesaler->exists() ){
            throw new Exception(_t('Wholesaler not found!'));
        }

        Utils::activity('view', 'wholesaler', $wholesaler->id);
        $groups = WholesalerGroup::all();

        $result = array();
        $result['wholesaler'] = $this->view->partial('index/_partials/wholesaler-view.phtml', array('wholesaler' => $wholesaler, 'groups' => $groups));
        $this->_helper->json($result);
    }

    public function removeWholesalerFromGroupAction(){
        $wholesalerId = $this->_getParam('wholesaler');
        $groupId = $this->_getParam('group');

        if( !Utils::user()->can('wholesaler_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $wholesalerGroup = new WholesalerGroupMap();
        $wholesalerGroup = reset($wholesalerGroup->findAll(array(array('wholesaler_id=?', $wholesalerId), array('group_id=?', $groupId))));

        if( !$wholesalerGroup ){
            throw new Exception(_t("Assigment not found!"));
        }

        $wholesalerGroup->delete();

        Utils::activity('remove-wholesaler-from-group', 'wholesaler', $wholesalerGroup->wholesaler_id);

        $wholesaler = new Wholesaler($wholesalerId);
        $groups = WholesalerGroup::all();

        $result = array();
        $result['wholesaler_groups'] = $this->view->partial('index/_partials/wholesaler-groups.phtml', array('wholesaler' => $wholesaler, 'groups' => $groups));
        $this->_helper->json($result);
    }

    public function addWholesalerToGroupAction(){
        $wholesalerId = $this->_getParam('wholesaler');
        $groupId = $this->_getParam('group');

        if( !Utils::user()->can('wholesaler_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $wholesalerGroup = new WholesalerGroupMap();
        $wholesalerGroup = reset($wholesalerGroup->findAll(array(array('wholesaler_id=?', $wholesalerId), array('group_id=?', $groupId))));

        if( $wholesalerGroup ){
            throw new Exception(_t("Wholesaler already in this group!"));
        }

        $wholesaler = new Wholesaler($wholesalerId);

        if( !$wholesaler->exists() ){
            throw new Exception(_t("Wholesaler not found!"));
        }

        $group = new WholesalerGroup($groupId);

        if( !$group->exists() ){
            throw new Exception(_t("Group not found!"));
        }

        $wholesalerGroup = new WholesalerGroupMap();
        $wholesalerGroup->wholesaler_id = $wholesalerId;
        $wholesalerGroup->group_id = $groupId;
        $wholesalerGroup->save();

        Utils::activity('add-wholesaler-to-group', 'wholesaler', $wholesalerGroup->wholesaler_id);

        $wholesaler = new Wholesaler($wholesalerId);
        $groups = WholesalerGroup::all();

        $result = array();
        $result['wholesaler_groups'] = $this->view->partial('index/_partials/wholesaler-groups.phtml', array('wholesaler' => $wholesaler, 'groups' => $groups));
        $this->_helper->json($result);
    }
    
    public function addRowAction(){
        $this->_helper->layout()->disableLayout();
        $index = $this->_getParam('index');
        $wholesalerProduct = new WholesalerProduct();
        
        $products = new Product();
        $products = $products->findAll(array(array('deleted=?', 0)));

        $this->view->assign('product', $wholesalerProduct);
        $this->view->assign('products', $products);
        $this->view->assign('wholesaler_row_index', $index);
        $this->renderScript('index/new/wholesaler-row.phtml');
    }

    public function addGroupAction(){
        $name = trim((string)$this->_getParam('name'));
        $wholesalerId = (int) $this->_getParam('wholesaler');

        if( !Utils::user()->can('wholesaler_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$name ){
            throw new Exception(_t('Enter group name!'));
        }

        $group = new WholesalerGroup();
        $existing = $group->findByColumn('name', $name);

        if( $existing ){
            throw new Exception(_t("Group with this name already exists!"));
        }

        $group->name = $name;
        $group->save();

        Utils::activity('add-group', 'wholesaler');

        $wholesaler = new Wholesaler($wholesalerId);
        $groups = WholesalerGroup::all();

        $result = array();
        $result['wholesaler_groups'] = $this->view->partial('index/_partials/wholesaler-groups.phtml', array('wholesaler' => $wholesaler, 'groups' => $groups, 'selected_group' => $group->id));
        $this->_helper->json($result);
    }
    
    public function removeGroupAction() {
		$groupId = (int) $this->_getParam('group');
		$wholesalerId = (int) $this->_getParam('wholesaler');
		
		if (!Utils::user()->can('wholesaler_edit')) {
			throw new Exception(_t('Access denied!'));
		}
		if (!$groupId) {
			throw new Exception(_t('Select group!'));
		}
		
		$wholesalerGroupModel = new WholesalerGroupModel();
		$wholesalerGroupModel->getAdapter()->beginTransaction();
		try {
			$group = new WholesalerGroup($groupId);
			if (!$group) {
				throw new Exception(_t("Wholesaler Group doesn't exist!"));
			}
			Utils::activity('remove-group', 'wholesaler');
			$group->deleted = 1;
			$group->delete();
			
			$wholesalerGroupModel->getAdapter()->commit();
		} catch (Exception $e) {
			$wholesalerGroupModel->getAdapter()->rollBack();
			throw $e;
		}
		
		$wholesaler = new Wholesaler($wholesalerId);
        $groups = WholesalerGroup::all();

        $result = array();
        $result['wholesaler_groups'] = $this->view->partial('index/_partials/wholesaler-groups.phtml', array('wholesaler' => $wholesaler, 'groups' => $groups, 'selected_group' => $group->id));
        $this->_helper->json($result);
	}

    public function editWholesalerAction(){
        $id = (int) $this->_getParam('id');
        $wholesaler = new Wholesaler($id);

        if( !Utils::user()->can('wholesaler_edit') ){
            throw new Exception(_t('Access denied!'));
        }
        
        $categoryModel = new TagCategoryModel();            
        $expense_tags= array();
                                                                                                                             
        foreach( $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_PURCHASE) as $category ){
            foreach( $category->tags as $tag ){
                $expense_tags[] = $tag;
            }
        }

        $result = array();
        $result['wholesaler_edit'] = $this->view->partial('index/_partials/wholesaler-edit.phtml', array('wholesaler' => $wholesaler, 'expense_tags' => $expense_tags));
        $this->_helper->json($result);
    }

    public function saveWholesalerAction(){
        $wholesalerParam = (array) $this->_getParam('wholesaler');
        $daysParam = (array) $this->_getParam('days');
        $deliveryParam = (array) $this->_getParam('delivery');
        $introParam = $this->_getParam('is_intro');
        $productParam = $this->_getParam('product', array());   
        $ordParam = $this->_getParam('ord', array());

        $orderedProducts = array();
        foreach( $ordParam as $ord ){
            $orderedProducts[] = $productParam[$ord];
        }                                         

        $newProducts = array();                

        foreach( $orderedProducts as $product ){
            if( $product['qty'] < 1 || !$product['description'] ){
                continue;
            }

            if( $vatIncludedParam ){
                $product['price'] = Utils::removeVAT($product['price'], $product['vat']);
            }

            $product['id'] = 0;
            $product['total_sum'] = $product['qty'] * $product['price'];                          

            $product['total_sum'] -= $product['total_sum'] * ($product['discount']/100);
            $product['description'] = Utils::strip_bad_tags($product['description']);

            $newProducts[] = (object) $product;
        }                                   

        if( !Utils::user()->can('wholesaler_edit') ){
            throw new Exception(_t('Access denied!'));
        }
        
        $wholesalerParam['is_intro'] = "0";
        if ( $introParam != null ) {
            $wholesalerParam['is_intro'] = "1";
        }

        if( !$deliveryParam ){
            foreach( $wholesalerParam as $key => $value ){
                if( strpos($key, 'delivery_') === 0 ){
                    $wholesalerParam[$key] = '';
                }
            }
        }

        if( !$wholesalerParam['company_name'] ){
            throw new Exception(_t("Enter at company name!"));
        }

        $wholesaler = new Wholesaler(isset($wholesalerParam['id']) ? $wholesalerParam['id'] : null);
        $wholesaler->load($wholesalerParam);
        $wholesaler->days = $daysParam;
        
        $products = $wholesaler->products;

        if ( !empty($products) ) {
            foreach( $products as $index => $product ){
                if( array_key_exists($index, $newProducts) ){
                    $newProducts[$index]->id = $product->id;
                    $product = new WholesalerProduct();
                    $product->load($newProducts[$index]);
                    $products[$index] = $product;
                    unset($newProducts[$index]);
                }else{
                    $product->delete();
                    unset($products[$index]);
                }
            }
        }

        foreach( $newProducts as $product ){
            $newProduct = new WholesalerProduct();
            $newProduct->load($product);
            $products[] = $newProduct;
        }


        $wholesaler->model()->getAdapter()->beginTransaction();

        try {                                                 

            foreach( $products as $product ){
                $product->wholesaler_id = $wholesaler->id;
                $product->save();
            }

            $wholesaler->model()->getAdapter()->commit();
        }catch( Exception $e ){
            $wholesaler->model()->getAdapter()->rollBack();
            throw $e;
        }
        
        $wholesaler->save();

        Utils::activity('edit', 'wholesaler', $wholesaler->id);

        $this->_helper->json((object) $wholesaler->data());
    }
    
    public function makecontactAction(){
        $wholesalerId = $this->_getParam('id');
        $wholesaler = new Wholesaler($wholesalerId);
        $contact = new Contact();
        $contact->firstname = $wholesaler->firstname2;
        $contact->lastname = $wholesaler->lastname2;
        $contact->company_name = $wholesaler->company_name;
        $contact->address = $wholesaler->address;
        $contact->postcode = $wholesaler->postcode;
        $contact->city = $wholesaler->city;
        $contact->country = $wholesaler->country;
        $contact->vat_number = $wholesaler->vat_number;
        $contact->email_address = $wholesaler->email_address;
        $contact->phone1 = $wholesaler->phone1;
        $contact->phone2 = $wholesaler->phone2;        
        $contact->fax = $wholesaler->fax;
        $contact->cellphone = $wholesaler->cellphone;
        $contact->info = $wholesaler->info;
        $contact->contact_person = $wholesaler->contact_person;
        $contact->role = $wholesaler->role;
        $contact->save();
        $result = array();
        $result['contact_id'] = $contact->id;
        $this->_helper->json((object) $result); 
    }
    
    public function showProductsAction(){
        $wholesalerId = $this->_getParam('id');
        $purchaseId = $this->_getParam('purchase_id');

        if( !Utils::user()->can('wholesaler_view') ){
            throw new Exception(_t('Access denied!'));
        }

        $wholesaler = new Wholesaler($wholesalerId);
        
        $products = new Product();
        $products = $products->findAll(array(array('deleted = ?', '0')));
        
        $wholesaler_row_index = 0;
        
        $results = array();
        
        foreach( $wholesaler->products as $product ) {
            $results[] = $this->view->partial('index/new/wholesaler-row.phtml', array('product' => $product, 'products' => $products, 'wholesaler_row_index' => $wholesaler_row_index++));
        }
        
        $purchase = new Purchase($purchaseId);
        foreach ( $purchase->products as $product ) {
            $product->delete();
        }
        
        $this->_helper->json((object) $results);
    }

    public function updateWholesalersListAction(){
        $groups = WholesalerGroup::all();

        $wholesalers = new Wholesaler();
        $wholesalers = $wholesalers->findAll(array(), array('company_name ASC'));

        $result = array();
        $result['wholesalers_list'] = $this->view->partial('index/_partials/wholesalers-sidebar.phtml', array('groups' => $groups, 'wholesalers' => $wholesalers));
        $this->_helper->json($result);
    }

    public function importTemplateAction(){
        $string  = '';
        $string .= implode(",", self::$CSV_FIELDS) . "\r\n";
        $string .= "John,Doe,john@dummy.com,(212) 742-8107,Wall Street,NY 10005,New York,USA,Manager";
        header('Content-Type: text/csv; encoding=UTF-8');
        header('Content-Length', strlen($string));
        header('Content-Disposition: attachment; filename="import-template.csv"');
        echo $string;
        die();
    }

    public function validateFileAction(){
        $filename = $this->_getParam('filename');

        if( strtolower(pathinfo($filename, PATHINFO_EXTENSION)) != 'csv' ){
            throw new Exception(_t("File type not allowed!"));
        }

        $this->_helper->json(array('success'=>1));
    }

    public function importWholesalersAction(){
        $file = $_FILES ? reset($_FILES) : null ;

        if( !Utils::user()->can('wholesaler_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$file ){
            throw new Exception(_t("No file uploaded!"));
        }

        if( $file['error'] != UPLOAD_ERR_OK ){
            throw new Exception(_t("File not uploaded successfully!"));
        }

        $f = fopen($file['tmp_name'], 'r');

        if( !$f ){
            throw new Exception(_t("Can't read from file!"));
        }


        $skip_first = true;
        $wholesaler = new Wholesaler();

        $wholesaler->model()->getAdapter()->beginTransaction();

        try {
            while( !feof($f) ){
                $line = fgetcsv($f);

                if( $skip_first ){
                    $skip_first = false;
                    continue;
                }

                $c = new Wholesaler();
                $c->load(array_combine(array_values(self::$CSV_FIELDS), $line));
                $c->save();
            }

            $wholesaler->model()->getAdapter()->commit();
        }catch( Exception $e ){
            $wholesaler->model()->getAdapter()->rollBack();
            throw new Exception(_t("Error while importing line: %s", ($line ? implode(",", $line) : 'no information')));
        }

        Utils::activity('import-wholesalers', 'wholesaler');

        $this->_redirect("/wholesalers");
    }

    public function exportWholesalersAction(){

        if( !Utils::user()->can('wholesaler_view') ){
            throw new Exception(_t('Access denied!'));
        }

        $string  = '';
        $string .= implode(",", self::$CSV_FIELDS) . "\r\n";

        $wholesaler = new Wholesaler();
        $wholesalers = $wholesaler->findAll();

        foreach( $wholesalers as $key => $value ){
            $line = array();
            $data = $value->data();
            foreach( self::$CSV_FIELDS as $field ){
                $line[] = $data[$field];
            }

            $string .= implode(",", $line) . "\r\n";
        }

        Utils::activity('export-wholesalers', 'wholesaler');

        header('Content-Type: text/csv; encoding=UTF-8');
        header('Content-Length', strlen($string));
        header('Content-Disposition: attachment; filename="wholesalers-' . date('d-m-Y') . '.csv"');
        echo $string;
        die();
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

