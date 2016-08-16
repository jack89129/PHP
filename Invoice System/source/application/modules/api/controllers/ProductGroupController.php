<?php

    class Api_ProductGroupController extends Jaycms_Controller_Action {

        public function getGroupAction(){
            $group = new ProductGroup((int) $this->_getParam('id'));

            if( !$group->exists() ){
                throw new Exception(_t("Product group does not exists!"));
            }

            $this->_helper->json(stringify($group->data()));
        }

        public function getGroupsAction(){
            $groups = ProductGroup::all();

            $result = array();
            foreach( $groups as $group ){
                $result[] = $group->data();
            }

            $this->_helper->json(stringify($result));
        }
    }