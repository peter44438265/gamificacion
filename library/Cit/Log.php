<?php

class Cit_Log
{

    /**
     *
     * @var Zend_Log
     */
    protected $logger;
    static $fileLogger = null;

    public static function getInstance()
    {
        if (self::$fileLogger === null) {
            self::$fileLogger = new self();
        }
        return self::$fileLogger;
    }

    /**
     *
     * @return Zend_Log
     */
    public function getLog()
    {
        return $this->logger;
    }

    protected function __construct()
    {
        $this->logger = Zend_Registry::get('Log');
    }

    /**
     * log a message
     * @param string $message
     */
    public static function info($message)
    {
        self::getInstance()->getLog()->info(self::getMessage($message));
    }

    /**
     * log a message
     * @param string $message
     */
    public static function err($message)
    {
        self::getInstance()->getLog()->err(self::getMessage($message));
    }

    /**
     * log a message
     * @param string $message
     */
    public static function warn($message)
    {
        self::getInstance()->getLog()->warn(self::getMessage($message));
    }

    public static function crit($message)
    {
        self::getInstance()->getLog()->crit(self::getMessage($message));
    }

    public static function getMessage($message)
    {
        return $_SERVER['REMOTE_ADDR'] . '|' . $message;
    }

}