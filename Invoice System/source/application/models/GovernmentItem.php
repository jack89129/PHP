<?php

    /**
     * @property string $code
     * @property int $type
     * @property string $name
     *
     */
    class GovernmentItem extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new GovernmentItemModel(), $id);
        }

    }