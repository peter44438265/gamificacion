<?php

class Cit_Util {

    private static $_contentid;
    const key='E(n7B^0R]Jhug<z1w?$k:bnI';
    protected $_filterSEO;

    static function getId($param) {
        if (empty(self::$_contentid[$param])) {
            self::$_contentid[$param] = rand(0, 10000000000);
        }
        return self::$_contentid[$param];
    }

    static function getCitIdHost() {
        return Zend_Registry::get('config')->citid->host;
    }

    static function getEntorno() {
        return 'http://' . Zend_Registry::get('config')->entorno->prefijo;
    }

    static function _parseUrl() {
        $uri = explode('/', $_SERVER["REQUEST_URI"]);
        return $uri[1];
    }

    static function generaCookieUsId($usuarioId, $data) {
        $usId = $usuarioId;
        if (empty($usId)) {
            return FALSE;
        }
        try {
            $tiempoExpiracion = time() + 60 * 60 * 24 * 30;
            $request = new Zend_Controller_Request_Http();
            $server = $request->getServer();
            $serverHostArray = explode('.', $server['HTTP_HOST']);
            $serverHostArray = array_reverse($serverHostArray);
            $dominio = $serverHostArray[1] . '.' . $serverHostArray[0];
            $secret = self::encriptar(Zend_Json::encode($data) . '***');
            setcookie('us_id', $secret, $tiempoExpiracion, '/', $dominio);
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    static function generaCookieSuscripcion($correo, $ciudad) {
        try {
            $tiempoExpiracion = time() + 60 * 60 * 24 * 30;
            $request = new Zend_Controller_Request_Http();
            $server = $request->getServer();
            $serverHostArray = explode('.', $server['HTTP_HOST']);
            $serverHostArray = array_reverse($serverHostArray);
            $dominio = $serverHostArray[1] . '.' . $serverHostArray[0];
            $data = array('correo' => $correo, 'ciudad' => $ciudad);
            $secret = Zend_Json::encode($data);
            setcookie('suscripcion', $secret, $tiempoExpiracion, '/', $dominio);
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    static function getCookieSuscripcion() {
        $responce = false;
        if (!empty($_COOKIE['suscripcion'])) {
            $responce = true;
        }
        return $responce;
    }

    static function _getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * description
     * @param paramType paramName paramDescription
     * @uses class::name()
     * @return returnType returnDescription
     */
    static function arrayToObject($array) {
        if (is_array($array)) {
            return (object) array_map(array('Cit_Utils', 'arrayToObject'),
                    $array);
        } else {
            return $array;
        }
    }

    /**
     * description
     * @param paramType paramName paramDescription
     * @uses class::name()
     * @return returnType returnDescription
     */
    static function toSEO($url) {
        if (!isset($this->_filterSEO)) {
            $this->_filterSEO = new Cit_Filter_Alnum();
        }
        return $this->_filterSEO->filter(trim($url), '-');
    }

    /**
     * Funcón para encritar una cadena
     * @param string $msg Cadena a encriptar
     * @uses Cit_Utils::encrypt()
     * @return string Cadena encriptada
     */
    static function encrypt($msg) {                          # return iv+ciphertext+mac
        return hash('sha256', $msg, false);
    }

    /**
     * Genera un código Hash
     * @param boolean $returnLast Indica si desea capturar el ultimo hash generado
     * @return string Codigo Hash
     */
    static function hashCode($returnLast=false) {
        $session = new Zend_Session_Namespace('HashCode');
        if (!$returnLast) {
            $session->hashCode = md5(time());
        }
        return $session->hashCode;
    }

    static function encriptar($cadena, $clave = self::key) {
        $cifrado = MCRYPT_RIJNDAEL_256;
        $modo = MCRYPT_MODE_ECB;
        return mcrypt_encrypt($cifrado, $clave, $cadena, $modo,
                mcrypt_create_iv(mcrypt_get_iv_size($cifrado, $modo), MCRYPT_RAND)
        );
    }

    static function desencriptar($cadena, $clave = self::key) {
        $cifrado = MCRYPT_RIJNDAEL_256;
        $modo = MCRYPT_MODE_ECB;
        return mcrypt_decrypt($cifrado, $clave, $cadena, $modo,
                mcrypt_create_iv(mcrypt_get_iv_size($cifrado, $modo), MCRYPT_RAND)
        );
    }

    /**
     * registra una cookie con las suscripciones del usuario
     * 
     * @param array $usuario array('us_id' => $value)
     * @return TRUE | FALSE 
     */
    static function encriptar3DES($key, $data) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $data = str_repeat(' ', $iv_size) . $data;
        $secret = mcrypt_encrypt(MCRYPT_3DES, $key, $data
                        , MCRYPT_MODE_CBC, $iv);
        return $secret;
    }

    static function getPathMask($fid = NULL, $product = NULL, $type = 'sidebar') {
        $types = array('sidebar' => 'thumbw90', 'portada' => 'thumbh460');
        if (!is_null($product) && !is_null($fid)) {
            $product = trim($product);
            $basePath = APPLICATION_ELEMENTOS_DIR . '/' . strtolower($product) . '/';
            $filename = str_pad($fid, 8, "0", STR_PAD_LEFT);
            $dir_split_file = preg_split('//', substr($filename, 0, strlen($filename) - 3), -1, PREG_SPLIT_NO_EMPTY);
            $scheme_dir = implode('/', $dir_split_file);
            return $basePath . $scheme_dir . '/' . $types[$type];
        }
    }

    static function _detect_mobile() {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';

        $mobile_browser = '0';

        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', $agent))
            $mobile_browser++;

        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
            $mobile_browser++;

        if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
            $mobile_browser++;

        if (isset($_SERVER['HTTP_PROFILE']))
            $mobile_browser++;

        $mobile_ua = substr($agent, 0, 4);
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'xda', 'xda-'
        );

        if (in_array($mobile_ua, $mobile_agents))
            $mobile_browser++;

        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
            $mobile_browser++;

        // Pre-final check to reset everything if the user is on Windows
        if (strpos($agent, 'windows') !== false)
            $mobile_browser = 0;

        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos($agent, 'windows phone') !== false)
            $mobile_browser++;

        if ($mobile_browser > 0)
            return true;
        else
            return false;
    }

    /**
     * retorna el dominio base.<br>
     * ejm: dominio base para dev.quioscodigital.pe<br>
     * <br>dominiobase = quioscodigital.pe
     *
     * @return string $dominio
     *
     */
    public static function traerDominioBase() {
        $serverHostArray = explode('.', $_SERVER['HTTP_HOST']);
        $serverHostArray = array_reverse($serverHostArray);
        $dominio = $serverHostArray[1] . '.' . $serverHostArray[0];

        return $dominio;
    }

    /**
     * devuelve la cantidad de días que existen entre 2 fechas
     *
     * @param Zend_Date $desde
     * @param Zend_Date $hasta
     * @return int $dias
     */
    public static function diasEntreFechas(Zend_Date $desde, Zend_Date $hasta) {
        $dias = $hasta->toString(Zend_Date::TIMESTAMP) - $desde->toString(Zend_Date::TIMESTAMP);
        $dias = (int) ($dias / (60 * 60 * 24));

        return $dias;
    }

    public static function limpiarString($cadena) {
        $cadena = trim($cadena);
        $cadena = strtr($cadena,
                        "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
                        "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
        $cadena = strtr($cadena, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz");
        $cadena = preg_replace('#([^.a-z0-9]+)#i', '-', $cadena);
        $cadena = preg_replace('#-{2,}#', '-', $cadena);
        $cadena = preg_replace('#-$#', '', $cadena);
        $cadena = preg_replace('#^-#', '', $cadena);
        return $cadena;
    }

    public static function setArray($data, $a) {
        if (empty($data)) {
            foreach ($data as $value) {
                
            }
        }
    }

    public static function validaImagen($imagen, $id) {
        $defaul = Cit_Server::getContent()->host . 'imagenes/default.jpg';
        if (!empty($imagen[$id])) {
            $defaul = $imagen[$id];
        }
        return $defaul;
    }

    public static function getUri() {
        $controller = new Zend_Controller_Action_Helper_Redirector();
        $cnt = $controller->getRequest()->getParam('patch', null);
        $uri = $_SERVER['REQUEST_URI'];
        $responce = $cnt;
        if (empty($cnt)) {
            $responce = base64_encode($uri);
        }
        return $responce;
    }

    public static function decodeUri($uri) {
        if (!empty($uri)) {
            $uri = base64_decode($uri);
        } else {
            $uri = '/';
        }
        return $uri;
    }

    /**
     * Intenta enviar un correo con información de error, si falla se registra
     * en el log de errores.
     *
     * @param string $mensaje
     * @param Exception $e Opcional
     */
    public static function notificarError($mensaje, Exception $e = null, $archivoNombre = NULL) {
        $negocio = '';
        $to = '';
        $prefijo = '';
        if (Zend_Registry::isRegistered('config')) {
            if (isset(Zend_Registry::get('config')->proyecto->nombre)) {
                $negocio = Zend_Registry::get('config')->proyecto->nombre;
            }

            if (isset(Zend_Registry::get('config')->entorno->prefijo)) {
                $prefijo = Zend_Registry::get('config')->entorno->prefijo;
                $negocio = $prefijo . $negocio;
            }

            if (isset($_SERVER['HTTP_HOST'])) {
                $negocio .= ' URL:' . $_SERVER['HTTP_HOST'];
            } else {
                if (!empty($archivoNombre)) {
                    $negocio = ' CRON - ' . $archivoNombre;
                } else {
                    $negocio = ' CRON';
                }
            }

            if (isset(Zend_Registry::get('config')->mail->mailDevelopers)) {
                $to = Zend_Registry::get('config')->mail->mailDevelopers;
            }
        }

        $excepcion = '';
        if ($e instanceOf Exception) {
            $exceptionArray = array();
            $exceptionArray['message'] = $e->getMessage();
            $exceptionArray['Trace'] = $e->getTraceAsString();
            ob_start();
            var_dump($exceptionArray);
            $excepcion = ' Detalle: ' . ob_get_contents();
            $mensaje .= $excepcion;
            ob_clean();
        }
        $correcto = false;
        $exc = null;
        if (!empty($to)) {
            $correcto = true;
            try {
                Cit_Mail::enviar('ALERTA en: ' . $negocio . ' => ' . APPLICATION_ENV,
                                $mensaje,
                                $to);
            } catch (Exception $e) {
                $exc = $e;
                $correcto = false;
            }
        }
        if (!$correcto) {
            self::registrarError($mensaje, $exc, $archivoNombre);
        }
    }

    /**
     * Intenta enviar un correo con información de error, si falla se registra
     * en el log de errores.
     *
     * @param string $mensaje
     * @param Exception $e Opcional
     */
    public static function notificar($asunto, $mensaje, Exception $e = null, $archivoNombre = NULL) {
        $negocio = '';
        $to = '';
        $prefijo = '';
        if (Zend_Registry::isRegistered('config')) {
            if (isset(Zend_Registry::get('config')->proyecto->nombre)) {
                $negocio = Zend_Registry::get('config')->proyecto->nombre;
            }

            if (isset(Zend_Registry::get('config')->entorno->prefijo)) {
                $prefijo = Zend_Registry::get('config')->entorno->prefijo;
                $negocio = $prefijo . $negocio;
            }

            if (isset($_SERVER['HTTP_HOST'])) {
                $negocio .= ' URL:' . $_SERVER['HTTP_HOST'];
            } else {
                if (!empty($archivoNombre)) {
                    $negocio = ' CRON - ' . $archivoNombre;
                } else {
                    $negocio = ' CRON';
                }
            }

            if (isset(Zend_Registry::get('config')->mail->mailDevelopers)) {
                $to = Zend_Registry::get('config')->mail->mailDevelopers;
            }
        }

        $excepcion = '';
        if ($e instanceOf Exception) {
            $exceptionArray = array();
            $exceptionArray['message'] = $e->getMessage();
            $exceptionArray['Trace'] = $e->getTraceAsString();
            ob_start();
            var_dump($exceptionArray);
            $excepcion = ' Detalle: ' . ob_get_contents();
            $mensaje .= $excepcion;
            ob_clean();
        }
        $correcto = false;
        $exc = null;
        if (!empty($to)) {
            $correcto = true;
            try {
                Cit_Mail::enviar('INFO - ' . $negocio . ' ' . $asunto,
                                $mensaje,
                                $to);
            } catch (Exception $e) {
                $exc = $e;
                $correcto = false;
            }
        }
        if (!$correcto) {
            self::registrarError($mensaje, $exc, $archivoNombre);
        }
    }

    /**
     * datos que se guardaran en la carpeta log "x.txt"
     * @param string $mensaje
     * @param Exception $e Opcional
     *
     */
    public static function registrarError($mensaje, Exception $e = NULL, $archivoNombre = NULL) {
        if ($archivoNombre == NULL) {
            $path = array(APPLICATION_PATH, '..', 'var', 'log', 'application.log');
        } else {
            $path = array(APPLICATION_PATH, '..', 'var', 'log', $archivoNombre . '-'
                . Zend_Date::now()->toString('Ymd') . '.log');
        }

        $excepcion = '';
        if ($e instanceOf Exception) {
            $exceptionArray = array();
            $exceptionArray['message'] = $e->getMessage();
            $exceptionArray['Trace'] = $e->getTraceAsString();
            ob_start();
            var_dump($exceptionArray);
            $excepcion = "\n" . ' Detalle: ' . ob_get_contents();
            ob_clean();
        }
        self::registrar(implode(DIRECTORY_SEPARATOR, $path),
                        'Error: ' . $mensaje . $excepcion . "\n\nFINAL ERROR\n\n");
    }

    public static function registrar($archivo, $mensaje, Exception $excepcion = null) {
        try {
            $f = @fopen($archivo, 'a');
            if ($f !== false) {
                if ($excepcion instanceOf Exception) {
                    $exceptionArray = array();
                    $exceptionArray['message'] = $excepcion->getMessage();
                    $exceptionArray['Trace'] = $excepcion->getTraceAsString();
                    ob_start();
                    var_dump($exceptionArray);
                    $mensaje .= ' Detalle: ' . ob_get_contents();
                    ob_clean();
                }
                fwrite($f, date('Y/m/d  H:i:s') . ' => ' . $mensaje . "\n");
                unset($mensaje);
                fclose($f);
            } else {
                throw new Exception('No se pudo crear el archivo "'
                        . $archivo . '"');
            }
            unset($f);
        } catch (Exception $e) {
            throw new Exception('Error: registro de Log fallo: '
                    . self::procesarExcepcion($e));
        }
    }

    /**
     * convierte el trace de una ecepcion en una cadena para su impresion.
     *
     * @param Exception $e
     * @return string
     */
    public static function procesarExcepcion(Exception $e) {
        $detalles = $e->getTrace();
        $detalle = 'Detalle: (' . $e->getMessage() . ')' . "\n";
        foreach ($detalles as $linea) {
            $fileTem = isset($linea['file']) ? $linea['file'] : '--';
            $lineTem = isset($linea['line']) ? $linea['line'] : '--';
            $detalle.= $fileTem . ':' . $lineTem . "\n";
            if (isset($linea['class'])) {
                $detalle.= 'clase: ' . $linea['class'];
            }
            if (isset($linea['function'])) {
                $detalle.= ' funcion: ' . $linea['function'];
            }
            $detalle.= "\n";
            if (isset($linea['args'])) {
                $detalle.= 'argumentos: ';
                if (is_array($linea['args'])) {
                    foreach ($linea['args'] as $argumento) {
                        if (is_array($argumento)) {
                            if (count($argumento) > 10) {
                                $detalle.= '(array(' . count($argumento) . ' elens),';
                            } else {
                                $detalle.= '(' . @implode(', ', $argumento) . '),';
                            }
                        } else {
                            $esClase = false;
                            if (is_object($argumento)) {
                                $esClase = get_class($argumento);
                            }
                            if ($esClase === false) {
                                $detalle.= $argumento . ', ';
                            } else {
                                $detalle.= $esClase . ', ';
                                if (isset($argumento->id)) {
                                    $detalle.= ' id: ' . $argumento->id . ', ';
                                }
                            }
                        }
                    }
                } else {
                    $detalle.= $linea['args'];
                }
            }
        }
        return $detalle;
    }

    public static function detect_ie6() {
        if (stristr($_SERVER['HTTP_USER_AGENT'], "msie 6")) {
            return true;
        } else
            return false;
    }

    public static function detect_ie7() {
        if (stristr($_SERVER['HTTP_USER_AGENT'], "msie 7")) {
            return true;
        } else
            return false;
    }

    public static function slug($str) {

        $before = array(
            'àáâãäåòóôõöøèéêëðçìíîïùúûüñšž',
            '/[^a-z0-9\s]/',
            array('/\s/', '/--+/', '/---+/')
        );

        $after = array(
            'aaaaaaooooooeeeeeciiiiuuuunsz',
            '',
            '-'
        );

        $str = strtolower($str);
        $str = strtr($str, $before[0], $after[0]);
        $str = preg_replace($before[1], $after[1], $str);
        $str = trim($str);
        $str = preg_replace($before[2], $after[2], $str);

        return $str;
    }
    public static function parseCombo($index, $value, $data){
        $responce = array();
        if(!empty($data)){
            foreach($data as $item){
                $responce[$item[$index]] = $item[$value];
            }
        }
        return $responce;
    }
}