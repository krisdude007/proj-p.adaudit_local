<?php

class Application_Model_DbTable_Mrb extends Zend_Db_Table_Abstract
{

    protected $_name = 'mrb';
    
    public function getColumnNames() {
         $sql = "SHOW columns FROM mrb";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
    public function getMRB() {
        $query = $this->select();
        return $this->fetchAll($query)->toArray();
    }
    public function getMRBbyATA($ata) {
        $query = $this->select();
        $query->where('ata = ?', $ata);
        return $this->fetchAll($query)->toArray();
    }
    public function getMRBbyID($id) {
        $query = $this->select();
        $query->where('id = ?', $id);
        return $this->fetchRow($query)->toArray();
    }

    public function getByMRBitemnumber($mrb_item_number){
        $select = $this->select();
        $select->where('mrb_item_number = ?', $mrb_item_number);
        return $this->fetchRow($select)->toArray();
    }

    public function getMRBbyMRBnumber($mrb_item_number){
        $select = $this->select();
        $select->where('mrb_item_number LIKE ?', $mrb_item_number.'%');
        return $this->fetchAll($select)->toArray();
    }
}



