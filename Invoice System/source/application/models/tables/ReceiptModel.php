<?php

    class ReceiptModel extends Jaycms_Db_Model {

        const STATUS_NEW = 'new';
        const STATUS_FINAL = 'final';

        protected $_name = 'receipt';

        public function findReceipts($type, $employeeId=0, $contactId=0, &$total=0, $limit=null, $page=null){
            $select = 	$this->select();

            if( $employeeId ){
                $select->where('employee_id = ?', $employeeId);
            }

            if( $contactId ){
                $select->where('contact_id = ?', $contactId);
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
                $result[$key] = new Receipt();
                $result[$key]->load($receipt);
            }

            return $result;
        }
        
        public function findReceiptsForPDF($employeeId=0, $date){        
            $db = Zend_Registry::get('db1');
            
            $sql = "
                SELECT pp.product_id id, pp.description name, sum(pp.qty) tnum FROM `receipt_product` pp
                LEFT JOIN `receipt` p ON pp.receipt_id = p.id
                WHERE DATE(p.delivery_date) = '".date(Constants::MYSQL_DAY_FORMAT, strtotime($date))."' and p.employee_id = '".$employeeId."'
                GROUP BY pp.product_id
                ORDER BY pp.product_id";
                
            $stmt = $db->query($sql);

            $result = $stmt->fetchAll();

            $receipts = array();
            foreach ( $result as $row ) {
                $receipts["id$row[id]"] = $row;
            }

            return $receipts;
        }

        public function findEmployeeReceipts($employeeId, $contactId=0, &$total=0, $limit=null, $page=null){
            $wheres = array();
            $query = "
                        SELECT
                                SQL_CALC_FOUND_ROWS
                                r.*

                        FROM
                                receipt r
                                LEFT JOIN contact_employee_map cem ON cem.contact_id = r.contact_id AND cem.employee_id = " . ((int) $employeeId) . "
                        WHERE

            ";


            $wheres[] = "(
                            " . $this->getAdapter()->quoteInto('r.employee_id = ?', $employeeId) . "
                            OR
                            " . $this->getAdapter()->quoteInto('r.created_by = ?', $employeeId) . "
                            OR
                            cem.id IS NOT NULL
                         )";


            if( $contactId ){
                $wheres[] = $this->getAdapter()->quoteInto('r.contact_id = ?', $contactId);
            }

            $query .= implode("\nAND\n", $wheres);
            $query .= "\nGROUP BY r.id\n";
            $query .= "\nORDER BY r.id DESC\n";

            if( $limit !== null && $page !== null ){
                $query .= "\nLIMIT " . ((int) $limit) . " OFFSET " . ((int) $page*$limit);
            }

//            echo "<pre>";
//            echo $query;
//            die();

            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_OBJ);
            $total 	= $this->getAdapter()->fetchOne("SELECT FOUND_ROWS()");

            foreach( $result as $key => $receipt ){
                $result[$key] = new Receipt();
                $result[$key]->load($receipt);
            }

            return $result;
        }


        public function whereAll($select){
            return $select;
        }
    }