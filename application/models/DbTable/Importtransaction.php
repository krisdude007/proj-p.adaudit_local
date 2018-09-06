<?php

class Application_Model_DbTable_Importtransaction extends Zend_Db_Table_Abstract
{
    protected $_name = 'import_transaction';

        public function get($id) {
            $sql = "SELECT *
            FROM import_transaction
            WHERE id = $id
            AND deleted = 0";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchRow($query);
    }  
}