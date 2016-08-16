<?php

    /**
     * @property int $id         
     * @property string $type
     */
    class MenuType extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new MenuTypeModel(), $id);
        }
    }