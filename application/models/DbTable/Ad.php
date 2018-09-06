<?php

class Application_Model_DbTable_Ad extends Zend_Db_Table_Abstract {

    protected $_name = 'ad';
    protected $_primary = 'ad_id';

    public function getName() {
        return $this->_name;
    }

    public function getAdById($id) {

        $sql = "SELECT
                        a.ad_id ,
                        a.ad_txt ,
                        a.ad_txt ,
                        a.ad_desc ,
                        a.effective_date ,
                        a.reference_txt ,
                        a.ata_id ,
                        b.fleet_id ,
                        c.affected_ad ,
                        d.ad_status_id ,
                        d.ad_status_txt ,
                        a.is_active ,
                        a.is_active_comment
            FROM
                ad a ,
                (
		SELECT
			a.ad_id ,
			group_concat(c.fleet_id , '') AS fleet_id
		FROM
			ad a ,
			ad_fleet b ,
			fleet c
		WHERE
			a.ad_id = $id
		AND a.ad_id = b.ad_id
		AND b.fleet_id = c.fleet_id
		AND a.deleted = 0
		AND b.deleted = 0
		AND c.deleted = 0
		GROUP BY
			a.ad_id
	) b
            LEFT JOIN(
	SELECT
		a.ad_id ,
		group_concat(c.ad_id , '') AS affected_ad
	FROM
		ad a ,
		ad_affected b ,
		ad c
	WHERE
		a.ad_id = $id
	AND a.ad_id = b.ad_id
	AND b.affected_ad_id = c.ad_id
	AND a.deleted = 0
	AND b.deleted = 0
	AND c.deleted = 0
	GROUP BY
		a.ad_id
            ) c ON b.ad_id = c.ad_id ,
             ad_status d
            WHERE
                    a.ad_id = $id
            AND a.ad_status_id = d.ad_status_id
            AND a.deleted = 0
            AND d.deleted = 0
            GROUP BY
	a.ad_id
            ";
        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchRow($query);
    }

    public function getAdsByCustomerId($customerId) {
        $sql = "SELECT
                    a.ad_id ,
                    a.ad_txt ,
                    a.ad_desc ,
                    group_concat(b.fleet_txt , '') AS fleet_txt,
                    c.ad_status_phase2
            FROM
                    ad a
            LEFT JOIN(
                    SELECT
                            a.ad_id ,
                            a.fleet_id ,
                            b.fleet_txt
                    FROM
                            ad_fleet a ,
                            fleet b
                    WHERE
                            a.fleet_id = b.fleet_id
                    AND a.deleted = 0
                    AND b.deleted = 0
            ) b ON a.ad_id = b.ad_id ,
             ad_customer c
            WHERE
                    a.ad_id = c.ad_id
            AND c.customer_id = $customerId
            AND a.deleted = 0
            AND c.deleted = 0
            GROUP BY
                    a.ad_id";

        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getAdsByFleetId($fleets) {

        $sql = "SELECT
	a.*, group_concat(c.fleet_id , '') AS fleet_id ,
	group_concat(c.fleet_txt , '') AS fleet_txt
        FROM
	(
		SELECT
			a.ad_id ,
			a.ad_txt ,
			a.ad_desc ,
			d.ad_status_txt ,
			a.is_active ,
			a.is_active_comment ,
			a.is_superceding ,
                        a.effective_date,
                        a.reference_txt,
                        a.ata_id,
			group_concat(e.ad_id , '') AS superceded_ad_id
		FROM
			ad a
		LEFT JOIN ad e ON a.ad_id = e.superceded_ad_id
		AND e.deleted = 0 ,
		ad_fleet b ,
		fleet c ,
		ad_status d
	WHERE
		a.ad_id = b.ad_id
	AND b.fleet_id = c.fleet_id
	AND c.fleet_id IN($fleets)
	AND a.ad_status_id = d.ad_status_id
	AND a.deleted = 0
	AND b.deleted = 0
	AND c.deleted = 0
	GROUP BY
		a.ad_id
	) a ,
            ad_fleet b ,
            fleet c
        WHERE
	a.ad_id = b.ad_id
        AND b.fleet_id = c.fleet_id
        GROUP BY
            a.ad_id
        ORDER BY
            a.ad_txt
                ";

        $query = $this->getAdapter()->quoteInto($sql, ''); //echo $query;exit;
        return $this->getAdapter()->fetchAll($query);
    }

    public function getAdLookupByFleet($id) {
        $id = (is_array($id)) ? $id['id'] : $id;

        $sql = "SELECT
                        distinct a.ad_id AS `value`,
                        a.ad_txt as valueText
                FROM
                        ad a ,
                        ad_fleet b 
                WHERE
                b.fleet_id in ($id)
                AND a.ad_id = b.ad_id
                AND a.deleted = 0
                AND b.deleted = 0
                 order by a.ad_txt";

        $query = $this->getAdapter()->quoteInto($sql, ''); //echo $query;exit;
        return $this->getAdapter()->fetchAll($query);
    }

    public function getSupercededAdsByAdId($id) {
        $sql = "SELECT
                        ad_id 
                FROM
                        ad
                WHERE 
                superceded_ad_id = $id
                AND deleted = 0
                ";

        $query = $this->getAdapter()->quoteInto($sql, ''); //echo $query;exit;
        return $this->getAdapter()->fetchAll($query);
    }

//    public function getAds() {
//
//        $sql = "SELECT
//                    a.ad_id ,
//                    a.ad_txt ,
//                    a.ad_desc ,
//                    group_concat(b.fleet_txt , '') AS fleet_txt,
//                    c.ad_status_phase2
//            FROM
//                    ad a
//            LEFT JOIN(
//                    SELECT
//                            a.ad_id ,
//                            a.fleet_id ,
//                            b.fleet_txt
//                    FROM
//                            ad_fleet a ,
//                            fleet b
//                    WHERE
//                            a.fleet_id = b.fleet_id
//                    AND a.deleted = 0
//                    AND b.deleted = 0
//            ) b ON a.ad_id = b.ad_id ,
//             ad_customer c
//            WHERE
//                    a.ad_id = c.ad_id
//            AND c.customer_id = 1
//            AND a.deleted = 0
//            AND c.deleted = 0
//            GROUP BY
//                    a.ad_id";
//
//        $query = $this->getAdapter()->quoteInto($sql, '');
//        return $this->getAdapter()->fetchAll($query);
//    }
//    public function getAdItem($id) {
//        $sql = "SELECT
//                    a.ad_id ,
//                    a.ad_txt ,
//                    a.ad_desc ,
//                    a.ad_status_txt, a                
//                    FROM
//                    ad a ,
//                    ad_customer ac
//                WHERE
//                    a.ad_id = ac.ad_id
//                AND a.ad_id = $id 
//                AND a.deleted = 0 
//                AND ac.deleted = 0
//                AND ac.customer_id = 1
//                ORDER BY
//                    a.ad_txt";
//
//        $query = $this->getAdapter()->quoteInto($sql, '');
//        $result = $this->getAdapter()->fetchRow($query);
//        return $result;
//    }

    public function getAdOptions() {
        $sql = "SELECT ad_id as value,
                       ad_txt as valueText
                FROM ad
                WHERE deleted = 0
                ORDER BY ad_txt
                ";
        // var_dump($sql);exit;
        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getApplicabilityOptions($params = null) {
        switch ($params['type']):
            case "aircraft":
                $sql = "SELECT id as value,
                       aircraft_model as valueText
                FROM aircraft
                WHERE deleted = 0
                ORDER BY aircraft_model
                ";
                break;
            case "engine":
                $sql = "SELECT id as value,
                       engine_model as valueText
                FROM engines
                WHERE deleted = 0
                ORDER BY engine_model
                ";
                break;
            case "propellor":
                $sql = "SELECT id as value,
                       propellor_model as valueText
                FROM propellors
                WHERE deleted = 0
                ORDER BY propellor_model
                ";
                break;
        endswitch;

        // var_dump($sql);exit;
        $query = $this->getAdapter()->quoteInto($sql, '');
        //var_dump($this->getAdapter()->fetchAll($query)); exit;
        return $this->getAdapter()->fetchAll($query);
    }

    public function newAd($data) {
        $adTable = new Application_Model_DbTable_Ad();
        $adFleetTable = new Application_Model_DbTable_AdFleet();
        $adCustomerTable = new Application_Model_DbTable_AdCustomer();
        $adStatusTable = new Application_Model_DbTable_AdStatus();
        $affectedAdTable = new Application_Model_DbTable_AdAffected();

        $urlPath = isset($_SERVER['HTTPS']) ? "https://" : "http://" . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $waitingStatusRec = $adStatusTable->getAdStatusByText('Waiting');
        $waitingStatusId = isset($waitingStatusRec[0]) ? $waitingStatusRec[0]['ad_status_id'] : NULL;
        $adId = 0;
        //var_dump($data);exit;
        try {
            $adId = $adTable->insert([
                'ad_txt' => $data['ad_txt'][0],
                'ad_desc' => $data['ad_desc'][0],
                'effective_date' => $data['effective_date'][0],
                'reference_txt' => $data['reference_txt'][0],
                'ata_id' => $data['ata_id'][0],
                'crea_usr_id' => $data['user_id'],
                'crea_dtm' => date('Y/m/d H:i:s', time()),
                'updt_usr_id' => $data['user_id'],
                'ad_status_id' => $waitingStatusId,
                'url_path' => $urlPath,
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            if ($adId > 0) {
                $adFleetTable->add($adId, $data['applicability'], $data['user_id']);
                $affectedAdTable->addAffectedAds($adId, $data['affectedAds'], $data['user_id']);
            }
        } catch (Exception $e) {
            return $e;
        }
        if ($adId > 0) {
//            $adCustomerTable->insert([
//                'ad_id'         => $ad_id,
//                'customer_id'        => 1,
//                'crea_usr_id'    => $data['user_id'],
//                'updt_dtm'       => date('Y/m/d H:i:s',time()),
//                'updt_usr_id'    => $data['user_id'],
//                'ad_status_phase2' => 'Waiting',
//            ]);
        }
        return;
    }

    public function setSupercededAd($parentAdId, $userId, $reset) {
        
        try {
            $childAds = $this->getSupercededAdsByAdId($parentAdId); 
            
            if ($reset == true) {
                $parentAdId = NULL;
            }
            foreach ($childAds as $childAd) {
                $updateSupAd = $this->updateSupercededAd($parentAdId, $childAd['ad_id'], $userId);
            }
        } catch (Exception $e) {//var_dump($e);exit;
        }
    }

    public function getUsers($id) {
        $atbdb = Zend_Registry::get('config')->resources->multidb->shared->dbname;
        $sql = "SELECT
	a.user_id ,
	a.user_full_nm
        FROM
                $atbdb.user a ,
                $atbdb.user_application b
        WHERE
                a.user_id = b.user_id
        AND b.application_id = $id
        AND a.deleted = 0
        AND b.deleted = 0
        ORDER BY
	a.user_full_nm";
        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
//        $temp = array();
//        $string = '';
//        foreach(array_values($result) as $user){
//            $temp[] = $user['user_id'];
//            $temp[] = $user['user_full_nm'];
//        }
//        return $temp;
    }

    public function setIsSuperceding($adId, $flag, $userId) {
        $adRec = $this->find($adId)->toArray();
        $oldFlag = $adRec[0]['is_superceding'];

        try {
            $where = array();
            $where[] = $this->getAdapter()->quoteInto('ad_id = ? ', $adId);

            //forcing the updt_dtm to make sure, if is_superceding record is not modified, it still enters the condition
            $urlPath = isset($_SERVER['HTTPS']) ? "https://" : "http://" . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $data = array('is_superceding' => $flag,
                'updt_usr_id' => $userId,
                'updt_dtm' => date('Y/m/d H:i:s', time()),
                'url_path' => $urlPath,
                'ip_address' => $_SERVER['REMOTE_ADDR']);
            $result = $this->update($data, $where);
            return true;
        } catch (Exception $e) {//var_dump($e);
            return false;
        }
    }

    public function updateSupercededAd($parentAdId, $childAdId, $userId) {

        $where = array();
        $where[] = $this->getAdapter()->quoteInto('ad_id = ? ', $childAdId);
        //var_dump($childAdId);
        $curParentAdRec = $this->getSupercededAdsByAdId($childAdId); //var_dump($curParentAdRec);
        $curParentAdId = !empty($curParentAdRec) ? $curParentAdRec[0]['ad_id'] : NULL;
        $urlPath = isset($_SERVER['HTTPS']) ? "https://" : "http://" . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $data = array('superceded_ad_id' => $parentAdId,
            'updt_usr_id' => $userId,
            'updt_dtm' => date('Y/m/d H:i:s', time()),
            'url_path' => $urlPath,
            'ip_address' => $_SERVER['REMOTE_ADDR']);

        $result = $this->update($data, $where);
    }

    public function getSupercedingAdId($adId) {
        $adRec = $this->find($adId)->toArray();
        return $adRec[0]['superceded_ad_id'];
    }

}
