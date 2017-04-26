<?php

/**
 * shortDescription
 *
 * longDescription
 *
 * @category   category
 * @package    package
 * @subpackage subpackage
 * @copyright  Leer archivo COPYRIGHT
 * @license    Leer archivo LICENSE
 * @version    Release: @package_version@
 */
class Cit_Application_Resource_Server extends Zend_Application_Resource_ResourceAbstract
{

    const DEFAULT_REGISTRY_KEY = 'Server';

    /**
     * description
     * @param paramType paramName paramDescription
     * @uses class::name()
     * @return returnType returnDescription
     */
    function init()
    {
        $options = $this->getOptions();
        $options['db']['defaultDb'] = $this->getDefaultDb();
        Zend_Registry::set(self::DEFAULT_REGISTRY_KEY, new Cit_Plugin_Server($options['content'], $options['static'], $options['file'], $options['db']));
        //$this->getBootstrap()->bootstrap('session');
        //session_start();
        return Zend_Registry::get(self::DEFAULT_REGISTRY_KEY);
    }

    function getDefaultDb()
    {
        return Zend_Db_Table::getDefaultAdapter();
        /* try {
          $bootstrap = $this->getBootstrap();
          $multidb = &$bootstrap->getPluginResource('multidb');
          return $multidb->getDefaultDb();
          } catch (Zend_Application_Resource_Exception $exc) {
          echo $exc->getTraceAsString();
          } */
    }

}