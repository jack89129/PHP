<?php

    /**
     * @property int $id
     * @property string $name
     * @property int $deleted
     *
     * @property Product[] $products
     */
    class ProductGroup extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new ProductGroupModel(), $id);
        }

        public function relations(){
            return array(
                'products' => array('Product', 'product_group_id', self::HAS_MANY, array(array('deleted = ?', 0)), array('natsort(name, "natural") ASC'))
            );
        }

        public function isDeleted(){
            return (bool) $this->deleted;
        }

        public static function all($where=array(array('deleted = ?', 0)), $sort=array('natsort(name, "natural") ASC')){
            $productGroup = new ProductGroup();
            return $productGroup->findAll($where, $sort);
        }
    }