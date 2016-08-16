<?php

    /**
     * @property int $id         
     * @property string $value
     */
    class Hapje extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new HapjeModel(), $id);
        }
    }