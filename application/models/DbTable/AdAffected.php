<?php

class Application_Model_DbTable_AdAffected extends Zend_Db_Table_Abstract {

    protected $_name = 'ad_affected';
    protected $_primary = 'ad_affected_id';

    public function addAffectedAds($adId, $affectedArr, $userId) {

        foreach ($affectedArr as $affectedAdId) {
            $res = $this->addAffectedAd($adId, $affectedAdId, $userId);
        }
    }

    public function addAffectedAd($adId, $affectedAdId, $userId) {
        return $this->insert([
                    'ad_id' => $adId,
                    'affected_ad_id' => $affectedAdId,
                    'crea_usr_id' => $userId,
                    'crea_dtm' => date('Y/m/d H:i:s', time()),
                    'updt_usr_id' => $userId,
        ]);
    }
    
    public function getAffectedAdByAd($id) {
                $sql = "SELECT
                    c.ad_id ,
                    c.ad_txt
                FROM
                    ad a ,
                    ad_affected b ,
                    ad c
                WHERE
                a.ad_id = $id
                    AND .a.ad_id = b.ad_id
                    AND b.affected_ad_id = c.ad_id
                    AND a.deleted = 0
                    AND b.deleted = 0
                    AND c.deleted = 0";

        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchRow($query);
    }

    public function delete($adId) {

        $sql = "delete from ad_affected where ad_id = $adId";

        $query = $this->getAdapter()->query($sql);
        $query->execute();
        //$where = $this->getAdapter()->quoteInto('ad_id = ?', $adId);echo $where;exit;
        //$this->delete($where);
    }

}
