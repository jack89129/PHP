<?php
class AgendaMenuModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'agenda_menu';        
    
    public function removeMenus($agenda_id) {
        $this->delete("agenda_id = $agenda_id");
    }
    
}

