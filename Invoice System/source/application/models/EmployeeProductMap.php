<?php

    /**
     * @property int $id
     * @property int $employee_id
     * @property int $product_id
     *
     * @property Employee $employee
     * @property Product $product
     */
    class EmployeeProductMap extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new EmployeeProductMapModel(), $id);
        }

        public function relations(){
            return array(
                'employee' => array('Employee', 'employee_id', self::HAS_ONE),
                'product' => array('Product', 'product_id', self::HAS_ONE)
            );
        }
        
        public function create(){
            $this->save();
        }
    }