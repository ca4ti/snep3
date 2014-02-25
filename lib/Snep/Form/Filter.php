<?php
/**
 *  This file is part of SNEP.
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as
 *  published by the Free Software Foundation, either version 3 of
 *  the License, or (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 */

 /**
 * Classe to manager a filter
 *
 * @see Snep_Form_Filter
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2011 OpenS Tecnologia 
 */
class Snep_Form_Filter extends Zend_Form {

    protected $submit;
    protected $reset;

    public function __construct() {
        $config_file = "./default/forms/filter.xml";
        $config = new Zend_Config_Xml($config_file, null, true);
        parent::__construct($config);

        $i18n = Zend_Registry::get("i18n");

        $this->setElementDecorators(
                array(
                    'ViewHelper',
                    'Label'
                )
        );

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'div', 'class'=>'zend_form')),
            'Form'
        ));

        $submit = new Zend_Form_Element_Submit("submit", array("label" => $i18n->translate("Filter")));
        $submit->removeDecorator('DtDdWrapper');
        $this->submit = $submit;

        // Botão Lista Completa
        $reset = new Zend_Form_Element_Button("buttom", array("label" => $i18n->translate("Cancel")));
        ;
        $reset->removeDecorator('DtDdWrapper');
        $this->reset = $reset;

        $this->addElement($submit);
        $this->addElement($reset);
    }

    public function setFieldOptions($options) {
        $campo = $this->getElement('campo');
        $campo->setMultiOptions($options);
    }

    public function setFieldValue($value) {
        $filtro = $this->getElement('filtro');
        $filtro->setValue($value);
    }

    public function setValue($value) {
        $filter_value = $this->getElement('campo');
        $filter_value->setValue($value);
    }

    public function setResetUrl($url) {
        $this->reset->setAttrib("onclick", "location.href='$url'");
    }

}
