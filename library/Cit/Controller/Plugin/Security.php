<?php

class Cit_Controller_Plugin_Security extends Zend_Controller_Plugin_Abstract {
    const DEFAULT_REGISTRY_KEY='Acl';
    const MODULO_DEFAULT = 'admin';
    const MODULO_ADMIN = 'admin';

    private $_acl;

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $module = strtolower($request->getModuleName());
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());

        $recurso = new Model_OuthRecurso();
        //Obj_OuthRecurso::$_data['rec_uri'] = $module . ':' . $controller . ':' . $action;
        //$recurso->guardarUri(Obj_OuthRecurso::$_data);

        $this->getAcl();
        $role = Obj_OuthRol::ROL_INVITADO;
        $session = Zend_Registry::get('Susuario');
        if (isset($session->status)) {
            if ($session->status == true) {
                $role = $session->usuario->rol_id;
            }
        }
        $strRecurso = $module . ':' . $controller . ':' . $action;
        if (!in_array($strRecurso, $this->_acl->getResources()))
            return;
        if (!$this->_acl->isAllowed($role, $strRecurso)) {
           // if ($module == Obj_OuthRecurso::MODULE_PLN) {
                $request->setModuleName('outh');
                $request->setControllerName('index');
                $request->setActionName('login');
            /*} elseif ($module == Obj_OuthRecurso::MODULE_COFFEE
                    && $controller == 'usuario') {
                $request->setModuleName('default');
                $request->setControllerName('registro');
                $request->setActionName('login');
            }*/
        }
    }

    private function getAcl() {
        if (!empty($this->_acl)) {
            return $this->_acl;
        }
        $this->_acl = new Zend_Acl();
        $roles = $this->getTable('outh_rol');
        if (!empty($roles)) {
            foreach ($this->getTable('outh_rol') as $row) {
                $this->_acl->addRole(new Zend_Acl_Role($row['rol_id']));
            }
        }
        $recursos = $this->getTable('outh_recurso');
        
        if (!empty($recursos)) {
            foreach ($this->getTable('outh_recurso') as $row) {
                $this->_acl->addResource(new Zend_Acl_Resource(trim($row['rec_uri'])));
            }
        }
        $recursorol = $this->getTable('outh_rol_recurso');
        if (!empty($recursorol)) {
            foreach ($this->getTable('outh_rol_recurso') as $row) {
                $this->_acl->$row['rolrec_permiso']($row['rol_id'], trim($row['rec_uri']));
            }
        }
        Zend_Registry::set(self::DEFAULT_REGISTRY_KEY, $this->_acl);
        return $this->_acl;
    }

    private function getTable($name) {
        $result = array();
        $cache = Zend_Registry::get('Cache');
        $key = 'acl_' . $name;
        if ($cache->test($key)) {
            //return $cache->load($key);
        }
        $table = new Zend_Db_Table($name);
        if ($name == 'outh_rol_recurso') {
            $sql = new Zend_Db_Select($table->getDefaultAdapter());
            $sql->from(array('acl' => 'outh_rol'), array('acl.rol_id'))
                    ->join(array('r' => 'outh_rol_recurso'), 'acl.rol_id=r.rol_id'
                            , array('r.rec_id', 'r.rolrec_permiso'))
                    ->join(array('rec' => 'outh_recurso'), 'rec.rec_id=r.rec_id'
                            , array('rec.rec_uri'));
            $sql->where("rec.rec_uri is not null and   rec.rec_uri !=''");
            $result = $table->getAdapter()->fetchAll($sql);
        } elseif ($name == 'outh_rol') {
            $table->select()->where('estado =?', 1);
            $result = $table->fetchAll()->toArray();
        } elseif ($name == 'outh_recurso') {
            $se = $table->select()
                            ->where('rec_estado =?', 1)
                            ->where("rec_uri is not null and   rec_uri !=''");
            $result = $table->fetchAll($se)->toArray();
        }
        $cache->save($result, $key);
        return $result;
    }

}
