<?php

class Application_Model_DbTable_Fleet extends Zend_Db_Table_Abstract {

    protected $_name = 'fleet';
    protected $_primary = 'fleet_id';

//public function getApplicabilityOptions($id)
//    {
//                
//                $sql = "SELECT fleet_id as value,
//                       fleet_txt as valueText
//                FROM fleet
//                WHERE 
//                deleted = 0 
//                ORDER BY fleet_txt
//                ";
//                
//                $query = $this->getAdapter()->quoteInto($sql,'');
//                return $this->getAdapter()->fetchAll($query);
//    }
    
public function getFleets()
    {
                
                $sql = "SELECT fleet_id as value,
                       fleet_txt as valueText
                FROM fleet
                WHERE 
                    deleted = 0 
                ORDER BY fleet_txt
                ";
                
                $query = $this->getAdapter()->quoteInto($sql,'');
                return $this->getAdapter()->fetchAll($query);
    }
    
    public function getFleetsByAdId($id)
    {   
        $id = (is_array($id)) ? $id['id'] : $id;
                
                $sql = "SELECT
                        b.fleet_id AS `value`,
                        c.fleet_txt as valueText
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
                                ";
                
                $query = $this->getAdapter()->quoteInto($sql,'');//echo $query;exit;
                return $this->getAdapter()->fetchAll($query);
    }
    
     public function getFleetListByAdId($id)
    {   
        $id = (is_array($id)) ? $id['id'] : $id;
                
                $sql = "SELECT
                        a.ad_id,
                        group_concat(c.fleet_txt, '') as fleet_txt
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
                GROUP by a.ad_id
                ";
                
                $query = $this->getAdapter()->quoteInto($sql,'');//echo $query;exit;
                return $this->getAdapter()->fetchAll($query);
    }
    
    public function getFleetByFleetTxt($fleetTxt) {
        $sql = "select * from fleet where fleet_txt = '$fleetTxt'";
                
                $query = $this->getAdapter()->quoteInto($sql,'');echo $query;
                return $this->getAdapter()->fetchAll($query);
    }

    
//    
//    public function getApplicabilityOptionsById($id) 
//    {
//            $sql = "SELECT
//                    b.fleet_id AS value,
//                    group_concat(b.fleet_txt , '') AS valueText
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
//            ) b ON a.ad_id = b.ad_id 
//            WHERE
//                    a.ad_id = $id
//            AND a.deleted = 0
//            GROUP BY
//                    a.ad_id";
//                
//                $query = $this->getAdapter()->quoteInto($sql,'');
//                return $this->getAdapter()->fetchAll($query);
//    }
}
