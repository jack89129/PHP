<?php
require_once("Zend/Session.php");

class SessionWrapper {
    protected static $_instance;
    public $namespace = null;

	private function __construct() {
			/* Explicitly start the session */
		try {
			Zend_Session::start();
		} catch (Exception $e) {
		}
			

			/* Create our Session namespace - using 'Default' namespace */
			$this->namespace = new Zend_Session_Namespace();

			/** Check that our namespace has been initialized - if not, regenerate the session id
			 * Makes Session fixation more difficult to achieve
 			 */
			if (!isset($this->namespace->initialized)) {
			    Zend_Session::regenerateId();
			    $this->namespace->initialized = true;
			}
	}

	/**
	 * Implementation of the singleton design pattern
	 * See http://www.talkphp.com/advanced-php-programming/1304-how-use-singleton-design-pattern.html
	 */
	public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
	public static function getId() {
		if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return Zend_Session::getId();
    }
    
    public function setSessVarInNamespace($namespace, $var, $value){    	
    	$ns = new Zend_Session_Namespace($namespace);
    	$ns->$var = $value;
    }

	public function getSessVarFromNamespace($namespace, $var, $default=null){
    	if(Zend_Session::namespaceIsset($namespace)){
    		$ns = new Zend_Session_Namespace($namespace);
    		$result = (isset($ns->$var)) ? $ns->$var: $default ;
    	} else {
    		$result = $default;
    	}
    	return $result;	
    }
    
    public function removeNameSpace($namespace){
    	if(Zend_Session::namespaceIsset($namespace)){
    		$ns = new Zend_Session_Namespace($namespace);
    		$ns->unsetAll();
    	}
    }
    

    /**
     * Public method to retrieve a value stored in the session
     * return $default if $var not found in session namespace
     * @param $var string
     * @param $default string
     * @return string
     */
    public function getSessVar($var, $default=null){
    	return (isset($this->namespace->$var)) ? $this->namespace->$var : $default;
    }

    /**
     * Public function to save a value to the session
     * @param $var string
     * @param $value
     */
    public function setSessVar($var, $value){
    	if (!empty($var) && !empty($value)){
    		$this->namespace->$var = $value;
    	}
    }
}
?>
