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
 * Class to list recording and information of calls.
 *
 * @see Snep_RecordReport_Manager
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 * 
 */
class Snep_RecordReport_Manager {

    public function __construct() {
        
    }
    
    /**
     * Get data of calls
     * @param <string> $condicao
     * @return <array>
     */
    public function getCalls($condicao) {

        $db = Zend_Registry::get('db');
                      
        $select = $db->select()
                ->from('cdr', array("calldate","src","dst","duration","billsec","disposition","userfield"))
                ->where($condicao)
                ->where('disposition = ?', 'ANSWERED')
                ->where('userfield != ?', ' ');
               

        $stmt = $db->query($select);
        $calls = $stmt->fetchall();

        return $calls;
    }
    
}
