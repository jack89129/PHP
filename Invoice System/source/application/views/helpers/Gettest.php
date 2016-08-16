<?php

class Jaycms_View_Helper_Gettest extends Zend_View_Helper_Abstract
{
    
	public function Gettest($id)
    {
    	$model = new Model();
    	$item = $model->findById($id);
    	return $item;
    }
}
?>