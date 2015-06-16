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
 * Snep Notifications
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2015 Opens Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class Snep_Notifications {

	/**
     * getNotification - 
     * @return <string> HTML rendered with notifications
     */
    public function getNotifications($url) {
        
        $i18n = Zend_Registry::get("i18n");
        $html = "";
        $notifications = self::getAll();
        
        if($notifications){
	        
	        foreach ($notifications as $key => $notification) {
	            
                // number of notifications in the top menu
                if($key < 3){
    	            $html .= "<li><a href=".$url."/index.php/default/notifications?id=".$notification['id'].">";
    	            $html .= "<div><strong>".$notification['title']."</strong>";
    	            $html .= "<span class='pull-right text-muted'><em>".date("d/m/Y G:i:s", strtotime($notification['creation_date']))."</em>";
    	            $html .= "</spam></div>"; 
    	            $html .= "<div>".substr($notification['message'], 0,30).'...</div></a></li>';
    	            
    	            if($key < 2){
    	            	$html .= "<li class='divider'></li>";
    	            }
                }
	        }

	        $html .= "<li class='divider'></li><li><a class='text-center' ";
	        $html .= "href='".$url."/index.php/default/notifications?id=all'>";
	        $html .= "<strong>".$i18n->translate('Read All Messages')."</strong>";
	        $html .= "</a></li>";
	        

	    }else{

	    	$html .= "<li><a class='text-center'>";
	        $html .= "<strong>".$i18n->translate('You have no notifications')."</strong>";
	        $html .= "</a></li>";

	    }
        
        return $html;
    }

    /**
     * Method to get all profiles
     * @return <array> $notifications
     */
    public function getAll() {

        $db = Zend_registry::get('db');

        $select = $db->select()
                ->from("core_notifications")
                ->order("creation_date DESC");

        $stmt = $db->query($select);
        $notifications = $stmt->fetchAll();

        return $notifications;
    }


    /**
     * Get notification where not read
     * @return <array> $notification
     */
    public function getNoView() {

        $db = Zend_registry::get('db');

        $select = $db->select()
                ->from("core_notifications")
                ->where("core_notifications.read = ?",false);

        $stmt = $db->query($select);
        $notification = $stmt->fetchAll();

        return $notification;
    }

    /**
     * setRead - Update core_notifications while user notification read
     * @param <int> $id
     */
    public function setRead($id) {

        $db = Zend_Registry::get('db');

        $update_data = array('read' => true,
            'reading_date' => date('Y-m-d H:i:s'));

        $db->update("core_notifications", $update_data, "id = '{$id}'");
    }

    /**
     * addNotification - Method to add notification a snep
     * @param <string> $title
     * @param <string> $notification
     */
    public function addNotification($title,$message) {

        $db = Zend_Registry::get('db');

        $insert_data = array('title' => $title,
            'message' => $message,
            'creation_date' => date('Y-m-d H:i:s'));

        $db->insert('core_notifications', $insert_data);
    
    }

}
?>