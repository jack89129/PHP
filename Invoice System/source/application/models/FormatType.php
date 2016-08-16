<?php

    /**
     * @property int $id
     * @property string $value  
     */
    class FormatType extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new FormatTypeModel(), $id);
        }
    }