<?php

    class PurchaseModel extends Jaycms_Db_Model {

        protected $_name = 'purchase';

        public function findPurchases($type, $contactId, $date_from=0, $date_to=0, &$total=0, &$sum=0.0, &$sum_no_vat=0.0, $limit=null, $page=null){
            $select = 	$this->select();

            $method = 'where' . ucfirst($type);
            if( method_exists($this, $method) ){
                call_user_func_array(array($this, $method), array($select));
            }

            if( $contactId ){
                $select->where('contact_id = ?', $contactId);
            }

            if( $date_from ){
                $select->where('DATE(invoice_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
            }

            if( $date_to ){
                $select->where('DATE(invoice_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
            }

            $statSelect = clone $select;
            $select->order('id DESC');

            if( $limit !== null && $page !== null ){
                $select->limit($limit, $page*$limit);
            }

            $result = $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
            $stat	= $statSelect->from( $this->_name, array(new Zend_Db_Expr('COUNT(*) as `count`'),
                new Zend_Db_Expr('SUM(total_sum) as `sum`'),
                new Zend_Db_Expr('SUM(total_excl_vat) as `sum_no_vat`')))
                ->query(Zend_Db::FETCH_OBJ)->fetch();

            $total 	= $stat->count;
            $sum	= $stat->sum;
            $sum_no_vat = $stat->sum_no_vat;

            foreach( $result as $key => $purchase ){
                $result[$key] = new Purchase();
                $result[$key]->load($purchase);
            }

            return $result;
        }
        
        public function findPurchasesForOverige($type, $date_from=0, $date_to=0, &$total=0, &$sum=0.0, &$sum_no_vat=0.0, $payment="bank"){
            /*$select =     $this->select();

            $method = 'where' . ucfirst($type);
            if( method_exists($this, $method) ){
                call_user_func_array(array($this, $method), array($select));
            }

            if( $date_from ){
                $select->where('DATE(paid_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
            }

            if( $date_to ){
                $select->where('DATE(paid_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
            }

            $statSelect = clone $select;
            $select->order('id DESC');

            $result = $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
            $stat    = $statSelect->from( $this->_name, array(new Zend_Db_Expr('COUNT(*) as `count`'),
                new Zend_Db_Expr('SUM(total_sum) as `sum`'),
                new Zend_Db_Expr('SUM(total_excl_vat) as `sum_no_vat`')))
                ->query(Zend_Db::FETCH_OBJ)->fetch();

            $total     = $stat->count;
            $sum    = $stat->sum;
            $sum_no_vat = $stat->sum_no_vat;*/
            
            $query = "
                    SELECT
                            p.*
                    FROM
                            purchase p
                            LEFT JOIN purchase_payment pp ON p.id = pp.purchase_id

                    WHERE
                            pp.payment_method = '".$payment."' AND
                    ";
            
            $wheres = array();
            
            if( $date_from ){
                $wheres[] = $this->getAdapter()->quoteInto('DATE(p.paid_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
            }

            if( $date_to ){
                $wheres[] = $this->getAdapter()->quoteInto('DATE(p.paid_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
            }
            
            $query .= implode("\nAND\n", $wheres);
            $statQuery = $query;
            
            $query .= "\nORDER BY p.id DESC\n";

            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            $stat    = $this->getAdapter()->query(str_replace('{select}', '  COUNT(*) as `count`,
                                                                        SUM(p.total_sum) as `sum`,
                                                                        SUM(p.total_excl_vat) as `sum_no_vat`', $statQuery))
                                     ->fetch(Zend_Db::FETCH_OBJ);

            $total     = $stat->count;
            $sum    = $stat->sum;
            $sum_no_vat = $stat->sum_no_vat;

            foreach( $result as $key => $purchase ){
                $result[$key] = new Purchase();
                $result[$key]->load($purchase);
            }

            return $result;
        }


        public function whereAll($select){
            return $select;
        }

        public function whereUrgent($select){
            return $select->where('paid_time = ?', '0000-00-00 00:00:00')
                ->where('TO_DAYS(expire_time) - TO_DAYS(NOW()) BETWEEN 0 AND ' . (int) Constants::INVOICE_URGENT_DAYS);
        }

        public function whereOutstanding($select){
            return $select->where('paid_time = ?', '0000-00-00 00:00:00');
        }

        public function whereLate($select){
            return $select->where('paid_time = ?', '0000-00-00 00:00:00')
                          ->where('expire_time
                           < NOW()');
        }

        public function wherePaid($select){
            return $select->where('paid_time != ?', '0000-00-00 00:00:00');
        }

        public function findUnpaidPurchases(){
            return $this->select()->where('paid_time = ?', '0000-00-00 00:00:00')->query(Zend_Db::FETCH_OBJ)->fetchAll();
        }

        public function getTotalByTag($dateFrom, $dateTo, Tag $tag, $group){

            $query = '
                    SELECT
                            SUM(pp.total_sum) as sum,
                            UNIX_TIMESTAMP(p.invoice_time) as `time`
                    FROM
                            purchase p
                            LEFT JOIN purchase_product pp ON pp.purchase_id = p.id

                    WHERE
        ';

            $wheres = array();

            $wheres[] = 'pp.tag_id = ' . $tag->id;

            if( $dateFrom ){
                $wheres[] = 'DATE(p.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\'';
            }

            if( $dateTo ){
                $wheres[] = 'DATE(p.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\'';
            }

            $wheres[] = 'p.paid_time != \'0000-00-00 00:00:00\'';

            $query .= implode("\nAND\n", $wheres);
            $query .= "\n";
            $query .= "GROUP BY " . strtoupper($group) . "(p.invoice_time)";

            $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            return $results;
        }

        public function getTagTotals($dateFrom, $dateTo, Tag $tag){

            $query = '
                    SELECT
                            SUM(pp.total_sum) as debit,
                            SUM(0) as credit,
                            UNIX_TIMESTAMP(p.invoice_time) as `time`,
                            p.id,
                            p.number,
                            "" as info,
                            c.id as contact_id,
                            c.company_name as contact_name
                    FROM
                            purchase p
                            LEFT JOIN wholesaler c ON c.id = p.contact_id
                            LEFT JOIN purchase_product pp ON pp.purchase_id = p.id

                    WHERE
        ';

            $wheres = array();

            $wheres[] = 'pp.tag_id = ' . $tag->id;

            if( $dateFrom ){
                $wheres[] = 'DATE(p.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\'';
            }

            if( $dateTo ){
                $wheres[] = 'DATE(p.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\'';
            }

            $wheres[] = 'p.paid_time != \'0000-00-00 00:00:00\'';

            $query .= implode("\nAND\n", $wheres);
            $query .= "\n";
            $query .= "GROUP BY p.id";
            $query .= "\n";
            $query .= " ORDER BY p.invoice_time ASC ";

            $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            return $results;
        }
        
        public function getContactsReport($year, $month) {
            $param = $year . '-' . $month;
            
            $query = ' 
                SELECT w.id, w.vat_number, sum(p.total_sum) total_sum, sum(p.vat_sum) vat_sum
                FROM `purchase` p 
                LEFT JOIN `wholesaler` w ON w.id = p.contact_id
                WHERE DATE_FORMAT(p.invoice_time, "%Y-%m") = "'.$param.'"
                GROUP BY p.contact_id
                ORDER BY p.contact_id
            ';
            
            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            
            return $result;
        }

        public function getContactsUnpaidPurchasesTotal($dateFrom, $dateTo){
            $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
            $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));

            $query = '
                    SELECT

                            SUM(p.total_sum) - IFNULL(SUM(pp.amount), 0) as sum,
                            IF( DATE(DATE_FORMAT(p.invoice_time, "%Y-%m-01")) >= DATE(' . $this->getAdapter()->quote(date('Y-m-01', $dateFrom)) . '),
                                UNIX_TIMESTAMP(DATE_FORMAT(p.invoice_time, "%Y-%m-01")),
                                "-"
                            ) as `time`,
                            p.contact_id,
                            c.company_name as contact_name

                    FROM
                            purchase p
                            LEFT JOIN wholesaler c ON c.id = p.contact_id
                            LEFT JOIN purchase_payment pp ON pp.purchase_id = p.id

                    WHERE
                            DATE(p.paid_time) = "0000-00-00"
                            AND
                            c.id IS NOT NULL
                            AND
                            DATE(DATE_FORMAT(p.invoice_time, "%Y-%m-01")) <= DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateTo)) . ')

                    GROUP BY p.contact_id, IF( DATE(DATE_FORMAT(p.invoice_time, "%Y-%m-01")) < DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateFrom)) . '), 1, DATE_FORMAT(p.invoice_time, "%Y-%m"))
                    ORDER BY CONCAT(c.company_name, c.id) ASC, p.invoice_time DESC
        ';


            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            $dates = array();
            $date = $dateFrom;

            do {
                $dates[] = $date;
                $date = strtotime('+ 1 MONTH', $date);
            }while($date < $dateTo);


            $grouped = array();
            foreach( $result as $row ){
                if( !array_key_exists($row['contact_id'], $grouped) ){
                    $grouped[$row['contact_id']] = array();
                    $grouped[$row['contact_id']]['contact'] = array();
                    $grouped[$row['contact_id']]['contact']['id'] = $row['contact_id'];
                    $grouped[$row['contact_id']]['contact']['name'] = $row['contact_name'];
                    $grouped[$row['contact_id']]['totals'] = array();
                    $grouped[$row['contact_id']]['totals'] = array_combine(array_values($dates), array_fill(0, count($dates), array('sum' => 0)));
                    $grouped[$row['contact_id']]['totals']['-'] = array('sum' => 0);
                }

                $grouped[$row['contact_id']]['totals'][ $row['time'] ] = $row;
            }

            foreach( $grouped as $key => $val ){
                if( !array_key_exists('total', $val) ){
                    $grouped[$key]['total'] = 0;
                }

                foreach( $val['totals'] as $k => $v ){
                    $grouped[$key]['total'] += $v['sum'];
                }
            }



            $datesWithDash = $dates;
            $datesWithDash[] = '-';
            $result = array('contacts' => array(), 'total' => array('totals' => array_combine(array_values($datesWithDash), array_fill(0, count($datesWithDash), 0)), 'total' => 0));

            if( !$grouped ){
                return $result;
            }

            $grouped = array_combine(array_keys(array_fill(0, count($grouped),0)), $grouped);
            $result['contacts'] = $grouped;

            foreach( $result['contacts'] as $key => $val ){
                foreach( $val['totals'] as $date => $val ){
                    $result['total']['totals'][$date] += $val['sum'];
                    $result['total']['total'] += $val['sum'];
                }
            }

            return $result;
        }

        public function getUnpaidPurchases($dateFrom, $dateTo, $contactId=0){
            $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
            $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));

            $query = '
                    SELECT
                            p.*

                    FROM
                            purchase p

                    WHERE
                            DATE(p.paid_time) = "0000-00-00"
                            ' . ($contactId ? ('AND p.contact_id = ' . $this->getAdapter()->quote($contactId)) : '' ) . '
                            AND
                            DATE(p.invoice_time) >= DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateFrom)) . ')
                            AND
                            DATE(p.invoice_time) <= DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateTo)) . ')

                    ORDER BY p.invoice_time DESC
        ';


            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

            foreach( $result as $key => $val ){
                $result[$key] = new Purchase();
                $result[$key]->load($val);
            }

            return $result;
        }

        public function vatOverview($dateFrom, $dateTo){
            $categoryModel = new TagCategoryModel();
            $categories = $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_PURCHASE);
            $tagsIDs = array('0');
            foreach( $categories as $category ){
                foreach( $category->tags as $tag ){
                    $tagsIDs[] = $tag->id;
                }
            }

            $query = '
                SELECT
                        IF(t.id IS NOT NULL, t.name, "Uncategorized") as tag,
                        SUM(pp.total_sum) as total_excl_vat,
                        SUM(pp.total_sum * (pp.vat / 100)) as vat_sum,
                        pp.vat as vat
                FROM
                        purchase p
                        LEFT JOIN purchase_product pp ON pp.purchase_id = p.id
                        LEFT JOIN tag t ON t.id = pp.tag_id

                WHERE
    ';

            $wheres = array();

            $wheres[] = $this->getAdapter()->quoteInto('pp.tag_id IN(?)', $tagsIDs);

            if( $dateFrom ){
                $wheres[] = 'DATE(p.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\'';
            }

            if( $dateTo ){
                $wheres[] = 'DATE(p.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\'';
            }

            $query .= implode("\nAND\n", $wheres);
            $query .= "\n";
            $query .= "GROUP BY pp.vat, pp.tag_id";
            $query .= "\n";
            $query .= "ORDER BY pp.vat ASC";

            $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            return $results;
        }

        public function vatGovernment($dateFrom, $dateTo){
            $vatCategoryModel = new VatCategory();
            $categories = $vatCategoryModel->findAll(array(array('type = ?', VatCategoryModel::TYPE_PURCHASE)));
            $govIDs = array();
            foreach( $categories as $category ){
                $govIDs[] = $category->id;
            }

            /*$query = '
                    SELECT
                            CONCAT( "(", vc.code, ") ", vc.name) as category,
                            vc.code as code,
                            SUM(pp.total_excl_vat) as total_excl_vat,
                            SUM(pp.total_sum * (pp.vat / 100)) as vat_sum
                    FROM
                            vat_category vc
                            LEFT JOIN tag t ON t.vat_category_id = vc.id
                            LEFT JOIN purchase_product pp ON pp.tag_id = t.id
                            LEFT JOIN purchase p ON p.id = pp.purchase_id

                    WHERE
        ';*/
            $query = '
                    SELECT
                            CONCAT( "(", vc.code, ") ", vc.name) as category,
                            vc.code as code,
                            SUM(pp.total_sum) as total_excl_vat,
                            SUM(pp.total_sum * (pp.vat / 100)) as vat_sum
                    FROM
                            vat_category vc
                            LEFT JOIN tag t ON t.vat_category_id = vc.id
                            LEFT JOIN purchase_product pp ON pp.tag_id = t.id
                            LEFT JOIN purchase p ON p.id = pp.purchase_id

                    WHERE
        ';

            $wheres = array();

            $wheres[] = $this->getAdapter()->quoteInto('vc.id IN(?)', $govIDs);

            if( $dateFrom ){
                $wheres[] = '( p.id IS NULL OR DATE(p.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\')';
            }

            if( $dateTo ){
                $wheres[] = '( p.id IS NULL OR DATE(p.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\')';
            }

            $query .= implode("\nAND\n", $wheres);
            $query .= "\n";
            $query .= "GROUP BY vc.id";
            $query .= "\n";
            $query .= "ORDER BY vc.ord ASC";

            $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            return $results;
        }

        public function search($search){

            $query = "
                    SELECT
                            pch.*
                    FROM
                            purchase pch
                            LEFT JOIN contact c ON c.id = pch.contact_id
                            LEFT JOIN purchase_product pp ON pp.purchase_id = pch.id
                            LEFT JOIN product p ON p.id = pp.product_id

                    WHERE
                            (
                                " . $this->getAdapter()->quoteInto('pch.number LIKE ?',       '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('pch.info LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.firstname LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.lastname LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.company_name LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.vat_number LIKE ?',   '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('pp.description LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.name LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.article_code LIKE ?', '%' . $search . '%') . "
                            )

                    GROUP BY pch.id
                    ORDER BY pch.id DESC
                    LIMIT 20
                 ";

            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

            foreach( $result as $key => $row ){
                $result[$key] = new Purchase();
                $result[$key]->load($row);
            }

            return $result;
        }
        
        public function getPurchase($purchase_number){                                                            
            return $this->select()->where('number = ? ', $purchase_number)->query(Zend_Db::FETCH_OBJ)->fetchAll();
        }
        
        public function setPaidPurchase($purchase_number, $kas_date) {
            $date = date("Y-m-d H:i:s", strtotime($kas_date));
            $data = array(
                'paid_time' => $date
            );

            $n = $this->update($data, "number = '$purchase_number'");
        }
        
        public function findUnpaidAllPurchases($amount, $number){ 
            $select = $this->select();
            if ( !empty($amount) )
                $select = $select->where('total_sum = ?', $amount);
            if ( !empty($number) )
                $select = $select->where('number like ?', '%'.$number.'%');
            return $select->where('paid_time = ?', '0000-00-00 00:00:00')->query(Zend_Db::FETCH_OBJ)->fetchAll();
        }
    }