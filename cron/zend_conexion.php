<?php

set_time_limit(0);
ini_set('memory_limit', '500M');
date_default_timezone_set("America/Lima");
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

# define de elementos de los productos
defined('APPLICATION_ELEMENTOS_DIR')
        || define('APPLICATION_ELEMENTOS_DIR', realpath(dirname(__FILE__) . '/../public/imagenes'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )));

require_once 'Zend/Application.php';

$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);

$db = $application->getBootstrap()->initCron();
Zend_Registry::set('db', $db);