<?php

    /**
     * @property int $id
     * @property string $check_date
     * @property string $end_date
     * @property int $inc_count
     * @property int $eq_count
     * @property int $dec_count
     */
    class Checkdate extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new CheckdateModel(), $id);
        }        

    }
