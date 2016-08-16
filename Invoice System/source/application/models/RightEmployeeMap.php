<?php

    /**
     * @property int $id
     * @property int $right_id
     * @property int $employee_id
     */
    class RightEmployeeMap extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new RightEmployeeMapModel(), $id);
        }

        public function relations(){
            return array(
                'right' => array('Right', 'right_id', self::HAS_ONE),
                'employee' => array('Employee', 'employee_id', self::HAS_ONE)
            );
        }
    }