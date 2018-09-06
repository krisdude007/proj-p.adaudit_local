<?php

// Comment


class PHPSlickGrid_Plugins_Headerdialog_Permissions extends PHPSlickGrid_Plugins_Headerdialog_Abstract {

    public $Javascript_File = array("/phpslickgrid/plugins/headerdialog/permissions.js");
    public $Options;
    
    function __construct($Options) {
        //parent::__construct();
        
        // Get our logger for debugging
        $this->log = Zend_Registry::get('log');
        
        $this->Options=$Options;
        
    }
    
    public function UpdateColumns() {
        //$this->log->debug($this->Options['acl']);
        
    }
    
    public function WireEvents($grid_name, $dialog_name, $NameSp, $view) {
        $Options=json_encode($this->Options);
        $HTML = $grid_name.$dialog_name."Permissions = new PHPSlick.HeaderDialog.Permissions($Options);\n";
        $HTML .= $grid_name.$dialog_name.".registerPlugin(".$grid_name.$dialog_name."Permissions);\n";
        return $HTML;
    }
    
}