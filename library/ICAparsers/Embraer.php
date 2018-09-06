<?php
require_once 'ICAparsers.php';
// Be sure to 'require_once' this file in the controller where you are calling it from.
class ICAparsers_Embraer extends ICAparsers{

  public function parseRow($storageTable, $document_id, $row_id)
  {
      $methodTable = new Application_Model_DbTable_Intervalmethod();

      $storedData = $storageTable->fetchRow(
        $storageTable->select('id','int_repeat','int_threshold','effectivity')
        ->where('document_id = ?', $document_id)
        ->where('id = ?', $row_id));

var_dump($storedData['int_threshold'], $storedData['int_repeat']);

      foreach ($storedData as $key => $value) {
          if($key == 'int_threshold'){
            $parsed = $this->parseRequirements($row_id, $value);
            $method_id = $methodTable->getID('Interval Threshold');
            var_dump($method_id,$parsed);
            $this->saveData('int_threshold', $method_id, $parsed, $document_id, $row_id);
          }
          if($key == 'int_repeat'){
            $parsed = $this->parseRequirements($row_id, $value);
            $method_id = $methodTable->getID('Interval Repeat');
            var_dump($method_id,$parsed);
            $this->saveData('int_repeat', $method_id, $parsed, $document_id, $row_id);
          }
        }   
      return;
  }


    public function init($storageTable, $document_id){
      $methodTable = new Application_Model_DbTable_Intervalmethod();

      $storedData = $storageTable->fetchAll(
        $storageTable->select('id','int_repeat','int_threshold','effectivity')
        ->where('document_id = ?', $document_id));

      foreach ($storedData as $key => $value) {
          if($value['int_threshold']){
            $parsed = $this->parseRequirements($value['id'], $value['int_threshold']);
            $method_id = $methodTable->getID('Interval Threshold');
            $this->saveData('int_threshold', $method_id, $parsed, $document_id, $value['id']);
          }
          if($value['int_repeat']){
            $parsed = $this->parseRequirements($value['id'], $value['int_repeat']);
            $method_id = $methodTable->getID('Interval Repeat');
            $this->saveData('int_repeat', $method_id, $parsed, $document_id, $value['id']);
          }
        }  
      return;
    }
}