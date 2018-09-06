<?php

class Application_Model_DbTable_Documents extends Zend_Db_Table_Abstract
{
    protected $_name = 'documents';
    protected $_primary = 'id';

    public function get($id)
    {
        $sql = "SELECT * 
                FROM documents
                WHERE id = $id
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchRow($query);
        return $result;
    }

    public function getFields($id)
    {
        $sql = "SELECT field_definition, exclude_keys
                FROM documents
                WHERE id = $id
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchRow($query);
        return $result;
    }

    public function getWithVersions($id)
    {
        $sql = "SELECT * 
                FROM documents
                WHERE id = $id
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $document = $this->getAdapter()->fetchRow($query);
        $versionsTable = new Application_Model_DbTable_Documentversions();
        $versions = $versionsTable->getByDocId($document['id']);
        return array(
            'document'=>$document,
            'versions'=>$versions);
    }

    public function getAll()
    {
        $sql = "SELECT * 
                FROM documents
                WHERE deleted = 0
                ORDER BY name";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchAll($query);
        return $result;
    }

    public function getAllWithVersions()
    {
        $sql = "SELECT * 
                FROM documents
                WHERE deleted = 0
                ORDER BY name";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $documents = $this->getAdapter()->fetchAll($query);
        $versionsTable = new Application_Model_DbTable_Documentversions();

        $returnArray = array();
        foreach ($documents as $document) {
            $versions = $versionsTable->getByDocId($document['id']);
            $returnArray[] = array(
                'document'=>$document,
                'versions'=>$versions);
        }
        return $returnArray;
    }

    public function updateDocument($did, $data)
    {
        $where = $this->getAdapter()->quoteInto('id=?', $did);
        $this->update($data, $where);
    }

    public function store($data)
    {
        // $table = new Application_Model_DbTable_Documentversions();
        $table = $this->getAdapter();
        $id = $table->insert('documents',$data);
        return $this->get($id);
    }

    public function deleteDocument($did)
    {
        $where = $this->getAdapter()->quoteInto('id=?', $did);
        $this->update(array('deleted'=>1), $where);
    }
}