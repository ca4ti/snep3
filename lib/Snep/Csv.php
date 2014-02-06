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
 * Classe que gera um CSV
 *
 * @see Snep_Csv
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2011 OpenS Tecnologia
 * @author    Rafael Pereira Bozzetti <rafael@opens.com.br>
 * 
 */
class Snep_Csv {

    public function __construct() {}

    /**
     *
     * @param array $data
     * @param bool $header
     * @return string 
     */
    public function generate($data, $header = true, $title = null) {

        $indexes = array();
        $values = "";
        foreach($data as $k => $registers) {

            if(is_null($indexes)) {
                $indexes = array_keys($registers);
            }
            $values .= preg_replace("/(\r|\n)+/", "", implode(",", $registers) ) ;
            $values .= "\n";
        }

        $headers = array();
        foreach($indexes as $i => $v) {
             if ($title){
                 $headers[$v] = $title[$v];
             }else{
                 $headers[$v] = $v;
             }
        }

        if($header) {
            $output = implode(",", $headers ) . "\n";
        }

        $output .= $values;
        return $output;
    }

}
