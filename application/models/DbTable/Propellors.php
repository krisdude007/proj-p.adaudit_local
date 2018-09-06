<?php

class Application_Model_DbTable_Propellors extends Zend_Db_Table_Abstract
{

    protected $_name = 'propellors';
    protected $_primary = 'id';

    function getAllEngines()
    {
        $sql = "SELECT *
                FROM engines
                ORDER BY engine_model
                ";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    function getAllPropellorData()
    {
        $sql = "SELECT *
                FROM engines a
                  LEFT JOIN manufacturer b
                    ON a.engine_manufacturer_id = b.id
                  LEFT JOIN tcds c
                    ON a.engine_tcds_id = c.id
                WHERE a.deleted = 0
                ORDER BY a.engine_model";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    function getEngine($id)
    {
        $sql = "SELECT *
                FROM engines
                WHERE manufacturer_id = $id
                AND deleted = 0
                ORDER BY engine_model
                ";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    function getEngineByID($id)
    {
        $sql = "SELECT *
                FROM engines
                WHERE id = $id
                ";
           // var_dump($sql);
        $query = $this->getAdapter()->quoteInto($sql,'');

        return $this->getAdapter()->fetchAll($query);
    }

    function getEngineByManufacturer($id)
    {
        $sql = "SELECT 
                    a.id,
                    a.engine_manufacturer_id,
                    a.engine_tcds_id,
                    a.engine_model,
                    b.manufacturer_id,
                    b.manufacturer_name,
                    c.tcds_id,
                    c.tcds,
                    c.tcds_revision,
                    c.tcds_date,
                    c.tcds_title
                FROM   engines a
                       LEFT JOIN manufacturer b
                              ON a.`engine_manufacturer_id` = b.id
                       LEFT JOIN tcds c
                              ON a.engine_tcds_id = c.id
                WHERE  a.engine_manufacturer_id = $id
                AND a.deleted = 0
                ORDER  BY a.engine_model
        ";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    function getEngineModels($term = null)
    {
        if($term){
            $sql = "SELECT engine_model
                FROM engines
                WHERE engine_model LIKE '%$term%'
                AND deleted = 0
                ";
        }else{
            $sql = "SELECT engine_model
                FROM engines
                WHERE deleted = 0
                ";
        }
        // var_dump($sql);exit;
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);       
    }
}
