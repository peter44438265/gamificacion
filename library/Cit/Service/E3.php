<?php

class Cit_Service_E3 {

    private $_awsServer;
    private $_bucket;

    public function __construct() {
        $this->_bucket = Zend_Registry::get('config')->aws->bucket;
        try {
            $this->_awsServer = new Zend_Service_Amazon_S3(Zend_Registry::get('config')->aws->key, Zend_Registry::get('config')->aws->secretkey);
            if (!$this->_awsServer->isBucketAvailable($this->_bucket)) {
                $this->_awsServer->createBucket($this->_bucket);
            }
        } catch (Exception $e) {
            //echo $e;die;
        }
    }

    /**
     * 
     * @param type $pathOrigen
     * @param type $patchDestino
     * @return type
     */
    public function putFile($pathOrigen, $patchDestino ) {
        //echo $this->_bucket;die;
        $responce = array();
        if (!empty($pathOrigen)) {
            try {
                $responce = $this->_awsServer->putObject("$this->_bucket/$patchDestino"
                        , file_get_contents($pathOrigen)
                        , array(Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ));
            } catch (Exception $e) {
                var_dump($e->getMessage(), $e->getTraceAsString());
            }
        }
        return $responce;
    }

    public function deleteFile($patchDestino) {
        $responce = array();
        try {
            $responce = $this->_awsServer->removeObject("$this->_bucket/$patchDestino");
        } catch (Exception $e) {
            var_dump($e->getMessage(), $e->getTraceAsString());
        }
        return $responce;
    }

}
