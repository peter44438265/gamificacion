<?php
/**
 * CpnUsuario model
 *
 * @author www.likerow.com(likerow@gmail.com)
 */
class Obj_CpnUsuario
{
    const ESTADO_ACTIVO=0;
    const ESTADO_INACTIVO=1;
    const ESTADO_ESPERA=2;
    /**
     * @var colums table
     */
    protected $usuario_id = null;

    /**
     * @var colums table
     */
    protected $rol_id = null;

    /**
     * @var colums table
     */
    protected $usuario_email = null;

    /**
     * @var colums table
     */
    protected $usuario_password = null;

    /**
     * @var colums table
     */
    protected $usuario_estado = null;

    /**
     * @var colums table
     */
    protected $fecha_registro = null;

    /**
     * @var colums table
     */
    protected $fecha_edicion = null;

    /**
     * @var colums table
     */
    protected $usuario_codigo = null;

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
    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    /**
     * setea value del columnna
     *
     * @param string $usuarioid
     */
    public function setUsuarioId($usuarioid)
    {
        $this->usuario_id = $usuarioid;
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
    public function getUsuarioEmail()
    {
        return $this->usuario_email;
    }

    /**
     * setea value del columnna
     *
     * @param string $usuarioemail
     */
    public function setUsuarioEmail($usuarioemail)
    {
        $this->usuario_email = $usuarioemail;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getUsuarioPassword()
    {
        return $this->usuario_password;
    }

    /**
     * setea value del columnna
     *
     * @param string $usuariopassword
     */
    public function setUsuarioPassword($usuariopassword)
    {
        $this->usuario_password = $usuariopassword;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getUsuarioEstado()
    {
        return $this->usuario_estado;
    }

    /**
     * setea value del columnna
     *
     * @param string $usuarioestado
     */
    public function setUsuarioEstado($usuarioestado)
    {
        $this->usuario_estado = $usuarioestado;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getFechaRegistro()
    {
        return $this->fecha_registro;
    }

    /**
     * setea value del columnna
     *
     * @param string $fecharegistro
     */
    public function setFechaRegistro($fecharegistro)
    {
        $this->fecha_registro = $fecharegistro;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getFechaEdicion()
    {
        return $this->fecha_edicion;
    }

    /**
     * setea value del columnna
     *
     * @param string $fechaedicion
     */
    public function setFechaEdicion($fechaedicion)
    {
        $this->fecha_edicion = $fechaedicion;
    }

    /**
     * retorna valor de la columna
     *
     * @return string
     */
    public function getUsuarioCodigo()
    {
        return $this->usuario_codigo;
    }

    /**
     * setea value del columnna
     *
     * @param string $usuariocodigo
     */
    public function setUsuarioCodigo($usuariocodigo)
    {
        $this->usuario_codigo = $usuariocodigo;
    }


}
