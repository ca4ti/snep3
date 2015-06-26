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
 * Classe functions contém um agrupamento de várias funções utilizadas no Snep 
 *
 * @category  Snep
 * @package   includes_functions
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 * @author Opens Tecnologia
 */
// Inclue Classes do Sistema 
require_once("classes.php");

/**
 * sql_like - Função para criar string sql para busca de ramais
 * @author - Rafael Bozzetti <rafael@opens.com.br> 
 * @param <String> $type - Identifica o tipo de comparação (1,2,3,4)
 * @param <String> $data - que identifique o numero do ramal
 * @param <String> $id - especifica se é 'dst', 'src' ou ambos ''(vazio) 
 * @return <String>
 */
function sql_like($type, $data, $id) {
    $retorno = '';

    switch ($type) {
        case 1:
            $retorno .= ($id == 'src' ? " or src = '$data' " : " or dst = '$data' ");
            break;
        case 2:
            $retorno .= ($id == 'src' ? " or src LIKE '$data%' " : " or dst LIKE '$data%' ");
            break;
        case 3:
            $retorno .= ($id == 'src' ? " or src LIKE '%$data' " : " or dst LIKE '%$data' ");
            break;
        case 4:
            $retorno .= ($id == 'src' ? " or src LIKE '%$data%' " : " or dst LIKE '%$data%' ");
            break;
    }
    return $retorno;
}

/**
 * sql_vinc - Reformulação da função de sql_vinculos()
 * @author - Rafael Bozzetti <rafael@opens.com.br>
 * @param <String> $src - identifica o tipo de comparação (1,2,3,4)
 * @param <String> $dst - que identifique o numero do ramal
 * @param <String> $srctype - especifica se é 'dst' ou 'src'
 * @param <String> $dsttype - 'src', 'dst', '' = ambos
 * @param <String> $base
 * @return <String>
 */
function sql_vinc($src, $dst, $srctype, $dsttype, $base = "") {


    // Quando o ramal não possue vinculos (Acesso geral) //
    if (trim($_SESSION['vinculos_user']) == "") {

        // Tratamento das origens especificadas
        if (strlen($src) > 0 && ($base == 'src' || $base == "")) {
            $array_src = explode(",", trim($src));

            if (count($array_src) > 0) {

                foreach ($array_src as $valor) {
                    $TMP_COND .= sql_like($srctype, $valor, 'src');
                }

                if (strlen($TMP_COND) > 0) {
                    $retorno = " AND  " . substr($TMP_COND, 4) . " ";
                }
            }
        }

        unset($TMP_COND);

        // Tratamento dos destinos especificados
        if (strlen($dst) > 0 && ($base == 'dst' || $base == "")) {
            $array_dst = explode(",", trim($dst));

            if (count($array_dst) > 0) {

                foreach ($array_dst as $valor) {
                    $TMP_COND .= sql_like($dsttype, $valor, 'dst');
                }

                if (strlen($TMP_COND) > 0) {
                    $retorno .= " AND  " . substr($TMP_COND, 4) . " ";
                }
            }
        }
    }
    // Quando possuem vinculos, seja ele mesmo ou de outros ramais //
    else {

        // Verifica se ramal e vinculo são iguais, sendo assim, restrito aos seus dados.
        if ($_SESSION['vinculos_user'] == $_SESSION['name_user']) {
            if ($base == "") {
                $retorno = " AND ( src='{$_SESSION['name_user']}' || dst='{$_SESSION['name_user']}' ) ";
            }
            if ($base == 'src') {
                $retorno = " AND ( src='{$_SESSION['name_user']}' ) ";
            }
            if ($base == 'dst') {
                $retorno = " AND ( dst='{$_SESSION['name_user']}' ) ";
            }
        }

        // Caso os vínculos sejam mais de 1 ou diferentes do ramal
        else {

            // Cria um array com os vinculos do usuário, para comparação
            $vinculados = explode(",", $_SESSION['vinculos_user']);

            $control = false;
            unset($TMP_COND);

            // Percorre origens especificadas e verifica se pertence aos indices
            if (strlen($src) >= 1 && ($base == 'src' || $base == "")) {
                $array_src = explode(",", trim($src));

                if (count($array_src) > 0) {

                    foreach ($array_src as $valor) {

                        if (in_array($valor, $vinculados)) {
                            $TMP_COND .= sql_like($srctype, $valor, 'src');
                        }
                    }
                    if (strlen($TMP_COND) > 0) {
                        $retorno .= $TMP_COND; //" AND ( ". substr( $TMP_COND, 4 ) ." )";
                    }
                }
            } else {
                foreach ($vinculados as $valor) {
                    $TMP_COND .= sql_like($srctype, $valor, 'src');
                }
                if (strlen($TMP_COND) > 0) {
                    $retorno .= $TMP_COND; //" AND ( ". substr( $TMP_COND, 4 ) ." )";
                }
                $controle = true;
            }

            unset($TMP_COND);

            // Percorre origens especificadas e verica se pertence aos indices
            if (strlen($dst) >= 1 && ($base == 'dst' || $base == "" )) {
                $array_dst = explode(",", trim($dst));

                if (count($array_dst) > 0) {

                    foreach ($array_dst as $valor) {

                        if (in_array($valor, $vinculados)) {
                            $TMP_COND .= sql_like($dsttype, $valor, 'dst');
                        }
                    }
                    if (strlen($TMP_COND) > 0) {
                        $retorno .= $TMP_COND; //" AND ( ". substr( $TMP_COND, 4 ) ." )";
                    }
                }
                //$controle = true;
            } else {
                if ($controle) {
                    foreach ($vinculados as $valor) {
                        $TMP_COND .= sql_like($srctype, $valor, 'dst');
                    }
                    if (strlen($TMP_COND) > 0) {
                        $retorno .= $TMP_COND; //" AND ( ". substr( $TMP_COND, 4 ) ." )";
                        $retorno .= $TMP_COND;
                    }
                }
            }
        }
    }
    $retorno = ( $retorno != "" ? "AND ( " . substr($retorno, 4) . " )" : "");

    return $retorno;
}

/**
 * Funcao para montar clausula where dos outros campos 
 * @global type $fld
 * @global type $fldtype
 * @param <String> $sql - variavel que contem o comando sql que esta sendo montado
 * @param <String> $fld - variavel com o conteudo fornecido pelo usuario
 * @param <int> $fldtype - tipo da comparacao (1-igual,2-inicia,3-termina,4=Contem)
 * @param <string> $nmfld - nome do campo a ser comparado no Banco de dados
 * @param <string> $tpcomp - Tipo de comparacao (AND ou OR)- default = AND
 * @return <String> 
 */
function do_field($sql, $fld, $fldtype, $nmfld = "", $tpcomp = "AND") {
    global $$fld, $$fldtype;
    if (isset($$fld) && ($$fld != '')) {
        if (strpos($sql, 'WHERE') > 0) {
            $sql = "$sql $tpcomp ";
        } else {
            $sql = "$sql WHERE ";
        }
        if ($nmfld == "") {
            $sql = "$sql $fld";
        } else {
            $sql = "$sql $nmfld";
        }
        if (isset($$fldtype)) {
            switch ($$fldtype) {
                case 1:
                    $sql = "$sql='" . $$fld . "'";
                    break;
                case 2:
                    $sql = "$sql LIKE '" . $$fld . "%'";
                    break;
                case 3:
                    $sql = "$sql LIKE '%" . $$fld . "'";
                    break;
                case 4:
                    $sql = "$sql LIKE '%" . $$fld . "%'";
                    break;
            }
        } else {
            $sql = "$sql LIKE '%" . $$fld . "%'";
        }
    }
    return $sql;
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
 * Executa programa do S.O.
 * @param <String> $program - Programa a ser executado
 * @param <array> $params - Parametros/argumentos
 * @return <String> 
 */
function execute_program($program, $params) {
    $path = array('/bin/', '/sbin/', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
    $buffer = '';
    while ($cur_path = current($path)) {
        if (is_executable("$cur_path/$program")) {
            if ($fp = popen("$cur_path/$program $params", 'r')) {
                while (!feof($fp)) {
                    $buffer .= fgets($fp, 4096);
                }
                return trim($buffer);
            }
        }
        next($path);
    }
}

/**
 * Executa comandos do S.O. Linux
 * @param <String> $cmd - Comando a ser executado
 * @param <String> $msg - Mensagem a ser mostrada
 * @param <boolean> $ret
 * @return <boolean> 
 */
function executacmd($cmd, $msg, $ret = False) {
    $result = exec("$cmd 2>&1", $out, $err);
    if ($err) {
        if ($msg != "")
            display_error($msg . " => " . $err, true);
        return FALSE;
    } else
    if ($ret)
        return $out;
    else
        return TRUE;
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

/**
 * Função de geração do arquivo CSV baseados nos relatórios
 * @author: Rafael Bozzetti <rafael@opens.com.br>
 * @param <Array> $arr_titulo - Array de criação do CSV, que determina os indices que deverão ser colocados no CSV
 * @param <Array> $arr_dados - Array de resultado da query $row
 * @return type
 */
function monta_csv($arr_titulo, $arr_dados) {

    /* Recebe os indices que foram declarados no array $titulo  */
    $indices = array_keys($arr_titulo);

    /* Monta o cabeçalho conforme o value declarado no array  */
    $titulos = implode(";", $arr_titulo);
    $dados_csv = '';
    $dad_csv = '';
    $formatter = new Formata();

    foreach ($arr_dados as $chave => $dados_ori) {

        /* Foreach que percorre o array principal ( $row )
         * e formata cada campo presente dele.
         */
        $dados = $dados_ori;

        if (isset($dados['duration'])) {
            $dados['duration'] = $formatter->fmt_segundos(array("a" => $dados_ori['duration'], "b" => 'hms', "A"));
        }
        if (isset($dados['billsec'])) {
            $dados['billsec'] = $formatter->fmt_segundos(array("a" => $dados_ori['billsec'], "b" => 'hms', "A"));
        }
        if (isset($dados['src'])) {
            $dados['src'] = $formatter->fmt_telefone(array("a" => $dados_ori['src']));
        }
        if (isset($dados['dst'])) {
            $dados['dst'] = $formatter->fmt_telefone(array("a" => $dados_ori['dst']));
        }
        if (isset($dados['par2'])) {
            $dados['dst'] = $formatter->fmt_telefone(array("a" => $dados_ori['dst']));
        }
        if (array_key_exists("tarifacao", $arr_titulo)) {
            if ($dados_ori['disposition'] == "ANSWERED") {
                $dados['tarifacao'] = $formatter->fmt_tarifa(array("a" => $dados_ori['dst'], "b" => $dados_ori['billsec'], "c" => $dados_ori['accountcode'], "d" => $dados_ori['calldate'], "e" => $dados_ori['tipo']));
            } else {
                $dados['tarifacao'] = "0,00";
            }
        }

        if ($dados['disposition']) {
            if ($dados['disposition'] == "ANSWERED") {
                $dados['disposition'] = "Atendida";
            }
            if ($dados['disposition'] == 'NO ANSWER') {
                $dados['disposition'] = "Não Atendida";
            }
            if ($dados['disposition'] == 'BUSY') {
                $dados['disposition'] = "Ocupada";
            }
        }
        if (isset($dados['dst'])) {
            $dados['origem'] = $formatter->fmt_cidade(array("a" => $dados_ori['dst']));
        }

        /* Tratamento das Estatísticas do Operador */

        if (isset($dados['otp_cha'])) {
            $dados['otp_cha'] = $formatter->fmt_segundos(array("a" => $dados_ori['otp_cha'], "b" => 'hms', "A"));
        }
        if (isset($dados['otp_ate'])) {
            $dados['otp_ate'] = $formatter->fmt_segundos(array("a" => $dados_ori['otp_ate'], "b" => 'hms', "A"));
        }
        if (isset($dados['otp_esp'])) {
            $dados['otp_esp'] = $formatter->fmt_segundos(array("a" => $dados_ori['otp_esp'], "b" => 'hms', "A"));
        }
        if (isset($dados['omd_cha'])) {
            $dados['omd_cha'] = $formatter->fmt_segundos(array("a" => $dados_ori['omd_cha'], "b" => 'hms', "A"));
        }
        if (isset($dados['omd_ate'])) {
            $dados['omd_ate'] = $formatter->fmt_segundos(array("a" => $dados_ori['omd_ate'], "b" => 'hms', "A"));
        }
        if (isset($dados['omd_esp'])) {
            $dados['omd_esp'] = $formatter->fmt_segundos(array("a" => $dados_ori['omd_esp'], "b" => 'hms', "A"));
        }
        if (isset($dados['rtp_cha'])) {
            $dados['rtp_cha'] = $formatter->fmt_segundos(array("a" => $dados_ori['rtp_cha'], "b" => 'hms', "A"));
        }
        if (isset($dados['rtp_ate'])) {
            $dados['rtp_ate'] = $formatter->fmt_segundos(array("a" => $dados_ori['rtp_ate'], "b" => 'hms', "A"));
        }
        if (isset($dados['rtp_esp'])) {
            $dados['rtp_esp'] = $formatter->fmt_segundos(array("a" => $dados_ori['rtp_esp'], "b" => 'hms', "A"));
        }
        if (isset($dados['rmd_cha'])) {
            $dados['rmd_cha'] = $formatter->fmt_segundos(array("a" => $dados_ori['rmd_cha'], "b" => 'hms', "A"));
        }
        if (isset($dados['rmd_ate'])) {
            $dados['rmd_ate'] = $formatter->fmt_segundos(array("a" => $dados_ori['rmd_ate'], "b" => 'hms', "A"));
        }
        if (isset($dados['rmd_esp'])) {
            $dados['rmd_esp'] = $formatter->fmt_segundos(array("a" => $dados_ori['rmd_esp'], "b" => 'hms', "A"));
        }
        if (isset($dados['tml'])) {
            $dados['tml'] = $formatter->fmt_segundos(array("a" => $dados_ori['tml'], "b" => 'hms', "A"));
        }
        if (isset($dados['tma'])) {
            $dados['tma'] = $formatter->fmt_segundos(array("a" => $dados_ori['tma'], "b" => 'hms', "A"));
        }
        if (isset($dados['tmef'])) {
            $dados['tmef'] = $formatter->fmt_segundos(array("a" => $dados_ori['tmef'], "b" => 'hms', "A"));
        }
        if (isset($dados['TA'])) {
            $dados['TA'] = $formatter->fmt_segundos(array("a" => $dados_ori['TA'], "b" => 'hms', "A"));
        }
        if (isset($dados['TN'])) {
            $dados['TN'] = $formatter->fmt_segundos(array("a" => $dados_ori['TN'], "b" => 'hms', "A"));
        }

        /* Este foreach percorre cada um dos arrays internos de $row e guarda o que
         * foi setado para ser exibido no array de criação.
         */
        foreach ($indices as $key => $ind) {

            $dad_csv .= $dados[$ind] . ";";
        }

        /* Adiciona quebra de linha */
        $dad_csv .= "\n";
        $dados_csv = $dad_csv;
    }

    /* Concatena Titulo e Dados em uma string */
    $titulo = $titulos . "\n";
    $titulo .= $dados_csv;

    /* Gera arquivo */
    $dataarq = date("d-m-Y_hm");
    $arquivo_csv = "../templates_c/csv$dataarq.csv";

    $fp = fopen($arquivo_csv, "w+");
    fputs($fp, $titulo);
    fclose($fp);

    return $arquivo_csv;
}

/**
 * Le informações no banco e gera arquivo de configurção dos peers
 * Esta função é chamada sempre no final de cada alteracao com os ramais e troncos
 * ela gera um novo .conf com as informacoes do banco
 * @global type $db
 * @global type $LANG
 * @global type $salas
 * @global type $conf_app
 * @global type $ccustos
 * @return boolean
 */
function grava_conf() {
    global $db, $LANG, $salas, $conf_app, $ccustos;

    foreach (array("sip", "iax2") as $tech) {
        $config = Zend_Registry::get('config');
        $asterisk_directory = $config->system->path->asterisk->conf;

        $file_conf = "$asterisk_directory/snep/snep-$tech.conf";
        $trunk_file_conf = "$asterisk_directory/snep/snep-$tech-trunks.conf";

        if (!is_writable($file_conf)) {
            display_error($LANG['msg_incoming_file_error'] . $file_conf, true);
            return False;
        }
        if (!is_writable($trunk_file_conf)) {
            display_error($LANG['msg_incoming_file_error'] . $trunk_file_conf, true);
            return False;
        }
        /* Apaga arquivo snep-sip.conf */
        file_put_contents($file_conf, '');

        /* Registro de Cabeçalho */
        $data_atual = date("d/m/Y H:m:s");
        $header = ";------------------------------------------------------------------------------------\n";
        $header .= "; Arquivo: snep-$tech.conf - Cadastro de ramais                                        \n";
        $header .= ";                                                                                    \n";
        $header .= "; Atualizado em: $data_atual                                                         \n";
        $header .= "; Copyright(c) 2008 Opens Tecnologia                                                 \n";
        $header .= ";------------------------------------------------------------------------------------\n";
        $header .= "; Os registros a Seguir sao gerados pelo Software SNEP.                              \n";
        $header .= "; Este Arquivo NAO DEVE ser editado Manualmente sob riscos de                        \n";
        $header .= "; causar mau funcionamento do Asterisk                                               \n";
        $header .= ";------------------------------------------------------------------------------------\n";

        /* Pega informações de ramais no banco */
        $sql = "SELECT * FROM peers WHERE name != 'admin' AND canal like '" . strtoupper($tech) . "%'";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $atual = $stmt->rowCount();
        } catch (Exception $e) {
            display_error($LANG['error'] . $e->getMessage(), true);
        }
        /* Percorre retorno e */
        $peers = "\n";
        $trunk = "\n";

        if ($atual > 0) {
            $database = Zend_Registry::get('db');
            foreach ($stmt->fetchAll() as $peer) {
                /* Organiza os codecs Allowed */
                $sipallow = explode(";", $peer['allow']);
                $allow = '';
                foreach ($sipallow as $siper) {
                    if ($siper != '') {
                        $allow .= $siper . ",";
                    }
                }
                $allow = substr($allow, 0, strlen($allow) - 1);

                if ($peer['peer_type'] == 'T') {

                    $select = $database->select()->from('trunks')->where("name = {$peer['name']}");
                    unset($stmt);
                    $stmt = $database->query($select);
                    $tronco = $stmt->fetchObject();

                    if ($tronco->type == "SNEPSIP") {
                        /* Monta entrada do tronco */
                        $peers .= '[' . $peer['username'] . "]\n";
                        $peers .= 'type=' . $peer['type'] . "\n";
                        $peers .= 'context=' . $peer['context'] . "\n";
                        $peers .= 'canreinvite=' . $peer['canreinvite'] . "\n";
                        $peers .= 'dtmfmode=' . ($peer['dtmfmode'] ? $peer['dtmfmode'] : "rfc2833") . "\n";
                        $peers .= 'host=' . $peer['host'] . "\n";
                        $peers .= 'qualify=' . ($peer['qualify'] == "no" ? "no" : "yes") . "\n";
                        $peers .= 'nat=' . $peer['nat'] . "\n";
                        $peers .= 'disallow=' . $peer['disallow'] . "\n";
                        $peers .= 'allow=' . $allow . "\n";
                        $peers .= "\n";
                    } else if ($tronco->type == "SNEPIAX2") {
                        /* Monta entrada do tronco */
                        $peers .= '[' . $peer['username'] . "]\n";
                        $peers .= 'type=' . $peer['type'] . "\n";
                        $peers .= 'username=' . $peer['username'] . "\n";
                        $peers .= 'secret=' . $peer['username'] . "\n";
                        $peers .= 'context=' . $peer['context'] . "\n";
                        $peers .= 'canreinvite=' . $peer['canreinvite'] . "\n";
                        $peers .= 'dtmfmode=' . ($peer['dtmfmode'] ? $peer['dtmfmode'] : "rfc2833") . "\n";
                        $peers .= 'host=' . $peer['host'] . "\n";
                        $peers .= 'qualify=' . ($peer['qualify'] == "no" ? "no" : "yes") . "\n";
                        $peers .= 'nat=' . $peer['nat'] . "\n";
                        $peers .= 'disallow=' . $peer['disallow'] . "\n";
                        $peers .= 'allow=' . $allow . "\n";
                        $peers .= "\n";
                    } else if ($tronco->dialmethod != "NOAUTH") {
                        /* Monta entrada do tronco */
                        $peers .= '[' . $peer['username'] . "]\n";
                        $peers .= 'type=' . $peer['type'] . "\n";
                        $peers .= 'context=' . $peer['context'] . "\n";
                        $peers .= ($peer['fromdomain'] != "") ? ('fromdomain=' . $peer['fromdomain'] . "\n") : "";
                        $peers .= ($peer['fromuser'] != "") ? ('fromuser=' . $peer['fromuser'] . "\n") : "";
                        $peers .= 'canreinvite=' . $peer['canreinvite'] . "\n";
                        $peers .= 'dtmfmode=' . ($peer['dtmfmode'] ? $peer['dtmfmode'] : "rfc2833") . "\n";
                        $peers .= 'host=' . $peer['host'] . "\n";
                        $peers .= 'qualify=' . $peer['qualify'] . "\n";
                        $peers .= 'nat=' . $peer['nat'] . "\n";
                        $peers .= 'disallow=' . $peer['disallow'] . "\n";
                        $peers .= 'allow=' . $allow . "\n";

                        if ($peer['port'] != "") {
                            $peers .= 'port=' . $peer['port'] . "\n";
                        }
                        if ($peer['call-limit'] != "" && $tronco->type == "SIP") {
                            $peers .= 'call-limit=' . $peer['call-limit'] . "\n";
                        }
                        if ($tronco->insecure != "") {
                            $peers .= 'insecure=' . $tronco->insecure . "\n";
                        }
                        if ($tronco->domain != "" && $tronco->type == "SIP") {
                            $peers .= 'domain=' . $tronco->domain . "\n";
                        }
                        if ($tronco->type == "IAX2") {
                            $peers .= 'trunk=' . $peer['trunk'] . "\n";
                        }
                        if ($tronco->reverse_auth) {
                            $peers .= 'username=' . $peer['username'] . "\n";
                            $peers .= 'secret=' . $peer['secret'] . "\n";
                        }
                        $peers .= "\n";
                    }
                    $trunk .= ($tronco->dialmethod != "NOAUTH" && !preg_match("/SNEP/", $tronco->type) ? "register => " . $peer['username'] . ":" . $peer['secret'] . "@" . $peer['host'] . "\n" : "");
                } else {
                    /* Monta entrada do ramal */
                    $peers .= '[' . $peer['name'] . "]\n";
                    $peers .= 'type=' . $peer['type'] . "\n";
                    $peers .= 'context=' . $peer['context'] . "\n";
                    $peers .= 'host=' . $peer['host'] . "\n"; # dinamyc
                    $peers .= 'secret=' . $peer['secret'] . "\n";
                    $peers .= 'callerid=' . $peer['callerid'] . "\n";
                    $peers .= 'canreinvite=' . $peer['canreinvite'] . "\n";
                    $peers .= 'dtmfmode=' . ($peer['dtmfmode'] ? $peer['dtmfmode'] : "rfc2833") . "\n";
                    $peers .= 'nat=' . $peer['nat'] . "\n";
                    $peers .= 'qualify=' . $peer['qualify'] . "\n";
                    $peers .= 'disallow=' . $peer['disallow'] . "\n";
                    $peers .= 'allow=' . $allow . "\n";

                    /*
                     * Envia informações de usuário a outra ponta. Faz
                     * com que um asterisk possa receber ligações deste.
                     */
                    $peers .= 'username=' . $peer['name'] . "\n";

                    /*
                     * Faz com que as ligações vindas desse canal SIP
                     * tenham o seu callerid forçado para o numero do
                     * ramal. Impede falsidade ideológica entre ramais.
                     */
                    //   $peers .= 'fromuser='.$peer['name']."\n";

                    /*
                     * Limita ligações simultaneas. Impedindo que o ramal
                     * SIP receba mais de uma ligação ao mesmo tempo.
                     *
                     * Esta opção afeta SIP Transfer que requer 2 canais.
                     * Problemas com alguns softphones e telefones IP.
                     */
                    $peers .= 'call-limit=' . $peer['call-limit'] . "\n";

                    $peers .= "\n";
                }
            }
            unset($database);
        }

        $trunkcont = str_replace(".conf", "-trunks.conf", $header) . $trunk;
        file_put_contents($trunk_file_conf, $trunkcont);

        /* Concatena Header do arquivo com conteudo e grava no arquivo. */
        $content = $header . $peers;

        file_put_contents($file_conf, $content);
    }
    // For�ando o asterisk a ler os arquivos
    ast_status("sip reload", "");
    ast_status("iax2 reload", "");
}

/**
 * Função para pegar a hora para DEBUG
 * @return <String>
 */
function utime() {
    $time = explode(" ", microtime());
    $usec = (double) $time[0];
    $sec = (double) $time[1];
    return $sec + $usec;
}

/**
 * Esta função é responsável por verificar e retornar o
 * nível de acesso que o usuários terá no sistema.
 * @author: Rafael Bozzetti <rafael@opens.com.br>
 * @param <String> $vinculados - vinculos armazenados na session vinculos_user
 * @param <String> $user - usuário autenticado
 * @return <int>
 */
function monta_nivel($vinculos, $user) {

    if (trim($vinculos) == "" || $user == "admin") {
        $retorno = 1;
    } elseif ($vinculos == $user) {
        $retorno = 2;
    } else {
        $retorno = 3;
    }
    return $retorno;
}
