<?php

class Cit_Html
{

    const HTTP = 'http://';

    static function getMenu()
    {
        $data = Zend_Json::decode(file_get_contents(Zend_Registry::get('config')->citid->sistemas));

        foreach ($data as $indice => $value) {
            $data[$indice]['sis_url'] = self::HTTP . Zend_Registry::get('config')->entorno->prefijo . $value['sis_url']
                    . '/' . Zend_Registry::get('CDusuario')->colegio['col_url_colegio'];
        }
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->menu = $data;
        echo $view->render('menu.phtml');
    }

    static function getColegios($tipo = null)
    {

        $data = Zend_Json::decode(file_get_contents(Zend_Registry::get('citid')->citid->host . '/portada/colegios'));
        $demo = array();
        foreach ($data as $indice => $value) {


            if(!empty($value['col_logo'])):
                $data[$indice]['sis_url'] =
                Zend_Registry::get('citid')->citid->host
                . '/' . $value['col_url_colegio'];
                $data[$indice]['col_logo'] =
                Zend_Registry::get('citid')->citid->host
                . '/images/' . $value['col_id'] . '/logos/' . $value['col_logo'];
            else:
                $data[$indice]['sis_url'] =
                Zend_Registry::get('citid')->citid->host
                . '/' . $value['col_url_colegio'];
                $data[$indice]['col_logo'] =
                Zend_Registry::get('citid')->citid->host
                . '/images/default/logos.png';
            endif;

            if ($value['col_situacion'] == 2) {
                $demo = $data[$indice];
            }
        }
        return (object) array('colegios' => $data, 'demo' => $demo);
    }

}