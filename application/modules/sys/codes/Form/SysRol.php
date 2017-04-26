<?php
/**
 * @author www.likerow.com(likerow@gmail.com)
 */
class Form_SysRol extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
        $this->setAttribs(array('class' => 'form-horizontal',
              'id' => 'form-validate',
              'enctype' => 'multipart/form-data'));
        
              $element = new Zend_Form_Element_Text('rol_id');
              $element->setAttribs(array('class' => 'span9'));
              $element->setLabel('Rol Id')
              ->setRequired(true)
              ->addValidator('NotEmpty')
              ->addFilter('StringTrim');
              $this->addElement($element);$element = new Zend_Form_Element_Text('rol_rol_id');
              $element->setAttribs(array('class' => 'span9'));
              $element->setLabel('Rol Rol Id')
              ->setRequired(true)
              ->addValidator('NotEmpty')
              ->addFilter('StringTrim');
              $this->addElement($element);$element = new Zend_Form_Element_Text('rol_nombre');
              $element->setAttribs(array('class' => 'span9'));
              $element->setLabel('Rol Nombre')
              ->setRequired(true)
              ->addValidator('NotEmpty')
              ->addFilter('StringTrim');
              $this->addElement($element);$this->addElement('button', 'submit', array(
              'label' => 'Guardar!',
              'type' => 'submit',
              'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
              ));
        
              $this->addElement('button', 'reset', array(
              'label' => 'Cancelar',
              'type' => 'reset',
              'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_DANGER,
              ));
              $this->addDisplayGroup(
              array('submit', 'delete', 'reset'),
              'actions',
              array(
              'disableLoadDefaultDecorators' => true,
              'decorators' => array('Actions')
              )
              );
              $this->clearDecorators();
              $this->addDecorator('FormElements')
              ->addDecorator('HtmlTag', array('tag' => '<div>', 'class' => ''))
              ->addDecorator('Form');
        
              $this->setElementDecorators(
              array(
              array('ViewHelper'),
              array('Errors'),
              array('Description'),
              array('Label', array('separator' => ' ', 'class' => 'form-label span3')),
              array(array('row2' => 'HtmlTag'), array('tag' => '<div>', 'class' => 'row-fluid'),),
              array(array('row1' => 'HtmlTag'), array('tag' => '<div>', 'class' => 'span12')),
              array(array('data' => 'HtmlTag'), array('tag' => '<div>', 'class' => 'form-row row-fluid')),
              )
              );
    }


}
