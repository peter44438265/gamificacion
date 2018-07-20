<?php

class Cpn_OuthController extends Cit_Controller_Base {
    private $_ModelUsuario = null;
    private $_ModelCliente = null;

    public function init() {
        $this->_ModelUsuario = new Model_CpnUsuario();
        $this->_ModelCliente = new Model_CpnCliente();
    }
    public function indexAction(){
        echo "entro";die;
    }
    public function registroclienteAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        #pregunto si los parametros dueron enviados mediante metodo post
        if ($this->getRequest()->isPost()) {
            
            #recibo los parametros enviados en $params
            $params = $this->getRequest()->getParams();
            $verificarsiexistecorreo=$this->_ModelUsuario->verifyEmail(trim($params['email_usu']));
            if($verificarsiexistecorreo['status']){
                $usuarioID=$this->_ModelUsuario->guardarUsuario(array(
                        'email' => trim($params['email_usu']),
                        'password' => trim($params['password_usu']),
                        'tipo' => 1,
                        'rol_id' => Obj_SysRol::ROL_CLIENTE));
                $this->_ModelCliente->guardarCliente(array(
                        'alias' => trim($params['alias_usu']),
                        'usuario_id' => $usuarioID,
                        'puntos' => 0));
                echo json_encode(array("status" => TRUE,"mensaje" => "Gracias por registrarse, se ha unido al futuro de los anuncios online."));
            }else{
                echo json_encode(array("status" => FALSE,"mensaje" => $verificarsiexistecorreo['mensaje']));
            }
            
        }
    }
}