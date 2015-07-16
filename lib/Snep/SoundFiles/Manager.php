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
 * Classe to manager a Sound Files and MOH Sound Files.
 *
 * @see Snep_SoundFiles_Manager
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2011 OpenS Tecnologia
 * @author    Rafael Pereira Bozzetti <rafael@opens.com.br>
 * 
 */
class Snep_SoundFiles_Manager {

    public function init() { 
        $this->lang = Zend_Registry::get('config')->system->language;
    }


    // --------------------------------------------------------------------------------------------
    // Functions for Sound Files 
    // --------------------------------------------------------------------------------------------
    /**
     * Get a sound informations 
     * @param <string> $file
     * @param <string> $language
     * @return <array> $sound
     */
    public function get($file,$lang = false) {

        if (!$lang) {
            $lang = $this->lang; 
        }

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('sounds')
                ->where("sounds.arquivo = ?", $file)
                ->where("sounds.language = ?", $lang);

        try {
            $stmt = $db->query($select);
            $sound = $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }

        return $sound;
    }


    /**
     * Add a sound file
     * @param array $file
     */
    public function add($file) {

        $db = Zend_Registry::get('db');

        $insert_data = array('arquivo' => $file['arquivo'],
            'descricao' => $file['descricao'],
            'data' => new Zend_Db_Expr('NOW()'),
            'language' => $file['language'],
            'tipo' => $file['tipo']);

        $db->insert('sounds', $insert_data);

        return $db->lastInsertId();
    }

    

    /**
     * Update a sound file register
     * @param Array $data
     */
    public function edit($file) {

        $db = Zend_Registry::get('db');

        $update_data = array('descricao' => $file['description'],
            'tipo' => 'AST');

        $db->update("sounds", $update_data, "arquivo = '{$file['arquivo']}' and language = '{$this->lang}'");
    }

    /**
     * Remove a Sound File register
     * @param string $file
     *        string $class
     */
    public function remove($file, $class = false) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        if ($class) {
            $db->delete('sounds', "arquivo = '$file' and secao = '$class'");
        } else {
            $db->delete('sounds', "arquivo = '$file' and language = '$this->lang'");
        }

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }



    // --------------------------------------------------------------------------------------------
    // Functions for Music on Hold
    // --------------------------------------------------------------------------------------------

    /**
     * Add a MOH Class
     * @param array $class
     * @return boolean
     */

    public function addClass($class) {

        $classes = self::getClasses();
        $classes[$class['name']] = $class;

        $header = ";----------------------------------------------------------------\n ";
        $header .= "; Arquivo: snep-musiconhold.conf - Cadastro Musicas de Espera    \n";
        $header .="; Sintaxe: [secao]                                               \n";
        $header .=";          mode=modo de leitura do(s) arquivo(s)                 \n";
        $header .=";          directory=onde estao os arquivos                      \n";
        $header .= ";          [application=aplicativo pra tocar o som] - opcional   \n";
        $header .= "; Include: em /etc/asterisk/musiconhold.conf                     \n";
        $header .= ";          include /etc/asterisk/snep/snep-musiconhold.conf      \n";
        $header .= "; Atualizado em: 26/05/2008 11:22:18                             \n";
        $header .= "; Copyright(c) 2008 Opens Tecnologia                             \n";
        $header .= ";----------------------------------------------------------------\n";
        $header .= "; Os registros a Seguir sao gerados pelo Software SNEP.          \n";
        $header .= "; Este Arquivo NAO DEVE ser editado Manualmente sob riscos de    \n";
        $header .= "; causar mau funcionamento do Asterisk                           \n";
        $header .= ";----------------------------------------------------------------\n\n";

        $body = '';
        foreach ($classes as $classe) {
            $body .= "[" . $classe['name'] . "] \n";
            $body .= "mode=" . $classe['mode'] . "\n";
            $body .= "directory=" . $classe['directory'] . "\n\n";
        }

        if (!file_exists($class['directory'])) {

            exec("mkdir {$class['directory']}");
            exec("mkdir {$class['directory']}/tmp");
            exec("mkdir {$class['directory']}/backup");

            file_put_contents('/etc/asterisk/snep/snep-musiconhold.conf', $header . $body);

            return true;
        }
        return false;
    }

    /**
     * Get information about a MOH Class
     * @param array $name
     * @return array $section
     */

   public function getClasse($name) {

        $sections = new Zend_Config_Ini('/etc/asterisk/snep/snep-musiconhold.conf');
        $_section = array();
        foreach ($sections->toArray() as $class => $info) {

            if ($class == $name) {
                $_section['name'] = $class;
                $_section['mode'] = $info['mode'];
                $_section['directory'] = $info['directory'];
            }
        }
        return $_section;
    }

    /**
     * Get a list of MOH Classes
     * @return array
     */

    public function getClasses() {

        $sections = new Zend_Config_Ini('/etc/asterisk/snep/snep-musiconhold.conf');
        $_section = array();
        foreach ($sections->toArray() as $class => $info) {
            $_section[$class]['name'] = $class;
            $_section[$class]['mode'] = $info['mode'];
            $_section[$class]['directory'] = $info['directory'];
        }
        return $_section;
    } 

    /**
     * Add a MOH sound
     * @param array $file
     * @return boolean
     */
    public function addClassFile($file) {

        $db = Zend_Registry::get('db');

        $insert_data = array('arquivo' => $file['arquivo'],
            'descricao' => $file['descricao'],
            'data' => new Zend_Db_Expr('NOW()'),
            'tipo' => 'MOH',
            'language' => $this->lang,
            'secao' => $file['secao']);

        try {
            $db->insert('sounds', $insert_data);
        } catch (Exception $e) {

            return false;
        }

        return true ;
    }

    /**
     * Edit a MOH ound
     * @param array $file
     * @return boolean
     */
    public function editClassFile($file) {

        $db = Zend_Registry::get('db');

        $insert_data = array('arquivo' => $file['arquivo'],
            'descricao' => $file['descricao'],
            'data' => new Zend_Db_Expr('NOW()'),
            'tipo' => 'MOH',
            'language' => $this->lang,
            'secao' => $file['secao']);

        try {
            $db->update('sounds', $insert_data, "arquivo='{$file['arquivo']}' and secao='{$file['secao']}'");
        } catch (Exception $e) {

            return false;
        }

        return $db->lastInsertId();
    }

    /**
     * Get a list of sounds of thea MOH Class
     * @param satring $class
     * @return array
     */

    public function getClassFiles($class) {

        $allClasses = Snep_SoundFiles_Manager::getClasses();

        $classesFolder = array();
        foreach ($allClasses as $id => $xclass) {
            $classesFolder[$id] = $id;
        }

        if (file_exists($class['directory'])) {
            $allFiles = array();
            $files = array();
            foreach (scandir($class['directory']) as $file) {

                if (!preg_match("/^\.+.*/", $file) && !in_array($file, $classesFolder)) {
                    if (preg_match("/^backup+.*/", $file)) {

                        foreach (scandir($class['directory'] . '/' . $file) as $backup) {
                            if (!preg_match("/^\.+.*/", $backup)) {
                                //        $files[] = $class['directory'] .'/backup/'. $backup;
                            }
                        }
                    } elseif (preg_match("/^tmp+.*/", $file)) {

                        foreach (scandir($class['directory'] . '/' . $file) as $tmp) {
                            if (!preg_match("/^\.+.*/", $tmp)) {
                                //       $files[] = $class['directory'] .'/tmp/'. $tmp;
                            }
                        }
                    } else {
                        $files[$file] = $class['directory'] . '/' . $file;
                        //$allFiles[$file] = $file;
                    }
                }
            }


            $resultado = array();
            foreach ($files as $name => $file) {
                $resultado[$name] = Snep_SoundFiles_Manager::get($name);
                $resultado[$name]['full'] = $file;
            }

            return $resultado;
        }
    }

    /**
     * Edit a MOH Class
     * @param string $originalName $newName
     * @return boolean
     */

    public function editClass($originalName, $newClass) {

        $classes = self::getClasses();

        $directory = '';

        foreach ($classes as $class => $item) {

            if ($originalName === $item['name']) {
                $classes[$class]['name'] = $newClass['name'];
                $classes[$class]['mode'] = $newClass['mode'];
                $directory = $classes[$class]['directory'];
                $classes[$class]['directory'] = $newClass['directory'];
            }
        }

        $header =  ";----------------------------------------------------------------\n ";
        $header .= "; Arquivo: snep-musiconhold.conf - Cadastro Musicas de Espera    \n";
        $header .= "; Sintaxe: [secao]                                               \n";
        $header .= ";          mode=modo de leitura do(s) arquivo(s)                 \n";
        $header .= ";          directory=onde estao os arquivos                      \n";
        $header .= ";          [application=aplicativo pra tocar o som] - opcional   \n";
        $header .= "; Include: em /etc/asterisk/musiconhold.conf                     \n";
        $header .= ";          include /etc/asterisk/snep/snep-musiconhold.conf      \n";
        $header .= "; Atualizado em: 26/05/2008 11:22:18                             \n";
        $header .= "; Copyright(c) 2008 Opens Tecnologia                             \n";
        $header .= ";----------------------------------------------------------------\n";
        $header .= "; Os registros a Seguir sao gerados pelo Software SNEP.          \n";
        $header .= "; Este Arquivo NAO DEVE ser editado Manualmente sob riscos de    \n";
        $header .= "; causar mau funcionamento do Asterisk                           \n";
        $header .= ";----------------------------------------------------------------\n\n";

        $body = '';

        foreach ($classes as $classe) {
            $body .= "[" . $classe['name'] . "] \n";
            $body .= "mode=" . $classe['mode'] . "\n";
            $body .= "directory=" . $classe['directory'] . "\n\n";
        }

        if (!file_exists($newClass['directory'])) {

            exec("mkdir {$newClass['directory']}");
            exec("mkdir {$newClass['directory']}/tmp");
            exec("mkdir {$newClass['directory']}/backup; ");

            exec("cp  {$directory}/* {$newClass['directory']}/");
            exec("cp  {$directory}/tmp/* {$newClass['directory']}/tmp/");
            exec("cp  {$directory}/backup/* {$newClass['directory']}/backup/");

            exec("rm -rf {$directory}");

            file_put_contents('/etc/asterisk/snep/snep-musiconhold.conf', $header . $body);

            return true;
        }
        return false;
    }

    /**
     * Remove a MOH Class and your files
     * @param string $class
     * @return boolean
     */
    public function removeClass($classRemove) {

        $classes = self::getClasses();

        $directory = '';
        foreach ($classes as $class => $item) {

            if ($classRemove['name'] == $item['name']) {

                if (file_exists($classRemove['directory'])) {
                    exec("rm -rf {$classRemove['directory']}");
                }
                unset($classes[$class]);
            }
        }

        $header =  ";----------------------------------------------------------------\n ";
        $header .= "; Arquivo: snep-musiconhold.conf - Cadastro Musicas de Espera    \n";
        $header .= "; Sintaxe: [secao]                                               \n";
        $header .= ";          mode=modo de leitura do(s) arquivo(s)                 \n";
        $header .= ";          directory=onde estao os arquivos                      \n";
        $header .= ";          [application=aplicativo pra tocar o som] - opcional   \n";
        $header .= "; Include: em /etc/asterisk/musiconhold.conf                     \n";
        $header .= ";          include /etc/asterisk/snep/snep-musiconhold.conf      \n";
        $header .= "; Atualizado em: 26/05/2008 11:22:18                             \n";
        $header .= "; Copyright(c) 2008 Opens Tecnologia                             \n";
        $header .= ";----------------------------------------------------------------\n";
        $header .= "; Os registros a Seguir sao gerados pelo Software SNEP.          \n";
        $header .= "; Este Arquivo NAO DEVE ser editado Manualmente sob riscos de    \n";
        $header .= "; causar mau funcionamento do Asterisk                           \n";
        $header .= ";----------------------------------------------------------------\n\n";

        $body = '';
        foreach ($classes as $classe) {
            $body .= "[" . $classe['name'] . "] \n";
            $body .= "mode=" . $classe['mode'] . "\n";
            $body .= "directory=" . $classe['directory'] . "\n\n";
        }

        file_put_contents('/etc/asterisk/snep/snep-musiconhold.conf', $header . $body);
    }

    // --------------------------------------------------------------------------------------------
    // Functions for syncronize files with database
    // --------------------------------------------------------------------------------------------
    /**
     * Get file list MOH sounds of the directory and update database
     * @param array $file
     */
    public function syncFiles() {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('sounds')
                ->where('sounds.tipo = ?', 'MOH');

        try {
            $stmt = $db->query($select);
            $sounds = $stmt->fetchAll();
        } catch (Exception $e) {
            return false;
        }

        $_sound = array();
        foreach ($sounds as $sound) {
            $_sound[$sound['arquivo']] = $sound['arquivo'];
        }


        $allClasses = Snep_SoundFiles_Manager::getClasses();
        $classesFolder = array();

        foreach ($allClasses as $id => $xclass) {


            $classesFolder[$id]['name'] = $xclass['name'];
            $classesFolder[$id]['directory'] = $xclass['directory'];

            if (file_exists($xclass['directory'])) {

                $allFiles = array();
                $files = array();
                foreach (scandir($xclass['directory']) as $thisClass => $file) {

                    if (!preg_match("/^\.+.*/", $file)) {

                        if (!preg_match('/^tmp+.*/', $file)) {

                            if (!preg_match('/^backup+.*/', $file)) {

                                if (!in_array($file, array_keys($allClasses))) {

                                    if (!in_array($file, $_sound)) {

                                        $newfile = array('arquivo' => $file,
                                            'descricao' => $file,
                                            'data' => new Zend_Db_Expr('NOW()'),
                                            'language' => $this->lang,
                                            'tipo' => 'MOH',
                                            'secao' => $id);


                                        Snep_SoundFiles_Manager::addClassFile($newfile);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Add a sound file of direcoty in syncronize
     * @param array $file
     */
    public function addSounds($file) {

        $db = Zend_Registry::get('db');

        $insert_data = array('arquivo' => $file,
            'descricao' => substr($file, 0, -4),
            'data' => new Zend_Db_Expr('NOW()'),
            'language' => $this->lang, 
            'tipo' => 'AST');

        try {
            $db->insert('sounds', $insert_data);
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return $e;
        }
    }


    /**
     * Get sound files
     * @return <array>
     */
    public function getSounds() {

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from('sounds')
                ->where("sounds.tipo = ?", 'AST')
                ->where("sounds.language = ?", $this->lang);

        $stmt = $db->query($select);
        $sounds = $stmt->fetchall();

        return $sounds;
    }


    // --------------------------------------------------------------------------------------------
    // Generic functions for Sound Files and MOH
    // --------------------------------------------------------------------------------------------
    /**
     * Verify file of sound in directories
     * @param <string> $name
     * @param <boolean> $full
     * @return <array> $result
     */
    public function verifySoundFiles($name, $full = false) {

        $sound_path = Zend_Registry::get('config')->system->path->asterisk->sounds;
        $web_path = Zend_Registry::get('config')->system->path->web;
        $lang = $this->lang ;
         if ($this->lang === "en") {
             $lang = "";
         }
        $result = array();

        if (file_exists($sound_path)) {
            if (file_exists($sound_path . '/' . $lang . '/'. $name)) {
                if ($full) {
                    $result['fullpath'] = $sound_path . '/' . $lang . '/'. $name;
                } else {
                    $result['fullpath'] = $web_path . '/sounds/' . $this->lang . '/'. $name;;
                }
            }
            if (file_exists($sound_path . '/' . $this->lang . '/backup/' . $name)) {
                if ($full) {
                    $result['backuppath'] = $sound_path . '/' . $lang . '/backup/' . $name;
                } else {
                    $result['backuppath'] = $web_path . '/sounds/' . $this->lang . '/backup/'. $name; ;
                }
            }
        }
        return $result;
    }

    /**
     * Verify file type
     * @param string
     * @return booolean
     */
    public function checkType($extension) {
        if($extension != "wav"  && $extension != "gsm") {
            return false ;
         } else {
            return true ;
        }  
    }

    /**
     * Parsing name file
     * @param <string> $name
     * @return <string> $name
     */
    public function parseName($name) {
    
        $invalid = array('â', 'ã', 'á', 'à', 'ẽ', 'é', 'è', 'ê', 'í', 'ì', 'ó', 'õ', 'ò', 'ú', 'ù', 'ç', " ", '@', '!');
        $valid = array('a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'o', 'u', 'u', 'c', "_", '_', '_');

        return str_replace($invalid, $valid, $name);
    }
    
    /**
     * Insert on table logs_users the data of sound files
     * @param <string> $acao
     * @param <array> $add
     */
    function insertLogSounds($acao, $add) {

        $db = Zend_Registry::get("db");

        $ip = $_SERVER['REMOTE_ADDR'];
        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();

        $insert_data = array('hora' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'idusuario' => $username,
            'cod' => $add["arquivo"],
            'param1' => $add["descricao"],
            'param2' => $add["tipo"],
            'value' => "SOM",
            'tipo' => $acao);

        $db->insert('logs_users', $insert_data);
    }

}
