<?php

class Application_Model_DbTable_Ata extends Zend_Db_Table_Abstract {

    protected $_name = 'ata';
    protected $_primary = 'ata_id';

   function getAta()
    {
            $sql = "SELECT ata_id as value,
                        concat(ata_code, ' - ', ata_txt) as valueText
                FROM ata
                WHERE deleted = 0 
                ORDER BY ata_code
        ";
        //var_dump($sql);exit;
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
    
    public function delete($adId) {
        
        $sql = "delete from ad_fleet where ad_id = $adId";
        
        $query = $this->getAdapter()->query($sql);
        $query->execute();
        //$where = $this->getAdapter()->quoteInto('ad_id = ?', $adId);echo $where;exit;
        //$this->delete($where);
    }
    
}
