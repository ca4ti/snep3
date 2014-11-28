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
 * Controller for users
 * 
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 * 
 */
class UsersController extends Zend_Controller_Action {

    /**
     * indexAction - List all users
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users")));

        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from(array("n" => "users"), array("id as ide", "name as nome", "email", "created", "updated"))
                ->join(array("g" => "profiles"), 'n.profile_id = g.id', "name");

        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("n.`$field` like '%$query%'");
        }

        $this->view->order = Snep_Order::setSelect($select, array("ide","nome", "email", "created", "updated", "name"), $this->_request);

        $page = $this->_request->getParam('page');
        $this->view->page = ( isset($page) && is_numeric($page) ? $page : 1 );
        $this->view->filtro = $this->_request->getParam('filtro');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);
        $paginator->setCurrentPageNumber($this->view->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $this->view->users = $paginator;
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
        $this->view->filter = array(
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/export/",
                "display" => $this->view->translate("Export CSV"),
                "css" => "back"),
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add/",
                "display" => $this->view->translate("Add User"),
                "css" => "include"));
    }

    /**
     *  addAction - Add User
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users"),
                    $this->view->translate("Add")));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("modules/default/forms/users.xml"));

        $profiles = Snep_Profiles_Manager::getAll();

        foreach ($profiles as $group) {
            $allGroups[$group['id']] = $group['name'];
        }

        if (count($profiles)) {
            $form->getElement('group')->setMultiOptions($allGroups);
        }

        if ($this->_request->getPost()) {

            if (empty($_POST['name'])) {
                $form->getElement('name')->setRequired(true);
            }
            if (empty($_POST['password'])) {
                $form->getElement('password')->setRequired(true);
            }

            if (empty($_POST['email'])) {
                $form->getElement('email')->setRequired(true);
            }

            $form_isValid = $form->isValid($_POST);

            if (empty($_POST['group'])) {
                $form->getElement('group')->addError($this->view->translate('No group selected'));
                $form_isValid = false;
            }

            $dados = $this->_request->getParams();

            $newId = Snep_Users_Manager::getName($dados['name']);

            if (count($newId) > 1) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }

            if ($form_isValid) {
                Snep_Users_Manager::add($dados);
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
        $this->view->form = $form;
    }

    /**
     * editAction - Edit users
     */
    public function editAction() {
        
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users"),
                    $this->view->translate("Edit")));

        $id = $this->_request->getParam('id');
        $user = Snep_Users_Manager::get($id);

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("modules/default/forms/users.xml"));

        $profile = Snep_Profiles_Manager::getAll();

        foreach ($profile as $group) {
            $allGroups[$group['id']] = $group['name'];
        }

        $idProfile = $user["profile_id"];

        if ($id != 1) {
            $group = $form->getElement('group');
            $group->setValue($user["profile_id"]);

            $group = $form->getElement('group')->setMultiOptions($allGroups);
            ( isset($user['group']) ? $group->setValue($user['group']) : null );
        } else {
            $form->removeElement('group');
        }

        $name = $form->getElement('name');
        ( isset($user['name']) ? $name->setValue($user['name']) : null );

        $password = $form->getElement('password');
        ( isset($user['password']) ? $password->setValue($user['password']) : null );
        $form->getElement('password')->renderPassword = true;

        $email = $form->getElement('email');
        ( isset($user['email']) ? $email->setValue($user['email']) : null );

        if ($this->_request->getPost()) {
            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $newId = Snep_Users_Manager::getName($dados['name']);

            if (count($newId) > 1) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }

            if ($form_isValid) {
                $dados['created'] = $user['created'];

                if (strlen($dados['password']) != 32) {
                    $dados['password'] = md5($dados['password']);
                }

                // Caso seja admin, recebe profile default
                if ($id == 1) {
                    $dados['group'] = 1;
                }

                // Ao editar grupo, o usuario perde as permissões individuais
                if ($idProfile != $dados['group']) {
                    Snep_Users_Manager::removePermission($id);
                }

                Snep_Users_Manager::edit($dados);
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
        $this->view->form = $form;
    }

    /**
     * Remove a user
     */
    public function removeAction() {

        $id = $this->_request->getParam('id');
        Snep_Users_Manager::removeRecovery($id);
        Snep_Users_Manager::removePermission($id);
        Snep_Users_Manager::remove($id);
        $this->_redirect($this->getRequest()->getControllerName());
    }

    /**
     *  Edit permission
     */
    public function permissionAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users"),
                    $this->view->translate("Permission")));

        $id = $this->_request->getParam('id');
        if ($id == null) {
            $id = $_POST['user'];
        }

        $profile = Snep_Permission_Manager::getIdProfile($id);
        $idProfile = (int) $profile["profile_id"];

        //Permissões do grupo
        $currentResourcesGroup = Snep_Permission_Manager::getAllPermissions($idProfile);

        //Permissões individuais do usuário 
        $currentResourcesUsers = Snep_Permission_Manager::getAllPermissionsUser($id);

        // Modulos do sistema
        $modules = Snep_Permission_Manager::getAll();

        $resources = array();
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

                        // verifica se arquivo possui opcao de escrita para montar label
                        if (substr($resource, -5) == 'write') {
                            $resources[$resource] = $label . " - " . $this->view->translate('write');
                        } else {
                            $resources[$resource] = $label . " - " . $this->view->translate('read');
                        }
                    }
                }
            }
        }

        $permissionsGroup = array_intersect_key($currentResourcesGroup, $resources);

        // usuário nao possui permissão individual
        if (empty($currentResourcesUsers)) {
            $this->view->user = false;
        } else {
            $permissionUser = array();

            foreach ($currentResourcesUsers as $key => $resourceUser) {
                $permissionUser[$key] = substr($resourceUser, 0, -1);
            }

            $permissionsUser = array_intersect_key($permissionUser, $resources);
            $this->view->user = true;
        }

        //verifica permissões do grupo do usuário
        $modulesAll = array();
        $cont = 0;

        // Caso possua dados de permissão do usuário, as permissões passam 
        // a ser da tabela users_permission
        foreach ($resources as $key => $res) {

            $modulesAll[$cont]['id_permission'] = $key;
            $modulesAll[$cont]['name'] = $res;
            foreach ($permissionsGroup as $item => $permissionGroup) {
                if ($key == $item) {
                    $modulesAll[$cont]['group'] = true;
                }
            }
            if (!empty($permissionsUser)) {
                foreach ($permissionsUser as $item_ => $userPermission) {

                    if ($key == $item_) {
                        $modulesAll[$cont]['user'] = true;
                        foreach ($currentResourcesUsers as $try => $allow) {
                            if ($item_ == $try) {
                                $modulesAll[$cont]['allow'] = substr($allow, -1);
                            }
                        }
                    }
                }
            }
            $cont++;
        }

        $this->view->modules = $modulesAll;
        $this->view->id = $id;

        if ($this->_request->isPost()) {

            $dados = array();
            $dados['id'] = $_POST['user'];

            unset($_POST['user']);
            foreach ($_POST as $key => $permission_id) {
                $dados['permission_id'][$key] = $key;
            }

            $deleted = array_diff($currentResourcesGroup, $dados['permission_id']);
            $added = $dados['permission_id'];

            if (!empty($permissionUser)) {
                $deletedusers = array_diff($permissionUser, $dados['permission_id']);
                $deleted = array_merge($deleted, $deletedusers);
            }

            // Caso se exclua todas as permissões
            if (!array_key_exists('permission_id', $dados)) {
                $deleted = array_merge($currentResourcesGroup, $permissionUser);
            }

            // retirar permissão do usuário por id 
            if (!empty($deleted)) {
                Snep_Permission_Manager::removePermissionUser($dados['id'], $deleted);
            }

            //adicionar permissão ao usuário por id
            if (!empty($added)) {
                Snep_Permission_Manager::addPermissionUser($dados['id'], $added);
            }

            $this->_redirect("/" . $this->getRequest()->getControllerName() . "/");
        }
    }

    /**
     * exportAction - Export contacts for CSV file.
     */
    public function exportAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users"),
                    $this->view->translate("Export CSV")));

        $ie = new Snep_CsvIE('users');
        if ($this->_request->getParam('download')) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $ie->export();
        } else {
            $this->view->form = $ie->exportResult();
            $this->view->title = "Export";
            $this->render('export');
        }
    }

}
