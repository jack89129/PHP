<?php
class ContactModel extends Jaycms_Db_Model {
	/**
	 * The default table name 
	 */
	protected $_name = 'contact';	
    
    public function getContactName($id) {
        $contact = $this->select()->where('id = ? ', $id)->query(Zend_Db::FETCH_OBJ)->fetchAll();
        if ( empty($contact) ) {
            return "";
        }
        return $contact[0]->firstname.' '.$contact[0]->lastname;
    }
    
    public function getContactByLogin($username, $password){
        $result = $this->select()->where('username = ?', $username)->where('pwd = ?', $password)->query(Zend_Db::FETCH_OBJ)->fetchAll();
        if ( empty($result) ) {
            return null;
        }
        $contact = new Contact();
        $contact->load($result[0]);
        return $contact;
    }
	
	public function customFindBy($searchText, $group_id){
		if(!empty($group_id)){
					$select = $this->_db->select()
								->from(array('con' => $this->_name))
								->join(array('cg' => 'contact_group'),'con.id = cg.contact_id')
								->where("firstname like '".$searchText."%'")
								->orWhere("lastname like '".$searchText."%'")
								->where("group_id = '".$group_id."'")
								->order('lastname ASC');
		}else{
			$select = $this->_db->select()
									->from(array('con' => $this->_name))
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

    public function autocomplete($field = 'company_name', $term, $limit=5){
                         $limit = $limit < 1 ? 5 : $limit;
        
        $query = "SELECT
                        *
                  FROM
                        " . $this->_name . "
                  WHERE
                        (
                            " . $this->getAdapter()->quoteInto($field . ' LIKE ?', $term . '%') . "             
                        )
                  ";

        $contacts =    $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_OBJ);
        return $contacts;
    }

    public function search($search){
        $query = "
                    SELECT
                            c.*
                    FROM
                            contact c

                    WHERE
                            (
                                " . $this->getAdapter()->quoteInto('c.firstname LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.lastname LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.company_name LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.address LIKE ?',      '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.postcode LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.city LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.country LIKE ?',      '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.vat_number LIKE ?',   '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.delivery_firstname LIKE ?',   '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.delivery_lastname LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.delivery_address LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.delivery_postcode	LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.delivery_city LIKE ?',        '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.delivery_country LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.contact_person LIKE ?',       '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.email_address LIKE ?',        '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.phone1 LIKE ?',               '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.phone2 LIKE ?',               '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.fax LIKE ?',                  '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.cellphone LIKE ?',            '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.info LIKE ?',                 '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.role LIKE ?',                 '%' . $search . '%') . "
                            )

                    ORDER BY c.firstname DESC
                    LIMIT 20
                 ";

        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

        foreach( $result as $key => $row ){
            $result[$key] = new Contact();
            $result[$key]->load($row);
        }

        return $result;
    }
    
    public function getGoodCustomers(&$total){
        $db = Zend_Registry::get('db1');
        
        $cur_year = date('Y');
        $sql = "
            SELECT CONCAT(c.firstname,' ',c.lastname) contact_name, c.vat_number, s.total_sum, s.vat_sum
            FROM 
            (
            SELECT i.contact_id, sum(i.total_sum) total_sum, sum(i.total_excl_vat) vat_sum
            FROM `invoice` i
            WHERE i.status != 'new' AND ( DATE(i.paid_time) like '$cur_year%' OR ( i.paid_time='0000-00-00 00:00:00' AND DATE(i.invoice_time) like '$cur_year%' ))
            GROUP BY i.contact_id
            ) s
            LEFT JOIN `contact` c ON s.contact_id = c.id
            WHERE s.total_sum >= 250
            ORDER BY s.contact_id
            ";
            
        $stmt = $db->query($sql);

        $result = $stmt->fetchAll();
        
        $total = 0;
        
        foreach ( $result as $row ) {
            $total += $row['total_sum'];
        }

        return $result;
    }
}

