<?php

class Application_Model_DbTable_AdImport extends Zend_Db_Table_Abstract {

    protected $_name = 'ad_import';
    protected $_primary = 'ad_import_id';
    
    public function getRecs() {
         $sql = "SELECT * from ad_import";

        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }
    
    public function import() {
        $sql = "SELECT
	a.ad_id ,
	b.ad_import_id ,
	b.ad_fleet
        FROM
	ad a ,
	ad_import b
        WHERE
	a.ad_txt = b.ad_txt";

        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }
}
