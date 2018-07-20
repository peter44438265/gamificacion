<?php
/**
 * SysRol model
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Obj_SysRol
{
    const ROL_ADM=1;
    const ROL_CLIENTE=2;
    const ROL_TIENDA=3;
            

    /**
     * @var colums table
     */
    protected $rol_id = null;

    /**
     * @var colums table
     */
    protected $rol_rol_id = null;

    /**
     * @var colums table
     */
    protected $rol_nombre = null;

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
    public function getRolId()
    {
        return $this->rol_id;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolid
     */
    public function setRolId($rolid)
    {
        $this->rol_id = $rolid;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRolRolId()
    {
        return $this->rol_rol_id;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolrolid
     */
    public function setRolRolId($rolrolid)
    {
        $this->rol_rol_id = $rolrolid;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRolNombre()
    {
        return $this->rol_nombre;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolnombre
     */
    public function setRolNombre($rolnombre)
    {
        $this->rol_nombre = $rolnombre;
    }


}
