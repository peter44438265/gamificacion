<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initConfig() {
        //Zend_Session::start();
        $dir = dirname(dirname(__FILE__)) . '/application/configs/application.ini';
        $config = new Zend_Config_Ini($dir, APPLICATION_ENV);
        Zend_Registry::set('config', $config);
        $sessionSistema = new Zend_Session_Namespace('Sadm');
        Zend_Registry::set('Sadm', $sessionSistema);
        $session = new Zend_Session_Namespace('Susuario');
        Zend_Registry::set('Susuario', $session);
        $cache = $this->getPluginResource('cachemanager')
                        ->getCacheManager()
                        ->getCache('default');
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('Cache', $cache);
        //Log
        Zend_Registry::set('Log', $this->getPluginResource('log')->getLog());

        //Cache de página
        $page = $this->getPluginResource('cachemanager')->getCacheManager()->getCache('page');
        $expires = $page->getOption('lifetime');
        $response = new Zend_Controller_Response_Http();
        $response->setHeader('Content-Type', 'text/html; charset=utf-8')
                ->setHeader('Accept-Encoding', 'gzip, deflate')
                ->setHeader('Expires', 'max-age=' . $expires, true)
                ->setHeader('Cache-Control', 'private', 'must-revalidate')
                ->setHeader('Pragma', 'no-cache', true);
        $this->getPluginResource('frontController')->getFrontController()->setResponse($response);
    }

    protected function initAutoload() {
        // BLOCK 1
        // this is a fallback to autoload our own classes in library
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->setFallbackAutoloader(true);
       
        // BLOCK 2
        // this is for loading forms classes in default module
        $autoloader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => 'Coffee',
                    'basePath' => APPLICATION_PATH . '/modules/coffee/',
                    'resourceTypes' => array(
                        'forms' => array('path' => '/forms',
                            'namespace' => 'Form_')
                    )
                        )
        );
       // var_dump($autoloader);exit;
         return $autoLoader;
    }
    

    public function _initZendDate() {
        Zend_Date::setOptions(array('format_type' => 'php'));
    }

    public function initCron() {
        $this->bootstrap('session');
        $this->bootstrap('multidb');
        $this->bootstrap('mail');
        $this->_initZendDate();
        $this->initCache();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);

        $adapter = Zend_Db_Table::getDefaultAdapter();
        return $adapter;
    }

    public function initCache() {
        $cache = $this->getPluginResource('cachemanager')->getCacheManager()->getCache('default');
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('Cache', $cache);
    }

    protected function _initRegistry() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/pagoefectivo.ini', $this->getEnvironment());
        Zend_Registry::set('pefectivo', $config);
    }

}

function manejador_mensajes_error($no, $str, $file, $line) {
    if (APPLICATION_ENV == 'development') {
//        throw new ErrorException($str, $no, 0, $file, $line);
        throw new Zend_Exception($str, $no);
    } else {

    }
}

function manejador_mensajes_error_cron($no, $str, $file, $line) {
//        throw new ErrorException($str, $no, 0, $file, $line);
    throw new Zend_Exception($str, $no);
}

