<?php

    /**
     * @property int $id
     * @property string $yearmonth
     * @property string $code
     * @property float $total
     *
     */
    class Report extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new ReportModel(), $id);
        }
        
        public function create(){
            $reportModel = new ReportModel();
            $diff = $reportModel->saveReport($this->yearmonth, $this->code, $this->total);
            return $diff;
        }

    }