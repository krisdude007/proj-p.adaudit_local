<?php
// Comment


class PHPSlickGrid_Plugins_Headerdialog_SimpleFilter extends PHPSlickGrid_Plugins_Headerdialog_Abstract {
    
    public $Javascript_File = array("/phpslickgrid/plugins/headerdialog/simplefilter.js");
    
    public function WireEvents($grid_name, $dialog_name, $NameSp, $view) {
        $HTML = $grid_name.$dialog_name."SimpleFilter = new PHPSlick.HeaderDialog.SimpleFilter();\n";
        $HTML .= $grid_name.$dialog_name.".registerPlugin(".$grid_name.$dialog_name."SimpleFilter);\n";
        return $HTML;
    }
    
    public function WireEvents2($grid_name, $dialog_name, $NameSp, $view) {
        $HTML = "\n// ****************************************************************\n";
        $HTML .= "// Wire up the simple filter to the gird refresh\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= $grid_name.$dialog_name."SimpleFilter.updateFilters.subscribe(function (e, args) {\n";
        
        $HTML .= "  var Filters = new Array();\n";
        $HTML .= "  ".$grid_name."Columns = ".$grid_name.".getColumns();\n";
        $HTML .= "  for(i in ".$grid_name."Columns) {\n";
        $HTML .= "    if (typeof ".$grid_name."Columns[i].Filters != 'undefined') { \n";
        $HTML .= "      Filters[".$grid_name."Columns[i].field]=".$grid_name."Columns[i].Filters;\n";
        $HTML .= "      \n";
        $HTML .= "    }\n";
        $HTML .= "  }\n";
        $HTML .= "  ".$grid_name."Data.setWhere(Filters);\n";
        $HTML .= "  ".$grid_name."Data.invalidate();\n";
        $HTML .= "  ".$grid_name.".invalidate();\n";
        $HTML .= "  ".$grid_name.".render();\n";
//        $HTML .= "  ".$grid_name."ListFilter.invalidate(Filters);\n";
        $HTML .= "});\n";
        $HTML .="\n\n";
        
        
//         $HTML .= "var activeHeaderColumns = ".$grid_name.".getColumns();\n";
//         $HTML .= "console.log(activeHeaderColumns);\n";
//         $HTML .= "for(i in activeHeaderColumns) {\n";
//         $HTML .= "    if (activeHeaderColumns[i].Filters.length > 0)\n";
//         $HTML .= "}\n";
        
        
        return $HTML;
    }
    
}