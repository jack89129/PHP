<?php

    /**
     * @property int $id
     * @property int $employee_id
     * @property float $latitude
     * @property float $longitude
     * @property int $time
     */
    class EmployeeLocation extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new EmployeeLocationModel(), $id);
        }


        public function getTime(){
            return $this->get('time') != 0 ? strtotime($this->get('time')) : 0;
        }

        public function setTime($value){
            $this->set('time', $value ? date(Constants::MYSQL_DATE_FORMAT, $value) : 0);
        }
    }