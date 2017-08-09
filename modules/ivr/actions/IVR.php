<?php
/**
 *  This file is part of SNEP, a SNEP Module.
 *
 *  IVR is a copyright from Opens Tecnologia.
 */

/**
 * Run a IVR
 *
 *
 * @see PBX_Rule
 * @see PBX_Rule_Action
 *
 * @category  Snep
 * @package   PBX_Rule_Action
 * @copyright Copyright (c) 2015 OpenS Tecnologia
 * @author    Douglas Conrad
 */
class IVR extends PBX_Rule_Action {

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
     * Define the actions configs
     * @param array $config
     */
    public function setConfig($config) {

        parent::setConfig($config);

        $this->file = $config['file'];
        $this->userentry = ( isset($config['userentry']) && $config['userentry'] == 'true') ? true:false;

    }

    /**
     * Return the Action Name
     * @return Action Name
     */
    public function getName() {
        return $this->i18n->translate("IVR");
    }

    /**
     * Retorna o numero da versão da classe.
     * @return Versão da classe
     */
    public function getVersion() {
        return SNEP_VERSION;
    }

    /**
     * Return the Action Description
     * @return
     */
    public function getDesc() {
        return $this->i18n->translate("Run an automation process defined by the administrator.");
    }

    /**
     * Return a XML with all configurations needed by the Action
     * @return String XML
     */
    public function getConfig() {
        $userentry = (isset($this->config['userentry']))?"<value>{$this->config['userentry']}</value>":"";
        $finalflow = (isset($this->config['finalflow']))?"<value>{$this->config['finalflow']}</value>":"";
        $file = (isset($this->config['file']))?"<value>{$this->config['file']}</value>":"";
        $prefix = (isset($this->config['prefix']))?"<value>{$this->config['prefix']}</value>":"";
        $timeout = (isset($this->config['timeout']))?"<value>{$this->config['timeout']}</value>":"10";
        $digits = (isset($this->config['digits']))?"<value>{$this->config['digits']}</value>":"4";
        $wrongaction = (isset($this->config['wrongaction']))?"<value>{$this->config['wrongaction']}</value>":"0";
        $wloops = (isset($this->config['wloops']))?"<value>{$this->config['wloops']}</value>":"1";

        $menu = array(
          "audio_file" => $this->i18n->translate('Menu Audio File'),
          "userentry" => $this->i18n->translate('Wait for user input'),
          "prefix" => $this->i18n->translate('IVR Prefix String - used to identify the next steps on IVR process'),
          "wait_time" => $this->i18n->translate('Wait for how long - in seconds'),
          "wait_digits" => $this->i18n->translate('Wait for which digits - split by ","'),
          "goto" => $this->i18n->translate('If Digits is wrong, go to action ID'),
          "loops" => $this->i18n->translate('How many loops')
        );
        return <<<XML
<params>
    <audio>
        <label>{$menu['audio_file']}</label>
        <id>file</id>
        $file
    </audio>
    <boolean>
    	<id>userentry</id>
    	<default>false</default>
    	<label>{$menu['userentry']}</label>
	$userentry
    </boolean>
    <string>
        <label>{$menu['prefix']}</label>
        <id>prefix</id>
        <default></default>
        $prefix
    </string>
    <int>
        <label>{$menu['wait_time']}</label>
        <id>timeout</id>
        <default>6</default>
        $timeout
    </int>
    <string>
        <label>{$menu['wait_digits']}</label>
        <id>digits</id>
        <default>1,2,3</default>
        $digits
    </string>
    <int>
        <label>{$menu['goto']}</label>
        <id>wrongaction</id>
        <default>1</default>
        $wrongaction
    </int>
    <int>
        <label>{$menu['loops']}</label>
        <id>wloops</id>
        <default>3</default>
        $wloops
    </int>
</params>
XML;
    }



    /**
     * Run the action. It is called inside the SNEP AGI.
     *
     * @param Asterisk_AGI $asterisk
     * @param Asterisk_AGI_Request $request
     */
    public function execute($asterisk, $request) {
        $log = Zend_Registry::get('log');
    		$this->log = $log;

    		$stop_flow = false;

    		$db = Zend_Registry::get("db");

        $log->info("Answering and playing audio file: " . $this->config['file']);
        $asterisk->answer();
    		//$asterisk->stream_file($this->config['file']);

  			if($stop_flow == true){
  				$asterisk->hangup();
  			}
        $loop = $this->config['wloops'];
        $digits = explode(",",$this->config['digits']);
        if($this->config['prefix']){
          $prefix = $this->config['prefix'] . '-';
        }else{
          $prefix = "";
        }

        $log->info("Waiting for digits: {$this->config['digits']}");


        while($loop > 0){
          if($this->config['userentry'] == "true"){
              $interaction++;
              $userinput_row = $asterisk->get_data($this->config['file'],$this->config['timeout']*1000,$this->config['digits']);
              $userinput = $userinput_row['result'];
              $log->info("User input option: [{$userinput}] in the [$interaction] interaction.");
              $log->info("Result code [{$userinput_row['code']}] and status [{$userinput_row['data']}]");
              if(in_array($userinput, $digits)){
                $log->info("Redirecting call to option $userinput");
                $asterisk->exec_goto('default',$prefix . $userinput,1);
                break;
              }
          }
          $loop--;
       }

      }

}
