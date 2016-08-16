<?php
class EmployeeModel extends Jaycms_Db_Model {
	/**
	 * The default table name 
	 */
	protected $_name = 'employee';	
	
	public function customFindBy($searchText,$group_id){
		if(!empty($group_id)){
					$select = $this->_db->select()
								->from(array('emp' => $this->_name))
								->join(array('egl' => 'employee_group_link'),'emp.id = egl.employee_id')
								->where("firstname like '".$searchText."%'")
								->orWhere("lastname like '".$searchText."%'")
								->where("employee_group_id = '".$group_id."'")
								->order('lastname ASC');
		}else{
			$select = $this->_db->select()
									->from(array('emp' => $this->_name))
									->where("firstname like '".$searchText."%'")
									->orWhere("lastname like '".$searchText."%'")
									->order('lastname ASC');
		}

		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->fetchAll($select);
		return $result;	
	}
	
	public function sortBy($fieldName,$direction){
		if(empty($fieldName))
			return;
			
		if(empty($direction))
			$direction = "asc";
		
		$select = $this->_db->select()
							->from($this->_name)
							->order($fieldName.' '.$direction);
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->fetchAll($select);
	
		return $result;	
	}

    public function search($search){
        $query = "
                    SELECT
                            e.*
                    FROM
                            employee e

                    WHERE
                            (
                                " . $this->getAdapter()->quoteInto('e.firstname LIKE ?',        '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.lastname LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.address LIKE ?',          '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.postcode LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.city LIKE ?',             '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.country LIKE ?',          '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.email_address LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.phone LIKE ?',            '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.role LIKE ?',             '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('e.username	LIKE ?',        '%' . $search . '%') . "
                            )

                    ORDER BY e.firstname DESC
                    LIMIT 20
                 ";

        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

        foreach( $result as $key => $row ){
            $result[$key] = new Employee();
            $result[$key]->load($row);
        }

        return $result;
    }
}

