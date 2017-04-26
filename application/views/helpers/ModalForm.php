<?php
class Zend_View_Helper_ModalForm extends Zend_View_Helper_Abstract
{
    public function ModalForm()
    {
        return $this->view->render('modal-form.phtml');
    }
}