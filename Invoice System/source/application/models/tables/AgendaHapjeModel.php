<?php
class AgendaHapjeModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'agenda_hapje';     
    
    public function getHapjes($agenda_id) {                     
        $query = "SELECT ah.id, ah.hapje_id, h.value hapje
                FROM agenda_hapje ah
                LEFT JOIN hapje h ON ah.hapje_id = h.id
                WHERE ah.agenda_id = $agenda_id
                order by ah.id";
                            
        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC); 

        return $result;
    }   
    
    public function removeHapjes($agenda_id) {
        $this->delete("agenda_id = $agenda_id");
    }
    
}

