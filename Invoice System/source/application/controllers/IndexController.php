<?php

class IndexController extends Jaycms_Controller_Action
{

     public function init()
    {
        parent::init();
        $this->redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
    }

    public function indexAction()
    {   
        $this->_redirect('/invoices');
    } 
    
    public function dashboardAction()
    {
        
    }
    
    public function stockAction()
    {
        
    }
    
    public function testAction(){
        die(_t('asdf'));
    }

}

