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
 * Class to manager Emails send
 *
 * @see Snep_Sendmail
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2017 OpenS Tecnologia
 * @author    Douglas Conrad <douglas.conrad@opens.com.br>
 *
 */
class Snep_Sendmail {

    /**
     * Send mail messages
     * @param array $to, $message, $subject
     */
    public function sendEmail($message) {

        $config_smtp = array('ssl' => Snep_ModuleSettings_Manager::getConfig("smtp_ssl")['config_value'],
                    'auth' => 'login',
                    'port' => Snep_ModuleSettings_Manager::getConfig("smtp_port")['config_value'],
                    'username' => Snep_ModuleSettings_Manager::getConfig("smtp_user")['config_value'],
                    'password' => Snep_ModuleSettings_Manager::getConfig("smtp_password")['config_value']);

        $transport = new Zend_Mail_Transport_Smtp(Snep_ModuleSettings_Manager::getConfig("smtp_server")['config_value'], $config_smtp);

        $config = Zend_Registry::get('config');
        $mail = new Zend_Mail("utf8");
        $mail->setBodyHtml( $message['message'] )
                    ->setFrom( "{$message['from']}" )
                    ->setSubject( $message['subject'] );

        $email = explode(",", $message['to']);
        foreach($email as $mail_address) {
            $mail_address = preg_replace('/\s+/', '', $mail_address);
            $mail->addTo( $mail_address );
        }
        $mail->send($transport);
        return true;
    }
}
