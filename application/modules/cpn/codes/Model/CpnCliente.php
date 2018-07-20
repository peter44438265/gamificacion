<?php

/**
 * CpnUsuario db table abstract
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Model_CpnCliente extends Zend_Db_Table_Abstract {

    /**
     * @var string Name of db table
     */
    protected $_name = 'clientes';

    /**
     * @var string or array of fields in table
     */
    protected $_primary = 'cliente_id';

    /**
     * retorna todos los registros de la tabla  ordenado por el parametro indicado
     *
     * @return array|null 
     * @param string $order
     */
    public function guardarCliente($datos) {
        if (isset($datos['cliente_id'])) {
            $id = $datos['cliente_id'];
            $where = array("cliente_id = ?" => $id);
            $this->update($datos, $where);
        } else {
            $id = $this->insert($datos);
        }
        return $id;
    }
}
