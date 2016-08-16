<?php

    /**
     * @property int $id
     * @property string $type  
     */
    class AgendaPartyType extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new AgendaPartyTypeModel(), $id);
        }
    }