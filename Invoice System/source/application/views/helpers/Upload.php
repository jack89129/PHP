<?php

    class Jaycms_View_Helper_Upload extends Zend_View_Helper_Abstract {

        public function upload(){
            return $this->view->partial('_helpers/upload.phtml');
        }
    }