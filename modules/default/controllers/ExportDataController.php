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
include ("includes/functions.php");

/**
 * Record Report Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2015 Opens Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class ExportDataController extends Zend_Controller_Action {

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
     * indexAction
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Reports"),
                    $this->view->translate("Export Report")));


        // Load tables
        $service = "CSV_GetParams";
        $url = Snep_Services::getPathService($service);
        $service_url = $url."&option=tables" ;
        $result = $this->callAPI($service_url) ;

        switch ($result['code']) {
            case 200:
                $tables = array() ;
                foreach(json_decode($result['data']) as $key => $value) {
                    $tables[$key] = $this->view->translate($value) ;
                }
                break;
            default:
                $erro = $this->view->translate("Error "). $result['code'];
                $erro .= $this->view->translate(". Please contact your system administrator");
                $this->view->error_message = $erro ;
                $this->renderScript('error/sneperror.phtml');
                break;
        }
        $this->view->tables = $tables;
        
        // Load Fields
        foreach ($tables as $table_key => $table_value) {
            $service_url = $url."&option=fields&table=".$table_key ;

            $result = $this->callAPI($service_url) ;

            switch ($result['code']) {
                case 200:
                    $$table_key = array() ;
                    $data = json_decode($result['data']) ;
                    foreach( $data as $key => $value) {
                        ${$table_key}[$key] = $this->view->translate($value) ;
                    }
                    break; 
                default:
                    $erro = $this->view->translate("Error "). $result['code'];
                    $erro .= $this->view->translate(". Please contact your system administrator");
                    $this->view->error_message = $erro ;
                    $this->renderScript('error/sneperror.phtml');
                    break;
            }
            $this->view->$table_key = $$table_key ;

        }   
            if ($this->_request->getPost()) {
            $this->exportAction();            
        }

    }

    /**
     * exportAction - Export contacts for CSV file.
     */
    public function exportAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Reports"),
                    $this->view->translate("Export Data Table")));

        $formData = $this->_request->getPost();
    
        if ($this->_request->getParam('download')) {
    
            $table = $_SESSION['exportData']['table'];

            $service = "CSV_ExportData";
            $url = Snep_Services::getPathService($service);
            $service_url = $url."&table=".$table."&fields=".$_SESSION['exportData']['coluns']."&order=".$_SESSION['exportData']['order'];
            $result = $this->callAPI($service_url) ;

            switch ($result['code']) {
                case 200:
                    $values = json_decode($result['data']) ; ;
                    break; 
                default:
                    $erro = $this->view->translate("Error "). $result['code'];
                    $erro .= $this->view->translate(". Please contact your system administrator");
                    $this->view->error_message = $erro ;
                    $this->renderScript('error/sneperror.phtml');
                    break;
            }
    


            // Varre array verificando se existe ; ou ,
            foreach($values as $key => $array){
                foreach($array as $colum => $value){
                    $res[$key][$colum] = str_replace(";", " ", $value);
                    $res[$key][$colum] = str_replace(",", " ", $value);
                }
            }

            $reportData['data'] = $res;
            $reportData['cols'] = explode(',', $_SESSION['exportData']['coluns']);

            if ($reportData) {
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender();
                
                $csv = new Snep_Csv();
                $csvData = $csv->generate($reportData['data'], $reportData['cols']);

                $dateNow = new Zend_Date();
                $fileName = $table . '_csv_' . $dateNow->toString("dd-MM-yyyy_hh'h'mm'm'") . '.csv';

                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                echo $csvData;

            } else {
                $this->view->error = $this->view->translate("No records found.");
                $this->renderScript('error/sneperror.phtml');
            }
        } else {
            // Selected columns
            $fields = "" ;
            foreach($formData['coluns'][$formData['group']] as $key => $value){
                $fields .= $key.",";
            }
            
            $ie = new Snep_CsvIE();
            $_SESSION['exportData']['table'] = $formData['group'];
            $_SESSION['exportData']['coluns'] = substr($fields, 0,-1);
            $_SESSION['exportData']['order'] = $formData['orderby'][$formData['group']];

            $this->view->form = $ie->exportResult();
            $this->view->title = "Export";
            $this->render('export');
        }

   }

    /**
    * Call API
    * @param <String> $service_url - url + parameters of API
    * @return <Array> - data and code
    */
    public function callAPI($service_url) {

        $http = curl_init($service_url);
        $status = curl_getinfo($http, CURLINFO_HTTP_CODE);
        
        curl_setopt($http, CURLOPT_RETURNTRANSFER,1);
        
        $http_response = curl_exec($http);
        $httpcode = curl_getinfo($http, CURLINFO_HTTP_CODE);
        
        curl_close($http);

        return array('data' => $http_response, 'code' => $httpcode);
    }
}
