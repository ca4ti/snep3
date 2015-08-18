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
 * Class to manager a extensions.
 *
 * @see Snep_Extensions_Manager
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class Snep_Extensions_Manager {

    public function __construct() {
        
    }

    /**
     * getValidation - checks if the exten is used in the rule 
     * @param <int> $id
     * @return <array>
     */
    public function getValidation($id) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('regras_negocio', array('id', 'desc'))
                ->where("regras_negocio.origem LIKE ?", 'R:' . $id)
                ->orwhere("regras_negocio.destino LIKE ?", 'R:' . $id);

        $stmt = $db->query($select);
        $regras = $stmt->fetchall();

        return $regras;
    }

    /**
     * getValidation - checks if the exten is used in the rule 
     * @param <int> $id
     * @return <array>
     */
    public function getValidationRules($id) {

        $db = Zend_Registry::get('db');

        $rulesQuery = "SELECT rule.id, rule.desc FROM regras_negocio as rule, regras_negocio_actions_config as rconf WHERE (rconf.regra_id = rule.id AND rconf.value = '$id')";
        $regras = $db->query($rulesQuery)->fetchAll();

        return $regras;
    }

    /**
     * getPeer - Monta array com todos dados do ramal
     * @param <int> $id - Código do ramal
     * @return <array> $ramal - Dados do ramal
     */
    function getPeer($id) {

        $db = Zend_Registry::get("db");

        $select = $db->select()
                ->from("peers", array("id", "name", "canal", "allow", "dtmfmode"))
                ->where("peers.name = ?", $id);


        $stmt = $db->query($select);
        $peer = $stmt->fetch();

        return $peer;
    }

    /**
     * insertLogRamal - insere na tabela logs_users os dados dos ramais
     * @global <int> $id_user
     * @param <array> $add
     */
    function insertLogRamal($acao, $add) {

        $db = Zend_Registry::get("db");

        $ip = $_SERVER['REMOTE_ADDR'];
        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();

        $insert_data = array('hora' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'idusuario' => $username,
            'cod' => $add["name"],
            'param1' => $add["canal"],
            'param2' => $add["allow"],
            'param3' => $add["dtmfmode"],
            'value' => "Ramal",
            'tipo' => $acao);

        $db->insert('logs_users', $insert_data);
    }

    /**
     * remove - Remove peer
     * @param <int> $id
     */
    public function remove($id) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->delete('peers', "name = '$id'");

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    /**
     * removeVoicemail - Remove voicemail
     * @param <int> $id
     */
    public function removeVoicemail($id) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->delete('voicemail_users', "customer_id = '$id'");

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }
    /**
     * Method to get all Peers 
     * @return Array
     */
    public function getAll() {

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from('peers', array('name','callerid')) 
            ->where("peer_type = 'R'");

        $stmt = $db->query($select);
        $peers = $stmt->fetchall();

        return $peers;
    }

}
