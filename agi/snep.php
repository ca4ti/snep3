#!/usr/bin/php-cgi -q
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
 *
 * AGI executable that makes the function calls in the Asterisk dialplan. 
 *
 * This application starts the environment for the Snep library can work in routing connections.
 *
 */
// Processes the signals coming from asterisk
declare(ticks = 1);
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGHUP, SIG_IGN);
}

// Controll errors display
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

require_once "Bootstrap.php";
new Bootstrap();

require_once "Snep/Config.php";
require_once "Snep/Logger.php";
require_once "PBX/Asterisk/AGI.php";
require_once "Zend/Console/Getopt.php";

$config = Snep_Config::getConfig();
$log = Snep_Logger::getInstance();
$asterisk = PBX_Asterisk_AGI::getInstance();

// Line command option configuration
try {
    $opts = new Zend_Console_Getopt(array(
        'version|v' => 'Prints version.',
        'outgoing_number|o=s' => 'Define a outgoing number',
        'xfer|x=s' => 'Replace the channel used for source identification.'
    ));
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    $log->err($e->getMessage());
    $log->err($e->getUsageMessage());
    exit;
}

// Version print
if ($opts->version) {
    echo "SNEP Version " . Zend_Registry::get('snep_version') . "\n";
    exit;
}

if ($opts->xfer) {
    $asterisk->request['agi_channel'] = $opts->xfer;
    $request = new PBX_Asterisk_AGI_Request($asterisk->request);
    $asterisk->requestObj = $request;
}

if ($opts->outgoing_number) {
    Zend_Registry::set("outgoingNumber", $opts->outgoing_number);
} else {
    Zend_Registry::set("outgoingNumber", "");
}

$log = Zend_Registry::get('log');
$request = $asterisk->requestObj;

// Primeira informação sobre a ligação
$log->info("Connection attempt from $request->origem ($request->channel) to $request->destino");
$origem = $request->origem;
try {
    // Procurando por regra de negócio no banco de dados
    $dialplan = new PBX_Dialplan();
    $dialplan->setRequest($asterisk->requestObj);
    $dialplan->parse();

    $regra = $dialplan->getLastRule();

} catch (PBX_Exception_NotFound $ex) {
    $log->info("No rule found for this request: " . $ex->getMessage());
    if (!$opts->xfer) {
        $asterisk->answer();
        $asterisk->stream_file('invalid');
        $asterisk->hangup();
    }
    exit();
} catch (Exception $ex) {
    $log->crit("Oops! Exception to resolv rules. Contact technical support.");
    $log->crit($ex);
    die();
}

$src = $request->getOriginalCallerid();

    if (class_exists("Agents_Agent") || class_exists("Agents_Manager")) {

        $validaAgente = Agents_Manager::getConfig();
        $validaSrc = (int) $validaAgente["alterSrc"];

        if ($validaSrc == true) {
            $src = $request->origem;
        } else {
            $src = $request->getOriginalCallerid();
        }
    }

// Definindo nome do arquivo de gravação.
// Formato: Timestamp_aaaammdd_hhmm_src_dst.wav
$filename = implode("_", array(
    time(),
    date("Ymd"),
    date("Hi"),
    $src,
    $request->getOriginalExtension()
        ));

// Definindo userfield com o nome do arquivo para que se possa encontrar a
// gravação a partir do registro no CDR.
$lastuserfield = $asterisk->get_variable('CDR(userfield)');
if ($lastuserfield['data'] === "") {
    $asterisk->set_variable("CDR(userfield)", $filename);
} else {
    $filename = $lastuserfield['data'];
}

$recordPath = realpath($config->ambiente->path_voz).date("/Y-m-d/");
$regra->setRecordApp($config->general->record->application, array($recordPath . "/" . $filename . ".wav", $config->general->record->flag));

$regra->setAsteriskInterface($asterisk);

try {
    $log->info("Running the rule {$regra->getId()}:$regra");
    $regra->execute($origem);
    $log->info("End of running the rule {$regra->getId()}:$regra");
} catch (PBX_Exception_AuthFail $ex) {
    $log->info("Failed to authenticate the extension.");
} catch (Exception $ex) {
    $log->crit($ex);
    die();
}
