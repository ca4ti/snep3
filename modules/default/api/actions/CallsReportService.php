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
 * Calls Report Service
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2015 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class CallsReportService implements SnepService {

    /**
     * Executa as ações do serviço
     */
    public function execute() {

        $config = Zend_Registry::get('config');
        $db = Zend_registry::get('db');

        $prefix_inout = $config->ambiente->prefix_inout;

        $start_date = $_GET['start_date'] . " " . $_GET['start_hour'];
        $end_date = $_GET['end_date'] . " " . $_GET['end_hour'];
        $report_type = $_GET['report_type'];

        if(isset($_GET['limit']))
            $limit = $_GET['limit'];

        // exceptions
        if(isset($_GET['exceptions'])){
            $exceptions = explode("_", $_GET['exceptions']);

            foreach($exceptions as $key => $value){
                (isset($exceptionsAll)) ? $exceptionsAll .= "'".$value."'," : $exceptionsAll = "'".$value."',";
            }
            $exceptions = substr($exceptionsAll, 0,-1);
            //$where_exceptions = " AND (src IN (".$exceptions.") OR dst IN (".$exceptions."))";

        }

        // Binds
        if(isset($_GET['clausulepeer']) && isset($_GET['clausule'])){

            $clausulepeer = explode("_", $_GET['clausulepeer']);
            $where_binds = '';

            foreach( $clausulepeer as $key => $value){
                $where_binds .= $value.",";
            }
            $where_binds = substr($where_binds, 0,-1);

            // Not permission
            if($_GET['clausule'] == 'nobound'){

                if(isset($exceptions)){
                    $where_binds = " AND (src IN (".$exceptions.") OR dst IN (".$exceptions."))"." AND (src NOT IN (".$where_binds.") OR dst NOT IN (".$where_binds."))";
                }else{
                    $where_binds = " AND (src NOT IN (".$where_binds.") OR dst NOT IN (".$where_binds."))";
                }

            }else{

                if(isset($exceptions)){
                    $where_binds = " AND (src IN (".$where_binds.",".$exceptions.") OR dst IN (".$where_binds.",".$exceptions."))";
                }else{
                    $where_binds = " AND (src IN (".$where_binds.") OR dst IN (".$where_binds."))";
                }
            }

        }

        // when no exits bind and exsts only exception special
        if(!isset($where_binds) && isset($exceptions)){
            $where_binds = " AND (src IN (".$exceptions.") OR dst IN (".$exceptions."))";
        }

        // Status call
        $where_options[0] = " disposition != 'ANSWERED'";
        $where_options[1] = " disposition != 'NO ANSWER'";
        $where_options[2] = " disposition != 'BUSY'";
        $where_options[3] = " disposition != 'FAILED'";

        if(isset($_GET['status_answered']))
            unset($where_options[0]);

        if(isset($_GET['status_noanswer']))
            unset($where_options[1]);

        if(isset($_GET['status_busy']))
            unset($where_options[2]);

        if(isset($_GET['status_failed']))
            unset($where_options[3]);


        /* Busca os ramais pertencentes ao grupo de ramal de origem selecionado */
        $ramaissrc = $ramaisdst = "";
        if (isset($_GET['groupsrc'])) {

            $groupsrc = $_GET['groupsrc'];
            $origens = Snep_ExtensionsGroups_Manager::getExtensionsGroup($groupsrc);

            if (count($origens) == 0) {
                return array("status" => "fail", "message" => "errorgroup");
            } else {
                $ramalsrc = "";

                foreach ($origens as $key => $ramal) {
                    $num = $ramal['name'];
                    if (is_numeric($num)) {
                        $ramalsrc .= $num . ',';
                    }
                }
                $ramaissrc = " AND src in (" . trim($ramalsrc, ',') . ") ";
            }
        }

        if (isset($_GET['groupdst'])) {

            $groupdst = $_GET['groupdst'];
            $destino = Snep_ExtensionsGroups_Manager::getExtensionsGroup($groupdst);

            if (count($destino) == 0) {
                return array("status" => "fail", "message" => "There are no extensions in the selected group");
            } else {
                $ramaldst = "";

                foreach ($destino as $key => $ramal) {
                    $num = $ramal['name'];
                    if (is_numeric($num)) {
                        $ramaldst .= $num . ',';
                    }
                }
                $ramaisdst = " AND dst in (" . trim($ramaldst, ',') . ") ";
            }
        }

        // Src or dst value
        if (isset($_GET['src'])) {
            $src = $_GET['src'];
            if (strpos($src, ",")) {
                $where_src = '';
                $arrSrc = explode(",", $src);

                foreach ($arrSrc as $srcs) {
                    ($_GET['order_src'] == 'equal') ? $where_src .= " OR (src = $srcs)" : $where_src .= " OR (src LIKE '%$srcs%')";
                }

                $where_src = " AND (" . substr($where_src, 3) . ")";

            } else {
                ($_GET['order_src'] == 'equal') ? $where_src = "AND (src = $src)" : $where_src = "AND (src LIKE '%$src%')";
            }
        }

        if (isset($_GET['dst'])) {
            $dst = $_GET['dst'];
            if (strpos($dst, ",")) {
                $where_dst = '';
                $arrdst = explode(",", $dst);

                foreach ($arrdst as $dsts) {
                    ($_GET['order_dst'] == 'equal') ? $where_dst .= " OR (dst = $dsts)" : $where_dst .= " OR (dst LIKE '%$dsts%')";
                }

                $where_dst = " AND (" . substr($where_dst, 3) . ")";

            } else {
                ($_GET['order_dst'] == 'equal') ? $where_dst = "AND (dst = $dst)" : $where_dst = "AND (dst LIKE '%$dst%')";
            }
        }

        // Time call option
        (isset($_GET['time_call_init'])) ? $where_options[] = ' duration >= '.$_GET['time_call_init'].' ' : null ;
        (isset($_GET['time_call_end'])) ? $where_options[] = ' duration <= '.$_GET['time_call_end'].' ' : null ;


        // Cost center
        if(isset($_GET['cost_center'])){
            $where_cost_center = "";
            $cost_centers = explode("_", $_GET['cost_center']);

            if (count($cost_centers) > 0) {
                $tmp_cc = "";
                foreach ($cost_centers as $valor) {
                    $tmp_cc .= " cdr.accountcode = '" . $valor . "'";
                    $tmp_cc .= " OR ";
                }
                $cost_centers = implode(",", $cost_centers);
                if($tmp_cc != "")
                    $where_cost_center.= " AND ( " . substr($tmp_cc, 0, strlen($tmp_cc) - 3) . " ) ";
            }else{
                $where_cost_center = " cdr.accountcode = ".$cost_centers;
            }
        }

        /* Where Prefix Login/Logout */
        $where_prefix = "";
        if (strlen($prefix_inout) > 3) {
            $cond_pio = "";
            $array_prefixo = explode(";", $prefix_inout);
            foreach ($array_prefixo as $valor) {
                $par = explode("/", $valor);
                $pio_in = $par[0];
                if (!empty($par[1])) {
                    $pio_out = $par[1];
                }
                $t_pio_in = strlen($pio_in);
                $t_pio_out = strlen($pio_out);
                $cond_pio .= " substr(dst,1,$t_pio_in) != '$pio_in' ";
                if (!$pio_out == '') {
                    $cond_pio .= " AND substr(dst,1,$t_pio_out) != '$pio_out' ";
                }
                $cond_pio .= " AND ";
            }
            if ($cond_pio != "")
                $where_prefix .= " AND ( " . substr($cond_pio, 0, strlen($cond_pio) - 4) . " ) ";
        }
        $where_prefix .= " AND ( locate('ZOMBIE',channel) = 0 ) ";

        // Locale call
        $locale_call = null;
        (isset($_GET['locale_call'])) ? $locale_call = true : null ;

        if($where_options){
            $where = "";
            foreach($where_options as $key => $option){
                $where .= ' AND ('.$option.') ';
            }
        }

        if($report_type != 'synthetic'){

            // SQL
            $select = "SELECT ccustos.codigo,ccustos.tipo,ccustos.nome, date_format(calldate,'%d/%m/%Y') AS key_dia, date_format(calldate,'%d/%m/%Y %H:%i:%s') AS dia, ";
            $select .= "src, dst, disposition, duration, billsec, accountcode, userfield, dcontext, amaflags, uniqueid, calldate, dstchannel FROM cdr, ccustos ";
            $select .= " WHERE ( calldate >= '$start_date' AND calldate <= '$end_date' AND ccustos.codigo = accountcode) ";
            $select .= (isset($where_cost_center)) ? $where_cost_center : '';
            $select .= (isset($where)) ? $where : '';
            $select .= (isset($where_src)) ? $where_src : '';
            $select .= (isset($where_dst)) ? $where_dst : '';
            $select .= (isset($ramaissrc)) ? $ramaissrc : '';
            $select .= (isset($ramaisdst)) ? $ramaisdst : '';
            $select .= (isset($where_binds)) ? $where_binds : '';
            $select .= $where_prefix;
            $select .= " GROUP BY userfield ORDER BY calldate, userfield ";
            $select .= (isset($limit)) ? " LIMIT ".$limit : '';

            $stmt = $db->query($select);

            while ($dado = $stmt->fetch()) {

                $row[] = $dado;

            }

        }

        $selectcont = "SELECT count(*) as cont,disposition,accountcode,date_format(calldate,'%d/%m/%Y') AS key_dia, ccustos.tipo  FROM cdr, ccustos ";
        $selectcont .= " WHERE ( calldate >= '$start_date' AND calldate <= '$end_date' AND ccustos.codigo = accountcode) ";
        $selectcont .= (isset($where_cost_center)) ? $where_cost_center : '';
        $selectcont .= (isset($where)) ? $where : '';
        $selectcont .= (isset($where_src)) ? $where_src : '';
        $selectcont .= (isset($where_dst)) ? $where_dst : '';
        $selectcont .= (isset($ramaissrc)) ? $ramaissrc : '';
        $selectcont .= (isset($ramaisdst)) ? $ramaisdst : '';
        $selectcont .= (isset($where_binds)) ? $where_binds : '';
        $selectcont .= $where_prefix;
        $selectcont .= " GROUP BY userfield ORDER BY calldate, userfield";

        $result = $db->query($selectcont)->fetchAll();
        $cont = count($result);

        //Totals
        $totals = array("ANSWERED" => 0, "NOANSWER" => 0, "BUSY" => 0, "FAILED" => 0, "TOTALS" => 0);
        $type = array("S" => 0, "E" => 0, "O" => 0);
        $values = array();
        $ccustos = array();
        $calldate = array();

        foreach($result as $key => $value){

            // Calls number
            if($value['disposition'] == 'ANSWERED'){
                $totals['TOTALS']++;
                $totals['ANSWERED']++;

            }elseif($value['disposition'] == 'NO ANSWER'){
                $totals['TOTALS']++;
                $totals['NOANSWER']++;

            }elseif($value['disposition'] == 'BUSY'){
                $totals['TOTALS']++;
                $totals['BUSY']++;


            }elseif($value['disposition'] == 'FAILED'){
                $totals['TOTALS']++;
                $totals['FAILED']++;
            }

            // // Synthetic
            if($report_type == 'synthetic'){

                (isset($ccustos[$value['accountcode']])) ? $ccustos[$value['accountcode']]++ : $ccustos[$value['accountcode']] = 1;

                if($value['disposition'] == 'ANSWERED'){
                    (isset($calldate[$value['key_dia']]['ANSWERED'])) ? $calldate[$value['key_dia']]['ANSWERED']++ : $calldate[$value['key_dia']]['ANSWERED'] = 0;
                }

                if($value['disposition'] == 'NO ANSWER'){
                    (isset($calldate[$value['key_dia']]['NOANSWER'])) ? $calldate[$value['key_dia']]['NOANSWER']++ : $calldate[$value['key_dia']]['NOANSWER'] = 0;
                }

                if($value['disposition'] == 'BUSY'){
                    (isset($calldate[$value['key_dia']]['BUSY'])) ? $calldate[$value['key_dia']]['BUSY']++ : $calldate[$value['key_dia']]['BUSY'] = 0;
                }

                if($value['disposition'] == 'FAILED'){
                    (isset($calldate[$value['key_dia']]['FAILED'])) ? $calldate[$value['key_dia']]['FAILED']++ : $calldate[$value['key_dia']]['FAILED'] = 0;
                }

                (isset($calldate[$value['key_dia']]['TOTALS'])) ? $calldate[$value['key_dia']]['TOTALS']++ : $calldate[$value['key_dia']]['TOTALS'] = 0;

                if($value['tipo'] == 'S'){
                    $type['S']++;
                }elseif($value['tipo'] == 'E'){
                    $type['E']++;
                }else{
                    $type['O']++;
                }

            }

        }

        if($report_type == 'synthetic'){
            return array("status" => "ok", "quantity" => $cont, "totals" => $totals, "ccustos" => $ccustos, "type" => $type, "calldate" => $calldate);
        }else{
            return array("status" => "ok", "data" => $row, "quantity" => $cont, "totals" => $totals, "select" => $select, "selectcont" => $selectcont);
        }
    }

}
