<?php

    class CheckdateModel extends Jaycms_Db_Model {         

        protected $_name = 'checkdate';
        
        public function getLastCheckResult() {
            $sql = "SELECT * FROM checkdate ORDER BY check_date DESC";
            $last = $this->getAdapter()->fetchRow($sql);
            $result = new Checkdate();
            $result->load($last);  
            return $result;
        }
        
        public function getIncCount() {
            $last = self::getLastCheckResult();
            $sql = "SELECT count(*) count FROM score WHERE dateid=" . $last->id . " AND score > oldscore";
            $result = $this->getAdapter()->fetchRow($sql);
            return $result['count'];
        }
        
        public function getEqCount() {
            $last = self::getLastCheckResult();
            $sql = "SELECT count(*) count FROM score WHERE dateid=" . $last->id . " AND score = oldscore";
            $result = $this->getAdapter()->fetchRow($sql);
            return $result['count'];                      
        }
        
        public function getDecCount() {
            $last = self::getLastCheckResult();
            $sql = "SELECT count(*) count FROM score WHERE dateid=" . $last->id . " AND score < oldscore";
            $result = $this->getAdapter()->fetchRow($sql);
            return $result['count'];                      
        }
          
    }