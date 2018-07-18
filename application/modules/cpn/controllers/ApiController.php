<?php

class Cpn_ApiController extends Zend_Controller_Action
{

    private $_server;
    private $_params;

    public function preDispatch()
    {
        parent::preDispatch();
    }

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_server = new Cit_Service_Rest();
        $this->_server->setFormat('json');
        $this->_server->setClass('Model_CpnApiVp');
        $this->_params=$this->getRequest()->getParams();
        unset($this->_params['controller']);
        unset($this->_params['action']);
        unset($this->_params['module']);
        
    }

    public function filterParams($params)
    {
        
    }
    public function registroUsuarioAction()
    {
        $request = array('method' => 'registroUsuario');
        $request=$this->_request($request);
        $this->_server->handle($request);
    }
    
    
    private function _request($request){
        unset($request["_"]);
        $paramKeys = array_keys($this->_params);
        $filterParams = $this->_params;
        if (!empty($paramKeys)) {
            foreach ($paramKeys AS $key) {
                $request[$key] = $filterParams[$key];
                /*if (!$request[$key]) {
                    echo $key;die;
                    throw new Exception($request[$key] . ' contained invalid data.');
                }*/
            }
        }
        return $request;
        
    }
    
}