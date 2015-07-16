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

/**
 * Faz o controle em banco dos Alias para expressões regulares.
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 * @author Henrique Grolli Bassotto
 */
class PBX_ExpressionAliases {

    private static $instance;

    protected function __construct() {
        
    }

    protected function __clone() {
        
    }

    /**
     * Retorna instancia dessa classe
     * @return PBX_ExpressionAliases
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * getAll - List expression Aliases
     * @return <array> $aliases
     */
    public function getAll() {
        $db = Zend_Registry::get('db');
        $select = "SELECT aliasid, name FROM expr_alias";

        $stmt = $db->query($select);
        $raw_aliases = $stmt->fetchAll();

        $aliases = array();
        foreach ($raw_aliases as $alias) {
            $aliases[$alias['aliasid']] = array(
                "id" => $alias['aliasid'],
                "name" => $alias['name'],
                "expressions" => array()
            );
        }

        $db = Zend_Registry::get('db');
        $select = "SELECT aliasid, expression FROM expr_alias_expression";

        $stmt = $db->query($select);
        $raw_expressions = $stmt->fetchAll();

        foreach ($raw_expressions as $expr) {
            $aliases[$expr["aliasid"]]["expressions"][] = $expr['expression'];
        }

        return $aliases;
    }

    /**
     * get
     * @param <int> $id
     * @return <array>
     * @throws PBX_Exception_BadArg
     */
    public function get($id) {
        if (!is_integer($id)) {
            throw new PBX_Exception_BadArg("Id must be numerical");
        }

        $db = Zend_Registry::get('db');
        $select = "SELECT name FROM expr_alias WHERE aliasid='$id'";

        $stmt = $db->query($select);
        $raw_alias = $stmt->fetchObject();
        $alias = array(
            "id" => $id,
            "name" => $raw_alias->name,
            "expressions" => array()
        );

        $db = Zend_Registry::get('db');
        $select = "SELECT expression FROM expr_alias_expression WHERE aliasid='$id'";

        $stmt = $db->query($select);
        $raw_expression = $stmt->fetchAll();

        foreach ($raw_expression as $expr) {
            $alias["expressions"][] = $expr['expression'];
        }

        return $alias;
    }

    /**
     * getValidation - checks if the regular expression is used in the rule 
     * @param <int> $id
     * @return <array>
     */
    public function getValidation($id) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('regras_negocio', array('id', 'desc'))
                ->where("regras_negocio.origem LIKE ?", '%AL:'.$id.'%')
                ->orwhere("regras_negocio.destino LIKE ?", '%AL:'.$id.'%');

        $stmt = $db->query($select);
        $regras = $stmt->fetchall();

        return $regras;
    }

    /**
     * register
     * @param <array> $expression
     * @throws Exception
     */
    public function register($expression) {
        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->insert("expr_alias", array("name" => $expression['name']));
        $id = $db->lastInsertId();

        foreach ($expression['expressions'] as $key => $expr) {
            $data = array("aliasid" => $id, "expression" => $expr);
            $db->insert("expr_alias_expression", $data);
        }

        try {
            $db->commit();
        } catch (Exception $ex) {
            $db->rollBack();
            throw $ex;
        }
    }

    /**
     * update - update expression alias
     * @param <array> $expression
     * @throws Exception
     */
    public function update($expression) {
        $id = $expression['id'];

        //log-user
        if (class_exists("Loguser_Manager")) {

            $add = self::getExpression($id);
            self::insertLogExpression("OLD", $add);
        }

        $db = Zend_Registry::get('db');
        $db->beginTransaction();
        $db->update("expr_alias", array("name" => $expression['name']), "aliasid='$id'");
        $db->delete("expr_alias_expression", "aliasid='$id'");

        foreach ($expression['expressions'] as $key => $expr) {
            $data = array("aliasid" => $id, "expression" => $expr);
            $db->insert("expr_alias_expression", $data);
        }

        try {
            $db->commit();
        } catch (Exception $ex) {
            $db->rollBack();
            throw $ex;
        }
    }

    /**
     * delete - Remove expression alias
     * @param <int> $id
     */
    public function delete($id) {
        $db = Zend_Registry::get('db');

        $db->delete("expr_alias", "aliasid='$id'");
    }

    /**
     * getExpression - Array with data of expression alias
     * @param <int> $id 
     * @return <array> $archive 
     */
    function getExpression($id) {

        $db = Zend_Registry::get("db");

        $select = $db->select()
                ->from("expr_alias", array("aliasid as id", "name"))
                ->where("expr_alias.aliasid = ?", $id);
        $stmt = $db->query($select);
        $archive = $stmt->fetch();

        $select = $db->select()
                ->from("expr_alias_expression", array("expression"))
                ->where("expr_alias_expression.aliasid = ?", $id);
        $stmt = $db->query($select);
        $expressions = $stmt->fetchall();
        $archive["exp"] = "";

        foreach ($expressions as $expr) {
            $archive["exp"] .= $expr["expression"] . " ";
        }

        return $archive;
    }

    /**
     * insertLogexpression - Insert data of expression on table logs_users 
     * @param <array> $add
     * @param <string> $acao
     */
    function insertLogExpression($acao, $add) {

        $db = Zend_Registry::get("db");

        $ip = $_SERVER['REMOTE_ADDR'];
        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();

        $insert_data = array('hora' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'idusuario' => $username,
            'cod' => $add["id"],
            'param1' => $add["name"],
            'param2' => $add["exp"],
            'value' => "EXP",
            'tipo' => $acao);

        $db->insert('logs_users', $insert_data);
    }

}
