<?php
/**
 * Efetua callback para o numero de origem da ligação e direciona para uma
 * extensão nova para ser processada novamente pelo Snep. Nada adicionado após
 * essa ação é executado.
 *
 * @see PBX_Rule
 * @see PBX_Rule_Action
 *
 * @category  Snep
 * @package   PBX_Rule_Action
 * @copyright Copyright (c) 2009 OpenS Tecnologia
 * @author    Henrique Grolli Bassotto
 */
class CallbackAction extends PBX_Rule_Action {

    /**
     * @var Internacionalization
     */
    private $i18n;

    /**
     * Constructor
     * @param
     */
    public function __construct() {
          $this->i18n = Zend_Registry::get("i18n");
    }

    /**
     * Retorna o nome da Ação. Geralmente o nome da classe.
     * @return Nome da Ação
     */
    public function getName() {
        return $this->i18n->translate("Callback");
    }

    /**
     * Retorna o numero da versão da classe.
     * @return Versão da classe
     */
    public function getVersion() {
        return SNEP_VERSION;
    }

    /**
     * Retorna uma breve descrição de funcionamento da ação.
     * @return Descrição de funcionamento ou objetivo
     */
    public function getDesc() {
        return $this->i18n->translate("Make a Callback to the caller number.");
    }

    /**
     * Devolve um XML com as configurações requeridas pela ação
     * @return String XML
     */
    public function getConfig() {
        $trs = Zend_Registry::get("i18n");
        $extension  = (isset($this->config['extension']))?"<value>{$this->config['extension']}</value>":"";
        $callerid   = (isset($this->config['callerid']))?"<value>{$this->config['callerid']}</value>":"";
        $play_audio = (isset($this->config['play_audio']))?"<value>{$this->config['play_audio']}</value>":"";
        $audio      = (isset($this->config['audio']))?"<value>{$this->config['audio']}</value>":"<value>astcc-pleasewait</value>";
        $callback_inbound_number = $trs->translate('Call back the Caller number');
        $callback_inbound_number_desc = $trs->translate('This will be the Inbound(DID) number which call will be redirected when the caller answer the call. Could be any Destiny Route Rule.');
        $source_number = $trs->translate('Specify the source call number');
        $source_number_desc = $trs->translate('Specify the source number to make the call back to the caller. This call will be processed by the Outgoing Routes with this source. Leave blank to keep here the original Caller number.');
        $callback_delay = $trs->translate('Delay trying call back');
        $callback_delay_desc = $trs->translate('This is the time in seconds the system will try contact the caller');
        $play_audio_label = $trs->translate('Answer and play audio before hangup the call to call back.');

        return <<<XML
<params>
    <string>
        <id>extension</id>
        <label>$callback_inbound_number</label>
        <description>$callback_inbound_number_desc</description>
        $extension
    </string>
    <string>
        <id>callerid</id>
        <label>$source_number</label>
        <description>$source_number_desc</description>
        $callerid
    </string>
    <int>
        <id>callback_delay</id>
        <label>$callback_delay</label>
        <description>$callback_delay_desc</description>
        <default>45</default>
        <unit>seconds</unit>
        <size>2</size>
        $callback_delay
    </int>
    <boolean>
        <id>play_audio</id>
        <label>$play_audio_label</label>
        $play_audio
    </boolean>
    <audio>
        <id>audio</id>
        $audio
    </audio>
</params>
XML;
    }

    /**
     * Devolve um XML com as configurações padrão da ação
     * @return String XML
     */
    public function getDefaultConfigXML() {
        $callback_delay  = (isset($this->defaultConfig['callback_delay'])) ? $this->defaultConfig['callback_delay'] : 5;
        $callback_delay  = "<value>$callback_delay</value>";

        return <<<XML
<params>
    <int>
        <id>callback_delay</id>
        <label>Atraso para Retorno da Ligação</label>
        <unit>segundos</unit>
        <size>2</size>
        $callback_delay
    </int>
</params>
XML;
    }

    /**
     * Executa a ação.
     * @param Asterisk_AGI $asterisk
     * @param PBX_Asterisk_AGI_Request $request
     */
    public function execute($asterisk, $request) {
        $log = Zend_Registry::get('log');

        $callback_delay = isset($this->defaultConfig['callback_delay']) ? $this->defaultConfig['callback_delay'] : 5;
        if(isset($this->config['callerid']) && $this->config['callerid'] != "") {
            $callerid = $this->config['callerid'];
        }
        else {
            $callerid = $request->origem;
        }

        $timeout = $this->config['callback_delay'] * 1000;
        if(isset($this->config['play_audio']) && $this->config['play_audio'] == "true") {
            $asterisk->answer();
            $asterisk->stream_file($this->config['audio']);
           // $asterisk->hangup();
        }

        $log->info("Making call for {$request->origem}");
        $ami = PBX_Asterisk_AMI::getInstance();
        $call = $ami->Originate("Local/{$request->origem}", $this->config['extension'], "default",1,NULL,NULL, $timeout,"$callerid");
        $status = serialize($call);
        $log->info("Call Status: $status -> {$this->config['extension']}");


        throw new PBX_Rule_Action_Exception_StopExecution("Fim da ligacao");
    }
}
