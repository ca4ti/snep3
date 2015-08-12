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

        foreach ($troncos as $val => $key) {
            $troncos[$val]['status'] = "N.D.";
            $troncos[$val]['latencia'] = "N.D.";
        }
        if (!$iax_trunk = ast_status("iax2 show peers", "", True)) {
            $this->view->error_message = $this->translate("Error! Failed to connect to server Asterisk.");
            $this->renderScript('error/sneperror.phtml');
            exit;
        }

        // Define array das linhas retornadas pelo Asterisk
        $trunksReg = explode("\n", ast_status("iax2 show registry", "", True));

        // Varre troncos cadastrados no sistema 
        foreach ($troncos as $key => $val) {

            $sis_chan = $val['channel'];
            $sis_clid = $val['callerid'];
            $sis_host = $val['host'];
            $sis_user = $val['username'];
            $sis_type = $val['type'];
            $CV = $CSS = False;

            // Varre troncos com autenticacao para pegar status e latencia
            foreach ($trunksReg as $tr_key => $tr_val) {
                if (preg_match('/^(Privilege|Host|$).*$/', $tr_val)) {
                    continue;
                }
                // Array individual apra cada tronco
                $tr_val_ind = explode(' ', ltrim(preg_replace('/ +/', ' ', $tr_val)));

                $tr_user = $tr_val_ind[2];
                $tr_host = substr($tr_val_ind[0], 0, strpos($tr_val_ind[0], ":"));

                // Verifica latencia do tronco
                $peer_user = ($sis_user != "") ? $sis_user : $tr_user;

                $peer_lat = ast_status("iax2 show peer $peer_user", "Status", True);
                $peer_lat = explode(":", $peer_lat);

                // SE    o username do BD = Username do Asterisk e
                // E SE  o host do BD = Hostname do Asterisk  
                // ENTÃO Define o status como sendo o State do Asterisk
                if ($tr_user === $sis_user && $tr_host === $sis_host) {
                    $troncos[$key]['status'] = $tr_val_ind[5];
                    $troncos[$key]['latencia'] = $peer_lat[1];
                } else {
                    // Se o tipo do tronco for VIRTUAL, BD nao tem Host e nem Username
                    if ($sis_type == "VIRTUAL") {
                        // Define como Username a 2a. parte do Channel
                        $virt_name = substr($sis_chan, strpos($sis_chan, "/") + 1);
                        if ($virt_name === $tr_user) {
                            $CV = True;
                            $troncos[$key]['status'] = $tr_val_ind[5];
                            $troncos[$key]['host'] = $tr_host;
                            $troncos[$key]['username'] = $tr_user;
                            $troncos[$key]['latencia'] = $peer_lat[1];
                        }
                    } elseif ($sis_type == "SNEPIAX2") {
                        $CSS = True;
                        $troncos[$key]['latencia'] = $peer_lat[1];
                    }
                }
            }
            if ($sis_type == "SNEPIAX2" && !$CSS) {

                // Define como Username a 2a. parte do Channel
                $virt_name = substr($sis_chan, strpos($sis_chan, "/") + 1);
                $iax_peer = explode("\n", ast_status("iax2 show peer $virt_name", "", True));
                $peer_lat = implode(":", preg_grep('/Status/', $iax_peer));
                $troncos[$key]['latencia'] = substr($peer_lat, strpos($peer_lat, ":") + 2);
                $peer_host = implode(":", preg_grep('/Addr->IP/', $iax_peer));
                $peer_host = substr($peer_host, strpos($peer_host, ":") + 2);
                $troncos[$key]['host'] = substr($peer_host, 0, strpos($peer_host, "Port"));
                $troncos[$key]['username'] = $virt_name;
            }
            if ($sis_type == "VIRTUAL" && !$CV) {

                // Define como Username a 2a. parte do Channel
                $virt_name = substr($sis_chan, strpos($sis_chan, "/") + 1);
                $iax_peer = explode("\n", ast_status("iax2 show peer $virt_name", "", True));
                $peer_lat = implode(":", preg_grep('/Status/', $iax_peer));
                $troncos[$key]['latencia'] = substr($peer_lat, strpos($peer_lat, ":") + 2);
                $peer_host = implode(":", preg_grep('/Addr->IP/', $iax_peer));
                $peer_host = substr($peer_host, strpos($peer_host, ":") + 1);
                $troncos[$key]['host'] = substr($peer_host, 0, strpos($peer_host, "Port"));
                $troncos[$key]['username'] = $virt_name;
            }
        }
        foreach ($troncos as $val => $key) {
            unset($troncos[$val]['channel']);
        }

        $this->view->trunkIax = $troncos;

        // Trunk Sip
        $like = 'SIP%';
        $troncos = Snep_IpStatus_Manager::getTrunk($like);

        // Popula troncos com itens faltantes do array
        foreach ($troncos as $val => $key) {
            $troncos[$val]['status'] = "N.D.";
            $troncos[$val]['latencia'] = "N.D.";
        }
        if (!$sip_trunk = ast_status("sip show peers", "", True)) {
            $this->view->error_message = $this->translate("Error! Failed to connect to server Asterisk.");
            $this->renderScript('error/sneperror.phtml');
            exit;
        }

        // Define array das linhas retornadas pelo Asterisk
        $trunksReg = explode("\n", ast_status("sip show registry", "", True));

        foreach ($troncos as $key => $val) {
            $troncos[$key]['status'] = "";
            $troncos[$key]['latencia'] = "";
            $sis_chan = $val['channel'];
            $sis_clid = $val['callerid'];
            $sis_host = $val['host'];
            $sis_user = $val['username'];
            $sis_type = $val['type'];

            // Varre troncos com autenticacao para pegar status e latencia
            $CV = $CSS = False;
            foreach ($trunksReg as $tr_key => $tr_val) {
                if (preg_match('/^(Privilege|Host|$).*$/', $tr_val)) {
                    continue;
                }
                // Array individual para cada tronco
                $tr_val_ind = explode(' ', ltrim(preg_replace('/ +/', ' ', $tr_val)));

                $tr_user = $tr_val_ind[2];
                $tr_host = substr($tr_val_ind[0], 0, strpos($tr_val_ind[0], ":"));

                // Verifica latencia do tronco
                $peer_user = ($sis_user != "") ? $sis_user : $tr_user;

                $sip_peer = explode("\n", ast_status("sip show peer $peer_user", "", True));
                $peer_lat = implode(":", preg_grep('/Status/', $sip_peer));
                $peer_lat = substr($peer_lat, strpos($peer_lat, ":") + 2);

                // SE    o username do BD = Username do Asterisk e
                // E SE  o host do BD = Hostname do Asterisk  
                // ENTÃO Define o status como sendo o State do Asterisk
                if ($tr_user === $sis_user && $tr_host === $sis_host) {
                    if ($tr_val_ind[4] === "Registered")
                        $troncos[$key]['status'] = $tr_val_ind[4];
                    else
                        $troncos[$key]['status'] = $tr_val_ind[4] . ' ' . $tr_val_ind[4];
                    	$troncos[$key]['latencia'] = $peer_lat;
                } else {
                    // Se o tipo do tronco for VIRTUAL, BD naotem Host e nem Username
                    if ($sis_type == "VIRTUAL") {
                        // Define como Username a 2a. parte do Channel
                        $virt_name = substr($sis_chan, strpos($sis_chan, "/") + 1);

                        if ($virt_name === $tr_user) {
                            $CV = True;
                            if ($tr_val_ind[3] === "Registered")
                                $troncos[$key]['status'] = $tr_val_ind[3];
                            else
                                $troncos[$key]['status'] = $tr_val_ind[3] . ' ' . $tr_val_ind[4];
                            $troncos[$key]['host'] = $tr_host;
                            $troncos[$key]['username'] = $tr_user;
                            $troncos[$key]['latencia'] = $peer_lat;
                        }
                    } elseif ($sis_type == "SNEPSIP") {
                        $CSS = True;
                        $troncos[$key]['latencia'] = $peer_lat;
                    }
                }
            }

            if ($sis_type == "SNEPSIP" && !$CSS) {

                // Define como Username a 2a. parte do Channel
                $virt_name = substr($sis_chan, strpos($sis_chan, "/") + 1);
                $sip_peer = explode("\n", ast_status("sip show peer $virt_name", "", True));
                $peer_lat = implode(":", preg_grep('/Status/', $sip_peer));
                $troncos[$key]['latencia'] = substr($peer_lat, strpos($peer_lat, ":") + 2);
                $peer_host = implode(":", preg_grep('/ToHost/', $sip_peer));
                $peer_host = substr($peer_host, strpos($peer_host, ":") + 2);
                $troncos[$key]['host'] = substr($peer_host, strpos($peer_host, ":"));
                $troncos[$key]['username'] = $virt_name;
            }

            if ($sis_type == "VIRTUAL" && !$CV) {

                // Define como Username a 2a. parte do Channel
                $virt_name = substr($sis_chan, strpos($sis_chan, "/") + 1);
                $sip_peer = explode("\n", ast_status("sip show peer $virt_name", "", True));
                $peer_lat = implode(":", preg_grep('/Status/', $sip_peer));
                $troncos[$key]['latencia'] = substr($peer_lat, strpos($peer_lat, ":") + 2);
                $peer_host = implode(":", preg_grep('/ToHost/', $sip_peer));
                $troncos[$key]['host'] = substr($peer_host, strpos($peer_host, ":") + 2);
                $troncos[$key]['username'] = $virt_name;
            }
        }

        foreach ($troncos as $val => $key) {
            unset($troncos[$val]['channel']);
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
