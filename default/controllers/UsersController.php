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
 * Controller for extension management
 * 
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class UsersController extends Zend_Controller_Action {

    /**
     * List all users
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users")
        ));
        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from(array("n" => "users"), array("id as ide", "name as nome", "email", "created", "updated"))
                ->join(array("g" => "profiles"), 'n.profile_id = g.id', "name")
                ->order('nome');

        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("n.`$field` like '%$query%'");
        }

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
        $this->view->filter = array(array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add/",
                "display" => $this->view->translate("Add User"),
                "css" => "include"));
    }

    /**
     *  Add User
     */
    public function addAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users"),
                    $this->view->translate("Add")
        ));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("default/forms/users.xml"));

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

            if ($form_isValid) {
                Snep_Users_Manager::add($dados);
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

        $this->view->form = $form;
    }

    /**
     * Edit users
     */
    public function editAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Users"),
                    $this->view->translate("Edit")
        ));

        $id = $this->_request->getParam('id');

        $user = Snep_Users_Manager::get($id);

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("default/forms/users.xml"));

        $profile = Snep_Profiles_Manager::getAll();

        foreach ($profile as $group) {
            $allGroups[$group['id']] = $group['name'];
        }

        $group = $form->getElement('group');
        $group->setValue($user["profile_id"]);

        $group = $form->getElement('group')->setMultiOptions($allGroups);
        ( isset($user['group']) ? $group->setValue($user['group']) : null );

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

            if ($form_isValid) {
                $dados['created'] = $user['created'];

                if (strlen($dados['password']) != 32) {
                    $dados['password'] = md5($dados['password']);
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
        Snep_Users_Manager::remove($id);
        $this->_redirect($this->getRequest()->getControllerName());
    }

}
