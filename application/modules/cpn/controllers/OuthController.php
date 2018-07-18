<?php

/**
 * @author www.likerow.com(likerow@gmail.com)
 */
class Cpn_OuthController extends Cit_Controller_Base {

    private $_ModelUsuario = null;
    private $_ModelCliente = null;
    private $_ModelLogNivCli = null;
    private $_status = null;
    private $_rol=null;

    public function init() {
        $this->_ModelUsuario = new Model_CpnUsuario();
        $this->_ModelUsuarioInvitados = new Model_CpnUsuarioInvitados();
        $this->_ModelCliente = new Model_CpnCliente();
        $this->_ModelLogNivCli = new Model_CpnLogNivelCliente();
        $this->_status = Zend_Registry::get('Susuario')->status;
        $this->_ModelPais = new Model_SysPaises();
        $this->_ModelTienda= new Model_CpnTiendas();
        $this->_ModelOfertas= new Model_CpnOferta();
        $this->_ModelTrabajadorInvitado= new Model_CpnTrabajadoresInvitados();
        $this->_ModelTiendaTrabajadores= new Model_CpnTiendaTrabajadores();
        $this->_ModelConfiguracionCorporativo= new Model_CpnConfiguracionCorporativo();
        $this->_rol= Zend_Registry::get('Susuario')->rolID;
        #0:intriga,1:produccion
        $this->_fase = 0;
    }

    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            #proceso de registro de un cliente
            $params = $this->getRequest()->getParams();
            $this->view->data = $params;
            $mensajes = $this->_ModelUsuario->_validaFormRegistro($params);
            if (empty($mensajes)) {
                #si no hay mensajes de error al validar el formulario
                //$nick = preg_replace('[\s+]', '', trim($params['nombres'])) . '.' . preg_replace('[\s+]', '', trim($params['apellidos']));
                $nick = $this->_ModelCliente->obtenerNick(trim($params['nombres']) . ' ' . trim($params['apellidos']));
                $refererID = Obj_CpnCliente::CLIENTE_DEFAULT_REFERER;
                if (!empty($params['referer'])) {
                    $usuario = $this->_ModelUsuario->getByAlias($params['referer']);
                    $refererID = $usuario['datos']['usuario_id'];
                }
                #guardando el usuario y cliente
                $codigo = $this->_ModelUsuario->obtenerCodigo();
                $responce = $this->_ModelUsuario->guardarUsuario(array(
                    'cliente_nombre' => trim($params['nombres']) . ' ' . trim($params['apellidos']),
                    'cliente_nick' => $nick,
                    'cliente_nacimiento' => $params['cli_anio'] . '-' . $params['cli_mes'] . '-' . $params['cli_dia'],
                    'usuario_email' => trim($params['email']),
                    'cliente_id_referer' => $refererID,
                    'usuario_codigo' => $codigo,
                    //'usuario_password' => md5(trim($params['password'])),
                    'rol_id' => Obj_SysRol::ROL_USUARIO));
                $responceLogin = false;
                if ($responce['status']) {
                    #si el usuario se guardo correctamente, logueamos
                    $responceLogin = $this->_ModelUsuario->autenticar(trim($params['email']), trim($params['password']), $this->_fase);
                    $mensaje = array();
                    if ($responceLogin) {
                        #si loguea correctamente, redirigimos
                        if (Zend_Registry::get('Susuario')->rolID == Obj_SysRol::ROL_MODERADOR_TIENDA) {
                            $this->_redirect('/canjeo');
                        } else {
                            $this->_redirect('/');
                        }
                    } else {
                        echo "Gracias por registrarse, se ha unido al futuro de las ofertas online";
                        /* envio de correo */
                    }
                    $this->view->mensajes = $mensaje;
                } else {
                    echo 'Problemas con el servidor por favor vuelva a intentarlo en unos momentos.';
                }
            } else {
                echo "error al registrar";
            }
            sleep(3);
        } elseif ($this->getRequest()->isGet()) {
            #obtener el id del referido
            if ($this->_status) {
                $this->_redirect(Cit_Server::getContent()->host);
            } else {

                $alias = $this->getRequest()->getParam('referer', null);
                $email = $this->getRequest()->getParam('email', null);
                $this->view->email=$email;
                if (!empty($alias)) {
                    $paises = $this->_ModelPais->getAll();
                    $this->view->paises = $paises;
                    $usuario = $this->_ModelUsuario->getByAlias($alias);
                    //var_dump($usuario);die;
                    if (!empty($usuario['datos'])) {
                        $this->view->referer = $alias;
                        if (!empty($usuario['datos']['logo'])) {
                            if ($usuario['tipo'] == 'cliente') {
                                $this->view->headTitle($usuario['datos']['nombre'] . ' te invita a unirte a valeplaza');
                                $this->view->urlLogo = Cit_Server::getStatic()->host . 'imagenes/clientes/' . $usuario['datos']['cliente_id'] . '/logo/' . $usuario['datos']['logo'];
                            } elseif ($usuario['tipo'] == 'tienda') {
                                $this->view->urlLogo = Cit_Server::getStatic()->host . 'imagenes/empresas/' . $usuario['datos']['tienda_id'] . '/logo/' . $usuario['datos']['logo'];
                            }
                        } else {
                            $this->view->urlLogo = Cit_Server::getContent()->host . 'img/avatar-50x50.png';
                        }
                        //$this->view->logo=$usuario['datos']['logo'];
                        $this->view->nombre = mb_convert_case($usuario['datos']['nombre'], MB_CASE_TITLE, "utf8");
                        //$this->view->titulo=$this->view->headTitle($usuario['datos']['nombre'].' te invita a unirte a valeplaza');
                    } else {
                        $this->_redirect(Cit_Server::getContent()->host);
                    }
                } else {
                    $fn = $this->getRequest()->getParam('fn', null);
                    $ln = $this->getRequest()->getParam('ln', null);
                    $email = $this->getRequest()->getParam('e', null);
                    $this->view->fn = $fn;
                    $this->view->ln = $ln;
                    $this->view->email = $email;
                }
            }
        }
    }

    public function logoutAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        #proceso para desloguear
        $patch = $this->getRequest()->getParam('patch', null);
        if (empty($patch)) {
            $patch = '/';
        }
        if($this->_rol == Obj_SysRol::ROL_USUARIO_CORPORATIVO || $this->_rol == Obj_SysRol::ROL_ADMIN_TIENDA_CORPORATIVO){
            //echo Zend_Registry::get('Susuario')->mitiendacorporativanick;die;
            $patch.="/".Zend_Registry::get('Susuario')->mitiendacorporativanick;
        }
        setcookie('vp_email', '', time() + 3600, '/');
        setcookie('vp_pass', '', time() + 3600, '/');
        $cart = Zend_Registry::get('cart');
        $cart->removeAll();
        Zend_Session::namespaceUnset('Susuario');
        Zend_Session::namespaceUnset('cart');
        $this->_redirect($patch);
    }

    public function loginAction() {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
        if ($this->getRequest()->isPost()) {
            
            #proceso para loguear
            $params = $this->getRequest()->getParams();
            $this->view->data = $params;
            $mensajes = $this->_ModelUsuario->_validaForm($params);
            $nick='';
            if (empty($mensajes)) {
                #si no hay mensajes de error al validar el formulario
                $password = md5(trim($params['password']));
                $email = trim($params['email']);

                $responce = $this->_ModelUsuario->autenticar(trim($email), trim($password), $this->_fase);
                if ($responce == true) {
                    if (!empty($params['rec'])) {
                        setcookie('vp_email', $email, time() + 3600, '/');
                        setcookie('vp_pass', $password, time() + 3600, '/');
                        setcookie('vp_recorder', '1', time() + 3600, '/');
                    } else {
                        setcookie('vp_email', '', time() + 3600, '/');
                        setcookie('vp_pass', '', time() + 3600, '/');
                        setcookie('vp_recorder', '0', time() + 3600, '/');
                    }
                    $dat = array();
                    $dat['usuario_cookie'] = $params['rec'];
                    $where = array('usuario_id = ?' => Zend_Registry::get('Susuario')->usID);
                    $this->_ModelUsuario->update($dat, $where);
                    if(!empty(Zend_Registry::get('Susuario')->nick))$nick=Zend_Registry::get('Susuario')->nick; else $nick="";
                    if(!empty($params['offset'])) Zend_Registry::get('Susuario')->offsetdate=$params['offset'];
                    #si existe el usario logueo y redirecciono a su perfil
                    //$this->_redirect('/perfil/' . Zend_Registry::get('Susuario')->nick);
                    //$this->_redirect('/');
                    
                } else {
                    $mensajes['mail'] = 'Su cuenta ha sido baneada';
                    //$nick='';
                    //echo json_encode(array('mensajes'=>$mensajes,'nick'=>$nick));
                }
                echo json_encode(array('mensajes'=>$mensajes,'nick'=>$nick));
            } else {
                echo json_encode(array('mensajes'=>$mensajes,'nick'=>$nick));
            }
            //$this->view->mensajes = $mensajes;
            sleep(3);
        }
    }

    public function confirmacionAction() {
        
        #### PROCESO DE CONFIRMACION DE REGISTRO PARA EL CLIENTE
        #recojo los parametros enviados
        $params = $this->getRequest()->getParams();
        
        #verifico que el parametro codigo existe
        if(!empty($params['codigo'])){
            #verifico si el cliente se encuentra en estado de espera de confirmacion;
            $verify = $this->_ModelUsuario->verifyUserEspera($params['codigo']);
            
            if ($verify['responce']) {
                $data = $this->_ModelUsuario->getDataByID($verify['id']);
                if ($this->getRequest()->isGet()) {
                    #si el metodo de envio es GET: se muestra solamente la vista necesaria
                    if ($this->_status) {
                        #si existe session abierta se redirige a la raiz
                        $this->_redirect('/');
                    } else {
                            #paso un parmetro a la vista con el valor de codigo
                            $this->view->codigo = $params['codigo'];
                            
                            #obtengo los datos del usuario por su usuario_id(id)
                            
                            $this->view->nombre = mb_convert_case($data['datos']['nombre'], MB_CASE_TITLE, "utf8");
                            if($data['datos']['rol_id'] == Obj_SysRol::ROL_USUARIO_CORPORATIVO){
                                $render = 'confirmacionclientecorporativo';
                                $idTienda=$this->_ModelTiendaTrabajadores->getIdCorporativobyUserID($verify['id']);
                                //echo $idTienda;die;
                                if(!empty($idTienda)){
                                    $datConfiguracion= $this->_ModelConfiguracionCorporativo->getConfiguracionByID($idTienda);
                                    $this->view->dataConfiguracion=$datConfiguracion;
                                    $this->view->headLink(array('rel' => 'shortcut icon', 'href' => Cit_Server::getStatic()->host."imagenes/corporativos/" . $idTienda . "/icono/" . $datConfiguracion['corpconfi_browser_icono'], 'type' => 'image/x-icon'), 'PREPEND');
                                }
                                
                                $this->render($render);
                            }
                    
                    }
                    
                } else if ($this->getRequest()->isPost()) {
                    #si el metodo de envio es POST: se hace el proceso logico de confirmacion del cliente
                    
                    #deshabilito el layout y la vista
                    $this->_helper->layout->disableLayout();
                    $this->_helper->viewRenderer->setNoRender();
                        if(!empty($params['pass'])){
                            $responce = $this->_ModelUsuario->confirmar($params['codigo'], $data['datos']['rol_id'], $params['pass']);
                            #preguntamos si la confirmacion se hizo satisfactoriamente
                            if ($responce['status']) {

                                #preguntamos si existe un codigo de referencia(invitado por otro usuario)
                                if (!empty($responce['cod'])) {
                                    #actualizamos la tabla usuario_invitado a ESTADO_CONFIRMADO
                                    $datInvitados = array(
                                        'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_CONFIRMADO
                                    );
                                    $whereInvitados = array("usuario_id = ?" => $responce['cod'], "usuinv_correo = ?" => $responce['email']);
                                    $this->_ModelUsuarioInvitados->update($datInvitados, $whereInvitados);
                                }

                                #proceso de login del cliente
                                $this->procesoLoginCliente($responce['email'], $params['pass']);
                            } else {
                                //$this->_helper->viewRenderer->setNoRender();
                                echo "<script>bootbox.alert('La cuenta ya ha sido confirmada',function(){document.location.href='" . Cit_Server::getContent()->host . "'});</script>";
                            }
                        }else{
                            $this->_helper->layout->disableLayout();
                            //$this->render('page-not-found');
                            //$this->view->mensaje="";
                            $this->_helper->viewRenderer->setNoRender();
                            echo "error";
                        }
                }
            } else {
                
                $this->_helper->layout->disableLayout();
                //$this->_helper->viewRenderer->setNoRender();
                $this->view->mensaje="El cliente ya esta registrado";
                $this->render('page-not-found');
                
            }
        }else{
            $this->_helper->layout->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();
            $this->view->mensaje="No existe el parametro codigo";
            $this->render('page-not-found');
            
        }
    }

    public function registroclienteAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        #pregunto si los parametros dueron enviados mediante metodo post
        if ($this->getRequest()->isPost()) {
            
            #recibo los parametros enviados en $params
            $params = $this->getRequest()->getParams();
            //$this->view->data = $params;
            
            #valido los parametros enviados
            w
        }
    }
    
    public function regintrigacorporativoAction() {
        #funcion encargada de registrar un usuario de tipo cliente
        ##
        ##
        ##
        ## deshabilito los layouts(head y footer)
        $this->_helper->layout->disableLayout();
        ##indico que esta funcion no tendra una vista
        $this->_helper->viewRenderer->setNoRender();
        
        #pregunto si los parametros dueron enviados mediante metodo post
        if ($this->getRequest()->isPost()) {
            
            #recibo los parametros enviados en $params
            $params = $this->getRequest()->getParams();
            //$this->view->data = $params;
            #verifico si el dni esta registrado en la bd
            $dataInvitacion=$this->_ModelTrabajadorInvitado->verifyExist($params['dni']);
            if(!$dataInvitacion['estado']){
                    if($this->_ModelCliente->verificarsiexisteidentidad($params['dni'])){
                        #si no existe un registro con la identidad seguimos.
                        #valido los parametros enviados
                        $mensajes = $this->_ModelUsuario->_validaFormRegistroIntriga($params);

                        #pregunto si no existe mensajes de error
                        if (empty($mensajes)) {
                            #genero un nick para el usuario cliente
                            $nick = $this->_ModelCliente->obtenerNick($params['nom_usu'] . ' ' . $params['ape_usu']);

                            #inicializo el id del referido
                            $refererID = Obj_CpnCliente::CLIENTE_DEFAULT_REFERER;

                            #pregunto si existe(se envio) el parametro referer
                            /*if (!empty($params['referer'])) {
                                #si existe obtengo el id de usuario del referido(cliente o tienda)
                                $usuario = $this->_ModelUsuario->getByAlias($params['referer']);
                                if (!empty($usuario['datos']['usuario_id'])) {
                                    $refererID = $usuario['datos']['usuario_id'];
                                }
                            }*/

                            #pregunto si existe el parametro fb (login mediante facebook)
                            //if(!empty($params['fb'])) $fb=$params['fb']; else $fb=null;
                            $fb=null;
                            #genero el codigo del usuario (servira para confirmar registro y como token)
                            $codigo = $this->_ModelUsuario->obtenerCodigo();

                            #creo el array para grabar en la tabla cliente y usuario
                            $datUsu = array(
                                'cliente_nombre' => $params['nom_usu'],
                                'cliente_apellido' => $params['ape_usu'],
                                'cliente_nick' => $nick,
                                'cliente_identidad' => $params['dni'],
                                'cliente_ciudad' => 1,
                                'cliente_fb_id' => $fb,
                                'usuario_pais' => 1,
                                'usuario_email' => $params['email_usu'],
                                'usuario_codigo' => $codigo,
                                'cliente_id_referer' => $refererID,
                                'rol_id' => Obj_SysRol::ROL_USUARIO_CORPORATIVO);

                            #guardo el registro en las tablas cliente y usuario
                            $responce = $this->_ModelUsuario->guardarUsuarioCorporativo($datUsu);

                            #pregunto si el guardado se ejecuto satisfactoriamente
                            if ($responce['status']) {
                                #guardo como trabajador de la tienda

                                $datTrabajador=array(
                                    "tienda_id"=>$dataInvitacion['tienda'],
                                    "cliente_id"=>$responce['cli_id'],
                                    "fecha_registro"=>Zend_Date::now()->toString('Y-m-d H:i:s'),
                                    "ttrabajadores_estado"=>0
                                );
                               $this->_ModelTiendaTrabajadores->insert($datTrabajador);
                                #creamos el array de datos para guardar en la tabla cpn_log_nivcli
                                $log = array(
                                    'cliente_id' => $responce['cli_id'],
                                    'nivcli_id' => '1'
                                );
                                #guardamos en la tabla cpn_log_nivcli
                                $this->_ModelLogNivCli->guardar($log);

                                #preguntamos si el cliente se registro bajo otro usuario
                                /*if (!empty($refererID)) {

                                    #preguntamos si el correo del usuario que se esta registrando ha sido invitado por algun otro usuario
                                    $responce = $this->_ModelUsuarioInvitados->verifyExistsInvitacion($params['email_usu']);
                                    if ($responce['responce']) {
                                        #preguntamos si el registro es del mismo quien le ha invitado
                                        if ($responce['id'] == $refererID) {
                                            #si es el mismo actualizamos el usuario invitado a REGISTRADO en la tabla cpn_usuario_invitados
                                            $datInvitados = array(
                                                'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO
                                            );
                                            $whereInvitados = array("usuario_id = ?" => $refererID, "usuinv_correo = ?" => $params['email_usu']);
                                            $this->_ModelUsuarioInvitados->update($datInvitados, $whereInvitados);
                                        } else {
                                            #si no es el mismo registramos al referido como si estuviera invitando al usuario(creado) en estado REGISTRADO
                                            $datInvitados = array(
                                                'usuario_id' => $refererID,
                                                'usuinv_correo' => $params['email_usu'],
                                                'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO
                                            );
                                            $this->_ModelUsuarioInvitados->guardar($datInvitados);

                                            #y cambiamos a estado REGISTRADO POR OTRO al otro usuario referido que no se llego a usar para este proceso de registro
                                            $datInvitados2 = array(
                                                'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO_OTRO
                                            );
                                            $whereInvitados2 = array("usuario_id = ?" => $responce['id'], "usuinv_correo = ?" => $params['email_usu']);
                                            $this->_ModelUsuarioInvitados->update($datInvitados2, $whereInvitados2);
                                        }
                                    } else {
                                        #si no existe una invitacion registramos al referido como si estuviera invitando al usuario(creado) en estado REGISTRADO
                                        $datInvitados = array(
                                            'usuario_id' => $refererID,
                                            'usuinv_correo' => $params['email_usu'],
                                            'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO
                                        );
                                        $this->_ModelUsuarioInvitados->guardar($datInvitados);
                                    }
                                } else {*/
                                    #si el cliente no se ha registrado bajo ningun usuario, igual consultamos si el correo de registro existe en la tabla cpn_usuario_invitados
                                    $responce = $this->_ModelUsuarioInvitados->verifyExistsInvitacion($params['email_usu']);
                                    if ($responce['responce']) {
                                        #si existe una invitacion, cambiamos el estado a REGISTRADO POR OTRO (por que no se registro bajo el perfil de quien lo invito)
                                        $datInvitados = array(
                                            'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO_OTRO
                                        );
                                        $whereInvitados = array("usuario_id = ?" => $responce['id'], "usuinv_correo = ?" => $params['email_usu']);
                                        $this->_ModelUsuarioInvitados->update($datInvitados, $whereInvitados);
                                    }
                                /*}*/

                                #enviamos correo de registro
                                $this->sendMailRegistroClienteCorporativo($datUsu);

                            } else {

                            }
                        } else {
                            echo json_encode($mensajes);
                        }
                    }else{
                        $mensajes['mail_status'] = 'Este DNI ya esta en uso';
                        echo json_encode($mensajes);
                    }
            }else{
                $mensajes['mail_status'] = 'DNI no registrado';
                echo json_encode($mensajes);
            }
        }
    }

    public function procesoLoginCliente($email, $pass) {
        if (!empty($email) && !empty($pass)) {
            $responce = $this->_ModelUsuario->autenticar(trim($email), md5(trim($pass)), $this->_fase);
            //var_dump($responce);die;
            if ($responce == true) {
                echo Zend_Registry::get('Susuario')->nick;
            } else {
                
            }
        }
    }
    public function sendMailRegistroClienteCorporativo($usdata){
        if (empty($usdata)) {
            throw new ErrorException('no se encontro el usuario para procesar la transacción');
        }

        $contenido = '';
        $asunto = 'Bienvenido al sistema de beneficios de la FAP';
        $view = new Zend_View();
        $view->setBasePath(APPLICATION_PATH
                . '/views'
                . '/scripts'
                . '/plantillasmails');
        $view->assign('usuario', $usdata);
        $dataConfiguracion=$this->_ModelConfiguracionCorporativo->getConfiguracionByID(Zend_Registry::get('config')->aws->corporativo);
        $view->assign('dataConfiguracion', $dataConfiguracion);
        $contenido = $view->render('plantilla-registro-cliente-corporativo.phtml');
        $to = $usdata['usuario_email'];
        //$bcc = '';
        $bcc = 'contacto@valeplaza.com';
        /*if (isset(Zend_Registry::get('config')->mail->mailDevelopers)) {
            $bcc = Zend_Registry::get('config')->mail->mailDevelopers;
        }*/
        try {
            //Cit_Mail::enviar($asunto, $contenido, $to);
            Cit_Mail::enviar($asunto, $contenido, $to, $bcc, '', '', TRUE);
        } catch (Exception $e) {
            Cit_Util::notificarError('No se pudo enviar el mensaje de correo cliente: ' . $usdata['usuario_email'], $e);
        }
    }
    public function sendMailRegistroCliente($usdata) {

        if (empty($usdata)) {
            throw new ErrorException('no se encontro el usuario para procesar la transacción');
        }

        $contenido = '';
        $asunto = 'Bienvenido a valeplaza';
        $view = new Zend_View();
        $view->setBasePath(APPLICATION_PATH
                . '/views'
                . '/scripts'
                . '/plantillasmails');
        $view->assign('usuario', $usdata);
        $contenido = $view->render('plantilla-registro-cliente.phtml');
        $to = $usdata['usuario_email'];
        $bcc = '';
        if (isset(Zend_Registry::get('config')->mail->mailDevelopers)) {
            $bcc = Zend_Registry::get('config')->mail->mailDevelopers;
        }
        try {
            //Cit_Mail::enviar($asunto, $contenido, $to);
            Cit_Mail::enviar($asunto, $contenido, $to, $bcc, '', '', TRUE);
        } catch (Exception $e) {
            Cit_Util::notificarError('No se pudo enviar el mensaje de correo cliente: ' . $usdata['usuario_email'], $e);
        }
    }

    public function tiendasAction() {
        //$this->_helper->layout->disableLayout();
        $code = $this->getRequest()->getParam('code', null);
        $paises = $this->_ModelPais->getAll();
        $this->view->tiendas=$this->_ModelTienda->getTiendasOnlyRegister("fecha_registro DESC",10,$code);
        $this->view->ofertas= $this->_ModelOfertas->getOfertasVigentesByTienda(null,null,null,null,false,"t1.fecha_registro DESC",10,null,$code);
        $this->view->paises = $paises;
    }
    public function registromovilAction(){
       $this->_helper->layout->disableLayout(); 
    }
    public function tiendasbasicoAction() {
        //echo date('O');die;
        //$date = Zend_Date::now();
        //$timeStamp = gmdate("Y-m-d H:i:s", $date->getTimestamp());
        //echo Zend_Date::now()->toString('Y-m-d H:i:s')."-".$timeStamp;die;
        //$this->_helper->layout->disableLayout();
        //$code = $this->getRequest()->getParam('code', null);
        //$paises = $this->_ModelPais->getAll();
        //$this->view->tiendas=$this->_ModelTienda->getTiendasOnlyRegister("fecha_registro DESC",10,$code);
        //$this->view->ofertas= $this->_ModelOfertas->getOfertasVigentesByTienda(null,null,null,null,false,"t1.fecha_registro DESC",10,null,$code);
        //$this->view->paises = $paises;
    }
    public function tiendascorporativoAction() {
        //$this->_helper->layout->disableLayout();
        //$code = $this->getRequest()->getParam('code', null);
        //$paises = $this->_ModelPais->getAll();
        //$this->view->tiendas=$this->_ModelTienda->getTiendasOnlyRegister("fecha_registro DESC",10,$code);
        //$this->view->ofertas= $this->_ModelOfertas->getOfertasVigentesByTienda(null,null,null,null,false,"t1.fecha_registro DESC",10,null,$code);
        //$this->view->paises = $paises;
    }
    
    public function regintrigamovilAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->getRequest()->isPost()) {

            #proceso para loguear
            $parametros = $this->getRequest()->getParams();
            
        $error = 0;
        $responce = array();
        $modelUsuario = new Model_CpnUsuario();
        $modelCliente = new Model_CpnCliente();
        $modelLogNivCli = new Model_CpnLogNivelCliente();
        $modelUsuarioInvitados=new Model_CpnUsuarioInvitados();
        
        $params['nombre']=urldecode($parametros['nom_usu']);
        $params['apellido']=urldecode($parametros['ape_usu']);
        $params['correo']=urldecode($parametros['email_usu']);
        $params['pais']=1;
        $params['ciudad']=1;
        $params['pass']=urldecode($parametros['pass']);
        $mensaje = $modelUsuario->wsValidaFormRegistroCliente($params);
        
        //var_dump($mensaje);echo "estancado"; die;
        if (!empty($mensaje['mensaje'])) {
            #no se cumplen los requisitos para registrar
            $responce['status'] = -1;
            $responce['messages'] = $mensaje;
            $error++;
        } else {
            #se han cumplido la validacion se puede proceder a registrar
                
            $nick = $modelCliente->obtenerNick($params['nombre'] . ' ' . $params['apellido']);
            $refererID = Obj_CpnCliente::CLIENTE_DEFAULT_REFERER;
            
            if (!empty($referer)) {
                #si existe obtengo el id de usuario del referido(cliente o tienda)
                $usuario = $modelUsuario->getByAlias($referer);
                if (!empty($usuario['datos']['usuario_id'])) {
                    $refererID = $usuario['datos']['usuario_id'];
                }else{
                    $responce['status'] = -2;
                    $responce['messages'] = 'El referido no existe';
                    return $responce;
                }
            }
            $codigo = $modelUsuario->obtenerCodigo();
            
            $datUsu = array(
                            'cliente_nombre' => $params['nombre'],
                            'cliente_apellido' => $params['apellido'],
                            'cliente_nick' => $nick,
                            'usuario_pais' => $params['pais'],
                            'cliente_ciudad' => $params['ciudad'],
                            'usuario_email' => urldecode($params['correo']),
                            'usuario_codigo' => $codigo,
                            'cliente_id_referer' => $refererID,
                            'rol_id' => Obj_SysRol::ROL_USUARIO
                    );
            $status = $modelUsuario->guardarUsuario($datUsu);
            $log = array(
                        'cliente_id' => $status['cli_id'],
                        'nivcli_id' => '1'
                    );
            $modelLogNivCli->guardar($log);
            
            if (!empty($refererID)) {
                        
                #preguntamos si el correo del usuario que se esta registrando ha sido invitado por algun otro usuario
                $responce = $modelUsuarioInvitados->verifyExistsInvitacion($params['correo']);
                if ($responce['responce']) {
                    #preguntamos si el registro es del mismo quien le ha invitado
                    if ($responce['id'] == $refererID) {
                        #si es el mismo actualizamos el usuario invitado a REGISTRADO en la tabla cpn_usuario_invitados
                        $datInvitados = array(
                            'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO
                        );
                        $whereInvitados = array("usuario_id = ?" => $refererID, "usuinv_correo = ?" => $params['correo']);
                        $modelUsuarioInvitados->update($datInvitados, $whereInvitados);
                    } else {
                        #si no es el mismo registramos al referido como si estuviera invitando al usuario(creado) en estado REGISTRADO
                        $datInvitados = array(
                            'usuario_id' => $refererID,
                            'usuinv_correo' => $params['correo'],
                            'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO
                        );
                        $modelUsuarioInvitados->guardar($datInvitados);

                        #y cambiamos a estado REGISTRADO POR OTRO al otro usuario referido que no se llego a usar para este proceso de registro
                        $datInvitados2 = array(
                            'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO_OTRO
                        );
                        $whereInvitados2 = array("usuario_id = ?" => $responce['id'], "usuinv_correo = ?" => $params['correo']);
                        $modelUsuarioInvitados->update($datInvitados2, $whereInvitados2);
                    }
                } else {
                    #si no existe una invitacion registramos al referido como si estuviera invitando al usuario(creado) en estado REGISTRADO
                    $datInvitados = array(
                        'usuario_id' => $refererID,
                        'usuinv_correo' => $params['correo'],
                        'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO
                    );
                    $modelUsuarioInvitados->guardar($datInvitados);
                }
            } else {
                #si el cliente no se ha registrado bajo ningun usuario, igual consultamos si el correo de registro existe en la tabla cpn_usuario_invitados
                $responce = $modelUsuarioInvitados->verifyExistsInvitacion($params['correo']);
                if ($responce['responce']) {
                    #si existe una invitacion, cambiamos el estado a REGISTRADO POR OTRO (por que no se registro bajo el perfil de quien lo invito)
                    $datInvitados = array(
                        'usuinv_estado' => Obj_CpnUsuarioInvitados::ESTADO_REGISTRADO_OTRO
                    );
                    $whereInvitados = array("usuario_id = ?" => $responce['id'], "usuinv_correo = ?" => $params['correo']);
                    $modelUsuarioInvitados->update($datInvitados, $whereInvitados);
                }
            }
            if(!empty($params['pass'])){
                $confirmacion=$modelUsuario->wsConfirmar($codigo, Obj_SysRol::ROL_USUARIO, $params['pass']);
                if(!empty($status['status']) && $confirmacion['status']){
                    
                    $responce['status'] = 1;
                    $responce['messages'] = 'registrado correctamente.';
                    $responce['data'] = $codigo;
                }else{
                    $responce['status'] = -1;
                    $responce['messages'] = 'Error.';
                }
            }else{
                $responce['status'] = 1;
                $responce['messages'] = 'registrado correctamente.';
                $responce['data'] = $codigo;
                $this->sendMailRegistroCliente($datUsu);
            }
                
        }
        echo json_encode($responce);
        }
    }

    public function loginfacebookAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->getRequest()->isPost()) {

            #proceso para loguear
            $params = $this->getRequest()->getParams();
            
            if (!empty($params['id'])) {
                //$this->view->data = $params;
                $mensajes = $this->_ModelUsuario->_validaFormFB($params);
                //var_dump($mensajes);die;
                if (empty($mensajes)) {
                    #el ID existe en la base de datos
                    
                    $usu=$this->_ModelUsuario->getByfacebookID($params['id']);
                    $password = trim($usu['usuario_password']);
                    $email = trim($usu['usuario_email']);
                    $this->_ModelUsuario->autenticar(trim($email), trim($password), $this->_fase);

                    //if ($responce == true) {
                    #si existe el usario logueo y redirecciono a su perfil
                    //$this->_redirect('/perfil/' . Zend_Registry::get('Susuario')->nick);
                    //$this->_redirect('/');
                    //} else {
                    //$mensajes['']['Login'] = 'Usuario o password incorrecto.';
                    //}
                } else {
                    #el ID no existe en la base de datos
                    #intentamos loguear por el correo que nos devolvio facebook
                    if(!empty($params['email_usu'])){
                        $mensajes2 = $this->_ModelUsuario->_validaFormFB2($params);
                        if(empty($mensajes2)){
                            
                            $password = trim($this->_ModelUsuario->getPasswordByEmail($params['email_usu']));
                            $email = trim($params['email_usu']);
                            $this->_ModelUsuario->autenticar(trim($email), trim($password), $this->_fase,$params['id']);
                        }else{
                            echo json_encode($mensajes2);
                        }
                    }else{
                        echo json_encode($mensajes);
                    }
                    

                    #proceso de registro de un cliente en la fase de intriga
                    //$params = $this->getRequest()->getParams();
                    //$this->view->data = $params;
                    /* $mensajes = $this->_ModelUsuario->_validaFormRegistroIntriga($params);
                      if (empty($mensajes)) {
                      #si no hay mensajes de error al validar el formulario

                      $nick=$this->_ModelCliente->obtenerNick($params['nom_usu'].' '.$params['ape_usu'] );
                      $refererID = Obj_CpnCliente::CLIENTE_DEFAULT_REFERER;

                      #guardando el usuario y cliente
                      $codigo= $this->_ModelUsuario->obtenerCodigo();
                      $datUsu = array(
                      'cliente_nombre' => $params['nom_usu'],
                      'cliente_apellido' => $params['ape_usu'],
                      'cliente_nick' => $nick,
                      'usuario_email' => $params['email_usu'],
                      'usuario_codigo' => $codigo,
                      'cliente_id_referer' => $refererID,
                      'rol_id' => Obj_SysRol::ROL_USUARIO);
                      $responce = $this->_ModelUsuario->guardarUsuario($datUsu,TRUE);
                      if ($responce['status']) {
                      $this->procesoLoginCliente($responce['email'], $params['pass']);
                      #si el usuario se guardo correctamente, enviamos un correo
                      //$mensaje = array();
                      //$this->sendMailRegistroCliente($datUsu);
                      //echo "Gracias por registrarse, se ha unido al futuro de las ofertas online. Se le ha enviado un correo para que confirme su registro";

                      //$this->view->mensajes = $mensaje;
                      /*$responce = $this->_ModelUsuario->confirmar($codigo, Obj_SysRol::ROL_USUARIO);
                      if ($responce['status']) {
                      #proceso de login del cliente

                      } else {
                      echo "Error";
                      }
                      } else {
                      echo 'Problemas con el servidor por favor vuelva a intentarlo en unos momentos.';
                      }
                      } else {
                      echo "error al registrar";
                      } */
                }
            } else {
                $mensajes['error'] = "error";
                echo json_encode($mensajes);
            }
            //$this->view->mensajes = $mensajes;
            sleep(3);
        }
    }

    /* public function logintwitterAction(){
      $redirect_uri = Cit_Server::getContent()->host.'outh/logintwitter';
      $consumer_key='WN00Bm95GiLXPophQHVkrYi2b';
      $consumer_secret='JmSGzEG0Om7B9exYPSuXNiaup0uvfL3jG7D04oypEZFLgznjJL';
      if(!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier']) && !empty(Zend_Registry::get('Susuario')->oauth_token) && !empty(Zend_Registry::get('Susuario')->oauth_token_secret)){
      //$token =$_GET['oauth_token'];
      $oauth_verifier=$_GET['oauth_verifier'];
      $to = new Cit_Twittero($consumer_key, $consumer_secret,Zend_Registry::get('Susuario')->oauth_token,Zend_Registry::get('Susuario')->oauth_token_secret);
      $tok = $to->getAccessToken( $oauth_verifier);
      Zend_Registry::get('Susuario')->oauth_token='';
      Zend_Registry::get('Susuario')->oauth_token_secret='';
      Zend_Registry::get('Susuario')->token=$tok['oauth_token'];
      Zend_Registry::get('Susuario')->token_secret=$tok['oauth_token_secret'];
      var_dump($tok);die;


      }else{
      $oauth = new Cit_Twittero($consumer_key,$consumer_secret);
      $tok = $oauth->getRequestToken( $redirect_uri );
      $url=$oauth->getAuthorizeURL($tok['oauth_token'],true);
      Zend_Registry::get('Susuario')->oauth_token=$tok['oauth_token'];
      Zend_Registry::get('Susuario')->oauth_token_secret=$tok['oauth_token_secret'];
      $this->_redirect($url);

      }
      } */
}
