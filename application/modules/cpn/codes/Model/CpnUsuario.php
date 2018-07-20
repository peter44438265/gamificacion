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
    protected $_name = 'usuario';

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
    public function guardarUsuario($datos) {
        if (isset($datos['usuario_id'])) {
            $id = $datos['usuario_id'];
            $where = array("usuario_id = ?" => $id);
            $this->update($datos, $where);
        } else {
            $datos['password']= password_hash($datos['password'], PASSWORD_DEFAULT);
            $datos['fecha_registro'] = Zend_Date::now()->toString('Y-m-d H:i:s');
            $id = $this->insert($datos);
        }
        return $id;
    }
    public function verifyEmail($email){
        $select = $this->getAdapter()->select()
                ->from(array('t1' => $this->_name))
                ->where("email = ?", $email);
        $result = $this->getAdapter()->fetchRow($select);
        if(!empty($result)){
            return array("status" => FALSE, "mensaje" => "Ya existe el correo");
        }else{
            return array("status" => TRUE);
        }
    }
}
