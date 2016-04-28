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
     * Initial settings of the class
     */
     public function init() {
        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();
        $this->view->lineNumber = Zend_Registry::get('config')->ambiente->linelimit;

        $this->queuesAll = Snep_QueuesGroups_Manager::getQueuesAll();

        $this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->key = Snep_Dashboard_Manager::getKey(
            Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
            Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
            Zend_Controller_Front::getInstance()->getRequest()->getActionName());
    }


    /**
     * indexAction - List all Queues groups
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Queues Groups")));

        $db = Zend_Registry::get('db');

        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();
        $this->view->user = $username;

        $select = $db->select()->from("group_queues");

        $stmt = $db->query($select);
        $queuesGroup = $stmt->fetchAll(); 
       
        $this->view->queuesgroups = $queuesGroup;
       
    }

    /**
     * addAction - Add pickup groups
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Queues Groups"),
            $this->view->translate("Add")));
        
        $this->view->nomembers = $this->queuesAll;

        //Define the action and load form
        $this->view->action = "add" ;
        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );

        //After POST
        if ($this->getRequest()->getPost()) {

            $form_isValid = true;
            $dados = $this->_request->getParams();

            $db = Zend_Registry::get('db');
            $groupName = $dados['name'];

            $sqlValidName = "SELECT * from group_queues where name = '$groupName'";
            $selectValidName = $db->query($sqlValidName);
            $resultGetId = $selectValidName->fetch();
            
            if ($resultGetId) {
                $form_isValid = false;
                $message = $this->view->translate("Name already exists.");
                $this->_helper->redirector('sneperror','error',null,array('error_message'=>$message));
            }

            if ($form_isValid) {
                $namegroup = array('nome' => $dados['name']);
                $groupId = Snep_QueuesGroups_Manager::addGroup($namegroup);

                $lastId = Snep_QueuesGroups_Manager::lastId();
                
                if($dados['duallistbox_group']){

                    foreach ($dados['duallistbox_group'] as $key => $queue) {

                        $queuesGroup = array('id_group' => $lastId,'id_queue' => $queue);
                        $this->view->queue = Snep_QueuesGroups_Manager::addQueuesGroup($queuesGroup);
                    }
                }

                $this->_redirect("/" . $this->getRequest()->getControllerName() . "/");
            }
            
        }

    }

    /**
     * editAction - Edit queus group
     */
    public function editAction() {
        
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Queues Groups"),
            $this->view->translate("Edit")));

        $id = $this->_request->getParam('id');
        $group = Snep_QueuesGroups_Manager::get($id);

        $members = Snep_QueuesGroups_Manager::getMembers($id);

        // Mount list member of the group
        $membersAll = $nomembers = array();
        foreach($this->queuesAll as $queue){
            $_ismember = false ;
            foreach($members as $value){
                if ($queue['id'] === $value['id_queue']) {
                    $_ismember = true ;
                    break ;
                }
            }
            if ($_ismember) {
                array_push($membersAll, array('id' => $queue['id'], 'name' => $queue['name']));
            } else {
                array_push($nomembers, array('id' => $queue['id'], 'name' => $queue['name']));
            }
        }

        $this->view->group = $group['name'];
        $this->view->membersAll = $membersAll;
        $this->view->nomembers = $nomembers;
        
        //Define the action and load form
        $this->view->action = "edit" ;
        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );

        // After POST
        if ($this->_request->getPost()) {
            
            $form_isValid = true;

            $dados = $this->_request->getParams();

            $db = Zend_Registry::get('db');
            $groupName = $dados['name'];

            $sqlValidName = "SELECT * from group_queues where name = '$groupName'";
            $selectValidName = $db->query($sqlValidName);
            $resultGetId = $selectValidName->fetch();
            
            if ($resultGetId && $dados['name'] != $groupName) {
                $form_isValid = false;
                $message = $this->view->translate("Name already exists.");
                $this->_helper->redirector('sneperror','error',null,array('error_message'=>$message));
            }
            
            if ($form_isValid) {

                
                $this->view->group = Snep_QueuesGroups_Manager::editGroup(array('name' => $dados['name'], 'id' => $dados['id']));
                
                // Remove members
                Snep_QueuesGroups_Manager::deleteMembers($id);

                if($dados["duallistbox_group"]){

                    foreach($dados["duallistbox_group"] as $key => $item){

                        $queuesGroup = array('id_group' => $id,'id_queue' => $item);
                        $this->view->queue = Snep_QueuesGroups_Manager::addQueuesGroup($queuesGroup);
                        
                    }
                }

                $this->_redirect($this->getRequest()->getControllerName());
                
            }
        }

    }

    /**
     *  Edit members
     */
    public function membersAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
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
     * removeAction - Delete queues group
     * @throws Zend_Controller_Action_Exception
     */
    public function removeAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Queues Group"),
                    $this->view->translate("Delete")));

        $id = $this->_request->getParam('id');

        $this->view->id = $id;
        $this->view->remove_title = $this->view->translate('Delete Queues Group.'); 
        $this->view->remove_message = $this->view->translate('The queues group will be deleted. After that, you have no way get it back.'); 
        $this->view->remove_form = 'queues-groups'; 
        $this->renderScript('remove/remove.phtml');

        if ($this->_request->getPost()) {

            Snep_QueuesGroups_Manager::deleteMembers($_POST['id']);
            Snep_QueuesGroups_Manager::deleteGroup($_POST['id']);
            $this->_redirect($this->getRequest()->getControllerName());
        }
    }

}

?>