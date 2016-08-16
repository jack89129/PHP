<?php

    class GovernmentItemModel extends Jaycms_Db_Model {

        protected $_name = 'government_item';
        
        public function getGovernmentReport($year, $month){
            $year_month = $year . '-' . $month;
            
            $query = "
                SELECT g.type, g.code, g.name, r.total
                FROM `government_item` g
                LEFT JOIN `report` r ON g.code = r.code
                WHERE r.yearmonth = '$year_month'
                ORDER BY g.code
            "; // ORDER BY g.type, g.code
            
            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            
            return $result;
        }
        
        public function getGovernmentReportByType($year, $month, $type){
            $year_month = $year . '-' . $month;
        
            $query = "
                SELECT g.type, g.code, g.name, r.total
                FROM `government_item` g
                LEFT JOIN `report` r ON g.code = r.code
                WHERE r.yearmonth = '$year_month' AND g.type = '$type'
                ORDER BY g.type, g.code
            ";
            
            $data = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            
            $result = array();
            foreach( $data as $cat ) {
                $row = array();
                $row['category'] = $cat['name'];
                $row['code'] = $cat['code'];
                $row['total_excl_vat'] = $cat['total'];
                $row['vat_sum'] = 0;
                $result[] = $row;
            }
            
            return $result;
        }
        
        public function getItem($code){
            $result = $this->select()->where('code = ?', $code)->query(Zend_Db::FETCH_OBJ)->fetchAll();
            if ( empty($result) ) {
                return "";
            }
            return $result[0]->name;
        }

    }
