<?php

    class Right extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new RightModel(), $id);
        }
    }