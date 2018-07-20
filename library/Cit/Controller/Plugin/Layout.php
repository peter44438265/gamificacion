<?php

class Cit_Controller_Plugin_Layout extends Zend_Controller_plugin_Abstract{

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        //echo "entro";die;
        $resourceLayout = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('layout');
        $viewLayout = $resourceLayout->getView();
        $session = "cartSession";
        $viewLayout->cart = Store_Cart_Factory::createInstance($session);
        Zend_Registry::set('cart', $viewLayout->cart);
        
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();

        #Establece los elementos del layout por default.
        $view->addScriptPath(APPLICATION_PATH . '/layouts/scripts/');
        $view->addScriptPath(APPLICATION_PATH . '/layouts/scripts/elements/');
        #Establece los elementos del layout por módulo. Esto sirve en caso de que se quiera especializar el header y footer.
        $view->addScriptPath(APPLICATION_PATH . '/layouts/scripts/' . $request->getModuleName() . '/elements/');
        #partial
        $view->addScriptPath(APPLICATION_PATH . '/views/scripts');
        //$view->addScriptPath(APPLICATION_PATH . '/views/scripts/partial');
        $view->addScriptPath(APPLICATION_PATH . '/views/scripts/perfiles');
        
        //$view->headTitle()->prepend('valeplaza');
        //echo $view->headTitle();
        $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $url = $this->getRequest()->getParam('url', "");
        $view->urlRedirect=$url;
        $moduleName = $request->getModuleName();
        $actionName = $request->getActionName();
        $controllerName = $request->getControllerName();
        //echo $moduleName."-".$actionName."-".$controllerName;
        //echo $uri;die;
        if ($moduleName == Obj_SysRecursos::MODULE_CPN && $actionName != 'error') {
                
                /*$detect= new Cit_Mobile();
                $view->mobile=false;
                if ( $detect->isMobile() ) {
                    $view->mobile=true;
                    header( 'Location: http://prem.valeplaza.com'.$uri);die;
                }*/

                if(!empty($this->_sessId)){
                    $view->sessId = $this->_sessId;
                }else{
                    $this->_sessId = Zend_Session::getId();
                    $view->sessId = $this->_sessId;
                }
            
        }/*else if( $moduleName == Obj_SysRecursos::MODULE_TAX && $controllerName == Obj_SysRecursos::CONTROLLER_EMP){
                $layout->setLayout('main-empresa');
        }*/
    }

}