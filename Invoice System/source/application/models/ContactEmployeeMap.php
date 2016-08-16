<?php

    /**
     * @property int $id
     * @property int $contact_id
     * @property int $employee_id
     */
    class ContactEmployeeMap extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new ContactEmployeeMapModel(), $id);
        }

        public function relations(){
            return array(
                'contact' => array('Contact', 'contact_id', self::HAS_ONE),
                'employee' => array('Employee', 'employee_id', self::HAS_ONE)
            );
        }
    }