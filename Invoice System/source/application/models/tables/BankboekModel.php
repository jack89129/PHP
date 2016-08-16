<?php
class BankboekModel extends Jaycms_Db_Model {
    /**
     * The default table name 
     */
    protected $_name = 'bankboek';    
    
    public function findKasboek($date) {
        return $this->select()->where('kas_date = ?', $date)->query(Zend_Db::FETCH_OBJ)->fetchAll();
    }
    
    public function searchKasboek($afsch) {
        return $this->select()->where('afsch like ?', '%'.$afsch.'%')->query(Zend_Db::FETCH_OBJ)->fetchAll();
    }
    
    public function getLatestBalance($date) {
        $result = $this->select()
                ->where('kas_date <= ?', $date)
                ->order('kas_date DESC')
                ->query(Zend_Db::FETCH_OBJ)->fetchAll();
        if ( empty($result) ) return 0.00;
        return $result[0]->amount;
    }
    
    public function getInitialBalance($date) {
        $result = $this->select()
                ->where('kas_date >= ?', $date)
                ->order('kas_date')
                ->query(Zend_Db::FETCH_OBJ)->fetchAll();
        if ( empty($result) ) return 0.00;
        return $result[0]->amount;
    }
    
    public function getEndBalance($date) {
        $balance = $this->findKasboek($date);
        if ( empty($balance) ) {
            return 0.00;
        }
        return $balance[0]->amount;
    }
    
    public function getStartBalance($date) {
        $balance = $this->findKasboek($date);
        if ( empty($balance) ) {
            $amount = $this->getLatestBalance($date);
            $this->setStartBalance($date, $amount);
            $this->setEndBalance($date, $amount);
            return $amount;
        }
        return $balance[0]->before_amount;
    }
    
    public function setStartBalance($date, $amount) {
        $balance = $this->findKasboek($date);
        if ( empty($balance) ) {
            $data = array(
                'kas_date' => $date,
                'before_amount' => $amount,
            );
            $this->insert($data);
        } else {
            $data = array(
                'before_amount' => $amount,
            );

            $n = $this->update($data, "kas_date = '$date'");
        }
    }
    
    public function getAfschrift($date) {
        $balance = $this->findKasboek($date);
        if ( empty($balance) ) {
            return "";
        }
        return $balance[0]->afsch;
    }
    
    public function saveAfsch($date, $afsch) {
        $data = array(
            'afsch' => $afsch
        );

        $n = $this->update($data, "kas_date = '$date'");
    }
    
    public function setEndBalance($date, $amount) {
        $balance = $this->findKasboek($date);
        if ( empty($balance) ) {
            $data = array(
                'kas_date' => $date,
                'amount' => $amount,
            );
            $this->insert($data);
        } else {
            $data = array(
                'amount' => $amount
            );

            $n = $this->update($data, "kas_date = '$date'");
        }
        
        $tomorrow = date('Y-m-d', strtotime($date) + 86400 );
        $balance = $this->findKasboek($tomorrow);
        if ( empty($balance) ) {
            $data = array(
                'kas_date' => $tomorrow,
                'before_amount' => $amount,
                'amount' => $amount,
            );
            $this->insert($data);
        } else {
            $data = array(
                'before_amount' => $amount
            );

            $n = $this->update($data, "kas_date = '$tomorrow'");
        }
    }
    
    public function getMonthBankboek($year_month, &$total=0, &$sum=0.0, &$sum_no_vat=0.0){
        $query = "
                    SELECT s.kas_date mdate, s.afsch afsch, t.number, t.contact, t.credit, t.debit
                    FROM
                    (
                        SELECT i.number number, CONCAT(ic.firstname, ' ', ic.lastname) contact, i.total_sum credit, 0 debit, DATE(i.paid_time) paid_time
                        FROM invoice i
                        LEFT JOIN invoice_payment ip ON i.id = ip.invoice_id
                        LEFT JOIN contact ic ON i.contact_id = ic.id
                        WHERE i.paid_time != '0000-00-00 00:00:00' AND i.status = 'final' AND ip.payment_method = 'bank' AND i.paid_time like '".$year_month."%' 
                        UNION
                        SELECT p.number number, CONCAT(pc.firstname2, ' ', pc.lastname2) contact, 0 credit, p.total_sum debit, DATE(p.paid_time) paid_time
                        FROM purchase p
                        LEFT JOIN purchase_payment pp ON p.id = pp.purchase_id
                        LEFT JOIN wholesaler pc ON p.contact_id = pc.id
                        WHERE p.paid_time != '0000-00-00 00:00:00' AND pp.payment_method = 'bank' AND p.paid_time like '".$year_month."%' 
                    ) t
                    LEFT JOIN bankboek s ON s.kas_date = t.paid_time
                    ";
        /*
        SELECT s.kas_date mdate, s.afsch afsch, t.number, t.contact, t.credit, t.debit
FROM
(SELECT i.number number, CONCAT(ic.firstname, ' ', ic.lastname) contact, i.total_sum credit, 0 debit, DATE(i.paid_time) paid_time
FROM invoice i
LEFT JOIN invoice_payment ip ON i.id = ip.invoice_id
LEFT JOIN contact ic ON i.contact_id = ic.id
WHERE i.paid_time != '0000-00-00 00:00:00' AND 
    i.status = 'final' AND
        ip.payment_method = 'bank'
UNION
SELECT p.number number, CONCAT(pc.firstname2, ' ', pc.lastname2) contact, 0 credit, p.total_sum debit, DATE(p.paid_time) paid_time
FROM purchase p
LEFT JOIN purchase_payment pp ON p.id = pp.purchase_id
LEFT JOIN wholesaler pc ON p.contact_id = pc.id
WHERE p.paid_time != '0000-00-00 00:00:00' AND 
        pp.payment_method = 'bank') t
LEFT JOIN bankboek s ON s.kas_date = t.paid_time
ORDER BY s.kas_date
        */
            
            $statQuery = $query;
            
            $query .= "\nORDER BY s.kas_date\n";

            $result = $this->getAdapter()->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            $stat    = $this->getAdapter()->query(str_replace('{select}', '  COUNT(*) as `count`,
                                                                        SUM(p.total_sum) as `sum`,
                                                                        SUM(p.total_excl_vat) as `sum_no_vat`', $statQuery))
                                     ->fetch(Zend_Db::FETCH_OBJ);

            $total     = $stat->count;
            $sum    = $stat->sum;
            $sum_no_vat = $stat->sum_no_vat;

            return $result;
    }
    
}

