<?php

class Application_Model_DbTable_AdStatus extends Zend_Db_Table_Abstract {

    protected $_name = 'ad_status';
    protected $_primary = 'ad_status_id';

    function getAdStatus()
    {
            $sql = "SELECT ad_status_id as value,
                        ad_status_txt as valueText
                FROM ad_status
                WHERE deleted = 0 
                ORDER BY ad_status_txt
        ";
        //var_dump($sql);exit;
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
    
    public function getAdStatusByText($text) {
         $sql = "SELECT ad_status_id
                FROM ad_status
                WHERE ad_status_txt = '$text'  
                AND deleted = 0 
        ";
        
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
}
