<?php

class Application_Model_DbTable_Sourcedocuments extends Zend_Db_Table_Abstract
{
    protected $_name    = 'source_documents';
    protected $_primary = 'id';
    
    public function getFileByUid($uid)
    {
        $sql = "SELECT *
                FROM source_documents
                WHERE uid = '$uid'
                AND status = 'Done'
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }    
    public function getFileById($id)
    {
        $sql = "SELECT *
                FROM source_documents
                WHERE id = '$id'
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchRow($query);
    }

    public function newDocument($fileInfo, $documentVersion)
    {        
        $uid_filepath = $this->get_uid_filepath('../files/');
        $table = new Application_Model_DbTable_Sourcedocuments();
        $id = $table->insert([
            'uid' => $documentVersion['uid'],
            'name' => $fileInfo['importfile']['name'],
            'size' => $fileInfo['importfile']['size'],
            'type' => $fileInfo['importfile']['type'],
            'file_storage_nm' => basename($uid_filepath),
        ]);
        return $this->getFileById($id);
    }

    private function get_uid_filepath($rootpath) {
        $goodname = false;
        while (!$goodname) {
            $candidate=sprintf('%8.8s.%3.3s',strrev(uniqid()),uniqid());
            if (!is_dir($rootpath.$candidate[0])) {
                mkdir($rootpath.$candidate[0]);
            }
            if (!file_exists($rootpath.$candidate[0]."/".$candidate)) {
                $goodname=true;
                $newfilepath=$rootpath.$candidate[0]."/".$candidate;
            }
        }
        return $newfilepath;
    }
}