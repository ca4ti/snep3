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
 * Classe to manager a Permission.
 *
 * @see Snep_Permission_Manager
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class Snep_Permission_Manager {

    public function __construct() {
        
    }

    /**
     * getAllPermissions - 
     * @param <string> $profile
     * @return <array>
     */
    public static function getAllPermissions($profile) {

        $db = Zend_registry::get('db');

        $select = $db->select()
                ->from("profiles_permissions", array("permission_id"))
                ->where('profile_id = ?', $profile)
                ->where('allow = ?', 1);

        $stmt = $db->query($select);
        $fetch = $stmt->fetchAll();

        $result = array();
        foreach ($fetch as $value)
            $result[] = $value['permission_id'];
        return $result;
    }

    /**
     * getAll - Retorna todos os resources dos modulos.
     * @return <array>
     */
    public static function getAll() {

        return Snep_Modules::$resources;
    }

    /**
     * update - Method to add update all resources.
     * @param <array> $resources
     * @param <string> $group
     */
    public static function update($resources, $profile) {

        $db = Zend_Registry::get('db');
        $db->delete("profiles_permissions", "profile_id = '$profile'");
        foreach ($resources as $key => $value) {
            $db->insert('profiles_permissions', array(
                "permission_id" => $key,
                "profile_id" => $profile,
                "created" => date('Y-m-d H:i:s'),
                "updated" => date('Y-m-d H:i:s'),
                "allow" => $value
                    )
            );
        }
    }

    /**
     * update - Method to add permission
     * @param <array> $resource
     * @param <string> $id
     */
    public static function addPermissionOld($resource, $id) {

        $db = Zend_Registry::get('db');
        $permission_id = $resource["permission_id"];

        $db->insert('profiles_permissions', array(
            "permission_id" => $permission_id,
            "profile_id" => $id,
            "created" => date('Y-m-d H:i:s'),
            "updated" => date('Y-m-d H:i:s'),
            "allow" => true)
        );
    }

    /**
     * update - Method to remove permission
     * @param <array> $resource
     * @param <string> $id
     */
    public static function removePermissionOld($permission, $id) {

        $db = Zend_Registry::get('db');

        $where[] = "permission_id = '$permission'";
        $where[] = "profile_id = $id";

        $update_data = array('allow' => false,
            'updated' => date('Y-m-d H:i:s'));

        $db->update("profiles_permissions", $update_data, $where);
    }
    
    /**
     * Method list permissions
     * @param type $id
     * @return type
     */
    public static function getPermissions($id) {

        $db = Zend_registry::get('db');

        $select = $db->select()
                ->from("profiles_permissions", array("permission_id"))
                ->where('profile_id = ?', $id)
                ->where('allow = ?', true);

        $stmt = $db->query($select);
        return $stmt->fetchall();
    }

    /**
     * checkExistenceCurrentResource - Verifica se o resource atual existe.
     * @return <booleano>
     */
    public static function checkExistenceCurrentResource() {

        $resources = Snep_Modules::$resources;

        $request = Zend_Controller_Front::getInstance()->getRequest();

        if (!isset($resources[$request->getModuleName()]))
            return false;
        if (!isset($resources[$request->getModuleName()][$request->getControllerName()]))
            return false;
        // if (!isset($resources[$request->getModuleName()][$request->getControllerName()][$request->getActionName()]))
        //     return false;
        return true;
    }

    /**
     * get - Method to get permission
     * @param <string> $group
     * @param <string> $resource
     * @return <array>
     */
    public static function get($group, $resource) {

        $db = Zend_registry::get('db');

        $select = $db->select()
                ->from("profiles_permissions", array("permission_id", "allow"))
                ->where('profile_id = ?', $group)
                ->where('permission_id = ?', $resource);

        $stmt = $db->query($select);
        return $stmt->fetch();
    }

}