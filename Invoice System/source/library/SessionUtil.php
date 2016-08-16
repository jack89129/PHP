<?php
require_once("Zend/Session.php");
/**
 * This class is used to read/write to the user HTTP Session
 * 
 * @package application/library
 */
class SessionUtil {

	protected static $_instance;
    public $namespace = null;   
	
	/*
	 * Construct if no session is set.
	 */    
	private function __construct() {
			/* Start the session */
			Zend_Session::start();

			/* Create default namcespace */
			$this->namespace = new Zend_Session_Namespace();

			/* Initialize */
			if (!isset($this->namespace->initialized)) {
			    Zend_Session::regenerateId();
			    $this->namespace->initialized = true;
			}
	}

	/**
	 * Singleton pattern
	 */
	public static function getInstance() {
        if (!isset(self::$instance)){
            self::$_instance = new SessionUtil();
        }
        return self::$_instance;
    }
    
    /**
     * Function to read values from the 'default' session namespace 
     * 
     * @param $sessionVariable : used session variable
     * @return saved value or NULL if session var not set
     */
	public function getSessionValue($sessionVariable){
    	return (isset($this->namespace->$sessionVariable)) ? $this->namespace->$sessionVariable : null;
    }
    
    /**
     * Function to save values to the 'default' session namespace 
     * 
     * @param $sessionVariable : used session variable
     */
    public function setSessionValue($sessionVariable, $value){    
    	if($sessionVariable != null){	
    		$this->namespace->$sessionVariable = $value;
    	}
    }
    
	/**
     * Function to remove variables from the 'default' session namespace 
     * 
     * @param $sessionVariable : used session variable
     */
    public function removeSessionVarible($sessionVariable){    
    	if($sessionVariable != null){	
    		$this->namespace->unset($sessionVariable);
    	}
    }
}
?>
