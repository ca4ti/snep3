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
 *  Class that  controls  the  persistence  of pickup groups.
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2016 Opens Tecnologia
 * @author Tiago Zimmermann
 */
class Snep_ModuleSettings_Manager {

    private function __construct() { /* Protegendo métodos dinâmicos */
    }

    private function __destruct() { /* Protegendo métodos dinâmicos */
    }

    private function __clone() { /* Protegendo métodos dinâmicos */
    }

    /**
     * Add module configuration in database
     * 
     * @param array $result
     */
    public static function addConfig($result) {

        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {
            $db->insert('core_config', $result);
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return $e;
        }
    }


    /**
    * Return a configuration from the database based on his module.
    *
    * @param string $module
    * @return array $data
    */
    public static function get($module) {

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from('core_config')
                ->where("config_module = '$module'");

        $stmt = $db->query($select);
        $data = $stmt->fetchAll();

        return $data;
    }

    /**
     * Remove a pickup group from the database based on his  ID.
     *
     * @param string $module
     */
    public static function delConfig($module) {

        $db = Zend_Registry::get('db');
        $db->delete("core_config", "config_module='{$module}'");
    }

}
