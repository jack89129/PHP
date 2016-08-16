<?php

    /**
     * @property int $id
     * @property string $name
     *
     * @property Wholesaler[] $wholesalers
     */
    class WholesalerGroup extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new WholesalerGroupModel(), $id);
        }

        public function relations(){
            return array(
                'wholesalers' => array('Wholesaler', array('WholesalerGroupMap' => array('group_id', 'wholesaler_id')), self::HAS_MANY_TO_MANY, array(), array('firstname ASC'))
            );
        }

        public static function all($where=array(), $sort=array('natsort(name, "natural") ASC')){
            $wholesalerGroup = new WholesalerGroup();
            return $wholesalerGroup->findAll($where, $sort);
        }

    }