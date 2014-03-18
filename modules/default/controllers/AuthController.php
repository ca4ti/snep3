<?php

/**
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
require_once 'Zend/Controller/Action.php';

/**
 * Authentication Controll
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 * @author    Henrique Grolli <henrique@opens.com.br>
 */
class AuthController extends Zend_Controller_Action {

    public function loginAction() {
        $this->view->headTitle($this->view->translate("Login"));
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Login")
        ));
        $this->view->hideMenu = true;

        $this->view->PAGEINFO = "{$this->getFrontController()->getBaseUrl()}/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/redefine";

        $config = Zend_Registry::get('config');
        if (trim($config->ambiente->db->host) == "") {
            if (Zend_Auth::getInstance()->hasIdentity()) {
                Zend_Auth::getInstance()->clearIdentity();
            }
            $this->_redirect("installer/");
        }

        if ($_GET) {
            $this->view->message = $this->view->translate("Password successfully recovered");
            $this->view->msgclass = 'sucess';
        }

        // Não precisamos fazer login se ja estamos logados
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/');
        }

        if ($this->_request->isPost()) {
            // Filtrando informações do usuário
            $f = new Zend_Filter_StripTags();
            $username = $f->filter($this->_request->getPost('user'));
            $password = $this->_request->getPost('password');


            if (empty($username)) {
                $this->view->message = $this->view->translate("Please enter a username");
                $this->view->msgclass = 'failure';
            } else {
                $db = Zend_Registry::get('db');

                // criando adaptador de autorização
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);

                // informações das tabelas
                $authAdapter->setTableName('users');
                $authAdapter->setIdentityColumn('name');
                $authAdapter->setCredentialColumn('password');

                // Valores vindos do usuário como credencial
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential(md5($password));

                // autenticação
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);


                // tratando resultados
                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        $this->view->message = $this->view->translate('User or password invalid');
                        $this->view->msgclass = 'failure';
                        break;
                    case Zend_Auth_Result::SUCCESS:
                        $auth->getStorage()->write($result->getIdentity());


                        $extension = $db->query("SELECT id, name FROM users WHERE name='$username'")->fetchObject();

                        /* Mantendo antigo verifica.php no ar */
                        $_SESSION['id_user'] = $extension->id;
                        $_SESSION['name_user'] = $username;
                        $_SESSION['active_user'] = $extension->name;
                        $_SESSION['vinculos_user'] = "";

                        $this->_redirect('/');
                        break;
                    default:
                        $this->view->message = $this->view->translate('Authentication failure');
                        $this->view->msgclass = 'failure';
                        break;
                }
            }
        }
    }

    /**
     * recuperationAction - Recuperation of password
     */
    public function recuperationAction() {

        $this->view->headTitle($this->view->translate("Password Recovery"));
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Password Recovery")
        ));

        $this->view->hideMenu = true;

        if ($_GET) {
            $this->view->msgclass = 'sucess';
            $this->view->message = $this->view->translate("We've sent an access code to the email: " . base64_decode($_GET['param']));
        }

        if ($this->_request->isPost()) {

            $f = new Zend_Filter_StripTags();

            $data = array();
            $data['user'] = $f->filter($this->_request->getPost('user'));
            $data['password'] = $f->filter($this->_request->getPost('password'));
            $data['newpassword'] = $f->filter($this->_request->getPost('newpassword'));
            $data['code'] = $f->filter($this->_request->getPost('code'));

            if (Snep_Auth_Manager::getUser($data['user']) == false) {
                $this->view->msgclass = 'failure';
                $this->view->message = $this->view->translate("User not registered");
            } else {

                if ($data['password'] != $data['newpassword']) {
                    $this->view->msgclass = 'failure';
                    $this->view->message = $this->view->translate("Passwords do not match");
                } else {
                    $recuperation = Snep_Auth_Manager::getPassword($data['user']);

                    if ($data['code'] != $recuperation["code"]) {
                        $this->view->msgclass = 'failure';
                        $this->view->message = $this->view->translate("Code invalid");
                    } else {
                        $date = date("Y-m-d H:i:s");
                        $expiration = $recuperation["expiration"];
                        if (strtotime($date) > strtotime($expiration)) {
                            $this->view->msgclass = 'failure';
                            $this->view->message = $this->view->translate("Time of validity of the code was expired");
                        } else {

                            Snep_Auth_Manager::getUpdatePass($data);
                            $this->_redirect("auth/login?recuperation=yes");
                        }
                    }
                }
            }
        }
    }

    public function redefineAction() {

        $this->view->headTitle($this->view->translate("Password Recovery"));
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    $this->view->translate("Password Recovery")
        ));

        $this->view->hideMenu = true;
        $this->view->msgclass = 'failure';

        if ($this->_request->isPost()) {

            $f = new Zend_Filter_StripTags();
            $username = $f->filter($this->_request->getPost('user'));

            $user = Snep_Auth_Manager::getUser($username);

            $date = date("Y-m-d H:i:s");

            if ($user == false) {
                $this->view->message = $this->view->translate("User not registered");
            } else {

                $user['code'] = $this->aleatorio();
                $user['expiration'] = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($date)));
                Snep_Auth_Manager::addCode($user);

                $code = $user['code'];
                $expiration = date('d/m/Y G:i:s', strtotime($user['expiration']));

                $config = Zend_Registry::get('config');
                $client = $config->ambiente->emp_nome;
                
                $msg = $this->view->translate("You asked for resetting your password the user <b>$username</b> on PABX <b>$client</b>.<br><br>Need to enter the code below for the redefinition.<br>Access code : <font color= red><b>" . $code . "</b></font><br>Your code will expire on: " . $expiration . "<br><br>If you have not requested a redefinition password, please disregard<br><br><i>Team Snep</i>.");
                $subject = $this->view->translate("Redefinition password - SNEP");

                Snep_Auth_Manager::sendEmail($user, $msg, $subject);
                $mail = base64_encode($user['email']);

                $this->_redirect('/auth/recuperation?param=' . $mail);
            }
        }
    }

    public function aleatorio() {
        $key = "";
        $valor = "ABCDEFGHJKLMNOPQRSTUVWXYZ0123456789";
        srand((double) microtime() * 1000000);
        for ($i = 0; $i < 6; $i++) {
            $key.= $valor[rand() % strlen($valor)];
        }
        return $key;
    }

    public function logoutAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            Zend_Auth::getInstance()->clearIdentity();
        }
        $this->_redirect("auth/login");
    }

}
