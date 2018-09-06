<?php

class Application_Model_DbTable_Intervalmethod extends Zend_Db_Table_Abstract
{
    protected $_name    = 'interval_method';
    protected $_primary = 'id';

    public function getID($text)
    {
        $sql = "SELECT id
            FROM interval_method
            WHERE method = '$text'
            AND deleted = 0";

        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchRow($query);
        return $result['id'];
    }

    public function getMethod($id)
    {
        $sql = "SELECT method, description
        FROM interval_method
        WHERE id = $id
        AND deleted = 0";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchRow($query);   
    }
    public function getMethods()
    {
        $sql = "SELECT id, method, description
        FROM interval_method
        WHERE deleted = 0";

        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchAll($query);
        $returnArray = array();
        foreach ($result as $key => $value) {
            $returnArray[$value['id']] = array(
                'method' => $value['method'],
                'description' => $value['description']);
        }
        return $returnArray;
    }
}