<?php

    /**
     * @property int $id
     * @property string $name
     * @property int|null $vat
     * @property string $type
     *
     * @property Tag[] $tags
     */
    class TagCategory extends Core_ActiveRecord_Row {

        public function __construct($id=null){
            parent::__construct(new TagCategoryModel(), $id);
        }

        public function relations(){
            return array(
                'tags' => array('Tag', 'tag_category_id', self::HAS_MANY, array(), array('natsort(id, "natural") ASC'))
            );
        }

        public function setVat($vat){
            $this->set('vat', strlen($vat) ? (int) $vat : null);
        }
    }