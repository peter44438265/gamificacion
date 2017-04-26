<?php

class Cit_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    private $_resources = array(
        'default',
        'auth',
        'academico',
        'pensiones',
        'resource',
        'seguridad',
    );

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        /*$acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role(Zoe_Model_Db_Usuario::ROL_VISITANTE));
        $acl->addRole(new Zend_Acl_Role(Zoe_Model_Db_Usuario::ROL_ADMINISTRADOR));
        foreach ($this->_resources as $resource) {
            $acl->addResource(new Zend_Acl_Resource($resource));
        }
        $acl->allow(Zoe_Model_Db_Usuario::ROL_VISITANTE, 'auth');
        $acl->allow(Zoe_Model_Db_Usuario::ROL_VISITANTE, 'default');

        $role = Zoe_Model_Db_Usuario::ROL_VISITANTE;
        $auth = Zend_Auth::getInstance();
        $user = '';
        if ($auth->hasIdentity()) {
            $user = $auth->getStorage()->read();
            $role = Zoe_Model_Db_Usuario::ROL_ADMINISTRADOR;
        }
        else{
            $this->getResponse()->setRedirect(Zend_Registry::get('config')->citid->url);
        }
        if (!$acl->isAllowed($role, $request->getModuleName(), $request->getActionName())) {
            if ($role == Zoe_Model_Db_Usuario::ROL_VISITANTE) {
                $request->setModuleName('auth');
                $request->setControllerName('index');
                $request->setActionName('index');
            } elseif($role == Zoe_Model_Db_Usuario::ROL_ADMINISTRADOR) {
                
            }else{
                $request->setModuleName('auth');
                $request->setControllerName('index');
                $request->setActionName('sinacceso');
            }
        }*/
    }

}