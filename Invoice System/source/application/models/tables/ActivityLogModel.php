<?php

    class ActivityLogModel extends Jaycms_Db_Model {

        protected $_name = 'activity_log';

        public function findLogs($target, $employeeId, $date_from=0, $date_to=0, &$total=0, $limit=null, $page=null){
            $select = $this->select();

            if( $target ){
                $select->where('target = ?', $target);
            }

            if( $employeeId ){
                $select->where('employee_id = ?', $employeeId);
            }

            if( $date_from ){
                $select->where('DATE(created_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
            }

            if( $date_to ){
                $select->where('DATE(created_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
            }


            $statSelect = clone $select;

            $select->order('id DESC');

            if( $limit !== null && $page !== null ){
                $select->limit($limit, $page*$limit);
            }

            $result = $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
            $stat	= $statSelect->from( $this->_name, array(new Zend_Db_Expr('COUNT(*) as `count`')))->query(Zend_Db::FETCH_OBJ)->fetch();

            $total 	= $stat->count;

            foreach( $result as $key => $log ){
                $result[$key] = new ActivityLog();
                $result[$key]->load($log);
            }

            return $result;
        }
    }