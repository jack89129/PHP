<?php

    /**
     * @property int $id
     * @property string $ip
     */
    class Ip extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new IpModel(), $id);
        }        

    }