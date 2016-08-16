<?php
/**
 * Helper for retrieving provider logo
 */
class Jaycms_View_Helper_ProviderLogo extends Zend_View_Helper_Abstract
{
	const PATH = "/resources/invoice/provider/";
	
	public function providerLogo(){
		return $this;
	}
	
    /**
     * Return URL of provider logo
     * 
     * @return string
     */
    public function url(){
         return $this->view->baseUrl() . self::PATH . "logo.jpg";
    }
    
	/**
     * Return path of provider logo
     * 
     * @return string
     */
    public function path(){
         return dirname(APPLICATION_PATH) . "/public" . self::PATH . "logo.jpg";
    }
    
	/**
     * Return true if provider logo exists
     * 
     * @return string
     */
    public function exists(){
         return file_exists($this->path());
    }
}
