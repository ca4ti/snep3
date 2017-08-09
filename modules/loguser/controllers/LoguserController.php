<?php

class Loguser_LoguserController extends Zend_Controller_Action {

    /**
     * getForm - Monta formulário
     * @return \Snep_Form_Simple
     */
    protected function getForm() {

        $form_config = new Zend_Config_Xml('./modules/loguser/forms/log-user.xml', 'general', true);
        $form = new Snep_Form_Simple($form_config);
        $form->setAction($this->getFrontController()->getBaseUrl() . '/loguser/log-user');


        $form->getElement('submit')->setLabel('Enviar');
        $form->getElement('submit')->removeDecorator('DtDdWrapper');
        $form->getElement('submit')->addDecorator(array("opentd" => 'HtmlTag'), array('class' => 'form_control', "colspan" => 2, 'tag' => 'td'));
        $form->getElement('submit')->addDecorator(array("opentr" => 'HtmlTag'), array('tag' => 'tr'));
        $form->removeElement('cancel');

        return $form;
    }

    /**
     * IndexAction - Monta tela principal
     */
    public function indexAction() {

        $session = new Zend_Session_Namespace("loguser");

        $this->view->breadcrumb = $this->view->translate("Log de Controle de usuario");
        $this->view->refs_post = $this->getFrontController()->getBaseUrl() . '/loguser/log-user/';

        $form = $this->getForm();
        $conf = Zend_Registry::get('config');
        $this->view->web = $conf->system->path->web;
        $this->view->groups = array(
          "all" => $this->view->translate('All Actions'),
          "trunks" => $this->view->translate('Trunks Actions'),
          "regras_negocio" => $this->view->translate('Routes Actions'),
          "peers" => $this->view->translate('Extensions Actions'),
          "expr_alias" => $this->view->translate('Expression Alias Actions'),
          "date_alias" => $this->view->translate('Expression Alias Actions'),
          "queues" => $this->view->translate('Queues Actions')
        );


        $session = new Zend_Session_Namespace("loguser");
        if ($this->getRequest()->isPost()) {
          $this->view->logs = Loguser_Manager::getAll($_POST['initDay'], $_POST['finalDay'], $_POST['selectAct']);
          $this->renderScript('loguser/view.phtml');
        }
        $this->view->form = $form;
    }

    /**
     * viewAction - Monta log conforme filtro
     */
    public function viewAction() {

        $session = new Zend_Session_Namespace("log-user");
        $form_data = $session->form_data;
        $this->view->PAGEINFO = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}";
        $this->view->breadcrumb = $this->view->translate("User control logs » {$form_data['initDay']} - {$form_data['finalDay']} ");
        $this->view->form_data = $form_data;
        $data = Loguser_Manager::logview($form_data);
        $data = explode("<br>", $data);
        $this->view->totals = $data;
        $this->view->data = $data;
    }

}
