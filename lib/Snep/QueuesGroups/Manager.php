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
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 */
class Snep_QueuesGroups_Manager {

    private function __construct() { /* Protegendo métodos dinâmicos */
    }

    /**
     * getQueuesAll - Obtem uma lista de todas as filas
     * @return <array> $queues
     */
    public function getQueuesAll() {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('queues', array('id', 'name'));

        $stmt = $db->query($select);
        $queues = $stmt->fetchAll();

        return $queues;
    }

    /**
     * addGroup - Adds the group to the database based on the value reported
     * @param <string> $queueGroup
     * @return \Exception|<object> $idQueueGroup
     */
    public static function addGroup($queueGroup) {
    	
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {
            
        	$insert_data = array('name' => $queueGroup['nome']);
            $db->insert('group_queues', $insert_data);

            $idQueueGroup = $db->lastInsertId();

            $db->commit();

            return $idQueueGroup;
        } catch (Exception $e) {
            $db->rollBack();
            return $e;
        }
    }

    /**
     * addQueuesGroup - Adds the group their queues in the database based on the value reported
     * @param <string> $queueGroup
     * @return \Exception|boolean
     */
    public function addQueuesGroup($queueGroup) {
    	
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {

            $insert_data = array('id_queue' => $queueGroup['id_queue'],
            					 'id_group' =>$queueGroup['id_group']);
            $db->insert('members_group_queues', $insert_data);
            $db->commit();

            return true;
        } catch (Exception $e) {

            $db->rollBack();
            return $e;
        }
    }

    /**
     * Method to get a last id of queue 
     * @return <int> 
     */
    public function lastId() {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('group_queues', array(' max( floor( id ) ) as id'))
                ->limit('1');

        $stmt = $db->query($select);
        $lastId = $stmt->fetch();
        $return = $lastId['id'];

        return $return;
    }

    /**
     * deleteMembers - Remove group members from the database based on his  ID.
     *
     * @param int $id
     */
    public static function deleteMembers($id) {

        $db = Zend_Registry::get('db');
        $db->delete("members_group_queues", "id_group='{$id}'");
    }

    /**
     * Remove a queue group from the database based on his  ID.
     *
     * @param int $id
     */
    public static function deleteGroup($id) {

        $db = Zend_Registry::get('db');
        $db->delete("group_queues", "id='{$id}'");
    }

    /**
     * Return a queue group from the database based on his  ID.
     *
     * @param int $id
     */
    public static function get($id) {

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from('group_queues')
                ->where("id = '$id'");

        $stmt = $db->query($select);
        $group = $stmt->fetch();

        return $group;
    }

    /**
     * Return a members in the queue group from the database based on his  ID.
     *
     * @param int $id
     */
    public static function getMembers($id) {

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from('members_group_queues')
                ->where("id_group = '$id'");

        $stmt = $db->query($select);
        $members = $stmt->fetchAll();

        return $members;
    }

    /**
     * editGroup - Edit the queue group name to the database based on the value reported
     * @param <string> $queueGroup
     * @return <boolean>
     * @throws Exception
     */
    public static function editGroup($queueGroup) {

        $db = Zend_Registry::get('db');
        $db->beginTransaction();
        $value = array('name' => $queueGroup['name']);

        try {

            $db->update('group_queues', $value, 'id =' . $queueGroup['id']);
            $db->commit();
            return true;
        } catch (Exception $e) {

            $db->rollBack();
            throw $e;
        }
    }



}

?>