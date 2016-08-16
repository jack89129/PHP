<?php
class Jaycms_Controller_Action extends Zend_Controller_Action
{
	public function init(){

        //file_put_contents(dirname(APPLICATION_PATH) . "/cache/log.txt", print_r($this->_getAllParams(), true), FILE_APPEND);

        if( $this->getRequest()->getModuleName() == 'api' ){
            Zend_Registry::set('api', true);
            $this->disableLayoutAndRender();

            if( !Utils::user() && $this->getRequest()->getControllerName() != 'login' ){
                throw new Exception("Access denied! Login to system!");
            }
        }                                                                         
        
        if ( $this->getRequest()->getControllerName() == 'register' ){
            return;
        }

        try {
            if( !Utils::user() && $this->getRequest()->getControllerName() != 'login' ){
                $this->_redirect('/login');
            }                                                                     
            
            $contact = SettingsModel::getInvoiceProviderBankNumber();
            if ( $contact == "" && $this->getRequest()->getControllerName() != 'contact' ) {
                $this->_redirect('/settings/contact?first=true');
            }
        } catch ( Exception $ex ) {
            $user = new Zend_Session_Namespace('user');
            $user->id = null;    
            $minstock_products = new Zend_Session_Namespace('min_stock_products');
            $minstock_products->id_list = null;
            $this->_redirect('/');
        }
	}

	public function preDispatch(){
        $this->view->user = Utils::user();
	}
	
	public function disableLayout(){
		$this->view->layout()->disableLayout();
	}
	public function disableLayoutAndRender(){
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	}
	
	public function redirect($url){
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		return $redirector->gotoUrl($url);
	}

	
	
	
}
