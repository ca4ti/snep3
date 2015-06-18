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
 * Extension Group Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class ExtensionsGroupsController extends Zend_Controller_Action {

    /**
     * Initial settings of the class
     */
     public function init() {
        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();
        $this->view->lineNumber = Zend_Registry::get('config')->ambiente->linelimit;

        // 
        $this->extensionsAll = Snep_ExtensionsGroups_Manager::getExtensionsAll();
    }

    /**
     * indexAction - List all Extensions groups
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions Groups")));
        
        $db = Zend_Registry::get('db');

        $this->view->tra = array("admin" => $this->view->translate("Administrators"),
            "users" => $this->view->translate("Users"),
            "NULL" => $this->view->translate("None"),
            "all" => $this->view->translate("All"));

        $select = $db->select()
                ->from("groups", array("name", "inherit"))
                ->where("name not in ('all','users','administrator','NULL') ");

        $stmt = $db->query($select);
        $data = $stmt->fetchAll(); 

        $this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->key = Snep_Dashboard_Manager::getKey(Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
                                              Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
                                              Zend_Controller_Front::getInstance()->getRequest()->getActionName());
        
        $this->view->extensionsgroups = $data;

        
    }

    /**
     * addAction - Adds a group and their extensions in the database
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions Group"),
                    $this->view->translate("Add")));

        $this->view->extensionsAll = $this->extensionsAll;


        //Define the action and load form
        $this->view->action = "add" ;
        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );
        
        // After Post
        if ($this->getRequest()->getPost()) {

            $dados = $this->_request->getParams();
            $newId = Snep_ExtensionsGroups_Manager::getName($dados['name']);
            $form_isValid = true;
            
            if (count($newId) > 1) {
                $form_isValid = false;
                $this->view->error_message = "Name already exists.";
                $this->renderScript('error/sneperror.phtml');
            }

            if ($form_isValid) {

                $group = array( 'name' => $dados['name'],
                                'inherit' => $dados['type']
                                );

                $group = Snep_ExtensionsGroups_Manager::addGroup($group);

                if ($dados['duallistbox_group'] && $group) {

                    foreach ($dados['duallistbox_group'] as $id => $extension) {

                        $extensionsGroup = array('group' => $dados['name'],
                                                 'extensions' => $extension
                                                 );

                        Snep_ExtensionsGroups_Manager::addExtensionsGroup($extensionsGroup);
                    }
                }

                //log-user
                if (class_exists("Loguser_Manager")) {
                    // $nome = $dados['name'];
                    // Snep_LogUser::salvaLog("Adicionou Grupo de ramal", $nome, 11);
                    // $add = Snep_ExtensionsGroups_Manager::getGroupLog($nome);
                    // Snep_ExtensionsGroups_Manager::insertLogGroup("ADD", $add);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

    }

    /**
     * editAction - Edit extensions groups
     */
    public function editAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions Group"),
                    $this->view->translate("Edit")));

        $id = $this->_request->getParam('id');

        $group = Snep_ExtensionsGroups_Manager::getGroup($id);
        
        // Extensions for this group
        $groupExtensions = array();
        foreach (Snep_ExtensionsGroups_Manager::getExtensionsOnlyGroup($id) as $data) {
            array_push($groupExtensions,array('name' => $data['name']));
        }

        // All extensions not pertence for this group
        $groupAllExtensions = array();
        foreach ($this->extensionsAll as $data) {
            $_ingroup = false ;
            foreach ($groupExtensions as $value) {
                if ($value['name'] === $data['name']) {
                    $_ingroup = true ;
                    break;
                }
            }
            if (!$_ingroup) {
                array_push($groupAllExtensions,array('name' => $data['name'], 'group' => $data['group']));
            }    
        }


        $this->view->group = $group;

        $this->view->type = $group["inherit"] ;
        $this->view->extensionsAll = $groupAllExtensions;
        $this->view->groupExtensions = $groupExtensions;




        //Define the action and load form
        $this->view->action = "edit" ;
        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );

        if ($this->_request->getPost()) {

            $dados = $this->_request->getParams();

            $idGroup = $dados['id'];
            $newId = Snep_ExtensionsGroups_Manager::getName($dados['name']);

            $form_isValid = true;
            
            if (count($newId) > 1 && $dados['id'] != $dados['name']) {
                $form_isValid = false;
                $this->view->error_message = "Name already exists.";
                $this->renderScript('error/sneperror.phtml');
            }

            if($form_isValid){
                
                $extensions['group'] = "admin";
                foreach($groupExtensions as $key => $groupExtension){
                    $extensions['extensions'] = $groupExtension;
                    Snep_ExtensionsGroups_Manager::addExtensionsGroup($extensions);
                }

                if ($dados['duallistbox_group']) {

                    foreach ($dados['duallistbox_group'] as $id => $extensions) {
                        Snep_ExtensionsGroups_Manager::addExtensionsGroup(array('extensions' => $extensions, 'group' => $idGroup));
                    }
                }

                Snep_ExtensionsGroups_Manager::editGroup($dados);
                

                //log-user
                if (class_exists("Loguser_Manager")) {

                    Snep_LogUser::salvaLog("Editou Grupo de ramal", $dados['name'], 11);
                    $add = Snep_ExtensionsGroups_Manager::getGroupLog($dados['name']);
                    Snep_ExtensionsGroups_Manager::insertLogGroup("NEW", $add);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }

    }

    /**
     * removeAction - Remove a Extensions Group
     */
    public function removeAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions Group"),
                    $this->view->translate("Delete")));

        $id = $this->_request->getParam('id');
        $this->view->id = $id;

        //checks if the group is used in the rule 
        $regras = Snep_ExtensionsGroups_Manager::getValidation($id);

        if (count($regras) > 0) {

            $this->view->error_message = $this->view->translate("Cannot remove. The following routes are using this extensions group: ") . "<br />";
            foreach ($regras as $regra) {
                $this->view->error_message .= $regra['id'] . " - " . $regra['desc'] . "<br />\n";
            }

            $this->renderScript('error/sneperror.phtml');
        } else {

            $extensions = Snep_ExtensionsGroups_Manager::getExtensionsGroup($id);

            if (count($extensions) > 0) {
                $this->_redirect($this->getRequest()->getControllerName() . '/migration/id/' . $id);
            } else {
                $this->view->message = $this->view->translate("The extension group will be deleted. Are you sure?.");   
            }

            $this->view->remove_title = $this->view->translate('Delete Extension Group.'); 
            $this->view->remove_message = $this->view->translate('The extension group will be deleted. After that, you have no way get it back.'); 
            $this->view->remove_form = 'extensions-groups'; 
            $this->renderScript('remove/remove.phtml');

            if ($this->_request->getPost()) {

                Snep_ExtensionsGroups_Manager::delete($_POST['id']);
                
                //log-user
                if (class_exists("Loguser_Manager")) {

                    Snep_LogUser::salvaLog("Excluiu Grupo de ramal", $_POST['id'], 11);
                    $add = Snep_ExtensionsGroups_Manager::getGroupLog($_POST['id']);
                    Snep_ExtensionsGroups_Manager::insertLogGroup("DEL", $add);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
    }

    /**
     * migrationAction - Migrate extensions to other Extensions Group
     */
    public function migrationAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions Group"),
                    $this->view->translate("Migrate")));

        $id = $this->_request->getParam('id');

        $_allGroups = Snep_ExtensionsGroups_Manager::getAllGroup();

        foreach ($_allGroups as $group) {

            if ($group['name'] != $id && $group['name'] != "NULL") {
                $allGroups[$group['name']] = $group['name'];
            }
        }

        if (isset($allGroups)) {

            $this->view->groups = $allGroups;
            
        } else {

            $this->view->error_message = "This is the only group and it has extensions associated. You can migrate these extensions to a new group.";
            $this->renderScript('error/sneperror.phtml');
        }

        $this->view->id = $id;

        if ($this->_request->getPost()) {
            
            $extensions = Snep_ExtensionsGroups_Manager::getExtensionsOnlyGroup($_POST['customerid']);
            
            foreach($extensions as $key => $value){
                Snep_ExtensionsGroups_Manager::addExtensionsGroup(array('extensions' => $value['name'], 'group' => $_POST['group']));
            }

            Snep_ExtensionsGroups_Manager::delete($_POST['customerid']);

            $this->_redirect($this->getRequest()->getControllerName());
        }
        
    }

}
