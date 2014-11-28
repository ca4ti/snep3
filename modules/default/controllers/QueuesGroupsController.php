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
 * Queeus Groups Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class QueuesGroupsController extends Zend_Controller_Action {

    /**
     *
     * @var Zend_Form
     */
    protected $form;

    /**
     * indexAction - List all Queues groups
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Queues Groups")));

        $db = Zend_Registry::get('db');

        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();
        $this->view->user = $username;

        $select = $db->select()->from("group_queues");

        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("`$field` like '%$query%'");
        }

        $this->view->order = Snep_Order::setSelect($select, array("id", "name"), $this->_request);


        $page = $this->_request->getParam('page');
        $this->view->page = ( isset($page) && is_numeric($page) ? $page : 1 );

        $this->view->filtro = $this->_request->getParam('filtro');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);

        $paginator->setCurrentPageNumber($this->view->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $this->view->queuesgroups = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/";
        $this->view->PAGE_URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/";

        $opcoes = array("name" => $this->view->translate("Name"));

        // Filter Form 
        $filter = new Snep_Form_Filter();
        $filter->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($opcoes);
        $filter->setFieldValue($this->_request->getPost('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add",
                "display" => $this->view->translate("Add Queues Group"),
                "css" => "include"),
        );
    }

    /**
     * addAction - Add pickup groups
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Manage"),
            $this->view->translate("Queues Groups"),
            $this->view->translate("Add")));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form_xml = new Zend_Config_Xml("modules/default/forms/queueGroup.xml");
        $form = new Snep_Form($form_xml->general);
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/add');
        
        $form->getElement('name')->setLabel($this->view->translate('Name'));
        
        if ($this->getRequest()->getPost()) {
            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $db = Zend_Registry::get('db');
            $groupName = $dados['name'];

            $sqlValidName = "SELECT * from group_queues where name = '$groupName'";
            $selectValidName = $db->query($sqlValidName);
            $resultGetId = $selectValidName->fetch();
            
            if ($resultGetId) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }

            if ($form_isValid) {
                $namegroup = array('nome' => $dados['name']);
                $groupId = Snep_QueuesGroups_Manager::addGroup($namegroup);

                $this->_redirect("/" . $this->getRequest()->getControllerName() . "/");
            }
            
        }

        $this->view->form = $form;
    }

    /**
     *  Edit members
     */
    public function membersAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Manage"),
            $this->view->translate("Queues Groups"),
            $this->view->translate("Members")));

        $id = $this->_request->getParam('id');
        $members = Snep_QueuesGroups_Manager::getMembers($id);
        $queuesAll = Snep_QueuesGroups_Manager::getQueuesAll();

        foreach($queuesAll as $key => $queue){
            foreach($members as $cha => $member){

                if($queue['id'] == $member["id_queue"]){
                    $queuesAll[$key]['belongs'] = true;
                }
            }
        }
        
        $this->view->queues = $queuesAll;
        $this->view->id = $id;

        if ($this->_request->isPost()) {

            $dados = array();
            $dados = $_POST;
            $id = (int)$dados["id_group"];
            
            Snep_QueuesGroups_Manager::deleteMembers($id);

            foreach ($dados as $key => $queue) {

                $queuesGroup = array('id_group' => $id,'id_queue' => $key);
                $this->view->queue = Snep_QueuesGroups_Manager::addQueuesGroup($queuesGroup);
            }

            $this->_redirect("/" . $this->getRequest()->getControllerName() . "/");
        }
    }

    /**
     * editAction - Edit queus group
     */
    public function editAction() {
        
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Manage"),
            $this->view->translate("Queues Groups"),
            $this->view->translate("Edit")));

        $id = $this->_request->getParam('id');
        $group = Snep_QueuesGroups_Manager::get($id);

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form_xml = new Zend_Config_Xml("modules/default/forms/queueGroup.xml");
        $form = new Snep_Form($form_xml->general);
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/edit/group/$id");

        $name = $form->getElement('name')->setValue($group['name']);

        if ($this->_request->getPost()) {
            $form_isValid = $form->isValid($_POST);

            $dados = $this->_request->getParams();

            $db = Zend_Registry::get('db');
            $groupName = $dados['name'];

            $sqlValidName = "SELECT * from group_queues where name = '$groupName'";
            $selectValidName = $db->query($sqlValidName);
            $resultGetId = $selectValidName->fetch();
            
            if ($resultGetId) {
                $form_isValid = false;
                $form->getElement('name')->addError($this->view->translate('Name already exists.'));
            }
            
            if ($form_isValid) {
                
                $this->view->group = Snep_QueuesGroups_Manager::editGroup(array('name' => $dados['name'], 'id' => $dados['group']));
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

        $this->view->form = $form;
    }

    /**
     * deleteAction - Delete queues group
     * @throws Zend_Controller_Action_Exception
     */
    public function deleteAction() {

        $id = $this->_request->getParam('id');

        Snep_QueuesGroups_Manager::deleteMembers($id);
        Snep_QueuesGroups_Manager::deleteGroup($id);

        $this->_redirect($this->getRequest()->getControllerName());
    }

}

?>