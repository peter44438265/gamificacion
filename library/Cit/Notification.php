<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Cit_Notification {
    const ERROR = 'error';
    const SUCCESS = 'success';
    const INFO = 'info';
    private static $_types = array(self::ERROR, self::SUCCESS, self::INFO);

    public static function _($tipe, $message = null) {
        if (!in_array($tipe, self::$_types)) {
            $message = 'El tipo de error no esta disponible.';
        }
        if ($tipe == self::SUCCESS) {
            if (empty($message)) {
                $message = 'Se proceso correctamente.';
            }
        }
        if ($tipe == self::ERROR) {
            if (empty($message)) {
                $message = 'Se produjeron errores durante el proceso.';
            }
        }
        if ($tipe == self::INFO) {
            if (empty($message)) {
                $message = 'Se producjeron warning en el proceso.';
            }
        }
        return '$.pnotify(' . Zend_Json::encode(array('title' => 'Red de salud',
            'text' => $message,
            'type' => $tipe,
            'icon' => true)) . ')';
    }

}

?>
