<?php

/**
 * Decorator definition for the submit buttons
 *
 * @category Forms
 * @package Twitter_Bootstrap_Form
 * @subpackage Decorator
 * @author Christian Soronellas <csoronellas@emagister.com>
 */

/**
 * A decorator to render the submit form buttons
 *
 * @category Forms
 * @package Twitter_Bootstrap_Form
 * @subpackage Decorator
 * @author Christian Soronellas <csoronellas@emagister.com>
 */
class Twitter_Bootstrap_Form_Decorator_GridForm extends Zend_Form_Decorator_Abstract {

    /**
     * Render all the buttons
     *
     * @return string
     */
    public function buildButtons() {
        $output = '';
        foreach ($this->getElement() as $element) {
            if ($element !== null && $element instanceof Zend_Form_Element) {
                $element->setDecorators(array(array('ViewHelper')));
                $output .= $element->render();
            }
        }

        return $output;
    }

    /**
     * Renders the content
     *
     * @param  string $content
     * @return string
     */
    public function render($content) {
        return '<div class="row-fluid">
            <div class="span12">
                <div class="row-fluid">
                    <label class="form-label span3 required" for="email">' .$this->getElement()->getName() . '</label>
                    ' . $this->buildButtons() . '
                </div>
            </div>
        </div>';
    }
}
