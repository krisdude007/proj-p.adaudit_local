<?php

class Application_Model_DbTable_Aircraft extends Zend_Db_Table_Abstract
{

    protected $_name = 'aircraft';
    protected $_primary = 'id';

    function getAicraftSelect()
    {
        $sql = "SELECT id as value, aircraft_model as valueText
                FROM aircraft
                WHERE deleted = 0
                ORDER BY aircraft_model";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);      
    }
    function getAicraftSelectByManufacturer($id)
    {
        $sql = "SELECT id as value, aircraft_model as valueText
                FROM aircraft
                WHERE deleted = 0
                AND aircraft_manufacturer_id = $id
                ORDER BY aircraft_model";
        $query = $this->getAdapter()->quoteInto($sql,'');
        //var_dump($sql);exit; 
        return $this->getAdapter()->fetchAll($query);      
    }
    function getAircraft()
    {
        $sql = $this->allAircraftDataSql();
        $sql .= " ORDER BY aircraft_model";
        $query = $this->getAdapter()->quoteInto($sql,'');
        // var_dump($this->getAdapter()->fetchAll($query));exit;
        return $this->getAdapter()->fetchAll($query);
    }
    function getAircraftByManufacturer($id)
    {
        $sql = $this->allAircraftDataSql($id);
        $sql .= "
                WHERE a.aircraft_manufacturer_id = $id
                ORDER BY a.aircraft_model
                ";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    function getAircraftByID($id)
    {
        $sql = $this->allAircraftDataSql();
        $sql .= "
                WHERE  a.id = $id
                AND a.deleted = 0
                ORDER  BY a.aircraft_model";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchRow($query);
    }

    public function newAircraft($data)
    {
        $aircraftTable = new Application_Model_DbTable_Aircraft();
        $aircraft_id = $aircraftTable->insert([
            'aircraft_manufacturer_id' => $data['aircraft_manufacturer_id'],
            'aircraft_model'           => $data['aircraft_model'],
            'aircraft_tcds_id'         => $data['aircraft_tcds_id'],
            'landing_gear'             => $data['landing_gear'],
            'engines'                  => $data['engines'],
            'propellors'               => $data['propellors'],
            'crea_usr_id'              => $data['user_id'],
            'crea_dtm'                 => date('Y/m/d H:i:s',time()),
        ]);
        $aircraftEngineTable = new Application_Model_DbTable_AircraftEngine();
        $aircraft_engine_id = $aircraftEngineTable->insert([
            'aircraft_id'   => $aircraft_id,
            'engine_id'    => $data['engine_id'],
            'propellor_id' => $data['propellor_id'],
            'crea_usr_id'  => $data['user_id'],
            'crea_dtm'     => date('Y/m/d H:i:s',time()),
        ]);
        return;
    }

    /// This sql provides all aircraft data 
    function allAircraftDataSql()
    {
        return
            "SELECT a.id                      AS aircraft_id,
                   a.aircraft_manufacturer_id AS aircraft_manufacturer_id,
                   a.aircraft_model           AS aircraft_model,
                   a.landing_gear,
                   a.engines,
                   a.propellors,
                   b.id                       AS aircraft_manufacturer_id,
                   b.manufacturer_name        AS aircraft_manufacturer_name,
                   c.id                       AS aircraft_tcds_id,
                   c.tcds                     AS aircraft_tcds,
                   c.tcds_date                AS aircraft_tcds_date,
                   c.tcds_revision            AS aircraft_tcds_revision,
                   c.tcds_title               AS aircraft_tcds_title,
                   d.id                       AS aircraft_engine_id,
                   e.id                       AS engine_id,
                   e.engine_manufacturer_id,
                   g.manufacturer_name        AS engine_manufacturer_name,
                   e.engine_tcds_id,
                   e.engine_model,
                   f.tcds                     AS engine_tcds,
                   f.tcds_revision            AS engine_tcds_revision,
                   f.tcds_date                AS engine_tcds_date,
                   f.tcds_title               AS engine_tcds_title
            FROM   aircraft a
                   LEFT JOIN manufacturer b
                          ON a.aircraft_manufacturer_id = b.id
                   LEFT JOIN tcds c
                          ON a.aircraft_tcds_id = c.id
                   LEFT JOIN aircraft_engine d
                          ON a.id = d.aircraft_id
                   LEFT JOIN engines e
                          ON d.engine_id = e.id
                   LEFT JOIN tcds f
                          ON e.engine_tcds_id = f.id
                   LEFT JOIN manufacturer g
                          ON e.engine_manufacturer_id = g.id";
    }
}
