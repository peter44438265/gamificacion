<?php

require_once 'Zend/Rest/Server.php';
require_once 'Zend/Session/Namespace.php';

class Cit_Service_Rest extends Zend_Rest_Server
{

    private static $echoResponse = true;
    private $production = true;
    private $format = 'xml';

    /**
     * Handles the REST Request.
     */
    public function setFormat($formato)
    {
        $this->format = $formato;
    }

    public function handle($request = false)
    {

        switch ($this->format) {
            case 'xml':
            case 'json':
            case 'phpserialize':
                break;

            default:
                header('HTTP/1.0 400 Bad Request');
                header('Error: invalid format');
                return;
        }

        $this->returnResponse(true);
        $response = parent::handle($request);

        if (isset($_REQUEST['redirectUri'])) {
            header('Location: ' . $_REQUEST['redirectUri']);
            $this->saveResponse($call, $response);
            if(!empty($request['callback'])){
                echo $request['callback']."(".$response.")";
            }else{
                echo $response;
            }
        } else if (self::$echoResponse) {
            header('Content-Type: text/' . $this->format);
            if(!empty($request['callback'])){
                echo $request['callback']."(".$response.")";
            }else{
                echo $response;
            }
        }
    }

    /**
     * Overridden to just return just the exception
     * 
     * (non-PHPdoc)
     * @see Zend_Rest_Server::fault()
     */
    public function fault($exception = null, $code = null)
    {
        return $exception;
    }

    /**
     * Handles the result in a non xml format.
     * 
     * @param unknown_type $value
     */
    private function handleResult($value)
    {
        $result = array();
        if ($value instanceof Exception) {
            $result['status'] = -1;
            $result['error'][] = $value->getMessage();
            /* $result['status'] = 'failed';
              $result['message'] = $value->getMessage(); */

            if ($this->production == false) {
                $result['stack'] = $value->getTraceAsString();
            }
        } else {
            $result = $value;
            //$result['status'] = 'success';
            //$result['result'] = $value;
        }

        if ($this->format == 'xml') {
            $dom = new DOMDocument('1.0', $this->getEncoding());
            $root = $dom->createElement('response');
            $dom->appendChild($root);
            parent::_structValue($result, $dom, $root);
            return $dom->saveXml();
        } else if ($this->format == 'json') {
            return json_encode($result);
        } else if ($this->format == 'phpserialize') {
            return serialize($result);
        }
        return '';
    }

    /**
     * Override to allow for returning json or phpresialized formats.
     * 
     * (non-PHPdoc)
     * @see Zend_Rest_Server::_handleStruct()
     */
    protected function _handleStruct($value)
    {
        return $this->handleResult($value);
    }

    /**
     * Override to allow for returning json or phpserialized formats.
     * 
     * (non-PHPdoc)
     * @see Zend_Rest_Server::_handleScalar()
     */
    protected function _handleScalar($value)
    {
        return $this->handleResult($value);
    }

    /**
     * Disables the normal outputing.
     */
    public static function disableXmlOutput()
    {
        // this sets it to simply return the response on the function end and not print it out.
        self::$echoResponse = false;
    }

    /**
     * Static call to invoke handling of the request.
     */
    public static function handleCall()
    {
        $s = new Cre8_View_Rest();
        $s->handle();
    }

}