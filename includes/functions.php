<?php

/**
  *  This file is part of SNEP.
 *  Para território Brasileiro leia LICENCA_BR.txt
 *  All other countries read the following disclaimer
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Classe Formata - Data Format
 *
 * @see Classes.php
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class Formata {
    
    /**
     * fmt_segundos - Formats seconds on a standard outlet
     * @param <array> $params
     * @return <string> $marty
     */
    function fmt_segundos($params,$smarty = null){
        
        $segundos = $params['a'] ;
        $tipo_ret = (isset($params['b']) && $params['b'] != "") ? $params['b'] : 'hms' ;
        
        switch($tipo_ret){
            case "m":
                $ret = $segundos/60;
            break;
            
            case "H":
                $ret = $segundos/3600;
            break;
        
            case "h":
                $ret = round($segundos/3600);
            break;
        
            case "D":
                $ret = $segundos/86400;
            break;
        
            case "d":
                $ret = round($segundos/86400);
            break;
        
            case "hms":
                $min_t = intval($segundos/60) ;
                $tsec = sprintf("%02s",intval($segundos%60)) ;
                $thor = sprintf("%02s",intval($min_t/60)) ;
                $tmin = sprintf("%02s",intval($min_t%60)) ;
                $ret = $thor.":".$tmin.":".$tsec;
            break ;
        
            case "ms":
                $min_t = intval($segundos/60) ;
                $tsec = sprintf("%02s",intval($segundos%60)) ;
                $tmin = sprintf("%02s",intval($min_t%60)) ;
                $ret = $tmin.":".$tsec;
            break ;
       }
       
       return $ret ;
   } 

   /**
     * fmt_telefone - Format Phone Number
     * @param <array> $params
     * @return <string> $number
     */
    function fmt_telefone($params) {
        
        $numero = trim($params['a']);
        
        // Get country code 
        $country = Zend_Registry::get('config')->system->country_code;
        
        switch ($country) {
            case 76 : // Brazil
                // Number < 8 digits, return same number
                if (strlen($numero) < 8 || !is_numeric($numero)) {
                    return $numero;
                }
                // Numbers 0300 and 0800
                if (substr($numero, 0, 4) == "0800" || substr($numero, 0, 4) == "0300") {
                    $numero = substr($numero, 0, 4) . "-" . substr($numero, 4);
                }
                switch (strlen($numero)) {
                    case 8: 
                    case 9:
                        // Local numbers
                        $num = substr($numero, -4);
                        $prefixo = substr($numero, 0, strlen($numero)-4);
                        $numero = "$prefixo-$num" ;
                        break ; 
                    case 10 :  
                        // DDD + Number 8 digits
                        $num = substr($numero, -4);
                        $prefixo = substr($numero, 2, 4);
                        $ddd = substr($numero,0,2);
                        $numero = "($ddd) $prefixo-$num" ; 
                        break ;
                    case 11 : 
                        $num = substr($numero, -4);
                        // 0 + DDD + Numer 8 digits 
                        if (substr($numero,0,1) == 0) {
                            $prefixo = substr($numero, 3, 4);
                            $ddd = substr($numero,0,3);
                        // DDD + Number 9 digits
                        } else { 
                            $prefixo = substr($numero, 2, 5);  // 9. digito
                            $ddd = substr($numero,0,2);
                        }
                        $numero = "($ddd) $prefixo-$num" ; 
                        break ;
                    case 12 : 
                        $num = substr($numero, -4);
                        // 0 + DDD + Number 9 digits 
                        if (substr($numero,0,1) == 0) {
                            $prefixo = substr($numero, 3, 5);  // 9. digito
                            $ddd = substr($numero,0,3);
                            $ope = "";
                         // OPER + DDD + number 8 digits
                        } else {
                            $prefixo = substr($numero, 4, 4);  
                            $ddd = substr($numero,2,2);
                            $ope = substr($numero,0,2);
                        }
                        $numero = "$ope ($ddd) $prefixo-$num" ;
                        break ;
                    case 13 : 
                        $num = substr($numero, -4);
                        // 0 + OPER + DDD + Number 8 digits
                        if (substr($numero,0,1) == 0) {
                            $prefixo = substr($numero, 5, 4); 
                            $ddd = substr($numero,3,2);
                            $ope = substr($numero,0,3);
                        // OPER + 0 + DDD + Number 8 Digits
                        } elseif (substr($numero,2,1) == 0) {
                            $prefixo = substr($numero, 5, 4); 
                            $ddd = substr($numero,2,3);
                            $ope = substr($numero,0,2);
                        } else {
                            // OPER + DDD + Number 9 Digits
                            $prefixo = substr($numero, 4, 5);  // 9. digito  
                            $ddd = substr($numero,2,2);
                            $ope = substr($numero,0,2);
                        }
                        $numero = "$ope ($ddd) $prefixo-$num" ;
                        break ;
                    case 14: 
                        // 0 + OPER + DDD + Number 9 digits
                        $num = substr($numero, -4);
                        $prefixo = substr($numero, 5, 4);
                        $ddd = substr($numero,3,2);
                        $ope = substr($numero,0,3);
                        $numero = "$ope ($ddd) $prefixo-$num" ;
                        break ;
                }; 
                break ;
        }
        // Return formated number
        return $numero;
    }

    /**
     * fmt_cep - Format CEP
     * @param <string> $number
     * @return <string> $formated cep number
     */
    function fmt_cep($number){
         // Get country code 
        $country = Zend_Registry::get('config')->system->country_code;
        
        switch ($country) {
            case 76 : // Brazil
                $cep = substr($number,0,2).".".substr($number, 2,3)."-".substr($number, 5,3); 
                break ;
        }
        return $cep ;
   }
   
} 

/**
 * Verifica alguns status do asterisk utilizando a classe phpagi-asmanager
 * @param <String> $comando - comando do asterisk ou Action
 *                          -> Se for Action, incluir a palavra "Action"
 * @param <String> $quebra - linha que retorna o resultado
 * @param <boolean>  $tudo - True/False - Se devolve todo Resultado ou nao
 * @return <String>
 */
function ast_status($comando, $quebra, $tudo = False) {
    require_once "AsteriskInfo.php";
    $astinfo = new AsteriskInfo();
    return $astinfo->status_asterisk($comando, $quebra, $tudo);
}


/**
 * 
 * Le arquivos do servidor
 * @param <String> $strFileName - Caminho/Nome do Arquivo a ser lido
 * @param <String> $intLines - Numero de linhas a serem retornadas
 * @param <String> $intBytes - Tamanho Maximo em bytes a ser lido por linha
 * @return <array> 
 */
function rfts($strFileName, $intLines = 0, $intBytes = 4096) {
    $strFile = "";
    $intCurLine = 1;
    if (file_exists($strFileName)) {
        if ($fd = fopen($strFileName, 'r')) {
            while (!feof($fd)) {
                $strFile .= fgets($fd, $intBytes);
                if ($intLines <= $intCurLine && $intLines != 0) {
                    break;
                } else {
                    $intCurLine++;
                }
            }
            fclose($fd);
        } else {
            return "ERROR";
        }
    } else {
        return "ERROR";
    }
    return $strFile;
}
