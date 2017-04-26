<?php

class App_PagoEfectivo extends Zend_Soap_Client {
    const ERR_SIN_CONFIGURACION = 'No se pudo obtener la configuración necesaria.';
    const ERR_SIN_RESPUESTA = 'No se obtuvo respuesta.';

    private $_config = NULL;
    private $_parametros = NULL;

    public function __construct() {
        if (Zend_Registry::isRegistered('pefectivo')) {
            if (isset(Zend_Registry::get('pefectivo')->pe)) {
                $this->_config = Zend_Registry::get('pefectivo')->pe;
            }
        }
        if (empty($this->_config)) {
            throw new Zend_Soap_Client_Exception(self::ERR_SIN_CONFIGURACION);
        }
    }

    /**
     * @return mixed
     */
    public function getParametro($nombre) {
        if (isset($this->_config->$nombre)) {
            return $this->_config->$nombre;
        }
        return NULL;
    }

    private function _registrarLog($paramtros) {
        if (Zend_Registry::isRegistered('Log')) {
            ob_start();
            var_dump($paramtros);
            $mensaje = ob_get_contents();
            ob_end_clean();
            Zend_Registry::get('Log')->log(get_class($this) . ' => ' . $mensaje, Zend_Log::CRIT);
        }
    }

    /* FUNCIONES PAGO EFECTIVO */

    /**
     * @return mixed
     */
    public function blackBox($medioPago, $transaccionId, $monto, $perfilPagoId, $urlOk, $urlError, $completarUrl = TRUE) {
        $cadenaDatos = array(
            'MerchantID=' . $this->_config->merchantId,
            'OrderId=' . $transaccionId,
            'Amount=' . $monto,
            'UserId=' . $perfilPagoId
        );
        if ($completarUrl) {
            $cadenaDatos[] = 'UrlOk=' . $this->_config->urlConfirmacion . '/' . $urlOk . '/';
            $cadenaDatos[] = 'UrlError=' . $this->_config->urlConfirmacion . '/' . $urlError . '/';
        } else {
            $cadenaDatos[] = 'UrlOk=' . $urlOk . '/';
            $cadenaDatos[] = 'UrlError=' . $urlError . '/';
        }
        $cadenaDatos[] = 'mp=' . $medioPago;

        $cadenaDatos = implode('|', $cadenaDatos);
        return $this->_blackBox($cadenaDatos);
    }

    public function blackBoxXMl($cadena) {
        return $this->_blackBox($cadena);
    }

    /**
     * @param string $cadena
     * @return mixed
     * @throws Zend_Soap_Client_Exception cuando no hubo respuesta
     */
    private function _blackBox($cadena) {
        $this->setWsdl($this->_config->urlEncripta);
        $this->setHttpLogin($this->_config->login);
        $this->setHttpPassword($this->_config->password);

        $resultado = $this->__call('BlackBox', array(array('Cad' => $cadena)));
        if (empty($resultado->BlackBoxResult)) {
            $this->_registrarLog(array(
                'accion' => 'BlackBox',
                'parametros' => $cadena,
                'resultado' => $resultado
            ));
            throw new Zend_Soap_Client_Exception(self::ERR_SIN_RESPUESTA);
        }
        return $resultado->BlackBoxResult;
    }

    /**
     * @param string $email
     * @param string $password
     * @param mixed $xml
     * @return array|false
     * @throws Zend_Soap_Client_Exception cuando no hubo respuesta
     */
    public function generarCIP($email, $password, $xml) {
        $this->setWsdl($this->_config->urlCIP);
        $this->setHttpLogin($this->_config->login);
        $this->setHttpPassword($this->_config->password);
        //$this->setSoapVersion(SOAP_1_2);

        $pagoEfectivoParametrosEnc = array(
            'request' => array(
                'CAPI' => $this->_config->CAPI,
                'CClave' => $this->_config->CClave,
                'Email' => $email,
                'Password' => $password,
                'Xml' => $xml
            )
        );

        $this->_parametros = $pagoEfectivoParametrosEnc;
        $resultado = $this->__call("GenerarCIP", array($pagoEfectivoParametrosEnc));
        if (!empty($resultado->GenerarCIPResult)) {
            $resultado = $resultado->GenerarCIPResult;
            $respuesta = array(
                'Estado' => (string) $resultado->Estado,
                'CIP' => (string) $resultado->CIP,
                'Mensaje' => (string) $resultado->Mensaje,
                'InformacionCIP' => (string) $resultado->InformacionCIP,
            );
        } else {
            $this->_registrarLog(array(
                'accion' => 'generarCIP',
                'parametros' => $pagoEfectivoParametrosEnc,
                'resultado' => $resultado
            ));

            throw new Zend_Soap_Client_Exception(self::ERR_SIN_RESPUESTA);
        }

        return $respuesta;
    }

    public function getCIPEncripta($token) {
        $datos = array(
            'cip=' . $token,
            'capi=' . $this->_config->CAPI,
            'cclave=' . $this->_config->CClave
        );
        $cadena = implode('|', $datos);
        return $this->_blackBox($cadena);
    }

    public function getUltimosParametros() {
        return $this->_parametros;
    }

    /**
     * @return array
     */
    public function blackBoxDecrypta($cadena) {
        $cadenaDatos = $this->_blackBoxDecrypta($cadena);

        $arrayDatos = array();
        $arrayPart = explode('|', $cadenaDatos);
        foreach ($arrayPart as $id => $dato) {
            $part = explode('=', $dato);
            $arrayDatos[$part[0]] = $part[1];
        }
        return $arrayDatos;
    }

    /**
     * @param string $cadena
     * @return mixed
     * @throws Zend_Soap_Client_Exception cuando no hubo respuesta
     */
    private function _blackBoxDecrypta($cadena) {
        $this->setWsdl($this->_config->urlEncripta);
        $this->setHttpLogin($this->_config->login);
        $this->setHttpPassword($this->_config->password);

        $resultado = $this->__call('BlackBoxDecrypta', array(array('Cad' => $cadena)));
        if (empty($resultado->BlackBoxDecryptaResult)) {
            $this->_registrarLog(array(
                'accion' => 'BlackBoxDecrypta',
                'parametros' => $cadena,
                'resultado' => $resultado
            ));
            throw new Zend_Soap_Client_Exception(self::ERR_SIN_RESPUESTA);
        }
        return $resultado->BlackBoxDecryptaResult;
    }

}