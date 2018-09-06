<?php

class Application_Model_DbTable_AdParagraph extends Zend_Db_Table_Abstract {

    protected $_name = 'ad_paragraph';
    protected $_primary = 'ad_paragraph_id';

    public function getName() {
        return $this->_name;
    }

    public function getParagraphs($id = false) {
        if ($id) {
            $sql = "Select ad_paragraph_id, paragraph_txt, if_txt, do_txt, is_actionable from ad_paragraph where ad_id = $id and deleted = 0";
        } else {
            $sql = "Select ad_paragraph_id, paragraph_txt, if_txt, do_txt, is_actionable from ad_paragraph and deleted = 0";
        }
        
        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }
    
    public function add($data) {
        $url = isset($_SERVER['HTTPS']) ? "https://" : "http://"."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
        
        $row = $this->createRow();
        $row->ad_id = $data['ad_id'];
        $row->paragraph_txt = $data['paragraph_txt'];
        $row->if_txt = $data['if_txt'];
        $row->do_txt = $data['do_txt'];
        $row->crea_usr_id = $data['user_id'];
        $row->ip_address = $_SERVER['REMOTE_ADDR'];
        $row->url_path = $url;
        $row->crea_dtm = date('Y/m/d H:i:s',time());
       
        $row->save();
	$newAdParagraphId = $row->ad_paragraph_id;
        
        return $newAdParagraphId;
    }
    
}
    