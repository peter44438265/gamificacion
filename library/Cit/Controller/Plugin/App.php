<?php
class Cit_Controller_Plugin_App extends Zend_Controller_Plugin_Abstract
{            
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if(Cit_Util::detect_ie6() || Cit_Util::detect_ie7()){
            $request->setModuleName('default');
            $request->setControllerName('index');
            $request->setActionName('ie');
        }
        $resourceLayout = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('layout');
        $view = $resourceLayout->getView();
        $session = "cartSession";
        $view->cart = Store_Cart_Factory::createInstance($session);
        Zend_Registry::set('cart', $view->cart);
    }
}