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
 * Controller for extension management
 */
class ExtensionsController extends Zend_Controller_Action {

    protected $form;
    protected $boardData;

    public function preDispatch() {
        $all_writable = true;
        $files = array(
            "snep-sip.conf" => false,
            "snep-sip-trunks.conf" => false,
            "snep-iax2.conf" => false,
            "snep-iax2-trunks.conf" => false
        );

        $config = Zend_Registry::get('config');
        $asteriskDirectory = $config->system->path->asterisk->conf;

        foreach ($files as $file => $status) {
            $files[$file] = is_writable($asteriskDirectory . "/snep/" . $file);
            if ($files[$file] === false && $all_writable === true) {
                $all_writable = false;
            }
        }

        $this->view->all_writable = $all_writable;
        if (!$all_writable) {
            $this->view->writable_files = $files;
        }
    }

    public function indexAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extensions")
        ));
        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        $db = Zend_Registry::get('db');
        $select = $db->select()->from("peers", array(
            "id" => "id",
            "exten" => "name",
            "name" => "callerid",
            "channel" => "canal",
            "group"
        ));
        $select->where("peer_type='R'");

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

        $this->view->extensions = $paginator;
        $this->view->pages = $paginator->getPages();
        $this->view->PAGE_URL = "/snep/index.php/extensions/index/";

        $options = array("name" => $this->view->translate("Extension"),
            "callerid" => $this->view->translate("Name"),
            "group" => $this->view->translate("Group")
        );

        $baseUrl = $this->getFrontController()->getBaseUrl();

        // Formulário de filtro.
        $filter = new Snep_Form_Filter();
        $filter->setAction($baseUrl . '/extensions/index');
        $filter->setValue($this->_request->getPost('campo'));
        $filter->setFieldOptions($options);
        $filter->setFieldValue($this->_request->getParam('filtro'));
        $filter->setResetUrl("{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/index/page/$page");

        $this->view->form_filter = $filter;
        $this->view->filter = array(
            array("url" => "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getControllerName()}/export/",
                "display" => $this->view->translate("Export CSV"),
                "css" => "back"),
            array("url" => $baseUrl . "/extensions/multiadd",
                "display" => $this->view->translate("Add Multiple Extensions"),
                "css" => "includes"),
            array("url" => $baseUrl . "/extensions/add",
                "display" => $this->view->translate("Add Extension"),
                "css" => "include")
        );
    }

    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extensions"),
                    $this->view->translate("Add Extension")
        ));
        $this->view->form = $this->getForm();
        if (!$this->view->all_writable) {
            $this->view->form->getElement("submit")->setAttrib("disabled", "disabled");
        }
        $this->view->boardData = $this->boardData;

        if ($this->getRequest()->isPost()) {

            if (key_exists('virtual_error', $postData)) {
                $this->view->error = "There's no trunks registered on the system. Try a different technology";
                $this->view->form->valid(false);
            }


            if ($this->view->form->isValid($_POST)) {
                $postData = $this->_request->getParams();

                $ret = $this->execAdd($postData);

                if (!is_string($ret)) {

                    //log-user
                    if (class_exists("Loguser_Manager")) {

                        $id = $_POST["extension"]["exten"];
                        Snep_LogUser::salvaLog("Adicionou Ramal", $id, 5);
                        $add = Snep_Extensions_Manager::getPeer($id);
                        Snep_Extensions_Manager::insertLogRamal("ADD", $add);
                    }

                    $this->_redirect('/extensions/');
                } else {
                    $this->view->error = $ret;
                    $this->view->form->valid(false);
                }
            }
        }

        $this->renderScript("extensions/add_edit.phtml");
    }

    public function editAction() {
        $id = $this->_request->getParam("id");
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extensions"),
                    $this->view->translate("Edit %s", $id)
        ));

        Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
        $form = $this->getForm();
        if (!$this->view->all_writable) {
            $form->getElement("submit")->setAttrib("disabled", "disabled");
        }
        $this->view->form = $form;
        $this->view->boardData = $this->boardData;

        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();

        if ($this->getRequest()->isPost()) {

            if ($this->view->form->isValid($_POST)) {
                $postData = $this->_request->getParams();
                $postData["extension"]["exten"] = $this->_request->getParam("id");

                $ret = $this->execAdd($postData, true);

                if (!is_string($ret)) {
                    $this->_redirect('/extensions/');
                } else {
                    $this->view->error = $ret;
                    $this->view->form->valid(false);
                }
            }
        }

        $extenUtil = new Snep_Extensions();
        $exten = $extenUtil->ExtenDataAsArray($extenUtil->get($id));

        $name = $exten["name"];
        $nameField = $form->getSubForm('extension')->getElement('exten');
        $nameField->setValue($name);
        $nameField->setAttrib('readonly', true);
        $nameField->setAttrib('disabled', true);

        if (!$exten["canal"] || $exten["canal"] == 'INVALID' || substr($exten["canal"], 0, strpos($exten["canal"], '/')) == '') {
            $techType = 'manual';
        } else {
            $techType = strtolower(substr($exten["canal"], 0, strpos($exten["canal"], '/')));
        }
        $form->getSubForm('technology')->getElement('type')->setValue($techType);

        $password = $exten["password"];
        $form->getSubForm('extension')->getElement('password')->setValue($password);
        $form->getSubForm('extension')->getElement('password')->renderPassword = true;

        $callerid = $exten["callerid"];
        $form->getSubForm('extension')->getElement('name')->setValue($callerid);

        $extenGroup = $exten["group"];
        $form->getSubForm('extension')->getElement('exten_group')->setValue($extenGroup);

        $pickupGroup = $exten["pickupgroup"];
        $form->getSubForm('extension')->getElement('pickup_group')->setValue($pickupGroup);

        $voiceMail = $exten["usa_vc"];
        if ($voiceMail) {
            $form->getSubForm('advanced')->getElement('voicemail')->setAttrib('checked', 'checked');
        }

        $email = $exten["email"];
        $form->getSubForm('advanced')->getElement('email')->setValue($email);

        $padlock = $exten["authenticate"];
        if ($padlock) {
            $form->getSubForm('advanced')->getElement('padlock')->setAttrib('checked', 'checked');
        }

        $timeTotal = $exten["time_total"];
        if (!empty($timeTotal)) {
            $form->getSubForm('advanced')->getElement('minute_control')->setAttrib('checked', 'checked');
            $form->getSubForm('advanced')->getElement('timetotal')->setValue($timeTotal);
            $ctrlType = $exten["time_chargeby"];
            $form->getSubForm('advanced')->getElement('controltype')->setValue($ctrlType);
        } else {
            $form->getSubForm('advanced')->getElement('timetotal')->setAttrib('disabled', true);
            $form->getSubForm('advanced')->getElement('timetotal')->setAttrib('readonly', true);
            $form->getSubForm('advanced')->getElement('controltype')->setAttrib('disabled', true);
            $form->getSubForm('advanced')->getElement('controltype')->setAttrib('readonly', true);
        }

        switch ($techType) {
            case "sip":
                $pass = $exten["secret"];
                $simCalls = $exten["call-limit"];
                $nat = $exten["nat"];
                $qualify = $exten["qualify"];
                $typeIp = $exten["type"];
                $dtmfMode = $exten["dtmfmode"];
                $form->getSubForm('sip')->getElement('password')->setValue($pass);
                $form->getSubForm('sip')->getElement('password')->renderPassword = true;
                $form->getSubForm('sip')->getElement('calllimit')->setValue($simCalls);
                if ($nat == 'yes') {
                    $form->getSubForm('sip')->getElement('nat')->setAttrib('checked', 'checked');
                }
                if ($qualify == 'yes') {
                    $form->getSubForm('sip')->getElement('qualify')->setAttrib('checked', 'checked');
                }
                $form->getSubForm('sip')->getElement('type')->setValue($typeIp);
                $form->getSubForm('sip')->getElement('dtmf')->setValue($dtmfMode);

                $codecs = explode(";", $exten['allow']);
                $form->getSubForm('sip')->getElement('codec')->setValue($codecs[0]);
                $form->getSubForm('sip')->getElement('codec1')->setValue($codecs[1]);
                $form->getSubForm('sip')->getElement('codec2')->setValue($codecs[2]);
                break;

            case "iax2":
                $pass = $exten["secret"];
                $simCalls = $exten["call-limit"];
                $nat = $exten["nat"];
                $qualify = $exten["qualify"];
                $typeIp = $exten["type"];
                $dtmfMode = $exten["dtmfmode"];
                $form->getSubForm('iax2')->getElement('password')->setValue($pass);
                $form->getSubForm('iax2')->getElement('password')->renderPassword = true;
                $form->getSubForm('iax2')->getElement('calllimit')->setValue($simCalls);
                if ($nat == 'yes') {
                    $form->getSubForm('iax2')->getElement('nat')->setAttrib('checked', 'checked');
                }
                if ($qualify == 'yes') {
                    $form->getSubForm('iax2')->getElement('qualify')->setAttrib('checked', 'checked');
                }
                $form->getSubForm('iax2')->getElement('type')->setValue($typeIp);
                $form->getSubForm('iax2')->getElement('dtmf')->setValue($dtmfMode);

                $codecs = explode(";", $exten['allow']);
                $form->getSubForm('iax2')->getElement('codec')->setValue($codecs[0]);
                $form->getSubForm('iax2')->getElement('codec1')->setValue($codecs[1]);
                $form->getSubForm('iax2')->getElement('codec2')->setValue($codecs[2]);
                break;

            case "khomp":
                $khompInfo = substr($exten["canal"], strpos($exten["canal"], '/') + 1);
                $khompBoard = substr($khompInfo, strpos($khompInfo, 'b') + 1, strpos($khompInfo, 'c') - 1);
                $khompChannel = substr($khompInfo, strpos($khompInfo, 'c') + 1);

                $khompInfo = new PBX_Khomp_Info();

                if ($khompInfo->hasWorkingBoards()) {
                    foreach ($khompInfo->boardInfo() as $board) {
                        if (preg_match("/KFXS/", $board['model'])) {
                            $channels = range(0, $board['channels']);
                            $form->getSubForm('khomp')->getElement('board')->addMultiOption($board['id'], $board['id']);
                            $boardList[$board['id']] = $channels;

                            if ($board['id'] == $khompBoard) {
                                foreach ($channels as $value) {
                                    $form->getSubForm('khomp')->getElement('channel')->addMultiOption($value, $value);
                                }
                            }
                        }
                    }
                    $form->getSubForm('khomp')->getElement('board')->setValue($khompBoard);
                    $form->getSubForm('khomp')->getElement('channel')->setValue($khompChannel);
                }
                break;

            case "virtual":
                $virtualTrunk = substr($exten["canal"], strpos($exten["canal"], '/') + 1);
                $form->getSubForm('virtual')->getElement('virtual')->setValue($virtualTrunk);
                break;

            case "manual":
                $manualComp = substr($exten["canal"], strpos($exten["canal"], '/') + 1);
                $form->getSubForm('manual')->getElement('manual')->setValue($manualComp);
                break;
        }

        $this->renderScript("extensions/add_edit.phtml");
    }

    protected function execAdd($postData, $update = false) {
        $formData = $postData;

        $db = Zend_Registry::get('db');

        $exten = $formData["extension"]["exten"];
        $sqlValidName = "SELECT * from peers where name = '$exten'";
        $selectValidName = $db->query($sqlValidName);
        $resultGetId = $selectValidName->fetch();

        if ($resultGetId && !$update) {
            return $this->view->translate('Extension already taken. Please, choose another denomination.');
        } else if ($update) {
            $idExt = $resultGetId['id'];
        }

        $context = 'default';
        $extenPass = $formData["extension"]["password"];
        $extenName = $formData["extension"]["name"];
        $extenGroup = $formData["extension"]["exten_group"];
        $extenPickGrp = $formData["extension"]["pickup_group"] == '' ? "NULL" : $formData["extension"]["pickup_group"];
        $peerType = "R";

        $techType = $formData["technology"]["type"];
        $secret = $formData[$techType]["password"];
        $type = $formData[$techType]["type"];
        $dtmfmode = $formData[$techType]["dtmf"];
        $callLimit = $formData[$techType]["calllimit"];

        $nat = 'no';
        if ($techType == 'sip' || $techType == 'iax2') {
            if (key_exists('nat', $formData[$techType])) {
                $nat = 'yes';
            }
        }

        $qualify = 'no';
        if ($techType == 'sip' || $techType == 'iax2') {
            if (key_exists('qualify', $formData[$techType])) {
                $qualify = 'yes';
            }
        }

        $channel = strtoupper($techType);
        if ($channel == "KHOMP") {
            $khompBoard = $formData[$techType]['board'];
            $khompChannel = $formData[$techType]['channel'];
            if ($khompBoard == null || $khompBoard == '') {
                return $this->view->translate('Select a Khomp board from the list');
            }
            if ($khompChannel == null || $khompChannel == '') {
                return $this->view->translate('Select a Khomp channel from the list');
            }
            $channel .= "/b" . $khompBoard . 'c' . $khompChannel;
        } else if ($channel == "VIRTUAL") {
            $virtualInfo = $formData[$techType]['virtual'];
            $channel .= "/" . $virtualInfo;
        } else if ($channel == "MANUAL") {
            $manualManual = $formData[$techType]['manual'];
            $channel .= "/" . $manualManual;
        } else {
            $channel .= "/" . $exten;
        }

        $advVoiceMail = 'no';
        if (key_exists("voicemail", $formData["advanced"])) {
            $advVoiceMail = 'yes';
        } else {
            $advVoiceMail = 'no';
        }

        $advPadLock = '0';
        if (key_exists("padlock", $formData["advanced"])) {
            $advPadLock = '1';
        } else {
            $advPadLock = '0';
        }

        if (key_exists("minute_control", $formData["advanced"])) {
            $advMinCtrl = true;
            $advTimeTotal = $formData["advanced"]["timetotal"] * 60;
            $advTimeTotal = $advTimeTotal == 0 ? "NULL" : "'$advTimeTotal'";
            $advCtrlType = $advTimeTotal > 0 ? "{$formData['advanced']['controltype']}" : "NULL";
        } else {
            $advMinCtrl = false;
            $advTimeTotal = 'NULL';
            $advCtrlType = 'N';
        }

        $defFielsExten = array("accountcode" => "''", "amaflags" => "''", "defaultip" => "''", "host" => "'dynamic'", "insecure" => "''", "language" => "'pt_BR'", "deny" => "''", "permit" => "''", "mask" => "''", "port" => "''", "restrictcid" => "''", "rtptimeout" => "''", "rtpholdtimeout" => "''", "musiconhold" => "'cliente'", "regseconds" => 0, "ipaddr" => "''", "regexten" => "''", "cancallforward" => "'yes'", "setvar" => "''", "disallow" => "'all'", "canreinvite" => "'no'");

        $sqlFieldsExten = $sqlDefaultValues = "";
        foreach ($defFielsExten as $key => $value) {
            $sqlFieldsExten .= ",$key";
            $sqlDefaultValues .= ",$value";
        }

        $advEmail = $formData["advanced"]["email"];

        if ($techType == "sip" || $techType == "iax2") {
            $allow = sprintf("%s;%s;%s", $formData[$techType]['codec'], $formData[$techType]['codec1'], $formData[$techType]['codec2']);
        } else {
            $allow = "ulaw";
        }

        if ($update) {
            $sql = "UPDATE peers ";
            $sql.=" SET name='$exten',password='$extenPass' , callerid='$extenName', ";
            $sql.= "context='$context',mailbox='$exten',qualify='$qualify',";
            $sql.= "secret='$secret',type='$type', allow='$allow', fromuser='$exten',";
            $sql.= "username='$exten',fullcontact='',dtmfmode='$dtmfmode',";
            $sql.= "email='$advEmail', `call-limit`='$callLimit',";
            $sql.= "outgoinglimit='1', incominglimit='1',";
            $sql.= "usa_vc='$advVoiceMail',pickupgroup=$extenPickGrp,callgroup='$extenPickGrp',";
            $sql.= "nat='$nat',canal='$channel', authenticate=$advPadLock, ";
            $sql.= "`group`='$extenGroup', ";
            $sql.= "time_total=$advTimeTotal, time_chargeby='$advCtrlType'  WHERE id=$idExt";
        } else {
            $sql = "INSERT INTO peers (";
            $sql.= "name, password,callerid,context,mailbox,qualify,";
            $sql.= "secret,type,allow,fromuser,username,fullcontact,";
            $sql.= "dtmfmode,email,`call-limit`,incominglimit,";
            $sql.= "outgoinglimit, usa_vc, pickupgroup, canal,nat,peer_type, authenticate,";
            $sql.= "trunk, `group`, callgroup, time_total, ";
            $sql.= "time_chargeby " . $sqlFieldsExten;
            $sql.= ") values (";
            $sql.= "'$exten','$extenPass','$extenName','$context','$exten','$qualify',";
            $sql.= "'$secret','$type','$allow','$exten','$exten','$fullcontact',";
            $sql.= "'$dtmfmode','$advEmail','$callLimit','1',";
            $sql.= "'1', '$advVoiceMail', $extenPickGrp ,'$channel','$nat', '$peerType',";
            $sql.= "$advPadLock,'no','$extenGroup',";
            $sql.= "'$extenPickGrp', $advTimeTotal, '$advCtrlType' " . $sqlDefaultValues;
            $sql.= ")";
        }

        $stmt = $db->query($sql);

        $idExten = $db->lastInsertId();


        if ($advVoiceMail == 'yes') {
            if ($update) {
                $db->delete("voicemail_users", " mailbox='$exten' ");
            }
            $sql = "INSERT INTO voicemail_users ";
            $sql.= " (fullname, email, mailbox, password, customer_id, `delete`) VALUES ";
            $sql.= " ('$extenName', '$advEmail','$exten','$extenPass','$exten', 'yes')";
            $stmt = $db->prepare($sql);
            $stmt->execute();
        }

        Snep_InterfaceConf::loadConfFromDb();
    }

    public function deleteAction() {

        $id = $this->_request->getParam("id");

        //checks if the exten is used in the rule 
        $rules = Snep_Extensions_Manager::getValidation($id);
        $rulesQuery = Snep_Extensions_Manager::getValidationRules($id);

        $rules = array_merge($rules, $rulesQuery);

        if (count($rules) > 0) {
            $errMsg = $this->view->translate('The following routes use this extension, modify them prior to remove this extension') . ":<br />\n";
            foreach ($rules as $regra) {
                $errMsg .= $regra['id'] . " - " . $regra['desc'] . "<br />\n";
            }
            $this->view->error = $errMsg;
            $this->view->back = $this->view->translate("Back");
            $this->_helper->viewRenderer('error');
        } else {

            //log-user
            if (class_exists("Loguser_Manager")) {

                Snep_LogUser::salvaLog("Excluiu Ramal", $id, 5);
                $add = Snep_Extensions_Manager::getPeer($id);
                Snep_Extensions_Manager::insertLogRamal("DEL", $add);
            }

            Snep_Extensions_Manager::remove($id);
            Snep_Extensions_Manager::removeVoicemail($id);

            try {
                
            } catch (PDOException $e) {
                $db->rollBack();
                $this->view->error = $this->view->translate("DB Delete Error: ") . $e->getMessage();
                $this->view->back = $this->view->translate("Back");
                $this->_helper->viewRenderer('error');
            }

            $return = Snep_InterfaceConf::loadConfFromDb();

            If ($return != true) {
                $this->view->error = $return;
                $this->view->back = $this->view->translate("Back");
                $this->_helper->viewRenderer('error');
            }

            $this->_redirect("default/extensions");
        }
    }

    /**
     * @return Snep_Form
     */
    protected function getForm() {
        if ($this->form === Null) {
            Zend_Registry::set('cancel_url', $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/index');
            $form_xml = new Zend_Config_Xml(Zend_Registry::get("config")->system->path->base . "/default/forms/extensions.xml");
            $form = new Snep_Form();
            $form->addSubForm(new Snep_Form_SubForm($this->view->translate("Extension"), $form_xml->extension), "extension");
            $form->addSubForm(new Snep_Form_SubForm($this->view->translate("Interface Technology"), $form_xml->technology), "technology");
            $form->addSubForm(new Snep_Form_SubForm(null, $form_xml->ip, "sip"), "sip");
            $form->addSubForm(new Snep_Form_SubForm(null, $form_xml->ip, "iax2"), "iax2");
            $form->addSubForm(new Snep_Form_SubForm(null, $form_xml->manual, "manual"), "manual");
            $subFormVirtual = new Snep_Form_SubForm(null, $form_xml->virtual, "virtual");
            if (PBX_Trunks::getAll() == null) {
                $subFormVirtual->removeElement('virtual');
                $subFormVirtual->addElement(new Snep_Form_Element_Html("extensions/trunk_error.phtml", "err", false, null, "virtual_error"));
            }
            $form->addSubForm($subFormVirtual, "virtual");
            $subFormKhomp = new Snep_Form_SubForm(null, $form_xml->khomp, "khomp");
            $selectFill = $subFormKhomp->getElement('board');
            $selectFill->addMultiOption(null, ' ');
            // Monta informações para placas khomp
            $boardList = array();

            $khompInfo = new PBX_Khomp_Info();

            if ($khompInfo->hasWorkingBoards()) {
                foreach ($khompInfo->boardInfo() as $board) {
                    if (preg_match("/KFXS/", $board['model'])) {
                        $channels = range(0, $board['channels']);
                        $selectFill->addMultiOption($board['id'], $board['id']);
                        $boardList[$board['id']] = $channels;
                    }
                }
                $subFormKhomp->getElement('channel')->setRegisterInArrayValidator(false);
                $boardTmp = Zend_Json_Encoder::encode($boardList);
                $this->boardData = $boardTmp;
            } else {
                $subFormKhomp->removeElement('board');
                $subFormKhomp->removeElement('channel');
                $subFormKhomp->addElement(new Snep_Form_Element_Html("extensions/khomp_error.phtml", "err", false, null, "khomp"));
            }
            $form->addSubForm($subFormKhomp, "khomp");
            $form->addSubForm(new Snep_Form_SubForm($this->view->translate("Advanced"), $form_xml->advanced), "advanced");
            $this->form = $form;
        }

        return $this->form;
    }

    protected function getmultiaddForm() {
        if ($this->form === Null) {
            $form_xml = new Zend_Config_Xml(Zend_Registry::get("config")->system->path->base . "/default/forms/extensionsMulti.xml");
            $form = new Snep_Form();
            $form->addSubForm(new Snep_Form_SubForm($this->view->translate("Extension"), $form_xml->extension), "extension");
            $form->addSubForm(new Snep_Form_SubForm($this->view->translate("Interface Technology"), $form_xml->technology), "technology");
            $form->addSubForm(new Snep_Form_SubForm(null, $form_xml->ip, "sip"), "sip");
            $form->addSubForm(new Snep_Form_SubForm(null, $form_xml->ip, "iax2"), "iax2");
            //$form->addSubForm(new Snep_Form_SubForm(null, $form_xml->manual, "manual"), "manual");
            $form->addSubForm(new Snep_Form_SubForm(null, $form_xml->virtual, "virtual"), "virtual");
            $subFormKhomp = new Snep_Form_SubForm(null, $form_xml->khomp, "khomp");
            $selectFill = $subFormKhomp->getElement('board');
            $selectFill->addMultiOption(null, ' ');
            // Monta informações para placas khomp

            $boardList = array();

            $khompInfo = new PBX_Khomp_Info();

            if ($khompInfo->hasWorkingBoards()) {
                foreach ($khompInfo->boardInfo() as $board) {
                    if (preg_match("/KFXS/", $board['model'])) {
                        $channels = range(0, $board['channels']);
                        $selectFill->addMultiOption($board['id'], $board['id']);
                        $boardList[$board['id']] = $channels;
                    }
                }
                //$subFormKhomp->getElement('channel')->setRegisterInArrayValidator(false);
                $boardTmp = Zend_Json_Encoder::encode($boardList);
                $this->boardData = $boardTmp;
            } else {
                $subFormKhomp->removeElement('board');
                $subFormKhomp->removeElement('channel');
                $subFormKhomp->addElement(new Snep_Form_Element_Html("extensions/khomp_error.phtml", "err", false, null, "khomp"));
            }
            $form->addSubForm($subFormKhomp, "khomp");
            //$form->addSubForm(new Snep_Form_SubForm($this->view->translate("Advanced"), $form_xml->advanced), "advanced");
            $this->form = $form;
        }

        return $this->form;
    }

    /**
     * exportAction - Export CSV
     */
    public function exportAction() {
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extension"),
                    $this->view->translate("Export")
        ));

        $ie = new Snep_CsvIE('peers');
        if ($this->_request->getParam('download')) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $ie->export();
        } else {
            $this->view->form = $ie->exportResult();
            $this->view->title = "Export";
            $this->render('import-export');
        }
    }

    public function multiaddAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Manage"),
                    $this->view->translate("Extensions"),
                    $this->view->translate("Add Multiples Extensions")
        ));

        $this->view->form = $this->getmultiaddForm();
        if (!$this->view->all_writable) {
            $this->view->form->getElement("submit")->setAttrib("disabled", "disabled");
        }
        $this->view->boardData = $this->boardData;

        if ($this->getRequest()->isPost()) {
            $postData = $this->_request->getParams();

            if ($this->view->form->isValid($_POST)) {


                $range = explode(";", $postData["extension"]["exten"]);

                $this->view->error = "";

                $khomp_iface = false;
                if (strtoupper($postData["technology"]["type"]) == 'KHOMP') {
                    $khompInfo = new PBX_Khomp_Info();
                    $khompChannels = array();
                    $khomp_iface = true;
                    $boardInfo = $khompInfo->boardInfo($postData["khomp"]["board"]);
                    for ($i = 0; $i < $boardInfo['channels']; $i++) {
                        $khompChannels[$i] = $i; //"KHOMP/b{$boardInfo['id']}c$i";
                    }
                }

                //log-user
                if (class_exists("Loguser_Manager")) {

                    Snep_LogUser::salvaLog("Adicionou Ramais multiplos", $_POST["extension"]["exten"], 5);
                    $tech = $_POST["technology"]["type"];
                    $codecs = $_POST[$tech]["codec"] . ";" . $_POST[$tech]["codec1"] . ";" . $_POST[$tech]["codec2"] . ";" . $_POST[$tech]["codec3"];
                    $add = array();
                    $add["name"] = $_POST["extension"]["exten"];
                    $add["canal"] = $tech;
                    $add["allow"] = $codecs;
                    $add["dtmfmode"] = $_POST[$tech]["dtmf"];
                    $add["directmedia"] = $_POST[$tech]["directmedia"];
                    Snep_Extensions_Manager::insertLogRamal("ADD R", $add);
                }

                foreach ($range as $exten) {

                    if ($this->view->error)
                        break;

                    if (is_numeric($exten)) {

                        $postData["extension"]["exten"] = $exten;
                        $postData["extension"]["password"] = $exten . $exten;
                        $postData["extension"]["name"] = 'Ramal' . $exten . '<' . $exten . '>';
                        $postData["sip"]["password"] = $exten;
                        $postData["iax"]["password"] = $exten;


                        $ret = $this->execAdd($postData);

                        if (is_string($ret)) {
                            $this->view->error = $exten . " - " . $ret;
                            $this->view->form->valid(false);
                            break;
                        }
                    } else {

                        $exten = explode(";", $exten);

                        foreach ($exten as $range) {

                            $rangeToAdd = explode('-', $range);


                            if (is_numeric($rangeToAdd[0]) && is_numeric($rangeToAdd[1])) {
                                $i = $rangeToAdd[0];
                                while ($i <= $rangeToAdd[1]) {

                                    $postData["id"] = $i;
                                    $postData["extension"]["exten"] = $i;
                                    $postData["extension"]["password"] = $i . $i;
                                    $postData["extension"]["name"] = 'Ramal ' . $i . '<' . $i . '>';
                                    $postData["sip"]["password"] = $i . $i;
                                    $postData["iax2"]["password"] = $i . $i;

                                    if ($khomp_iface && count($khompChannels) > 0) {
                                        $channel = array_shift($khompChannels);
                                        $postData["extension"]["khomp_channel"] = $channel;
                                    }
                                    $ret = $this->execAdd($postData);
                                    $i++;

                                    if (is_string($ret)) {
                                        $this->view->error = $i . " - " . $ret;
                                        $this->view->form->valid(false);
                                        break;
                                    }
                                }
                            }
                            if ($this->view->error)
                                break;
                        }
                    }
                }
                if (!$this->view->error) {
                    $this->_redirect('/extensions/');
                }
            }
        }
    }

}
