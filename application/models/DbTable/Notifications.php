<?php
class Application_Model_DbTable_Notifications extends Zend_Db_Table_Abstract {

    protected $_name = 'notifications';
    protected $_primary = 'notification_id';
    
    public function getNotifications($dtg,$myID){
        $db = $this->getAdapter();
        $sql = $db->quoteInto("select
                notification_txt,
                alert_level,
                DATE_FORMAT(time_stamp,'%m/%d/%Y @ %l:%i %p') as time_stamp,
                IF(sent_by = '0', 'N/A', sent_by) as sent_by
            from
                notifications
            where
                user_id = '".$myID."'
                and time_stamp > ?
            order by
                time_stamp",$dtg);
        $results = $db->fetchall($sql);
        return $results;
    }
    
}

