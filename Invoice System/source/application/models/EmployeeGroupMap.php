<?php

    /**
     * @property int $id
     * @property int $employee_id
     * @property int $employee_group_id
     *
     * @property Employee $employee
     * @property EmployeeGroup $group
     */
    class EmployeeGroupMap extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new EmployeeGroupMapModel(), $id);
        }

        public function relations(){
            return array(
                'employee' => array('Employee', 'employee_id', self::HAS_ONE),
                'group' => array('EmployeeGroup', 'employee_group_id', self::HAS_ONE)
            );
        }
    }