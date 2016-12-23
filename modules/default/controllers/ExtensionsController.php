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
require_once "includes/AsteriskInfo.php";

/**
 * Extension Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class ExtensionsController extends Zend_Controller_Action {

    /**
     *
     * @var Zend_Form
     */
    protected $boardData;

    /**
     * preDispatch
     */
    public function preDispatch() {

        // Test Asterisk connection
        try {
            $astinfo = new AsteriskInfo();
            // Read Khomp links
            try {
                $data = $astinfo->status_asterisk("khomp links show concise", "", True) ;
            } catch (Exception $e) {
                $this->view->error_message = $this->view->translate("Socket connection to the server is not available at the moment.");
                $this->renderScript('error/sneperror.phtml');;
            }
        } catch (Exception $e) {
            $this->view->error_message =  $this->view->translate("Error! Failed to connect to server Asterisk.");
            $this->renderScript('error/sneperror.phtml');
        }

    }


    /**
     * Initial settings of the class
     */
     public function init() {

        $this->view->url = $this->getFrontController()->getBaseUrl() . '/' . $this->getRequest()->getControllerName();
        $this->view->lineNumber = Zend_Registry::get('config')->ambiente->linelimit;
        $this->view->peers_digits =  Zend_Registry::get('config')->canais->peers_digits;

        $this->extenGroups = Snep_ExtensionsGroups_Manager::getAll();

        $this->pickupGroups = Snep_PickupGroups_Manager::getAll();

        $this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->key = Snep_Dashboard_Manager::getKey(
            Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
            Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
            Zend_Controller_Front::getInstance()->getRequest()->getActionName());
    }


    /**
     * indexAction - List extensions
     */

    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions")));

        $extensions = Snep_Extensions_Manager::getAll();

        if(empty($extensions)){
            $this->view->error_message = $this->view->translate("You do not have registered extensions. <br><br> Click 'Add Extensions' ou 'Multi Add Extensions' to make the first registration");
        }

        $this->view->extensions = $extensions;

    }

    /**
     * addAction - Add extensions
     * @return type
     * @throws ErrorException
     */
    public function addAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions"),
                    $this->view->translate("Add")));

        $this->view->pickupGroups = $this->pickupGroups;
        $this->view->extenGroups = $this->extenGroups;
        // Set ExtensionGroup  "Default"
        $this->view->extenInGroup = array('1' => "");

        // Mont codec's list and sets the default codec for each option
        $codecsDefault = array("alaw","ilbc","g729","gsm","h264","h263","h263p","ulaw","all");
        $codec1 = $codec2 = $codec3 = "";
        foreach($codecsDefault as $key => $value){
            $codec1 .= '<option value="'.$value.'"'.($value==="alaw" ? " selected " : "").'>'.$value.'</option>\n';
            $codec2 .= '<option value="'.$value.'"'.($value==="ulaw" ? " selected " : "").'>'.$value.'</option>\n';
            $codec3 .= '<option value="'.$value.'"'.($value==="gsm"  ? " selected " : "").'>'.$value.'</option>\n';
        } // END foreach
        $this->view->codec1 = $codec1;
        $this->view->codec2 = $codec2;
        $this->view->codec3 = $codec3;

        // Mount trunks list
        $this->view->trunks = Snep_Trunks_Manager::getData();

        // Khomp boards
        $boardList = array();
        $khompInfo = new PBX_Khomp_Info();

        if ($khompInfo->hasWorkingBoards()) {
            foreach ($khompInfo->boardInfo() as $board) {
                if (preg_match("/FXS/", $board['model'])) {
                    $channels = range(0, $board['channels']);
                    foreach($channels as $key => $chan){
                        $boardList['b'.$board['id'].'c'.$chan] =  $board['model'] . ' - b' .$board['id'].'c'.$chan;
                    }
                }
            }
        }
        $this->view->boardData = $boardList;


        //Define the action and load form
        $this->view->action = "add" ;
        $this->view->techType = 'sip';
        $this->view->directmedianonat = "checked";
        $this->view->typeFriend = "checked";
        $this->view->dtmfrf = "checked";
        $this->view->nat_no = 'checked' ;
        $extension = array("name" => "",
            "callerid" => "",
            "secret" => "",
            "call-limit" => "",
            "email" => "",
            "password" => "",
            "usa_vc" => "",
            "cancallforward" => "",
            "authenticate" => 0);
        $extension['qualify'] = 'yes';
        $this->view->extension = $extension;

        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );

        // After POST
        if ($this->getRequest()->isPost()) {

            $data = $this->_request->getParams();

            if (key_exists('virtual_error', $data)) {
                $this->view->error_message = "There's no trunks registered on the system. Try a different technology";
                $this->renderScript('error/sneperror.phtml');
            }

            $ret = $this->execAdd($data);

            if (!is_string($ret)) {
                $this->_redirect('/extensions/');
            } else {
                $message = $ret;
                $this->_helper->redirector('sneperror','error',null,array('error_message'=>$message));
            }

        }
    }

    /**
     * editAction - Edit extensions
     * @return type
     * @throws ErrorException
     */
    public function editAction() {

        $id = $this->_request->getParam("id");
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions"),
                    $this->view->translate("Edit")));

        // Load data about exten
        $exten = Snep_Extensions_Manager::getPeer($id);

        $nameValue = explode("<", $exten['callerid']);
        if(count($nameValue) > 1){
            $exten['callerid'] = $nameValue[0];
        };

        $this->view->extension = $exten ;

        // Groups
        $this->view->pickupGroups = $this->pickupGroups;
        $this->view->extenGroups = $this->extenGroups;

        $extenInGroup = array();
        foreach(Snep_ExtensionsGroups_Manager::getGroupsExtensions($exten['id']) as $key => $value){
            $extenInGroup[$value['group_id']] = "";
        }
        $this->view->extenInGroup = $extenInGroup;

        // Tech Type
        if (!$exten["canal"] || $exten["canal"] == 'INVALID' || substr($exten["canal"], 0, strpos($exten["canal"], '/')) == '') {
            $techType = 'manual';
        } else {
            $techType = strtolower(substr($exten["canal"], 0, strpos($exten["canal"], '/')));
        }

        $this->view->sip = "";
        $this->view->iax2 = "";
        $this->view->manual = "";
        $this->view->virtual = "";
        $this->view->khomp = "";
        $this->view->techType   = $techType; //"selected";
        $this->view->$techType = "selected";
        $this->view->technology = $techType;

        $timeTotal = $exten["time_total"];
        if (!empty($timeTotal)) {

            $this->view->timetotal = $timeTotal / 60;
            $this->view->controltype = $exten["time_chargeby"];

            $this->view->Y = "";
            $this->view->M = "";
            $this->view->D = "";
            $this->view->$exten["time_chargeby"] = "checked";
        }

        switch ($techType) {

            case "sip":

                $this->view->directmediayes = "";
                $this->view->directmedianonat = "";
                $this->view->directmediaupdate = "";
                $this->view->directmediaoutgoing = "";
                switch ($exten['directmedia']) {
                    case "yes":
                        $this->view->directmediayes = "checked";
                        break;
                    case "nonat":
                        $this->view->directmedianonat = "checked";
                        break;
                    case "no":
                        $this->view->directmedianonat = "checked";
                        break;
                    case "outgoing":
                        $this->view->directmediaoutgoing = "checked";
                        break;
                    case "update":
                        $this->view->directmediaupdate = "checked";
                        break;
                }

                $this->view->typePeer = "";
                $this->view->typeFriend = "";
                $this->view->typeUser = "";
                switch ($exten['type']) {
                    case "peer" :
                        $this->view->typePeer = "checked";
                        break ;
                    case "friend" :
                        $this->view->typeFriend = "checked";
                        break ;
                    case "user" :
                        $this->view->typeUser = "checked";
                        break ;
                }

                $this->view->dtmfrf = "";
                $this->view->dtmfinband = "";
                $this->view->dtmfinfo = "";
                if($exten['dtmfmode'] == "rfc2833"){
                    $this->view->dtmfrf = "checked";
                }elseif($exten['dtmfmode'] == "inband"){
                    $this->view->dtmfinband = "checked";
                }else{
                    $this->view->dtmfinfo = "checked";
                }

                $array_nat = explode(",",$exten['nat']);
                foreach($array_nat as $key => $val) {
                    $label = "nat_".$val;
                    $this->view->$label = "checked";
                }


                $codecsDefault = array("ulaw","alaw","ilbc","g729","gsm","h264","h263","h263p","all");
                $codecs = explode(";", $exten['allow']);

                $codec1 = "";
                $codec2 = "";
                $codec3 = "";
                foreach($codecsDefault as $key => $value){

                    $codec1 .= ($value == $codecs[0]) ? '<option value="'.$value.'" selected>'.$value.'</option>\n' : '<option value="'.$value.'">'.$value.'</option>\n';
                    $codec2 .= ($value == $codecs[1]) ? '<option value="'.$value.'" selected>'.$value.'</option>\n' : '<option value="'.$value.'">'.$value.'</option>\n';
                    $codec3 .= ($value == $codecs[2]) ? '<option value="'.$value.'" selected>'.$value.'</option>\n' : '<option value="'.$value.'">'.$value.'</option>\n';

                }

                $this->view->codec1 = $codec1;
                $this->view->codec2 = $codec2;
                $this->view->codec3 = $codec3;

                break;

            case "iax2":

                $this->view->directmediayes = "";
                $this->view->directmediano = "";
                if($exten['directmedia'] == "yes"){
                    $this->view->directmediayes = "checked";
                }else{
                    $this->view->directmediano = "checked";
                }

                $this->view->typePeer = "";
                $this->view->typeFriend = "";
                if($exten['type'] == "peer"){
                    $this->view->typePeer = "checked";
                }else{
                    $this->view->typeFriend = "checked";
                }

                $this->view->dtmfrf = "";
                $this->view->dtmfinband = "";
                $this->view->dtmfinfo = "";
                if($exten['dtmfmode'] == "rfc2833"){
                    $this->view->dtmfrf = "checked";
                }elseif($exten['dtmfmode'] == "inband"){
                    $this->view->dtmfinband = "checked";
                }else{
                    $this->view->dtmfinfo = "checked";
                }

                $codecsDefault = array("ulaw","alaw","ilbc","g729","gsm","h264","h263","h263p","all");
                $codecs = explode(";", $exten['allow']);

                $codec1 = "";
                $codec2 = "";
                $codec3 = "";
                foreach($codecsDefault as $key => $value){

                    $codec1 .= ($value == $codecs[0]) ? '<option value="'.$value.'" selected>'.$value.'</option>\n' : '<option value="'.$value.'">'.$value.'</option>\n';
                    $codec2 .= ($value == $codecs[1]) ? '<option value="'.$value.'" selected>'.$value.'</option>\n' : '<option value="'.$value.'">'.$value.'</option>\n';
                    $codec3 .= ($value == $codecs[2]) ? '<option value="'.$value.'" selected>'.$value.'</option>\n' : '<option value="'.$value.'">'.$value.'</option>\n';

                }

                $this->view->codec1 = $codec1;
                $this->view->codec2 = $codec2;
                $this->view->codec3 = $codec3;

                break;

            case "khomp":

                $khompInfo = substr($exten["canal"], strpos($exten["canal"], '/') + 1);
                $khompBoard = substr($khompInfo, strpos($khompInfo, 'b') + 1, strpos($khompInfo, 'c') - 1);
                $khompChannel = substr($khompInfo, strpos($khompInfo, 'c') + 1);

                $boardList = array();

                $khompInfo = new PBX_Khomp_Info();

                if ($khompInfo->hasWorkingBoards()) {
                    foreach ($khompInfo->boardInfo() as $board) {

                        if (preg_match("/FXS/", $board['model'])) {

                            $channels = range(0, $board['channels']);

                            foreach($channels as $key => $chan){

                                $boardList['b'.$board['id'].'c'.$chan] =  $board['model'] . ' - b' .$board['id'].'c'.$chan;
                            }
                        }
                    }
                }

                $this->view->boardData = $boardList;
                $this->view->khompChecked = 'b'.$khompBoard.'c'.$khompChannel;

                break;

            case "virtual":
                $virtualTrunk = substr($exten["canal"], strpos($exten["canal"], '/') + 1);

                $trunks =  Snep_Trunks_Manager::getData();
                $this->view->trunks = $trunks;
                $this->view->trunkChecked = $virtualTrunk;

                break;

            case "manual":
                $manualComp = substr($exten["canal"], strpos($exten["canal"], '/') + 1);
                $this->view->manual = $manualComp;
                break;
        }

        //Define the action and load form
        $this->view->disabled = 'disabled';
        $this->view->action = "edit" ;

        $this->renderScript( $this->getRequest()->getControllerName().'/addedit.phtml' );

        // After POST
        if ($this->getRequest()->isPost()) {

            $postData = $this->_request->getParams();

            $postData["exten"] = $this->_request->getParam("id");

            // increment in callerid "Name <exten>"
            $nameValue = explode("<", $postData['name']);
            if(count($nameValue) <= 1){
                $postData['name'] = $postData['name'];
            }else{
                $postData['name'] = $nameValue[0]."<".$postData['exten'].">";
            };


            $ret = $this->execAdd($postData, true);

            if (!is_string($ret)) {
                $this->_redirect('/extensions/');
            } else {
                $this->view->error_message = $ret;
                $this->renderScript('error/sneperror.phtml');;
            }

        }

    }

    /**
     * execAdd
     * @param <array> $postData
     * @param <boolean> $update
     * @return type
     */
    protected function execAdd($formData, $update = false) {

        $db = Zend_Registry::get('db');
        $exten = $formData["exten"];
        $sqlValidName = "SELECT * from peers where name = '$exten'";
        $selectValidName = $db->query($sqlValidName);
        $resultGetId = $selectValidName->fetch();

        if ($resultGetId && !$update) {
            return $this->view->translate('Extension already taken. Please, choose another denomination.');
        } else if ($update) {
            $idExten = $resultGetId['id'];
        }

        $context = 'default';
        $extenPass = $formData["passwordpadlock"];
        $extenName = $formData["name"];
        $extenGroup = $formData["exten_group"];
        $pickup_group = Snep_PickupGroups_Manager::getName($formData["pickup_group"]);

        $extenPickGrp = $formData["pickup_group"] == '' ? "NULL" : $pickup_group["cod_grupo"];
        $peerType = "R";

        $techType = $formData["technology"];

        $secret = (isset($formData["password"]))? $formData["password"]: "";


        $dtmfmode = (isset($formData["dtmf"]))? $formData["dtmf"]: "";
        $directmedia = $formData["directmedia"];
        $callLimit = $formData["calllimit"];

        if ($techType == 'sip' || $techType == 'iax2') {
            $nat_types = array('no','comedia','force_rport','auto_comedia','auto_force_rport');
            $nat = "" ;
            foreach ($nat_types as $key => $val) {
                if (isset($formData['nat_'.$val])) {
                    if ($nat === "") {
                        $nat = $val ;
                    } else {
                        $nat .= ','.$val ;
                    }
                }
            }
            if ($nat === "") {
                $nat = 'no';
            }
        }

        $qualify = 'no';
        if ($techType == 'sip' || $techType == 'iax2') {
            if (key_exists('qualify', $formData)) {
                $qualify = 'yes';
            }
        }

        // Type: friend, user, peer
        $type = $formData['type'];

        $channel = strtoupper($techType);

        if ($channel == "KHOMP") {

            $board = explode('c', $formData['channel']);

            $khompBoard = substr($board[0], 1);
            $khompChannel = $board[1];

            if ($khompBoard == null || $khompBoard == '') {
                return $this->view->translate('Select a Khomp board from the list');
            }
            if ($khompChannel == null || $khompChannel == '') {
                return $this->view->translate('Select a Khomp channel from the list');
            }
            $channel .= "/b" . $khompBoard . 'c' . $khompChannel;
        } else if ($channel == "VIRTUAL") {
            $channel .= "/" . (int)$formData["board"];
        } else if ($channel == "MANUAL") {
            $manual = $formData['manual'];
            $channel .= "/" . $manual;
        } else {
            $channel .= "/" . $exten;
        }

        $advVoiceMail = 'no';
        if (key_exists("voicemail", $formData)) {
            $advVoiceMail = 'yes';
        }

        if (key_exists("authenticate", $formData)) {
            $advPadLock = 1;
        } else {
            $advPadLock = 0;
        }

        $advCancallforward = 'no';
        if ($formData["cancallforward"]) {
            $advCancallforward = 'yes';
        } else {
            $advCancallforward = 'no';
        }

        //if (key_exists("minute_control", $formData["advanced"])) {
        if ($formData["minute_control"]) {
            $advMinCtrl = true;
            $advTimeTotal = $formData["timetotal"] * 60;
            $advTimeTotal = $advTimeTotal == 0 ? "NULL" : "'$advTimeTotal'";
            $advCtrlType = $formData['controltype'];
        } else {
            $advMinCtrl = false;
            $advTimeTotal = 'NULL';
            $advCtrlType = 'N';
        }

        $defFielsExten = array("accountcode" => "''", "amaflags" => "''", "defaultip" => "''", "host" => "'dynamic'", "insecure" => "''", "language" => "'pt_BR'", "deny" => "''", "permit" => "''", "mask" => "''", "port" => "''", "restrictcid" => "''", "rtptimeout" => "''", "rtpholdtimeout" => "''", "musiconhold" => "'cliente'", "regseconds" => 0, "ipaddr" => "''", "regexten" => "''", "setvar" => "''", "disallow" => "'all'");

        $sqlFieldsExten = $sqlDefaultValues = "";
        foreach ($defFielsExten as $key => $value) {
            $sqlFieldsExten .= ",$key";
            $sqlDefaultValues .= ",$value";
        }

        $advEmail = $formData["email"];

        if ($techType == "sip" || $techType == "iax2") {
            $allow = sprintf("%s;%s;%s", $formData['codec'], $formData['codec1'], $formData['codec2']);
        } else {
            $allow = "ulaw";
        }

        if ($update) {
            $sql = "UPDATE peers ";
            $sql.=" SET name='$exten',password='$extenPass' , callerid='$extenName', ";
            $sql.= "context='$context',mailbox='$exten',qualify='$qualify',";
            $sql.= "secret='$secret',type='$type', allow='$allow', ";
            $sql.= "defaultuser='$exten',fullcontact='',dtmfmode='$dtmfmode',";
            $sql.= "email='$advEmail', `call-limit`='$callLimit',";
            $sql.= "outgoinglimit='1', incominglimit='1',";
            $sql.= "usa_vc='$advVoiceMail',pickupgroup=$extenPickGrp,callgroup='$extenPickGrp',";
            $sql.= "nat='$nat',canal='$channel', authenticate=$advPadLock, ";
            $sql.= "`directmedia`='$directmedia',";
            $sql.= "time_total=$advTimeTotal, time_chargeby='$advCtrlType', cancallforward='$advCancallforward'";
            $sql.= "  WHERE id=$idExten";
        } else {
            $sql = "INSERT INTO peers (";
            $sql.= "name, password,callerid,context,mailbox,qualify,";
            $sql.= "secret,type,allow,defaultuser,fullcontact,";
            $sql.= "dtmfmode,email,`call-limit`,incominglimit,";
            $sql.= "outgoinglimit, usa_vc, pickupgroup, canal,nat,peer_type, authenticate,";
            $sql.= "trunk, callgroup, time_total, cancallforward, directmedia, ";
            $sql.= "time_chargeby " . $sqlFieldsExten;
            $sql.= ") values (";
            $sql.= "'$exten','$extenPass','$extenName','$context','$exten','$qualify',";
            $sql.= "'$secret','$type','$allow','$exten','$fullcontact',";
            $sql.= "'$dtmfmode','$advEmail','$callLimit','1',";
            $sql.= "'1', '$advVoiceMail', $extenPickGrp ,'$channel','$nat', '$peerType',$advPadLock,";
            $sql.= "'no','$extenPickGrp', $advTimeTotal, '$advCancallforward', '$directmedia', ";
            $sql.= "'$advCtrlType' " . $sqlDefaultValues;
            $sql.= ")";
        }

        $stmt = $db->query($sql);
        if (! $update) {
           $idExten = $db->lastInsertId();
        }

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
        if (isset($extenGroup)) {
            $extensions_group = Snep_ExtensionsGroups_Manager::getGroupsExtensions($idExten);
        } else {
            $extensions_group = array();
        }

        // Update table core_peer_groups
        Snep_ExtensionsGroups_Manager::updateGroupsExtension($idExten,$extensions_group,$extenGroup) ;
        Snep_InterfaceConf::loadConfFromDb();
    }

    /**
     * removeAction - Remove exetension
     * @return type
     * @throws ErrorException
     */
    public function removeAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions"),
                    $this->view->translate("Delete")));

        $exten = $this->_request->getParam("id");

        //checks if the exten is used in the rule
        $rules = Snep_Extensions_Manager::getValidation($exten);
        $rulesQuery = Snep_Extensions_Manager::getValidationRules($exten);
        $rules = array_merge($rules, $rulesQuery);

        if (count($rules) > 0) {
            $errMsg = $this->view->translate('The following routes use this extension, modify them prior to remove this extension') . ":<br />\n";
            foreach ($rules as $regra) {
                $errMsg .= $regra['id'] . " - " . $regra['desc'] . "<br />\n";
            }
            $this->view->error_message = $errMsg;
            $this->view->back = $this->view->translate("Back");
            $this->renderScript('error/sneperror.phtml');

        } else {

            $this->view->id = $exten;
            $this->view->remove_title = $this->view->translate('Delete Extension.');
            $this->view->remove_message = $this->view->translate('The extension will be deleted. After that, you have no way get it back.');
            $this->view->remove_form = 'extensions';
            $this->renderScript('remove/remove.phtml');

            if ($this->_request->getPost()) {

                $exten = $_POST['id'];
                $db = Zend_Registry::get('db');
                $sql = "SELECT id from peers where name = '$exten'";
                $stmt = $db->query($sql);
                $result = $stmt->fetch();
                $idExten = $result['id'];

                try {
                    Snep_Binds_Manager::removeBondByPeer($exten);
                    Snep_Extensions_Manager::remove($exten);
                    Snep_Extensions_Manager::removeVoicemail($exten);
                    Snep_ExtensionsGroups_Manager::deleteExtensionGroups($idExten);
                    Snep_InterfaceConf::loadConfFromDb();

                } catch (PDOException $e) {
                    $db->rollBack();
                    $this->view->error_message = $this->view->translate("DB Delete Error: ") . $e->getMessage();
                    $this->view->back = $this->view->translate("Back");
                    $this->renderScript('error/sneperror.phtml');;
                }

                $this->_redirect("default/extensions");
            }
        }
    }


    /**
     * multiremoveAction - Delete Extensions
     */
    public function multiremoveAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions"),
                    $this->view->translate("Delete Multiples")));

        if ($this->getRequest()->isPost()) {

            $data = $this->_request->getParams();
            $range = array() ;
            // Mount extensions list
            if (isset($data['exten'])) {
                $range = explode(";", $data["exten"]);
                $data = $data["exten"];
            }

            foreach ($range as $exten) {
                if (is_numeric($exten)) {
                    $extensions[$exten]="" ;
                }else{
                    $exten = explode(";", $exten);
                    foreach ($exten as $extension) {
                        $rangeToAdd = explode('-', $extension);

                        if (is_numeric($rangeToAdd[0]) && is_numeric($rangeToAdd[1])) {
                            $start = (int) $rangeToAdd[0];
                            $end = (int) $rangeToAdd[1];
                            while ($start <= $end) {
                                $extensions[$start] = "";
                                $start++;
                            }
                        }
                    }
                }
            }
            // checks if the exten is used in the rule
            $rules = array();
            foreach ($extensions as $key => $value) {

                $_rules = Snep_Extensions_Manager::getValidation($key);
                $rulesQuery = Snep_Extensions_Manager::getValidationRules($key);
                if (count($_rules) > 0 || count($rulesQuery) > 0 ) {
                    $rules[$key] = array_merge($_rules, $rulesQuery);
                }
            }

            if (count($rules) > 0) {
                $errMsg = $this->view->translate('The following extensions are in use in routes, modify them prior to remove this extension') . ":<br />\n";
                foreach ($rules as $ext => $regra) {
                    foreach ($regra as $k => $v) {
                        $errMsg .= $this->view->translate('Extension')." : ".$key." - ";
                        $errMsg .= $this->view->translate('Rule')." : ". $v['id'] . " - " . $v['desc'] . "<br />\n";
                    }
                }
                $this->view->error_message = $errMsg;
                $this->view->back = $this->view->translate("Back");
                $this->renderScript('error/sneperror.phtml');
            } else {

                foreach ($extensions as $key => $value) {
                    $exten = $key;
                    $db = Zend_Registry::get('db');
                    $sql = "SELECT id from peers where name = '$exten'";
                    $stmt = $db->query($sql);
                    $result = $stmt->fetch();
                    $idExten = $result['id'];

                    try {

                        Snep_Extensions_Manager::remove($exten);
                        Snep_Extensions_Manager::removeVoicemail($exten);
                        Snep_ExtensionsGroups_Manager::deleteExtensionGroups($idExten);

                    } catch (PDOException $e) {
                        $db->rollBack();
                        $this->view->error_message = $this->view->translate("DB Delete Error: ") . $e->getMessage();
                        $this->view->back = $this->view->translate("Back");
                        $this->renderScript('error/sneperror.phtml');;
                    }

                }
                $this->_redirect("default/extensions");
            }

        }
    }


    /**
     * multiaddAction - Add multi extensions
     * @return type
     * @throws ErrorException
     */
    public function multiaddAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Extensions"),
                    $this->view->translate("Add Multiples Extensions")));

        $this->view->pickupGroups = $this->pickupGroups;
        $this->view->extenGroups = $this->extenGroups;
        // Set ExtensionGroup  "Default"
        $this->view->extenInGroup = array('1' => "");

        $this->view->boardData = $this->boardData;

        // Monta SELECT de codecs e define o default para cada opcao
        $codecsDefault = array("alaw","ilbc","g729","gsm","h264","h263","h263p","ulaw","all");
        $codec1 = $codec2 = $codec3 = "";
        foreach($codecsDefault as $key => $value){
            $codec1 .= '<option value="'.$value.'"'.($value==="alaw" ? " selected " : "").'>'.$value.'</option>\n';
            $codec2 .= '<option value="'.$value.'"'.($value==="ulaw" ? " selected " : "").'>'.$value.'</option>\n';
            $codec3 .= '<option value="'.$value.'"'.($value==="gsm"  ? " selected " : "").'>'.$value.'</option>\n';
        }  // END foreach
        $this->view->codec1 = $codec1;
        $this->view->codec2 = $codec2;
        $this->view->codec3 = $codec3;

        $this->view->trunks = Snep_Trunks_Manager::getData();

        if ($this->getRequest()->isPost()) {

            $data = $this->_request->getParams();

            $range = explode(";", $data["exten"]);
            $this->view->error = "";

            foreach ($range as $exten) {

                if ($this->view->error)
                    break;

                if (is_numeric($exten)) {

                    $data["exten"] = $exten;
                    $data["password"] = $exten . $exten;
                    $data["name"] = $this->view->translate("Extension ") ." ".$exten . " <" . $exten.">" ;
                    $data["sip"]["password"] = $exten;
                    $data["iax"]["password"] = $exten;
                    $data['type'] = 'friend' ;

                    $ret = $this->execAdd($data);

                    if (is_string($ret)) {
                        $this->view->error .= $exten . " - " . $ret;
                        break;
                    }
                } else {

                    $exten = explode(";", $exten);

                    foreach ($exten as $extension) {
                        $rangeToAdd = explode('-', $extension);

                        if (is_numeric($rangeToAdd[0]) && is_numeric($rangeToAdd[1])) {
                            $i = $rangeToAdd[0];
                            while ($i <= $rangeToAdd[1]) {

                                $data["id"] = $i;
                                $data["exten"] = $i;
                                $data["password"] = $i . $i;
                                $data["name"] = $this->view->translate("Extension ") ." ".$i . " <" . $i.">" ;
                                $data["sip"]["password"] = $i . $i;
                                $data["iax2"]["password"] = $i . $i;
                                $data['type'] = 'friend' ;

                                $ret = $this->execAdd($data);

                                if (is_string($ret)) {
                                    $this->view->error .= $i . " - " . $ret;
                                    break;
                                }
                                $i++;
                            }
                        }
                        if ($this->view->error)
                            break;
                    }
                }
            }

            if ($this->view->error) {
                $this->view->error_message = $this->view->error ;
                $this->renderScript('error/sneperror.phtml');
            } else {
                $this->_redirect("default/extensions");
            }
        }

    }

}
