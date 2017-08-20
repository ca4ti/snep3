<?php

/**
 * Invite Controller
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2017 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */
class InviteController extends Zend_Controller_Action {

    /**
     * Initial settings of the class
     */
    public function init() {
        $this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->key = Snep_Dashboard_Manager::getKey(
            Zend_Controller_Front::getInstance()->getRequest()->getModuleName(),
            Zend_Controller_Front::getInstance()->getRequest()->getControllerName(),
            Zend_Controller_Front::getInstance()->getRequest()->getActionName());
    }

    /**
     * indexAction
     */
    public function indexAction() {

        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
            $this->view->translate("Invite")));

        $layout = Zend_Layout::getMvcInstance();

        $invite = $this->getRequest()->getParam('id');

        $db = Zend_Registry::get('db');

        if(!isset($invite)){
          $this->_redirect('/');
        }else{
          $today = date("Y-m-d H:i:s");
          $select = $db->select()
                ->from('signup')
                ->where('invite = ?', $invite)
                ->where('validate > ?', $today);
                
          $query = $db->query($select)->fetch();

          if($query){
            $this->view->invite = $invite;
            $layout->setLayout('signup');
          }else{
            $this->_redirect('/');
          }
        }

    }
}
