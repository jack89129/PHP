<?php

    class VatCategoryModel extends Jaycms_Db_Model {

        const TYPE_INVOICE  = 'invoice';
        const TYPE_PURCHASE = 'purchase';
        const TYPE_OTHER    = 'other';

        protected $_name = 'vat_category';
        
        public function getCategories($type){
            return $this->select()->where('type = ?', $type)->order('code')->query(Zend_Db::FETCH_OBJ)->fetchAll();
        }
    }