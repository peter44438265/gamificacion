<?php

class Cit_Session_Validator_RemoteAddress
        extends Zend_Session_Validator_Abstract
{

    public function setup()
    {
        $this->setValidData((isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : null);
    }

    function validate()
    {
        $currentBrowser = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : null;
        return $currentBrowser === $this->getValidData();
    }

}

//class ZExtraLib_Session_Validator_RemoteAddress extends Zend_Session_Validator_Abstract