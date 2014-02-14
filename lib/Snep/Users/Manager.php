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
 * Class to manager a Users.
 *
 * @see Snep_Users_Manager
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class Snep_Users_Manager {

    public function __construct() {
        
    }

    /**
     * Method to add a user.
     * @param <array> $user
     */
    public function add($user) {

        $db = Zend_Registry::get('db');

        $insert_data = array('name' => $user['name'],
            'password' => md5($user['password']),
            'email' => $user['email'],
            'profile_id' => $user['group'],
            'created' => date('Y-m-d H:i:s'),
            'updated' => date('Y-m-d H:i:s'));

        $db->insert('users', $insert_data);
    }

    /**
     * Method to remove a user
     * @param <int> $id
     */
    public function remove($id) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->delete('users', "id = '$id'");

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    /**
     * Method to remove data of user in table password_recovery
     * @param <int> $id
     */
    public function removeRecovery($id) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->delete('password_recovery', "user_id = '$id'");

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    /**
     * addProfile - Adds the id of the profile to the User
     * @param <array> $data
     * @return \Exception|boolean
     */
    public function addProfile($data) {

        $db = Zend_Registry::get('db');

        $update_data = array('profile_id' => $data['profile'],
            'updated' => date('Y-m-d H:i:s'));

        $db->update("users", $update_data, "id = '{$data['user']}'");
    }

    /**
     * Method to get a user by id
     * @param <int> $id
     * @return <Array> $users
     */
    public function get($id) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('users')
                ->where("users.id = ?", $id);

        $stmt = $db->query($select);
        $users = $stmt->fetch();

        return $users;
    }

    /**
     * Method to update a user data
     * @param <Array> $user
     */
    public function edit($user) {

        $db = Zend_Registry::get('db');

        $update_data = array('name' => $user['name'],
            'password' => $user['password'],
            'email' => $user['email'],
            'profile_id' => $user['group'],
            'created' => $user['created'],
            'updated' => date('Y-m-d H:i:s'));

        $db->update("users", $update_data, "id = '{$user['id']}'");
    }

    /**
     * addProfileByName - Update profile
     * @param <array> $data
     * @return \Exception|boolean
     */
    public function addProfileByName($data) {

        $db = Zend_Registry::get('db');
        $db->beginTransaction();
        $cond = $data['box'];
        $where = "`users`.`name` = '{$cond}'";

        try {

            $value = array("users.profile_id" => (int) $data['id']);

            $db->update("users", $value, $where);
            $db->commit();

            return true;
        } catch (Exception $e) {

            $db->rollBack();
            return $e;
        }
    }

    /**
     * removeProfileByName - Update user for profile default
     * @param <string> $name
     * @param <int> $id
     */
    public function removeProfileByName($name) {

        $db = Zend_Registry::get('db');

        $update_data = array('profile_id' => 1,
            'updated' => date('Y-m-d H:i:s'));

        $db->update("users", $update_data, "name = '{$name}'");
    }

}

?>