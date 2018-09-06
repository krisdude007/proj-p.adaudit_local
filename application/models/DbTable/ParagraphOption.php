<?php

class Application_Model_DbTable_ParagraphOption extends Zend_Db_Table_Abstract {

    protected $_name = 'paragraph_option';
    protected $_primary = 'paragraph_option_id';

    public function getName() {
        return $this->_name;
    }

    public function getParagraphOptions($id = false) {
        if ($id) {
           $sql = "SELECT *
                FROM paragraph_option WHERE ad_paragraph_id = $id
                AND deleted = 0
                ORDER BY paragraph_option_id
                
                ";
        } else {
            $sql = "SELECT *
                FROM paragraph_option WHERE ad_paragraph_id = 0 
                and deleted = 0
                ORDER BY paragraph_option_id";
        }
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
    
     public function add($id) {
        $url = isset($_SERVER['HTTPS']) ? "https://" : "http://"."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
        
        $row = $this->createRow();
        $row->ad_paragraph_id = $id;
        $row->ip_address = $_SERVER['REMOTE_ADDR'];
        $row->url_path = $url;
        $row->crea_dtm = date('Y/m/d H:i:s',time());
       
        $row->save();
	$newParagraphOptionId = $row->paragraph_option_id;
        
        return $newParagraphOptionId;
    }
}
    