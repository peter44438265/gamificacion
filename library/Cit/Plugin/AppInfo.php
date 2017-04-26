<?php
class Cit_Plugin_AppInfo
{
    protected $_name;
    protected $_author;
    protected $_version;
    protected $_date;
    public function __construct ($name, $author, $version, $date)
    {
        $this->setName($name);
        $this->setAuthor($author);
        $this->setVersion($version);
        $this->setDate($date);
    }
    public function setName ($name)
    {
        $this->_name = trim($name);
        return $this;
    }
    public function getName ()
    {
        return $this->_name;
    }
    public function setAuthor ($author)
    {
        $this->_author = trim($author);
        return $this;
    }
    public function getAuthor ()
    {
        return $this->_author;
    }
    public function setVersion ($version)
    {
        $this->_version = trim($version);
        return $this;
    }
    public function getVersion ()
    {
        return $this->_version;
    }
    public function getDate ()
    {
        return $this->_date;
    }
    public function setDate ($date)
    {
        $this->_date = $date;
    }
}