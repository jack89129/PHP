<?php

    /**
     * @property int $id
     * @property int $purchase_id
     * @property string $paid_time
     * @property float $amount
     * @property string $payment_method
     *
     * @property Purchase[] $purchase
     *
     */

    class PurchasePayment extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new PurchasePaymentModel(), $id);
        }

        public function relations(){
            return array(
                'purchase' => array('Purchase', 'purchase_id', self::HAS_ONE)
            );
        }

        public function getPaidTime(){
            return $this->get('paid_time') != 0 ? strtotime($this->get('paid_time')) : 0 ;
        }

        public function setPaidTime($value){
            $this->set('paid_time', $value ? date(Constants::MYSQL_DATE_FORMAT, $value) : 0 );
        }
    }