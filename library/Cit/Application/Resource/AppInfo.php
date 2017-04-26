<?php
class Cit_Application_Resource_AppInfo extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_REGISTRY_KEY = 'AppInfo';
    protected $_appInfo = null;
    public function init ()
    {
        $this->_appInfo = null;
        return $this->getAppInfo();
    }
    public function getAppInfo ()
    {
        if ($this->_appInfo === null) {
            $options = $this->getOptions();
            $name = isset($options['name']) ? $options['name'] : '';
            $author = isset($options['author']) ? $options['author'] : '';
            $version = isset($options['version']) ? $options['version'] : '';
            $date = isset($options['date']) ? $options['date'] : '';
            $this->_appInfo = new Cit_Plugin_AppInfo($name, $author, 
            $version, $date);
            $key = (isset($options['registry_key']) &&
             ! is_numeric($options['registry_key'])) ? $options['registry_key'] : self::DEFAULT_REGISTRY_KEY;
            Zend_Registry::set($key, $this->_appInfo);
        }
        return $this->_appInfo;
    }
}