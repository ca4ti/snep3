<?php

/*
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

require_once '../../../includes/functions.php';


/**
 * Services Contact
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2017 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class ContactsService implements SnepService {

    /**
     * Execute action
     * URL: http://127.0.0.1/snep/modules/default/api/?service=Contact&phone=4899999999&name=Xxxxx
     */
    public function execute() {

    	$config = Zend_Registry::get('config');
      $db = Zend_registry::get('db');

      // get by phone
      if(isset($_GET["phone"])){
        $phone = $_GET["phone"];
        $select = "SELECT * from contacts_phone as p, contacts_names as n WHERE p.phone = ".$phone ." AND p.contact_id = n.id";
        $stmt = $db->query($select);
        $contact = $stmt->fetchAll();

        if(!empty($contact)){
        	return array("status" => "ok", "contact" => $contact);
      	}elseif(!$_GET["name"]){
      		return array("status" => "empty", "message" => "No entries found.");
      	}
      }

      // get by name
      if(isset($_GET["name"])){
        $name = $_GET["name"];
        $select = "SELECT * from contacts_phone as p, contacts_names as n WHERE n.name = '".$name ."' AND p.contact_id = n.id";
        $stmt = $db->query($select);
        $contact = $stmt->fetchAll();

        if(!empty($contact)){
        	return array("status" => "ok", "contact" => $contact);
      	}else{
      		return array("status" => "empty", "message" => "No entries found.");
      	}
      }

    }

}
