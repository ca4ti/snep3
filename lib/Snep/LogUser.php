<?php

/**
 *  This file is part of SNEP.
 *  Para território Brasileiro leia LICENCA_BR.txt
 *  All other countries read the following disclaimer
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
 * Class to manage actions Loguser
 * 
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br> 
 *            
 * 
 */
class Snep_LogUser {
    
    /**
     * salvaLog - Inserts data in the database
     * @param <string> $acao
     * @param <string> $idAction
     * @param <int> $tipo
     * @return boolean
     */
    function salvaLog($acao, $idAction, $tipo) {
        $db = Zend_Registry::get("db");
        $ip = $_SERVER['REMOTE_ADDR'];
                
        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();
        $acao = mysql_escape_string($acao);
        
        $insert_data = array('hora' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'idusuario' => $username,
            'acao' => $acao,
            'idaction' => $idAction,
            'tipo' => $tipo);
       
       $db->insert('logs', $insert_data);
    }

    

}

?>