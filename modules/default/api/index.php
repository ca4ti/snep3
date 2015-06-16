<?php

// silenciando strict até arrumar zend_locale
// date_default_timezone_set("America/Sao_Paulo");

$config_file = "../../../includes/setup.conf";

//encontrado diretórios do sistema
if (!file_exists($config_file)) {
    die("FATAL ERROR: arquivo $config_file nao encontrado");
};
$config = parse_ini_file($config_file, true);



// Adicionando caminho de libs ao include path para autoloader trabalhar:
set_include_path($config['system']['path.base'] . "/lib" . PATH_SEPARATOR . get_include_path());
$logdir = $config['system']['path.log'];
unset($config);
// iniciando auto loader
require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();


// Registrando namespaces para as outras bibliotecas
$autoloader->registerNamespace('Snep_');
$autoloader->registerNamespace('PBX_');
$autoloader->registerNamespace('Asterisk_');

// Carregando arquivo de configuração do snep e alocando as informações
// no registro do Zend.
$config = new Zend_Config_Ini($config_file);
$debug = (boolean) $config->system->debug;
Zend_Registry::set('configFile', $config_file);
Zend_Registry::set('config', $config);



// Iniciando sistema de logs
$log = new Zend_Log();
Zend_Registry::set('log', $log);

// Definindo aonde serão escritos os logs
$writer = new Zend_Log_Writer_Stream($logdir . '/ui.log');
// Filtramos a 'sujeira' dos logs se não estamos em debug mode.
if (!$debug) {
    $filter = new Zend_Log_Filter_Priority(Zend_Log::WARN);
    $writer->addFilter($filter);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}
$log->addWriter($writer);

// Iniciando banco de dados
$db = Zend_Db::factory('Pdo_Mysql', $config->ambiente->db->toArray());
Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('db', $db);
unset($db);

require_once(dirname(__FILE__) . "/actions/SnepService.php");

/**
 * Função para  os erros
 * @param Causa do erro, "mensagem que será impressa"
 * @param tipo do erro, fatal usa die, normal usa echo
 */
function error($cause) {
    die('{"status":"error","cause":"' . $cause . '"}');
}

// Serviço a ser executado
if (!isset($_GET['service'])) {
    error("Servico nao específicado");
} else {
    if (strtolower($_GET['service']) == "snep")
        error("Nome de servico invalido");
    $service = $_GET['service'] . "Service";
}

$filename = dirname(__FILE__) . "/actions/" . $service . ".php";




// Verifica a existencia do serviço
if (file_exists($filename)) {
	require_once($filename);
} else {
    error("Servico nao encontrado; $service") ;
}


// Carrega o serviç
$service = new $service;
// Executa o serviço
$resultado = $service->execute();

// Seta o HTTP header de conteudo de resposta para application/json
header('Content-Type: application/json'); 

// // Imprime resultado
if ($_GET['service'] == "CallsReport") {
    echo str_replace('\\/', '/', json_encode($resultado));
} else {
    echo json_encode($resultado);
}
