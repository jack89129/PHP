<?php

    /**
     * @property int $id         
     * @property string $name
     * @property int $amount
     */
    class MenuProduct extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new MenuProductModel(), $id);
        }
    }