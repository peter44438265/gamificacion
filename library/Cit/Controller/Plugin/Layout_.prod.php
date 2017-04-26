<?php

class Cit_Controller_Plugin_Layout extends Zend_Controller_plugin_Abstract{

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
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
        //$view->titulo=$view->headTitle('Valeplaza')->setSeparator(' - ');
        
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
                $ModelMessages= new Model_CpnMensajes();
                $ModelAmistad= new Model_CpnAmistad();
                $modelUsuario= new Model_CpnUsuario();
                $modelTienda= new Model_CpnTiendas();
                $modelCliente= new Model_CpnCliente();
                $modelClienteTrofeos= new Model_CpnClienteTrofeos();
                $modelTiendaTrofeos= new Model_CpnTiendaTrofeos();
                $modelPais = new Model_SysPaises();
                $modelNotificacion=new Model_CpnNotificacion();
            /*$detect= new Cit_Mobile();
            $view->mobile=false;
                if ( $detect->isMobile() ) {
                    $view->mobile=true;
                    header( 'Location: http://m.valeplaza.com'.$uri);die;
                }*/
                if(!empty($this->_sessId)){
                    $view->sessId = $this->_sessId;
                }else{
                    $this->_sessId = Zend_Session::getId();
                    $view->sessId = $this->_sessId;
                }
            if (Zend_Registry::get('Susuario')->status) {
                //echo $view->titulo;
                //$layout->setLayout('main-admin');
                //$resursosModel = new Model_OuthRecurso();
                if(Zend_Registry::get('Susuario')->rolID == Obj_SysRol::ROL_USUARIO){
                    $data = $modelCliente->getIdByUsu(Zend_Registry::get('Susuario')->usID);
                    $numero = $data['cliente_puntos'];
                    
                    /**/
                    /*$listRanking = $ModelAmistad->getRankingbyID(Zend_Registry::get('Susuario')->usID);
                    
                    $view->listRanking = $listRanking;
                    */
                    $list_trofeos= $modelClienteTrofeos->getTrofeosByCliente(Zend_Registry::get('Susuario')->cliente_id);
                    //$list_trofeos_ids= $modelClienteTrofeos->getTrofeosByClienteIDs(Zend_Registry::get('Susuario')->cliente_id);
                    
                    $ids=null;
                    
                    if(!empty($list_trofeos)){
                        $arrIDs=array();
                        $i=0;
                        foreach ($list_trofeos as $key => $value) {
                            $arrIDs[$i]=$value['trofeo_id'];
                            $i++;
                        }
                        $ids = implode(',', $arrIDs);

                    }
                        $next_trofeos = $modelClienteTrofeos->getTwoNextTrofeos($ids);
                        $view->next_trofeos=$next_trofeos;
                        $view->mis_trofeos= $list_trofeos;
                    /**/
                }else{
                    $params = $this->getRequest()->getParams();
                    $data = $modelTienda->getIdByUsu(Zend_Registry::get('Susuario')->usID,1);
                    if($data['usuario_estado'] !='1'){
                    if(!empty($params['referer']))$view->referer=$params['referer']; else $view->referer=null;
                    #hay sesion de una tienda
                    
                    $numero = $data['tienda_puntos'];
                    
                    $list_trofeos= $modelTiendaTrofeos->getTrofeosByTienda(Zend_Registry::get('Susuario')->tienda_id);
                    //$list_trofeos_ids= $modelClienteTrofeos->getTrofeosByClienteIDs(Zend_Registry::get('Susuario')->cliente_id);
                    $ids=null;
                    
                    if(!empty($list_trofeos)){
                        $arrIDs=array();
                        $i=0;
                        foreach ($list_trofeos as $key => $value) {
                            $arrIDs[$i]=$value['trotie_id'];
                            $i++;
                        }
                        $ids = implode(',', $arrIDs);

                    }
                        $next_trofeos = $modelTiendaTrofeos->getTwoNextTrofeos($ids);
                        $view->next_trofeos=$next_trofeos;
                        $view->mis_trofeos= $list_trofeos;
                    }else{
                        Zend_Session::namespaceUnset('Susuario');
                        header( 'Location: '.Cit_Server::getContent()->host);die;
                    }
                }
                    
                    $longitud = strlen($numero);
                    $dif = 6 - $longitud;

                    switch ($dif) {
                        case 0:$numero = $numero;
                            break;
                        case 1:$numero = '0' . $numero;
                            break;
                        case 2:$numero = '00' . $numero;
                            break;
                        case 3:$numero = '000' . $numero;
                            break;
                        case 4:$numero = '0000' . $numero;
                            break;
                        case 5:$numero = '00000' . $numero;
                            break;
                        case 6:$numero= '000000'; break;
                    }

                    $view->cm = $numero[0];
                    $view->dm = $numero[1];
                    $view->um = $numero[2];
                    $view->c = $numero[3];
                    $view->d = $numero[4];
                    $view->u = $numero[5];
                $puntos=$modelUsuario->getPuntajeByID(Zend_Registry::get('Susuario')->usID);
                //$newMessages= $ModelMessages->getNewMessages(Zend_Registry::get('Susuario')->usID);
                $messages = $ModelMessages->getHiloNoLeido(Zend_Registry::get('Susuario')->usID);
                $solicitudes= $ModelAmistad->getSolicitudes(Zend_Registry::get('Susuario')->usID);
                $notificaciones= $modelNotificacion->getNotificaciones(Zend_Registry::get('Susuario')->usID);
                //$recursos = $resursosModel->getByRol(Zend_Registry::get('Susuario')->usuario->rol_id);
                //$modelRecurso = new Obj_OuthRecurso($recursos);
                //$data = $modelRecurso->getRecursos();
                //$recurso = $modelRecurso->getRecurso($request->getModuleName() . ':'
                                //. $request->getControllerName() . ':'
                                //. $request->getActionName());
                $view->pageActual = '';
                $view->puntos=$puntos;
                /*if (!empty($data)) {
                    $view->pageActual = $recurso;
                }*/
                $htmlMessages="";
                $view->Messages=$htmlMessages;
                $view->newMessages=  count($messages);
                $htmlNotificaciones="";
                $i=0;
                if(!empty($notificaciones)){
                    //var_dump($notificaciones);die;
                    
                    foreach ($notificaciones as $key => $value) {
                        //echo $value['notificacion_datos'];die;
                        if($value['notificacion_estado']==0) $i++;
                        $data=  json_decode($value['notificacion_datos']);
                        //var_dump($data);die;
                        //$data=  json_decode($data);
                        //echo $data->cliente;die;
                        if(!empty($data->tienda)) $tiendaNombre=$data->tienda; else $tiendaNombre="";
                        if(!empty($data->cliente)) $clienteNombre=$data->cliente; else $clienteNombre="";
                        if(!empty($data->producto)) $productoNombre=$data->producto; else $productoNombre="";
                        $buscar = array("#@@t#","#@@c#","#@@p#");
                        $cambiar = array($tiendaNombre,"<strong>".$clienteNombre."</strong>",$productoNombre);
                        $mensaje=str_replace($buscar, $cambiar, $value['notbase_descripcion']);
                        
                        if(!empty($data->img)){
                            if($value['notificacion_tipo']==0){
                                $logo=Cit_Server::getStatic()->host."imagenes/clientes/".$data->idImg."/logo/".$data->img;
                            }else{
                                $logo=Cit_Server::getStatic()->host."imagenes/empresas/".$data->idImg."/logo/".$data->img;
                            }
                        }else{
                            $logo=Cit_Server::getContent()->host."img/avatar-50x50.png";
                        }
                        if(!empty($data->url)) $url=$data->url; else $url="";
                                $htmlNotificaciones.="<div class='noti-hd' id='' onclick=\" document.location.href='".$url."'\">
                                                        
                                                            <div class='col1'>
                                                                <img src='".$logo."' alt='Logo' title='Logo' height='45' width='45'/>
                                                            </div>
                                                            <div class='col2'>
                                                                <div class='aviso'>".$mensaje."</div>
                                                                <div class='tiempo'>hace 10 min</div>
                                                            </div>
                                                      
                                                       </div>";
                    }
                }
                $view->newNotificaciones=$i;
                $view->notificaciones=$htmlNotificaciones;
                
                
                    
                
            }else{
                    $view->code = $this->getRequest()->getParam('code', null);
                    $referer=$this->getRequest()->getParam('referer', null);
                    $geolocalizar=FALSE;
                    if($controllerName=='index' && $actionName=='index' && empty($referer)){ $geolocalizar=TRUE; }
                    $view->geolocalizar=$geolocalizar;
                    $email=$this->getRequest()->getCookie('vp_email');
                    $pass=$this->getRequest()->getCookie('vp_pass');
                    $rec=$this->getRequest()->getCookie('vp_recorder');
                    if(!empty($email) && !empty($pass) && !empty($rec)){
                        $modelUsuario->autenticar($email, $pass,0);
                       // $this->vp_recordar=true;
                    }else{
                        //$this->vp_recordar=false;
                        $view->paises= $modelPais->getAll();
                    }
                    if(!empty($rec)){
                        $view->vp_recordar=true;
                    }else{
                        $view->vp_recordar=false;
                    }
            }
        }else if( $moduleName == Obj_SysRecursos::MODULE_TAX && $controllerName == Obj_SysRecursos::CONTROLLER_EMP){
                $layout->setLayout('main-empresa');
        }
    }

}