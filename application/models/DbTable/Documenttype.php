<?php

class Application_Model_DbTable_Documenttype extends Zend_Db_Table_Abstract
{
    protected $_name = 'document_type';
    protected $_primary = 'id';

    public function get($id)
    {
        $sql = "SELECT id, type, description
                FROM document_type
                WHERE id = $id
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchRow($query);
        return $result;
    }

    public function getAll()
    {
        $sql = "SELECT id, type, description, id as value, type as valueText
                FROM document_type
                WHERE deleted = 0
                ORDER BY type";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchAll($query);
        return $result;
    }

}