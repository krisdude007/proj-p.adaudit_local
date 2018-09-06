<?php

class Application_Model_DbTable_AdFleet extends Zend_Db_Table_Abstract {

    protected $_name = 'ad_fleet';
    protected $_primary = 'ad_fleet_id';

    public function add($adId, $fleetsArr, $userId) {
        
        foreach($fleetsArr as $fleet) {

             $this->insert([
                 'ad_id'         => $adId,
                 'fleet_id'        => $fleet,
                 'crea_usr_id'    => $userId,
                 'crea_dtm'       => date('Y/m/d H:i:s',time()),
                 'updt_usr_id'    => $userId,
             ]);
            }
    }
    
    public function delete($adId) {
        
        $sql = "delete from ad_fleet where ad_id = $adId";
        
        $query = $this->getAdapter()->query($sql);
        $query->execute();
        //$where = $this->getAdapter()->quoteInto('ad_id = ?', $adId);echo $where;exit;
        //$this->delete($where);
    }
    
}
