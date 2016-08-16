<?php
class EmployeeGroupMapModel extends Jaycms_Db_Model {
	/**
	 * The default table name 
	 */
	protected $_name = 'employee_group_map';
	
	public function findByEmpId($empId){
		if(empty($empId))
			return;
		
		$select = $this->_db->select()
							->from($this->_name)
							->where('employee_id = ?', $empId)
							->order('employee_group_id ASC');
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->fetchRow($select);
		return $result;	
	}
	
	
}

