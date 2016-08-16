<?php

    class SettingPackModel extends Jaycms_Db_Model {

        const STATUS_NEW = 'new';
        const STATUS_FINAL = 'final';

        protected $_name = 'setting_pack';

        public function findPacks($type, $employeeId=0, &$total=0, $limit=null, $page=null){
            $select = 	$this->select();

            if( $employeeId ){
                $select->where('employee_id = ?', $employeeId);
            }

            $method = 'where' . ucfirst($type);
            if( method_exists($this, $method) ){
                call_user_func_array(array($this, $method), array($select));
            }

            $statSelect = clone $select;
            $select->order('id DESC');

            if( $limit !== null && $page !== null ){
                $select->limit($limit, $page*$limit);
            }

            $result = $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
            $stat	= $statSelect->from( $this->_name, array(new Zend_Db_Expr('COUNT(*) as `count`')))
                ->query(Zend_Db::FETCH_OBJ)->fetch();

            $total 	= $stat->count;

            foreach( $result as $key => $receipt ){
                $result[$key] = new SettingPack();
                $result[$key]->load($receipt);
            }

            return $result;
        }
        
        public function findPackByEmp($empId) {
            $select =     $this->select();
            $select->where('employee_id = ?', $empId);
            $result = $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
            return $result;
        }
        
        public function findFinalPack($empId) {
            $select =     $this->select();
            $select->where('employee_id = ?', $empId);
            $select->where('status = ?', self::STATUS_FINAL);
            $result = $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
            return $result;
        }


        public function whereAll($select){
            return $select;
        }
    }