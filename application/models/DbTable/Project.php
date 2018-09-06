<?php

class Application_Model_DbTable_Project extends Zend_Db_Table_Abstract
{
    protected $_name = 'project';
    protected $_primary = 'project_id';

    public function getProjectByID($project_id)
    {
        $sql = "SELECT *
                FROM   project
                WHERE  project_id = $project_id";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getProjectNameByID($project_id)
    {
        $sql = "SELECT project_txt
                FROM   project
                WHERE  project_id = $project_id";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }

    public function getSraWorksheetForProject($project_id)
    {
        $sql = "SELECT sra_worksheet
                FROM   project
                WHERE  project_id = $project_id";

        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
}