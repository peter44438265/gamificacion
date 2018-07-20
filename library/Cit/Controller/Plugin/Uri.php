<?php

class Cit_Controller_Plugin_Uri extends Zend_Controller_plugin_Abstract {

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        /*$ofertas = new Zend_Controller_Router_Route(
                        'ofertas/:ciudad',
                        array(
                            'module' => 'default',
                            'controller' => 'index',
                            'action' => 'index',
                        )
        );
        $router->addRoute('ofertas', $ofertas);
        $ciudad = new Zend_Controller_Router_Route(
                        'ofertas/:ciudad/:categoria/*',
                        array(
                            'module' => 'default',
                            'controller' => 'index',
                            'action' => 'index',
                        )
        );
        $router->addRoute('ciudad', $ciudad);
        $producto = new Zend_Controller_Router_Route(
                        'producto/:ciudad/:categoria/*',
                        array(
                            'module' => 'default',
                            'controller' => 'producto',
                            'action' => 'index',
                        )
        );
        $router->addRoute('producto', $producto);

        $compras = new Zend_Controller_Router_Route(
                        'compras/:ciudad/:categoria/*',
                        array(
                            'module' => 'default',
                            'controller' => 'compras',
                            'action' => 'index',
                        )
        );
        $router->addRoute('compras', $compras);*/
        
        
        /*$empresa = new Zend_Controller_Router_Route(
                        'empresa/:controller/:action',
                        array(
                            'module' => 'default',
                            'controller' => 'empresa',
                        )
        );
        $router->addRoute('empresa', $empresa);

        $defaultRoute = new Zend_Controller_Router_Route(
                        'module/:controller/:action/*',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'index'
                        )
        );
        $router->addRoute('defaultRoute', $defaultRoute);*/
    }

}