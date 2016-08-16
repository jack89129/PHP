<?php
class InvoiceModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'invoice';    
    
    const STATUS_NEW = 'new';
    const STATUS_FINAL = 'final';

    const PROFORMA_STATUS_NEW = 'new';
    const PROFORMA_STATUS_OPEN = 'open';
    const PROFORMA_STATUS_ACCEPTED = 'accepted';
    const PROFORMA_STATUS_DENIED = 'denied';
    const PROFORMA_STATUS_INVOICE = 'invoice';
    const PROFORMA_STATUS_ARCHIVE = 'archive';
    
    const WEBSHOP_INVOICE = 'yes';
    const NORMAL_INVOICE = 'no';
    
            
    public function findInvoices($type, $contactId, $employeeId, $proforma, $credit, $date_from=0, $date_to=0, &$total=0, &$sum=0.0, &$sum_no_vat=0.0, $limit=null, $page=null){

        $employee = new Employee($employeeId);

        $query =    "
                        SELECT
                                {select}
                        FROM
                                invoice i
                                LEFT JOIN contact_employee_map cem ON cem.contact_id = i.contact_id
                                " . (!$employee->can('contact_view_all') ? ' AND ' . $this->getAdapter()->quoteInto('cem.employee_id = ?', $employeeId) : '') . "
                        WHERE

                    ";

        $wheres = array(1);

        $wheres[] = $this->getAdapter()->quoteInto('proforma = ?', $proforma ? 1 : 0);
        $wheres[] = $this->getAdapter()->quoteInto('credit = ?', $credit ? 1 : 0);
        
        $method = 'where' . implode('', array_map('ucfirst', preg_split('@[^A-Z0-9]+@i', $type)));
        if( method_exists($this, $method) ){
            $wheres = call_user_func_array(array($this, $method), array($wheres));
        }else{
            throw new Exception(_t("Unknown search method!"));
        }

        if( $contactId ){
            $wheres[] = $this->getAdapter()->quoteInto('i.contact_id = ?', $contactId);
        }

        if( $employeeId && !$employee->can('contact_view_all') ){
            $wheres[] = "(
                            " . $this->getAdapter()->quoteInto('i.created_by = ?', $employeeId) . "
                            OR
                            cem.id IS NOT NULL
                         )";
        }

        if( $date_from ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.invoice_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
        }

        if( $date_to ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.invoice_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
        }


        $query .= implode("\nAND\n", $wheres);
        $statQuery = $query;

        $query .= "\nGROUP BY i.id\n";
        $query .= "\nORDER BY i.number DESC\n";

        if( $limit !== null && $page !== null ){
            $query .= "LIMIT " . ((int)$limit) . " OFFSET " . ((int) $page*$limit);
        }
        
        $result = $this->getAdapter()->query(str_replace('{select}', 'i.*', $query))->fetchAll(Zend_Db::FETCH_OBJ);
        $stat    = $this->getAdapter()->query(str_replace('{select}', '  COUNT(*) as `count`,
                                                                        SUM(i.total_sum) as `sum`,
                                                                        SUM(i.total_excl_vat) as `sum_no_vat`', $statQuery))
                                     ->fetch(Zend_Db::FETCH_OBJ);

        $total     = $stat->count;
        $sum    = $stat->sum;
        $sum_no_vat = $stat->sum_no_vat;

        foreach( $result as $key => $invoice ){
            $result[$key] = new Invoice();
            $result[$key]->load($invoice);
        }
        
        return $result;
    } 
    
    public function findInvoicesForPDF($type, $contactId, $employeeId, $proforma, $credit, $date_from=0, $date_to=0, &$total=0, &$sum=0.0, &$sum_no_vat=0.0, $limit=null, $page=null){

        $employee = new Employee($employeeId);

        $query =    "
                        SELECT
                                {select}
                        FROM
                                invoice i
                                LEFT JOIN contact_employee_map cem ON cem.contact_id = i.contact_id
                                " . (!$employee->can('contact_view_all') ? ' AND ' . $this->getAdapter()->quoteInto('cem.employee_id = ?', $employeeId) : '') . "
                        WHERE
                            i.from_webshop = 'no' AND 
                    ";

        $wheres = array(1);

        $wheres[] = $this->getAdapter()->quoteInto('proforma = ?', $proforma ? 1 : 0);
        $wheres[] = $this->getAdapter()->quoteInto('credit = ?', $credit ? 1 : 0);
        
        $method = 'where' . implode('', array_map('ucfirst', preg_split('@[^A-Z0-9]+@i', $type)));
        if( method_exists($this, $method) ){
            $wheres = call_user_func_array(array($this, $method), array($wheres));
        }else{
            throw new Exception(_t("Unknown search method!"));
        }

        if( $contactId ){
            $wheres[] = $this->getAdapter()->quoteInto('i.contact_id = ?', $contactId);
        }

        $wheres[] = "(
                        " . $this->getAdapter()->quoteInto('i.created_by = ?', $employeeId) . "
                        OR
                        cem.id IS NOT NULL
                     )";

        if( $date_from ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.invoice_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
        }

        if( $date_to ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.invoice_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
        }


        $query .= implode("\nAND\n", $wheres);
        $statQuery = $query;

        $query .= "\nGROUP BY i.id\n";
        $query .= "\nORDER BY id DESC\n";

        if( $limit !== null && $page !== null ){
            $query .= "LIMIT " . ((int)$limit) . " OFFSET " . ((int) $page*$limit);
        }
        
        $result = $this->getAdapter()->query(str_replace('{select}', 'i.*', $query))->fetchAll(Zend_Db::FETCH_OBJ);
        $stat    = $this->getAdapter()->query(str_replace('{select}', '  COUNT(*) as `count`,
                                                                        SUM(i.total_sum) as `sum`,
                                                                        SUM(i.total_excl_vat) as `sum_no_vat`', $statQuery))
                                     ->fetch(Zend_Db::FETCH_OBJ);

        $total     = $stat->count;
        $sum    = $stat->sum;
        $sum_no_vat = $stat->sum_no_vat;

        foreach( $result as $key => $invoice ){
            $result[$key] = new Invoice();
            $result[$key]->load($invoice);
        }
        
        return $result;
    }                             
    
    public function findInvoicesForOverige($type, $employeeId, $date_from=0, $date_to=0, &$total=0, &$sum=0.0, &$sum_no_vat=0.0, $payment="bank"){

        $employee = new Employee($employeeId);

        $query =    "
                        SELECT
                                {select}
                        FROM
                                invoice i
                                LEFT JOIN invoice_payment ip ON i.id = ip.invoice_id
                                LEFT JOIN contact_employee_map cem ON cem.contact_id = i.contact_id
                                " . (!$employee->can('contact_view_all') ? ' AND ' . $this->getAdapter()->quoteInto('cem.employee_id = ?', $employeeId) : '') . "
                        WHERE
                            i.status != 'new' AND
                            ip.payment_method = '".$payment."' AND 
                    ";

        $wheres = array(1);
        
        $method = 'where' . implode('', array_map('ucfirst', preg_split('@[^A-Z0-9]+@i', $type)));
        if( method_exists($this, $method) ){
            $wheres = call_user_func_array(array($this, $method), array($wheres));
        }else{
            throw new Exception(_t("Unknown search method!"));
        }

        if( $employeeId && !$employee->can('contact_view_all') ){
            $wheres[] = "(
                            " . $this->getAdapter()->quoteInto('i.created_by = ?', $employeeId) . "
                            OR
                            cem.id IS NOT NULL
                         )";
        }

        if( $date_from ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.paid_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
        }

        if( $date_to ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.paid_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
        }


        $query .= implode("\nAND\n", $wheres);
        $statQuery = $query;

        $query .= "\nGROUP BY i.id\n";
        $query .= "\nORDER BY id DESC\n";
        
        $result = $this->getAdapter()->query(str_replace('{select}', 'i.*', $query))->fetchAll(Zend_Db::FETCH_OBJ);
        $stat    = $this->getAdapter()->query(str_replace('{select}', '  COUNT(*) as `count`,
                                                                        SUM(i.total_sum) as `sum`,
                                                                        SUM(i.total_excl_vat) as `sum_no_vat`', $statQuery))
                                     ->fetch(Zend_Db::FETCH_OBJ);

        $total     = $stat->count;
        $sum    = $stat->sum;
        $sum_no_vat = $stat->sum_no_vat;
        
        

        foreach( $result as $key => $invoice ){
            $result[$key] = new Invoice();
            $result[$key]->load($invoice);
        }
        
        return $result;
    }    
    
    public function findPaidInvoicesForPDF($type, $employeeId, $date_from=0, $date_to=0, &$total=0, &$sum=0.0, &$sum_no_vat=0.0){

        $employee = new Employee($employeeId);

        $query =    "
                        SELECT
                                {select}
                        FROM
                                invoice i
                                LEFT JOIN contact_employee_map cem ON cem.contact_id = i.contact_id
                                " . (!$employee->can('contact_view_all') ? ' AND ' . $this->getAdapter()->quoteInto('cem.employee_id = ?', $employeeId) : '') . "
                        WHERE
                            i.status != 'new' AND i.from_webshop = 'no' AND 
                    ";

        $wheres = array(1);
        
        $method = 'where' . implode('', array_map('ucfirst', preg_split('@[^A-Z0-9]+@i', $type)));
        if( method_exists($this, $method) ){
            $wheres = call_user_func_array(array($this, $method), array($wheres));
        }else{
            throw new Exception(_t("Unknown search method!"));
        }
        $wheres[] = "(
                        " . $this->getAdapter()->quoteInto('i.created_by = ?', $employeeId) . "
                        OR
                        cem.id IS NOT NULL
                     )";

        if( $date_from ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.paid_time) >= ?', date(Constants::MYSQL_DAY_FORMAT, $date_from));
        }

        if( $date_to ){
            $wheres[] = $this->getAdapter()->quoteInto('DATE(i.paid_time) <= ?', date(Constants::MYSQL_DAY_FORMAT, $date_to));
        }

        $query .= implode("\nAND\n", $wheres);
        $statQuery = $query;

        $query .= "\nGROUP BY i.id\n";
        $query .= "\nORDER BY id DESC\n";
        
        $result = $this->getAdapter()->query(str_replace('{select}', 'i.*', $query))->fetchAll(Zend_Db::FETCH_OBJ);
        $stat    = $this->getAdapter()->query(str_replace('{select}', '  COUNT(*) as `count`,
                                                                        SUM(i.total_sum) as `sum`,
                                                                        SUM(i.total_excl_vat) as `sum_no_vat`', $statQuery))
                                     ->fetch(Zend_Db::FETCH_OBJ);

        $total     = $stat->count;
        $sum    = $stat->sum;
        $sum_no_vat = $stat->sum_no_vat;
        
        

        foreach( $result as $key => $invoice ){
            $result[$key] = new Invoice();
            $result[$key]->load($invoice);
        }
        
        return $result;
    }                              

    public function whereAll($wheres){
        return $wheres;
    }
    
    public function whereAlmost($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.status = ?', self::STATUS_FINAL);
        return $wheres;
    }
    
    public function whereUrgent($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.paid_time = ?', '0000-00-00 00:00:00');
        $wheres[] = 'i.step = 1';
        $wheres[] = 'TO_DAYS(NOW()) - TO_DAYS(i.expire_time) >= '.((int) Constants::INVOICE_URGENT_DAYS);
        return $wheres;
    }
    
    public function whereOutstanding($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.status != ?', self::STATUS_NEW);
        $wheres[] = $this->getAdapter()->quoteInto('i.paid_time = ?', '0000-00-00 00:00:00');
        //$wheres[] = 'i.step = 0';
        //$wheres[] = 'i.expire_time >= NOW()';
        return $wheres;
    }
    
    public function whereJudge($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.status != ?', self::STATUS_NEW);
        $wheres[] = $this->getAdapter()->quoteInto('i.paid_time = ?', '0000-00-00 00:00:00');
        $wheres[] = 'i.step = 2';
        $wheres[] = 'TO_DAYS(NOW()) - TO_DAYS(i.expire_time) > ' . ((int) Constants::INVOICE_JUDGE_DAYS);
        return $wheres;
    }
    
    public function whereLate($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.status != ?', self::STATUS_NEW);
        $wheres[] = $this->getAdapter()->quoteInto('i.paid_time = ?', '0000-00-00 00:00:00');
        $wheres[] = 'i.step = 1';
        $wheres[] = 'NOW() > i.expire_time';
        return $wheres;
    }
    
    public function whereUnsent($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.status = ?', self::STATUS_NEW);
        return $wheres;
    }

    public function wherePaid($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.paid_time != ?', '0000-00-00 00:00:00');
        return $wheres;
    }

    public function whereSent($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.paid_time = ?', '0000-00-00 00:00:00');
        $wheres[] = $this->getAdapter()->quoteInto('i.status = ?', self::STATUS_FINAL);
        return $wheres;
    }

    public function whereProformaOpen($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.proforma_status = ?', self::PROFORMA_STATUS_OPEN);
        return $wheres;
    }

    public function whereProformaAccepted($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.proforma_status = ?', self::PROFORMA_STATUS_ACCEPTED);
        return $wheres;
    }

    public function whereProformaDenied($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.proforma_status = ?', self::PROFORMA_STATUS_DENIED);
        return $wheres;
    }

    public function whereProformaInvoice($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.proforma_status = ?', self::PROFORMA_STATUS_INVOICE);
        return $wheres;
    }

    public function whereProformaArchive($select){
        $wheres[] = $this->getAdapter()->quoteInto('i.proforma_status = ?', self::PROFORMA_STATUS_ARCHIVE);
        return $wheres;
    }

    public function whereProformaExpired($wheres){
        $wheres[] = $this->getAdapter()->quoteInto('i.status != ?', self::STATUS_NEW);
        $wheres[] = $this->getAdapter()->quoteInto('i.paid_time = ?', '0000-00-00 00:00:00');
        $wheres[] = 'i.expire_time < NOW()';
        return $wheres;
    }

    public function findUnpaidInvoices(){
        return $this->select()->where('status = ?', self::STATUS_FINAL)->where('paid_time = ?', '0000-00-00 00:00:00')->query(Zend_Db::FETCH_OBJ)->fetchAll();
    }
    
    public function getInvoices($invoice_number){                                                            
        return $this->select()->where('number = ? ', $invoice_number)->query(Zend_Db::FETCH_OBJ)->fetchAll();
    }
    
    public function findUnpaidAllInvoices($amount, $number){       
        $select = $this->select();
        if ( !empty($amount) )
            $select = $select->where('total_sum = ?', $amount);                                                     
        if ( !empty($number) )
            $select = $select->where('number like ?', '%'.$number.'%');
        return $select->where('paid_time = ?', '0000-00-00 00:00:00')->query(Zend_Db::FETCH_OBJ)->fetchAll();
    }
    
    public function getContactsReport($year, $month) {
        $param = $year . '-' . $month;
        
        $query = ' 
            SELECT w.id, w.vat_number, sum(p.total_sum) total_sum, sum(p.vat_sum) vat_sum
            FROM `invoice` p 
            LEFT JOIN `contact` w ON w.id = p.contact_id
            WHERE w.is_intro = 1 AND DATE_FORMAT(p.invoice_time, "%Y-%m") = "'.$param.'"
            GROUP BY p.contact_id
            ORDER BY p.contact_id
        ';
        
        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
        
        return $result;
    }

    public function getTotalByTag($dateFrom, $dateTo, Tag $tag, $group){

        $query = '
                    SELECT
                            SUM(ip.total_sum) as sum,
                            UNIX_TIMESTAMP(i.invoice_time) as `time`
                    FROM
                            invoice i
                            LEFT JOIN invoice_product ip ON ip.invoice_id = i.id

                    WHERE
        ';

        $wheres = array();

        $wheres[] = 'ip.tag_id = ' . $tag->id;

        if( $dateFrom ){
            $wheres[] = 'DATE(i.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\'';
        }

        if( $dateTo ){
            $wheres[] = 'DATE(i.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\'';
        }

        $wheres[] = 'i.status = ' . $this->getAdapter()->quote(InvoiceModel::STATUS_FINAL);
        $wheres[] = 'i.proforma = 0';
        $wheres[] = 'i.credit = 0';

        $query .= implode("\nAND\n", $wheres);
        $query .= "\n";
        $query .= "GROUP BY " . strtoupper($group) . "(i.invoice_time)";

        $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
        return $results;
    }

    public function getTagTotals($dateFrom, $dateTo, Tag $tag){
        $query = '
                    SELECT
                            SUM(IF(i.credit, 0, ip.total_sum)) as debit,
                            SUM(IF(i.credit, ip.total_sum, 0)) as credit,
                            UNIX_TIMESTAMP(i.invoice_time) as `time`,
                            i.id,
                            i.number,
                            i.info,
                            c.id as contact_id,
                            CONCAT( c.firstname, " ", c.lastname) as contact_name
                    FROM
                            invoice i
                            LEFT JOIN contact c ON c.id = i.contact_id
                            LEFT JOIN invoice_product ip ON ip.invoice_id = i.id

                    WHERE
        ';

        $wheres = array();

        $wheres[] = 'ip.tag_id = ' . $tag->id;

        if( $dateFrom ){
            $wheres[] = 'DATE(i.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\'';
        }

        if( $dateTo ){
            $wheres[] = 'DATE(i.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\'';
        }

        $wheres[] = 'i.status = ' . $this->getAdapter()->quote(InvoiceModel::STATUS_FINAL);
        $wheres[] = 'i.proforma = 0';

        $query .= implode("\nAND\n", $wheres);
        $query .= "\n";
        $query .= "GROUP BY i.id";
        $query .= "\n";
        $query .= "ORDER BY i.invoice_time ASC";

        $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
        return $results;
    }

    public function getContactsUnpaidInvoicesTotal($dateFrom, $dateTo){
        $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
        $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));

        $query = '
                    SELECT

                            SUM(i.total_sum) - IFNULL(SUM(ip.amount),0) as sum,
                            IF( DATE(DATE_FORMAT(i.invoice_time, "%Y-%m-01")) >= DATE(' . $this->getAdapter()->quote(date('Y-m-01', $dateFrom)) . '),
                                UNIX_TIMESTAMP(DATE_FORMAT(i.invoice_time, "%Y-%m-01")),
                                "-"
                            ) as `time`,
                            i.contact_id,
                            CONCAT(c.firstname, " ", c.lastname) as contact_name

                    FROM
                            invoice i
                            LEFT JOIN contact c ON c.id = i.contact_id
                            LEFT JOIN invoice_payment ip ON ip.invoice_id = i.id
                    WHERE
                            DATE(i.paid_time) = "0000-00-00"
                            AND
                            i.status = ' . $this->getAdapter()->quote(self::STATUS_FINAL) . '
                            AND
                            c.id IS NOT NULL
                            AND
                            DATE(DATE_FORMAT(i.invoice_time, "%Y-%m-01")) <= DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateTo)) . ')

                    GROUP BY i.contact_id, IF( DATE(DATE_FORMAT(i.invoice_time, "%Y-%m-01")) < DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateFrom)) . '), 1, DATE_FORMAT(i.invoice_time, "%Y-%m"))
                    ORDER BY CONCAT(c.firstname, c.id) ASC, i.invoice_time DESC
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
                //$result['total']['totals'][$date] += $val['sum'];
                $result['total']['total'] += $val['sum'];
            }
        }

        return $result;
    }
    
    public function getOrderInvoices($contactId){
        $select = $this->_db->select()
                            ->from($this->_name)
                            ->where("contact_id = '".$contactId."%'")
                            ->where("from_webshop = 'yes'")
                            ->order('id DESC');
        $this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);    
        $result = $this->getAdapter()->fetchAll($select);
        
        foreach( $result as $key => $val ){
            $result[$key] = new Invoice();
            $result[$key]->load($val);
        }
    
        return $result;    
    }

    public function getUnpaidInvoices($dateFrom, $dateTo, $contactId=0){
        $dateFrom   = mktime(0,0,0, date('m', $dateFrom), 1, date('Y', $dateFrom));
        $dateTo     = mktime(0,0,-1, date('m', $dateTo)+1, 1, date('Y', $dateTo));

        $query = '
                    SELECT
                            i.*

                    FROM
                            invoice i
                            LEFT JOIN contact c ON c.id = i.contact_id
                    WHERE
                            DATE(i.paid_time) = "0000-00-00"
                            AND
                            i.status = ' . $this->getAdapter()->quote(self::STATUS_FINAL) . '
                            AND ' . ($contactId ? ('c.id = ' . $this->getAdapter()->quote($contactId)) : 'c.id IS NOT NULL' ) . '
                            AND
                            DATE(i.invoice_time) >= DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateFrom)) . ')
                            AND
                            DATE(i.invoice_time) <= DATE(' . $this->getAdapter()->quote(date('Y-m-d', $dateTo)) . ')

                    ORDER BY i.invoice_time DESC
        ';


        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

        foreach( $result as $key => $val ){
            $result[$key] = new Invoice();
            $result[$key]->load($val);
        }

        return $result;
    }

    public function vatOverview($dateFrom, $dateTo){
        $categoryModel = new TagCategoryModel();
        $categories = $categoryModel->getCategoriesByType(TagCategoryModel::TYPE_INVOICE);
        $tagsIDs = array('0');
        foreach( $categories as $category ){
            foreach( $category->tags as $tag ){
                $tagsIDs[] = $tag->id;
            }
        }

        $query = 'SELECT tt.name as tag, r.total_excl_vat, r.vat_sum, tt.vat
                  FROM tag tt
                  LEFT JOIN 
                  (
                    SELECT
                            IF(t.id IS NOT NULL, t.name, "Uncategorized") as tag,
                            SUM(ip.total_sum) as total_excl_vat,
                            SUM(ip.total_sum * (ip.vat / 100)) as vat_sum,
                            ip.vat as vat
                    FROM
                            invoice i
                            LEFT JOIN invoice_product ip ON ip.invoice_id = i.id
                            LEFT JOIN tag t ON t.vat = ip.vat

                    WHERE
        ';

        $wheres = array();

        $wheres[] = $this->getAdapter()->quoteInto('ip.tag_id IN(?)', $tagsIDs);

        if( $dateFrom ){
            $wheres[] = 'DATE(i.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\'';
        }

        if( $dateTo ){
            $wheres[] = 'DATE(i.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\'';
        }

        $wheres[] = 'i.status = ' . $this->getAdapter()->quote(InvoiceModel::STATUS_FINAL);
        $wheres[] = 'i.proforma = 0';
        $wheres[] = 'i.credit = 0';

        $query .= implode("\nAND\n", $wheres);
        $query .= "\n";
        $query .= "GROUP BY ip.vat";
        $query .= "\n";
        $query .= "ORDER BY ip.vat ASC ) r ON tt.vat = r.vat";

        $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
        return $results;
    }

    public function vatGovernment($dateFrom, $dateTo){
        $vatCategoryModel = new VatCategory();
        $categories = $vatCategoryModel->findAll(array(array('type = ?', VatCategoryModel::TYPE_INVOICE)));
        $govIDs = array();
        foreach( $categories as $category ){
            $govIDs[] = $category->id;
        }

        $query = '
                    SELECT
                            CONCAT( "(", vc.code, ") ", vc.name) as category,
                            vc.code as code,
                            SUM(ip.total_sum) as total_excl_vat,
                            SUM(ip.total_sum * (ip.vat / 100)) as vat_sum
                    FROM
                            vat_category vc
                            LEFT JOIN tag t ON t.vat_category_id = vc.id
                            LEFT JOIN invoice_product ip ON ip.tag_id = t.id
                            LEFT JOIN invoice i ON i.id = ip.invoice_id

                    WHERE
        ';

        $wheres = array();

        $wheres[] = $this->getAdapter()->quoteInto('vc.id IN(?)', $govIDs);

        if( $dateFrom ){
            $wheres[] = '( i.id IS NULL OR DATE(i.invoice_time) >= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateFrom) . '\')';
        }

        if( $dateTo ){
            $wheres[] = '( i.id IS NULL OR DATE(i.invoice_time) <= \'' . date(Constants::MYSQL_DAY_FORMAT, $dateTo) . '\')';
        }

        $wheres[] = '( i.id IS NULL OR i.status = ' . $this->getAdapter()->quote(InvoiceModel::STATUS_FINAL) . ')';
        $wheres[] = '( i.id IS NULL OR i.proforma = 0 )';

        $query .= implode("\nAND\n", $wheres);
        $query .= "\n";
        $query .= "GROUP BY vc.id";
        $query .= "\n";
        $query .= "ORDER BY vc.ord ASC";

        $results = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
        return $results;
    }

    public function search($search, $offers, $credit = 0){

        $query = "
                    SELECT
                            i.*
                    FROM
                            invoice i
                            LEFT JOIN contact c ON c.id = i.contact_id
                            LEFT JOIN invoice_product ip ON ip.invoice_id = i.id
                            LEFT JOIN product p ON p.id = ip.product_id

                    WHERE
                            (
                                " . $this->getAdapter()->quoteInto('i.number LIKE ?',       '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('i.info LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.firstname LIKE ?',    '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.lastname LIKE ?',     '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.company_name LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('c.vat_number LIKE ?',   '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('ip.description LIKE ?', '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.name LIKE ?',         '%' . $search . '%') . "
                                OR
                                " . $this->getAdapter()->quoteInto('p.article_code LIKE ?', '%' . $search . '%') . "
                            )
                            AND
                            i.proforma = " . ((int) $offers) . "
                            AND
                            i.credit = " . ((int) $credit) . "
                            
                    GROUP BY i.id
                    ORDER BY i.id DESC
                    LIMIT 20
                 ";

        $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);

        foreach( $result as $key => $row ){
            $result[$key] = new Invoice();
            $result[$key]->load($row);
        }

        return $result;
    }
    
    public function findInvoiceForPDF($employeeId=0, $date){        
            $db = Zend_Registry::get('db1');
            
            $sql = "
                SELECT pp.product_id id, pp.description name, sum(pp.qty) snum FROM `invoice_product` pp
                LEFT JOIN `invoice` p ON pp.invoice_id = p.id
                WHERE p.from_webshop = 'no' AND DATE(p.invoice_time) = '".date(Constants::MYSQL_DAY_FORMAT, strtotime($date))."' and p.created_by = '".$employeeId."'
                GROUP BY pp.product_id
                ORDER BY pp.product_id";
                
            $stmt = $db->query($sql);

            $result = $stmt->fetchAll();

            $invoices = array();
            foreach ( $result as $row ) {
                $invoices["id$row[id]"] = $row;
            }

            return $invoices;
        }
    
    public function setPaidInvoice($invoice_number, $kas_date) {
        $date = date("Y-m-d H:i:s", strtotime($kas_date));
        $data = array(
            'paid_time' => $date,
            'status'    => 'final'
        );

        $n = $this->update($data, "number = '$invoice_number'");
    }
    
    public function setUnPaidInvoice($id) {
        $date = "0000-00-00 00:00:00";
        $data = array(
            'paid_time' => $date,
            'status'    => 'final'
        );

        $n = $this->update($data, "id = '$id'");
    }
    
    public function setStepInvoice($id, $step) {
        $date = date('Y-m-d');
        $data = array(
            'expire_time' => $date,
            'step'    => $step
        );

        $n = $this->update($data, "id = '$id'");
    }
    
    public function getAllInvoices($isProforma, $prefix = ''){        
        $result = $this->select()
                ->where('proforma = ?', $isProforma)
                ->where('number like ?', $prefix.'%')
                ->order('number')
                ->query(Zend_Db::FETCH_OBJ)->fetchAll();

        foreach( $result as $key => $row ){
            $result[$key] = new Invoice();
            $result[$key]->load($row);
        }

        return $result;
    }
}

