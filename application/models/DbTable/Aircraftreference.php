<?php

class Application_Model_DbTable_Aircraftreference extends Zend_Db_Table_Abstract
{

    protected $_name = '1_aircraft_reference';
    protected $_primary = 'record_id';
    function getManufacturers()
    {
        $sql = "
                SELECT DISTINCT manufacturer, `code`, model
                FROM   `1_aircraft_reference`
                WHERE type_aircraft = 5
                AND type_engine = 5
                AND weight = 'CLASS 3'
                AND seats > 20
                ORDER  BY manufacturer";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);        
    }

    function getAircraft()
    {
        $sql = "
                SELECT model, `code`
                FROM   `1_aircraft_reference`
                WHERE type_aircraft = 5
                AND type_engine = 5
                AND weight = 'CLASS 3'
                AND seats > 20
                AND model LIKE '737-%'
                ORDER  BY model";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query); 
    }
}
