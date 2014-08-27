<?php

/*
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
 * Controller of profiles
 * 
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class ProfilesController extends Zend_Controller_Action {

    /**
     * indexAction - List all profiles
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles")));

        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from(array("profiles"), array("id", "name", "created", "updated"))
                ->order('id');

        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("profiles.`$field` like '%$query%'");
        }

        $page = $this->_request->getParam('page');
        $this->view->page = ( isset($page) && is_numeric($page) ? $page : 1 );
        $this->view->filtro = $this->_request->getParam('filtro');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);
        $paginator->setCurrentPageNumber($this->view->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $this->view->profiles = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->PAGE_URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/";

        $opcoes = array("id" => $this->view->translate("Code"),
            "name" => $this->view->translate("Name"));

        $filter = new Snep_Form_Filter();
        $filter->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($opcoes);
        $filter->setFieldValue($this->_request->getPost('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add/",
                "display" => $this->view->translate("Add Profiles"),
                "css" => "include"));
    }

    /**
     *  AddAction - Add profile
     */
    public function addAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles"),
                    $this->view->translate("Add")));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("modules/default/forms/profiles.xml"));
        $form->getElement('name')->setRequired(true);

        if ($this->_request->getPost()) {

            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $newId = Snep_Profiles_Manager::getName($dados['name']);

            if (count($newId) > 1) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }

            if ($form_isValid) {

                Snep_Profiles_Manager::add($dados);
                $lastId = Snep_Profiles_Manager::lastId();
                $dados['id'] = $lastId;

                $this->_redirect($this->getRequest()->getControllerName() . "/permission/id/$lastId?action=add");
            }
        }

        $this->view->form = $form;
    }

    /**
     * editAction - Edit profiles
     */
    public function editAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles"),
                    $this->view->translate("Edit")));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form_xml = new Zend_Config_Xml("modules/default/forms/profiles.xml");
        $form = new Snep_Form($form_xml);

        $id = $this->_request->getParam('id');
        $profile = Snep_Profiles_Manager::get($id);

        $name = $form->getElement('name');
        ( isset($profile['name']) ? $name->setValue($profile['name']) : null );

        if ($this->_request->getPost()) {

            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $newId = Snep_Profiles_Manager::getName($dados['name']);

            if (count($newId) > 1) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }

            if ($form_isValid) {
                $dados['created'] = $profile['created'];

                Snep_Profiles_Manager::edit($dados);

                $data = array();
                $data['id'] = $dados['id'];

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
        $this->view->form = $form;
    }

    /**
     * memberAction - Add members to profile
     */
    public function memberAction() {

        $id = $this->_request->getParam('id');
        $profile = Snep_Profiles_Manager::get($id);

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles"),
                    $this->view->translate("Members"),
                    $this->view->translate("Name"),
                    $this->view->translate($profile['name'])));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form_xml = new Zend_Config_Xml("modules/default/forms/member.xml");
        $form = new Snep_Form($form_xml);

        $usersProfiles = array();

        foreach (Snep_Profiles_Manager::getUsersProfiles($id) as $data) {
            $usersProfiles[$data['name']] = "{$data['name']}";
        }

        $usersAll = array();
        $userNoprofile = Snep_Profiles_Manager::getUsersnotProfile($id);

        foreach ($userNoprofile as $data) {

            // Retira da lista de usuarios, caso o mesmo possua permissao individual
            $existPermission = Snep_Permission_Manager::existPermission($data['id']);

            if ($existPermission == false) {
                if (!isset($usersProfiles[$data['name']])) {
                    $usersAll[$data['nome']] = "{$data['nome']}" . " - (" . $data['name'] . ")";
                }
            }
        }

        $this->view->objSelectBox = "users";
        $form->setSelectBox($this->view->objSelectBox, $this->view->translate('Users'), $usersAll, $usersProfiles);

        if ($this->_request->getPost()) {

            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            if ($form_isValid) {
                $dados['created'] = $profile['created'];

                foreach (Snep_Profiles_Manager::getUsersProfiles($id) as $only) {

                    $profileOnly['box'] = $only['name'];
                    $profileOnly['id'] = $dados['id'];

                    Snep_Users_Manager::addProfileByName($profileOnly);
                }

                $data = array();
                $data['id'] = $dados['id'];

                if (isset($dados['box_add'])) {

                    foreach ($dados['box_add'] as $key => $box) {
                        $data['box'] = $box;
                        $this->view->users = Snep_Users_Manager::addProfileByName($data);
                    }
                }

                // Exclui membros na edição
                if (isset($dados['box'])) {
                    foreach ($dados['box'] as $item => $box) {
                        Snep_Users_Manager::removeProfileByName($box, $data['id']);
                    }
                }
                $this->_redirect("/" . $this->getRequest()->getControllerName() . "/");
            }
        }
        $this->view->form = $form;
    }

    /**
     * removeAction - Remove a profile
     */
    public function removeAction() {

        $id = $this->_request->getParam('id');
        $members = Snep_Profiles_Manager::getUsersProfiles($id);

        // migra membros para o profile default
        foreach ($members as $item => $member) {
            Snep_Profiles_Manager::migration($member);
        }

        Snep_Profiles_Manager::removePermission($id);
        Snep_Profiles_Manager::remove($id);
        $this->_redirect($this->getRequest()->getControllerName());
    }

    /**
     *  permissionAction - Add permissions to profile
     */
    public function permissionAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles"),
                    $this->view->translate("Permission")));

        // verifica action add. Caso sim envia em seguida para permissões
        $actionAdd = false;
        if (isset($_GET["action"])) {
            $actionAdd = true;
        }

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form_xml = new Zend_Config_Xml("modules/default/forms/permission.xml");
        $form = new Snep_Form($form_xml);

        $id = $this->_request->getParam('id');
        $currentResources = Snep_Permission_Manager::getAllPermissions($id);

        $modules = Snep_Permission_Manager::getAll();

        $resources = array();
        $selected = array();

        foreach ($modules as $moduleKey => $module) {

            foreach ($module as $controllerKey => $controller) {

                if (isset($controller["write"])) {
                    $controller["write"] = $controller["read"];
                }
                // Modulos de erro e login/logout não são inseridos no banco
                $valid = true;
                if ($controllerKey == 'error' || $controllerKey == "auth" || $controllerKey == "installer") {
                    $valid = false;
                }

                if ($valid == true) {

                    foreach ($controller as $actionKey => $action) {
                        $resource = $moduleKey . '_' . $controllerKey . '_' . $actionKey;
                        $label = Snep_Modules::$modules[$moduleKey]->getName() . " - " . $action;

                        if (substr($label, 0, 7) == 'Default') {
                            $label = substr_replace($label, '', 0, 10);
                        }

                        // verifica se modulo possui opcao de escrita para montar label
                        if (substr($resource, -5) == 'write') {
                            $resources[$resource] = $label . " - " . $this->view->translate('write');
                        } else {
                            $resources[$resource] = $label . " - " . $this->view->translate('read');
                        }

                        if (array_search($resource, $currentResources) !== FALSE)
                            $selected[$resource] = $label;
                    }
                }
            }
        }

        $questionValid = new Zend_Validate_InArray(array('Yes'));
        $questionValid->setMessage('Yes is required!');

        $this->view->objSelectBox = "permissions";

        foreach ($selected as $key => $item) {

            if (substr($key, -5) == 'write') {
                $label = $item . " - " . $this->view->translate('write');
            } else {
                $label = $item . " - " . $this->view->translate('read');
            }
            $selected[$key] = $label;
        }

        // verifica itens na edicao
        foreach ($resources as $cont => $resource) {
            foreach ($selected as $key => $item) {
                if ($cont == $key) {
                    unset($resources[$cont]);
                }
            }
        }

        $form->setSelectBox($this->view->objSelectBox, $this->view->translate('Permission'), $resources, $selected);

        if ($this->getRequest()->getPost()) {

            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $update = array();
            $permissions = Snep_Permission_Manager::getPermissions($id);

            foreach ($resources as $key => $value) {

                if (isset($dados['box_add'])) {
                    if (array_search($key, $dados['box_add']) !== FALSE) {
                        $update[$key] = true;
                    } else {
                        $update[$key] = false;
                    }
                }
            }

            Snep_Permission_Manager::update($update, $id);

            // Adiciona permissões cadastradas antes da edição(Não estão na box_add)
            if (isset($permissions)) {
                foreach ($permissions as $item => $permission) {
                    Snep_Permission_Manager::addPermissionOld($permission, $id);
                }
            }

            // Exclui permissão na edição
            if (isset($dados['box'])) {

                foreach ($dados['box'] as $item => $permission) {
                    Snep_Permission_Manager::removePermissionOld($permission, $id);
                }
            }

            if ($form_isValid) {

                if ($actionAdd == true) {
                    $this->_redirect($this->getRequest()->getControllerName() . "/member/id/$id");
                } else {
                    $this->_redirect($this->getRequest()->getControllerName());
                }
            }
        }

        $this->view->form = $form;
    }

}

?>