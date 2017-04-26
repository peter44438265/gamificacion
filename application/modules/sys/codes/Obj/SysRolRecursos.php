<?php
/**
 * SysRolRecursos model
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Obj_SysRolRecursos
{

    /**
     * @var colums table
     */
    protected $rol_id = null;

    /**
     * @var colums table
     */
    protected $rec_id = null;

    /**
     * @var colums table
     */
    protected $rolrec_permiso = null;

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
    public function getRecId()
    {
        return $this->rec_id;
    }

    /**
     * setea value del columnna
     *
     * @param string $recid
     */
    public function setRecId($recid)
    {
        $this->rec_id = $recid;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRolrecPermiso()
    {
        return $this->rolrec_permiso;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolrecpermiso
     */
    public function setRolrecPermiso($rolrecpermiso)
    {
        $this->rolrec_permiso = $rolrecpermiso;
    }


}
