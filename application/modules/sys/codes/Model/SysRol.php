<?php
/**
 * SysRol db table abstract
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Model_SysRol extends Zend_Db_Table_Abstract
{

    /**
     * @var string Name of db table
     */
    protected $_name = 'sys_rol';

    /**
     * @var string or array of fields in table
     */
    protected $_primary = 'rol_id';

    /**
     * retorna todos los registros de la tabla  ordenado por el parametro indicado
     *
     * @return array|null 
     * @param string $order
     */
    public function getAll($order = null)
    {
        $key = $this->_name .'_all';
              if (!$result = Cit_Cache::load($key)) {
              $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name));
              if (!empty($order))
              $select->order($order);
              $result = $this->getAdapter()->fetchAll($select);
               Cit_Cache::save($result, $key);
              }
              return $result;
    }

    /**
     * retorna la tabla por ID
     *
     * @return array|null 
     * @param string $id
     */
    public function getById($id)
    {
        $key = $this->_name .'_id'. $id;
              if (!$result = Cit_Cache::load($key)) {
              $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name))
              ->where('rol_id = ?', $id);
              $result = $this->getAdapter()->fetchRow($select);
              Cit_Cache::save($result, $key);
              }
              return $result;
    }

    /**
     * Inserta o actualiza la tabla segun sea el caso
     *
     * @param array $datos
     * @param string $fechaEdicion
     * @return string
     */
    public function guardar($datos, $fechaEdicion = TRUE)
    {
        if (isset($datos['rol_id'])) {
              if ($fechaEdicion == TRUE) {
              $datos['fecha_edicion'] = Zend_Date::now()->toString('Y-m-d H:i:s');
              }
              $id = $datos['rol_id'];
              $where = array("rol_id = ?" => $id);
              $this->update($datos, $where);
              } else {
              $datos['fecha_creacion'] = Zend_Date::now()->toString('Y-m-d H:i:s');
              $id = $this->insert($datos);
              }
              return $id;
    }


}
