<?php
class PHPSlickGrid_Plugins_Headerdialog_Applyclose extends PHPSlickGrid_Plugins_Headerdialog_Abstract {

    public $Javascript_File = array("/phpslickgrid/plugins/headerdialog/applyclose.js");
    
    public function WireEvents($grid_name, $dialog_name, $NameSp, $view) {
        $HTML = $grid_name.$dialog_name."ApplyClose = new PHPSlick.HeaderDialog.ApplyClose();\n";
        $HTML .= $grid_name.$dialog_name.".registerPlugin(".$grid_name.$dialog_name."ApplyClose);\n";
        return $HTML;
    }
}