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
 * Cost Center Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 * @author    Rafael Pereira Bozzetti
 */
class CostCenterController extends Zend_Controller_Action {

    /**
     * List all Cost Center's
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Cost Center")
        ));
        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from("ccustos", array("codigo", "tipo", "nome", "descricao"));

        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("`$field` like '%$query%'");
        }

        $this->view->types = array('E' => $this->view->translate('Entrada'),
            'S' => $this->view->translate('Saída'),
            'O' => $this->view->translate('Outras'));

        $page = $this->_request->getParam('page');
        $this->view->page = ( isset($page) && is_numeric($page) ? $page : 1 );
        $this->view->filtro = $this->_request->getParam('filtro');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);
        $paginator->setCurrentPageNumber($this->view->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $this->view->costcenter = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->PAGE_URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/";

        $opcoes = array("codigo" => $this->view->translate("Code"),
            "tipo" => $this->view->translate("Type"),
            "nome" => $this->view->translate("Name"),
            "descricao" => $this->view->translate("Description"));

        $filter = new Snep_Form_Filter();
        $filter->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($opcoes);
        $filter->setFieldValue($this->_request->getPost('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add",
                "display" => $this->view->translate("Add Cost Center"),
                "css" => "include"),
        );
    }

    /**
     * Add new Cost Center's
     */
    public function addAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Cost Center"),
                    $this->view->translate("Add")
        ));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("modules/default/forms/cost_center.xml"));

        if ($this->_request->getPost()) {

            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $newId = Snep_CostCenter_Manager::get($dados['id']);

            if (count($newId) > 1) {
                $form_isValid = false;
                $form->getElement('id')->addError($this->view->translate('Code already exists.'));
            }

            if ($form_isValid) {
                $dados = $this->_request->getParams();
                Snep_CostCenter_Manager::add($dados);

                //log-user
                if (class_exists("Loguser_Manager")) {
                    $id = $dados["id"];
                    Snep_LogUser::salvaLog("Adicionou Centro de Custos", $id, 6);
                    $add = Snep_CostCenter_Manager::get($id);
                    Snep_CostCenter_Manager::insertLogCcustos("ADD", $add);
                }
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

        $this->view->form = $form;
    }

    /**
     * Remove Cost Center's
     */
    public function removeAction() {
        $id = $this->_request->getParam('id');

        //log-user
        if (class_exists("Loguser_Manager")) {

            Snep_LogUser::salvaLog("Excluiu Centro de Custos", $id, 6);
            $add = Snep_CostCenter_Manager::get($id);
            Snep_CostCenter_Manager::insertLogCcustos("DEL", $add);
        }
        Snep_CostCenter_Manager::remove($id);
    }

    /**
     * Edit Cost Center's
     */
    public function editAction() {
        $id = $this->_request->getParam('id');
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Cost Center"),
                    $this->view->translate("Edit")
        ));

        $costCenter = Snep_CostCenter_Manager::get($id);

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form(new Zend_Config_Xml("modules/default/forms/cost_center.xml"));
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/edit/id/' . $id);
        $form->getElement('id')->setValue($costCenter['codigo'])->setAttrib('readonly', true);
        $form->getElement('name')->setValue($costCenter['nome']);
        $form->getElement('description')->setValue($costCenter['descricao']);
        $form->getElement('type')->setValue($costCenter['tipo']);

        if ($this->_request->getPost()) {

            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            if ($form_isValid) {

                //log-user
                if (class_exists("Loguser_Manager")) {
                    $add = Snep_CostCenter_Manager::get($id);
                    Snep_CostCenter_Manager::insertLogCcustos("OLD", $add);
                }

                Snep_CostCenter_Manager::edit($dados);

                if (class_exists("Loguser_Manager")) {
                    Snep_LogUser::salvaLog("Editou Centro de Custos", $id, 6);
                    $add = Snep_CostCenter_Manager::get($id);
                    Snep_CostCenter_Manager::insertLogCcustos("NEW", $add);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

        $this->view->form = $form;
    }

}