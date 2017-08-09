<?php

class Billing_BillingController extends Zend_Controller_Action {

    /**
     * getForm - Monta formulário
     * @return \Snep_Form_Simple
     */
    protected function getForm() {

        //$form_config = new Zend_Config_Xml('./modules/loguser/forms/log-user.xml', 'general', true);
        //$form = new Snep_Form_Simple($form_config);
        $form = new Snep_Form_Simple();
        $form->setAction($this->getFrontController()->getBaseUrl() . '/billing/billing');


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

        $session = new Zend_Session_Namespace("billing");

        $this->view->breadcrumb = $this->view->translate("Billing");
        $this->view->url = $this->getFrontController()->getBaseUrl() . '/billing';

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


        $session = new Zend_Session_Namespace("billing");
        if ($this->getRequest()->isPost()) {
          $this->view->logs = Billing_Manager::getAll($_POST['initDay'], $_POST['finalDay'], $_POST['selectAct']);
          $this->renderScript('billing/view.phtml');
        }
        $this->view->form = $form;
    }

    /**
    * Add Telco Action
    */
    public function addAction(){
      $db = Zend_Registry::get('db');
      Zend_Debug::Dump($_POST);exit;
    }

}
