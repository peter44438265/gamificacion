<?php
/**
 * SysRecursos model
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Obj_SysRecursos
{
    const MODULE_SYS = 'sys';
    const MODULE_CPN = 'cpn';
    const MODULE_TAX = 'emp';
    const CONTROLLER_EMP = 'empresa';
    /**
     * @var colums table
     */
    protected $rec_id = null;

    /**
     * @var colums table
     */
    protected $rec_pat_id = null;

    /**
     * @var colums table
     */
    protected $rec_uri = null;

    /**
     * @var colums table
     */
    protected $rec_desc = null;

    /**
     * @var colums table
     */
    protected $rec_tipo = null;

    /**
     * @var colums table
     */
    protected $rec_module = null;

    /**
     * @var colums table
     */
    protected $rec_estado = null;

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
    public function getRecPatId()
    {
        return $this->rec_pat_id;
    }

    /**
     * setea value del columnna
     *
     * @param string $recpatid
     */
    public function setRecPatId($recpatid)
    {
        $this->rec_pat_id = $recpatid;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRecUri()
    {
        return $this->rec_uri;
    }

    /**
     * setea value del columnna
     *
     * @param string $recuri
     */
    public function setRecUri($recuri)
    {
        $this->rec_uri = $recuri;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRecDesc()
    {
        return $this->rec_desc;
    }

    /**
     * setea value del columnna
     *
     * @param string $recdesc
     */
    public function setRecDesc($recdesc)
    {
        $this->rec_desc = $recdesc;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRecTipo()
    {
        return $this->rec_tipo;
    }

    /**
     * setea value del columnna
     *
     * @param string $rectipo
     */
    public function setRecTipo($rectipo)
    {
        $this->rec_tipo = $rectipo;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRecModule()
    {
        return $this->rec_module;
    }

    /**
     * setea value del columnna
     *
     * @param string $recmodule
     */
    public function setRecModule($recmodule)
    {
        $this->rec_module = $recmodule;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getRecEstado()
    {
        return $this->rec_estado;
    }

    /**
     * setea value del columnna
     *
     * @param string $recestado
     */
    public function setRecEstado($recestado)
    {
        $this->rec_estado = $recestado;
    }


}
