<?php
/**
 * SysRol db table abstract
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Model_SysGeo extends Zend_Db_Table_Abstract
{

    /**
     * @var string Name of db table
     */
    protected $_name = 'sys_geo';

    /**
     * @var string or array of fields in table
     */
    protected $_primary = 'geo_id';

    /**
     * retorna todos los registros de la tabla  ordenado por el parametro indicado
     *
     * @return array|null 
     * @param string $order
     */
    public function getAll($order = null)
    {
        //$key = $this->_name .'_all';
              //if (!$result = Cit_Cache::load($key)) {
              $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name));
              if (!empty($order))
              $select->order($order);
              $result = $this->getAdapter()->fetchAll($select);
               //Cit_Cache::save($result, $key);
              //}
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
              ->where('ubigeo_id = ?', $id);
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
        if (isset($datos['ubigeo_id'])) {
              /*if ($fechaEdicion == TRUE) {
              $datos['fecha_edicion'] = Zend_Date::now()->toString('Y-m-d H:i:s');
              }*/
              $id = $datos['ubigeo_id'];
              $where = array("ubigeo_id = ?" => $id);
              $this->update($datos, $where);
              } else {
              //$datos['fecha_creacion'] = Zend_Date::now()->toString('Y-m-d H:i:s');
              $id = $this->insert($datos);
              }
              return $id;
    }
    
    public function getbyIp($ip){
            /*$select = $this->getAdapter()->select()
                    ->from(array('t1' =>'sys_ips'))
                    ->join(array('t2'=>'sys_geo'),'t1.geo_id=t2.geo_id')
                    ->where("ip_start <= INET_ATON('".$ip."')")
                    ->order('ip_start DESC')
                    ->limit(1);*/
            $select = $this->getAdapter()->select()
                    ->from(array('t1' =>'sys_ips'))
                    ->join(array('t2'=>'sys_geo'),'t1.geo_id=t2.geo_id')
                    ->where($ip." between ip_start and ip_end")
                    //->order('ip_start DESC')
                    ->limit(1);
            //echo $select;die;
            $result = $this->getAdapter()->fetchRow($select);
            return $result;
    }
 


}
