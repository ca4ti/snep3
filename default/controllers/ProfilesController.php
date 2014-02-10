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
 */
class ProfilesController extends Zend_Controller_Action {

    /**
     * List all profiles
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles")
        ));
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
     *  Add profile
     */
    public function addAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles"),
                    $this->view->translate("Add")
        ));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("default/forms/profiles.xml"));

        try {

            $profiles = Snep_Profiles_Manager::getUsersAll();
        } catch (Exception $e) {

            display_error($LANG['error'] . $e->getMessage(), true);
        }

        $users = array();
        foreach ($profiles as $key => $val) {

            $users[$val['id']] = $val['nome'] . " - (" . $val['name'] . ")";
        }

        $this->view->objSelectBox = "users";

        $form->setSelectBox($this->view->objSelectBox, $this->view->translate('Users'), $users);

        if ($this->_request->getPost()) {

            if (empty($_POST['name'])) {
                $form->getElement('name')->setRequired(true);
            }

            $form_isValid = $form->isValid($_POST);

            $dados = $this->_request->getParams();

            $lastId = Snep_Profiles_Manager::lastId();

            $dados['id'] = $lastId + 1;

            if ($form_isValid) {
                Snep_Profiles_Manager::add($dados);

                if ($dados['box_add']) {

                    foreach ($dados['box_add'] as $id => $users) {

                        $profileGroup = array('user' => $users,
                            'profile' => $dados['id']);

                        $this->view->extensions = Snep_Users_Manager::addProfile($profileGroup);
                    }
                }


                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

        $this->view->form = $form;
    }

    /**
     * Edit profiles
     */
    public function editAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Profiles"),
                    $this->view->translate("Edit")
        ));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form_xml = new Zend_Config_Xml("default/forms/profiles.xml");
        $form = new Snep_Form($form_xml);

        $id = $this->_request->getParam('id');

        $profile = Snep_Profiles_Manager::get($id);

        $name = $form->getElement('name');
        ( isset($profile['name']) ? $name->setValue($profile['name']) : null );

        $usersProfiles = array();

        foreach (Snep_Profiles_Manager::getUsersProfiles($id) as $data) {

            $usersProfiles[$data['name']] = "{$data['name']}";
        }

        $usersAll = array();
        foreach (Snep_Profiles_Manager::getUsersnotProfile($id) as $data) {

            if (!isset($usersProfiles[$data['name']])) {

                $usersAll[$data['name']] = "{$data['name']}";
            }
        }

        $this->view->objSelectBox = "users";

        $form->setSelectBox($this->view->objSelectBox, $this->view->translate('Users'), $usersAll, $usersProfiles);

        if ($this->_request->getPost()) {

            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            if ($form_isValid) {
                $dados['created'] = $profile['created'];

                Snep_Profiles_Manager::edit($dados);

                foreach (Snep_Profiles_Manager::getUsersProfiles($id) as $only) {

                    $profileOnly['box'] = $only['name'];
                    $profileOnly['id'] = 1;

                    Snep_Users_Manager::addProfileByName($profileOnly);
                }

                $data = array();
                $data['id'] = $dados['id'];

                if ($dados['box_add']) {

                    foreach ($dados['box_add'] as $id => $box) {
                        $data['box'] = $box;

                        $this->view->users = Snep_Users_Manager::addProfileByName($data);
                    }
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
        $this->view->form = $form;
    }

    /**
     * Remove a profile
     */
    public function removeAction() {
        $id = $this->_request->getParam('id');

        Snep_Profiles_Manager::remove($id);
        $this->_redirect($this->getRequest()->getControllerName());
    }

}

?>