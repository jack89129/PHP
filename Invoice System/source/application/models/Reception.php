<?php

    /**
     * @property int $id         
     * @property string $name
     */
    class Reception extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new ReceptionModel(), $id);
        }
    }