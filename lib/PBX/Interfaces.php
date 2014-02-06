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

require_once "Snep/Db.php";
require_once "PBX/Trunks.php";
require_once "PBX/Usuarios.php";

/**
 * Classe para facilitar a recuperação de interfaces do banco
 * de dados.
 *
 * NOTA: interfaces DEVEM POSSUIR DONO, seja ramal ou tronco.
 *
 * Classe "portada" da versão 3 do snep. Foram retiradas algumas funcionalidades
 * mas mantidas suas assinaturas para que possam ser restauradas ou implementadas
 * ao longo do tempo.
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2010 OpenS Tecnologia
 * @author Henrique Grolli Bassotto
 */
class PBX_Interfaces {

    private $interfaceList;

    public function __construct() {
        $this->interfaceList = array();
    }

    /**
     * Adiciona interfaces na lista de inclusão.
     *
     * @param Snep_Interface $interface
     */
    public function addInterface($interface) {
        throw new Exception("Nao suportado por essa versao do snep");
    }

    /**
     * Executa a inclusão no banco de dados
     */
    public function commit() {
        throw new Exception("Nao suportado por essa versao do snep");
    }

    /**
     * Remove interfaces do banco de dados.
     *
     * @param id da interface a ser removida do banco de dados do snep.
     */
    public static function delete($interface_id) {
        throw new Exception("Nao suportado por essa versao do snep");
    }

    /**
     * Retorna uma instancia de interface a partir de sua persistencia no
     * banco de dados do Snep.
     *
     * @param int Id da interface no banco.
     * @return PBX_Asterisk_Interface do tipo correspondente ao presente no banco
     */
    public static function get($id) {
        throw new Exception("Nao suportado por essa versao do snep");
    }

    /**
     * Retorna todas as interfaces do banco de dados em um array.
     * Esse metodo também pode retornar todas as classes de um tipo
     * específico.
     *
     * @param string Tipo da interface a serem retornadas no array
     * (SIP, IAX2, etc).
     * @return Array associativo com as interfaces encontradas.
     */
    public static function getAll($tipo = 'all') {
        throw new Exception("Nao suportado por essa versao do snep");
    }
    
    /**
     * Procura o dono de uma interface baseado em canal.
     *
     * @param string $channel Canal da interface
     * @return object Objeto que representa o dono da interface (se houver)
     */
    public static function getChannelOwner($channel) {
        $db = Snep_Db::getInstance();

        $select = $db->select()
                     ->from('trunks');

        $trunk_ifaces = $db->query($select)->fetchAll();

        foreach ($trunk_ifaces as $interface) {
            if(preg_match("#^{$interface['id_regex']}$#i", $channel)) {
                return PBX_Trunks::get($interface['id']);
            }
        }

        $select = $db->select()
                     ->from('peers')
                     ->where("name != 'admin' AND peer_type='R'");

        $interfaces = $db->query($select)->fetchAll();

        foreach ($interfaces as $interface) {
            if(preg_match("#^{$interface['canal']}$#i", $channel)) {
                return PBX_Usuarios::get($interface['name']);
            }
        }
        
        return null;
    }

    /**
     * Força reconfiguração de todas as interfaces. Chama as configurações
     * efetivas das interfaces no banco de dados para que seja reconstruida
     * a configuração real das mesmas.
     */
    public static function reconfigure() {
        throw new Exception("Nao suportado por essa versao do snep");
    }

    /**
     * Faz o armazenamento de uma interface no banco de dados de interfaces
     * do snep.
     *
     * @param Interface a ser registrada no banco de dados.
     * @return id que foi atribuido pelo banco a nova interface
     */
    public static function register($interface) {
        throw new Exception("Nao suportado por essa versao do snep");
    }

    /**
     * Atualiza configurações de interface no banco de dados.
     *
     * Funcionamento:
     *   Esse método só opera com objetos que herdem a classe
     * PBX_Asterisk_Interface, e que tenha siddo gerada por essa mesma
     * classe, ou seja, que possuam o atributo ID válido e correspondente
     * ao da interface a ser atualizado.
     *
     * Esse método também faz atualização de tipo. Ou seja, um tipo de
     * interface pode ser alterado mantendo o mesmo id.
     *
     * @param Interface a ser armazenada no banco de dados no lugar da
     * interface de mesmo id.
     */
    public static function update($interface) {
        throw new Exception("Nao suportado por essa versao do snep");
    }
}
