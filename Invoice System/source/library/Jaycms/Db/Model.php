<?php
class Jaycms_Db_Model extends Zend_Db_Table_Abstract {
	
	public function directSql($sql){		
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->query($sql)->fetchAll();
		return $result;		
	}
	
	public function directSqlRow($sql){		
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->query($sql)->fetchAll();
		return $result;		
	}
	
	public function findById($id){			
		$select = $this->_db->select()
							->from($this->_name)
							->where('id = ?', $id);
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->fetchRow($select);
		return $result;		
	}
	
	public function findByName($name){			
		$select = $this->_db->select()
							->from($this->_name)
							->where('name = ?', $name);
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->fetchRow($select);
		return $result;		
	}
			
	public function findAll(){			
		$select = $this->_db->select()
							->from($this->_name);
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->fetchAll($select);
		return $result;		
	}
	
	public function findAllByColumn($column, $value){			
		$select = $this->_db->select()
							->from($this->_name)
							->where( $column . ' = ?', $value);
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);	
		$result = $this->getAdapter()->fetchAll($select);
		return $result;		
	}
	
	public function updateById($data, $id){
		$where = $this->_db->quoteInto('id = ?', $id);		
		$this->update($data, $where);
	}
	
	public function deleteById($id){
		$where = $this->_db->quoteInto('id = ?', $id);										
		$this->delete($where);
	}
}

