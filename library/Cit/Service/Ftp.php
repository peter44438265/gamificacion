<?php
class ZExtraLib_Service_Ftp
{
    /**
     * @var string
     */
    protected $_host;
    /**
     * @var string
     */
    protected $_username;
    /**
     * @var string
     */
    protected $_password;
    /**
     * @var string
     */
    protected $_cidftp;
    
    /**
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct ($host, $username, $password)
    {
        $this->_host = $host; //host del servidor ftp
        $this->_username = $username; // usuario ftp
        $this->_password = $password; //passwrod ftp
    }
    
    /**
     * @return string
     */
    public function openFtp () 
    {
        $this->_cidftp = ftp_connect($this->_host); // Luego creamos un login al mismo con nuestro usuario y contraseña
        $resultado = ftp_login($this->_cidftp, $this->_username, $this->_password);
        /*$result = ! ((! $this->_cidftp) || (! $resultado));
        if ((! $this->_cidftp) || (! $resultado)) {
            $result = "Fallo en la conexión";
            //die();
        } else {
            $result = "Conectado.";
            //die();
        }*/
        
        ftp_pasv($this->_cidftp, true);       
        return $resultado;
    }
     /**
     * lista los archivos de directorio
     * @return void
     */
    public function listFtp ($dir=null)
    {
        $lista = array();
        if(empty ($dir)){ 
          $dir = ftp_pwd($this->_cidftp);  
        }
        if (isset($this->_cidftp)){
             //obteniendo directorio actual
            $lista = ftp_nlist($this->_cidftp,$dir);
            return $lista;
        }         
        else return false;
    }
     /**
     * descarga archivo
     * @return void
     */
    public function download ($handle, $remote_file)
    {
        ftp_fget($this->_cidftp, $handle, $remote_file, FTP_BINARY, 0);
    }
    
    
     public function closeFtp ()
    {
        if (isset($this->_cidftp))
        ftp_close($this->_cidftp);
        else return false;
    }
   
    
    /**
     * @param unknown_type $ruta
     * @param unknown_type $file
     */
    public function upImage ($ruta, $file)
    {
        ftp_put($this->_cidftp, $ruta, $file, FTP_BINARY);
        ftp_chmod($this->_cidftp, 0777, $ruta);
    }
    
    function getPermisos($ruta)
    {
        @ftp_chmod($this->_cidftp, 0777, $ruta);
    }
}