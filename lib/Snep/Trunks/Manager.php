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

}
