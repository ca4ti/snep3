<?php

/*
 *  This file is part of SNEP.
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
 * Controller permission
 * 
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 * 
 */
class PermissionController extends Zend_Controller_Action {

    /**
     * indexAction
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction() {
        $exten = $this->getRequest()->getParam("exten");

        if ($exten === null) {
            throw new Zend_Controller_Action_Exception('Page not found.', 404);
        }

        if ($exten == 'error' ) {
            $this->view->error = $this->translate("You do not have permission to perform this action") ;
            $this->renderScript('error/sneperror.phtml');
        } elseif($exten == 'error-unset') {
            $this->view->error = $this->translate("You do not have permission to perform this action") ;
            $this->renderScript('error/sneperror.phtml');
        }

        try {
            PBX_Usuarios::get($exten);
        } catch (PBX_Exception_NotFound $ex) {
            throw new Zend_Controller_Action_Exception($this->view->translate("Extension %s does not exists.", $exten), 404);
        }

        $this->view->exten = $exten;
        $this->view->breadcrumb = Snep_Breadcrumb::renderPath(array(
                    "Manage",
                    "Extensions",
                    "Permissions"));

        $resources = array();
        foreach (Snep_Acl::getInstance()->getResources() as $resource) {
            $res_tree = explode("_", $resource);
            $resource = array();
            while ($element = array_pop($res_tree)) {
                $resource = array($element => $resource);
            }
            $resources = array_merge_recursive($resources, $resource);
        }
        $this->view->resources = $resources;
    }

}
