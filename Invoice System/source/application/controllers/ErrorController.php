<?php
require_once 'Zend/Controller/Action.php';

/**
 * ErrorController 
 * 
 * @uses      Zend_Controller_Action
 * @package   Spindle
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version   $Id: $
 */
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $this->_helper->viewRenderer->setViewSuffix('phtml');
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                $this->view->code    = 404;
                if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER) {
                    $this->view->info = sprintf(
                                            'Unable to find controller "%s" in module "%s"', 
                                            $errors->request->getControllerName(),
                                            $errors->request->getModuleName()
                                        );
                }
                if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION) {
                    $this->view->info = sprintf(
                                            'Unable to find action "%s" in controller "%s" in module "%s"', 
                                            $errors->request->getActionName(),
                                            $errors->request->getControllerName(),
                                            $errors->request->getModuleName()
                                        );
                }
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                $this->view->code    = 500;
                $this->view->info    = $errors->exception;
                break;
        }

        if( APPLICATION_ENV == 'testing' ){
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo "Error: ", (string) $errors->exception;
        }

        if( Zend_Registry::isRegistered('force-json') && Zend_Registry::get('force-json') ){
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo '<div id="response">';
            echo Zend_Json::encode(array('error' => !is_scalar($this->view->info) ? $this->view->info->getMessage() : $this->view->info));
            echo '</div>';
            return;
        }

    	if( (Zend_Registry::isRegistered('api') && Zend_Registry::get('api')) || $this->getRequest()->isXmlHttpRequest() ){
    		$json = array('error' => !is_scalar($this->view->info) ? $this->view->info->getMessage() : $this->view->info, 'exception' => '');

    		
    		if( APPLICATION_ENV == "development" ){
    			$json['exception'] = (string) $this->view->info;
    		}
    		
    		$this->_helper->json($json);
    	}
    }
}
