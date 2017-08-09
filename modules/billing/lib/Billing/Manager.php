<?php

/**
 * Class to manager Bills
 *
 * @see Billing/Billing
 *
 * @category  Snep
 * @package   Billing
 * @copyright Copyright (c) 2013 OpenS Tecnologia
 * @author    Douglas Conrad <conrad@opens.com.br>
 *
 */
class Telcos_Manager {

    /**
    * getAll - Get all bills
    * @return <array>
    */
    public function getAll(){
      $db = Zend_Registry::get('db');
      $select = $db->select()->from('billing');
      $telcos = $db->query($select)->fetchAll();

      return $telcos;

    }

    /**
    * add <array> area, price, telco
    * @return <$id>
    */
    public function add($bill){
      $db = Zend_Registry::get('db');

      $insert_data = array(
        "area" => $bill['area'],
        "price" => $bill['price'],
        "telco" => $bill['telco']
      );

      try {
        $db->insert('billing', $insert_data);

        return intval($db->lastInsertId());

      }catch(Exception $e){
        return $e;
      }

    }

    /**
     * Method to get a telco by id
     * @param int $id
     * @return Array
     */
    public function get($id) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from('billing')
            ->where("id = ?", $id);

        $stmt = $db->query($select);
        $telco = $stmt->fetch();

        return $telco;

    }


    /**
     * Method to remove a telco
     * @param int $id
     */
    public function remove($id) {

        $db = Zend_Registry::get('db');

        $db->beginTransaction();
        $db->delete('billing', "id = '$id'");

        try {
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Method to update a telco
     * @param <array>
     */
    public function update($bill) {

        $db = Zend_Registry::get('db');
        $update_data = array(
          "area" => $bill['area'],
          "price" => $bill['price'],
          "telco" => $bill['telco']
        );
        $db->beginTransaction();
        $db->update("billing", $update_data, "id = '{$bill['id']}'");
        try {
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }

    }

}
