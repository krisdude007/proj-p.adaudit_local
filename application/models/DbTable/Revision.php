<?php

class Application_Model_DbTable_Revision extends Zend_Db_Table_Abstract
{
    protected $_name = 'revision_highlights';
    // protected $_primary = 'id';
    
    // protected function _setupDatabaseAdapter()
    // {
    //     // see _initDatabase() in the Bootstrap.php file
    //     $this->_db = Zend_Registry::get('mmm_db');
    //     parent::_setupDatabaseAdapter();
    // }
    public function getItemRevisionHighlight($aircraft, $mpd_item, $revision){
        $sql = "SELECT * 
                FROM revision_highlights
                WHERE item = '$mpd_item' 
                AND revision = '$revision'
                AND aircraft = '$aircraft'";
        $query = $this->getAdapter()->quoteInto($sql,'');
        $result = $this->getAdapter()->fetchAll($query);
        return $result;
    }

}