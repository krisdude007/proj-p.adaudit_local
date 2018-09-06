<?php

class Application_Model_DbTable_Intervals extends Zend_Db_Table_Abstract
{
    protected $_name    = 'intervals';
    protected $_primary = 'id';
    
    public function clearTable($document_id)
    {
        $where = $this->getAdapter()->quoteInto('document_id=?', $document_id);
        $this->delete($where);
    }

    public function saveData($type = null, $method_id, $data, $document_id, $document_item_id)
    {
        $table = new Application_Model_DbTable_Intervals();
        foreach ($data as $key => $value) {
            $note = null;
            // skip some keys
            if($key == 'id') continue;
            if(is_null($value)) continue;
            if($key == 'note' && $value){
              $key = 'NOTE';
              $value = 'NOTE';
              $note = 'NOTE';
            }
            if($key == 'numbered_note' && $value){
              $key = 'NOTE';
              $value = $value;
              $note = 'NOTE ' . $value;
            }
            if($key == 'star_note' && $value){
              $key = '(*)';
              $value = '(*)';
              $note = '(*)';
            }
            // save the interval data
            $data = array(
              'document_id'        => $document_id,
              'document_item_id'   => $document_item_id,
              'interval_method_id' => $method_id,
              'interval_type'      => $key,
              'interval_value'     => $value,
              'interval_note'      => $note
            );
            $row_id = $table->insert($data);
        }
        return TRUE;
    }
    
    public function getByDocId($id)
    {
        $sql = $this->commonSQL();
        $sql .= " WHERE document_id = $id
                AND a.deleted = 0";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $interval_records = $this->getAdapter()->fetchAll($query);
        return $interval_records;
    }

    public function getByDocItemId($document_id, $row_id)
    {
        $sql = $this->commonSQL();
        $sql .= " WHERE a.document_item_id = $row_id
                AND a.document_id = $document_id
                AND a.deleted = 0
                ORDER BY a.id DESC";

        $query = $this->getAdapter()->quoteInto($sql,'');
        $interval_records = $this->getAdapter()->fetchAll($query);
        return $interval_records;
    }

    public function commonSQL()
    {
        return "SELECT  a.id as interval_id,
                        a.interval_value,
                        a.interval_type,
                        a.interval_note,
                        a.document_item_id,
                        b.id as interval_method_id,
                        b.method,
                        b.description
                    FROM intervals a
                    LEFT JOIN interval_method b
                    ON a.interval_method_id = b.id";
    }
}