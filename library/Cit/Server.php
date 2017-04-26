<?php

class Cit_Server
{

    /**
     *
     * @var ZExtraLib_Application_Resource_Server
     */
    protected $server;
    static $fileServer = null;

    public static function getInstance()
    {
        if (self::$fileServer === null) {
            self::$fileServer = new self();
        }
        return self::$fileServer;
    }

    /**
     *
     * @return ZExtraLib_Application_Resource_Server
     */
    public function getServer()
    {
        return $this->server;
    }

    protected function __construct()
    {
        $this->server = Zend_Registry::get('Server');
    }

    public static function getDb($name = null)
    {
        if ($name === null) {
            return self::getInstance()->getServer()->getDb()->system;
        } else {
            return self::getInstance()->getServer()->getDb()->$name;
        }
    }

    public static function getContent()
    {
        return (object) self::getInstance()->getServer()->getContent();
    }

    public static function getStatic()
    {
        return (object) self::getInstance()->getServer()->getStatic();
    }

    public static function getFile($index = 0)
    {
        $file = self::getInstance()->getServer()->getFile();
        return (object) $file[$index];
    }

}