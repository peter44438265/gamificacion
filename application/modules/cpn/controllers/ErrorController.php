<?php

class Cpn_ErrorController extends Zend_Controller_Action {

    public function errorAction() {
        $this->_helper->layout->disableLayout();
        $errors = $this->_getParam('error_handler');
        if (empty($errors)) {
            $this->view->message = 'Lo sentimos no tiene acceso a esta página';
            return;
        }
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->mensajeFormat = '<div id="container">
                      <p></p>
                      <h3>Página no encontrada</h3>
                      <p>La página que buscas no existe, probablemente el enlace que usaste es erróneo.<br/>

                      </p>
                      <p>&laquo volver a la <a href="/">página principal</a></p>
                    </div>';
                return;
                break;
            default:
                // application errore
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        $mensaje = $this->_prepararMensajedeError($errors);
        if (APPLICATION_ENV == 'production') {
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'NO_HTTP_HOST';
            $asunto = 'ERROR :' . $host . ' : Error en Aplicación';
            $to = '';
            if (isset(Zend_Registry::get('config')->mail->mailDevelopers)) {
                $to = Zend_Registry::get('config')->mail->mailDevelopers;
            }
            try {
                //Cit_Mail::enviar($asunto, $mensaje, $to);
                Cit_Mail::enviar($asunto, $mensaje, $to, '', '', '', TRUE);
            } catch (Exception $e) {
                Cit_Log::crit($mensaje);
            }
        } else {
            Cit_Log::crit($mensaje);
        }

        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        $this->view->request = $errors->request;
    }

    private function _prepararMensajedeError($errors) {
        $forwarded = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? " - $_SERVER[HTTP_X_FORWARDED_FOR];" : ";";
        $mensaje = "[[ORIGEN]]: \r\n\r\n $_SERVER[REMOTE_ADDR]$forwarded "
                . @strftime("%A, %d de %B del %Y %T  %z") . "\r\n\r\n";
        $mensaje .= "[[URL]]: \r\n\r\n" . $this->getRequest()->getRequestUri() . "\r\n\r\n";
        $mensaje .= "[[MENSAJE DE ERROR]]: \r\n\r\n" . $errors->exception->getMessage() . "\r\n\r\n";

        $traza = $errors->exception->getTraceAsString();

        $mensaje .= "[[TRAZA]]: \r\n\r\n" . $traza . "\r\n\r\n";

        $paramPost = '';
        $post = $this->getRequest()->getPost();
        foreach ($post as $key => $val) {
            $paramPost .= "$key: $val\r\n";
        }

        if (!empty($paramPost)) {
            $mensaje .= "[[POST]]: \r\n\r\n $paramPost";
        }

        return $mensaje;
    }

}

