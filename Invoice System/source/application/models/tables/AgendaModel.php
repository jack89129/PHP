<?php
class AgendaModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'agenda';      
    
    public function getAgendaByDate($date) {
        $result = $this->select()->where('reserved_date = ?', $date)->where('status != 0')->query(Zend_Db::FETCH_OBJ)->fetchAll();
        
        foreach( $result as $key => $agenda ){
            $result[$key] = new Agenda();
            $result[$key]->load($agenda);         
        }
        return $result;
    }
    
    public function getAgendaByMonth($year, $month) {                     
        if ( strlen($month) == 1 ) $month = "0".$month;
        $query = "SELECT a.reserved_date, a.status, 
                (SELECT sum(b.cnt) FROM agenda b WHERE b.reserved_date=a.reserved_date AND b.status = 1) green, 
                (SELECT sum(b.cnt) FROM agenda b WHERE b.reserved_date=a.reserved_date AND b.status = 2) orange 
                FROM agenda a
                WHERE a.reserved_date like '$year-$month%'          
                group by a.reserved_date
                order by a.reserved_date";
                            
        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC); 

        return $result;
    }
    
}

