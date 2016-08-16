<?php

class Webshop_IndexController extends Jaycms_Controller_Action
{

    public function init()
    {	
    	parent::init();
    	$this->view->page_title = _t("Webshop");
    	$this->view->page_sub_title = _t("demo...");
    	$this->view->current_module = "webshop";
        $has_webshop = SettingsModel::getWebshopActivation();
        if ( $has_webshop != 'on' ) {
            $this->_redirect('/');
        }
    }

    public function indexAction()
    {
		$productModel = new ProductModel();
        $result = $productModel->getWebshopProducts();
        $product = null;
        $group_name = "";
        if ( !empty($result) ) {
            $product = new Product();
            $product->load($result[0]);
            $group_name = $result[0]['group_name'];
        }
        $this->view->result = $result;
        $this->view->product = $product;
        $this->view->group_name = $group_name;
    }
    
    public function getAction()
    {
        $productId = (int) $this->_getParam('id', 0);
        
        $product = new Product($productId);
        $product = $product->exists() ? $product : null ;
        $group_name = $product->group->name;
        
        $result = array();
        $result['content'] = $this->view->partial('index/_partials/webshop-view.phtml', array('product' => $product, 'group_name' => $group_name));
        
        $this->_helper->json($result);
    }
}

