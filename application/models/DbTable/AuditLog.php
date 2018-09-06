<?php

class Application_Model_DbTable_AuditLog extends Zend_Db_Table_Abstract {

    protected $_name = 'audit_log';
    protected $_primary = 'audit_log_id';
    
    public function add($data) {
        $url = isset($_SERVER['HTTPS']) ? "https://" : "http://"."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
        
        $auditLogId = $this->insert([                
                'table_nm'        => $data['table_nm'],
                'key_column_nm'        => $data['key_column_nm'],
                'key_column_value'  => $data['key_column_value'],
                'modified_column_nm'      => $data['modified_column_nm'],
                'old_value'      => $data['old_value'],
                'new_value' => $data['new_value'],
                'ip_address'      => $_SERVER['REMOTE_ADDR'],
                'url_path' => $url,
                'crea_usr_id' => $data['crea_usr_id'],
                'crea_dtm'    => date('Y/m/d H:i:s',time()),
            ]);
    }
}
