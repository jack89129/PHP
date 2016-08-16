<?php
class HapjeModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'hapje';      
    
    public function autocomplete($term, $limit=5){
        $limit = $limit < 1 ? 5 : $limit;
        
        $query = "SELECT
                        *
                  FROM
                        " . $this->_name . "
                  WHERE
                        (
                            " . $this->getAdapter()->quoteInto('value LIKE ?', $term . '%') . "             
                        )
                  ";

        $hapjes = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_OBJ);
        return $hapjes;
    }
}

