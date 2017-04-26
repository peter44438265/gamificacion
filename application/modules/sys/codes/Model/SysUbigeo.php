<?php
/**
 * SysRol db table abstract
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Model_SysUbigeo extends Zend_Db_Table_Abstract
{

    /**
     * @var string Name of db table
     */
    protected $_name = 'sys_ubigeo';

    /**
     * @var string or array of fields in table
     */
    protected $_primary = 'ubigeo_id';

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
    
    public function getProvincia($id,$tiendaID=null){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_provincia'),array('distinct(prov_id) as prov_id','prov_nombre','prov_coordenada'));
                    if(!empty($tiendaID)){ 
                        $select->join(array('t2'=>'cpn_mall'),'t1.prov_id=t2.ubg_id',array(''))
                                ->join(array('t3'=>'cpn_local'),'t2.mall_id=t3.mall_id',array(''))
                                ->where('t3.tienda_id =?',$tiendaID);
                    }
                    $select->where('t1.pais_id = ?', $id);
            $result = $this->getAdapter()->fetchAll($select);
            //echo $select;die;
            return $result;
                    /*echo "SELECT * FROM `sys_ubigeo` where pais_id='1' and ubg_cod like ('%0000');";*/
    }
    public function getDistritos($provinciaID){
        $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_distritos'),array('*'));
                    $select->where('t1.provincia_id = ?', $provinciaID);
            $result = $this->getAdapter()->fetchAll($select);
            //echo $select;die;
            return $result;
    }
    /*public function getProvinciaByCode($codeID){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_provincia'),array('distinct(prov_id) as prov_id','prov_nombre','prov_coordenada'))
                    ->join(array('t2' =>'sys_paises'),'t1.pais_id=t2.pais_id',array(''))
                    ->where('t2.pais_codigo = ?', $codeID);
            $result = $this->getAdapter()->fetchAll($select);
            //echo $select;die;
            return $result;
                    
    }*/
    public function getProvinciaByCode($id,$tiendaID=null){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_provincia'),array('distinct(prov_id) as prov_id','prov_nombre','prov_coordenada'))
                    ->join(array('t2'=>'sys_paises'),'t1.pais_id=t2.pais_id',array(''));
                    if(!empty($tiendaID)){ 
                        $select->join(array('t2'=>'cpn_mall'),'t1.prov_id=t2.ubg_id',array(''))
                                ->join(array('t3'=>'cpn_local'),'t2.mall_id=t3.mall_id',array(''))
                                ->where('t3.tienda_id =?',$tiendaID);
                    }
                    $select->where('t2.pais_codigo = ?', $id);
            $result = $this->getAdapter()->fetchAll($select);
            //echo $select;die;
            return $result;
                    /*echo "SELECT * FROM `sys_ubigeo` where pais_id='1' and ubg_cod like ('%0000');";*/
    }
    
    public function getProvinciaAvailable($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_provincia'))
                    ->where("prov_id in(select distinct(ubg_id) from cpn_local a inner join cpn_mall b on a.mall_id=b.mall_id where a.tienda_id='".$id."' )");
            $result = $this->getAdapter()->fetchAll($select);
            //echo $select;die;
            return $result;
                    /*echo "SELECT * FROM `sys_ubigeo` where pais_id='1' and ubg_cod like ('%0000');";*/
    }
    public function getZonaAvailable($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'cpn_mall'))
                    ->where("mall_id in(select distinct(mall_id) from cpn_local where tienda_id='".$id."' )");
            $result = $this->getAdapter()->fetchAll($select);
            //echo $select;die;
            return $result;
                    /*echo "SELECT * FROM `sys_ubigeo` where pais_id='1' and ubg_cod like ('%0000');";*/
    }
    public function getCiudad($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_ciudades'))
                     ->where('prov_id = ?', $id);
            //echo $select;die;
            $result = $this->getAdapter()->fetchAll($select);
            /*$cod=$result['cod'];
           
            $select2 = $this->getAdapter()->select()
                     ->from(array('t1' => $this->_name))
                     ->where("ubg_cod like ('".$cod."%00')")
                     ->where("ubg_cod <> '".$cod."0000'");
            $result2 = $this->getAdapter()->fetchAll($select2);*/
            return $result;
    }
    public function getProvinciaDat($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_provincia'))
                     ->where('prov_id = ?', $id);
            //echo $select;die;
            $result = $this->getAdapter()->fetchRow($select);
            return $result;
    }
    /*public function getdistrito($id){
            $select = $this->getAdapter()->select()
                     ->from(array('t1' => $this->_name),array("cod"=>"SUBSTRING(ubg_cod,1,4)"))
                     ->where('ubg_id = ?', $id);
            $result = $this->getAdapter()->fetchRow($select);
            $cod=$result['cod'];
           
            $select2 = $this->getAdapter()->select()
                     ->from(array('t1' => $this->_name))
                     ->where("ubg_cod like ('".$cod."%')")
                     ->where("ubg_cod <> '".$cod."00'");
            //echo $select2;die;
            $result2 = $this->getAdapter()->fetchAll($select2);
            return $result2;
    }*/
    /*public function getPaisByUbg($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name),array('pais_id'))
                    ->where('ubg_id = ?', $id);
            $result = $this->getAdapter()->fetchRow($select);
            return $result['pais_id'];
    }
    public function getCodigoById($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name),array('ubg_cod'))
                    ->where('ubg_id = ?', $id);
            $result = $this->getAdapter()->fetchRow($select);
            return $result['ubg_cod'];
    }
    public function getIdByCodigo($cod){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => $this->_name),array('ubg_id'))
                    ->where('ubg_cod = ?', $cod);
            $result = $this->getAdapter()->fetchRow($select);
            return $result['ubg_id'];
    }*/
    
    public function getCiudadesByProvincia($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_ciudades'),array('*'))
                    ->where('prov_id = ?', $id);
            $result = $this->getAdapter()->fetchAll($select);
            return $result;
    }
    public function getProvinciasByPais($id){
            $select = $this->getAdapter()->select()
                    ->from(array('t1' => 'sys_provincia'),array('*'))
                    ->where('pais_id = ?', $id);
            $result = $this->getAdapter()->fetchAll($select);
            return $result;
    }
    
    public function getCiudadesSeguidoresByTiendaAndPais($pais,$tiendaID){
        $select = $this->getAdapter()->select()
                ->from(array('t1' => 'cpn_tienda_seguidores'),array(''))
                ->join(array('t2'=>'cpn_cliente'),'t1.cliente_id=t2.cliente_id',array(''))
                ->join(array('t3'=>'cpn_usuario'),'t2.usuario_id=t3.usuario_id',array(''))
                ->join(array('t4'=>'cpn_niveles_cliente'),'t2.nivcli_id=t4.nivcli_id',array(''))
                //->joinleft(array('t5'=>'sys_paises'),'t3.usuario_pais=t5.pais_id',array(''))
                ->joinleft(array('t6'=>'sys_provincia'),'t2.cliente_ciudad=t6.prov_id',array('distinct(prov_id) as prov_id','prov_nombre','prov_coordenada'))
                ->where('t1.tienda_id = ?', $tiendaID)
                ->where('t6.pais_id = ?', $pais)
                ->where('tseg_estado = ?',  Obj_CpnTiendaSeguidores::SIGUE);
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
    }


}
