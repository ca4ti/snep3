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
require_once "includes/AsteriskInfo.php";
require_once "includes/functions.php";
require_once "includes/AMI.php";

/**
 * IP Status Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class IpStatusController extends Zend_Controller_Action {

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
     * @return type
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Ip Status")));

        try {
            $astinfo = new AsteriskInfo();
        } catch (Exception $e) {
            $this->view->error_message = $this->view->translate("Error! Failed to connect to server Asterisk.");
            $this->renderScript('error/sneperror.phtml');
            return;
        }

        if (!$data = ast_status("database show", "", True)) {
            $this->view->error_message = $this->view->translate("Error! Failed to connect to server Asterisk.");
            $this->renderScript('error/sneperror.phtml');
            exit;
        }


        // Peers  -  asterisk 11 show only registered peers
        if (!$data = ast_status("database show", "", True)) {
            $this->view->error_message = $this->view->translate("Error! Failed to connect to server Asterisk.");
            $this->renderScript('error/sneperror.phtml');
            exit;
        }
        $lines = explode("\n", $data);
        $arr = array();

        foreach ($lines as $indice => $ramal) {
            $arr[] = substr($ramal, 0, strpos($ramal, ":"));
        }

        $agents = array();
        $lista = array();

        foreach ($arr as $ind => $arr2) {
            if (substr($arr2, 1, 3) == 'IAX' || substr($arr2, 1, 3) == 'SIP') {
                $lista[$ind]['tec'] = substr($arr2, 1, 3);
                $lista[$ind]['num'] = substr($arr2, 14);
            }
        }



        $ramais = array();
        foreach ($lista as $ram) {
            $swp = $this->ramalInfo($ram);

            if ($swp['ramal'] != '') {
                $ramais[] = $swp;
            }
        }

        $filas = array();
        $queues = Snep_IpStatus_Manager::getQueue();

        foreach ($queues as $key => $val) {

            $queue_stat = explode("\n", ast_status("queue show " . $val['name'], "", True));
            $calls = $ctd = 0;
            $calls = substr($queue_stat[1], strpos($queue_stat[1], "has") + 3, 3);
            foreach ($queue_stat as $q_key => $q_val) {
                if ($q_key > 2) {
                    if (preg_match('/Callers/i', $q_val)) {
                        break;
                    }
                    $ctd += 1;
                }
            }
            $filas[] = array('name' => $val['name'], 'calls' => $calls, 'members' => $ctd);
        }

        //Iax2 Trunk
        $like = 'IAX%';
        $troncos = Snep_IpStatus_Manager::getTrunk($like);

        // Popula troncos com itens faltantes do array
        $ami = new AMI () ;
        $peer_list = $ami->get_IAXpeerlist() ;
        $reg_list = $ami->get_IAXregistry();


        foreach ($troncos as $key => $val) {
            $ami = new AMI ();
            $troncos[$key]['status'] = "N.D.";
            $troncos[$key]['latencia'] = "N.D.";
            // Get information about peer
            $iax_username = $val['type'] === "VIRTUAL" ? substr($val['channel'], 5): $val['username'];
            foreach ($peer_list as $pl_key => $pl_val) {
                if ($pl_val['objectname'] === $iax_username) {
                    $troncos[$key]['latencia'] = $pl_val['status'];
                }
            }

            //Get information about registry
            
            if ($val['type'] === "VIRTUAL") {
               $troncos[$key]['host'] = $res_peer['ipaddress'] ;
            }
            $res_reg = $ami->get_IAXregistry($troncos[$key]['host'],$iax_username);
            if ($res_reg) {
                $troncos[$key]['status'] = $res_reg['state'];
            }
           
        }

        foreach ($troncos as $val => $key) {
            unset($troncos[$val]['channel']);
            unset($troncos[$val]['username']);
        }


        $this->view->trunkIax = $troncos;

        // Get Trunks Sip from database - table: trunks
        $like = 'SIP%';
        $troncos = Snep_IpStatus_Manager::getTrunk($like);

        // Popula troncos com itens faltantes do array
        foreach ($troncos as $key => $val) {
            $ami = new AMI ();
            $troncos[$key]['status'] = "N.D.";
            $troncos[$key]['latencia'] = "N.D.";
            // Get information about peer
            $sip_username = $val['type'] === "VIRTUAL" ? substr($val['channel'], 4): $val['username'];
            $res_peer = $ami->get_sippeer($sip_username);
            if ($res_peer) {
                $troncos[$key]['latencia'] = $res_peer['status'];
            }
            // Get information about registry
            if ($val['type'] === "VIRTUAL") {
                $troncos[$key]['host'] = $res_peer['tohost'] ;
            }
            $res_reg = $ami->get_SIPshowregistry($troncos[$key]['host'],$sip_username);
            if ($res_reg) {
                $troncos[$key]['status'] = $res_reg['state'];
            }
           
        }


        foreach ($troncos as $val => $key) {
            unset($troncos[$val]['channel']);
            unset($troncos[$val]['username']);
            if (trim($troncos[$val]['latencia']) === "") {
                $troncos[$val]['latencia'] = "UNREACHABLE";
            }
            if (trim($troncos[$val]['status']) === "") {
                $troncos[$val]['status'] = "N.D.";
            }
        }


        $this->view->troncoSip = $troncos;

        /* -------------------------------------------------------------------------------------- */

        if (!$codecs = ast_status("g729 show licenses", "", True)) {
            $this->view->error_message = $this->translate("Error! Failed to connect to server Asterisk.");
            $this->renderScript('error/sneperror.phtml');
            exit;
        }

        $arrCodecs = explode("\n", $codecs);

        $codec = null;
        if (!preg_match("/No such command/", $arrCodecs['1'])) {
            $arrValores = explode(" ", $arrCodecs['1']);
            $exp = explode("/", $arrValores['0']);
            $codec = array('0' => $arrValores['3'],
                '1' => $exp['0'],
                '2' => $exp['1']
            );
        }

        $this->view->filas = $filas;
        $this->view->ramais = $ramais;
        $this->view->codecs = $codec;
        $this->view->headMeta()->appendHttpEquiv('refresh','10');
    }

    /**
     * ramalInfo
     * @param <String> $ramal
     * @return <string>
     */
    function ramalInfo($ramal) {
        if ($ramal['tec'] == 'SIP') {
            if (!$info = ast_status("sip show peer {$ramal['num']}", "", True)) {
                $this->view->error_message = $this->translate("Error! Failed to connect to server Asterisk.");
                $this->renderScript('error/sneperror.phtml');
                exit;
            }

            $return = null;

            $return = array();

            if (preg_match("/(\d+)/", $info, $matches)) {
                $return['ramal'] = $matches[0];
            }
            else
                $return['ramal'] = 'Indeterminado';

            $return['tipo'] = 'SIP';

            $tmp = substr($info, strpos($info, 'Addr->IP'), +35);
            if (preg_match("#[0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}# ", $tmp, $matches)) {
                $return['ip'] = $matches[0];
            }
            else
                $return['ip'] = 'Indeterminado';

            $tmp = substr($info, strpos($info, 'Status'), +40);
            if (preg_match("#\((.*?)\)#", $tmp, $matches))
                $return['delay'] = $matches[0];
            else
                $return['delay'] = '---';

            $tmp = substr($info, strpos($info, 'Codec Order'), +50);
            if (preg_match("#\((.*?)\)#", $tmp, $matches)) {
                $return['codec'] = $matches[0];
                $return['codec'] = str_replace(")", "", $return['codec']);
                $return['codec'] = str_replace("(", "", $return['codec']);
                $return['codec'] = str_replace("|", ", ", $return['codec']);
            }
            else
                $return['codec'] = '---';

            return $return;
        }
    }

}
