<?php

class Application_Model_DbTable_Changehistory extends Zend_Db_Table_Abstract
{

    protected $_name = 'change_history';
    protected $_primary = 'id';
   
   public function changed($data)
   {
        $table = new Application_Model_DbTable_Changehistory();
        $id = $table->insert($data);
        return $id;
   }
}
