<?php
$mystring = dirname(__FILE__);
require_once($mystring.'/Crypto.php');
class App_Service_PagoEfectivo extends App_Service {
    public static $_instance;
  
    protected $_options = array('apiKey' => MERCHAN_ID,
				'capi'=> CAPI,	
				'cclave'=> CCLAVE,	
				'url' =>  WSCIP,
				'crypto' => array('securityPath' =>  SECURITY_PATH,
						  'publicKey' => PUBLICKEY,
						  'privateKey' => PRIVATEKEY,
						  'url' => WSCRYPTA),
				 'gen' => array('url' => WSGENPAGO),
				 'mailAdmin' =>	EMAIL_CONTACTO,
				 'medioPago' => MEDIO_PAGO  		
	                        );
    protected $_crypto;
    protected $_lastPayRequest;
    
    /*
     * Constructor de la aplicación
     * @param string $securityPath Carpeta donde se almacenan public.key y private.key
     */
    public function __construct($options = null)
    {	
        if (isset($options))
            $this->_options = array_merge($this->_options, $options);        
        $this->_crypto =  App_Service_Crypto::getInstance($this->_options['crypto']);
    }
    
    /*
     * Solicitar Pago
     * @param string $xml XML de envio de solicitud de pago
     * @return SimpleXMLElement Resultado de Servicio Ejm:
     * SimpleXMLElement Object
     * (
     *     [iDResSolPago] => 33
     *     [CodTrans] => 3300020
     *     [Token] => 2a3848a4-183a-490c-813a-40d90e82ef96
     *     [Fecha] => 21/02/2012 11:26:27 a.m.
     * )
     */
    public function solicitarPago( $xml )
    { 
        $info = $this->_loadService('SolicitarPago',
                        array( 'request' =>
                        array('cServ' => $this->_options['apiKey'],
                            'CClave' => $this->_crypto->signer($xml),
                            'Xml' => $this->_crypto->encrypt($xml))));        
        $info = $info->SolicitarPagoResult;
    //    var_dump($info); exit();
        if ($info->Estado != 1) throw new Exception('Pago Efectivo : ' . $info->Mensaje);
        return simplexml_load_string($this->_crypto->decrypt($info->Xml));
    }
    
    /*
     * Solicitar Pago
     * @param string $xml XML de envio de solicitud de pago
     * @return SimpleXMLElement Resultado de Servicio Ejm:
     */
    public function eliminarPago($CIP)
    {
        $info = $this->_loadService('EliminarCIP',
                        array( 'request' =>
                        array('CAPI' => $this->_options['capi'],
                            'CClave' => $this->_options['cclave'],
                            'CIP' => (string)$CIP)));        
        
        $info = $info->EliminarCIPResult;
        return $info;
    }
    
    /*
     * Solicitar Pago
     * @param string $xml XML de envio de solicitud de pago
     * @return SimpleXMLElement Resultado de Servicio Ejm:
     */
    public function consultarCip($CIP)
    {        
        $info = $this->_loadService('ConsultarCIP',
                        array( 'request' =>
                        array('CAPI' => $this->_options['capi'],
                            'CClave' => $this->_options['cclave'],
                            'CIPS' => (string)$CIP)));
        
        $info = $info->ConsultarCIPResult;
        return $info;
    }
    
    /*
     * 
     */
    public function consultarSolicitudPago($xml)
    {
        
        if (gettype($xml) == 'integer'){ 
            $xml = '<?xml version="1.0" encoding="utf-8" ?><ConsultarPago> <idResSolPago>'.$xml.'</idResSolPago></ConsultarPago>';
        }
        
        $info = $this->_loadService('ConsultarSolicitudPago',
                        array( 'request' =>
                        array('cServ' => $this->_options['apiKey'],
                            'CClave' => $this->_crypto->signer($xml),
                            'Xml' => $this->_crypto->encrypt($xml))));                      
        $info = $info->ConsultarSolicitudPagoResult;
        
        if ($info->Estado != 1) throw new Exception('Pago Efectivo : ' . $info->Mensaje);
        return simplexml_load_string($this->_crypto->decrypt($info->Xml));
    }
    
    public function desencriptarData($string)
    {
        return $this->_crypto->decrypt($string);
    }
    
    //Adicional para generar un log - pruebas locales de notificacion
    //Para generar una nueva linea en el archivo de LOG
        public function addRowFileLog($file, $data){
            $fp = fopen($file, 'a+') or die ("Error opening file in write mode!");  
            
            fwrite($fp, str_pad($data, 55));            
            fwrite($fp, "\n\r");
            fclose($fp);
        }
}
