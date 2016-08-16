<?php

    /**
     * @property string $kas_date
     * @property float $amount 
     *  
     * @author james
     *
     */
    class Kasboek extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            $this->kas_date = null;
            $this->amount = 0.0;
            $this->afsch = null;
            parent::__construct(new KasboekModel(), $id);
        }

        public function getKasDate(){
            return $this->get('kas_date') != null ? $this->get('kas_date') : date('Y-m-d');
        }

        public function setKasDate($value){
            $this->set('kas_date', $value ? $value : date('Y-m-d'));
        }

        public function getAmount(){
            return $this->get('amount') != 0 ? $this->get('amount') : 0 ;
        }

        public function setAmount($value){
            $this->set('amount', $value ? $value : 0 );
        }
        
    }