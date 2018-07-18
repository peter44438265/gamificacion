<?php

class Model_CpnApiVp {

    /**
     *
     * @param <type> $codigo
     * @param <type> $coordenada
     * @return array
     */
    //protected $_ModelUsuario = new Model_CpnUsuario();
    
    public function registroUsuario($alias,$mail, $pass, $tipo='normal',$versionapk=NULL) {
        $responce = array();
        $modelUsuario = new Model_CpnUsuario();
        $error = 0;
        
        $params['email'] = $mail;
        $params['pass'] = $pass;
        $params['tipo'] = $tipo;
        $mensaje = $modelUsuario->wsValidaForm($params);
        //$modelUsuario->wsAutenticar(urldecode($mail),urldecode($pass));
        if (!empty($mensaje['mensaje'])) {
            #no existe usuario
            $responce['status'] = -1;
            $responce['messages'] = $mensaje['mensaje'];
            $error++;
        } else {
           #existe usuario (cliente,tienda,adm,market,moderador tienda)
            /*$configuracion=array(
                                "micorporacion"=>$corporacion
                            );*/
            
            
            
            if (empty($error)) {
                if(!empty($versionapk)){
                    $modelVersion = new Model_CpnVersion();
                    $status=$modelVersion->verificarversion($versionapk);
                        if($status['status'] == 1 || $status['status'] == -3 ){
                            /*si es version actual o no prioritaria*/
                            $responce['data'] = $mensaje['data'];
                        }
                    $responce['status'] = $status['status'];
                    $responce['messages'] = $status['mensaje'];
                }else{
                    $responce['status'] = -4;
                    $responce['messages']= 'no se ha enviado la version.';
                }
                /*$responce['configuracion'] = */
            }
        }

        return $responce;
    }
}
