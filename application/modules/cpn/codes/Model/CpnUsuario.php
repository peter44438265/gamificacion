<?php

/**
 * CpnUsuario db table abstract
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Model_CpnUsuario extends Zend_Db_Table_Abstract {

    /**
     * @var string Name of db table
     */
    protected $_name = 'cpn_usuario';

    /**
     * @var string or array of fields in table
     */
    protected $_primary = 'usuario_id';

    /**
     * retorna todos los registros de la tabla  ordenado por el parametro indicado
     *
     * @return array|null 
     * @param string $order
     */
    public function changeStatusUsuario($id, $est) {
        $dat['usuario_estado'] = $est;
        $where = array("usuario_id = ?" => $id);
        $this->update($dat, $where);
    }

    public function getAll($order = null) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('(select count(*) from cpn_usuario_invitados t7 where t1.usuario_id=t7.usuario_id) as c_usu_inv', 'usuario_id', 'usuario_estado'))
                ->joinleft(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id', "concat(cliente_nombre,' ',cliente_apellido) as nombre1", 'cliente_nick as nick1'))
                ->joinleft(array('t3' => 'cpn_tienda_usuario'), 't1.usuario_id=t3.usuario_id', array(''))
                ->joinleft(array('t4' => 'cpn_tiendas'), 't3.tienda_id=t4.tienda_id', array('tienda_id', 'tienda_nombre as nombre2', 'tienda_nick as nick2'))
                ->joinleft(array('t5' => 'cpn_niveles_cliente'), 't2.nivcli_id=t5.nivcli_id', array('nivcli_titulo as titulo1'))
                ->joinleft(array('t6' => 'cpn_niveles_tienda'), 't4.nivtie_id=t6.nivtie_id', array('nivtie_titulo as titulo2'))
        //->joinleft(array('t7'=>'cpn_usuario_invitados'),'t1.usuario_id=t7.usuario_id',array('count(usuinv_id) as c_usu_inv'))
        ;
        //echo $select;die;
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    public function getClientsAll($order = null) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('(select count(*) from cpn_usuario_invitados t7 where t1.usuario_id=t7.usuario_id) as c_usu_inv', 'usuario_id', 'usuario_estado'))
                ->join(array('t3' => 'sys_paises'), 't1.usuario_pais=t3.pais_id', array('pais_nombre'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id', "concat(cliente_nombre,' ',cliente_apellido) as nombre1", 'cliente_nick as nick1', 'cliente_logo','cliente_puntos'))
                ->join(array('t5' => 'cpn_niveles_cliente'), 't2.nivcli_id=t5.nivcli_id', array('nivcli_titulo as titulo1'))
                ->join(array('t6' => 'sys_provincia'), 't2.cliente_ciudad=t6.prov_id', array('prov_nombre'))
                ->where("t1.rol_id =?", Obj_SysRol::ROL_USUARIO);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    public function getTypeByID($usID) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('rol_id'))
                ->where("t1.usuario_id =?", $usID);
        $result = $this->getAdapter()->fetchRow($select);
        if ($result['rol_id'] == Obj_SysRol::ROL_USUARIO) {
            return 'cliente';
        } else if ($result['rol_id'] == Obj_SysRol::ROL_ADMIN_TIENDA) {
            return 'tienda';
        } else {
            return 'otros';
        }
    }

    public function changeStatusTienda($id, $flag) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array(''))
                ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array('tienda_id'))
                ->where('t2.usuario_id =?', $id);
        $result = $this->getAdapter()->fetchRow($select);
        if (!empty($result)) {
            $select2 = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name), array('usuario_id'))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array(''))
                    ->where('t2.tienda_id =?', $result['tienda_id']);
            $rs = $this->getAdapter()->fetchAll($select2);
            foreach ($rs as $key => $value) {
                $dat['usuario_estado'] = $flag;
                $where = array("usuario_id = ?" => $value['usuario_id']);
                $this->update($dat, $where);
            }
        } else {
            return "error1";
        }
    }

    public function getAllMails($order = null) {
        //$key = $this->_name . '_all';
        //if (!$result = Cit_Cache::load($key)) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_email'));
        if (!empty($order))
            $select->order($order);
        $result = $this->getAdapter()->fetchAll($select);
        $arr = array();
        foreach ($result as $key => $value) {
            array_push($arr, $value['usuario_email']);
        }
        //Cit_Cache::save($result, $key);
        //}
        return $arr;
    }

    /**
     * retorna la tabla por ID
     *
     * @return array|null 
     * @param string $id
     */
    public function getById($id) {
        //$key = $this->_name . '_id' . $id;
        //if (!$result = Cit_Cache::load($key)) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_id = ?', $id);
        $result = $this->getAdapter()->fetchRow($select);
        //Cit_Cache::save($result, $key);
        //}
        return $result;
    }

    public function getByAlias($alias) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id', 'cliente_puntos', 'cliente_nombre as nombre', 'cliente_logo as logo', 'cliente_portada as portada'))
                ->where('cliente_nick = ?', $alias);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name), array('usuario_id'))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array(''))
                    ->join(array('t3' => 'cpn_tiendas'), 't2.tienda_id=t3.tienda_id', array('tienda_id', 'tienda_puntos', 'tienda_nombre as nombre', 'tienda_logo as logo', 'tienda_portada as portada'))
                    ->where('t1.usuario_estado = ?', Obj_CpnUsuario::ESTADO_ACTIVO)
                    ->where('t1.rol_id = ?', Obj_SysRol::ROL_ADMIN_TIENDA)
                    ->where('tienda_nick = ?', $alias);
            $result = $this->getAdapter()->fetchRow($select);
            return array('datos' => $result, 'tipo' => 'tienda');
        } else {
            return array('datos' => $result, 'tipo' => 'cliente');
        }
    }

    public function getDataByID($id) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'fecha_registro', 'usuario_email', 'usuario_codigo', 'usuario_estado'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id', 'cliente_nombre as nombre', 'cliente_puntos', 'cliente_nick as nick', 'cliente_logo as logo', 'cliente_portada as portada', 'cliente_animo as animo', 'cliente_estado_ayuda'))
                ->join(array('t3' => 'cpn_niveles_cliente'), 't2.nivcli_id=t3.nivcli_id', array('nivcli_titulo', 'nivcli_clase'))
                ->where('t1.rol_id =?', Obj_SysRol::ROL_USUARIO)
                ->where('t1.usuario_id = ?', $id);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name), array('usuario_id', 'fecha_registro', 'usuario_email', 'usuario_codigo', 'usuario_estado'))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array(''))
                    ->join(array('t3' => 'cpn_tiendas'), 't2.tienda_id=t3.tienda_id', array('tienda_id','rubro_id', 'tienda_nombre as nombre', 'tienda_logo as logo', 'tienda_portada as portada', 'tienda_puntos', 'tienda_nick as nick', 'tienda_descripcion', 'tienda_descripcion_breve', 'tienda_animo as animo', 'tienda_telefono', 'tienda_web', 'tienda_estado_ayuda', 'tienda_url_logo', 'tienda_tipo', 'tienda_direccion', 'tienda_url_fb', 'tienda_count_visitas', 'tienda_peticion_ofertas','tienda_contrato', '(select count(*) from cpn_tienda_seguidores s1 where s1.tienda_id=t3.tienda_id and tseg_estado=' . Obj_CpnTiendaSeguidores::SIGUE . ') as seguidores'))
                    ->joinleft(array('t4' => 'cpn_rubro'), 't4.rubro_id=t3.rubro_id', array('rubro_nombre'))
                    ->join(array('t5' => 'cpn_niveles_tienda'), 't3.nivtie_id=t5.nivtie_id', array('nivtie_titulo', 'nivtie_clase'))
                    ->where('(t1.rol_id =' . Obj_SysRol::ROL_ADMIN_TIENDA . ' or t1.rol_id =' . Obj_SysRol::ROL_ADMIN . ')')
                    //->orwhere('t1.rol_id =?', Obj_SysRol::ROL_ADMIN)
                    ->where('t1.usuario_id = ?', $id);
            //echo $select;die;
            $result = $this->getAdapter()->fetchRow($select);
            if (!empty($result['fecha_registro'])) {
                $fec = explode(' ', $result['fecha_registro']);
                $fec = explode('-', $fec[0]);
                $result['fecha_registro'] = $fec[2] . "/" . $fec[1] . "/" . $fec[0];
            }
            return array('datos' => $result, 'tipo' => 'tienda');
        } else {
            $fec = explode(' ', $result['fecha_registro']);
            $fec = explode('-', $fec[0]);
            $result['fecha_registro'] = $fec[2] . "/" . $fec[1] . "/" . $fec[0];
            return array('datos' => $result, 'tipo' => 'cliente');
        }
    }

    public function getDataByEmail($email) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'fecha_registro', 'usuario_password'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id', 'cliente_nombre as nombre', 'cliente_puntos', 'cliente_nick as nick', 'cliente_logo as logo'))
                ->where('t1.usuario_email = ?', $email);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name), array('usuario_id', 'fecha_registro', 'usuario_password'))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array(''))
                    ->join(array('t3' => 'cpn_tiendas'), 't2.tienda_id=t3.tienda_id', array('tienda_id', 'tienda_nombre as nombre', 'tienda_logo as logo', 'tienda_portada as portada', 'tienda_puntos', 'tienda_nick as nick', 'tienda_descripcion', 'tienda_telefono', 'tienda_web', '(select count(*) from cpn_tienda_seguidores s1 where s1.tienda_id=t3.tienda_id and tseg_estado=' . Obj_CpnTiendaSeguidores::SIGUE . ') as seguidores'))
                    ->joinleft(array('t4' => 'cpn_rubro'), 't4.rubro_id=t3.rubro_id', array('rubro_nombre'))
                    ->where('t1.usuario_id = ?', $email);
            $result = $this->getAdapter()->fetchRow($select);
            return array('datos' => $result, 'tipo' => 'tienda');
        } else {
            return array('datos' => $result, 'tipo' => 'cliente');
        }
    }

    public function getDataByEmailActivo($email) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'fecha_registro', 'usuario_password'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id', 'cliente_nombre as nombre', 'cliente_puntos', 'cliente_nick as nick', 'cliente_logo as logo'))
                ->where('t1.usuario_email = ?', $email)
                ->where('t1.usuario_estado =?', Obj_CpnUsuario::ESTADO_ACTIVO);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name), array('usuario_id', 'fecha_registro', 'usuario_password'))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array(''))
                    ->join(array('t3' => 'cpn_tiendas'), 't2.tienda_id=t3.tienda_id', array('tienda_id', 'tienda_nombre as nombre', 'tienda_logo as logo', 'tienda_portada as portada', 'tienda_puntos', 'tienda_nick as nick', 'tienda_descripcion', 'tienda_telefono', 'tienda_web', '(select count(*) from cpn_tienda_seguidores s1 where s1.tienda_id=t3.tienda_id and tseg_estado=' . Obj_CpnTiendaSeguidores::SIGUE . ') as seguidores'))
                    ->joinleft(array('t4' => 'cpn_rubro'), 't4.rubro_id=t3.rubro_id', array('rubro_nombre'))
                    ->where('t1.usuario_email = ?', $email)
                    ->where('t1.usuario_estado <>?', Obj_CpnUsuario::ESTADO_INACTIVO);
            $result = $this->getAdapter()->fetchRow($select);
            return array('datos' => $result, 'tipo' => 'tienda');
        } else {
            return array('datos' => $result, 'tipo' => 'cliente');
        }
    }

    public function getDataByAlias($alias) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_cliente'))
                ->join(array('t2' => 'cpn_usuario'), 't1.usuario_id=t2.usuario_id', array('usuario_id'))
                ->where("cliente_nick = '" . $alias . "'");
        $rs = $this->getAdapter()->fetchRow($select);
        if (empty($rs)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_tiendas'))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.tienda_id=t2.tienda_id', array(''))
                    ->join(array('t3' => 'cpn_usuario'), 't2.usuario_id=t3.usuario_id', array('usuario_id'))
                    ->where("tienda_nick = '" . $alias . "' and (t3.rol_id='" . Obj_SysRol::ROL_ADMIN_TIENDA . "' or t3.rol_id='" . Obj_SysRol::ROL_ADMIN . "' or t3.rol_id='" . Obj_SysRol::ROL_MODERADOR_TIENDA . "' or t3.rol_id='" . Obj_SysRol::ROL_MARKET . "')")
                    ->where('t3.usuario_estado = ?', Obj_CpnUsuario::ESTADO_ACTIVO);
            //echo $select;die;
            $result = $this->getAdapter()->fetchRow($select);
            return $result;
        } else {
            return $rs;
        }
    }

    public function getAllUsersByAlias($alias) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_cliente'), array(''))
                ->join(array('t2' => 'cpn_usuario'), 't1.usuario_id=t2.usuario_id', array('usuario_id'))
                ->where("cliente_nick = '" . $alias . "'");
        $rs = $this->getAdapter()->fetchAll($select);
        if (empty($rs)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_tiendas'), array(''))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.tienda_id=t2.tienda_id', array(''))
                    ->join(array('t3' => 'cpn_usuario'), 't2.usuario_id=t3.usuario_id', array('usuario_id'))
                    ->where("tienda_nick = '" . $alias . "'");
            //->where('t3.rol_id =?', Obj_SysRol::ROL_ADMIN_TIENDA);
            $result = $this->getAdapter()->fetchAll($select);
            return $result;
        } else {//$usID=$modelUsuario->getIdById($datos['cliente_id'],'cli');
            return $rs;
        }
    }

    public function getUserAdmByID($id) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_cliente'), array(''))
                ->join(array('t2' => 'cpn_usuario'), 't1.usuario_id=t2.usuario_id', array('usuario_id', 'fecha_registro'))
                ->where("t2.usuario_id = '" . $id . "'");
        $rs = $this->getAdapter()->fetchRow($select);
        if (empty($rs)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_tiendas'), array(''))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.tienda_id=t2.tienda_id', array(''))
                    ->join(array('t3' => 'cpn_usuario'), 't2.usuario_id=t3.usuario_id', array('usuario_id'))
                    ->where("t3.usuario_id = '" . $id . "' and t3.rol_id ='" . Obj_SysRol::ROL_ADMIN_TIENDA . "'");
            $result = $this->getAdapter()->fetchRow($select);
            return $result;
        } else {//$usID=$modelUsuario->getIdById($datos['cliente_id'],'cli');
            return $rs;
        }
    }

    public function getIdById($id, $type) {
        if ($type == 'cli') {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_cliente'), array(''))
                    ->join(array('t2' => 'cpn_usuario'), 't1.usuario_id=t2.usuario_id', array('usuario_id'))
                    ->where("cliente_id = '" . $id . "'");
        } elseif ($type == 'tienda') {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_tiendas'), array(''))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.tienda_id=t2.tienda_id', array(''))
                    ->join(array('t3' => 'cpn_usuario'), 't2.usuario_id=t3.usuario_id', array('usuario_id'))
                    ->where("t1.tienda_id = '" . $id . "' and t3.rol_id ='" . Obj_SysRol::ROL_ADMIN_TIENDA . "'");
        }

        $rs = $this->getAdapter()->fetchRow($select);

        return $rs['usuario_id'];
    }

    public function getReferidosByID($id) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_cliente'), array('*'))
                ->join(array('t2' => $this->_name), 't1.usuario_id=t2.usuario_id', array('usuario_email', 'usuario_estado'))
                ->where("cliente_id_referer = '" . $id . "'");
        $rs = $this->getAdapter()->fetchAll($select);
        return $rs;
    }

    public function getReferidosActivosByID($id) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_cliente'), array('*'))
                ->join(array('t2' => $this->_name), 't1.usuario_id=t2.usuario_id', array('usuario_email', 'usuario_estado'))
                ->where("cliente_id_referer = '" . $id . "'")
                ->where("usuario_estado = '" . Obj_CpnUsuario::ESTADO_ACTIVO . "'");
        $rs = $this->getAdapter()->fetchAll($select);
        return $rs;
    }

    /**
     * Inserta o actualiza la tabla segun sea el caso
     *
     * @param array $datos
     * @param string $fechaEdicion
     * @return string
     */
    public function guardar($datos, $fechaEdicion = TRUE) {
        if (isset($datos['usuario_id'])) {
            if ($fechaEdicion == TRUE) {
                $datos['fecha_edicion'] = Zend_Date::now()->toString('Y-m-d H:i:s');
            }
            $id = $datos['usuario_id'];
            $where = array("usuario_id = ?" => $id);
            $this->update($datos, $where);
        } else {
            $datos['fecha_registro'] = Zend_Date::now()->toString('Y-m-d H:i:s');
            $id = $this->insert($datos);
        }
        return $id;
    }

    public function guardarUsuario($datos, $fb = FALSE) {
        $modelCliente = new Model_CpnCliente();

        $datCli = array();
        if (empty($datos['cliente_nacimiento']))
            $datos['cliente_nacimiento'] = '';
        if (empty($datos['usuario_password']))
            $datos['usuario_password'] = '';
        $datCli['cliente_nombre'] = $datos['cliente_nombre'];
        $datCli['cliente_apellido'] = $datos['cliente_apellido'];
        $datCli['cliente_nacimiento'] = $datos['cliente_nacimiento'];
        $datCli['cliente_nick'] = $datos['cliente_nick'];
        $datCli['cliente_id_referer'] = $datos['cliente_id_referer'];
        $datCli['cliente_ciudad'] = $datos['cliente_ciudad'];
        if(!empty($datos['cliente_fb_id'])) $datCli['cliente_fb_id'] = $datos['cliente_fb_id']; else $datCli['cliente_fb_id'] = NULL; 
        unset($datos['cliente_nombre']);
        unset($datos['cliente_apellido']);
        unset($datos['cliente_nacimiento']);
        unset($datos['cliente_nick']);
        unset($datos['cliente_id_referer']);
        unset($datos['cliente_ciudad']);
        unset($datos['cliente_fb_id']);


        #obtengo datos del usuario segun su correo
        /* $data = $this->fetchRow(array('usuario_email = ?' => $datos['usuario_email']));

          if (!empty($data['rol_id'])) {
          if ($data['rol_id'] == ModelObjRol::ROL_VISITANTE) {
          $datos['rol_id'] = ModelObjRol::ROL_USUARIO;
          }
          }
          if (empty($datos['cliente_facebook_id'])) {
          if (!empty($data['cliente_facebook_id'])) {
          $datCli['cliente_facebook_id'] = $data['cliente_facebook_id'];
          } else {
          $datCli['cliente_facebook_id'] = '';
          }
          }

          if (empty($datos['cliente_facebook_link'])) {
          if (!empty($data['cliente_facebook_link'])) {
          $datCli['cliente_facebook_link'] = $data['cliente_facebook_link'];
          } else {
          $datCli['cliente_facebook_link'] = '';
          }
          } */

        //if(empty($data)){
        $datos['fecha_registro'] = Zend_Date::now()->toString('Y-m-d H:i:s');
        if ($fb) {
            $codigoCliente = $modelCliente->obtenerCodigoCliente();
            $datCli['cliente_codigo'] = $codigoCliente;
            $datos['usuario_estado'] = Obj_CpnUsuario::ESTADO_ACTIVO;
        } else {
            $datos['usuario_estado'] = Obj_CpnUsuario::ESTADO_ESPERA;
        }


        #guardo en la tabla usuarios y recupero el ID
        $id = $this->insert($datos);

        $datCli['usuario_id'] = $id;
        $datCli['cliente_puntos'] = Obj_CpnPuntajes::NULL_PUNTOS;

        #guardo en la tabla cliente incluido los puntos por registro
        $cliID = $modelCliente->guardar($datCli);


        //}
        return array('status' => true, 'usuario_id' => $id, 'cli_id' => $cliID);
    }

    public function getByEmail($email) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_email = ?', $email);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            /* $select2 = $this->getAdapter()->select()
              ->from(array('t1' => 'cpn_invitados'))
              ->where('invitado_email = ?', $email);
              $result=$this->getAdapter()->fetchRow($select2); */
            return $result;
        } else {
            return $result;
        }
    }

    public function getByEmail2($email) {
        #registro de un moderador de una tienda
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_email = ?', $email);


        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select2 = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_usuario_invitados'))
                    ->where('usuinv_correo = ?', $email);
            $result = $this->getAdapter()->fetchRow($select2);
            return $result;
        } else {
            return $result;
        }
    }

    public function getByEmail3($email) {
        #registro de una tienda en la tabla de invitados
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_email = ?', $email);


        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select2 = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_usuario_invitados'))
                    ->where('usuinv_correo = ?', $email);
            $result = $this->getAdapter()->fetchRow($select2);
            if (empty($result)) {
                $select2 = $this->getAdapter()->select()
                        ->from(array('t1' => 'cpn_invitados'))
                        ->where('invitado_email = ?', $email);
                $result = $this->getAdapter()->fetchRow($select2);
                return $result;
            } else {
                return $result;
            }
        } else {
            return $result;
        }
    }

    public function confirmar($codigo, $rol = Obj_SysRol::ROL_USUARIO, $pass = null) {
        #proceso logico de confrirmacion de un usuario cliente
        $modelLog = new Model_CpnLogPuntos();
        $modelPuntaje = new Model_CpnPuntajes();
        $modelCliente = new Model_CpnCliente();
        $modelTienda = new Model_CpnTiendas();
        $modelAmistad = new Model_CpnAmistad();
        $modelSeguidor = new Model_CpnTiendaSeguidores();
        //$modelClienteTrofeos = new Model_CpnClienteTrofeos();
        
        #obtengo datos del usuario segun codigo de confirmacion
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'usuario_email', 'usuario_password'))
                ->where('usuario_codigo = ?', $codigo)
                ->where('usuario_estado = ?', Obj_CpnUsuario::ESTADO_ESPERA)
                ->where('rol_id = ?', $rol);
        $arrCodigo = $this->getAdapter()->fetchRow($select);

        if (!empty($pass)) {
            #actualizo el password del usuario
            $datusu = array(
                'usuario_password' => md5($pass)
            );
            $where = array('usuario_id = ?' => $arrCodigo['usuario_id']);
            $this->update($datusu, $where);
        }
        if (!empty($arrCodigo)) {

            #habilitar usuario
            $dat = array();
            $dat['usuario_estado'] = Obj_CpnUsuario::ESTADO_ACTIVO;
            $where = array('usuario_id =?' => $arrCodigo['usuario_id']);
            $this->update($dat, $where);



            if ($rol == Obj_SysRol::ROL_ADMIN_TIENDA) {
                #proceso de confirmacion para un usuario TIPO TIENDA
                
                $modelPuntaje = new Model_CpnPuntajes();
                //$puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::REGISTRO_EMPRESA_ID);
                $select = $this->getAdapter()->select()
                        ->from(array('t1' => 'cpn_tiendas'), array('tienda_id', 'tienda_nick'))
                        ->join(array('t2' => 'cpn_tienda_usuario'), 't1.tienda_id=t2.tienda_id', array(''))
                        ->join(array('t3' => $this->_name), 't2.usuario_id=t3.usuario_id', array('usuario_password'))
                        ->where('t3.usuario_id = ?', $arrCodigo['usuario_id'])
                        ->where('rol_id = ?', Obj_SysRol::ROL_ADMIN_TIENDA);
                $arrTiendaId = $this->getAdapter()->fetchRow($select);
                if (!empty($arrTiendaId)) {
                    //$ModelTienda = new Model_CpnTiendas();
                    $dat = array();
                    $codigoTienda = $modelTienda->obtenerCodigoTienda();
                    $dat['tienda_codigo'] = $codigoTienda;
                    $dat['tienda_estado'] = Obj_CpnTiendas::ESTADO_ACTIVO;
                    //$dat['tienda_puntos'] = $puntos;
                    $where = array('tienda_id =?' => $arrTiendaId['tienda_id']);
                    $nick = $arrTiendaId['tienda_nick'];
                    $pass = $arrTiendaId['usuario_password'];
                    $modelTienda->update($dat, $where);
                }
            } elseif ($rol == Obj_SysRol::ROL_USUARIO) {

                #proceso de confirmacion para un suaurio de TIPO CLIENTE
                $select = $this->getAdapter()->select()
                        ->from(array('t1' => $this->_name), array('usuario_password'))
                        ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id_referer', 'cliente_id', 'cliente_nick', 'cliente_nombre'))
                        ->where('t1.usuario_id = ?', $arrCodigo['usuario_id']);
                $referer = $this->getAdapter()->fetchRow($select);

                $nick = $referer['cliente_nick'];
                $pass = $referer['usuario_password'];

                $dat = array();
                $codigoCliente = $modelCliente->obtenerCodigoCliente();
                $dat['cliente_codigo'] = $codigoCliente;
                $where = array('cliente_id =?' => $referer['cliente_id']);
                $modelCliente->update($dat, $where);
                /**/
                # se le da puntos al cliente por registrarse
                $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::REGISTRO_ID);
                $descripcion = "valeplaza te regala puntos por registrarte";
                $datLog = array(
                    'usuario_id' => $arrCodigo['usuario_id'],
                    'usuario_id_id' => null,
                    'usuario_id_causante' => null,
                    'pnt_id' => Obj_CpnPuntajes::REGISTRO_ID,
                    'log_puntos' => $puntos,
                    'log_descripcion' => $descripcion,
                    'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                );

                #el cliente referido gana puntos tipo referer
                $modelLog->guardar($datLog);

                #VALEPLAZA pierde puntos
                $this->LogValeplaza($arrCodigo['usuario_id'], Obj_CpnPuntajes::REGISTRO_ID, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

                /*$oldpuntos = $modelCliente->getPuntajeById($referer['cliente_id']);
                $newpuntos = $oldpuntos + $puntos;
                $cli = array('cliente_puntos' => $newpuntos);*/
                $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                            );
                $where = array('usuario_id = ?' => $arrCodigo['usuario_id']);

                #actualizamos los puntos del cliente referido
                $modelCliente->update($datcli, $where);
                /**/
                if (!empty($referer['cliente_id_referer'])) {
                    #si existe el cliente se registro bajo la referencia de un usuario tipo "cliente" o "tienda"
                    $usuario = $this->getDataByID($referer['cliente_id_referer']);
                    $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::REFERER);
                    #LE DAMOS PUNTOS AL CLIENTE POR SER INVITADO DE OTRO USUARIO
                    $datLog = array(
                        'usuario_id' => $arrCodigo['usuario_id'],
                        'usuario_id_id' => null,
                        'usuario_id_causante' => $usuario['datos']['usuario_id'],
                        'pnt_id' => Obj_CpnPuntajes::REFERER,
                        'log_puntos' => $puntos,
                        'log_descripcion' => "ganas puntos por llegar a Valeplaza gracias a ".$usuario['datos']['nombre'],
                        'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                    );

                    #el cliente referido gana puntos tipo referer
                    $modelLog->guardar($datLog);
                    
                    #VALEPLAZA pierde puntos
                    $this->LogValeplaza($arrCodigo['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);
                    /*$oldpuntos = $modelCliente->getPuntajeById($arrCodigo['usuario_id']);
                    $newpuntos = $oldpuntos + ($puntos);
                    $cli = array('cliente_puntos' => $newpuntos);*/
                    $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                            );
                    $where = array('usuario_id = ?' => $arrCodigo['usuario_id']);
                    #actualizamos los puntos del cliente referido
                    $modelCliente->update($datcli, $where);
                    if ($usuario['tipo'] == 'cliente') {
                        #el cliente referido gana puntos tipo registro si cumple requisitos

                        $this->_setTrofeoToClient($usuario['datos']['usuario_id'], $usuario['datos']['cliente_id'], $usuario['datos']['cliente_puntos'], Obj_CpnClienteTrofeos::TIP_AMX_REQ, Obj_CpnClienteTrofeos::TIP_AMX_REQ_TEXT);
                        //$status = $modelClienteTrofeos->verifyLogroByCliente($referer['cliente_id'], Obj_CpnClienteTrofeos::TROFEO_DESB_CONTA);
                        //if ($return) {
                        #si el cliente referido ya obtuvo del logro del desbloqueo del contador
                        
                        $descripcion = $referer['cliente_nombre'] . ' es tu nuevo amigo, ganas puntos por haberlo invitado a Valeplaza';
                        $datLog = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => null,
                            'usuario_id_causante' => $arrCodigo['usuario_id'],
                            'pnt_id' => Obj_CpnPuntajes::REFERER,
                            'log_puntos' => $puntos,
                            'log_descripcion' => $descripcion,
                            'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                        );

                        #el cliente referido gana puntos tipo referer
                        $modelLog->guardar($datLog);

                        #VALEPLAZA pierde puntos
                        $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

                        //$oldpuntos = $modelCliente->getPuntajeById($usuario['datos']['cliente_id']);
                        //$newpuntos = $oldpuntos + ($puntos);
                        //$cli = array('cliente_puntos' => $newpuntos);
                        $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                            );
                        $where = array('usuario_id = ?' => $usuario['datos']['usuario_id']);

                        #actualizamos los puntos del cliente referido
                        $modelCliente->update($datcli, $where);
                        //}
                        #se lo coloca como amigo del cliente referido
                        $datAmigo1 = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => $arrCodigo['usuario_id'],
                            'usuario_id_ini_amistad' => $usuario['datos']['usuario_id'],
                            'amistad_tipo' => Obj_CpnAmistad::TIPO_AMISTAD,
                            'amistad_estado' => Obj_CpnAmistad::AMISTAD_ACTIVO
                        );
                        $datAmigo2 = array(
                            'usuario_id' => $arrCodigo['usuario_id'],
                            'usuario_id_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_ini_amistad' => $usuario['datos']['usuario_id'],
                            'amistad_tipo' => Obj_CpnAmistad::TIPO_AMISTAD,
                            'amistad_estado' => Obj_CpnAmistad::AMISTAD_ACTIVO
                        );
                        $modelAmistad->guardar($datAmigo1);
                        $modelAmistad->guardar($datAmigo2);

                        #######################################
                        $selectco = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_cliente'), array('count(*) as num'))
                                ->where('t1.cliente_id_referer = ?', $usuario['datos']['usuario_id']);
                        $count = $this->getAdapter()->fetchRow($selectco);
                        if ($count['num'] == 3) {
                            $descripcion = "VALEPLAZA te premia por invitar a tus 3 primeros amigos";
                            $datLog['usuario_id'] = $usuario['datos']['usuario_id'];
                            $datLog['usuario_id_id'] = null;
                            $datLog['usuario_id_causante'] = null;
                            $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                            $datLog['log_puntos'] = 30;
                            $datLog['log_descripcion'] = $descripcion;
                            $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;

                            #guardamos el log de los puntos ganados por el cliente
                            $modelLog->guardar($datLog);

                            #guardamos el LOG del banco valeplaza
                            $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::PREMIO, 30, Obj_CpnLogPuntos::TIPO_PIERDE);
                            $wherecli = array("cliente_id = ?" => $usuario['datos']['cliente_id']);
                            $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + 30'),
                            );
                            $modelCliente->update($datcli, $wherecli);
                        }


                        #######################################
                    } else {
                        #puntos para el referido tipo tienda

                        $this->_setTrofeoToTienda($usuario['datos']['usuario_id'], $usuario['datos']['tienda_id'], $usuario['datos']['tienda_puntos'], Obj_CpnClienteTrofeos::TIP_SOC_INV, Obj_CpnClienteTrofeos::TIP_SOC_INV_TEXT);

                        $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::SEGUIR);
                        $descripcion = $referer['cliente_nombre'] . ' es tu nuevo socio, ganas puntos';
                        $datLog = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => null,
                            'usuario_id_causante' => $arrCodigo['usuario_id'],
                            'pnt_id' => Obj_CpnPuntajes::SEGUIR,
                            'log_puntos' => $puntos,
                            'log_descripcion' => $descripcion,
                            'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                        );
                        $modelLog->guardar($datLog);
                        #dar los puntos a la tienda  
                        /*$oldpuntos = $modelTienda->getPuntajeById($usuario['datos']['tienda_id']);
                        $newpuntos = $oldpuntos + $puntos;
                        $tie = array('tienda_puntos' => $newpuntos);*/
                        $datTie = array(
                                'tienda_puntos' => new Zend_Db_Expr('tienda_puntos + '.$puntos),
                            );
                        $where = array('tienda_id = ?' => $usuario['datos']['tienda_id']);

                        #actualizamos de la tienda referida
                        $modelTienda->update($datTie, $where);

                        #se lo coloca como seguidor de la tienda referida
                        $datSeguidor = array(
                            'tienda_id' => $usuario['datos']['tienda_id'],
                            'cliente_id' => $referer['cliente_id'],
                            'tseg_estado' => Obj_CpnTiendaSeguidores::SIGUE
                        );
                        $modelSeguidor->guardar($datSeguidor);

                        #VALEPLAZA pierde puntos
                        $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

                        #######################################
                        $selectco = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_cliente'), array('count(*) as num'))
                                ->where('t1.cliente_id_referer = ?', $usuario['datos']['usuario_id']);
                        $count = $this->getAdapter()->fetchRow($selectco);
                        if ($count['num'] == 3) {
                            $descripcion = "VALEPLAZA te premia por invitar a tus 3 primeros socios";
                            $datLog['usuario_id'] = $usuario['datos']['usuario_id'];
                            $datLog['usuario_id_id'] = null;
                            $datLog['usuario_id_causante'] = null;
                            $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                            $datLog['log_puntos'] = 50;
                            $datLog['log_descripcion'] = $descripcion;
                            $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;

                            #guardamos el log de los puntos ganados por el cliente
                            $modelLog->guardar($datLog);

                            #guardamos el LOG del banco valeplaza
                            $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::PREMIO, 50, Obj_CpnLogPuntos::TIPO_PIERDE);
                            $wheretie = array("tienda_id = ?" => $usuario['datos']['tienda_id']);
                            $dattie = array(
                                'tienda_puntos' => new Zend_Db_Expr('tienda_puntos + 50'),
                            );
                            $modelTienda->update($dattie, $wheretie);
                        }


                        #######################################
                    }
                }
            }
            if (!empty($referer['cliente_id_referer']))
                $cod = $referer['cliente_id_referer'];
            else
                $cod = null;
            return array('status' => TRUE, 'email' => $arrCodigo['usuario_email'], 'pass' => $pass, 'nick' => $nick, 'cod' => $cod);
        } else {
            return array('status' => FALSE);
        }
    }

    public function _setTrofeoToClient($usID, $cliID, $oldpuntos, $tip, $tiptext) {
        //$modelPuntaje = new Model_CpnPuntajes();
        $modelLog = new Model_CpnLogPuntos();
        $modelClienteLogro = new Model_CpnClienteTrofeos();
        $modelCliente = new Model_CpnCliente();
        $modelLogNivelClie = new Model_CpnLogNivelCliente();
        $modelTiendaSeguidores = new Model_CpnTiendaSeguidores();
        $modelValesCompartidos = new Model_CpnValesCompartidos();
        if ($tip == Obj_CpnClienteTrofeos::TIP_AMX_REQ) {
            #obtengo la cantidad de amigos invitados y confirmados (cpn_usuario)
            $referidos = $this->getReferidosActivosByID($usID);
            $i = count($referidos);
        } else if ($tip == Obj_CpnClienteTrofeos::TIP_CLUB_ASOC) {
            #obtengo la cantidad de clubs del cliente
            $listFavoritos = $modelTiendaSeguidores->getFavoritos($cliID);
            $i = count($listFavoritos);
        } else if ($tip == Obj_CpnClienteTrofeos::TIP_COMP_VAL) {
            #obtengo la cantidad de vales compartidos
            $listCompartidos = $modelValesCompartidos->getCompartidosByCliente($cliID);
            $i = count($listCompartidos);
        }
        $slcttrofeos = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_trofeos'), array('trofeo_id', 'trofeo_amigos_req', 'trofeo_clubes_req', 'trofeo_ahorro_req', 'trofeo_puntos', 'nivcli_id', 'trofeo_nombre'))
                ->where('tiptro_id =?', $tip);

        $rstrofeos = $this->getAdapter()->fetchAll($slcttrofeos);
        foreach ($rstrofeos as $key => $value) {
            #recorro todos los trofeos segun el tipo ($tip)
            //echo $value[$tiptext].'-'.$i;
            if ($value[$tiptext] == $i) {
                #si encontramos una coincidencia entre los referidos actuales y los amigos_req de un logro
                $verify = $modelClienteLogro->verifyLogroByCliente($cliID, $value['trofeo_id']);
                if (!$verify) {
                    #no existe una relacion cliente-logro, es la primera vez que llega al requerimiento del logro
                    #guardamos el logro conseguido por el cliente
                    $datLogro = array(
                        'cliente_id' => $cliID,
                        'trofeo_id' => $value['trofeo_id'],
                        'clitro_estatus' => 0
                    );
                    $modelClienteLogro->guardar($datLogro);

                    $puntos = $value['trofeo_puntos'];
                    $newpuntos = $oldpuntos + $puntos;
                    $cli = array('cliente_puntos' => $newpuntos);
                    $where = array('usuario_id = ?' => $usID);
                    #actualizamos los puntos del cliente referido
                    $modelCliente->update($cli, $where);

                    $descripcion = "VALEPLAZA te premia por conseguir el logro " . $value['trofeo_nombre'];
                    $datLog['usuario_id'] = $usID;
                    $datLog['usuario_id_id'] = null;
                    $datLog['usuario_id_causante'] = null;
                    $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                    $datLog['log_puntos'] = $puntos;
                    $datLog['log_descripcion'] = $descripcion;
                    $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;
                    #guardamos el log de los puntos ganados por el cliente
                    $modelLog->guardar($datLog);

                    #guardo el LOG del banco VALEPLAZA
                    $this->LogValeplaza($usID, Obj_CpnPuntajes::PREMIO, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);
                    if (!empty($value['nivcli_id'])) {
                        #el trofeo esta relacionado a un nivel
                        #obtenemos los logros conseguidos por el cliente que tienen relacion con el nivel
                        $slctclitro = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_cliente_trofeo'), array('clitro_id'))
                                ->join(array('t2' => 'cpn_trofeos'), 't1.trofeo_id=t2.trofeo_id', array(''))
                                ->where('t2.nivcli_id =?', $value['nivcli_id'])
                                ->where('t1.cliente_id =?', $cliID);
                        $rsclitro = $this->getAdapter()->fetchAll($slctclitro);
                        #contamos el numero de trofeos
                        $cantTrofeosCli = count($rsclitro);
                        #consultamos si el nivel ha conseguido la cantidad de logros requeridos
                        $slctnivcli = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_niveles_cliente'), array('nivcli_id'))
                                ->where('t1.trofeos_req =?', $cantTrofeosCli)
                                ->where('t1.nivcli_id =?', $value['nivcli_id']);
                        $rsnivcli = $this->getAdapter()->fetchAll($slctnivcli);
                        if (!empty($rsnivcli)) {
                            #el cliente ha logrado conseguir los/el trofeo(s) necesario para subir de nivel
                            $cli = array('nivcli_id' => $value['nivcli_id']);
                            $where = array('cliente_id = ?' => $cliID);
                            $modelCliente->update($cli, $where);
                            #registramos en el log del nuevo nivel al que llego el cliente
                            $log = array(
                                'cliente_id' => $cliID,
                                'nivcli_id' => $value['nivcli_id']
                            );
                            $modelLogNivelClie->guardar($log);
                        }
                    } else {
                        #el trofeo no esta relacionado a un nivel
                    }
                }
            }
        }
    }

    public function _setTrofeoToTienda($usID, $tieID, $oldpuntos, $tip, $tiptext) {
        //$modelPuntaje = new Model_CpnPuntajes();
        $modelLog = new Model_CpnLogPuntos();
        $modelTiendaLogro = new Model_CpnTiendaTrofeos();
        $modelTienda = new Model_CpnTiendas();
        $modelLogNivelTie = new Model_CpnLogNivelTienda();
        $modelTiendaSeguidores = new Model_CpnTiendaSeguidores();
        $modelOfertas = new Model_CpnOferta();
        if ($tip == Obj_CpnClienteTrofeos::TIP_SOC_INV) {
            #obtengo la cantidad de socios adquiridos
            //$referidos = $modelTiendaSeguidores->getSeguidoresByTienda($tieID);
            $referidos = $this->getReferidosActivosByID($usID);
            $i = count($referidos);
        } else if ($tip == Obj_CpnClienteTrofeos::TIP_VAL_PBL) {
            #obtengo la cantidad de vales publicados
            $listVales = $modelOfertas->getAllOfertasByTienda($tieID);
            $i = count($listVales);
        }
        $slcttrofeos = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_trofeos_tie'), array('trotie_id', 'trotie_socios_req', 'trotie_puntos', 'trotie_vales_req', 'nivtie_id', 'trotie_nombre'))
                ->where('tiptro_id =?', $tip);
        $rstrofeos = $this->getAdapter()->fetchAll($slcttrofeos);
        foreach ($rstrofeos as $key => $value) {
            #recorro todos los trofeos segun el tipo ($tip)
            //echo $value[$tiptext].'-'.$i;die;
            if ($value[$tiptext] == $i) {
                #si encontramos una coincidencia entre los referidos actuales y los amigos_req de un logro
                $verify = $modelTiendaLogro->verifyLogroByTienda($tieID, $value['trotie_id']);
                if (!$verify) {
                    #no existe una relacion tienda-logro, es la primera vez que llega al requerimiento del logro
                    #guardamos el logro conseguido por la tienda
                    $datLogro = array(
                        'tienda_id' => $tieID,
                        'trotie_id' => $value['trotie_id'],
                        'tietro_estatus' => 0
                    );
                    $modelTiendaLogro->guardar($datLogro);

                    $puntos = $value['trotie_puntos'];
                    $newpuntos = $oldpuntos + $puntos;
                    $tie = array('tienda_puntos' => $newpuntos);
                    $where = array('tienda_id = ?' => $tieID);
                    #actualizamos los puntos de la tienda referido
                    $modelTienda->update($tie, $where);
                    $descripcion = "VALEPLAZA te premia por conseguir el logro " . $value['trotie_nombre'];
                    $datLog['usuario_id'] = $usID;
                    $datLog['usuario_id_id'] = null;
                    $datLog['usuario_id_causante'] = null;
                    $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                    $datLog['log_puntos'] = $puntos;
                    $datLog['log_descripcion'] = $descripcion;
                    $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;
                    #guardamos el log de los puntos ganados por la tienda
                    $modelLog->guardar($datLog);

                    #guardo el LOG del banco VALEPLAZA
                    $this->LogValeplaza($usID, Obj_CpnPuntajes::PREMIO, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);
                    if (!empty($value['nivtie_id'])) {
                        #el trofeo esta relacionado a un nivel
                        #obtenemos los logros conseguidos por la tienda que tienen relacion con el nivel
                        $slctclitro = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_tienda_trofeo'), array('tietro_id'))
                                ->join(array('t2' => 'cpn_trofeos_tie'), 't1.trotie_id=t2.trotie_id', array(''))
                                ->where('t2.nivtie_id =?', $value['nivtie_id'])
                                ->where('t1.tienda_id =?', $tieID);
                        $rsclitro = $this->getAdapter()->fetchAll($slctclitro);
                        #contamos el numero de trofeos
                        $cantTrofeosTie = count($rsclitro);
                        #consultamos si el nivel ha conseguido la cantidad de logros requeridos
                        $slctnivcli = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_niveles_tienda'), array('nivtie_id'))
                                ->where('t1.trofeos_req =?', $cantTrofeosTie)
                                ->where('t1.nivtie_id =?', $value['nivtie_id']);
                        $rsnivcli = $this->getAdapter()->fetchAll($slctnivcli);
                        if (!empty($rsnivcli)) {
                            #la tienda ha logrado conseguir los/el trofeo(s) necesario para subir de nivel
                            $tie = array('nivtie_id' => $value['nivtie_id']);
                            $where = array('tienda_id = ?' => $tieID);
                            $modelTienda->update($tie, $where);
                            #registramos en el log del nuevo nivel al que llego la tienda
                            $log = array(
                                'tienda_id' => $tieID,
                                'nivtie_id' => $value['nivtie_id']
                            );
                            $modelLogNivelTie->guardar($log);
                        }
                    } else {
                        #el trofeo no esta relacionado a un nivel
                    }
                }
            }
        }
    }

    public function autenticar($username, $passwordIng, $fase = 0, $facebookID = null) {
        $filtro = new Zend_Filter_StripTags;
        $username = $filtro->filter($username);
        $passwordIng = $filtro->filter($passwordIng);

        try {
            $auth = Zend_Auth::getInstance();
            $adapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('Server')->getDb()->auth, 'cpn_usuario', 'usuario_email', 'usuario_password');
            $adapter->setIdentity($username);
            $adapter->setCredential($passwordIng);
            $result = $auth->authenticate($adapter);
            if ($result->isValid()) {
                $data = $adapter->getResultRowObject(null, 'clave');
                if ($data->usuario_estado == Obj_CpnUsuario::ESTADO_ACTIVO) {
                    $modelPais = new Model_SysPaises();
                    $logo2 = Cit_Server::getContent()->host . "img/j.jpg";
                    $logo = Cit_Server::getContent()->host . "img/avatar-50x50.png";
                    $portada = Cit_Server::getContent()->host . "img/cabecera.jpg";

                    if ($data->rol_id == Obj_SysRol::ROL_USUARIO) {
                        $modelCliente = new Model_CpnCliente();
                        $modelAgente = new Model_CpnAgente();
                        $cliente = $modelCliente->getIdByUsu($data->usuario_id);
                        if (empty($cliente['cliente_fb_id'])) {
                            $modelCliente->update(array('cliente_fb_id' => $facebookID), array("cliente_id = ?" => $cliente['cliente_id']));
                        }
                        Zend_Registry::get('Susuario')->cliente_id = $cliente['cliente_id'];
                        Zend_Registry::get('Susuario')->nombre = $cliente['nombre']." ".$cliente['apellido'];
                        Zend_Registry::get('Susuario')->nick = $cliente['cliente_nick'];
                        //Zend_Registry::get('Susuario')->puntos = $cliente['cliente_puntos'];
                        Zend_Registry::get('Susuario')->data = $cliente;
                        Zend_Registry::get('Susuario')->pais = $modelPais->getMonedaByCliente($cliente['cliente_id']);
                        if (!empty($cliente['logo'])) {
                            $logo = Cit_Server::getStatic()->host . "imagenes/clientes/" . $cliente['cliente_id'] . "/logo/" . $cliente['logo'];
                            $logo2 = Cit_Server::getStatic()->host . "imagenes/clientes/" . $cliente['cliente_id'] . "/logo/" . $cliente['logo'];
                        }
                        Zend_Registry::get('Susuario')->urlLogo = $logo;
                        Zend_Registry::get('Susuario')->urlLogo2 = $logo2;
                        if (!empty($cliente['portada'])) {
                            $portada = Cit_Server::getStatic()->host . "imagenes/clientes/" . $cliente['cliente_id'] . "/portada/" . $cliente['portada'];
                        }
                        Zend_Registry::get('Susuario')->urlPortada = $portada;
                        $cad='<div class="barra_menuc"><div class="barra_menu"><ul class="ulpl">
            <a href=' . Cit_Server::getContent()->host . "navegar".'><li class="cambiap">
                   <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/casa.png"><span class="menupf">Inicio</span>
            </li></a>
            <a href=' . Cit_Server::getContent()->host . "mapa".'><li class="cambiap">
                   <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/lupa.png"><span class="menupf">Buscar</span>
            </li></a>            
            <a href=' . Cit_Server::getContent()->host . "clubs".'><li class="cambiap">
                  <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/store.png"><span class="menupf">Tiendas</span>
            </li></a>
            
            <a href=' . Cit_Server::getContent()->host . "misvales".'><li class="cambiap">
                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/vale.png"><span class="menupf">Mis vales</span>
            </li></a>
            <a href=' . Cit_Server::getContent()->host . "ordenes".'><li class="cambiap">
                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'logo-menu/pedido.png"><span class="menupf">Mis pedidos</span>
            </li></a>
            <a href=' . Cit_Server::getContent()->host . "mensaje".'><li class="cambiap">
                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/mensaje2.png"><span class="menupf">Mis chats</span>
            </li></a>';
            if($modelAgente->validarAgenteActivo($cliente['cliente_id'])){
                $cad.='<a href=' . Cit_Server::getContent()->host . "agentes".'><li class="cambiap">
                       <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/casa.png"><span class="menupf">Agentes</span>
                </li></a>';
            }
            $cad.='<a href=' . Cit_Server::getContent()->host . "ayuda/cliente" . '><li class="cambiap">
                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/signo.png"><span class="menupf">Ayuda</span>
            </li> </a> 
        </ul>
		
		</div></div>';

            
                        /*cad = "<ul>";

                        $cad.="<li class='li-menu-buscar'><a href='" . Cit_Server::getContent()->host . "navegar'><i class='i-follow'></i><span>Buscador</span></a></li>";
                        //$cad.="<li class='li-menu-inicio'><a href='" . Cit_Server::getContent()->host . "perfil/" . Zend_Registry::get('Susuario')->nick . "'><i class='i-inicio'></i><span>Perfil</span></a></li>";
                        //$cad.="<li class='li-menu-amigos'><a href='" . Cit_Server::getContent()->host . "amigos'><i class='i-amigos'></i><span>Amigos</span></a></li>";
                        $cad.="<li class='li-menu-clubs'><a href='" . Cit_Server::getContent()->host . "clubs'><i class='i-club'></i><span>Tiendas</span></a></li>";
                        $cad.="<li class='li-menu-ofertas'><a href='" . Cit_Server::getContent()->host . "misvales'><i class='i-tag'></i><span>Mis vales</span></a></li>";
                        $cad.="<li class='li-menu-mensajes'><a href='" . Cit_Server::getContent()->host . "mensaje'><i class='i-mens'></i><span>Mensajes</span></a></li>";
                        $cad.="<li class='li-menu-transacciones'><a href='" . Cit_Server::getContent()->host . "transacciones'><i class='i-trans'></i><span>puntos</span></a></li>";
                        //$cad.="<li class='li-menu-perfil'><a href='" . Cit_Server::getContent()->host . "'><i class='i-perf'></i><span>Niveles</span></a></li>";
                        $cad.="<li class='li-ayuda'><a href='" . Cit_Server::getContent()->host . "ayuda/cliente'><i class='i-ayuda'></i><span>Ayuda</span></a></li></ul>";*/
                    } else {
                        $modelTienda = new Model_CpnTiendas();

                        $tienda = $modelTienda->getIdByUsu($data->usuario_id, 1);
                        Zend_Registry::get('Susuario')->tienda_id = $tienda['tienda_id'];
                        Zend_Registry::get('Susuario')->nick = $tienda['tienda_nick'];
                        //Zend_Registry::get('Susuario')->puntos = $tienda['tienda_puntos'];
                        Zend_Registry::get('Susuario')->rubro = $tienda['rubro_id'];
                        Zend_Registry::get('Susuario')->data = $tienda;

                        Zend_Registry::get('Susuario')->pais = $modelPais->getMonedaByTienda($tienda['tienda_id']);
                        Zend_Registry::get('Susuario')->tienda_completar = $tienda['tienda_completar'];
                        if (!empty($tienda['logo'])) {
                            $logo = Cit_Server::getStatic()->host . "imagenes/empresas/" . $tienda['tienda_id'] . "/logo/" . $tienda['logo'];
                            $logo2 = Cit_Server::getStatic()->host . "imagenes/empresas/" . $tienda['tienda_id'] . "/logo/" . $tienda['logo'];
                        }
                        Zend_Registry::get('Susuario')->urlLogo = $logo;
                        Zend_Registry::get('Susuario')->urlLogo2 = $logo2;
                        if (!empty($tienda['portada'])) {
                            $portada = Cit_Server::getStatic()->host . "imagenes/empresas/" . $tienda['tienda_id'] . "/portada/" . $tienda['portada'];
                        }
                        Zend_Registry::get('Susuario')->urlPortada = $portada;
                        if ($data->rol_id == Obj_SysRol::ROL_ADMIN) {

                            $cad = "<div class='barra_menuc'><div class='barra_menu'><ul class='ulpl'>                                
                                <li class='li-menu-fisica'><a href='" . Cit_Server::getContent()->host . "banco'><i class='i-house' ></i><span>Banco</span></a></li>"
                                ."<li class='li-menu-fisica'><a href='" . Cit_Server::getContent()->host . "admdepositos'><i class='i-house' ></i><span>Depositos</span></a></li>";
                            //$cad.="<li class='li-menu-virtual'><a href='" . Cit_Server::getContent()->host . "variables'><i class='i-store' ></i><span>variables</span></a></li>";
                            $cad.="<li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "usuarios'><i class='i-books'></i><span>Clientes</span></a></li>
                                <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "usuariostiendas'><i class='i-books'></i><span>Tiendas</span></a></li>
                                <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "admproductos'><i class='i-books'></i><span>Productos</span></a></li>
                                <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "admvales'><i class='i-books'></i><span>Vales</span></a></li>";
                            $cad.="<li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "admempresas'><i class='i-books'></i><span>Empresas</span></a></li>";
                            //$cad.="<li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "adminvitados'><i class='i-books'></i><span>Invitados</span></a></li>";
                            //$cad.="<li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "regvirtuales'><i class='i-books'></i><span>Registro de Virtuales</span></a></li>";
                            $cad.="<li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "chatadm'><i class='i-books'></i><span>Chat soporte</span></a></li>
                                <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "matricula'><i class='i-books'></i><span>Matricular tienda</span></a></li>
                                <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "admpagos'><i class='i-books'></i><span>Pagos</span></a></li>
                                <li class='li-reportes'><a href='" . Cit_Server::getContent()->host . "reportes'><i class='i-books'></i><span>Reportes</span></a></li>
                                <li class='li-ayuda'><a href='" . Cit_Server::getContent()->host . "ayuda/tienda'><i class='i-ayuda'></i><span>Ayuda</span></a></li></ul></div></div>";
                        } else if ($data->rol_id == Obj_SysRol::ROL_ADMIN_TIENDA) {
                            #html del menu para tienda
                            $cad='<div class="barra_menuc"><div class="barra_menu"><ul class="ulpl">
                                <a href="' . Cit_Server::getContent()->host . 'perfil/'.$tienda['tienda_nick'].'"><li class="cambiap">
                                    <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/store.png"><span class="menupf">Perfil</span>
                                </li></a>
                               <a href=' . Cit_Server::getContent()->host . "vales/misvales".'><li class="cambiap">
                                    <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/vale.png"><span class="menupf">Vales</span>
                                </li></a>    
                                <a href=' . Cit_Server::getContent()->host . "catalogo".'><li class="cambiap">
                                    <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/catalogo.png"><span class="menupf">Catálogo</span>
                                </li></a>
                                <a href=' . Cit_Server::getContent()->host . "banner".'><li class="cambiap">
                                    <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/banner.png"><span class="menupf">Banners</span>
                                </li></a>
                                <a href=' . Cit_Server::getContent()->host . "locales".'><li class="cambiap">
                                    <img  class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/ubicacion.png"><span class="menupf">Locales</span>
                                </li></a> 
                                <a href=' . Cit_Server::getContent()->host . "mensaje".'><li class="cambiap">
                                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/mensaje2.png"><span class="menupf">Mis chats</span>
                                 </li></a> 
                                <a href=' . Cit_Server::getContent()->host . "canjeo".'><li class="cambiap">
                                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/vale.png"><span class="menupf">Canjear Vales</span>
                                 </li></a>';                                                             
                               $cad.='<a href=' . Cit_Server::getContent()->host . "socios" . '><li class="cambiap">
                                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/socio.png"><span class="menupf">Socios</span>
                                  </li> </a>';
                               $cad.='<a href=' . Cit_Server::getContent()->host . "ayuda/cliente" . '><li class="cambiap">
                                    <img class="iconospf" src="'.Cit_Server::getContent()->host.'headerfinal/signo.png"><span class="menupf">Ayuda</span>
                                  </li> </a>';
            
                                $cad.=' </ul></div></div>';
                            /* <a target="_blank" href="<?php echo Cit_Server::getContent()->host;?>locales/edit/id/<?php echo $value['idcpn_direcciones']?>"> */
                           /* $cad = "<ul>";
                            //$cad.="<li class='li-menu-inicio'><a href='" . Cit_Server::getContent()->host . "perfil/" . Zend_Registry::get('Susuario')->nick . "'><i class='i-home'></i><span>Perfil</span></a></li>";
                            $cad.="<li class='li-menu-fisica'><a href='" . Cit_Server::getContent()->host . "locales'><i class='i-local' ></i><span>Locales</span></a></li>
                                <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "catalogo'><i class='i-cata'></i><span>Catálogo</span></a></li>";
                            /* if (!empty($fase)) $cad.="<li class='li-menu-ofertas'><a href='" . Cit_Server::getContent()->host . "vales/misvales'><i class='i-tag'></i><span>Vales</span></a></li>";
                            //$cad.="<li class='li-menu-seguidores'><a href='" . Cit_Server::getContent()->host . "socios'><i class='i-amigos'></i><span>Socios</span></a></li>";
                            //if(!empty($fase)) 
                          /*  $cad.="<li class='li-menu-mensajes'><a href='" . Cit_Server::getContent()->host . "mensaje'><i class='i-mens'></i><span>Mensajes</span></a></li>";

                            $cad.="<li class='li-menu-estadisticas'><a href='" . Cit_Server::getContent()->host . "estadisticas'><i class='i-est'></i><span>Estadísticas</span></a></li>";
                            $cad.="<li class='li-menu-canjeo'><a href='" . Cit_Server::getContent()->host . "canjeo'><i class='i-canje'></i><span>Canjear Vales</span></a></li>";
                            $cad.="<li class='li-menu-transacciones'><a href='" . Cit_Server::getContent()->host . "transacciones'><i class='i-trans'></i><span>puntos</span></a></li>
                                   <li class='li-menu-perfil'><a href='" . Cit_Server::getContent()->host . "'><i class='i-perf'></i><span>Niveles</span></a></li>
                                    <li class='li-ayuda'><a href='" . Cit_Server::getContent()->host . "ayuda/tienda'><i class='i-ayuda'></i><span>Ayuda</span></a></li></ul>";*/
                        } else if ($data->rol_id == Obj_SysRol::ROL_MODERADOR_TIENDA) {
                            Zend_Registry::get('Susuario')->tus_local = $tienda['tus_local'];
                            $cad = "<ul>
                                <li class='li-menu-canjeos'><a href='" . Cit_Server::getContent()->host . "canjeo'><i class='i-home'></i><span>Canje</span></a></li></ul>";
                        } else if ($data->rol_id == Obj_SysRol::ROL_MARKET) {
                            $cad = "<ul>
                                        <li class='li-menu-canjeos'><a href='" . Cit_Server::getContent()->host . "'><i class='i-home'></i><span>Market</span></a></li>
                                        <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "chatadm'><i class='i-books'></i><span>Chat soporte</span></a></li>
                                    </ul>";
                        }else if($data->rol_id = Obj_SysRol::ROL_ADMIN_TIENDA_CORPORATIVO){
                            $cad = "<ul>
                                        <li class='li-menu-canjeos'><a href='" . Cit_Server::getContent()->host . "'><i class='i-home'></i><span>Market</span></a></li>
                                        <li class='li-menu-producto'><a href='" . Cit_Server::getContent()->host . "chatadm'><i class='i-books'></i><span>Chat soporte</span></a></li>
                                    </ul>";
                        }
                    }
                    Zend_Registry::get('Susuario')->usuario = $data;
                    Zend_Registry::get('Susuario')->usID = $data->usuario_id;
                    Zend_Registry::get('Susuario')->rolID = $data->rol_id;
                    Zend_Registry::get('Susuario')->status = true;
                    Zend_Registry::get('Susuario')->menu = $cad;
                } else {
                    return false;
                }
                return true;
            } else {
                $auth->clearIdentity();
                return false;
            }
        } catch (Exception $e) {
            throw new Zend_Db_Statement_Exception($e->getMessage());
            return false;
        }
    }

    public function _validaForm($params) {
        #funcion para validar los datos de logueo
        $mesaje = array();
        $usuarioVal = new Zend_Validate();
        $usuarioVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());

        $passwordVal = new Zend_Validate();
        $passwordVal->addValidator(new Zend_Validate_NotEmpty());
        $usuarioVal->isValid($params['email']);
        $passwordVal->isValid($params['password']);
        $password = $passwordVal->getMessages();
        $msgeUser = $usuarioVal->getMessages();
        if (!empty($msgeUser))
            $mesaje['mail'] = 'Correo sin formato';
        if (!empty($password))
            $mesaje['mail'] = 'contraseña sin formato';

        if ($usuarioVal->isValid($params['email'])) {
            //$modelUsuario = new Model_CpnUsuario();
            $usdata = $this->getByEmail($params['email']);
            if (empty($usdata)) {
                $mesaje['mail'] = 'El correo no existe';
            } else {
                if ($passwordVal->isValid($params['password'])) {
                    $usdata = $this->verifyUsuario($params['email'], md5($params['password']));
                    if (empty($usdata)) {
                        $mesaje['mail'] = 'Contraseña inválida';
                    }
                }
            }
        }

        if (empty($mesaje)) {
            $mesaje = array();
        }
        return $mesaje;
    }

    public function _validaFormFB($params) {
        #funcion para validar los datos de logueo
        $mesaje = array();
        $usuarioVal = new Zend_Validate();
        $usuarioVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());

        /* $passwordVal = new Zend_Validate();
          $passwordVal->addValidator(new Zend_Validate_NotEmpty()); */
        //$usuarioVal->isValid($params['email']);
        //$passwordVal->isValid($params['password']);
        //$password = $passwordVal->getMessages();
        /* $msgeUser = $usuarioVal->getMessages();
          if (!empty($msgeUser))
          $mesaje['mail_format'] = 'Correo sin formato'; */
        /* if (!empty($password))
          $mesaje['contraseña'] = 'contraseña sin formato'; */

        //if ($usuarioVal->isValid($params['id'])) {
        //$modelUsuario = new Model_CpnUsuario();
        $usdata = $this->getByfacebookID($params['id']);
        //var_dump($usdata);die;
        if (empty($usdata)) {
            $mesaje['mail'] = 'No existe el ID';
        } else {
            /* if ($passwordVal->isValid($params['password'])) {
              $usdata = $this->verifyUsuario($params['email'],md5($params['password']));
              if (empty($usdata)) {
              $mesaje['mail'] = 'Contraseña invalida';
              }
              } */
        }
        //}

        if (empty($mesaje)) {
            $mesaje = array();
        }
        return $mesaje;
    }

    public function _validaFormFB2($params) {
        #funcion para validar los datos de logueo
        $mesaje = array();
        $usuarioVal = new Zend_Validate();
        $usuarioVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());

        /* $passwordVal = new Zend_Validate();
          $passwordVal->addValidator(new Zend_Validate_NotEmpty()); */
        //$usuarioVal->isValid($params['email']);
        //$passwordVal->isValid($params['password']);
        //$password = $passwordVal->getMessages();
        /* $msgeUser = $usuarioVal->getMessages();
          if (!empty($msgeUser))
          $mesaje['mail_format'] = 'Correo sin formato'; */
        /* if (!empty($password))
          $mesaje['contraseña'] = 'contraseña sin formato'; */

        //if ($usuarioVal->isValid($params['id'])) {
        //$modelUsuario = new Model_CpnUsuario();
        //echo $params['email_usu'];die;
        $usdata = $this->getByEmail($params['email_usu']);
        //var_dump($usdata);die;
        if (empty($usdata)) {
            $mesaje['mail'] = 'No existe el correo';
        } else {
            /* if ($passwordVal->isValid($params['password'])) {
              $usdata = $this->verifyUsuario($params['email'],md5($params['password']));
              if (empty($usdata)) {
              $mesaje['mail'] = 'Contraseña invalida';
              }
              } */
        }
        //}

        if (empty($mesaje)) {
            $mesaje = array();
        }
        return $mesaje;
    }

    public function _validaFormRegistro($params) {
        $mesaje = array();
        #nombres
        $nombreVal = new Zend_Validate();
        $nombreVal->addValidator(new Zend_Validate_NotEmpty());
        $nombreVal->isValid($params['nombres']);
        $msgeNombre = $nombreVal->getMessages();
        if (!empty($msgeNombre))
            $mesaje['Nombres'] = $msgeNombre;
        #apellidos
        $apellidosVal = new Zend_Validate();
        $apellidosVal->addValidator(new Zend_Validate_NotEmpty());
        $apellidosVal->isValid($params['apellidos']);
        $msgeApellidos = $apellidosVal->getMessages();
        if (!empty($msgeApellidos))
            $mesaje['Apellidos'] = $msgeApellidos;

        #correo
        $usuarioVal = new Zend_Validate();
        $usuarioVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());
        $usuarioVal->isValid($params['email']);
        $msgeUser = $usuarioVal->getMessages();
        if (!empty($msgeUser))
            $mesaje['Email'] = $msgeUser;

        #password
        $passwordVal = new Zend_Validate();
        $passwordVal->addValidator(new Zend_Validate_NotEmpty());
        $passwordVal->isValid($params['password']);
        $msgePassword = $passwordVal->getMessages();
        if (!empty($msgePassword))
            $mesaje['Contraseña'] = $msgePassword;

        #confirmacion
        $confirmacionVal = new Zend_Validate();
        $confirmacionVal->addValidator(new Zend_Validate_NotEmpty());
        $confirmacionVal->isValid($params['confirmpassword']);
        $msgeConfirm = $confirmacionVal->getMessages();


        if ($params['password'] != $params['confirmpassword']) {
            $mesaje['Confirmacion Contraseña'][] = 'Las contraseñas no son iguales.';
        }
        if ($usuarioVal->isValid($params['email'])) {
            $modelUsuario = new Model_CpnUsuario();
            $usdata = $modelUsuario->getByEmail($params['email']);
            if (!empty($usdata)) {
                $mesaje['Email']['mail'] = 'La cuenta de email ya esta registrada.';
            }
        }
        if (!empty($msgeConfirm))
            $mesaje['Confirmacion Contraseña'] = $msgeConfirm;

        if (empty($mesaje)) {
            $mesaje = array();
        }
        return $mesaje;
    }

    public function _validaFormRegistroIntriga($params) {
        $mesaje = array();
        #nombres
        $nombreVal = new Zend_Validate();
        $nombreVal->addValidator(new Zend_Validate_NotEmpty());
        $nombreVal->isValid($params['nom_usu']);
        $msgeNombre = $nombreVal->getMessages();
        if (!empty($msgeNombre))
            $mesaje['nombre'] = 'Igrese nombre';

        #apellidos
        $apeVal = new Zend_Validate();
        $apeVal->addValidator(new Zend_Validate_NotEmpty());
        $apeVal->isValid($params['ape_usu']);
        $msgeApe = $apeVal->getMessages();
        if (!empty($msgeApe))
            $mesaje['apellidos'] = 'Ingrese apellidos';

        #correo
        $usuarioVal = new Zend_Validate();
        $usuarioVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());
        $usuarioVal->isValid($params['email_usu']);
        $msgeUser = $usuarioVal->getMessages();
        if (!empty($msgeUser))
            $mesaje['email'] = 'ingresar email';

        if ($usuarioVal->isValid($params['email_usu'])) {
            $modelUsuario = new Model_CpnUsuario();
            $usdata = $modelUsuario->getByEmail($params['email_usu']);
            if (!empty($usdata)) {
                $mesaje['mail_status'] = 'La cuenta de email ya esta registrada.';
            }
        }

        if (empty($mesaje)) {
            $mesaje = array();
        }
        return $mesaje;
    }

    public function getByfacebookID($id) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_cliente'))
                ->join(array('t2' => 'cpn_usuario'), 't1.usuario_id=t2.usuario_id', array('usuario_email', 'usuario_password'))
                ->where('cliente_fb_id = ?', $id);
        $result = $this->getAdapter()->fetchRow($select);
        return $result;
    }

    public function verifyUserEspera($codigo, $rol = Obj_SysRol::ROL_USUARIO) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'usuario_email', 'usuario_password'))
                ->where('usuario_codigo = ?', $codigo)
                ->where('usuario_estado = ?', Obj_CpnUsuario::ESTADO_ESPERA)
                ->where('rol_id = ?', $rol);
        $arrCodigo = $this->getAdapter()->fetchRow($select);
        if (!empty($arrCodigo)) {
            return array('id' => $arrCodigo['usuario_id'], 'responce' => TRUE);
        } else {
            return array('responce' => FALSE);
        }
    }

    public function obtenerCodigo() {
        $codigo = md5(uniqid("", true));
        return $codigo;
    }

    public function LogValeplaza($usuarioID, $tip_mod, $puntos, $tip_beneficio) {
        $modelBancoMovimiento = new Model_CpnBancoMovimiento();
        $modelBanco = new Model_CpnBanco();
        $datLog = array();
        $datLog['banco_id'] = Obj_CpnBanco::BANCO_ID;
        $datLog['usuario_id'] = $usuarioID;
        if ($tip_beneficio == Obj_CpnLogPuntos::TIPO_GANA) {
            $datLog['banmov_puntos_ingreso'] = $puntos;
        } elseif ($tip_beneficio == Obj_CpnLogPuntos::TIPO_PIERDE) {
            $datLog['banmov_puntos_egreso'] = $puntos;
        }
        $datLog['banmov_motivo'] = $tip_mod;
        $datLog['banmov_fecha'] = Zend_Date::now()->toString('Y-m-d H:i:s');
        ;
        #guardar log de registro de puntos de la empresa por registrarse
        $modelBancoMovimiento->guardar($datLog);

        $modelBanco->actualizarSaldo($puntos, $tip_beneficio);
    }

    public function PasswordAleatorio($id) {
        $new = substr(md5(rand(100001, 99000)), 0, 6);
        $dat = array(
            'usuario_password' => md5($new)
        );
        $where = array('usuario_id =?' => $id);
        $this->update($dat, $where);
        return $new;
    }

    public function verifyInvitado($email) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_invitados'), array('invitado_id'))
                ->where("invitado_email = '" . $email . "'");
        $result = $this->getAdapter()->fetchRow($select);
        return $result;
    }

    public function verifyUsuario($email, $pass) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_email = ?', $email)
                ->where('usuario_password = ?', $pass);
        return $this->getAdapter()->fetchRow($select);
    }

    public function getPasswordByEmail($email) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_email = ?', $email);
        $result = $this->getAdapter()->fetchRow($select);
        return $result['usuario_password'];
    }

    public function getPuntajeByID($usID) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array(''))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_puntos as puntos'))
                ->where('t1.usuario_id = ?', $usID);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name), array(''))
                    ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array(''))
                    ->join(array('t3' => 'cpn_tiendas'), 't2.tienda_id=t3.tienda_id', array('tienda_puntos as puntos'))
                    ->where('t1.usuario_id = ?', $usID);
            $result = $this->getAdapter()->fetchRow($select);
            //return $result['tienda_puntos'];
        } else {

            //return $result['cliente_puntos'];
        }
        return $result['puntos'];
    }

    public function verifyEmail($email, $usID) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id'))
                ->where("usuario_email = '" . $email . "'")
                ->where("usuario_id <> '" . $usID . "'");
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result))
            return true;
        else
            return false;
        /* if (!empty($result)) {
          if (in_array($usID, $result)) {
          return true;
          } else {
          return false;
          }
          } else {
          return true;
          } */
    }

    public function verifyIdentidad($dni, $usID) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array(''))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_identidad'))
                ->where("cliente_identidad = '" . $dni . "'")
                ->where("t1.usuario_id <> '" . $usID . "'");
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result))
            return true;
        else
            return false;
    }

    public function buscadoramx($like = null, $pais_id, $all = 0, $usID = 0) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array("cliente_id", "concat(cliente_nombre,' ',cliente_apellido) as nom1", "cliente_nick as nick1", "cliente_logo as logo1"))
                ->join(array('t3' => "cpn_niveles_cliente"), 't2.nivcli_id=t3.nivcli_id', array('nivcli_clase', 'nivcli_titulo'))
                ->joinleft(array('t5' => 'sys_provincia'), 't2.cliente_ciudad=t5.prov_id', array("prov_nombre as ciudad1"))
                ->join(array('t6' => 'sys_paises'), 't1.usuario_pais=t6.pais_id', array('pais_nombre as ciudad2'))
                ->where('t1.rol_id in (6)');
        if (!empty($like))
            $select->where("concat(cliente_nombre,' ',cliente_apellido) like " . $this->getAdapter()->quote("%" . $like . "%"));
        $select->where('t1.usuario_estado= ?', Obj_CpnUsuario::ESTADO_ACTIVO);
        if (empty($all)) {
            //$select->where('t6.pais_id=?',$pais_id);
            //$select->limit('3');
        }
        $result = $this->getAdapter()->fetchAll($select);

        if (!empty($all)) {
            foreach ($result as $key => $value) {
                $select2 = $this->getAdapter()->select()
                        ->from(array('t1' => 'cpn_amistad'), array('amistad_estado'))
                        ->where('t1.usuario_id=' . $value['usuario_id'] . ' and t1.usuario_id_id=' . $usID);

                $result2 = $this->getAdapter()->fetchRow($select2);

                $slctLogros = $this->getAdapter()->select()
                        ->from(array('t1' => 'cpn_cliente_trofeo'))
                        ->join(array('t2' => 'cpn_trofeos'), 't1.trofeo_id=t2.trofeo_id')
                        ->where('cliente_id = ?', $value['cliente_id'])
                        ->order('clitro_registro desc')
                        ->limit('4');

                $rsLogros = $this->getAdapter()->fetchAll($slctLogros);
                if (!empty(Zend_Registry::get('Susuario')->cliente_id)) {
                    if ($value['usuario_id'] == $usID) {
                        $result[$key]['estado_rel'] = -1;
                    } else {
                        if (!empty($result2)) {
                            if ($result2['amistad_estado'] == 2) {
                                $result[$key]['estado_rel'] = 2;
                            } else {
                                $result[$key]['estado_rel'] = 1;
                            }
                        } else {
                            $result[$key]['estado_rel'] = 0;
                        }
                    }
                } else {
                    $result[$key]['estado_rel'] = -2;
                }
                $result[$key]['logros'] = $rsLogros;
            }
        }
        return $result;
    }

    public function buscadortiendas($like, $pais_id, $all = 0, $cliID = 0) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id'))
                // ->joinleft(array('t2'=>'cpn_cliente'),'t1.usuario_id=t2.usuario_id',array("cliente_id","concat(cliente_nombre,' ',cliente_apellido) as nom1","cliente_nick as nick1","cliente_logo as logo1"))
                //->joinleft(array('t5'=>'sys_provincia'),'t2.cliente_ciudad=t5.prov_id',array("prov_nombre as ciudad1"))
                ->join(array('t3' => 'cpn_tienda_usuario'), 't1.usuario_id=t3.usuario_id', array(''))
                ->join(array('t4' => 'cpn_tiendas'), 't3.tienda_id=t4.tienda_id', array('tienda_id', 'tienda_nombre as nom2', 'tienda_nick as nick2', "tienda_logo as logo2", "tienda_url_logo as logo3", "tienda_tipo"))
                ->join(array('t5' => "cpn_niveles_tienda"), 't4.nivtie_id=t5.nivtie_id', array('nivtie_clase', 'nivtie_titulo'))
                ->join(array('t6' => 'sys_paises'), 't1.usuario_pais=t6.pais_id', array('pais_id', 'pais_nombre as ciudad2'))
                ->join(array('t7' => 'cpn_rubro'), 't4.rubro_id=t7.rubro_id', array('rubro_nombre'))
                ->where('t1.rol_id in (1,3)')
                ->where("tienda_nombre like " . $this->getAdapter()->quote("%" . $like . "%"))
                ->where('t1.usuario_estado= ?', Obj_CpnUsuario::ESTADO_ACTIVO)
                ->where('t4.tienda_flag = ?', Obj_CpnTiendas::ESTADO_VISIBLE)
                ->where('t4.tienda_tipo = ?', Obj_CpnTiendas::TIENDA_REGISTRADO)
                ->where('t6.pais_id = ?', $pais_id);
                //->where('t6.pais_id = ?', Obj_SysPaises::PAIS_DEFAULT);
        if (empty($all)) {
            //$select->where('t6.pais_id=?',$pais_id);
            //$select->order('pais_id Desc')->where('t6.pais_id =?',$pais_id);
            $select->order("FIELD(t6.pais_id, " . $pais_id . ") DESC");

            $select->limit('3');
        }
        //echo $select;die;
        $result = $this->getAdapter()->fetchAll($select);

        if (!empty($all)) {
            foreach ($result as $key => $value) {
                $select2 = $this->getAdapter()->select()
                        ->from(array('t1' => 'cpn_tienda_seguidores'))
                        ->where('t1.tienda_id=' . $value['tienda_id'] . ' and t1.cliente_id=' . $cliID);
                $result2 = $this->getAdapter()->fetchRow($select2);

                $slctLogros = $this->getAdapter()->select()
                        ->from(array('t1' => 'cpn_tienda_trofeo'))
                        ->join(array('t2' => 'cpn_trofeos_tie'), 't1.trotie_id=t2.trotie_id')
                        ->where('tienda_id = ?', $value['tienda_id'])
                        ->order('tietro_registro desc')
                        ->limit('4');

                $rsLogros = $this->getAdapter()->fetchAll($slctLogros);
                $result[$key]['logros'] = $rsLogros;
                /* if($value['usuario_id']==$usID){
                  $result[$key]['estado_rel']=-1;
                  }else{ */
                if (!empty($result2)) {
                    $result[$key]['estado_rel'] = 1;
                } else {
                    $result[$key]['estado_rel'] = 0;
                }
                //}
            }
        }
        return $result;
    }

    public function getUsuarioADMofLocal($iddireccion) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'usuario_email'))
                ->join(array('t2' => 'cpn_tienda_usuario'), 't1.usuario_id=t2.usuario_id', array(''))
                ->where('t1.rol_id =?', Obj_SysRol::ROL_MODERADOR_TIENDA)
                ->where('t2.tus_local =?', $iddireccion);
        //echo $select;die;
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }

    public function getmodbyID($id) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'usuario_email'))
                ->join(array('t2' => 'cpn_moderadores'), 't1.usuario_id=t2.usuario_id', array('mod_nombre', 'mod_apellido', 'mod_estado'))
                ->where('t1.rol_id =?', Obj_SysRol::ROL_MODERADOR_TIENDA)
                ->where('t1.usuario_id =?', $id);
        //echo $select;die;
        $result = $this->getAdapter()->fetchRow($select);
        return $result;
    }

    ###################################################################### WEB SERVICE

    public function wsVerifyUsuario($email, $pass,$tipo) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('rol_id AS rol','usuario_codigo as token'))
                ->where('usuario_email = ?', $email);
                if($tipo=="normal"){$select->where('usuario_password = ?', $pass);}
                $select->where('usuario_estado= ?',Obj_CpnUsuario::ESTADO_ACTIVO);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $request = FALSE;
        } else {
            $request = TRUE;
        }
        return array('request' => $request, 'data' => $result);
    }

    public function wsGetByEmail($email) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id'))
                ->where('t1.usuario_email = ?', $email);
        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select2 = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_invitados'), array('invitado_id'))
                    ->where('t1.invitado_email = ?', $email);
            $result = $this->getAdapter()->fetchRow($select2);
            return $result;
        } else {
            return $result;
        }
    }

    public function wsGetByEmailPassword($correo) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id'))
                ->where('t1.usuario_email = ?', $correo)
                ->where('t1.usuario_estado =?', Obj_CpnUsuario::ESTADO_ACTIVO);
        $result = $this->getAdapter()->fetchRow($select);
        return $result;
    }

    public function wsGetByEmailInvitado($email) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_email = ?', $email);


        $result = $this->getAdapter()->fetchRow($select);
        if (empty($result)) {
            $select2 = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_usuario_invitados'))
                    ->where('usuinv_correo = ?', $email);
            $result = $this->getAdapter()->fetchRow($select2);
            if (empty($result)) {
                $select2 = $this->getAdapter()->select()
                        ->from(array('t1' => 'cpn_invitados'))
                        ->where('invitado_email = ?', $email);
                $result = $this->getAdapter()->fetchRow($select2);
                return $result;
            } else {
                return $result;
            }
        } else {
            return $result;
        }
    }

    public function wsValidaForm($params) {
        #funcion para validar los datos de logueo
        //echo $params['tipo'];die;
        $mesaje = array();
        $usuarioVal = new Zend_Validate();
        $usuarioVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());

        $passwordVal = new Zend_Validate();
        $passwordVal->addValidator(new Zend_Validate_NotEmpty());
        if ($usuarioVal->isValid(urldecode($params['email']))) {
            $usdata = $this->wsGetByEmail(urldecode($params['email']));
            if (empty($usdata)) {
                $mesaje = 'El correo no existe';
            } else {
                if($params['tipo']=="normal"){
                    if ($passwordVal->isValid($params['pass'])) {
                        $usdata = $this->wsVerifyUsuario(urldecode($params['email']), md5($params['pass']),$params['tipo']);
                        if (!$usdata['request']) {
                            $mesaje = 'Contrasenia invalida';
                        }
                    } else {
                        $mesaje = 'contrasenia sin formato';
                    }
                }else{
                    //echo $params['tipo'];die;
                    $usdata = $this->wsVerifyUsuario(urldecode($params['email']), md5($params['pass']),$params['tipo']);
                    if (!$usdata['request']) {
                            $mesaje = 'Error de autenticacion';
                        }
                    
                }
            }
        } else {
            $mesaje = 'Correo sin formato';
        }

        if (empty($mesaje)) {
            return array('mensaje' => $mesaje, 'data' => $usdata['data']);
        } else {
            return array('mensaje' => $mesaje, 'data' => array());
        }
    }

    public function wsValidaFormRegistroCliente($params) {
        //var_dump($params);die;
        $mesaje = array();
        #nombres
        $nombreVal = new Zend_Validate();
        $nombreVal->addValidator(new Zend_Validate_NotEmpty());
        $nombreVal->isValid($params['nombre']);
        $msgeNombre = $nombreVal->getMessages();
        if (!empty($msgeNombre))
            $mesaje = 'Ingrese nombre';

        #apellidos
        $apeVal = new Zend_Validate();
        $apeVal->addValidator(new Zend_Validate_NotEmpty());
        $apeVal->isValid($params['apellido']);
        $msgeApe = $apeVal->getMessages();
        if (!empty($msgeApe))
            $mesaje = 'Ingrese apellidos';

        #correo
        $correoVal = new Zend_Validate();
        $correoVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());
        if ($correoVal->isValid($params['correo'])) {
            $usdata = $this->wsGetByEmailInvitado($params['correo']);
            //var_dump($usdata);die;
            if (!empty($usdata)) {
                $mesaje = 'La cuenta de email ya esta registrada.';
            }
        } else {
            $mesaje = 'ingrese un correo adecuado';
        }
        return $mesaje;
        /* if (empty($mesaje)) {
          return array('mensaje'=>$mesaje,'data'=>$usdata['data']);
          }else{
          return array('mensaje'=>$mesaje,'data'=>array());
          } */
    }

    public function wsValidaFormRegistroInvitado($params) {
        $mesaje = array();
        #nombres
        $nombreVal = new Zend_Validate();
        $nombreVal->addValidator(new Zend_Validate_NotEmpty());
        $nombreVal->isValid($params['nombre']);
        $msgeNombre = $nombreVal->getMessages();
        if (!empty($msgeNombre))
            $mesaje = 'Ingrese nombre';
        #correo
        $correoVal = new Zend_Validate();
        $correoVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());
        if ($correoVal->isValid($params['correo'])) {
            $usdata = $this->wsGetByEmail($params['correo']);
            if (!empty($usdata)) {
                $mesaje = 'La cuenta de email ya esta registrada.';
            }
        } else {
            $mesaje = 'ingrese un correo adecuado';
        }
        return $mesaje;
        /* if (empty($mesaje)) {
          return array('mensaje'=>$mesaje,'data'=>$usdata['data']);
          }else{
          return array('mensaje'=>$mesaje,'data'=>array());
          } */
    }

    public function wsValidaFormRecuperarPassword($params) {
        $mesaje = array();

        #correo
        $correoVal = new Zend_Validate();
        $correoVal->addValidator(new Zend_Validate_NotEmpty())
                ->addValidator(new Zend_Validate_EmailAddress());
        if ($correoVal->isValid($params['correo'])) {
            $usdata = $this->wsGetByEmailPassword($params['correo']);
            if (empty($usdata)) {
                $mesaje = 'No existe el correo';
            }
        } else {
            $mesaje = 'ingrese un correo adecuado';
        }
        return $mesaje;
    }

    public function wsValidaFiltrosTienda($params) {
        $mesaje = array();

        #id de categoria
        $categoriaIDVal = new Zend_Validate();
        $categoriaIDVal->addValidator(new Zend_Validate_Digits());

        #ipalabra clave
        $likeVal = new Zend_Validate();
        $likeVal->addValidator(new Zend_Validate_Alnum());
        if (!$categoriaIDVal->isValid($params['categoria']) || !$likeVal->isValid($params['like'])) {
            $mesaje = 'parametros invalidos';
        }
        return $mesaje;
    }

    public function wsConfirmar($codigo, $rol = Obj_SysRol::ROL_USUARIO, $pass = null) {
        $modelLog = new Model_CpnLogPuntos();
        $modelPuntaje = new Model_CpnPuntajes();
        $modelCliente = new Model_CpnCliente();
        $modelAmistad= new Model_CpnAmistad();
        $modelTienda=new Model_CpnTiendas();
        $modelSeguidor=new Model_CpnTiendaSeguidores();
        //$modelClienteTrofeos = new Model_CpnClienteTrofeos();
        #obtengo datos del usuario segun codigo de confirmacion
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'usuario_email', 'usuario_password'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id','cliente_id_referer','cliente_nombre'))
                ->where('usuario_codigo = ?', $codigo)
                ->where('usuario_estado = ?', Obj_CpnUsuario::ESTADO_ESPERA)
                ->where('rol_id = ?', $rol);
        $arrCodigo = $this->getAdapter()->fetchRow($select);

        if (!empty($pass) && !empty($arrCodigo)) {
            #actualizo el password del usuario
            $datusu = array(
                'usuario_password' => md5($pass),
                'usuario_estado' => Obj_CpnUsuario::ESTADO_ACTIVO
            );
            $where = array('usuario_id = ?' => $arrCodigo['usuario_id']);
            $this->update($datusu, $where);

            $codigoCliente = $modelCliente->obtenerCodigoCliente();
            $dat['cliente_codigo'] = $codigoCliente;
            $where2 = array('cliente_id =?' => $arrCodigo['cliente_id']);
            $modelCliente->update($dat, $where2);


            # se le da puntos al cliente por registrarse
            $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::REGISTRO_ID);
            $descripcion = "valeplaza te regala puntos por registrarte";
            $datLog = array(
                'usuario_id' => $arrCodigo['usuario_id'],
                'usuario_id_id' => null,
                'usuario_id_causante' => null,
                'pnt_id' => Obj_CpnPuntajes::REGISTRO_ID,
                'log_puntos' => $puntos,
                'log_descripcion' => $descripcion,
                'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
            );

            #el cliente referido gana puntos tipo referer
            $modelLog->guardar($datLog);

            #VALEPLAZA pierde puntos
            $this->LogValeplaza($arrCodigo['usuario_id'], Obj_CpnPuntajes::REGISTRO_ID, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

            /*$oldpuntos = $modelCliente->getPuntajeById($arrCodigo['cliente_id']);
            $newpuntos = $oldpuntos + $puntos;
            $cli = array('cliente_puntos' => $newpuntos);*/
            $datcli = array(
                            'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                        );
            $where3 = array('usuario_id = ?' => $arrCodigo['usuario_id']);

            #actualizamos los puntos del cliente referido
            $modelCliente->update($datcli, $where3);
            ########################################################################################
                if (!empty($arrCodigo['cliente_id_referer'])) {
                    #si existe el cliente se registro bajo la referencia de un usuario tipo "cliente" o "tienda"
                    $usuario = $this->getDataByID($arrCodigo['cliente_id_referer']);
                    $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::REFERER);
                    #LE DAMOS PUNTOS AL CLIENTE POR SER INVITADO DE OTRO USUARIO
                    $datLog = array(
                        'usuario_id' => $arrCodigo['usuario_id'],
                        'usuario_id_id' => null,
                        'usuario_id_causante' => $usuario['datos']['usuario_id'],
                        'pnt_id' => Obj_CpnPuntajes::REFERER,
                        'log_puntos' => $puntos,
                        'log_descripcion' => "ganas puntos por llegar a Valeplaza gracias a ".$usuario['datos']['nombre'],
                        'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                    );

                    #el cliente referido gana puntos tipo referer
                    $modelLog->guardar($datLog);
                    
                    #VALEPLAZA pierde puntos
                    $this->LogValeplaza($arrCodigo['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);
                    /*$oldpuntos = $modelCliente->getPuntajeById($arrCodigo['usuario_id']);
                    $newpuntos = $oldpuntos + ($puntos);
                    $cli = array('cliente_puntos' => $newpuntos);*/
                    $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                            );
                    $where = array('usuario_id = ?' => $arrCodigo['usuario_id']);
                    #actualizamos los puntos del cliente referido
                    $modelCliente->update($datcli, $where);
                    if ($usuario['tipo'] == 'cliente') {
                        #el cliente referido gana puntos tipo registro si cumple requisitos

                        $this->_setTrofeoToClient($usuario['datos']['usuario_id'], $usuario['datos']['cliente_id'], $usuario['datos']['cliente_puntos'], Obj_CpnClienteTrofeos::TIP_AMX_REQ, Obj_CpnClienteTrofeos::TIP_AMX_REQ_TEXT);
                        //$status = $modelClienteTrofeos->verifyLogroByCliente($referer['cliente_id'], Obj_CpnClienteTrofeos::TROFEO_DESB_CONTA);
                        //if ($return) {
                        #si el cliente referido ya obtuvo del logro del desbloqueo del contador
                        
                        $descripcion = $arrCodigo['cliente_nombre'] . ' es tu nuevo amigo, ganas puntos por haberlo invitado a Valeplaza';
                        $datLog = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => null,
                            'usuario_id_causante' => $arrCodigo['usuario_id'],
                            'pnt_id' => Obj_CpnPuntajes::REFERER,
                            'log_puntos' => $puntos,
                            'log_descripcion' => $descripcion,
                            'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                        );

                        #el cliente referido gana puntos tipo referer
                        $modelLog->guardar($datLog);

                        #VALEPLAZA pierde puntos
                        $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

                        //$oldpuntos = $modelCliente->getPuntajeById($usuario['datos']['cliente_id']);
                        //$newpuntos = $oldpuntos + ($puntos);
                        //$cli = array('cliente_puntos' => $newpuntos);
                        $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                            );
                        $where = array('usuario_id = ?' => $usuario['datos']['usuario_id']);

                        #actualizamos los puntos del cliente referido
                        $modelCliente->update($datcli, $where);
                        //}
                        #se lo coloca como amigo del cliente referido
                        $datAmigo1 = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => $arrCodigo['usuario_id'],
                            'usuario_id_ini_amistad' => $usuario['datos']['usuario_id'],
                            'amistad_tipo' => Obj_CpnAmistad::TIPO_AMISTAD,
                            'amistad_estado' => Obj_CpnAmistad::AMISTAD_ACTIVO
                        );
                        $datAmigo2 = array(
                            'usuario_id' => $arrCodigo['usuario_id'],
                            'usuario_id_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_ini_amistad' => $usuario['datos']['usuario_id'],
                            'amistad_tipo' => Obj_CpnAmistad::TIPO_AMISTAD,
                            'amistad_estado' => Obj_CpnAmistad::AMISTAD_ACTIVO
                        );
                        $modelAmistad->guardar($datAmigo1);
                        $modelAmistad->guardar($datAmigo2);

                        #######################################
                        $selectco = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_cliente'), array('count(*) as num'))
                                ->where('t1.cliente_id_referer = ?', $usuario['datos']['usuario_id']);
                        $count = $this->getAdapter()->fetchRow($selectco);
                        if ($count['num'] == 3) {
                            $descripcion = "VALEPLAZA te premia por invitar a tus 3 primeros amigos";
                            $datLog['usuario_id'] = $usuario['datos']['usuario_id'];
                            $datLog['usuario_id_id'] = null;
                            $datLog['usuario_id_causante'] = null;
                            $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                            $datLog['log_puntos'] = 30;
                            $datLog['log_descripcion'] = $descripcion;
                            $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;

                            #guardamos el log de los puntos ganados por el cliente
                            $modelLog->guardar($datLog);

                            #guardamos el LOG del banco valeplaza
                            $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::PREMIO, 30, Obj_CpnLogPuntos::TIPO_PIERDE);
                            $wherecli = array("cliente_id = ?" => $usuario['datos']['cliente_id']);
                            $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + 30'),
                            );
                            $modelCliente->update($datcli, $wherecli);
                        }


                        #######################################
                    } else {
                        #puntos para el referido tipo tienda

                        $this->_setTrofeoToTienda($usuario['datos']['usuario_id'], $usuario['datos']['tienda_id'], $usuario['datos']['tienda_puntos'], Obj_CpnClienteTrofeos::TIP_SOC_INV, Obj_CpnClienteTrofeos::TIP_SOC_INV_TEXT);

                        $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::SEGUIR);
                        $descripcion = $arrCodigo['cliente_nombre'] . ' es tu nuevo socio, ganas puntos';
                        $datLog = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => null,
                            'usuario_id_causante' => $arrCodigo['usuario_id'],
                            'pnt_id' => Obj_CpnPuntajes::SEGUIR,
                            'log_puntos' => $puntos,
                            'log_descripcion' => $descripcion,
                            'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                        );
                        $modelLog->guardar($datLog);
                        #dar los puntos a la tienda  
                        /*$oldpuntos = $modelTienda->getPuntajeById($usuario['datos']['tienda_id']);
                        $newpuntos = $oldpuntos + $puntos;
                        $tie = array('tienda_puntos' => $newpuntos);*/
                        $datTie = array(
                                'tienda_puntos' => new Zend_Db_Expr('tienda_puntos + '.$puntos),
                            );
                        $where = array('tienda_id = ?' => $usuario['datos']['tienda_id']);

                        #actualizamos de la tienda referida
                        $modelTienda->update($datTie, $where);

                        #se lo coloca como seguidor de la tienda referida
                        $datSeguidor = array(
                            'tienda_id' => $usuario['datos']['tienda_id'],
                            'cliente_id' => $arrCodigo['cliente_id'],
                            'tseg_estado' => Obj_CpnTiendaSeguidores::SIGUE
                        );
                        $modelSeguidor->guardar($datSeguidor);

                        #VALEPLAZA pierde puntos
                        $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

                        #######################################
                        $selectco = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_cliente'), array('count(*) as num'))
                                ->where('t1.cliente_id_referer = ?', $usuario['datos']['usuario_id']);
                        $count = $this->getAdapter()->fetchRow($selectco);
                        if ($count['num'] == 3) {
                            $descripcion = "VALEPLAZA te premia por invitar a tus 3 primeros socios";
                            $datLog['usuario_id'] = $usuario['datos']['usuario_id'];
                            $datLog['usuario_id_id'] = null;
                            $datLog['usuario_id_causante'] = null;
                            $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                            $datLog['log_puntos'] = 50;
                            $datLog['log_descripcion'] = $descripcion;
                            $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;

                            #guardamos el log de los puntos ganados por el cliente
                            $modelLog->guardar($datLog);

                            #guardamos el LOG del banco valeplaza
                            $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::PREMIO, 50, Obj_CpnLogPuntos::TIPO_PIERDE);
                            $wheretie = array("tienda_id = ?" => $usuario['datos']['tienda_id']);
                            $dattie = array(
                                'tienda_puntos' => new Zend_Db_Expr('tienda_puntos + 50'),
                            );
                            $modelTienda->update($dattie, $wheretie);
                        }


                        #######################################
                    }
                }
            ########################################################################################
            return array('status' => TRUE);
        }else{
            return array('status' => FALSE);
        }
    }
    
    public function wsReferir($codigo, $rol = Obj_SysRol::ROL_USUARIO) {
        $modelLog = new Model_CpnLogPuntos();
        $modelPuntaje = new Model_CpnPuntajes();
        $modelCliente = new Model_CpnCliente();
        $modelAmistad= new Model_CpnAmistad();
        $modelTienda=new Model_CpnTiendas();
        $modelSeguidor=new Model_CpnTiendaSeguidores();
        //$modelClienteTrofeos = new Model_CpnClienteTrofeos();
        #obtengo datos del usuario segun codigo de confirmacion
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name), array('usuario_id', 'usuario_email', 'usuario_password'))
                ->join(array('t2' => 'cpn_cliente'), 't1.usuario_id=t2.usuario_id', array('cliente_id','cliente_id_referer','cliente_nombre'))
                ->where('usuario_codigo = ?', $codigo)
                ->where('rol_id = ?', $rol);
        $arrCodigo = $this->getAdapter()->fetchRow($select);

        if (!empty($arrCodigo)) {
            
            ########################################################################################
                if (!empty($arrCodigo['cliente_id_referer'])) {
                    #si existe el cliente se registro bajo la referencia de un usuario tipo "cliente" o "tienda"
                    $usuario = $this->getDataByID($arrCodigo['cliente_id_referer']);
                    $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::REFERER);
                    #LE DAMOS PUNTOS AL CLIENTE POR SER INVITADO DE OTRO USUARIO
                    $datLog = array(
                        'usuario_id' => $arrCodigo['usuario_id'],
                        'usuario_id_id' => null,
                        'usuario_id_causante' => $usuario['datos']['usuario_id'],
                        'pnt_id' => Obj_CpnPuntajes::REFERER,
                        'log_puntos' => $puntos,
                        'log_descripcion' => "ganas puntos por llegar a Valeplaza gracias a ".$usuario['datos']['nombre'],
                        'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                    );

                    #el cliente referido gana puntos tipo referer
                    $modelLog->guardar($datLog);
                    
                    #VALEPLAZA pierde puntos
                    $this->LogValeplaza($arrCodigo['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);
                    /*$oldpuntos = $modelCliente->getPuntajeById($arrCodigo['usuario_id']);
                    $newpuntos = $oldpuntos + ($puntos);
                    $cli = array('cliente_puntos' => $newpuntos);*/
                    $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                            );
                    $where = array('usuario_id = ?' => $arrCodigo['usuario_id']);
                    #actualizamos los puntos del cliente referido
                    $modelCliente->update($datcli, $where);
                    if ($usuario['tipo'] == 'cliente') {
                        #el cliente referido gana puntos tipo registro si cumple requisitos

                        $this->_setTrofeoToClient($usuario['datos']['usuario_id'], $usuario['datos']['cliente_id'], $usuario['datos']['cliente_puntos'], Obj_CpnClienteTrofeos::TIP_AMX_REQ, Obj_CpnClienteTrofeos::TIP_AMX_REQ_TEXT);
                        //$status = $modelClienteTrofeos->verifyLogroByCliente($referer['cliente_id'], Obj_CpnClienteTrofeos::TROFEO_DESB_CONTA);
                        //if ($return) {
                        #si el cliente referido ya obtuvo del logro del desbloqueo del contador
                        
                        $descripcion = $arrCodigo['cliente_nombre'] . ' es tu nuevo amigo, ganas puntos por haberlo invitado a Valeplaza';
                        $datLog = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => null,
                            'usuario_id_causante' => $arrCodigo['usuario_id'],
                            'pnt_id' => Obj_CpnPuntajes::REFERER,
                            'log_puntos' => $puntos,
                            'log_descripcion' => $descripcion,
                            'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                        );

                        #el cliente referido gana puntos tipo referer
                        $modelLog->guardar($datLog);

                        #VALEPLAZA pierde puntos
                        $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

                        //$oldpuntos = $modelCliente->getPuntajeById($usuario['datos']['cliente_id']);
                        //$newpuntos = $oldpuntos + ($puntos);
                        //$cli = array('cliente_puntos' => $newpuntos);
                        $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + '.$puntos),
                            );
                        $where = array('usuario_id = ?' => $usuario['datos']['usuario_id']);

                        #actualizamos los puntos del cliente referido
                        $modelCliente->update($datcli, $where);
                        //}
                        #se lo coloca como amigo del cliente referido
                        $datAmigo1 = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => $arrCodigo['usuario_id'],
                            'usuario_id_ini_amistad' => $usuario['datos']['usuario_id'],
                            'amistad_tipo' => Obj_CpnAmistad::TIPO_AMISTAD,
                            'amistad_estado' => Obj_CpnAmistad::AMISTAD_ACTIVO
                        );
                        $datAmigo2 = array(
                            'usuario_id' => $arrCodigo['usuario_id'],
                            'usuario_id_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_ini_amistad' => $usuario['datos']['usuario_id'],
                            'amistad_tipo' => Obj_CpnAmistad::TIPO_AMISTAD,
                            'amistad_estado' => Obj_CpnAmistad::AMISTAD_ACTIVO
                        );
                        $modelAmistad->guardar($datAmigo1);
                        $modelAmistad->guardar($datAmigo2);

                        #######################################
                        $selectco = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_cliente'), array('count(*) as num'))
                                ->where('t1.cliente_id_referer = ?', $usuario['datos']['usuario_id']);
                        $count = $this->getAdapter()->fetchRow($selectco);
                        if ($count['num'] == 3) {
                            $descripcion = "VALEPLAZA te premia por invitar a tus 3 primeros amigos";
                            $datLog['usuario_id'] = $usuario['datos']['usuario_id'];
                            $datLog['usuario_id_id'] = null;
                            $datLog['usuario_id_causante'] = null;
                            $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                            $datLog['log_puntos'] = 30;
                            $datLog['log_descripcion'] = $descripcion;
                            $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;

                            #guardamos el log de los puntos ganados por el cliente
                            $modelLog->guardar($datLog);

                            #guardamos el LOG del banco valeplaza
                            $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::PREMIO, 30, Obj_CpnLogPuntos::TIPO_PIERDE);
                            $wherecli = array("cliente_id = ?" => $usuario['datos']['cliente_id']);
                            $datcli = array(
                                'cliente_puntos' => new Zend_Db_Expr('cliente_puntos + 30'),
                            );
                            $modelCliente->update($datcli, $wherecli);
                        }


                        #######################################
                    } else {
                        #puntos para el referido tipo tienda

                        $this->_setTrofeoToTienda($usuario['datos']['usuario_id'], $usuario['datos']['tienda_id'], $usuario['datos']['tienda_puntos'], Obj_CpnClienteTrofeos::TIP_SOC_INV, Obj_CpnClienteTrofeos::TIP_SOC_INV_TEXT);

                        $puntos = $modelPuntaje->getPntById(Obj_CpnPuntajes::SEGUIR);
                        $descripcion = $arrCodigo['cliente_nombre'] . ' es tu nuevo socio, ganas puntos';
                        $datLog = array(
                            'usuario_id' => $usuario['datos']['usuario_id'],
                            'usuario_id_id' => null,
                            'usuario_id_causante' => $arrCodigo['usuario_id'],
                            'pnt_id' => Obj_CpnPuntajes::SEGUIR,
                            'log_puntos' => $puntos,
                            'log_descripcion' => $descripcion,
                            'log_tipo' => Obj_CpnLogPuntos::TIPO_GANA
                        );
                        $modelLog->guardar($datLog);
                        #dar los puntos a la tienda  
                        /*$oldpuntos = $modelTienda->getPuntajeById($usuario['datos']['tienda_id']);
                        $newpuntos = $oldpuntos + $puntos;
                        $tie = array('tienda_puntos' => $newpuntos);*/
                        $datTie = array(
                                'tienda_puntos' => new Zend_Db_Expr('tienda_puntos + '.$puntos),
                            );
                        $where = array('tienda_id = ?' => $usuario['datos']['tienda_id']);

                        #actualizamos de la tienda referida
                        $modelTienda->update($datTie, $where);

                        #se lo coloca como seguidor de la tienda referida
                        $datSeguidor = array(
                            'tienda_id' => $usuario['datos']['tienda_id'],
                            'cliente_id' => $arrCodigo['cliente_id'],
                            'tseg_estado' => Obj_CpnTiendaSeguidores::SIGUE
                        );
                        $modelSeguidor->guardar($datSeguidor);

                        #VALEPLAZA pierde puntos
                        $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::REFERER, $puntos, Obj_CpnLogPuntos::TIPO_PIERDE);

                        #######################################
                        $selectco = $this->getAdapter()->select()
                                ->from(array('t1' => 'cpn_cliente'), array('count(*) as num'))
                                ->where('t1.cliente_id_referer = ?', $usuario['datos']['usuario_id']);
                        $count = $this->getAdapter()->fetchRow($selectco);
                        if ($count['num'] == 3) {
                            $descripcion = "VALEPLAZA te premia por invitar a tus 3 primeros socios";
                            $datLog['usuario_id'] = $usuario['datos']['usuario_id'];
                            $datLog['usuario_id_id'] = null;
                            $datLog['usuario_id_causante'] = null;
                            $datLog['pnt_id'] = Obj_CpnPuntajes::PREMIO;
                            $datLog['log_puntos'] = 50;
                            $datLog['log_descripcion'] = $descripcion;
                            $datLog['log_tipo'] = Obj_CpnLogPuntos::TIPO_GANA;

                            #guardamos el log de los puntos ganados por el cliente
                            $modelLog->guardar($datLog);

                            #guardamos el LOG del banco valeplaza
                            $this->LogValeplaza($usuario['datos']['usuario_id'], Obj_CpnPuntajes::PREMIO, 50, Obj_CpnLogPuntos::TIPO_PIERDE);
                            $wheretie = array("tienda_id = ?" => $usuario['datos']['tienda_id']);
                            $dattie = array(
                                'tienda_puntos' => new Zend_Db_Expr('tienda_puntos + 50'),
                            );
                            $modelTienda->update($dattie, $wheretie);
                        }


                        #######################################
                    }
                }
            ########################################################################################
            return array('status' => TRUE);
        }else{
            return array('status' => FALSE);
        }
    }
    public function wsGetById($token) {
        //$key = $this->_name . '_id' . $id;
        //if (!$result = Cit_Cache::load($key)) {
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where('usuario_codigo = ?', $token);
        $result = $this->getAdapter()->fetchRow($select);
        //Cit_Cache::save($result, $key);
        //}
        return $result;
    }
    

}
