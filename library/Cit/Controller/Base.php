<?php

class Cit_Controller_Base extends Zend_Controller_Action {

    public $categoriaId = '';

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        $this->categoriaId = $request->getParam('categoriaId', null);
         $this->subCategoriaId = $request->getParam('subCategoriaId', null);
        parent::__construct($request, $response, $invokeArgs);
    }

}

