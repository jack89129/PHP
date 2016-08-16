<?php
//
// +--------------------------------------------------------------------+
// | ZF Skeleton Demo Application - by Onlime Webhosting                |
// +--------------------------------------------------------------------+
// | Copyright (c) 2007-2009 Onlime Webhosting, Philip Iezzi            |
// |                         http://www.onlime.ch/                      |
// +--------------------------------------------------------------------+
// | Powered by Zend Framework - http://framework.zend.com/             |
// +--------------------------------------------------------------------+
//

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure Zend Framework library is on include_path
set_include_path('.' 
					. PATH_SEPARATOR . get_include_path()
					. PATH_SEPARATOR . '../library'
					. PATH_SEPARATOR . '../application/classes'
					. PATH_SEPARATOR . '../application/models'					 					 

					);

/** Zend_Application */					
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend');
$autoloader->setFallBackAutoloader(true); 


$application->bootstrap()
            ->run();


