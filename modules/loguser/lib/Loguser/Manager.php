<?php

/**
 * Class to manager logs
 *
 * @see Loguser
 *
 * @category  Snep
 * @package   Loguser
 * @copyright Copyright (c) 2013 OpenS Tecnologia
 * @author    Tiago Zimmermann <tiago.zimmermann@opens.com.br>
 *
 */
class Loguser_Manager {

    /**
    * getAll - Get all logs by Category
    * @param <array> $initial_date, $final_date, $Category
    * @return <array>
    */
    public function getAll($initial_date, $final_date, $category){
      $db = Zend_Registry::get('db');
      $select = $db->select()->from('logs_users')
      ->where('datetime >= ?', $initial_date)
      ->where('datetime <= ?', $final_date);

      if($category != "all"){
        $select = $select->where('`table` = ?', $category);
      }
      $select = $select->order('datetime DESC');

      return $db->query($select)->fetchAll();

    }

    /**
     * verlog - Monta log para usuário
     * @param <array> $dados
     * @return <string>
     */
    public function logview($form) {

        if (isset($form)) {
            $start_date = $form["initDay"];
            $data_fim = $form["finalDay"];

            $data_inicial = explode(" ", $data_inicio);
            $data_final = explode(" ", $data_fim);

            $data_inicio = implode(preg_match("~\/~", $data_inicial[0]) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $data_inicial[0]) == 0 ? "-" : "/", $data_inicial[0])));
            $data_fim = implode(preg_match("~\/~", $data_final[0]) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $data_final[0]) == 0 ? "-" : "/", $data_final[0])));

            $data_inicial = $data_inicio . " " . $data_inicial[1] . ":00";
            $data_final = $data_fim . " " . $data_final[1] . ":59";

            $db = Zend_Registry::get('db');
            $sql = "SELECT hora, ip, idusuario, acao, idregratronco from logs WHERE ( logs.hora >= '$data_inicial' AND logs.hora <= '$data_final') order by hora asc";
            $stmt = $db->query($sql);
            $logs = $stmt->fetchAll();

            $result = "";
            foreach ($logs as $item => $value) {
                if ($value['acao'] == "Editou parametros") {
                    $result .= "Hora: " . $value['hora'] . " | IP : " . $value['ip'] . " | ID do usuario : " . $value['idusuario'] . " | Acao : " . $value['acao'] . "<br>";
                } else {
                    $result .= "Hora: " . $value['hora'] . " | IP : " . $value['ip'] . " | ID do usuario : " . $value['idusuario'] . " | Acao : " . $value['acao'] . " | ID da acao : " . $value['idregratronco'] . "<br>";
                }
            }
            return $result;
        }
    }
}
