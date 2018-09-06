<?php

class Application_Model_DbTable_Documentversions extends Zend_Db_Table_Abstract
{
    protected $_name    = 'document_versions';
    protected $_primary = 'id';
    
    public function get($id)
    {
        $sql = "SELECT *
                FROM document_versions
                WHERE id = $id
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchRow($query);
    }

    public function newVersion($document_id, $data)
    {
        $uid = sha1(uniqid(mt_rand(), true));
        $table = new Application_Model_DbTable_Documentversions();
        $id = $table->insert([
            'uid' => $uid,
            'revision_num' => $data['revision_num'],
            'revision_dt' => $data['revision_dt'],
            'comment' => $data['comment'],
            'document_id' => $data['document_id'],
            'crea_usr_id' => $data['user_id'],
            'crea_dtm' => date('Y/m/d H:i:s',time()),
        ]);
        return $this->get($id);
    }

    public function updateVersion($document_version_id, $data)
    {
        $where = $this->getAdapter()->quoteInto('id=?', $document_version_id);
        return $this->update($data, $where);
    }

    public function deleteDocumentVersions($document_version_id)
    {
        $where = $this->getAdapter()->quoteInto('id=?', $document_version_id);
        $this->update(array('deleted'=>1), $where);
    }

    public function getByDocId($id)
    {
        $sql = "SELECT *
                FROM document_versions
                WHERE document_id = $id
                AND deleted = 0
                ORDER BY revision_num";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $document_versions = $this->getAdapter()->fetchAll($query);
        return $document_versions;
    }
}