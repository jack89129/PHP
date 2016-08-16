<?php

    /**
     * @property int $id
     * @property int wholesaler_id
     * @property int group_id
     *
     * @property Wholesaler $wholesaler
     * @property WholesalerGroup $group
     */
    class WholesalerGroupMap extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new WholesalerGroupMapModel(), $id);
        }

        public function relations(){
            return array(
                'wholesaler' => array('Wholesaler', 'wholesaler_id', self::HAS_ONE),
                'group' => array('WholesalerGroup', 'group_id', self::HAS_ONE)
            );
        }

    }