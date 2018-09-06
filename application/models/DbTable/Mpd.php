<?php

class Application_Model_DbTable_Mpd extends Zend_Db_Table_Abstract
{
    protected $_name = 'mpd';
    protected $_primary = 'id';

    public function getByDocId($id, $version_id = null)
    {
        if(is_null($version_id)){
            $sql = "SELECT *
                    FROM mpd
                    WHERE document_id = $id
                    AND deleted = 0";
        }else{
            $sql = "SELECT *
                    FROM mpd
                    WHERE document_id = $id
                    AND document_version_id = $version_id
                    AND deleted = 0";
        }
        $query = $this->getAdapter()->quoteInto($sql,'');
        $mpd_records = $this->getAdapter()->fetchAll($query);
        $intervalTable = new Application_Model_DbTable_Intervals();
        $intervals = $intervalTable->getByDocId($id);
        $returnArray = array();
        foreach ($mpd_records as $record) {
            $tempArray = array();
            foreach($intervals as $key => $interval){
                if($record['id'] == $interval['document_item_id']){
                    $tempArray[] = $interval;
                    unset($intervals[$key]); // to shorten process time, remove this key after it's been captured
                }
            }
            $returnArray[$record['id']] = array(
                'record' => $record,
                'intervals' => $tempArray
            );
            unset($tempArray);
        }
        return $returnArray;
    }


    public function getByDocIdNoIntervals($id, $version_id = null)
    {
        if(is_null($version_id)){
            $sql = "SELECT *
                    FROM mpd
                    WHERE document_id = $id
                    AND deleted = 0";
        }else{
            $sql = "SELECT *
                    FROM mpd
                    WHERE document_id = $id
                    AND document_version_id = $version_id
                    AND deleted = 0";
        }
        $query = $this->getAdapter()->quoteInto($sql,'');
        $mpd_records = $this->getAdapter()->fetchAll($query);
    
        return $mpd_records;
    }

    public function getRow($document_id, $row)
    {
        $sql = "SELECT *
                FROM mpd
                WHERE document_id = $document_id
                AND id = $row
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchRow($query);
    }

    public function getRowWithIntervals($document_id, $document_version_id, $row_id)
    {
        $sql = "SELECT *
                FROM mpd
                WHERE document_id = $document_id
                AND id = $row_id
                AND deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $row = $this->getAdapter()->fetchRow($query);

        $intervalTable = new Application_Model_DbTable_Intervals();
        $intervals = $intervalTable->getByDocItemId($document_id, $row_id);
        return array(
            'row' => $row,
            'intervals' => $intervals
        );
    }


    public function getMPD($aircraft=null, $revision = null) {
        if(is_null($aircraft)){
            $sql = "SELECT *
            FROM mpd";
        }else{
            $sql = "SELECT *
            FROM mpd
            WHERE aircraft = '$aircraft'
            AND revision = '$revision'";
        }
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
    public function getMPDHighlights($aircraft=null, $revision = null) {

            $sql = "SELECT *
        FROM revision_highlights
        WHERE aircraft = '$aircraft'
        AND revision = '$revision'";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getMPDaircraftmodels(){
        $sql = "SELECT DISTINCT aircraft
                FROM mpd
                ORDER BY aircraft";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getAirplaneApplicability($aircraft){
        $sql = "SELECT DISTINCT appl_airplane
                FROM mpd
                WHERE aircraft = '$aircraft'";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getEngineApplicability($aircraft){
        $sql = "SELECT DISTINCT appl_engine
                FROM mpd
                WHERE aircraft = '$aircraft'";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getTasks($aircraft){
        $sql = "SELECT DISTINCT task
                FROM mpd
                WHERE aircraft = '$aircraft'";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
    public function getAvailableRevisions($aircraft){
        $sql = "SELECT DISTINCT revision
                FROM mpd
                WHERE aircraft = '$aircraft'";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getByMPDitemnumber($mpd_item_number){
        $select = $this->select();
        $select->where('mpd_item_number = ?', $mpd_item_number);
        return $this->fetchRow($select)->toArray();
    }

    public function deleteRecordsByDocumentID($document_id)
    {
        $where = $this->getAdapter()->quoteInto('document_id=?', $document_id);
        $this->update(array('deleted'=>1), $where);
    }
    public function deleteRecordsByDocumentVersionID($document_version_id)
    {
        $where = $this->getAdapter()->quoteInto('document_version_id=?', $document_version_id);
        $this->update(array('deleted'=>1), $where);
    }

    public function updateRecord($id)
    {

    }

    public function deleteRecord($id)
    {
        $where = $this->getAdapter()->quoteInto('id=?', $id);
        $this->update(array('deleted'=>1), $where);
    }
}

