<?php
require_once 'ICAparsers.php';
// Be sure to 'require_once' this file in the controller where you are calling it from.
class ICAparsers_Airbus extends ICAparsers{


    public function parseRow($storageTable, $document_id, $row_id){
      $methodTable = new Application_Model_DbTable_Intervalmethod();

        $storedData = $storageTable->fetchRow(
            $storageTable->select('id','interval','int_threshold','int_repeat','applicability')
                ->where('document_id = ?', $document_id)
                ->where('id = ?', $row_id));

        foreach ($storedData as $key => $value) {
            if($key == 'interval'){
                $intervalData = $this->extractIntervalData($value, $row_id, $key);
                $method_id     = $methodTable->getID('Interval');
                $this->saveData('interval', $method_id, $intervalData['interval'], $document_id, $row_id);
                $method_id     = $methodTable->getID('Threshold');
                $this->saveData('threshold', $method_id, $intervalData['threshold'], $document_id, $row_id);
            }
            if($key == 'int_threshold'){
                $parsed = $this->parseRequirements($row_id, $value);
                $method_id = $methodTable->getID('Interval Threshold');
                $this->saveData('int_threshold', $method_id, $parsed, $document_id, $row_id);
              }
              if($key == 'int_repeat'){
                $parsed = $this->parseRequirements($row_id, $value);
                $method_id = $methodTable->getID('Interval Repeat');
                //var_dump($parsed); exit;
                $this->saveData('int_repeat', $method_id, $parsed, $document_id, $row_id);
              }
        }
        return;
    }
    public function init($storageTable, $document_id){
      $methodTable = new Application_Model_DbTable_Intervalmethod();

        $storedData = $storageTable->fetchAll(
            $storageTable->select('id','interval','int_threshold','int_repeat','applicability')
                ->where('document_id = ?', $document_id));

        foreach ($storedData as $key => $value) {
            if($value['interval']){
                $intervalData = $this->extractIntervalData($value['interval'], $value['id'], $key);
                $method_id     = $methodTable->getID('Interval');
                $this->saveData('interval', $method_id, $intervalData['interval'], $document_id, $value['id']);
                $method_id     = $methodTable->getID('Threshold');
                $this->saveData('threshold', $method_id, $intervalData['threshold'], $document_id, $value['id']);
            }
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

    /**
     * Extract the I: and T: data from the text string
     * @param  [type] $text       [description]
     * @param  [type] $id         [description]
     * @param  [type] $field_name [description]
     * @return array             [description]
     */
    public function extractIntervalData($text, $id, $field_name)
    {
        $result    = array(
          'id' => $id,
          'interval'  => array(),
          'threshold' => array()
        );

        $threshold = false;
        $interval  = false;

        $interval  = strpos($text, "I:");
        $threshold = strpos($text, "T:");

        if((is_numeric($interval)) && (is_numeric($threshold))) {
            if($interval > $threshold) {
                $iString   = trim(str_replace("OR", "", str_replace("I:", "", substr($text, $interval))));
                $tString   = trim(str_replace("OR", "", str_replace("T:", "", substr($text, 0, $interval))));
            } else {
                $iString   = trim(str_replace("OR", "", str_replace("I:", "", substr($text, 0, $threshold))));
                $tString   = trim(str_replace("OR", "", str_replace("T:", "", substr($text, $threshold))));
            }
            $result['interval']  = $this->parseRequirements($id, $iString);
            $result['threshold'] = $this->parseRequirements($id, $tString);

        } else if((is_numeric($interval)) && (!is_numeric($threshold))) {
            $iString   = trim(str_replace("OR", "", str_replace("I:", "", substr($text, $interval))));
            $result['interval']  = $this->parseRequirements($id, $iString);

        } else if((!is_numeric($interval)) && (is_numeric($threshold))) {
            $tString   = trim(str_replace("OR", "", str_replace("T:", "", substr($text, $threshold))));
            $result['threshold'] = $this->parseRequirements($id, $tString);
        } else if((!is_numeric($interval)) && (!is_numeric($threshold)) && ($text != "")) {
            $orString   = trim(str_replace("OR", "", $text));
            $result[$field_name] = $this->parseRequirements($id, $orString);
        }
        return $result;
    }
}