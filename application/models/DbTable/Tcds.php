<?php

class Application_Model_DbTable_Tcds extends Zend_Db_Table_Abstract
{

    protected $_name = 'tcds';
    protected $_primary = 'id';

    function getTcds($type)
    {
        $sql = "SELECT id, manufacturer_id, tcds, tcds_type, tcds_revision, tcds_date, tcds_title
                FROM tcds
                WHERE deleted = 0
                AND tcds_type = '$type'
                ORDER BY tcds
                ";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
}
