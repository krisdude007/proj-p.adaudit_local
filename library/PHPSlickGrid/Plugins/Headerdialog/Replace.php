<?php

// Comment


class PHPSlickGrid_Plugins_Headerdialog_Replace extends PHPSlickGrid_Plugins_Headerdialog_Abstract {

    public $Javascript_File = array("/phpslickgrid/plugins/headerdialog/replace.js");
    public $Options;
    
    function __construct($Options) {
        //parent::__construct();
        
        $this->Options=$Options;
        
    }
    
    public function WireEvents($grid_name, $dialog_name, $NameSp, $view) {
        $Options=json_encode($this->Options);
        $HTML = $grid_name.$dialog_name."Replace = new PHPSlick.HeaderDialog.Replace($Options);\n";
        $HTML .= $grid_name.$dialog_name.".registerPlugin(".$grid_name.$dialog_name."Replace);\n";
        return $HTML;
    }
    
}