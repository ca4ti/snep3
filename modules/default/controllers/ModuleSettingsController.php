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
 * Module Settings Controller - System settings controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class ModuleSettingsController extends Zend_Controller_Action {


    /**
     * Initial settings of the class
     */
    public function init() {

        $this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->key = Snep_Dashboard_Manager::getKey(
            Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
            Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
            Zend_Controller_Front::getInstance()->getRequest()->getActionName());
    }


    /**
     * indexAction - List parameters
     */
    public function indexAction() {
        
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Module Settings")));

        $config = Zend_Registry::get('config');
        
        $path = $config->system->path->base."/modules";

        $directory = scandir($path);
        $data = array();
        $cont_modules = count($directory);
        $all_modules = "";

        // receives the form parameter through json file of modules
        foreach($directory as $dir => $folder){
            
            if($folder != '.' && $folder != 'default' && $folder != '..'){
                $filename = $path."/".$folder."/configs/config.json";
                
                if(file_exists($filename)){
                    $data[$dir] = file_get_contents($filename);
                }
            }
        }

        if(empty($data)){
            if($cont_modules > 3){
                // It has modules without setting
                $this->view->error_message = $this->view->translate("Its modules do not require extra settings"); 
            }else{
                // No modules
                $this->view->error_message = $this->view->translate("Your Snep not have additional modules");
            }
            
        }else{

            // mounts array with form data
            $cont = 0;
            foreach ($data as $module => $json) {
                
                $values = json_decode($json);
                $view[$cont]["name"] = $values->nome;
                $module_name = $values->nome;
                $all_modules .= "#".$values->nome.",";
                unset($values->nome);
            
                foreach($values as $key => $value){

                    if($value->type == "select"){

                        $view[$cont]["view"][$key]["type"] = $value->type;
                        $view[$cont]["view"][$key]["key"] = $module_name."=".$value->key;
                        $view[$cont]["view"][$key]["label"] = $value->label;

                        foreach($value->option as $x => $option){
                        
                            $view[$cont]["view"][$key]["select"][$x]["label"] = $option->label;
                            $view[$cont]["view"][$key]["select"][$x]["key"] = $option->key;
                            
                            if(isset($option->selected)){
                                $view[$cont]["view"][$key]["select"][$x]["selected"] = true;    
                            
                            }else{
                                $view[$cont]["view"][$key]["select"][$x]["selected"] = "";
                            }
                        }

                    }elseif($value->type == "separator"){
                        $view[$cont]["view"][$key]["type"] = $value->type;    
                    
                    }else{
                    
                        $view[$cont]["view"][$key]["type"] = $value->type;
                        $view[$cont]["view"][$key]["key"] = $module_name."=".$value->key;
                        $view[$cont]["view"][$key]["label"] = $value->label;
                        (isset($value->placeholder))? $view[$cont]["view"][$key]["placeholder"] = $value->placeholder : $view[$cont]["view"][$key]["placeholder"] = "";
                        
                    } 
                }
                $cont++;
            }
            
            //get data in database
            foreach($view as $i => $val){

                $records = Snep_ModuleSettings_Manager::get($val["name"]);

                if(!empty($records)){

                    foreach($val["view"] as $x => $form){
                        foreach($records as $y => $database){

                            if($form["type"] == "text" || $form["type"] == "password"){

                                $exp = explode("=", $form["key"]);

                                if($exp[1] == $database["config_name"]){
                                    $view[$i]["view"][$x]["value"] = $database["config_value"];
                                }
                             
                            }elseif($form["type"] == "select"){
                                
                                $exp = explode("=", $form["key"]);
                                if($exp[1] == $database["config_name"]){
                                    $view[$i]["view"][$x]["selected"] = $database["config_value"];    
                                }

                            }elseif($form["type"] == "checkbox"){
                                                     
                                $exp = explode("=", $form["key"]);
                                if($exp[1] == $database["config_name"]){
                                
                                    if($database["config_value"] == "on"){
                                        $view[$i]["view"][$x]["checked"] = true;    
                                    }
                                }
                            }
                        }
                    }
                }  
            }         
            
            $this->view->data = $view;
            
            $this->view->allModules = substr($all_modules,0,-1);

        }
        
        // Verify if the request is a post
        if ($this->_request->getPost()) {

            $formData = $this->getRequest()->getParams();

            unset($formData["controller"]);
            unset($formData["action"]);
            unset($formData["module"]);
            unset($formData["signup"]);
            
            // capture model name
            foreach($formData as $key => $value){
                $res = explode("=",$key);
            }
            
            // remove data in database of module
            Snep_ModuleSettings_Manager::delConfig($res[0]);
            
            // Insert key and value    
            foreach($formData as $key => $value){

                if($value != ''){

                    $res = explode("=",$key);

                    $result["config_module"] = $res[0];
                    $result["config_name"] = $res[1];
                    $result["config_value"] = $value;

                    Snep_ModuleSettings_Manager::addConfig($result);

                }
            } 

            // redirect
            $this->_redirect('/');              
        }

    }

}
