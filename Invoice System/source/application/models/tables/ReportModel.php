<?php

    class ReportModel extends Jaycms_Db_Model {

        protected $_name = 'report';
        
        public function getReport($year_month, $code){
            $result = $this->select()->where('yearmonth = ?', $year_month)->where('code = ?', $code)->query(Zend_Db::FETCH_OBJ)->fetchAll();
            if ( empty($result) ) {
                return null;
            }
            return $result[0];
        }
        
        public function saveReport($year_month, $code, $total){
            if ( $total == null ) $total = 0.00;
            $result = $this->getReport($year_month, $code);
            $diff = $total;
            if ( $result == null ) {
                $data = array(
                    'yearmonth' => $year_month,
                    'code' => $code,
                    'total' => $total,
                );
                $this->insert($data);
            } else {
                $data = array(
                    'total' => $total,
                );
                $this->update($data, "yearmonth = '$year_month' AND code = '$code'");
                $diff = $total - $result->total;
            }
            return $diff;
        }
        
        public function updateReport($year_month, $code, $diff){
            $result = $this->getReport($year_month, $code);
            
            $data = array(
                'total' => $result->total + $diff,
            );
            $this->update($data, "yearmonth = '$year_month' AND code = '$code'");
        }

    }
