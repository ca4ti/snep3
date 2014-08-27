<?php

/**
 *  This file is part of SNEP.
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as
 *  published by the Free Software Foundation, either version 3 of
 *  the License, or (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 */

/**
 * Class to manager a trunks.
 *
 * @see Snep_Trunks_Manager
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class Snep_Trunks_Manager {

    public function __construct() {
        
    }

    /**
     * getValidation - checks if the queue is used in the rule 
     * @param <int> $id
     * @return <array>
     */
    public function getValidation($id) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('regras_negocio', array('id', 'desc'))
                ->where("regras_negocio.origem LIKE ?", 'T:' . $id)
                ->orwhere("regras_negocio.destino LIKE ?", 'T:' . $id);

        $stmt = $db->query($select);
        $regras = $stmt->fetchall();

        return $regras;
    }

    /**
     * getId
     * @param <string> $name
     * @return <array>
     */
    public function getId($name) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('trunks', array('id'))
                ->where("trunks.callerid = ?", $name);

        $stmt = $db->query($select);
        $id = $stmt->fetch();

        return $id;
    }

    /**
     * getRules - checks if the queue is used in the rule 
     * @param <int> $id
     * @return <array>
     */
    public function getRules($id) {

        $db = Zend_Registry::get('db');

        $rules_query = "SELECT rule.id, rule.desc FROM regras_negocio as rule, regras_negocio_actions_config as rconf WHERE (rconf.regra_id = rule.id AND rconf.value = '$id' AND (rconf.key = 'tronco' OR rconf.key = 'trunk'))";
        $regras = $db->query($rules_query)->fetchAll();

        return $regras;
    }

    /**
     * Remove a trunk
     * @param <int> $id
     */
    public function remove($id) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->delete('trunks', "id = '$id'");

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    /**
     * Remove a trunk
     * @param <int> $id
     */
    public function removePeers($name) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->delete('peers', "name = '$name'");

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    /**
     * getTrunk - set array widh data of trunk
     * @param <int> $id - Trunk code 
     * @return <array> $tronco - Data of trunk
     */
    function getTrunkLog($id) {

        $tronco = array();

        $db = Zend_Registry::get("db");
        $sql = "SELECT  from  trunks where id='$id'";
        $select = $db->select()
                ->from('trunks', array('id', 'name', 'callerid', 'dtmfmode', 'insecure', 'username', 'allow', 'type', 'host', 'map_extensions', 'reverse_auth', 'domain'))
                ->where('trunks.id = ?', $id);

        $stmt = $db->query($select);
        $tronco = $stmt->fetch();

        if ($tronco["type"] != "KHOMP" && $tronco["type"] != "VIRTUAL") {

            $name = $tronco["name"];

            $select = $db->select()
                    ->from('peers', array('fromuser', 'fromdomain', 'nat', 'port', 'qualify', 'type as type_peer', `call-limit as call_limit`))
                    ->where('peers.name = ?', $name);
            $stmt = $db->query($select);
            $peer = $stmt->fetch();

            foreach ($peer as $item => $info) {
                $tronco[$item] = $info;
            }
        }

        return $tronco;
    }

    /**
     * insertLogTronco - Insert data in database
     * @param <string> $acao
     * @param <array> $add
     */
    function insertLogTronco($acao, $add) {

        $db = Zend_Registry::get("db");
        $ip = $_SERVER['REMOTE_ADDR'];
        $hora = date('Y-m-d H:i:s');

        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();

        if ($acao == "Adicionou tronco") {
            $valor = "ADD";
        } else if ($acao == "Excluiu tronco") {
            $valor = "DEL";
        } else {
            $valor = $acao;
        }

        if ($add["type"] != "KHOMP" && $add["type"] != "VIRTUAL") {

            $insert_data = array('id_trunk' => $add['id'],
                'hora' => $hora,
                'ip' => $ip,
                'idusuario' => $username,
                'name' => $add["name"],
                'callerid' => $add["callerid"],
                'dtmfmode' => $add["dtmfmode"],
                'insecure' => $add["insecure"],
                'username' => $add["username"],
                'allow' => $add["allow"],
                'type' => $add["type"],
                'host' => $add["host"],
                'map_extensions' => $add["map_extensions"],
                'reverse_auth' => $add["reverse_auth"],
                'domain' => $add["domain"],
                'nat' => $add["nat"],
                'port' => $add["port"],
                'qualify' => $add["qualify"],
                'call_limit' => $add["call_limit"],
                'tipo' => $valor);

            $db->insert('logs_trunk', $insert_data);
        } else {

            $insert_data = array('id_trunk' => $add['id'],
                'hora' => $hora,
                'ip' => $ip,
                'idusuario' => $username,
                'name' => $add["name"],
                'callerid' => $add["callerid"],
                'dtmfmode' => $add["dtmfmode"],
                'insecure' => $add["insecure"],
                'username' => $add["username"],
                'allow' => $add["allow"],
                'type' => $add["type"],
                'host' => $add["host"],
                'map_extensions' => $add["map_extensions"],
                'reverse_auth' => $add["reverse_auth"],
                'domain' => $add["domain"],
                'tipo' => $valor);

            $db->insert('logs_trunk', $insert_data);
        }
    }

    /**
     * Method to get trunks by name
     * @param <string> $id
     * @return Array
     */
    public function getName($name) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from("trunks", array("id", "callerid"))
                ->where("trunks.callerid = ?", $name);

        $stmt = $db->query($select);
        $trunk = $stmt->fetch();

        return $trunk;
    }

}
