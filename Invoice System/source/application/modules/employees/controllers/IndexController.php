<?php

class Employees_IndexController extends Jaycms_Controller_Action
{

 	public function init()
    {	
    	parent::init();
    	$this->view->page_title = _t("Werknemers");
    	$this->view->page_sub_title = _t("Overzicht, werknermers informatie en meer...");
    	$this->view->current_module = "employees";
    }

    public function indexAction()
    {
        if( !Utils::user()->can('employee_view') ){
            throw new Exception(_t('Access denied!'));
        }

        Utils::activity('index', 'employee');

        $employeeId = (int) $this->_getParam('employee_id');
		$groups = EmployeeGroup::all();

        $employees = new Employee();
        $employees = $employees->findAll(array(), array('lastname ASC'));

        $employee = new Employee($employeeId);

		$this->view->employee = $employee->exists() ? $employee : reset($employees);
        $this->view->groups = $groups;
        $this->view->employees = $employees;
    }

    public function viewEmployeeAction(){
        $id = (int) $this->_getParam('id');
        $employee = new Employee($id);

        if( !Utils::user()->can('employee_view') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$employee->exists() ){
            throw new Exception(_t('Employee not found!'));
        }

        Utils::activity('view', 'employee', $employee->id);
        $groups = EmployeeGroup::all();

        $result = array();
        $result['employee'] = $this->view->partial('index/_partials/employee-view.phtml', array('employee' => $employee, 'groups' => $groups));
        $this->_helper->json($result);
    }

    public function removeEmployeeFromGroupAction(){
        $employeeId = $this->_getParam('employee');
        $groupId = $this->_getParam('group');

        if( !Utils::user()->can('employee_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $employeeGroup = new EmployeeGroupMap();
        $employeeGroup = reset($employeeGroup->findAll(array(array('employee_id=?', $employeeId), array('employee_group_id=?', $groupId))));

        if( !$employeeGroup ){
            throw new Exception(_t("Assignment not found!"));
        }

        $employeeGroup->delete();

        Utils::activity('remove-employee-from-group', 'employee', $employeeGroup->employee_id);

        $employee = new Employee($employeeId);
        $groups = EmployeeGroup::all();

        $result = array();
        $result['employee_groups'] = $this->view->partial('index/_partials/employee-groups.phtml', array('employee' => $employee, 'groups' => $groups));
        $this->_helper->json($result);
    }

    public function addEmployeeToGroupAction(){
        $employeeId = $this->_getParam('employee');
        $groupId = $this->_getParam('group');

        if( !Utils::user()->can('employee_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $employeeGroup = new EmployeeGroupMap();
        $employeeGroup = reset($employeeGroup->findAll(array(array('employee_id=?', $employeeId), array('employee_group_id=?', $groupId))));

        if( $employeeGroup ){
            throw new Exception(_t("Employee already in this group!"));
        }

        $employee = new Employee($employeeId);

        if( !$employee->exists() ){
            throw new Exception(_t("Employee not found!"));
        }

        $group = new EmployeeGroup($groupId);

        if( !$group->exists() ){
            throw new Exception(_t("Group not found!"));
        }

        $employeeGroup = new EmployeeGroupMap();
        $employeeGroup->employee_id = $employeeId;
        $employeeGroup->employee_group_id = $groupId;
        $employeeGroup->save();

        Utils::activity('add-employee-to-group', 'employee', $employeeGroup->employee_id);

        $employee = new Employee($employeeId);
        $groups = EmployeeGroup::all();

        $result = array();
        $result['employee_groups'] = $this->view->partial('index/_partials/employee-groups.phtml', array('employee' => $employee, 'groups' => $groups));
        $this->_helper->json($result);
    }

    public function addGroupAction(){
        $name = trim((string)$this->_getParam('name'));
        $employeeId = (int) $this->_getParam('employee');

        if( !Utils::user()->can('employee_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$name ){
            throw new Exception(_t('Enter group name!'));
        }

        $group = new EmployeeGroup();
        $existing = $group->findByColumn('name', $name);

        if( $existing ){
            throw new Exception(_t("Group with this name already exists!"));
        }

        $group->name = $name;
        $group->save();

        Utils::activity('add-group', 'employee');

        $employee = new Employee($employeeId);
        $groups = EmployeeGroup::all();

        $result = array();
        $result['employee_groups'] = $this->view->partial('index/_partials/employee-groups.phtml', array('employee' => $employee, 'groups' => $groups, 'selected_group' => $group->id));
        $this->_helper->json($result);
    }
    
	public function removeGroupAction() {
		$groupId = (int) $this->_getParam('group');
		$employeeId = (int) $this->_getParam('employee');
		
		if (!Utils::user()->can('employee_edit')) {
			throw new Exception(_t('Access denied!'));
		}
		if (!$groupId) {
			throw new Exception(_t('Select group!'));
		}
		
		$employeeGroupModel = new EmployeeGroupModel();
		$employeeGroupModel->getAdapter()->beginTransaction();
		try {
			$group = new EmployeeGroup($groupId);
			if (!$group) {
				throw new Exception(_t("Employee Group doesn't exist!"));
			}
			Utils::activity('remove-group', 'employee');
			$group->deleted = 1;
			$group->delete();
			
			$employeeGroupModel->getAdapter()->commit();
		} catch (Exception $e) {
			$employeeGroupModel->getAdapter()->rollBack();
			throw $e;
		}
		
		$employee = new Contact($employeeId);
        $groups = EmployeeGroup::all();

        $result = array();
        $result['employee_groups'] = $this->view->partial('index/_partials/employee-groups.phtml', array('employee' => $employee, 'groups' => $groups, 'selected_group' => $group->id));
        $this->_helper->json($result);
	}

    public function editEmployeeAction(){
        $id = (int) $this->_getParam('id');
        $employee = new Employee($id);

        if( !Utils::user()->can('employee_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        $result = array();
        $result['employee_edit'] = $this->view->partial('index/_partials/employee-edit.phtml', array('employee' => $employee));
        $this->_helper->json($result);
    }

    public function saveEmployeeAction(){
        $employeeParam = (array) $this->_getParam('employee');
        $password1 = $this->_getParam('password1');
        $password2 = $this->_getParam('password2');

        if( !Utils::user()->can('employee_edit') ){
            throw new Exception(_t('Access denied!'));
        }

        if( !$employeeParam['firstname'] && !$employeeParam['lastname'] ){
            throw new Exception(_t("Enter at least one name!"));
        }

        if( $password1 ){
            if( iconv_strlen($password1, 'UTF-8') < 6 ){
                throw new Exception(_t("Password must be at least 6 characters long!"));
            }

            if( $password1 != $password2 ){
                throw new Exception(_t("Passwords must match!"));
            }
        }



        $employee = new Employee($employeeParam['id']);
        $employee->model()->getAdapter()->beginTransaction();

        try {
            $employee->firstname = $employeeParam['firstname'];
            $employee->lastname = $employeeParam['lastname'];
            $employee->address = $employeeParam['address'];
            $employee->postcode = $employeeParam['postcode'];
            $employee->city = $employeeParam['city'];
            $employee->country = $employeeParam['country'];
            $employee->email_address = $employeeParam['email_address'];
            $employee->phone = $employeeParam['phone'];
            $employee->role = $employeeParam['role'];
            $employee->username = $employeeParam['username'];

            if( $password1 ){
                $employee->password = Employee::saltPassword($password1);
            }

            $employee->save();

            if( $employeeParam['id'] ){
                Utils::activity('edit', 'employee', $employee->id);
            }else{
                Utils::activity('new', 'employee', $employee->id);
            }

            $employee->model()->getAdapter()->commit();
        }catch(Exception $e){
            $employee->model()->getAdapter()->rollBack();
            throw $e;
        }

        $this->_helper->json((object) $employee->data());
    }

    public function updateEmployeesListAction(){
        $groups = EmployeeGroup::all();

        if( !Utils::user()->can('employee_view') ){
            throw new Exception(_t('Access denied!'));
        }

        $employees = new Employee();
        $employees = $employees->findAll(array(), array('lastname ASC'));

        $result = array();
        $result['employees_list'] = $this->view->partial('index/_partials/employees-sidebar.phtml', array('groups' => $groups, 'employees' => $employees));
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

