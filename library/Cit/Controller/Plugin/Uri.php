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
        
        $buscar = new Zend_Controller_Router_Route(
                        'buscar/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'buscar',
                            'action' => 'index',
                        )
        );
        $router->addRoute('buscar', $buscar);
        $outh = new Zend_Controller_Router_Route(
                        'unetea/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'outh',
                            'action' => 'index',
                        )
        );
        $router->addRoute('outh', $outh);
        
        $unete = new Zend_Controller_Router_Route(
                        'unete/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'outh',
                            'action' => 'index',
                        )
        );
        $router->addRoute('outh2', $unete);
        
        
        
        $login = new Zend_Controller_Router_Route(
                        'login/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'outh',
                            'action' => 'login',
                        )
        );
        $router->addRoute('outh3', $login);
        $vpempresas = new Zend_Controller_Router_Route(
                        'empresas/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'outh',
                            'action' => 'tiendas',
                        )
        );
        $router->addRoute('outh4', $vpempresas);
        
        $vpempresasbasico = new Zend_Controller_Router_Route(
                        'planes/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'outh',
                            'action' => 'tiendasbasico',
                        )
        );
        $router->addRoute('vpbasico', $vpempresasbasico);
        
        $vpempresascorporativo = new Zend_Controller_Router_Route(
                        'corporativo/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'outh',
                            'action' => 'tiendascorporativo',
                        )
        );
        $router->addRoute('vpcorporativo', $vpempresascorporativo);
        
        $outhp = new Zend_Controller_Router_Route(
                        'perfil/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'index',
                        )
        );
        $router->addRoute('perfil', $outhp);
        $isidro = new Zend_Controller_Router_Route(
                        'cfsanisidro/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'isidro',
                        )
        );
        $router->addRoute('isidro', $isidro);
        
        $comentarios = new Zend_Controller_Router_Route(
                        'feedback/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'comentarios',
                            'action' => 'index',
                        )
        );
        $router->addRoute('comentarios', $comentarios);
        /*$outhp2 = new Zend_Controller_Router_Route(
                        'dirigir/:url/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'index',
                        )
        );
        $router->addRoute('perfil2', $outhp2);*/
        /*$tiendacontent = new Zend_Controller_Router_Route(
                        'tiendas/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'tiendacontent',
                        )
        );
        $router->addRoute('tiendacontent', $tiendacontent);*/
        
        $conocenos = new Zend_Controller_Router_Route(
                        'perfil/:referer/conocenos',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'conocenos',
                        )
        );
        $router->addRoute('conocenos', $conocenos);
        $tiendasvirtuales = new Zend_Controller_Router_Route(
                        'perfil/:referer/tiendasvirtuales',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'tiendasvirtuales',
                        )
        );
        $router->addRoute('tiendasvirtuales', $tiendasvirtuales);
        
        $valesv = new Zend_Controller_Router_Route(
                        'perfil/:referer/vales',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'vales',
                        )
        );
        $router->addRoute('valesv', $valesv);
        
        $privacidad = new Zend_Controller_Router_Route(
                        'privacidad',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'privacidad',
                        )
        );
        $router->addRoute('privacidad', $privacidad);
        
        $terminos = new Zend_Controller_Router_Route(
                        'condiciones',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'condiciones',
                        )
        );
        $router->addRoute('terminos', $terminos);
        
        $soporteEmp = new Zend_Controller_Router_Route(
                        'soporte-empresa',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'soporte-empresa',
                        )
        );
        $router->addRoute('soporte-empresa', $soporteEmp);
        
        $soporteCli = new Zend_Controller_Router_Route(
                        'soporte-cliente',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'soporte-cliente',
                        )
        );
        $router->addRoute('soporte-cliente', $soporteCli);
        
        $recuperarPass = new Zend_Controller_Router_Route(
                        'recuperar-password',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'recuperar-password',
                        )
        );
        $router->addRoute('recuperar-password', $recuperarPass);

        $valcli = new Zend_Controller_Router_Route(
                        'valida/:codigo/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'outh',
                            'action' => 'confirmacion',
                        )
        );
        $router->addRoute('valida', $valcli);
        
        $valtie = new Zend_Controller_Router_Route(
                        'confirma/:codigo/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'tienda',
                            'action' => 'confirmacion',
                        )
        );
        $router->addRoute('confirma', $valtie);
        
        $espera = new Zend_Controller_Router_Route(
                        'espera',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'espera',
                        )
        );
        $router->addRoute('espera', $espera);
        
        $esperaconfirmbusiness = new Zend_Controller_Router_Route(
                        'esperaconfirmacionbusiness',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'esperaconfirmacionbusiness',
                        )
        );
        $router->addRoute('esperaconfirmbusiness', $esperaconfirmbusiness);
        
        $esperaconfirm = new Zend_Controller_Router_Route(
                        'esperaconfirmacion',
                        array(
                            'module' => 'cpn',
                            'controller' => 'index',
                            'action' => 'esperaconfirmacion',
                        )
        );
        $router->addRoute('esperaconfirm', $esperaconfirm);
		
        $viewvale = new Zend_Controller_Router_Route(
                'vale-view/:referer/',
                array(
                    'module' => 'cpn',
                    'controller' => 'producto',
                    'action' => 'index',
                )
        );
        $router->addRoute('valeview', $viewvale);
        
		
        $viewproducto = new Zend_Controller_Router_Route(
                'producto-view/:referer/',
                array(
                    'module' => 'cpn',
                    'controller' => 'producto',
                    'action' => 'producto',
                )
        );
        $router->addRoute('valeproducto', $viewproducto);
        
        $viewproductotest = new Zend_Controller_Router_Route(
                'productotest-view/:referer/',
                array(
                    'module' => 'cpn',
                    'controller' => 'map',
                    'action' => 'producto',
                )
        );
        $router->addRoute('viewproductotest', $viewproductotest);
        
        $viewdireccion = new Zend_Controller_Router_Route(
                'direccion-view/:referer/',
                array(
                    'module' => 'cpn',
                    'controller' => 'direccion',
                    'action' => 'index',
                )
        );
        $router->addRoute('direccionview', $viewdireccion);
        
        $pedidos = new Zend_Controller_Router_Route(
                'mispedidos/',
                array(
                    'module' => 'cpn',
                    'controller' => 'pedidos',
                    'action' => 'index',
                )
        );
        $router->addRoute('pedidos', $pedidos);
        
        $peticiontie = new Zend_Controller_Router_Route(
                        'peticion-view/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'peticiontie',
                            'action' => 'index',
                        )
        );
        $router->addRoute('peticiontie', $peticiontie);
        $peticioncli = new Zend_Controller_Router_Route(
                        'responce-view/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'peticioncli',
                            'action' => 'index',
                        )
        );
        $router->addRoute('peticioncli', $peticioncli);
        
        $miagente = new Zend_Controller_Router_Route(
                        'mi-agente/:referer/',
                        array(
                            'module' => 'cpn',
                            'controller' => 'agentes',
                            'action' => 'miagente',
                        )
        );
        $router->addRoute('miagente', $miagente);
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