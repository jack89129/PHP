<?php

    /**
     * @property int $id
     * @property int $agenda_id
     * @property int $type_id
     * @property int $menu_id    
     * @property int $buffet
     *
     * @property Agenda $agenda
     * @property MenuType $type
     * @property MenuProduct $menu
     */
    class AgendaMenu extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new AgendaMenuModel(), $id);
        }

        public function relations(){
            return array(
                'agenda' => array('Agenda', 'agenda_id', self::HAS_ONE),
                'type' => array('MenuType', 'type_id', self::HAS_ONE),
                'menu' => array('MenuProduct', 'menu_id', self::HAS_ONE)
            );
        }
    }