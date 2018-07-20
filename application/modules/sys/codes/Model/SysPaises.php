<?php
/**
 * SysRol db table abstract
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Model_SysPaises extends Zend_Db_Table_Abstract
{

    /**
     * @var string Name of db table
     */
    protected $_name = 'sys_paises';

    /**
     * @var string or array of fields in table
     */
    protected $_primary = 'pais_id';

    /**
     * retorna todos los registros de la tabla  ordenado por el parametro indicado
     *
     * @return array|null 
     * @param string $order
     */
    public function getAll($order = null)
    {
              $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name))
              ->where('pais_estado = ?',  Obj_SysPaises::PAIS_ACTIVO);

              $result = $this->getAdapter()->fetchAll($select);

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
        //$key = $this->_name .'_id'. $id;
              //if (!$result = Cit_Cache::load($key)) {
              $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name))
              ->where('pais_id = ?', $id);
              $result = $this->getAdapter()->fetchRow($select);
              //Cit_Cache::save($result, $key);
              //}
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
        if (isset($datos['pais_id'])) {
              /*if ($fechaEdicion == TRUE) {
              $datos['fecha_edicion'] = Zend_Date::now()->toString('Y-m-d H:i:s');
              }*/
              $id = $datos['pais_id'];
              $where = array("pais_id = ?" => $id);
              $this->update($datos, $where);
              } else {
              //$datos['fecha_creacion'] = Zend_Date::now()->toString('Y-m-d H:i:s');
              $id = $this->insert($datos);
              }
              return $id;
    }
    public function getMonedaByTienda($id){
        //echo $id;die;
              $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name))
              ->join(array('t3'=>'cpn_usuario'),'t1.pais_id=t3.usuario_pais',array('')) 
              ->join(array('t4'=>'cpn_tienda_usuario'),'t4.usuario_id=t3.usuario_id',array(''))         
              ->join (array('t2'=> 'cpn_tiendas'),'t4.tienda_id=t2.tienda_id',array(''))
              ->where('t2.tienda_id = ?', $id);
              $result = $this->getAdapter()->fetchRow($select);

              return $result;
    }
    
    public function getMonedaByCliente($id){
            $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name))
              ->join(array('t2'=>'cpn_usuario'),'t1.pais_id=t2.usuario_pais',array('')) 
              ->join(array('t3'=>'cpn_cliente'),'t2.usuario_id=t3.usuario_id',array(''))
              ->where('t3.cliente_id = ?', $id);
            //echo $select;die;
              $result = $this->getAdapter()->fetchRow($select);

              return $result;
    }
    
    public function getCiudadesByPais($paisID){
        $select = $this->getAdapter()->select()
              ->from(array('t1' => 'sys_provincia'))
              ->join(array('t2'=>$this->_name),'t1.pais_id=t2.pais_id',array(''))
              ->where('t2.pais_id = ?', $paisID);
        $result = $this->getAdapter()->fetchAll($select);

              return $result;
    }
    
    public function getPaisesSeguidoresByTienda($tiendaID){
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_tienda_seguidores'),array(''))
                ->join(array('t2'=>'cpn_cliente'),'t1.cliente_id=t2.cliente_id',array(''))
                ->join(array('t3'=>'cpn_usuario'),'t2.usuario_id=t3.usuario_id',array(''))
                ->join(array('t4'=>'cpn_niveles_cliente'),'t2.nivcli_id=t4.nivcli_id',array(''))
                ->joinleft(array('t5'=>'sys_paises'),'t3.usuario_pais=t5.pais_id',array('distinct(pais_id) as pais_id','pais_nombre'))
                ->where('t1.tienda_id = ?', $tiendaID)
                ->where('tseg_estado = ?',  Obj_CpnTiendaSeguidores::SIGUE);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }
    
    public function getByPais($pais){
        $select = $this->getAdapter()->select()
              ->from(array('t1' => $this->_name))
              ->where('t1.pais_nombre = ?', $pais);
        $result = $this->getAdapter()->fetchRow($select);

        return $result['pais_codigo'];
    }
    
    


}
