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
 * Queues Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 * @author    Rafael Pereira Bozzetti
 */
class QueuesController extends Zend_Controller_Action {

    /**
     * indexAction - List all Queues
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Queues")));

        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from("queues");

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

        $this->view->queues = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->PAGE_URL = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/";

        $opcoes = array("name" => $this->view->translate("Name"),
            "musiconhold" => $this->view->translate("Audio Class"),
            "strategy" => $this->view->translate("Strategy"),
            "servicelevel" => $this->view->translate("SLA"),
            "timeout" => $this->view->translate("Timeout"));

        $filter = new Snep_Form_Filter();
        $filter->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($opcoes);
        $filter->setFieldValue($this->_request->getPost('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/export/",
                "display" => $this->view->translate("Export CSV"),
                "css" => "back"),
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/add/",
                "display" => $this->view->translate("Add Queue"),
                "css" => "include"));
    }

    /**
     *  AddAction - Add Queue
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Queues"),
                    $this->view->translate("Add Queues")));

        $sections = new Zend_Config_Ini('/etc/asterisk/snep/snep-musiconhold.conf');
        $_section = array_keys($sections->toArray());
        $section = array();
        foreach ($_section as $value) {
            $section[$value] = $value;
        }

        $files = '/var/lib/asterisk/sounds/';
        if (file_exists($files)) {

            $files = scandir($files);
            $sounds = array("" => "");

            foreach ($files as $i => $value) {
                if (substr($value, 0, 1) == '.') {
                    unset($files[$i]);
                    continue;
                }
                if (is_dir($files . '/' . $value)) {
                    unset($files[$i]);
                    continue;
                }
                $sounds[$value] = $value;
            }
        }

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form();
        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        $essentialData = new Zend_Config_Xml('./modules/default/forms/queues.xml', 'essential', true);
        $essential = new Snep_Form_SubForm($this->view->translate("General Configuration"), $essentialData);

        $essential->getElement('musiconhold')->setMultiOptions($section);
        $essential->getElement('timeout')->setValue(0);
        $essential->getElement('announce_frequency')->setValue(0);
        $essential->getElement('retry')->setValue(0);
        $essential->getElement('wrapuptime')->setValue(0);
        $essential->getElement('servicelevel')->setValue(0);
        $essential->getElement('strategy')->setMultiOptions(array('ringall' => $this->view->translate('For all agents available (ringall)'),
            'roundrobin' => $this->view->translate('Search for a available agent (roundrobin)'),
            'leastrecent' => $this->view->translate('For the agent idle for the most time (leastrecent)'),
            'random' => $this->view->translate('Randomly (random)'),
            'fewestcalls' => $this->view->translate('For the agent that answerd less calls (fewestcalls)'),
            'rrmemory' => $this->view->translate('Equally (rrmemory)')));

        $form->addSubForm($essential, "essential");

        $advancedData = new Zend_Config_Xml('./modules/default/forms/queues.xml', 'advanced', true);
        $advanced = new Snep_Form_SubForm($this->view->translate("Advanced Configuration"), $advancedData);

        $boolOptions = array(1 => $this->view->translate('Yes'),
            0 => $this->view->translate('No'));

        $advanced->getElement('announce')->setMultiOptions($sounds);
        $advanced->getElement('queue_youarenext')->setMultiOptions($sounds);
        $advanced->getElement('queue_thereare')->setMultiOptions($sounds);
        $advanced->getElement('queue_callswaiting')->setMultiOptions($sounds);
        $advanced->getElement('queue_thankyou')->setMultiOptions($sounds);
        $advanced->getElement('leavewhenempty')->setMultiOptions($boolOptions)->setValue(0);
        $advanced->getElement('reportholdtime')->setMultiOptions($boolOptions)->setValue(0);
        $advanced->getElement('memberdelay')->setValue(0);
        $advanced->getElement('joinempty')
                ->setMultiOptions(array('yes' => $this->view->translate('Yes'),
                    'no' => $this->view->translate('No'),
                    'strict' => $this->view->translate('Restrict')))
                ->setValue('no');
        /*
          $autofill  = $advanced->getElement('autofill');
          $autofill->setLabel($this->view->translate("Distribuir chamadas simultaneamente na fila até que não existam mais agentes disponíveis ou chamadas na fila") )
          ->setMultiOptions( $boolOptions )
          ->setValue('no');

          $autopause  = $advanced->getElement('autopause');
          $autopause->setLabel($this->view->translate("Pausar automaticamente um agente quando ele não atender uma chamada") )
          ->setMultiOptions( $boolOptions )
          ->setValue('no');
         */
        $form->addSubForm($advanced, "advanced");

        if ($this->_request->getPost()) {

            $dados = array('name' => $_POST['essential']['name'],
                'musiconhold' => $_POST['essential']['musiconhold'],
                'announce' => $_POST['advanced']['announce'],
                'context' => $_POST['advanced']['context'],
                'timeout' => $_POST['essential']['timeout'],
                'queue_youarenext' => $_POST['advanced']['queue_youarenext'],
                'queue_thereare' => $_POST['advanced']['queue_thereare'],
                'queue_callswaiting' => $_POST['advanced']['queue_callswaiting'],
                'queue_thankyou' => $_POST['advanced']['queue_thankyou'],
                'announce_frequency' => $_POST['essential']['announce_frequency'],
                'retry' => $_POST['essential']['retry'],
                'wrapuptime' => $_POST['essential']['wrapuptime'],
                'maxlen' => $_POST['essential']['maxlen'],
                'servicelevel' => $_POST['essential']['servicelevel'],
                'strategy' => $_POST['essential']['strategy'],
                'joinempty' => $_POST['advanced']['joinempty'],
                'leavewhenempty' => $_POST['advanced']['leavewhenempty'],
                'reportholdtime' => $_POST['advanced']['reportholdtime'],
                'memberdelay' => $_POST['advanced']['memberdelay'],
                'weight' => $_POST['advanced']['weight'],
                    /*
                      'autofill'          => $_POST['advanced']['autofill'],
                      'autopause'         => $_POST['advanced']['autopause']
                     */
            );

            $form_isValid = $form->isValid($_POST);
            if ($form_isValid) {

                Snep_Queues_Manager::add($dados);

                //log-user
                if (class_exists("Loguser_Manager")) {

                    $id = $dados["name"];
                    Snep_LogUser::salvaLog("Adicionou Fila", $id, 7);
                    $add = Snep_Queues_Manager::get($id);
                    Snep_Queues_Manager::insertLogQueue("ADD", $add);
                }

                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
        $this->view->form = $form;
    }

    /**
     * editAction - Edit Queues
     */
    public function editAction() {

        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $id = $this->_request->getParam("id");

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Queues"),
                    $this->view->translate("Edit $id")));

        $queue = Snep_Queues_Manager::get($id);

        $sections = new Zend_Config_Ini('/etc/asterisk/snep/snep-musiconhold.conf');
        $_section = array_keys($sections->toArray());
        $section = array();
        foreach ($_section as $value) {
            $section[$value] = $value;
        }

        $files = '/var/lib/asterisk/sounds/';
        if (file_exists($files)) {

            $files = scandir($files);
            $sounds = array("" => "");

            foreach ($files as $i => $value) {
                if (substr($value, 0, 1) == '.') {
                    unset($files[$i]);
                    continue;
                }
                if (is_dir($files . '/' . $value)) {
                    unset($files[$i]);
                    continue;
                }
                $sounds[$value] = $value;
            }
        }

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form();
        $form->setAction($this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/edit/id/' . $id);

        $essentialData = new Zend_Config_Xml('./modules/default/forms/queues.xml', 'essential', true);
        $essential = new Snep_Form_SubForm($this->view->translate("General Configuration"), $essentialData);

        $essential->getElement('name')->setValue($queue['name'])->setAttrib('readonly', true);
        $essential->getElement('musiconhold')->setMultiOptions($section)->setValue($queue['musiconhold']);
        $essential->getElement('timeout')->setValue($queue['timeout']);
        $essential->getElement('announce_frequency')->setValue($queue['announce_frequency']);
        $essential->getElement('retry')->setValue($queue['retry']);
        $essential->getElement('wrapuptime')->setValue($queue['wrapuptime']);
        $essential->getElement('maxlen')->setValue($queue['maxlen']);
        $essential->getElement('servicelevel')->setValue($queue['servicelevel']);
        $essential->getElement('strategy')
                ->addMultiOptions(array('ringall' => $this->view->translate('For all agents available (ringall)'),
                    'roundrobin' => $this->view->translate('Search for a available agent (roundrobin)'),
                    'leastrecent' => $this->view->translate('For the agent idle for the most time (leastrecent)'),
                    'random' => $this->view->translate('Randomly (random)'),
                    'fewestcalls' => $this->view->translate('For the agent that answerd less calls (fewestcalls)'),
                    'rrmemory' => $this->view->translate('Equally (rrmemory)')))
                ->setValue($queue['strategy']);


        $form->addSubForm($essential, "essential");

        $advancedData = new Zend_Config_Xml('./modules/default/forms/queues.xml', 'advanced', true);
        $advanced = new Snep_Form_SubForm($this->view->translate("Advanced Configuration"), $advancedData);

        $boolOptions = array(1 => $this->view->translate('Yes'),
            0 => $this->view->translate('No'));

        $advanced->getElement('announce')->setMultiOptions($sounds)->setValue($queue['announce']);
        $advanced->getElement('context')->setValue($queue['context']);
        $advanced->getElement('queue_youarenext')->setMultiOptions($sounds)->setValue($queue['queue_youarenext']);
        $advanced->getElement('queue_thereare')->setMultiOptions($sounds)->setValue($queue['queue_thereare']);
        $advanced->getElement('queue_callswaiting')->setMultiOptions($sounds)->setValue($queue['queue_callswaiting']);
        $advanced->getElement('queue_thankyou')->setMultiOptions($sounds)->setValue($queue['queue_thankyou']);
        $advanced->getElement('joinempty')
                ->setMultiOptions(array('yes' => $this->view->translate('Yes'),
                    'no' => $this->view->translate('No'),
                    'strict' => $this->view->translate('Restrict')))
                ->setValue($queue['joinempty']);
        $advanced->getElement('leavewhenempty')->setMultiOptions($boolOptions)->setValue($queue['leavewhenempty']);
        $advanced->getElement('reportholdtime')->setMultiOptions($boolOptions)->setValue($queue['reportholdtime']);
        $advanced->getElement('memberdelay')->setValue($queue['memberdelay']);
        $advanced->getElement('weight')->setValue($queue['weight']);
        /*
          $autofill  = $advanced->getElement('autofill');
          $autofill->setLabel($this->view->translate("Distribuir chamadas simultaneamente na fila até que não existam mais agentes disponíveis ou chamadas na fila") )
          ->setMultiOptions( $boolOptions )
          ->setValue('no');

          $autopause  = $advanced->getElement('autopause');
          $autopause->setLabel($this->view->translate("Pausar automaticamente um agente quando ele não atender uma chamada") )
          ->setMultiOptions( $boolOptions )
          ->setValue('no');
         */

        $form->addSubForm($advanced, "advanced");

        if ($this->_request->getPost()) {

            $dados = array('name' => $_POST['essential']['name'],
                'musiconhold' => $_POST['essential']['musiconhold'],
                'announce' => $_POST['advanced']['announce'],
                'context' => $_POST['advanced']['context'],
                'timeout' => $_POST['essential']['timeout'],
                'queue_youarenext' => $_POST['advanced']['queue_youarenext'],
                'queue_thereare' => $_POST['advanced']['queue_thereare'],
                'queue_callswaiting' => $_POST['advanced']['queue_callswaiting'],
                'queue_thankyou' => $_POST['advanced']['queue_thankyou'],
                'announce_frequency' => $_POST['essential']['announce_frequency'],
                'retry' => $_POST['essential']['retry'],
                'wrapuptime' => $_POST['essential']['wrapuptime'],
                'maxlen' => $_POST['essential']['maxlen'],
                'servicelevel' => $_POST['essential']['servicelevel'],
                'strategy' => $_POST['essential']['strategy'],
                'joinempty' => $_POST['advanced']['joinempty'],
                'leavewhenempty' => $_POST['advanced']['leavewhenempty'],
                'reportholdtime' => $_POST['advanced']['reportholdtime'],
                'memberdelay' => $_POST['advanced']['memberdelay'],
                'weight' => $_POST['advanced']['weight'],
                    /*
                      'autofill'          => $_POST['advanced']['autofill'],
                      'autopause'         => $_POST['advanced']['autopause']
                     */
            );

            $form_isValid = $form->isValid($_POST);
            if ($form_isValid) {

                Snep_Queues_Manager::edit($dados);
                $this->_redirect($this->getRequest()->getControllerName());
            }
        }
        $this->view->form = $form;
    }

    /**
     * removeAction - Remove a queue
     */
    public function removeAction() {

        $id = $this->_request->getParam('id');

        // check if the queues is used in the rule or have members 
        $regras = Snep_Queues_Manager::getValidation($id);
        $exten_members = Snep_Queues_Manager::getValidationPeers($id);
        $agent_members = Snep_Queues_Manager::getValidationAgent($id);
        $info = Snep_Queues_Manager::get($id);
        $error = false;

        if (count($exten_members) > 0 || count($agent_members) > 0) {
            $msg = $this->view->translate("The following members make use of this queue, remove before deleting:") . "<br />\n";

            if (count($exten_members) > 0) {
                $error = true;
                $valida = 1;

                foreach ($exten_members as $membros) {
                    $member = explode("/", $membros['membername']);
                    $member = $member[1];
                    $msg .= $this->view->translate("Extension:") . $member . "<br/>\n";
                }
            }

            if (count($agent_members) > 0) {
                $error = true;
                $valida = 1;
                foreach ($agent_members as $member_agent) {
                    $msg .= $this->view->translate("Agent:") . $member_agent['agent_id'] . "<br/>\n";
                }
            }

            $this->view->error = $msg . "<br />";
            $this->_helper->viewRenderer('error');
        }

        if (count($regras) > 0) {
            $error = true;
            $this->view->error = $this->view->translate("Cannot remove. The following routes are using this queues: ") . "<br />";
            foreach ($regras as $regra) {

                $this->view->error .= $regra['id'] . " - " . $regra['desc'] . "<br />\n";
            }
        }
        if ($error) {
            $this->_helper->viewRenderer('error');
        } else {
            //log-user
            if (class_exists("Loguser_Manager")) {
                $add = Snep_Queues_Manager::get($id);
            }

            Snep_Queues_Manager::deleteMembersGroup($info['id']);
            Snep_Queues_Manager::removeQueuePeers($id);
            Snep_Queues_Manager::remove($id);
            Snep_Queues_Manager::removeQueues($id);

            if (class_exists("Loguser_Manager")) {

                Snep_LogUser::salvaLog("Excluiu Fila", $id, 7);
                Snep_Queues_Manager::insertLogQueue("DEL", $add);
            }

            $this->_redirect($this->getRequest()->getControllerName());
        }
    }

    /**
     * membersAction - Set member queue 
     */
    public function membersAction() {

        $queue = $this->_request->getParam("id");

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Queues"),
                    $this->view->translate("Members $queue")));

        $members = Snep_Queues_Manager::getMembers($queue);
        $mem = array();
        foreach ($members as $m) {
            $mem[$m['interface']] = $m['interface'];
        }

        $_allMembers = Snep_Queues_Manager::getAllMembers();
        $notMem = array();
        foreach ($_allMembers as $row) {
            $cd = explode(";", $row['canal']);
            foreach ($cd as $canal) {
                if (strlen($canal) > 0) {
                    if (!array_key_exists($canal, $mem)) {
                        $notMem[$canal] = $row['callerid'] . " ($canal)({$row['group']})";
                    }
                }
            }
        }

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = new Snep_Form();

        $this->view->objSelectBox = 'members';
        $form->setSelectBox($this->view->objSelectBox, $this->view->translate("Add Member"), $notMem, $mem);

        $queueId = new Zend_Form_Element_hidden('id');
        $queueId->setvalue($queue);
        $form->addElement($queueId);

        $this->view->form = $form;

        if ($this->_request->getPost()) {

            // remove members 
            if (isset($_POST['box'])) {
                foreach ($_POST['box'] as $id => $del) {

                    Snep_Queues_Manager::removeMember($del);
                }
            }


            if (isset($_POST['box_add'])) {
                Snep_Queues_Manager::removeAllMembers($queue);
                foreach ($_POST['box_add'] as $add) {
                    Snep_Queues_Manager::insertMember($queue, $add);
                }
            }

            $this->_redirect($this->getRequest()->getControllerName() . '/');
        }
    }

    /**
     * cidadeAction - Depracated method
     * PALEATIVOS para adaptação da interface.     *
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

    /**
     * exportAction - Export cost center for CSV file.
     */
    public function exportAction() {

        $queues = Snep_Queues_Manager::getCsv();

        foreach ($queues as $key => $queue) {
            $member = "";
            $members = Snep_Queues_Manager::getMembers($queue['name']);

            foreach ($members as $item) {
                $member .= $item["membername"] . ",";
            }
            $queues[$key]['members'] = $member;
        }

        $headers = array('name' => $this->view->translate('Name'),
            'musiconhold' => $this->view->translate('Music on hold class'),
            'context' => $this->view->translate('Go to Context'),
            'servicelevel' => $this->view->translate('SLA'),
            'members' => $this->view->translate('Members'));

        $csv = new Snep_Csv();
        $csv_data = $csv->generate($queues, $headers);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $dateNow = new Zend_Date();
        $fileName = $this->view->translate('Queues_csv_') . $dateNow->toString($this->view->translate(" dd-MM-yyyy_hh'h'mm'm' ")) . '.csv';

        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo $csv_data;
    }

}
