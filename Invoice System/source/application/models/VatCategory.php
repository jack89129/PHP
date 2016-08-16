<?php

    /**
     * @property int $id
     * @property string $code
     * @property string $name
     */
    class VatCategory extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new VatCategoryModel(), $id);
        }
    }