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
 * Cost center Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class CostCenterController extends Zend_Controller_Action {

    /**
     * Initial settings of the class
     */
     public function init() {
        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();
        $this->view->lineNumber = Zend_Registry::get('config')->ambiente->linelimit;

        $this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->key = Snep_Dashboard_Manager::getKey(
            Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
            Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
            Zend_Controller_Front::getInstance()->getRequest()->getActionName());
    }

    /**
     * List all Tag's
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Tag")));


        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from("ccustos", array("codigo", "tipo", "nome", "descricao"))
                ->order("codigo");

        $stmt = $db->query($select);
        $data = $stmt->fetchAll();

        if(empty($data)){
            $this->view->error_message = $this->view->translate("You do not have registered cost centers. <br><br> Click 'Add cost center' to make the first registration
");

        }

        $this->view->types = array(
            'E' => $this->view->translate('Incoming'),
            'S' => $this->view->translate('Outgoing'),
            'O' => $this->view->translate('Other'));

        $this->view->costcenter = $data;

    }

    /**
     * Add new Tag's
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Tag"),
                    $this->view->translate("Add")));

        //Define the action and load form
        $this->view->action = "add" ;
        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );

        // After POST
        if ($this->_request->getPost()) {

            $form_isValid = true;

            $dados = $this->_request->getParams();
            $newId = Snep_CostCenter_Manager::get($dados['id']);

            if (count($newId) > 1) {
                $form_isValid = false;
                $this->view->error_message = $this->view->translate("Code already exists.");
                $this->renderScript('error/sneperror.phtml');
            }

            if ($form_isValid) {

                Snep_CostCenter_Manager::add($dados);

                //log-user
                if (class_exists("Loguser_Manager")) {
                    $loguser = array(
                      'table' => 'ccustos',
                      'registerid' => $dados['id'],
                      'description' => "Added New Tag - {$dados['id']} - {$dados['name']}"
                    );
                    Snep_LogUser::log("add", $loguser);
                }
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
    }

    /**
     * Edit Tag's
     */
    public function editAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Tag"),
                    $this->view->translate("Edit")));

        $id = $this->_request->getParam('id');
        $costCenter = Snep_CostCenter_Manager::get($id);

        $this->view->E = "";
        $this->view->S = "";
        $this->view->O = "";
        $this->view->$costCenter["tipo"] = "checked";
        $this->view->costcenter = $costCenter;

        //Define the action and load form
        $this->view->action = "edit" ;
        $this->view->disable = "disabled" ;
        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );

        // After POST
        if ($this->_request->getPost()) {

            $form_isValid = true;

            $dados = $this->_request->getParams();

            if ($form_isValid) {

                //log-user
                if (class_exists("Loguser_Manager")) {
                    $add = Snep_CostCenter_Manager::get($id);
                    Snep_CostCenter_Manager::insertLogCcustos("OLD", $add);
                }

                Snep_CostCenter_Manager::edit($dados);

                //log-user
                if (class_exists("Loguser_Manager")) {
                    $loguser = array(
                      'table' => 'ccustos',
                      'registerid' => $dados['id'],
                      'description' => "Edited Tag - {$dados['id']} - {$dados['name']}"
                    );
                    Snep_LogUser::log("update", $loguser);
                }
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

    }

    /**
     * Remove Tag's
     */
    public function removeAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Tag"),
                    $this->view->translate("Delete")));

        $id = $this->_request->getParam('id');

        $cdr_data = Snep_CostCenter_Manager::getCdr($id);

        if($cdr_data){

            $this->view->error_message = $this->view->translate("You have data connections with this cost center, so this exclusion is not permitted.");
            $this->renderScript('error/sneperror.phtml');

        }else{

            $this->view->id = $id;
            $this->view->remove_title = $this->view->translate('Delete Tag.');
            $this->view->remove_message = $this->view->translate('The cost center will be deleted. After that, you have no way get it back.');
            $this->view->remove_form = 'cost-center';
            $this->renderScript('remove/remove.phtml');

            if ($this->_request->getPost()) {

                //log-user
                if (class_exists("Loguser_Manager")) {
                    $data = Snep_CostCenter_Manager::get($id);
                    $loguser = array(
                      'table' => 'ccustos',
                      'registerid' => $id,
                      'description' => "Deleted Tag - {$id} - {$data['nome']}"
                    );
                    Snep_LogUser::log("delete", $loguser);
                }
                Snep_CostCenter_Manager::remove($_POST['id']);
                $this->_redirect($this->getRequest()->getControllerName());
            }

        }
    }

}
