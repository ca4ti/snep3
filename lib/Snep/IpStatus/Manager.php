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
 * Classe to manager status
 *
 * @see Snep_IpStatus_Manager
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class Snep_IpStatus_Manager {

    public function __construct() {
        
    }

    /**
     * Get name all Queue
     */
    public function getQueue() {

        $db = Zend_registry::get('db');

        $select = $db->select()
                ->from('queues', array('name'));

        $stmt = $db->query($select);
        $queues = $stmt->fetchAll();

        return $queues;
    }

    /**
     * Get trunk 
     */
    public function getTrunk($like) {

        $db = Zend_registry::get('db');

        $select = $db->select()
                ->from('trunks', array('channel', 'callerid', 'host', 'username', 'type'))
                ->where('trunks.channel LIKE ?', $like);

        $stmt = $db->query($select);
        $trunk = $stmt->fetchAll();

        return $trunk;
    }

}

?>
