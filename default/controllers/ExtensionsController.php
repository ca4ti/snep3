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
            if($files[$file] === false && $all_writable === true) {
                $all_writable = false;
            }
        }

        $this->view->all_writable = $all_writable;
        if(!$all_writable) {
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
            "id"      => "id",
            "exten"   => "name",
            "name"    => "callerid",
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
            /*array("url" => $baseUrl . "/extensions/multiadd",
                "display" => $this->view->translate("Add Multiple Extensions"),
                "css" => "includes"),*/
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
        if(!$this->view->all_writable) {
            $this->view->form->getElement("submit")->setAttrib("disabled", "disabled");
        }
        $this->view->boardData = $this->boardData;

        if ($this->getRequest()->isPost()) {
            
             if (key_exists('virtual_error', $postData)){
                    $this->view->error = "There's no trunks registered on the system. Try a different technology";
                    $this->view->form->valid(false);
                    }
                    

            if ($this->view->form->isValid($_POST)) {
                $postData = $this->_request->getParams();
                
                $ret = $this->execAdd($postData);

                if (!is_string($ret)) {
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
        if(!$this->view->all_writable) {
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

        if($techType == "sip" || $techType == "iax2") {
            $allow = sprintf("%s;%s;%s", $formData[$techType]['codec'], $formData[$techType]['codec1'], $formData[$techType]['codec2']);
        }
        else {
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
        $db = Zend_Registry::get('db');

        $id = $this->_request->getParam("id");

        // Fazendo procura por referencia a esse ramal em regras de negócio.
        $rulesQuery = "SELECT id, `desc` FROM regras_negocio WHERE origem LIKE '%R:$id%' OR destino LIKE '%R:$id%'";
        $rules = $db->query($rulesQuery)->fetchAll();

        $rulesQuery = "SELECT rule.id, rule.desc FROM regras_negocio as rule, regras_negocio_actions_config as rconf WHERE (rconf.regra_id = rule.id AND rconf.value = '$id')";
        $rules = array_merge($rules, $db->query($rulesQuery)->fetchAll());

        if (count($rules) > 0) {
            $errMsg = $this->view->translate('The following routes use this extension, modify them prior to remove this extension') . ":<br />\n";
            foreach ($rules as $regra) {
                $errMsg .= $regra['id'] . " - " . $regra['desc'] . "<br />\n";
            }
            $this->view->error = $errMsg;
            $this->view->back = $this->view->translate("Voltar");
            $this->_helper->viewRenderer('error');
        }
        $sql = "DELETE FROM peers WHERE name='" . $id . "'";

        $db->beginTransaction();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $sql = "delete from voicemail_users where customer_id='$id'";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        try {
            $db->commit();
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
            if(PBX_Trunks::getAll() == null){
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

    public function multiaddAction() {

        $this->view->breadcrumb = $this->view->translate("Manage » Extensions » Add Multiple Extension");

        $this->view->form = $this->getmultiaddForm();
        if(!$this->view->all_writable) {
            $this->view->form->getElement("submit")->setAttrib("disabled", "disabled");
        }
        $this->view->boardData = $this->boardData;

        if ($this->getRequest()->isPost()) {

            if ($this->view->form->isValid($_POST)) {

                $postData = $this->_request->getParams();

                $range = explode(";", $postData["extension"]["exten"]);

                $dataForm = array();
                $dataForm["controller"] = $postData["controller"];
                $dataForm["action"] = $postData["action"];
                $dataForm["module"] = $postData["module"];
                $dataForm["extension"]["exten_group"] = $postData["extension"]["exten_group"];
                $dataForm["extension"]["pickup_group"] = $postData["extension"]["pickup_group"];
                $dataForm["technology"]["type"] = $postData["technology"]["type"];
                $dataForm["sip"]["calllimit"] = "";
                $dataForm["sip"]["nat"] = "no";
                $dataForm["sip"]["qualify"] = "no";
                $dataForm["sip"]["type"] = "peer";
                $dataForm["sip"]["dtmf"] = $postData["sip"]["dtmf"];
                $dataForm["sip"]["codec"] = $postData["sip"]["codec"];
                $dataForm["sip"]["codec1"] = $postData["sip"]["codec1"];
                $dataForm["sip"]["codec2"] = $postData["sip"]["codec2"];
                $dataForm["iax2"]["calllimit"] = "";
                $dataForm["iax2"]["nat"] = "no";
                $dataForm["iax2"]["qualify"] = "no";
                $dataForm["iax2"]["type"] = "peer";
                $dataForm["iax2"]["dtmf"] = $postData["iax2"]["dtmf"];
                $dataForm["iax2"]["codec"] = $postData["iax2"]["codec"];
                $dataForm["iax2"]["codec1"] = $postData["iax2"]["codec1"];
                $dataForm["iax2"]["codec2"] = $postData["iax2"]["codec2"];

                if ( isset($postData["manual"]["manual"]) ) {

                    $dataForm["manual"]["manual"] = $postData["manual"]["manual"];
                } else {

                    $dataForm["manual"]["manual"] = "1";
                }

                $dataForm["virtual"]["virtual"] = $postData["virtual"]["virtual"];

                $dataForm["khomp"]["board"] = $postData["khomp"]["board"];

                if ( isset($postData["advanced"]["voicemail"]) ) {

                    $dataForm["advanced"]["voicemail"] = $postData["advanced"]["voicemail"];
                } else {

                    $dataForm["advanced"]["voicemail"] = "";
                }

                if ( isset($postData["advanced"]["email"]) ) {

                    $dataForm["advanced"]["email"] = $postData["advanced"]["email"];
                } else {

                    $dataForm["advanced"]["email"] = "";
                }

                foreach ($range as $exten) {

                    if ( is_numeric($exten)) {

                        $dataForm["extension"]["exten"] = $exten;
                        $dataForm["extension"]["password"] = $exten.$exten;
                        $dataForm["extension"]["name"] = 'Ramal'.$exten.'<'.$exten.'>';
                        $dataForm["sip"]["password"] = $exten;
                        $dataForm["iax"]["password"] = $exten;
                        
                        $ret = $this->execAdd($dataForm);

                        if (!is_string($ret)) {
                            $this->_redirect('/extensions/');
                        } else {
                            $this->view->error = $ret;
                            $this->view->form->valid(false);
                        }

                    } else {

                        $exten = explode("-",$exten);

                        foreach (range($exten[0], $exten[1]) as $exten) {

                            $dataForm["id"] = $exten;
                            $dataForm["extension"]["exten"] = $exten;
                            $dataForm["extension"]["password"] = $exten.$exten;
                            $dataForm["extension"]["name"] = 'Ramal'.$exten.'<'.$exten.'>';
                            $dataForm["sip"]["password"] = $exten.$exten;
                            $dataForm["iax2"]["password"] = $exten.$exten;

                            $ret = $this->execAdd($dataForm);

                            if (!is_string($ret)) {
                                $this->_redirect('/extensions/');
                            } else {
                                $this->view->error = $ret;
                                $this->view->form->valid(false);
                            }
                        }
                    }
                }
            }
        }
    }
}
