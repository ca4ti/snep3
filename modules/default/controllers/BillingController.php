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
 * Billing Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 * @author    Rafael Pereira Bozzetti
 */
class BillingController extends Zend_Controller_Action {

    /**
     * List all Billing
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Carrier"),
                    $this->view->translate("Billing")
        ));

        $this->view->url = $this->getFrontController()->getBaseUrl() . "/" . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from("tarifas_valores", array('DATE_FORMAT(data,\'%d/%m/%Y %T\') as data', 'vcel', 'vfix'))
                ->from("tarifas")
                ->from("operadoras", array('nome'))
                ->where("operadoras.codigo = tarifas.operadora")
                ->where("tarifas_valores.codigo = tarifas.codigo");

        if ($this->_request->getPost('filtro')) {
            $field = mysql_escape_string($this->_request->getPost('campo'));
            $query = mysql_escape_string($this->_request->getPost('filtro'));
            $select->where("`$field` like '%$query%'");
        }

        $page = $this->_request->getParam('page');
        $this->view->page = ( isset($page) && is_numeric($page) ? $page : 1 );
        $this->view->filtro = $this->_request->getParam('filtro');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);
        $paginator->setCurrentPageNumber($this->view->page);
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->ambiente->linelimit);

        $this->view->billing = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->PAGE_URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/";

        $opcoes = array("nome" => $this->view->translate("Carrier"),
            "pais" => $this->view->translate("Country"),
            "estado" => $this->view->translate("State"),
            "cidade" => $this->view->translate("City"),
            "prefixo" => $this->view->translate("Prefix"),
            "ddd" => $this->view->translate("City Code"));

        $filter = new Snep_Form_Filter();
        $filter->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($opcoes);
        $filter->setFieldValue($this->_request->getPost('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add/",
                "display" => $this->view->translate("Incluir Tarifa"),
                "css" => "include"));
    }

    /**
     *  Add Queue
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Carrier"),
                    $this->view->translate("Add")
        ));

        $form = new Snep_Form(new Zend_Config_Xml("default/forms/queues.xml"));
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/add');


        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        foreach (Snep_Carrier_Manager::getAll() as $_carrier) {
            $carriers[$_carrier['codigo']] = $_carrier['nome'];
        }
        $this->view->carriers = $carriers;

        $states['--'] = '--';
        foreach (Snep_Billing_Manager::getStates() as $state) {
            $states[$state['cod']] = $state['name'];
        }
        $this->view->states = $states;

        $cities['--'] = '--';
        foreach (Snep_Billing_Manager::getCity('AC') as $city) {
            $cities[$city['name']] = $city['name'];
        }
        $this->view->cities = $cities;
        $dados = $this->_request->getParams();

        if ($this->_request->getPost()) {

            $form_isValid = true;

            $this->view->error = array();

            if (!preg_match('/[0-9]+$/', $dados['ddd']) || $dados['ddd'] == "") {
                $form_isValid = false;
                $this->view->error['ddd'] = $this->view->translate("City Code not numeric");
            }

            if (!preg_match('/[0-9]+$/', $dados['ddi']) || $dados['ddi'] == "") {
                $form_isValid = false;
                $this->view->error['ddi'] = $this->view->translate("Country Code not numeric");
            }

            if (!preg_match('/[0-9]+$/', $dados['prefixo']) || $dados['prefixo'] == "") {
                $form_isValid = true;
                $this->view->error['prefixo'] = $this->view->translate("Prefix not numeric");
            }
            if ($dados['operadora'] == "") {
                $form_isValid = false;
                $this->view->error['operadora'] = $this->view->translate("Carrier not selected ");
            }

            if ($form_isValid) {

                if ($_POST['ddd'] == "") {
                    $_POST['ddd'] = 0;
                }
                if ($_POST['ddi'] == "") {
                    $_POST['ddi'] = 0;
                }

                $billing = Snep_Billing_Manager::getPrefix($_POST);

                if ($billing) {
                    $form_isValid = false;
                    $this->view->message = $this->view->translate("This bill is already set");
                }
            }

            if ($form_isValid) {

                $xdados = array('data' => $_POST['data'],
                    'carrier' => $_POST['operadora'],
                    'country_code' => $_POST['ddi'],
                    'country' => $_POST['pais'],
                    'city_code' => $_POST['ddd'],
                    'city' => $_POST['cidade'],
                    'state' => $_POST['estado'],
                    'prefix' => $_POST['prefixo'],
                    'tbf' => $_POST['vfix'],
                    'tbc' => $_POST['vcel']);

                Snep_Billing_Manager::add($xdados);

                //log-user
                if (class_exists("Loguser_Manager")) {

                    $id = Snep_Billing_Manager::getId($xdados["city"]);
                    $add = Snep_Billing_Manager::getTarifaLog($id['codigo']);
                    $lastId = Snep_Billing_Manager::getLastId();
                    $add["codigo"] = $lastId;
                    Snep_Billing_Manager::insertLogTarifa("ADD", $add);
                    Snep_LogUser::salvaLog("Adicionou tarifa", $lastId, 4);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }

            $this->view->dados = ( isset($dados) ? $dados : null);
        }
    }

    /**
     * Edit Billing
     */
    public function editAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Carrier"),
                    $this->view->translate("Edit")
        ));

        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');

        $id = $this->_request->getParam("id");

        $_carriers = Snep_Carrier_Manager::getAll();
        foreach ($_carriers as $_carrier) {
            $carriers[$_carrier['codigo']] = $_carrier['nome'];
        }
        $this->view->carriers = $carriers;

        $this->view->Carrier = Snep_Billing_Manager::get($id);

        $this->view->billingValues = Snep_Billing_Manager::getBillingValues($id);

        $_estado = Snep_Billing_Manager::getStates();
        foreach ($_estado as $estado) {
            if ($estado['cod'] == $this->view->Carrier['estado']) {
                $this->view->billingState = $estado;
            }
        }

        if ($this->_request->getPost()) {

            if (isset($_POST['action'])) {
                foreach ($_POST['action'] as $ida => $num) {
                    if ($num < count($this->view->billingValues)) {

                        $values = array('data' => $_POST['data'][$num],
                            'vcel' => $_POST['vcel'][$num],
                            'vfix' => $_POST['vfix'][$num]);

                        //log-user      
                        if (class_exists("Loguser_Manager")) {

                            $old = Snep_Billing_Manager::getTarifaLog($id);
                            $old['codigo'] = $id;
                            Snep_Billing_Manager::insertLogTarifa("OLD", $old);
                        }

                        Snep_Billing_Manager::editBilling($id, $values);
                        
                        if (class_exists("Loguser_Manager")) {

                            $new = Snep_Billing_Manager::getTarifaLog($id);
                            $new['codigo'] = $id;
                            Snep_Billing_Manager::insertLogTarifa("NEW E", $new);
                            Snep_LogUser::salvaLog("Editou tarifa", $id, 4);
                        }
                    } else {

                        $values = array('data' => $_POST['data'][$num],
                            'vcel' => $_POST['vcel'][$num],
                            'vfix' => $_POST['vfix'][$num]);

                        Snep_Billing_Manager::addBilling($id, $values);

                        
                        if (class_exists("Loguser_Manager")) {

                            $new = Snep_Billing_Manager::getTarifaLog($id);
                            $new['codigo'] = $id;
                            Snep_Billing_Manager::insertLogTarifa("NEW N", $new);
                            Snep_LogUser::salvaLog("Adicionou nova tarifa", $id, 4);
                        }
                    }
                }
            }

            $this->_redirect($this->getRequest()->getControllerName());
        }
    }

    /**
     * Remove a Billing
     */
    public function removeAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Billing"),
                    $this->view->translate("Delete")
        ));

        $id = $this->_request->getParam('id');

        //log-user
        if (class_exists("Loguser_Manager")) {

            Snep_LogUser::salvaLog("Excluiu tarifa", $id, 4);
            $del = Snep_Billing_Manager::getTarifaLog($id);
            $del["codigo"] = $id;
            Snep_Billing_Manager::insertLogTarifa("DEL", $del);
        }

        Snep_Billing_Manager::remove($id);

        $this->_redirect($this->getRequest()->getControllerName());
    }

    /**
     * Get cities from state
     * POST Array state
     */
    public function dataAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $data = $_POST;

        if (isset($data['state'])) {
            $_states = Snep_Billing_Manager::getCity($data['state']);

            $states = array();
            foreach ($_states as $state) {
                $states[] = $state['name'];
            }
        }

        echo Zend_Json::encode($states);
    }

    /**
     * METODOS PALEATIVOS para adaptação da interface.
     */
    public function cidadeAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $estado = isset($_POST['uf']) && $_POST['uf'] != "" ? $_POST['uf'] : display_error($LANG['msg_nostate'], true);
        $municipios = Snep_Cnl::get($estado);

        $options = '';
        if (count($municipios > 0)) {
            foreach ($municipios as $cidades) {
                $options .= "<option  value='{$cidades['municipio']}' > {$cidades['municipio']} </option> ";
            }
        } else {
            $options = "<option> {$LANG['select']} </option>";
        }

        echo $options;
    }

}