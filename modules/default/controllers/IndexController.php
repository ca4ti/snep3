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
 * controller index
 */
class IndexController extends Zend_Controller_Action {
    
    /**
     * indexAction - List dashboard
     */
    public function indexAction() {

        // checked if snep registred in itc
        if( $_SESSION['registered'] != true && $_SESSION['noregister'] != true){
            
            $this->view->headTitle($this->view->translate("Register"));
            $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                        $this->view->translate("Register")));

            // Get configuration properties from Zend_Registry
            $config = Zend_Registry::get('config');

            $distro = $config->system->itc_distro;
            $required_register = $config->system->itc_required;

            $viewnoregister = false;
            if($required_register == "true"){
                $viewnoregister = true;
            }
            
            $this->view->viewnoregister = $viewnoregister;

            // Ping in ITC
            $url = trim($config->system->itc_address);
            $url .= "devices/ping/".$_SESSION['uuid'];

            $http = curl_init($url);

            curl_setopt($http, CURLOPT_SSL_VERIFYPEER, false);
            $status = curl_getinfo($http, CURLINFO_HTTP_CODE);
            curl_setopt($http, CURLOPT_RETURNTRANSFER,1);
            $http_response = curl_exec($http);          
            $httpcode = curl_getinfo($http, CURLINFO_HTTP_CODE);
            curl_close($http);

            switch ($httpcode) {
                case 200:
                    $this->view->country = Snep_Register_Manager::getCountry();                
                    $this->view->state = Snep_Register_Manager::getState();                                
                    break;
                case 500:
                    $this->view->error = $this->view->translate("Internal Server Error. Please try later");
                    break;
                case false:
                    $this->view->error = $this->view->translate("To register your Snep you must be connected to an internet network. Check your connection and try again.");
                    break;
                default:
                    $this->view->error = $this->view->translate("Error: Code ") . $httpcode . $this->view->translate(". Please contact the administrator.");
                    break;
            }

            $layout = Zend_Layout::getMvcInstance(); 
            $layout->setLayout('register');

            if ($this->_request->isPost()) {
                  
                // register User    
                if($_POST['save'] == 'register'){
                    
                    unset($_POST['save']);

                    $data = $_POST;
                    $data['device_type_id'] = 1;
                    $data['device_uuid'] = $_SESSION["uuid"];

                    if($_POST['address']){
                        $data['address'] = $_POST['address'] ;
                    } 

                    if($_POST['zipcode']){
                        $data['zipcode'] = $_POST['zipcode'];
                    } 

                    if($_POST['phone']){
                        $data['tel'] = $_POST['phone']; 
                    } 

                    if($_POST['cell']){
                        $data['cel'] = $_POST['cell']; 
                    } 
                    
                    if(isset($distro)){
                        $data['distribution_id'] = $distro;
                    }
                    
                    $content = json_encode($data);
                    $url = trim($config->system->itc_address) . "auth/sign";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

                    $json_response = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    switch ($httpcode) {
                        case 201:
                            $this->view->confirm = true;                
                            break;
                        case 500:
                            $this->view->error = $this->view->translate("Internal Server Error. Please try later");
                            break;
                        case 409:
                            $this->view->error = $this->view->translate("User already exists.");
                            break;
                        case false:
                            $this->view->error = $this->view->translate("Without internet connection.");
                            break;
                        default:
                            $this->view->error = $this->view->translate("Error: Code ") . $httpcode . $this->view->translate(". Please contact the administrator.");
                            break;
                    }
                     
                    $layout = Zend_Layout::getMvcInstance(); 
                    $layout->setLayout('register'); 
                    
                }elseif($_POST['save'] == 'confirm'){
                
                    unset($_POST['save']);
                    $dado = $_POST;
                    $dado['device_uuid'] = $_SESSION["uuid"];

                    $content = json_encode($dado);
                    $url = trim($config->system->itc_address) . "auth/confirm_hash";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

                    $json_response = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                    
                    switch ($httpcode) {
                        case 200:
                            $data = json_decode($json_response);
                            $api_key = $data->details->api_key;               
                            $client_key = $data->details->client_key;
                            Snep_Register_Manager::registerITC($api_key,$client_key); 

                            //register distributions
                            $distributions = $data->details->distributions;
                            Snep_Register_Manager::addDistributions($distributions);
                                
                            $this->view->registerd = true;              
                            break;
                        case 500:
                            $this->view->error = $this->view->translate("Internal Server Error. Please try later");
                            break;
                        case 409:
                            $this->view->error = $this->view->translate("User already exists.");
                            break;
                        case false:
                            $this->view->error = $this->view->translate("Without internet connection.");
                            break;
                        default:
                            $this->view->error = $this->view->translate("Error: Code ") . $httpcode . $this->view->translate(". Please contact the administrator.");
                            break;
                    }

                    $layout = Zend_Layout::getMvcInstance(); 
                    $layout->setLayout('register'); 

                }elseif($_POST['save'] == 'opensnep'){

                    $title = $this->view->translate('Welcome to Intercomunexão.');
                    $message = $this->view->translate('By registering your SNEP, you connect to the portal of Intercomunexão. Portal where you will have access to exclusive solutions, high-quality support and a constantly evolving technology. <br>Access: itc.opens.com.br');
                    Snep_Notifications::addNotification($title,$message);

                    // go to snep
                    $_SESSION['registered'] = true;
                    $this->_redirect('/');

                }elseif($_POST['save'] == 'login'){
                    
                    // user already registered
                    unset($_POST['save']);

                    $config = Zend_Registry::get('config');
                    $distro = $config->system->itc_distro;

                    $data = $_POST;
                    $data['device_type_id'] = 1;
                    $data['device_uuid'] = $_SESSION["uuid"];

                    if(isset($distro)){
                        $data['distribution_id'] = $distro;
                    }

                    $content = json_encode($data);
                    $url = trim($config->system->itc_address) . "auth/slogin";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                    $json_response = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                    
                    switch ($httpcode) {
                        case 200:
                            $data = json_decode($json_response);

                            $api_key = $data->details->api_key;               
                            $client_key = $data->details->client_key;

                            $distributions = $data->details->distributions;
                            Snep_Register_Manager::addDistributions($distributions);
                            Snep_Register_Manager::registerITC($api_key,$client_key); 
                            $this->view->registerd = true;            
                            break;
                        case 500:
                            $this->view->error = $this->view->translate("Internal Server Error. Please try later");
                            break;
                        case 401:
                            $this->view->error = $this->view->translate("User or password incorrect.");
                            break;
                        case false:
                            $this->view->error = $this->view->translate("Without internet connection.");
                            break;
                        default:
                            $this->view->error = $this->view->translate("Error: Code ") . $httpcode . $this->view->translate(". Please contact the administrator.");
                            break;
                    }
                     
                    $layout = Zend_Layout::getMvcInstance(); 
                    $layout->setLayout('register'); 
                    
                }elseif($_POST['save'] == 'noregister'){

                    // no ITC register in time
                    Snep_Register_Manager::noregister();
                    $_SESSION['noregister'] = true; 
                    $this->_redirect('/');
                }
            }

        }else{

            $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Dashboard")));

            $idLastNotification = Snep_Notifications::getDateLastNotification();
            $configs = Snep_Config::getConfiguration('CORE','HOST_NOTIFICATION');
            
            // Ping in ITC
            $url = $configs["config_value"].$idLastNotification;
                        
            $http = curl_init($url);

            curl_setopt($http, CURLOPT_SSL_VERIFYPEER, false);
            $status = curl_getinfo($http, CURLINFO_HTTP_CODE);
            curl_setopt($http, CURLOPT_RETURNTRANSFER,1);
            $http_response = curl_exec($http);          
            $httpcode = curl_getinfo($http, CURLINFO_HTTP_CODE);
            curl_close($http);
            
            switch ($httpcode) {
                case 200:
                    
                    $notifications = json_decode($http_response);
                    foreach($notifications as $item => $notification){
                        Snep_Notifications::addNotification(utf8_decode($notification->title),utf8_decode($notification->message),$notification->id);    
                    }
                    break;
                case 500:
                    
                    $notificationWarning = Snep_Notifications::getNotificationWarning();
                    if($notificationWarning == false){
                        
                        $title = $this->view->translate('Warning');
                        $message = $this->view->translate('Internal Server Error. <br> To receive notifications about new features, modules and related news Snep you must be connected to an internet network. Check your connection and try again.');
                        Snep_Notifications::addNotification($title,$message);
                    }    
                    
                    break;
                case false:

                    $notificationWarning = Snep_Notifications::getNotificationWarning();
                    if($notificationWarning == false){

                        $title = $this->view->translate('Warning');
                        $message = $this->view->translate('Error notifications.<br> To receive notifications about new features, modules and related news Snep you must be connected to an internet network. Check your connection and try again.');
                        Snep_Notifications::addNotification($title,$message);
                    }
                    
                    break;
                default:

                    $notificationWarning = Snep_Notifications::getNotificationWarning();
                    if($notificationWarning == false){

                        $title = $this->view->translate('Warning');
                        $message = $this->view->translate("Error: Code ") . $httpcode . $this->view->translate(". Please contact the administrator for receiver notifications.");
                        Snep_Notifications::addNotification($title,$message);
                    }
                    break;
            }            

            $modelos = Snep_Dashboard_Manager::getModelos();

            if (isset($_GET['dashboard_add'])) {
                Snep_Dashboard_Manager::add($_GET['dashboard_add']);
            }
            
            $this->view->dashboard = Snep_Dashboard_Manager::getArray($modelos);
            if(!$this->view->dashboard)$this->_helper->redirector('add', 'index');
        }       
    }
    
    /**
     * addAction - Add item in dashboard
     */
    public function addAction() {
        
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Dashboard"),
                    $this->view->translate("Edit")));

        $url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        $usados = Snep_Dashboard_Manager::get();
        $modelos = Snep_Dashboard_Manager::getModelos();

        foreach($modelos aS $key=>$value){
            
            $array[$key]['name'] = $value['nome'];
            $array[$key]['desc'] = $value['descricao'];
        }

        foreach($array as $id => $iten){
            foreach($usados as $x => $used){
                if($id == $used){
                    $array[$id]['used'] = "checked";
                }
            }
        }

        $this->view->list = $array;        
        if ($this->_request->getPost()) {
            
            $dados = $this->_request->getParams();
            
            foreach($dados['dash'] as $key => $value){
                $dashboard[] = $key;
            }

            Snep_Dashboard_Manager::set($dashboard);
            $this->_redirect($this->getRequest()->getControllerName());
            
        }        
    }
   
}

?>
