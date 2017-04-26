<?php
class Cit_Plugin_Server
{
    const DB_QUERY = 1;
    const DB_AUTH = 2;
    const DB_PROCESS = 3;
    const DB_SYSTEM = 4;
    protected $_content;
    protected $_static;
    protected $_file;
    protected $_db;
    public function __construct ($content, $static, $file, $db)
    {
        $this->setContent($content);
        $this->setStatic($static);
        $this->setFile($file);
        $this->setDb(new Cit_Plugin_DatabaseSecure($db));
    }
    /**
     * @return the $_db
     */
    public function getDb ()
    {
        return $this->_db;
    }
    /**
     * @param field_type $_db
     */
    public function setDb ($_db)
    {
        $this->_db = $_db;
    }
    /**
     * @return the $_content
     */
    public function getContent ()
    {
        return $this->_content;
    }
    /**
     * @return the $_static
     */
    public function getStatic ()
    {
        return $this->_static;
    }
    /**
     * @return the $_file
     */
    public function getFile ()
    {
        return $this->_file;
    }
    /**
     * @param field_type $_content
     */
    public function setContent ($_content)
    {
        $this->_content = $_content;
    }
    /**
     * @param field_type $_static
     */
    public function setStatic ($_static)
    {
        $this->_static = $_static;
    }
    /**
     * @param field_type $_file
     */
    public function setFile ($_file)
    {
        $this->_file = $_file;
    }
}