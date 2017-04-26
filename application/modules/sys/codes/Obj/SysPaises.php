<?php
/**
 * SysRol model
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Obj_SysPaises
{
    const PAIS_ACTIVO=0;
    const PAIS_INACTIVO=1;
    const PAIS_DEFAULT=1;
    /**
     * @var colums table
     */
    protected $pais_id = null;

    /**
     * @var colums table
     */
    protected $pais_nombre = null;

    /**
     * contruct 
     *
     * @param array $datos
     */
    public function __construct($datos)
    {
        foreach ($datos as $indice => $value){
                                                    if(isset ($this->$indice)){
                                                        $this->$indice = $value;
                                                    }
                                                }
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getPaisId()
    {
        return $this->pais_id;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolid
     */
    public function setPaisId($paisid)
    {
        $this->pais_id = $paisid;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getPaisNombre()
    {
        return $this->pais_nombre;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolrolid
     */
    public function setPaisNombre($paisnombre)
    {
        $this->pais_nombre = $paisnombre;
    }


}
