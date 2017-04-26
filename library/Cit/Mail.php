<?php

class Cit_Mail extends Zend_Mail {

    /**
     *
     * @var String
     */
    protected static $_fromEmail = '';
    /**
     *
     * @var String
     */
    protected static $_fromName = '';

    public static function setFromEmail($email) {
        self::$_fromEmail = $email;
    }

    public static function setFromName($name) {
        self::$_fromName = $name;
    }

    /**
     * @param string $asunto
     * @param string $mensaje
     * @param string $to Correos a quienes se envia. Deben estar en una cadena unidos con ','
     *              <br>Ejm: correo1@algo.com,correo2@algo.com
     * @param string $bcc Opcional Correos a quienes se envia una copia oculta. Deben estar en una cadena unidos con ','
     *              <br>Ejm: correo1@algo.com,correo2@algo.com
     * @param string $fromName Opcional
     * @param string $fromName Opcional
     * @param boolean $html Opcional Indica si el $mensaje se debe enviar como html. Por defecto es false
     * @param string $cc Opcional Corres a quienes se envía una copia. Deben estar en una cadena unidos con ','
     *              <br>Ejm: correo1@algo.com,correo2@algo.com
     *
     * @exception Zend_Exception;
     */
    public static function enviar($asunto, $mensaje, $to, $bcc = '', $fromEmail = '', $fromName = '', $html = false, $cc = '', $adjuntos = NULL, $pathAdjunto = NULL) {
        self::_initTrasport();
        $mail = new Zend_Mail();
        $mail->setSubject(Cit_String::parseString($asunto)->toISO()->__toString());
        if ($html) {
            $mail->setBodyHtml(Cit_String::parseString($mensaje)->toISO()->__toString());
        } else {
            $mail->setBodyText(Cit_String::parseString($mensaje)->toISO()->__toString());
        }

        if ($to != '') {
            $emails = explode(',', $to);
            foreach ($emails as $email) {
                $mail->addTo(trim($email));
            }
        } else {
            return;
        }

        if ($bcc != '') {
            $emails = explode(',', $bcc);
            foreach ($emails as $email) {
                $mail->addBcc(trim($email));
            }
        }

        if ($cc != '') {
            $emails = explode(',', $cc);
            foreach ($emails as $email) {
                $mail->addCc(trim($email));
            }
        }

        $fromEmail = !empty($fromEmail) ? $fromEmail : self::$_fromEmail;
        $fromName = !empty($fromName) ? $fromName : self::$_fromName;
        $from = self::getDefaultFrom();
        if ($from != null) {
            if (empty($fromEmail)) {
                $fromEmail = $from['email'];
            }
            if (empty($fromName)) {
                $fromName = $from['name'];
            }
        }
        $mail->setFrom($fromEmail, Cit_String::parseString($fromName)->toISO()->__toString());

        if ($adjuntos != NULL && $pathAdjunto != NULL) {
            $adjuntos = explode(',', $adjuntos);
            foreach ($adjuntos as $adjunto) {
                $miAdjunto = $pathAdjunto . '/' . $adjunto;
                $fileContents = file_get_contents($miAdjunto);
                $file = $mail->createAttachment($fileContents);
                $file->filename = $adjunto;
            }
        }

        try {
            $mail->send();
        } catch (Exception $e) {
            throw $e;
        }
        self::_endTrasport();
    }

    /**
     * @return boolean
     */
    protected static function _initTrasport() {
        // Verificando existencia de resource transport
        $transport = Zend_Mail::getDefaultTransport();
        if ($transport instanceOf Zend_Mail_Transport_Smtp) {
            return false;
        }

        $config = Zend_Registry::get('config');
        if (!isset($config->resources->mail)) {
            return false;
        }
        if (!isset($config->resources->mail->transport)) {
            return false;
        }
        if (isset($config->resources->mail->transport->type)) {
            if ($config->resources->mail->transport->type != 'smtp') {
                return false;
            }
        }
        $port = 25;
        if (isset($config->resources->mail->transport->port)) {
            $port = $config->resources->mail->transport->port;
        }

        $transport = null;
        if (Zend_Registry::isRegistered('mailTransport')) {
            $transport = Zend_Registry::get('mailTransport');
        }

        if (!($transport instanceOf Zend_Mail_Transport_Smtp)) {
            $paramsTransport = array(
                'port' => $port
            );

            if (isset($config->resources->mail->transport->auth)) {
                $paramsTransport['auth'] = $config->resources->mail->transport->auth;
            }

            if (isset($config->resources->mail->transport->username)) {
                $paramsTransport['username'] = $config->resources->mail->transport->username;
            }

            if (isset($config->resources->mail->transport->password)) {
                $paramsTransport['password'] = $config->resources->mail->transport->password;
            }

            if (isset($config->resources->mail->transport->ssl)) {
                $paramsTransport['ssl'] = 'ssl';
            }
            $transport = new Zend_Mail_Transport_Smtp(
                            $config->resources->mail->transport->host,
                            $paramsTransport);
        }
        if ($transport instanceOf Zend_Mail_Transport_Smtp) {
            Cit_Mail::setDefaultTransport($transport);
            Cit_Mail::setFromEmail($config->resources->mail->defaultFrom->email);
            Cit_Mail::setFromName($config->resources->mail->defaultFrom->name);
            Zend_Registry::set('mailTransport', $transport);
        }
        return true;
    }

    protected static function _endTrasport() {
        if (Zend_Registry::isRegistered('mailTransport')) {
            $trasnport = Zend_Registry::get('mailTransport');
            if ($trasnport instanceOf Zend_Mail_Transport_Smtp) {
                $coneccion = $trasnport->getConnection();
                try {
                    $coneccion->disconnect();
                    Zend_Registry::set('mailTransport', NULL);
                } catch (Exception $e) {

                }
            }
        }
    }

}