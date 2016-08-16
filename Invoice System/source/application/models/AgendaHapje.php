<?php

    /**
     * @property int $id
     * @property int $agenda_id
     * @property int $hapje_id
     *
     * @property Agenda $agenda
     * @property Hapje $hapje
     */
    class AgendaHapje extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new AgendaHapjeModel(), $id);
        }

        public function relations(){
            return array(
                'agenda' => array('Agenda', 'agenda_id', self::HAS_ONE),
                'hapje' => array('Hapje', 'hapje_id', self::HAS_ONE)
            );
        }
    }