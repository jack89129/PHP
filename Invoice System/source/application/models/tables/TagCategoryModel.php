<?php

    class TagCategoryModel extends Jaycms_Db_Model {
        protected $_name = 'tag_category';

        const TYPE_INVOICE = 'invoice';
        const TYPE_PURCHASE = 'purchase';
        const TYPE_OTHER = 'other';
        const TYPE_TEMPORARY = 'temporary';

        /**
         * @return TagCategory[]
         */
        public function getCategoriesByType($type){
            $categories = $this->select()->where('type = ?', $type)->order('natsort(name, "natural") ASC')->query()->fetchAll(Zend_Db::FETCH_ASSOC);

            foreach( $categories as $key => $category ){
                $categories[$key] = new TagCategory();
                $categories[$key]->load($category);
            }

            return $categories;
        }
    }