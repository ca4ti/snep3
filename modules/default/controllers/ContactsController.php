<?php

/**
 *  This file is part of SNEP.
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Contacts Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class ContactsController extends Zend_Controller_Action {

    /**
     * List all Contacts
     */
    public function indexAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Contacts")));
        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from(array("n" => "contacts_names"), array("id as ide", "name as nome", "city", "state", "cep"))
                ->join(array("g" => "contacts_group"), 'n.group = g.id')
                ->join(array("p" => "contacts_phone"), 'n.id = p.contact_id')
                ->group("n.id");
                
        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("n.`$field` like '%$query%'");
        }

        $this->view->order = Snep_Order::setSelect($select, array("ide","nome", "name", "city", "state", "cep", "phone"), $this->_request);

        $page = $this->_request->getParam('page');
        $this->view->page = ( isset($page) && is_numeric($page) ? $page : 1 );
        $this->view->filtro = $this->_request->getParam('filtro');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);
        $paginator->setCurrentPageNumber($this->view->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $this->view->contacts = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->PAGE_URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/";

        $opcoes = array("name" => $this->view->translate("Name"),
            "city" => $this->view->translate("City"),
            "state" => $this->view->translate("State"),
            "cep" => $this->view->translate("ZIP Code"));

        $filter = new Snep_Form_Filter();
        $filter->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($opcoes);
        $filter->setFieldValue($this->_request->getPost('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/multiRemove/",
                "display" => $this->view->translate("Remove multiple"),
                "css" => "remove"),
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/import/",
                "display" => $this->view->translate("Import CSV"),
                "css" => "includes"),
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/export/",
                "display" => $this->view->translate("Export CSV"),
                "css" => "back"),
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add/",
                "display" => $this->view->translate("Add Contact"),
                "css" => "include")
        );
    }

    /**
     *  Add Contact
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Contacts"),
                    $this->view->translate("Add")));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("modules/default/forms/contacts.xml"));
        $form->getElement('id')->setValue(Snep_Contacts_Manager::getLastId())->setAttrib('disabled', true);

        $_allGroups = Snep_ContactGroups_Manager::getAll();
        foreach ($_allGroups as $group) {
            $allGroups[$group['id']] = $group['name'];
        }

        if (count($_allGroups)) {
            $form->getElement('group')->setMultiOptions($allGroups);
        }

        $_allState = Snep_Contacts_Manager::getStates();
        foreach ($_allState as $state) {
            $allState[$state['cod']] = $state['name'];
        }

        if (count($_allState)) {
            $form->getElement('state')->setMultiOptions($allState);
        }

        $form = $this->getForm($form);
        $this->view->form = $form;

        if ($this->_request->getPost()) {

            $_POST['id'] = Snep_Contacts_Manager::getLastId();
            $form_isValid = $form->isValid($_POST);

            if (empty($_POST['group'])) {
                $form->getElement('group')->addError($this->view->translate('No group selected'));
                $form_isValid = false;
            }

            if (Snep_Contacts_Manager::get($_POST['id'])) {
                $form->getElement('id')->addError($this->view->translate('Code already exists'));
                $form_isValid = false;
            }

            if ($form_isValid) {

                Snep_Contacts_Manager::add($_POST);
                $numbers = explode(",", $_POST['phoneValue']);
                foreach ($numbers as $key => $phone) {
                    Snep_Contacts_Manager::addNumber($_POST['id'], $phone);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        } else {
            $this->view->dataphone = "phoneObj.addItem();\n";
        }
        $this->renderScript('contacts/add.phtml');
    }

    /**
     * getForm
     * @param <object> $form
     * @return <object>
     */
    protected function getForm($form) {

        $phoneField = new Snep_Form_Element_Html("contacts/elements/phone.phtml", "phone", false);
        $phoneField->setLabel($this->view->translate("Phone"));
        $phoneField->setOrder(9);
        $form->addElement($phoneField);
        $this->form = $form;
        return $this->form;
    }

    /**
     * Edit Contact
     */
    public function editAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Contacts"),
                    $this->view->translate("Edit")));

        $id = $this->_request->getParam('id');

        $contact = Snep_Contacts_Manager::get($id);
        $phones = Snep_Contacts_Manager::getPhone($id);

        foreach ($phones as $phone) {
            $contact['phone'][] = $phone['phone'];
        }

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("modules/default/forms/contacts.xml"));
        $form->getElement('id')->setValue($contact['id'])->setAttrib('disabled', true);
        $form->getElement('name')->setValue($contact['name']);
        $form = $this->getForm($form);

        $_allGroups = Snep_ContactGroups_Manager::getAll();
        foreach ($_allGroups as $group) {
            $allGroups[$group['id']] = $group['name'];
        }

        $group = $form->getElement('group')->setMultiOptions($allGroups);
        ( isset($contact['group']) ? $group->setValue($contact['group']) : null );

        $address = $form->getElement('address');
        ( isset($contact['address']) ? $address->setValue($contact['address']) : null );

        $city = $form->getElement('city');
        ( isset($contact['city']) ? $city->setValue($contact['city']) : null );

        $_allState = Snep_Contacts_Manager::getStates();
        foreach ($_allState as $state) {
            $allState[$state['cod']] = $state['name'];
        }

        $state = $form->getElement('state')->setMultiOptions($allState);
        ( isset($contact['state']) ? $state->setValue($contact['state']) : null );

        $zipcode = $form->getElement('zipcode');
        ( isset($contact['cep']) ? $zipcode->setValue($contact['cep']) : null );

        $this->view->form = $form;

        if ($this->_request->getPost()) {
            $_POST['id'] = $id;
            $form_isValid = $form->isValid($_POST);

            $dados = $this->_request->getParams();

            if ($form_isValid) {

                Snep_Contacts_Manager::edit($dados);
                Snep_Contacts_Manager::removePhone($id);

                $numbers = explode(",", $_POST['phoneValue']);
                foreach ($numbers as $key => $phone) {
                    Snep_Contacts_Manager::addNumber($id, $phone);
                }
                $this->_redirect($this->getRequest()->getControllerName());
            }
        } else {

            $this->view->dataphone = "phoneObj.addItem();\n";
            $phoneList = $contact['phone'];

            $phone = "phoneObj.addItem(" . count($phoneList) . ");\n";

            foreach ($phoneList as $index => $value) {
                $phone .= "phoneObj.widgets[$index].value='{$value}';\n";
            }

            $this->view->dataphone = $phone;
            $this->renderScript('contacts/edit.phtml');
        }
    }

    /**
     * Remove a Contact
     */
    public function removeAction() {

        $id = $this->_request->getParam('id');

        Snep_Contacts_Manager::removePhone($id);
        Snep_Contacts_Manager::remove($id);
        $this->_redirect($this->getRequest()->getControllerName());
    }

    /**
     * Import contacts from CSV file
     */
    public function importAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Contacts"),
                    $this->view->translate("Import CSV")));

        $this->view->message = $this->view->translate("The file must be separated by commas. Header is optional and columns can be associated in the next screen.");

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form();
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/csv/');
        $form->addElement(new Zend_Form_Element_File('file'));
        $this->view->form = $form;
    }

    /**
     * multiRemoveAction - Remove various contacts
     */
    public function multiremoveAction() {

        if ($this->_request->getPost()) {

            if ($_POST['group'] == 'all') {
                $groups = Snep_ContactGroups_Manager::getAll();
            } else {
                $groups = Snep_ContactGroups_Manager::get($_POST['group']);
            }

            foreach ($groups as $group) {
                
                $contact = Snep_Contacts_Manager::getMember($group['id']);
                foreach ($contact as $key => $id) {
                    Snep_Contacts_Manager::removePhone($id['id']);
                }

                Snep_Contacts_Manager::removeByGroupId($group['id']);
            }

            $this->_redirect($this->getRequest()->getControllerName());
        } else {

            $this->view->message = $this->view->translate('Select a contact group to remove your contacts.');
            $_contactGroups = Snep_ContactGroups_Manager::getAll();
            $contactGroups = array('all' => $this->view->translate('All Groups'));
            foreach ($_contactGroups as $contactGroup) {
                $contactGroups[$contactGroup['id']] = $contactGroup['name'];
            }

            $form = new Snep_Form();

            $select = new Zend_Form_Element_Select('group');
            $select->addMultiOptions($contactGroups)->setLabel($this->view->translate('Group'));

            $select->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(array('elementTd' => 'HtmlTag'), array('tag' => 'div', 'class' => 'input')),
                array('Label', array('tag' => 'div', 'class' => 'label')),
                array(array('elementTr' => 'HtmlTag'), array('tag' => 'div', 'class' => 'line')),
            ));

            $form->addElement($select);

            $this->view->form = $form;
            $this->renderScript("contacts/select-multi-remove.phtml");
        }
    }

    /**
     * Associate fields between database and csv file
     */
    public function csvAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Contacts"),
                    $this->view->translate("Import CSV"),
                    $this->view->translate("Column Association")));

        $adapter = new Zend_File_Transfer_Adapter_Http();
        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        if (!$adapter->isValid()) {
            $this->view->invalid = true;
        } else {
            $this->view->invalid = false;
            $adapter->receive();

            $fileName = $adapter->getFileName();
            $handle = fopen($fileName, "r");
            $csv = array();
            $row_number = 2;
            $first_row = explode(",", str_replace("\n", "", fgets($handle, 4096)));
            $column_count = count($first_row);
            $csv[] = $first_row;

            while (!feof($handle)) {
                $line = fgets($handle, 4096);
                if (strpos($line, ",")) {

                    $row = explode(",", preg_replace("/[^a-zA-Z0-9 éúíóáÉÚÍÓÁèùìòàÈÙÌÒÀõãñÕÃÑêûîôâÊÛÎÔÂëÿüïöäËYÜÏÖÄ,\._\*#]/", "", $line));

                    if (count($row) != $column_count) {
                        throw new ErrorException($this->view->translate("Invalid column count on line %d", $row_number));
                    }
                    $csv[] = $row;
                    $row_number++;
                }
            }

            fclose($handle);

            $standard_fields = array("discard" => $this->view->translate("Discard"),
                "name" => $this->view->translate("Name"),
                "address" => $this->view->translate("Address"),
                "city" => $this->view->translate("City"),
                "state" => $this->view->translate("State"),
                "zipcode" => $this->view->translate("Zip Code"),
                "phone" => $this->view->translate("Phone"));

            $session = new Zend_Session_Namespace('csv');
            $session->data = $csv;

            $_groups = Snep_ContactGroups_Manager::getAll();
            foreach ($_groups as $group) {
                $groups[$group['id']] = $group['name'];
            }

            if (!count($_groups) > 0) {
                $this->view->error = $this->view->translate('There is no contacts group registered.');
            }
            
            $this->view->csvprocess = array_slice($csv, 0, count($csv)+1);
            $this->view->fields = $standard_fields;
            ( isset($groups) ? $this->view->group = $groups : $this->view->group = false);
        }
    }

    /**
     * exportAction - Export contacts for CSV file.
     */
    public function exportAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Contacts"),
                    $this->view->translate("Export CSV")));

        if ($this->_request->getPost()) {

            $db = Zend_Registry::get('db');
            $select = $db->select()
                    ->from(array("n" => "contacts_names"), array("id", "name as nome", "city", "state", "cep"))
                    ->join(array("g" => "contacts_group"), 'n.group = g.id', array("g.name"))
                    ->order('g.id');

            if ($_POST['group'] != 'all') {
                $select->where('g.id = ?', $_POST['group']);
            }

            $stmt = $db->query($select);
            $contacts = $stmt->fetchAll();

            if (empty($contacts)) {
                $this->_redirect($this->getRequest()->getControllerName() . '/errorExport');
            }

            foreach ($contacts as $key => $contact) {
                $phones = Snep_Contacts_Manager::getPhone($contact['id']);
                $phone = "";
                foreach ($phones as $cont => $number) {
                    $phone .= $number['phone'] . " ";
                }
                $contacts[$key]['phones'] = $phone;
            }

            $headers = array('id' => $this->view->translate('Code'),
                'nome' => $this->view->translate('Name'),
                'city' => $this->view->translate('City'),
                'state' => $this->view->translate('State'),
                'cep' => $this->view->translate('ZipCode'),
                'name' => $this->view->translate('Group'),
                'phones' => $this->view->translate('Phones'));

            $csv = new Snep_Csv();
            $csv_data = $csv->generate($contacts, $headers);

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            $dateNow = new Zend_Date();
            $fileName = $this->view->translate('Contacts_csv_') . $dateNow->toString($this->view->translate(" dd-MM-yyyy_hh'h'mm'm' ")) . '.csv';

            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            echo $csv_data;
        } else {

            $this->view->message = $this->view->translate('Select a contact group to export.');
            $_contactGroups = Snep_ContactGroups_Manager::getAll();
            $contactGroups = array('all' => $this->view->translate('All Groups'));
            foreach ($_contactGroups as $contactGroup) {
                $contactGroups[$contactGroup['id']] = $contactGroup['name'];
            }

            $form = new Snep_Form();
            $select = new Zend_Form_Element_Select('group');
            $select->addMultiOptions($contactGroups);

            $form->addElement($select);
            $this->view->form = $form;
            $this->renderScript("contacts/export.phtml");
        }
    }

    /**
     * Process a csv file
     */
    public function csvprocessAction() {
        if ($this->getRequest()->isPost()) {
            $session = new Zend_Session_Namespace('csv');
            $fields = $_POST['field'];
            $skipped = false;
            $validateEmpty = new Zend_Validate_NotEmpty();
            $validateAlnum = new Zend_Validate_Alnum();
            $error = array();
            $errorAdd = false;

            foreach ($session->data as $contact) {

                if (isset($_POST['discard_first_row']) && $_POST['discard_first_row'] == "on" && $skipped == false) {
                    $skipped = true;
                    continue;
                }
                $contactData = array("discard" => "",
                    "name" => "",
                    "address" => "",
                    "city" => "",
                    "state" => "",
                    "zipcode" => "",
                    "phone" => "");

                $addEntry = true;
                foreach ($contact as $column => $data) {
                    if ($fields[$column] != "discard") {
                        $contactData[$fields[$column]] = $data;
                    }
                }

                $contactData['group'] = $_POST['group'];
                $contactData['id'] = Snep_Contacts_Manager::getLastId();

                if (!array_key_exists('name', $contactData) || !$validateEmpty->isValid($contactData['name'])) {
                    $addEntry = false;
                    $error[] = $contactData;
                } else if ((!array_key_exists('phone', $contactData) || !$validateEmpty->isValid($contactData['phone']))) {
                    $addEntry = false;
                    $error[] = $contactData;
                }

                if ($addEntry) {

                    Snep_Contacts_Manager::add($contactData);
                    Snep_Contacts_Manager::addNumber($contactData['id'], $contactData['phone']);
                } else {

                    $errorAdd = true;
                    $this->_redirect($this->getRequest()->getControllerName() . '/error');
                }
            }
            if (count($error) > 0) {
                $errorString = $this->view->translate('Os seguintes registros do CSV contem dados nulos::<br/>');
                foreach ($error as $value) {
                    $errorString.= implode(',', $value) . '<br/>';
                }
                throw new ErrorException($errorString);
            }
        }
        if (!$errorAdd) {

            $this->_redirect($this->getRequest()->getControllerName());
        }
    }

    public function errorAction() {
        
    }

    public function errorexportAction() {
        
    }

}
