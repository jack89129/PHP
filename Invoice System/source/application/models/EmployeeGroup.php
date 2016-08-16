<?php

    /**
     * @property int $id
     * @property string $name
     *
     * @property Employee[] $employees
     */
    class EmployeeGroup extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new EmployeeGroupModel(), $id);
        }

        public function relations(){
            return array(
                'employees' => array('Employee', array('EmployeeGroupMap' => array('employee_group_id', 'employee_id')), self::HAS_MANY_TO_MANY)
            );
        }

        public static function all($where=array(), $sort=array('natsort(name, "natural") ASC')){
            $emmployeeGroup = new EmployeeGroup();
            return $emmployeeGroup->findAll($where, $sort);
        }
    }