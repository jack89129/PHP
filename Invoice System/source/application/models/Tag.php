<?php

    /**
     * @property int $id
     * @property int $tag_category_id
     * @property string $name
     * @property int|null $vat
     * @property int $vat_category_id
     *
     * @property TagCategory[] $category
     * @property VatCategory[] $vat_category;
     */
    class Tag extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new TagModel(), $id);
        }

        public function relations(){
            return array(
                'category' => array('TagCategory', 'tag_category_id', self::HAS_ONE),
                'vat_category' => array('VatCategory', 'vat_category_id', self::HAS_ONE)
            );
        }

        public function getNameWithCategory(){
            if( $this->category ){
                return $this->category->name . ' - ' . $this->name;
            }

            return $this->name;
        }

        public function setVat($vat){
            $this->set('vat', strlen($vat) ? (int) $vat : null);
        }
    }