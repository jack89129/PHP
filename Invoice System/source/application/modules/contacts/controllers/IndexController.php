<?php

class Contacts_IndexController extends Jaycms_Controller_Action
{

    private static $CSV_FIELDS = array('firstname', 'lastname', 'email_address', 'phone', 'address', 'postcode', 'city', 'country', 'role');

 	public function init()
    {	
    	parent::init();
    	$this->view->page_title = _t("Debiteuren");
    	$this->view->page_sub_title = _t("Overzicht, debiteur informatie en meer...");
    	$this->view->current_module = "contacts";
    }

    public function indexAction()
    {
        $contactId = (int) $this->_getParam('contact_id');

        if( !Utils::user()->can('contact_view') ){
            throw new Exception(_t('Access denied!'));
        }

        Utils::activity('index', 'contact');

		$groups = ContactGroup::all();

        /*$where = array();
        if( !Utils::user()->can('contact_view_all') ){
            $ids[] = 0;
            foreach( Utils::user()->contacts as $contact ){
                $ids[] = $contact->id;
            }
            $where[] = array('id IN (?)', $ids);
        } */

        $contacts = new Contact();
        //$contacts = $contacts->findAll($where, array('IF( LENGTH(lastname), lastname, company_name) ASC'));
        $contacts = $contacts->findAll(array(), array('company_name'));

        //$employees = new Employee();
        //$employees = $employees->findAll(array(), array('firstname ASC'));

        $contact = new Contact($contactId);

		$this->view->contact = $contact->exists() ? $contact : reset($contacts);
        $this->view->groups = $groups;
        $this->view->contacts = $contacts;
        //$this->view->employees = $employees;
    }

    public function viewContactAction(){
        $id = (int) $this->_getParam('id');
        $contact = new Contact($id);

        if( !Utils::user()->can('contact_view') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$contact->exists() ){
            throw new Exception(_t('Contact not found!'));
        }

        Utils::activity('view', 'contact', $contact->id);

        $groups = ContactGroup::all();

        $employees = new Employee();
        $employees = $employees->findAll(array(), array('firstname ASC'));

        $result = array();
        $result['contact'] = $this->view->partial('index/_partials/contact-view.phtml', array('contact' => $contact, 'groups' => $groups, 'employees' => $employees));
        $this->_helper->json($result);
    }
    
    public function validateVatAction(){
        $vat = $this->_getParam('vat');
        $result = array();
        $result['is_valid'] = Utils::validationVATNumber($vat);
        $this->_helper->json($result);
    }

    public function removeContactFromGroupAction(){
        $contactId = $this->_getParam('contact');
        $groupId = $this->_getParam('group');

        if( !Utils::user()->can('contact_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $contactGroup = new ContactGroupMap();
        $contactGroup = reset($contactGroup->findAll(array(array('contact_id=?', $contactId), array('group_id=?', $groupId))));

        if( !$contactGroup ){
            throw new Exception(_t("Assigment not found!"));
        }

        $contactGroup->delete();

        Utils::activity('remove-contact-from-group', 'contact', $contactGroup->contact_id);

        $contact = new Contact($contactId);
        $groups = ContactGroup::all();

        $result = array();
        $result['contact_groups'] = $this->view->partial('index/_partials/contact-groups.phtml', array('contact' => $contact, 'groups' => $groups));
        $this->_helper->json($result);
    }

    public function addContactToGroupAction(){
        $contactId = $this->_getParam('contact');
        $groupId = $this->_getParam('group');

        if( !Utils::user()->can('contact_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $contactGroup = new ContactGroupMap();
        $contactGroup = reset($contactGroup->findAll(array(array('contact_id=?', $contactId), array('group_id=?', $groupId))));

        if( $contactGroup ){
            throw new Exception(_t("Contact already in this group!"));
        }

        $contact = new Contact($contactId);

        if( !$contact->exists() ){
            throw new Exception(_t("Contact not found!"));
        }

        $group = new ContactGroup($groupId);

        if( !$group->exists() ){
            throw new Exception(_t("Group not found!"));
        }

        $contactGroup = new ContactGroupMap();
        $contactGroup->contact_id = $contactId;
        $contactGroup->group_id = $groupId;
        $contactGroup->save();

        Utils::activity('add-contact-to-group', 'contact', $contactGroup->contact_id);

        $contact = new Contact($contactId);
        $groups = ContactGroup::all();

        $result = array();
        $result['contact_groups'] = $this->view->partial('index/_partials/contact-groups.phtml', array('contact' => $contact, 'groups' => $groups));
        $this->_helper->json($result);
    }

    public function removeContactEmployeeAction(){
        $contactId = $this->_getParam('contact');
        $employeeId = $this->_getParam('employee');

        if( !Utils::user()->can('contact_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $contactEmployee = new ContactEmployeeMap();
        $contactEmployee = reset($contactEmployee->findAll(array(array('contact_id=?', $contactId), array('employee_id=?', $employeeId))));

        if( !$contactEmployee ){
            throw new Exception(_t("Assigment not found!"));
        }

        $contactEmployee->delete();

        Utils::activity('remove-employee-from-contact', 'contact', $contactEmployee->contact_id);

        $contact = new Contact($contactId);
        $employees = new Employee();
        $employees = $employees->findAll(array(), array('firstname ASC'));

        $result = array();
        $result['contact_employees'] = $this->view->partial('index/_partials/contact-employees.phtml', array('contact' => $contact, 'employees' => $employees));
        $this->_helper->json($result);
    }
    
    public function makewholesalerAction(){
        $contactId = $this->_getParam('id');
        $contact = new Contact($contactId);
        $wholesaler = new Wholesaler();
        $wholesaler->firstname2 = $contact->firstname;
        $wholesaler->lastname2 = $contact->lastname;
        $wholesaler->company_name = $contact->company_name;
        $wholesaler->address = $contact->address;
        $wholesaler->postcode = $contact->postcode;
        $wholesaler->city = $contact->city;
        $wholesaler->country = $contact->country;
        $wholesaler->vat_number = $contact->vat_number;
        $wholesaler->email_address = $contact->email_address;
        $wholesaler->phone1 = $contact->phone1;
        $wholesaler->phone2 = $contact->phone2;        
        $wholesaler->fax = $contact->fax;
        $wholesaler->cellphone = $contact->cellphone;
        $wholesaler->info = $contact->info;
        $wholesaler->contact_person = $contact->contact_person;
        $wholesaler->role = $contact->role;
        $wholesaler->save();
        $result = array();
        $result['wholesaler_id'] = $wholesaler->id;
        $this->_helper->json((object) $result); 
    }

    public function addContactEmployeeAction(){
        $contactId = $this->_getParam('contact');
        $employeeId = $this->_getParam('employee');

        if( !Utils::user()->can('contact_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $contactEmployee = new ContactEmployeeMap();
        $contactEmployee = reset($contactEmployee->findAll(array(array('contact_id=?', $contactId), array('employee_id=?', $employeeId))));

        if( $contactEmployee ){
            throw new Exception(_t("Employee already in this contact!"));
        }

        $contact = new Contact($contactId);

        if( !$contact->exists() ){
            throw new Exception(_t("Contact not found!"));
        }

        $employee = new Employee($employeeId);

        if( !$employee->exists() ){
            throw new Exception(_t("Employee not found!"));
        }

        $contactEmployee = new ContactEmployeeMap();
        $contactEmployee->contact_id = $contactId;
        $contactEmployee->employee_id = $employeeId;
        $contactEmployee->save();

        Utils::activity('add-employee-to-contact', 'contact', $contactEmployee->contact_id);

        $contact = new Contact($contactId);
        $employees = new Employee();
        $employees = $employees->findAll(array(), array('firstname ASC'));

        $result = array();
        $result['contact_employees'] = $this->view->partial('index/_partials/contact-employees.phtml', array('contact' => $contact, 'employees' => $employees));
        $this->_helper->json($result);
    }

    public function addGroupAction(){
        $name = trim((string)$this->_getParam('name'));
        $contactId = (int) $this->_getParam('contact');

        if( !Utils::user()->can('contact_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$name ){
            throw new Exception(_t('Enter group name!'));
        }

        $group = new ContactGroup();
        $existing = $group->findByColumn('name', $name);

        if( $existing ){
            throw new Exception(_t("Group with this name already exists!"));
        }

        $group->name = $name;
        $group->save();

        Utils::activity('add-group', 'contact');

        $contact = new Contact($contactId);
        $groups = ContactGroup::all();

        $result = array();
        $result['contact_groups'] = $this->view->partial('index/_partials/contact-groups.phtml', array('contact' => $contact, 'groups' => $groups, 'selected_group' => $group->id));
        $this->_helper->json($result);
    }
	
	public function removeGroupAction() {
		$groupId = (int) $this->_getParam('group');
		$contactId = (int) $this->_getParam('contact');
		
		if (!Utils::user()->can('contact_edit')) {
			throw new Exception(_t('Access denied!'));
		}

		if (!$groupId) {
			throw new Exception(_t('Select group!'));
		}
		
		$contactGroupModel = new ContactGroupModel();
		$contactGroupModel->getAdapter()->beginTransaction();
		try {
			$group = new ContactGroup($groupId);
			if (!$group) {
				throw new Exception(_t("Contact Group doesn't exist!"));
			}
			Utils::activity('remove-group', 'contact');
			$group->deleted = 1;
			$group->delete();
			
			$contactGroupModel->getAdapter()->commit();
		} catch (Exception $e) {
			$contactGroupModel->getAdapter()->rollBack();
			throw $e;
		}
		
		$contact = new Contact($contactId);
        $groups = ContactGroup::all();

        $result = array();
        $result['contact_groups'] = $this->view->partial('index/_partials/contact-groups.phtml', array('contact' => $contact, 'groups' => $groups, 'selected_group' => $group->id));
        $this->_helper->json($result);
	}
	
    public function editContactAction(){
        $id = (int) $this->_getParam('id');
        $contact = new Contact($id);

        if( !Utils::user()->can('contact_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $result = array();
        $result['contact_edit'] = $this->view->partial('index/_partials/contact-edit.phtml', array('contact' => $contact));
        $this->_helper->json($result);
    }

    public function saveContactAction(){
        $contactParam = (array) $this->_getParam('contact');
        $daysParam = (array) $this->_getParam('days');
        $deliveryParam = (array) $this->_getParam('delivery');
        $introParam = $this->_getParam('is_intro');
        
        if( !Utils::user()->can('contact_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$deliveryParam ){
            foreach( $contactParam as $key => $value ){
                if( strpos($key, 'delivery_') === 0 ){
                    $contactParam[$key] = '';
                }
            }
        }
        
        $contactParam['is_intro'] = "0";
        if ( $introParam != null ) {
            $contactParam['is_intro'] = "1";
        }

        if( empty($contactParam['firstname']) && empty($contactParam['lastname']) && empty($contactParam['company_name']) ){
            throw new Exception(_t("Enter at least firstname, lastname or company name!"));
        }
        /*
        if ( $contactParam['pwd'] != $contactParam['pwd_confirm'] ) {
            throw new Exception(_t("Passwords must match!"));
        }
        
        if ( empty($contactParam['pwd']) ) {
            unset($contactParam['pwd']);
        } else {
            $contactParam['pwd'] = Employee::saltPassword($contactParam['pwd']);
        }
        unset($contactParam['pwd_confirm']);*/

        $contact = new Contact(isset($contactParam['id']) ? $contactParam['id'] : null);
        $contact->load($contactParam);
        $contact->days = $daysParam;
        $contact->save();

        Utils::activity('edit', 'contact', $contact->id);

	    $this->_helper->json((object) $contact->data());
    }

    public function updateContactsListAction(){
        $groups = ContactGroup::all();

        $where = array();
        /*if( !Utils::user()->can('contact_view_all') ){
            $ids[] = 0;
            foreach( Utils::user()->contacts as $contact ){
                $ids[] = $contact->id;
            }
            $where[] = array('id IN (?)', $ids);
        } */

        $contacts = new Contact();
        $contacts = $contacts->findAll($where, array('IF( LENGTH(lastname), lastname, company_name) ASC'));

        $result = array();
        $result['contacts_list'] = $this->view->partial('index/_partials/contacts-sidebar.phtml', array('groups' => $groups, 'contacts' => $contacts));
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

    public function importContactsAction(){
        $file = $_FILES ? reset($_FILES) : null ;

        if( !Utils::user()->can('contact_edit') ){
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
        $contact = new Contact();

        $contact->model()->getAdapter()->beginTransaction();

        try {
            while( !feof($f) ){
                $line = fgetcsv($f);

                if( $skip_first ){
                    $skip_first = false;
                    continue;
                }

                $c = new Contact();
                $c->load(array_combine(array_values(self::$CSV_FIELDS), $line));
                $c->save();
            }

            $contact->model()->getAdapter()->commit();
        }catch( Exception $e ){
            $contact->model()->getAdapter()->rollBack();
            throw new Exception(_t("Error while importing line: %s",  ($line ? implode(",", $line) : 'no information')));
        }

        Utils::activity('import-contacts', 'contact');

        $this->_redirect("/contacts");
    }

    public function exportContactsAction(){

        if( !Utils::user()->can('contact_view') ){
            throw new Exception(_t('Access denied!'));
        }

        $string  = '';
        $string .= implode(",", self::$CSV_FIELDS) . "\r\n";

        $contact = new Contact();
        $contacts = $contact->findAll();

        foreach( $contacts as $key => $value ){
            $line = array();
            $data = $value->data();
            foreach( self::$CSV_FIELDS as $field ){
                $line[] = $data[$field];
            }

            $string .= implode(",", $line) . "\r\n";
        }

        Utils::activity('export-contacts', 'contact');

        header('Content-Type: text/csv; encoding=UTF-8');
        header('Content-Length', strlen($string));
        header('Content-Disposition: attachment; filename="contacts-' . date('d-m-Y') . '.csv"');
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

