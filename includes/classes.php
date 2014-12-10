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

/*-----------------------------------------------------------------------------
 * Classe Bar_Graph - Monta uma linha horizontal para exibicao grafica
 * Recebe : array($parrams), com os seguintes valores:
 *          a = Valor percentual
 * Retorna: Linha de "imagens"  que compoe uma barra horizontal do grafico
 *----------------------------------------------------------------------------*/

/**
 * Classe Bar_Graph - Riding a horizontal line for Graphic Display
 *
 * @see Classes.php
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class Bar_Graph {
    
    function linha($params, &$smarty) {
        $a = $params['a'] ;
       
        if (strpos($a,"%") > 0)
            $a = substr($a,0,strpos($a,"%")) ;
        
        if ($a < 10) {
            $ret="<table class=subtable border=0 cellpadding=0 cellspacing=0 width=100%><tr>" ;
            $ret.="<td class=subtable height=16 width=$a% style=".'"background: url(../imagens/greenbar_middle.gif) repeat-x;">'."</td><td class=subtable style=".'"text-align:left;color: #000;font-weight: bold" '." width=".(100-$a)."%>$a%</td></tr></table>" ;
        } elseif ($a >= 10  && $a < 50) {
            $ret="<table class=subtable border=0 cellpadding=0 cellspacing=0 width=100%><tr>" ;
            $ret.="<td class=subtable height=16 width=$a% style=".'"background: url(../imagens/greenbar_middle.gif) repeat-x;text-align:right;color: #000;font-weight: bold">'."$a% </td><td class=subtable width=".(100-$a)."%></td></tr></table>" ;
        } elseif ($a >= 50  && $a < 80) {
            $ret="<table class=subtable border=0 cellpadding=0 cellspacing=0 width=100%><tr>" ;
            $ret.="<td class=subtable height=16 width=$a% style=".'"background: url(../imagens/orangebar_middle.gif) repeat-x;text-align:right;color: #FFF;font-weight: bold">'."$a% </td><td class=subtable width=".(100-$a)."%></td></tr></table>" ;
        } else {
            $ret="<table class=subtable border=0 cellpadding=0 cellspacing=0 width=100%><tr>" ;
            $ret.="<td class=subtable height=16 width=$a% style=".'"background: url(../imagens/redbar_middle.gif) repeat-x;text-align:right;color: #FFF;font-weight: bold">'."$a% </td><td class=subtable width=".(100-$a)."%></td></tr></table>" ;
        } 

       return $ret ;
    } 
} 

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
     * @return <string> $marty
     */
    function fmt_telefone($params, $smarty = null) {
        
        $numero = trim($params['a']);
        
        if (strlen($numero) < 8 || !is_numeric($numero))
            return $numero;
        if (substr($numero, 0, 4) == "0800" || substr($numero, 0, 4) == "0300") {
            $numero = substr($numero, 0, 4) . "-" . substr($numero, 4);
        }
        if (strlen($numero) == 8 || strlen($numero) == 9 ) {
            $num = substr($numero, -4);
            $prefixo = substr($numero, 0, strlen($numero)-4);
            $numero = "$prefixo-$num" ;   

        } elseif ( strlen($numero) == 10 ) {
            $num = substr($numero, -4);
            $prefixo = substr($numero, 2, 4);
            $ddd = substr($numero,0,2);
            $numero = "($ddd) $prefixo-$num" ; 

        } elseif ( strlen($numero) == 11 ) {
            $num = substr($numero, -4);
            if (substr($numero,0,1) == 0) {
                $prefixo = substr($numero, 3, 4);
                $ddd = substr($numero,0,3);
            } else { 
                $prefixo = substr($numero, 2, 5);  // 9. digito
                $ddd = substr($numero,0,2);
            }
            $numero = "($ddd) $prefixo-$num" ; 
        
        } elseif ( strlen($numero) == 12 ) {
            $num = substr($numero, -4);
            if (substr($numero,0,1) == 0) {
                $prefixo = substr($numero, 3, 5);  // 9. digito
                $ddd = substr($numero,0,3);
                $ope = "";
            } else {
                $prefixo = substr($numero, 4, 4);  
                $ddd = substr($numero,2,2);
                $ope = substr($numero,0,2);
            }
            $numero = "$ope ($ddd) $prefixo-$num" ;

        } elseif ( strlen($numero) == 13 ) {
            $num = substr($numero, -4);
            if (substr($numero,0,1) == 0) {
                $prefixo = substr($numero, 5, 4); 
                $ddd = substr($numero,3,2);
                $ope = substr($numero,0,3);
            } elseif (substr($numero,2,1) == 0) {
                $prefixo = substr($numero, 5, 4); 
                $ddd = substr($numero,2,3);
                $ope = substr($numero,0,2);
            } else {
                $prefixo = substr($numero, 4, 5);  // 9. digito  
                $ddd = substr($numero,2,2);
                $ope = substr($numero,0,2);
            }
            $numero = "$ope ($ddd) $prefixo-$num" ;

        } elseif ( strlen($numero) == 14 ) {
            $num = substr($numero, -4);
            $prefixo = substr($numero, 5, 4);
            $ddd = substr($numero,3,2);
            $ope = substr($numero,0,3);
            $numero = "$ope ($ddd) $prefixo-$num" ;
        } 
        return $numero;
    }

    /**
     * fmt_cep - Format CEP
     * @param <string> $number
     * @return <string> $cep
     */
    function fmt_cep($number){
         
        $cep = substr($number,0,2).".".substr($number, 2,3)."-".substr($number, 5,3); 
        return $cep ;
   }

    /**
     * fmt_cidade - Research and displays city name
     * @param <array> $params
     * @return <string> $smarty
     */
    function fmt_cidade($params,$smarty = "")  {
        $db = Zend_Registry::get('db');

        global $LANG;
        $flag = "N" ;
        $tp_ret = ($smarty=="A") ? "A" : "" ;
        $telefone=trim($params['a']);
        
        if (strlen($telefone)==6)
            $prefixo = $telefone ;
        elseif (strlen($telefone) > 6) {
            $prefixo = substr($telefone,0,strlen($telefone)-4) ;
            if (strlen($prefixo) > 6)
               $prefixo=substr($prefixo,-6) ;
        } else
            $prefixo = $telefone ;
            
            if (!is_numeric($prefixo))
                $cidade = $LANG['unknown'];
        
        try {
             $sqlcidade = "select ars_cidade.name as municipio, ars_ddd.estado as uf from ars_prefixo INNER JOIN ars_cidade on ars_cidade.id = ars_prefixo.cidade INNER JOIN ars_ddd on ars_ddd.cidade = ars_cidade.id where prefixo = '$prefixo'" ;
             $rowcidade = $db->query($sqlcidade)->fetch();
             $cidade = ucfirst(strtolower($rowcidade['municipio']))."-".$rowcidade['uf'] ;
         
        if (strlen($cidade) <= 3) {
            if (strlen($telefone) >= 8 && substr($prefixo,-4,1) > 6)
                $cidade = $LANG['cell'];
            else
                if ( strlen($telefone) >= 14 )
                    $cidade = $LANG['international'];
                else
                    $cidade = $LANG['local'];
        } else
            $flag = "S" ;  // Encontrou cidade na tabela CNL
        } catch (Exception $e) {
            $cidade = $LANG['error'];
        }
      
        if ($tp_ret == "A")
            return array("cidade"=>$cidade,"flag"=>$flag) ;
        else
            return $cidade ;
    } 
   
    /**
     * fmt_gravacao - 
     * @param <array> $params
     * @return <string> $smarty
     */
    function fmt_gravacao($params,$smarty) {

        $calldate = $params['a'] ;
        $userfield = $params['b'] ;     

        $caminho = Snep_Manutencao::arquivoExiste($calldate, $userfield);

        if($caminho) {
            $smarty->assign('voz', $caminho);
        }else{
            $smarty->assign('voz', 'N.D.');   
        }
   
    } 

    /**
     * fmt_gravacao - Estimated rate of a Liaison
     * @param <array> $params
     * @return <string> $smarty
     */
    function fmt_tarifa($param, $smarty = null) {
        $db = Zend_Registry::get('db');

        $destino = $param['a'] ;
        $duracao = $param['b'] ;
        $ccusto = $param['c'] ;
        $dt_chamada = $param['d'] ;
        $tipoccusto = ( isset($param['e']) ? $param['e'] : NULL );

        // DEBUG de Tarifação, retorna string com informações da tarifação
        $dbg = 0;

        // Aceita somente destino de 8,11 ou 13 digitos
        $tn = strlen($destino) ;      
        $duracao = (int)$duracao ;

        // Chamada não efetuada, tempo igual a zero.
        if ($duracao == 1) {
            return $smarty == "A" ? "0" : "0,00";
            exit;
        }

        // Descarta ligação de entrada, não tarifáveis.
        if ( $tn < 8  || !is_numeric($destino) ) {
            return $smarty == "A" ? "0" : "0,00";
            exit;
        }

        // Descarta 0800, não tarifáveis
        if(substr(trim($destino),0,4) == "0800") {
            return $smarty == "A" ? "10" : "0,00";
            exit;
        }

        // Separa o numero do telefone em 3 partes: telefone , ddd, e ddi
        $num_dst = substr( $destino, -4 );
        $prefixo = substr( $destino, -8, 4 );

        $ddd_dst = "";

        if(strlen($destino) >= 10) {
            $ddd_dst = substr( $destino, -10, 2 );
        }

        if( $tn == 11 ) {
            $ddi_dst = "" ;
        }
        elseif( $tn > 13 ) {
            $ddi_dst = "" ;
        }
      
        if ($dbg==1) {
            $dst_fmtd = "(". $ddd_dst .") ". $prefixo ."-". $num_dst;
            $ret = "<hr>CCUSTOS=$ccusto == DATA=$dt_chamada == TEMPO=$duracao <br>DST = $dst_fmtd" ;
        }

        // Pesquisa cidades no CNL - Anatel
        $array_cidade = $this->fmt_cidade( array("a" => $destino),"A" );

        $cidade = $array_cidade['cidade'];

        if ( $array_cidade['flag'] == "S" ) {
            $nome_cidade = substr( $cidade, 0, strlen( $cidade ) -3 );         
        }else {
            $nome_cidade = "";
        }

        if ($dbg==1) {
            $ret .= " // CIDADE = $cidade( $nome_cidade )" ;
        }

        // Verifica se existe operadora vinculada ao Ccusto da ligação.
        $t = Snep_Operadoras::getOperadoraCcusto( $ccusto );
        $op = ( count( $t ) > 0 ? true : false );
        /*
              $t['codigo'];
              $t['tpm']   // Tempo do 1o. minuto da operadora - em seg
              $t['tdm']   // Tempo em segundos dos intervalos subsequentes
              $t['tbf']   // Valor Padrao para Fixo
              $t['tbc']   // Valor Padrao para Celular
              $t['vpf']   // Valor de partida para Fixo
              $t['vpc']   // Valor de partida para Celular.
        */
      
        if ($dbg == 1) {
            $ret .= " // OPERADORA={$t['codigo']} , TPM={$t['tpm']} , TDM={$t['tdm']} , TBF={$t['tbf']} , TBC={$r['tbc']}, VPC={$t['vpc']}, VPF={$t['vpf']}" ;
        }
        
        // Nao encontrou operadora ligada ao Centro de Custos da Chamda 
        if ( trim( $t['codigo'] ) === "" ) {
            return $smarty == "A" ? "0" : "0,00";
        }

        /* Pega dados das tarifas conforme requisitos da operadora, ddi , ddd e prefixo)
           Condicoes do cadastro de tarifas - ATENCAO: Diferentes cidades tem o mesmo DDD
           1) ddd valido + prefixo valido - Tarifa especial para o prefixo
           2) ddd valido + prefixo=0000   - Tarifa generica para os prefixo do ddd
           3) ddi valido + ddd=valido + prefixo=0000 - Tarifa para determinada regiao do pais
           4) ddi valido + ddd=0 + prefixo=0000 - Tarifa generica para o pais
        */

        // Verifica a existência de tarifas definidas para operadora
        $td = false;

        if( is_null( $nome_cidade ) ) {
            $cid = null;
        }else{
            $cid = $nome_cidade;
        }
      
        $td = Snep_Tarifas::getTarifaDisp($t['operadora'], $ddd_dst, strtoupper( trim( $cid ) ) );

        // Caso exista, verifica tarifas conforme data da ligação
        if( $td ) {
            array_push( $td, substr($dt_chamada, 0, 10) );
            $tr = Snep_Tarifas::getTarifaReaj($td);
            if( $tr ) {
                $t['tbf'] = $tr['vfix'];
                $t['tbc'] = $tr['vcel'];
                $t['vpf'] = $tr['vpf'];
                $t['vpc'] = $tr['vpc'];
            }          
        }

        if($dbg == 1) {
             $ret .= " # REAJUSTE #  TBF: {$t['tbf']}  TBC: {$t['tbc']} VPF: {$t['vpf']} VPC: {$t['vpc']} ";
             $ret .= " // COD_TARIFA=$cod_tarifa" ;
        }

        // Calcula o tempo do primeiro minuto e desconta o tempo restante
        $tp_fone = ( ( strlen( $destino ) >= 8 && substr( $prefixo, -4, 1) >= 6 ) ? "C" : "F" );

        if ($tp_fone == 'C') {
            $vp = $t['vpc'] ;   // Tarifa de Partida valida ara o tempo do primeiro minuto
            $tb = $t['tbc'] ;   // Tarifa para o restante dos tempo
        } else {
            $vp = $t['vpf'] ;   // Tarifa de Partida valida ara o tempo do primeiro minuto
            $tb = $t['tbf'];    // Tarifa para o restante dos tempo
        }

        if($dbg == 1) {
            $ret .= "<br /> [Dur Cadastro] [Arranque] {$t['tpm']} [Restante] {$t['tdm']} [T.Basica] {$tb} [V.Partida] {$vp} ";
            $ret .= "<br /> [Dur Chamada] {$duracao}  [Arranque] {$t_arq} [Restante] {$t_rst}  ";
        }
      
        // Calcula a tarifa
        $tarifa = Snep_Tarifas::calcula($duracao, $t['tpm'], $t['tdm'], $tb);

        if($dbg == 1) {
            echo $ret;
        }

        if ($smarty == "A")
            return $tarifa;
        else
            return number_format($tarifa,2,",","");
    }

} 
