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
 * Extension Groups Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 */
class ExtensionsGroupsController extends Zend_Controller_Action {

    /**
     *
     * @var Zend_Form
     */
    protected $form;

    /**
     *
     * @var array
     */
    protected $forms;

    /**
     * indexAction - List all Extensions groups
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extension Groups")));

        $db = Zend_Registry::get('db');

        $this->view->tra = array("admin" => $this->view->translate("Administrators"),
            "users" => $this->view->translate("Users"),
            "NULL" => $this->view->translate("None"),
            "all" => $this->view->translate("All"));

        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();
        $this->view->user = $username;

        $select = $db->select()
                ->from("groups", array("name", "inherit"))
                ->where("name not in ('all','users','administrator','NULL') ");

        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("`$field` like '%$query%'");
        }

        $this->view->order = Snep_Order::setSelect($select, array("name", "inherit"), $this->_request);

        $page = $this->_request->getParam('page');
        $this->view->page = ( isset($page) && is_numeric($page) ? $page : 1 );

        $this->view->filtro = $this->_request->getParam('filtro');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);

        $paginator->setCurrentPageNumber($this->view->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $this->view->extensionsgroups = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/";
        $this->view->PAGE_URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/";

        $opcoes = array("name" => $this->view->translate("Name"));

        // Formulário de filtro.
        $filter = new Snep_Form_Filter();
        $filter->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($opcoes);
        $filter->setFieldValue($this->_request->getPost('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add",
                "display" => $this->view->translate("Add Extension Group"),
                "css" => "include"),
        );
    }

    /**
     * addAction - Adds a group and their extensions in the database
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extension Groups"),
                    $this->view->translate("Add Extension Groups")));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form_xml = new Zend_Config_Xml("modules/default/forms/extensions_groups.xml");
        $form = new Snep_Form($form_xml);
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/add');

        $form->removeElement('id');
        $form->getElement('name')
                ->setLabel($this->view->translate('Name'));

        $form->getElement('type')
                ->setRequired(true)
                ->setLabel($this->view->translate('Type'))
                ->setMultiOptions(array('all' => $this->view->translate('Administrator'),
                    'users' => $this->view->translate('User'),
                    'NULL' => $this->view->translate('None')));


        try {
            $extensionsAllGroup = Snep_ExtensionsGroups_Manager::getExtensionsAll();
        } catch (Exception $e) {

            display_error($LANG['error'] . $e->getMessage(), true);
        }

        $extensions = array();

        foreach ($extensionsAllGroup as $key => $val) {

            $extensions[$val['name']] = $val['name'] . " ( " . $val['group'] . " )";
        }
        $this->view->objSelectBox = "extensions";

        $form->setSelectBox($this->view->objSelectBox, $this->view->translate('Extensions'), $extensions);

        if ($this->getRequest()->getPost()) {


            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $newId = Snep_ExtensionsGroups_Manager::getName($dados['name']);
            
            if (count($newId) > 1) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }

            if ($form_isValid) {

                $group = array('name' => $dados['name'],
                    'inherit' => $dados['type']);

                $this->view->group = Snep_ExtensionsGroups_Manager::addGroup($group);

                if ($dados['box_add'] && $this->view->group) {

                    foreach ($dados['box_add'] as $id => $extensions) {

                        $extensionsGroup = array('group' => $dados['name'],
                            'extensions' => $extensions);

                        $this->view->extensions = Snep_ExtensionsGroups_Manager::addExtensionsGroup($extensionsGroup);
                    }
                }

                //log-user
                if (class_exists("Loguser_Manager")) {

                    $nome = $dados['name'];
                    Snep_LogUser::salvaLog("Adicionou Grupo de ramal", $nome, 11);
                    $add = Snep_ExtensionsGroups_Manager::getGroupLog($nome);
                    Snep_ExtensionsGroups_Manager::insertLogGroup("ADD", $add);
                }

                $this->_redirect("/" . $this->getRequest()->getControllerName() . "/");
            }
        }

        $this->view->form = $form;
    }

    /**
     * editAction - Edit extensions groups
     */
    public function editAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extension Groups"),
                    $this->view->translate("Edit Extension Groups")));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $xml = new Zend_Config_Xml("modules/default/forms/extensions_groups.xml");
        $form = new Snep_Form($xml);
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/edit');

        $id = $this->_request->getParam('id');

        if (class_exists("Loguser_Manager")) {
            $add = Snep_ExtensionsGroups_Manager::getGroupLog($id);
            Snep_ExtensionsGroups_Manager::insertLogGroup("OLD", $add);
        }

        $group = Snep_ExtensionsGroups_Manager::getGroup($id);
        $groupId = $form->getElement('id')->setValue($id);
        $groupName = $form->getElement('name')->setValue($group['name'])->setLabel($this->view->translate('Name'));

        $groupType = $form->getElement('type');
        $groupType->setRequired(true)
                ->setLabel($this->view->translate('Type'))
                ->setMultiOptions(array('all' => $this->view->translate('Administrator'),
                    'users' => $this->view->translate('User'),
                    'NULL' => $this->view->translate('None')))
                ->setValue($group['inherit']);

        $groupExtensions = array();
        foreach (Snep_ExtensionsGroups_Manager::getExtensionsOnlyGroup($id) as $data) {
            $groupExtensions[$data['name']] = "{$data['name']}";
        }

        $groupAllExtensions = array();
        foreach (Snep_ExtensionsGroups_Manager::getExtensionsAll() as $data) {

            if (!isset($groupExtensions[$data['name']])) {

                $groupAllExtensions[$data['name']] = "{$data['name']}" . " ( " . "{$data['group']}" . " )";
            }
        }

        $this->view->objSelectBox = "extensions";
        $form->setSelectBox($this->view->objSelectBox, $this->view->translate('Extensions'), $groupAllExtensions, $groupExtensions);

        if ($this->_request->getPost()) {

            $form_isValid = $form->isValid($_POST);

            $dados = $this->_request->getParams();
            $idGroup = $dados['id'];

            $newId = Snep_ExtensionsGroups_Manager::getName($dados['name']);
            
            if (count($newId) > 1) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }

            if($form_isValid){

                if ($dados['box_add']) {

                    foreach ($dados['box_add'] as $id => $dados['name']) {
                        Snep_ExtensionsGroups_Manager::addExtensionsGroup(array('extensions' => $dados['name'], 'group' => $idGroup));
                    }
                }

                if ($dados['box']) {

                    foreach ($dados['box'] as $id => $dados['name']) {
                        Snep_ExtensionsGroups_Manager::addExtensionsGroup(array('extensions' => $dados['name'], 'group' => 'all'));
                    }
                }

                //log-user
                if (class_exists("Loguser_Manager")) {

                    Snep_LogUser::salvaLog("Editou Grupo de ramal", $dados['name'], 11);
                    $add = Snep_ExtensionsGroups_Manager::getGroupLog($dados['name']);
                    Snep_ExtensionsGroups_Manager::insertLogGroup("NEW", $add);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

        $this->view->form = $form;
    }

    /**
     * deleteAction - Remove a Extensions Group
     */
    public function deleteAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extension Groups"),
                    $this->view->translate("Delete Extension Groups")));

        $id = $this->_request->getParam('id');
        $confirm = $this->_request->getParam('confirm');

        //checks if the group is used in the rule 
        $regras = Snep_ExtensionsGroups_Manager::getValidation($id);

        if (count($regras) > 0) {

            $this->view->error = $this->view->translate("Cannot remove. The following routes are using this extensions group: ") . "<br />";
            foreach ($regras as $regra) {
                $this->view->error .= $regra['id'] . " - " . $regra['desc'] . "<br />\n";
            }

            $this->_helper->viewRenderer('error');
        } else {

            if ($confirm == 1) {

                Snep_ExtensionsGroups_Manager::delete($id);
                $this->_redirect($this->getRequest()->getControllerName());
            }

            //log-user
            if (class_exists("Loguser_Manager")) {

                Snep_LogUser::salvaLog("Excluiu Grupo de ramal", $id, 11);
                $add = Snep_ExtensionsGroups_Manager::getGroupLog($id);
                Snep_ExtensionsGroups_Manager::insertLogGroup("DEL", $add);
            }

            $extensions = Snep_ExtensionsGroups_Manager::getExtensionsGroup($id);

            if (count($extensions) > 0) {
                $this->_redirect($this->getRequest()->getControllerName() . '/migration/id/' . $id);
            } else {

                $this->view->message = $this->view->translate("The extension group will be deleted. Are you sure?.");
                $form = new Snep_Form();
                $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/delete/id/' . $id . '/confirm/1');

                $form->getElement('submit')->setLabel($this->view->translate('Yes'));

                $this->view->form = $form;
            }
        }
    }

    /**
     * migrationAction - Migrate extensions to other Extensions Group
     */
    public function migrationAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extension Groups"),
                    $this->view->translate("Migrate Extension Group")));

        $id = $this->_request->getParam('id');

        $_allGroups = Snep_ExtensionsGroups_Manager::getAllGroup();

        foreach ($_allGroups as $group) {

            if ($group['name'] != $id) {
                $allGroups[$group['name']] = $group['name'];
            }
        }

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form();
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/migration/');

        if (isset($allGroups)) {

            $groupSelect = new Zend_Form_Element_Select('select');
            $groupSelect->setMultiOptions($allGroups);
            $groupSelect->setLabel($this->view->translate($this->view->translate("New Group")));
            $form->addElement($groupSelect);
            $this->view->message = $this->view->translate("This groups has extensions associated. Select another group for these extensions. ");
        } else {

            $groupName = new Zend_Form_Element_Text('new_group');
            $groupName->setLabel($this->view->translate($this->view->translate("New Group")));
            $form->addElement($groupName);
            $this->view->message = $this->view->translate("This is the only group and it has extensions associated. You can migrate these extensions to a new group.");
        }

        $id_exclude = new Zend_Form_Element_Hidden("id");
        $id_exclude->setValue($id);

        $form->addElement($id_exclude);

        if ($this->_request->getPost()) {

            if (isset($_POST['select'])) {

                $toGroup = $_POST['select'];
            } else {

                $new_group = array('group' => $_POST['new_group']);
                $toGroup = Snep_ExtensionsGroups_Manager::addGroup($new_group);
            }

            $extensions = Snep_ExtensionsGroups_Manager::getExtensionsOnlyGroup($id);

            foreach ($extensions as $extension) {
                Snep_ExtensionsGroups_Manager::addExtensionsGroup(array('extensions' => $extension['name'], 'group' => $toGroup));
            }

            Snep_ExtensionsGroups_Manager::delete($id);

            $this->_redirect($this->getRequest()->getControllerName());
        }
        $this->view->form = $form;
    }

}
