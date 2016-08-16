<?php

    /**
     * @property int $id         
     * @property string $name
     */
    class AgendaLocation extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            return parent::__construct(new AgendaLocationModel(), $id);
        }
    }