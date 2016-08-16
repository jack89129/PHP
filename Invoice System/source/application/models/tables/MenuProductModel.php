<?php
class MenuProductModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'menu_product';    
    
    public function autocomplete($term, $limit=5){
        $limit = $limit < 1 ? 5 : $limit;
        
        $query = "SELECT
                        *
                  FROM
                        " . $this->_name . "
                  WHERE
                        (
                            " . $this->getAdapter()->quoteInto('name LIKE ?', $term . '%') . "             
                        )
                  ";

        $menus = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_OBJ);
        return $menus;
    }
    
    public function getAgendaMenus($agenda_id) {                     
        $query = "SELECT mt.id, mt.type, l.agenda_id, l.buffet, l.menu_id, l.prodname, l.amount
                    FROM `menu_type` mt
                    LEFT JOIN 
                    (SELECT m.agenda_id, m.type_id, m.buffet, m.menu_id, p.name prodname, p.amount
                    FROM `agenda_menu` m
                    LEFT JOIN `menu_type` t ON m.type_id = t.id
                    LEFT JOIN `menu_product` p ON m.menu_id = p.id
                    WHERE m.agenda_id = '$agenda_id') l ON l.type_id = mt.id
                    ORDER BY mt.id, l.menu_id";
                            
        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC); 

        return $result;
    }    
    
}

