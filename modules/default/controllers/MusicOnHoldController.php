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
 * Music on Hold Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class MusicOnHoldController extends Zend_Controller_Action {

    /**
     * Initial settings of the class
     */
    public function init() {

        $this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->key = Snep_Dashboard_Manager::getKey(
            Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
            Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
            Zend_Controller_Front::getInstance()->getRequest()->getActionName());
        $this->modes = array(
            'files' => $this->view->translate('Directory'),
            'mp3' => $this->view->translate('MP3'),
            'quietmp3' => $this->view->translate('Normal'),
            'mp3nb' => $this->view->translate('Without buffer'),
            'quietmp3nb' => $this->view->translate('Without buffer quiet'),
            'custom' => $this->view->translate('Custom application')
            );
        $this->view->lineNumber = Zend_Registry::get('config')->ambiente->linelimit;
        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" . $this->getRequest()->getControllerName();
    }

    /**
     * indexAction - List all Music on Hold sounds
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions")));

        Snep_SoundFiles_Manager::syncFiles();

        $sections = Snep_SoundFiles_Manager::getClasses();


        if(empty($sections)){
            $this->view->error_message = $this->view->translate("You do not have registered session. <br><br> Click 'Add Session' to make the first registration
");
        }
        $this->view->modes = $this->modes;
        $this->view->sections = $sections;

    }

    /**
     *  addAction - Add Sound File
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions"),
                    $this->view->translate("Add")));

        $viewModes = "";
        foreach($this->modes as $key => $value){
            $viewModes .= "<option value='".$key . "'>".$value." </option>\n";

        }
        //Define the action and load form
        $this->view->modes = $viewModes ;
        $this->view->action = "add" ;
        $this->renderScript( 'music-on-hold/addedit.phtml' );

        if ($this->_request->getPost()) {

            $dados = $this->_request->getParams();
            $classes = Snep_SoundFiles_Manager::getClasses();
            $form_isValid = true;

            if ($dados['base'] != '/var/lib/asterisk/moh/') {
                $this->view->error_message = $this->view->translate('Invalid Path');
                $this->renderScript('error/sneperror.phtml');
                $form_isValid = false;
            }

            if (file_exists($dados['directory'])) {
                $this->view->error_message = $this->view->translate('Directory already exists');
                $this->renderScript('error/sneperror.phtml');
                $form_isValid = false;
            }

            foreach ($classes as $name => $item) {

                if ($item['name'] == $dados['name']) {
                    $this->view->error_message = $this->view->translate('Music on hold class already exists');
                    $this->renderScript('error/sneperror.phtml');
                    $form_isValid = false;
                }

                $fullPath = $dados['base'] . $dados['directory'];
                
                if ($item['directory'] == $fullPath) {
                    $this->view->error_message = $this->view->translate('Directory already exists');
                    $this->renderScript('error/sneperror.phtml');
                    $form_isValid = false;
                }
            }

            if ($form_isValid) {
                $_POST['directory'] = $_POST['base'] . $_POST['directory'];
                Snep_SoundFiles_Manager::addClass($_POST);
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
        
    }

    /**
     * Edit Carrier
     */
    public function editAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions"),
                    $this->view->translate("Edit")));

        $file = $this->_request->getParam("file");
        $data = Snep_SoundFiles_Manager::getClasse($file);

        $viewModes = "";
        foreach($this->modes as $key => $value){
            $viewModes .= ($key == $data['mode']) ? "<option value='".$key . "' selected >".$value." </option>\n": "<option value='".$key . "'>".$value." </option>\n";
        }

        $this->view->modes = $viewModes;

        $directory = explode("/", $data['directory']);
        $directoryName = array_pop($directory);

        $this->view->directoryName = $directoryName;
        $originalName = $data['name'];
        
        $this->view->file = $data;

        //Define the action and load form
        $this->view->disabled = 'disabled';
        $this->view->action = "edit" ;
        $this->renderScript( 'music-on-hold/addedit.phtml' );

        if ($this->_request->getPost()) {

            $dados = $this->_request->getParams();

            $class = array(
                'name' => $_POST['name'],
                'mode' => $_POST['mode'],
                'directory' => $_POST['base'] . $_POST['directory']);

            
            Snep_SoundFiles_Manager::editClass($originalName, $class);
            $this->_redirect($this->getRequest()->getControllerName());
            
        }

    }

    /**
     * Remove a Carrier
     */
    public function removeAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions"),
                    $this->view->translate("Remove")));

        $file = $this->_request->getParam('file');

        $this->view->class = Snep_SoundFiles_Manager::getClasse($file);
        $this->view->message = $this->view->translate("You are removing a music on hold class, it has some audio files attached to it.");
        $this->view->confirmation = $this->view->translate("Delete Sound Files?");
        $this->view->id = $file;
        
        if ($this->_request->getPost()) {
            
            if ($_POST['delete']) {
                $class = Snep_SoundFiles_Manager::getClasse($_POST['customerid']);
                Snep_SoundFiles_Manager::removeClass($class);
            }
            $this->_redirect($this->getRequest()->getControllerName());
            
        }
        
    }

    /**
     * fileAction
     */
    public function fileAction() {

        $file = $this->_request->getParam('class');

        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" .
                $this->getRequest()->getControllerName();

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions"),
                    $this->view->translate("File"),
                    $file));

        $class = Snep_SoundFiles_Manager::getClasse($file);
        $files = Snep_SoundFiles_Manager::getClassFiles($class);
        
        if(empty($files)){
            $this->view->error_message = $this->view->translate("You do not have registered file. <br><br> Click 'Add File' to make the first registration
");
            
        }

        $this->view->files = $files;

        $arrayInf = array('data' => null,
            'descricao' => $this->view->translate('Not Found'),
            'secao' => $class['name']);

        if (isset($this->view->files)) {
            foreach ($this->view->files as $file => $list) {
                if (!isset($list['arquivo'])) {
                    $arrayInf['arquivo'] = $file;
                    $this->view->files[$file] = $arrayInf;
                    (!isset($errors) ? $errors = "" : false);
                    $errors .= $this->view->translate("File {$file} not found") . "<br/>";
                }
            }
        }

        ( isset($errors) ? $this->view->error = array('error' => true, 'message' => $errors) : false);

        
    }

    /**
     * AddfileAction - Add file
     */
    public function addfileAction() {

        $className = $this->_request->getParam('class');
        $class = Snep_SoundFiles_Manager::getClasse($className);

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions"),
                    $this->view->translate("Add File")));

        $this->view->section = $class['name'];
        
        if ($this->_request->getPost()) {
           
            $class = Snep_SoundFiles_Manager::getClasse($_POST['section']);
            $form_isValid = true;
            $dados = $this->_request->getParams();

            $invalid = array('â', 'ã', 'á', 'à', 'ẽ', 'é', 'è', 'ê', 'í', 'ì', 'ó', 'õ', 'ò', 'ú', 'ù', 'ç', " ", '@', '!');
            $valid = array('a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'o', 'u', 'u', 'c', "_", '_', '_');

            $originalName = str_replace($invalid, $valid, $_FILES['file']['name']);
            $files = Snep_SoundFiles_Manager::get($originalName);

            if ($files) {
                $this->view->error_message = $this->view->translate("File already exists");
                $this->renderScript('error/sneperror.phtml');
                $form_isValid = false;
            }

            if ($form_isValid) {

                $uploadName = $_FILES['file']['tmp_name'];
                $arq_tmp = $class['directory'] . "/tmp/" . $originalName;
                $arq_dst = $class['directory'] . "/" . $originalName;
                $arq_bkp = $class['directory'] . "/backup/" . $originalName;
                $arq_orig = $class['directory'] . "/" . $originalName;

                exec("mv $uploadName $arq_tmp");

                if ($_POST['gsm']) {
                    $fileNe = basename($arq_dst, '.wav');
                    exec("sox $arq_tmp -r 8000 {$fileNe}.gsm");
                    $originalName = basename($originalName, '.wav') . ".gsm";
                } else {
                    exec("sox $arq_tmp -r 8000 -c 1 -e signed-integer -b 16 $arq_dst");
                }

                if (file_exists($arq_dst) || file_exists($fileNe)) {
                    Snep_SoundFiles_Manager::
                    addClassFile(array('arquivo' => $originalName,
                        'descricao' => $dados['description'],
                        'data' => new Zend_Db_Expr('NOW()'),
                        'tipo' => 'MOH',
                        'secao' => $dados['section']));
                }
                $this->_redirect($this->getRequest()->getControllerName() . "/file/class/$className/");
            }
        }
        
    }

    /**
     * editfileAction - Edit file
     */
    public function editfileAction() {

        $fileName = $this->_request->getParam('file');
        $class = $this->_request->getParam('class');

        $className = Snep_SoundFiles_Manager::getClasse($class);
        $files = Snep_SoundFiles_Manager::getClassFiles($className);
        $_files = array('arquivo' => '', 'descricao' => '',
            'tipo' => '', 'secao' => $class, 'full' => '');

        foreach ($files as $name => $file) {
            if ($name == $fileName) {
                if (isset($file['arquivo'])) {
                    $_files = $file;
                }
            }
        }

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions"),
                    $this->view->translate("Edit File")));

        $this->view->filename = $fileName;
        $this->view->file = $_files;
        $this->view->section = $_files['secao'];
        $this->view->originalPath = $_files['full'];

        if ($this->_request->getPost()) {

            $class = Snep_SoundFiles_Manager::getClasse($_POST['section']);
            $form_isValid = $form->isValid($_POST);
            $dados = $this->_request->getParams();

            $invalid = array('â', 'ã', 'á', 'à', 'ẽ', 'é', 'è', 'ê', 'í', 'ì', 'ó', 'õ', 'ò', 'ú', 'ù', 'ç', " ", '@', '!');
            $valid = array('a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'o', 'u', 'u', 'c', "_", '_', '_');

            if ($_FILES['file']['size'] > 0) {

                $oldName = $_POST['originalName'];
                $originalName = str_replace($invalid, $valid, $_FILES['file']['name']);
                $files = Snep_SoundFiles_Manager::get($originalName);
                $form_isValid = true;

                if ($files) {
                    $this->view->error_message = $this->view->translate("The file already exists");
                    $this->renderScript('error/sneperror.phtml');
                    $form_isValid = false;
                }

                if ($form_isValid) {
                    $uploadName = $_FILES['file']['tmp_name'];
                    $arq_tmp = $class['directory'] . "/tmp/" . $originalName;
                    $arq_dst = $class['directory'] . "/" . $originalName;
                    $arq_bkp = $class['directory'] . "/backup/" . $originalName;
                    $arq_orig = $class['directory'] . "/" . $originalName;

                    exec("mv $uploadName $arq_tmp");

                    $fileNe = basename($arq_dst, 'wav');

                    if ($_POST['gsm']) {
                        exec("sox $arq_tmp -r 8000 {$fileNe}.gsm");
                        $exists = file_exists($fileNe . "gsm");
                    } else {
                        exec("sox $arq_tmp -r 8000 -c 1 -e signed-integer -b 16 $arq_dst");
                        $exists = file_exists($arq_dst);
                    }
                }

                if ($exists) {
                    exec("rm -f {$_POST['originalPath']}");

                    Snep_SoundFiles_Manager::remove($oldName);
                    Snep_SoundFiles_Manager::
                    add(array('arquivo' => $originalName,
                        'descricao' => $dados['description'],
                        'data' => new Zend_Db_Expr('NOW()'),
                        'tipo' => 'MOH',
                        'secao' => $dados['section']));
                }
            } else {
                $originalName = $_POST['originalName'];
                Snep_SoundFiles_Manager::
                editClassFile(array('arquivo' => $originalName,
                    'descricao' => $dados['description'],
                    'data' => new Zend_Db_Expr('NOW()'),
                    'tipo' => 'MOH',
                    'secao' => $dados['section']));
            }

            $this->_redirect($this->getRequest()->getControllerName() . "/file/class/{$className['name']}/");
        }

    }

    /**
     * removefileAction - Remove file
     */
    public function removefileAction() {
        
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Music on Hold Sessions"),
                    $this->view->translate("Delete File")));

        $file = $this->_request->getParam('file');
        $class = $this->_request->getParam('class');

        $className = Snep_SoundFiles_Manager::getClasse($class);
        $files = Snep_SoundFiles_Manager::getClassFiles($className);

        $this->view->id = $class;
        $this->view->remove_title = $this->view->translate('Delete music on hold files.'); 
        $this->view->remove_message = $this->view->translate('The music on hold files will be deleted. After that, you have no way get it back.'); 
        $this->view->remove_form = 'music-on-hold'; 
        $this->view->remove_action = 'removefile'; 
        $this->renderScript('remove/remove.phtml');
        $_SESSION['musiconhold'] = $files;

        if ($this->_request->getPost()) {

            $files = $_SESSION['musiconhold'];
            
            foreach ($files as $name => $path) {
                if ($file == $name) {

                    exec("rm {$path['full']} ");

                    if (!file_exists($path['full'])) {
                        Snep_SoundFiles_Manager::remove($name, $path['secao']);
                    }
                }
            }
            
            unset($_SESSION['musiconhold']);
            $class = $_POST['id'];
            $this->_redirect($this->getRequest()->getControllerName() . "/file/class/$class");
        }
    }

}