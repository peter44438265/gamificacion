<?php
/**
 * SysRol model
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Obj_SysUbigeo
{

    /**
     * @var colums table
     */
    protected $ubigeo_id = null;

    /**
     * @var colums table
     */
    protected $ubigeo_desc = null;

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
    public function getUbigeoId()
    {
        return $this->ubigeo_id;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolid
     */
    public function setUbigeoId($ubigeoid)
    {
        $this->ubigeo_id = $ubigeoid;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getUbigeoDesc()
    {
        return $this->ubigeo_desc;
    }

    /**
     * setea value del columnna
     *
     * @param string $rolrolid
     */
    public function setUbigeoDesc($ubigeodesc)
    {
        $this->ubigeo_desc = $ubigeodesc;
    }


}
