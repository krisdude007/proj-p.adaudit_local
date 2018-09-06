<?php

class Application_Model_DbTable_AdParaImport extends Zend_Db_Table_Abstract {

    protected $_name = 'ad_para_import';
    protected $_primary = 'ad_paragraph_id';
    
    public function getRecs() {
         $sql = "SELECT * from ad_para_import";

        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }
    
    public function import() {
        $sql = "SELECT
	a.ad_id ,
	b.*
        FROM
	ad a ,
	ad_para_import b
        WHERE
	a.ad_txt = b.ad_txt";

        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }
}
