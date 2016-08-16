<?php 
error_reporting( E_ALL | E_STRICT );

define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));
define('APPLICATION_PATH', BASE_PATH . '/application');


// Include path
set_include_path(
    '.'
    . PATH_SEPARATOR . BASE_PATH . '/library'
    . PATH_SEPARATOR . BASE_PATH . '/application/classes'
    . PATH_SEPARATOR . BASE_PATH . '/application/models'
    . PATH_SEPARATOR . get_include_path()
);



// Define application environment
define('APPLICATION_ENV', 'testing');

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallBackAutoloader(true);

// Create application, bootstrap, and run
            
require_once 'controllers/ControllerTestCase.php';