<?php

    /**
     * @property int $id
     * @property int $employee_id
     * @property int $product_id
     * @property int $transit
     * @property int $reservation
     * @property int $reservation_pending
     *
     * @property Employee $employee
     * @property Product $product
     */
    class EmployeeStock extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new EmployeeStockModel(), $id);
        }

        public function relations(){
            return array(
                'employee' => array('Employee', 'employee_id', self::HAS_ONE),
                'product' => array('Product', 'product_id', self::HAS_ONE)
            );
        }
    }