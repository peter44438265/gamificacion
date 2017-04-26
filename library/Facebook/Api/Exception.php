<?php

/**
 * @author  Laurynas Karvelis <laurynas.karvelis@gmail.com>
 * @author  Explosive Brains Limited
 * @license http://sam.zoy.org/wtfpl/COPYING
 */
class Facebook_Api_Exception extends Exception
{
    /**
     * @var array The result from the API server that represents the exception information.
     */
    protected $_result;

    /**
     * Make a new API Exception with the given result.
     *
     * @param array $result The result from the API server
     */
    public function __construct($result)
    {
        $this->_result = $result;
        parent::__construct($result['message'], $result['code']);
    }

    /**
     * Return the associated result object returned by the API server.
     *
     * @param void
     * @return array The result from the API server
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Returns the associated type for the error. This will default to
     * 'Exception' when a type is not available.
     *
     * @param void
     * @return string
     */
    public function getType()
    {
        return isset($this->_result['type'])
            ? $this->_result['type']
            : 'Exception';
    }

    /**
     * To make debugging easier.
     *
     * @param void
     * @return string The string representation of the error
     */
    public function __toString()
    {
        $str = $this->getType() . ': ';
        if ($this->code != 0) {
            $str .= $this->code . ': ';
        }
        return $str . $this->message;
    }
}