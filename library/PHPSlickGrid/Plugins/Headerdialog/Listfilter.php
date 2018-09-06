<?php
// Comment


class PHPSlickGrid_Plugins_Headerdialog_ListFilter extends PHPSlickGrid_Plugins_Headerdialog_Abstract {
    
    public $Javascript_File = array("/phpslickgrid/plugins/headerdialog/listfilter2.js");
    public $Options;
    
    function __construct($Options) {
        //parent::__construct();
    
        $this->Options=$Options;
    
    }
    
    public function WireEvents($grid_name, $dialog_name, $NameSp, $view) {
        $Options=json_encode($this->Options);
        $HTML = $grid_name.$dialog_name."ListFilter = new PHPSlick.HeaderDialog.ListFilter2($Options,".$this->Options['gridName']."Data);\n";
        $HTML .= $grid_name.$dialog_name.".registerPlugin(".$grid_name.$dialog_name."ListFilter);\n";
        return $HTML;
    }
    
    public function WireEvents2($grid_name, $dialog_name, $NameSp, $view) {
        $HTML = "// ****************************************************************\n";
        $HTML .= "// Wire up the list filter to the gird refresh\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= $grid_name.$dialog_name."ListFilter.updateFilters.subscribe(function (e, args) {\n";
        $HTML .= "  var currentFilter = ".$this->Options['gridName']."Data.getWhere();\n";
//        $HTML .= "  console.log('updatingFilters from Listfiltrs');\n";
//         $HTML .= "  for(var i=0;i<currentFilter.length;i++) {\n";
//         $HTML .= "    if (currentFilter[i].operator=='in') {\n";
//         $HTML .= "     console.log(currentFilter[i]);\n";    
//         $HTML .= "    }\n";
//         $HTML .= "  }\n";
//        $HTML .= "  console.log('args');\n";
//        $HTML .= "  console.log(args);\n";
        
//        $HTML .= "  ".$this->Options['gridName']."Data.setIn(args.column,args.value,args.mode);\n";

        $HTML .= "  var Filters = new Array();\n";
        $HTML .= "  ".$grid_name."Columns = ".$grid_name.".getColumns();\n";
        $HTML .= "  for(i in ".$grid_name."Columns) {\n";
        $HTML .= "    if (typeof ".$grid_name."Columns[i].Filters != 'undefined') { \n";
        $HTML .= "      Filters[".$grid_name."Columns[i].field]=".$grid_name."Columns[i].Filters;\n";
        $HTML .= "    }\n";
        $HTML .= "  }\n";
        $HTML .= "  ".$grid_name."Data.setWhere(Filters);\n";
        
//        $HTML .= "  console.log(".$this->Options['gridName']."Data.getWhere());\n";
        $HTML .= "  ".$this->Options['gridName']."Data.invalidate();\n";
        $HTML .= "  ".$this->Options['gridName'].".invalidate();\n";
        $HTML .= "  ".$this->Options['gridName'].".render();\n";
        $HTML .= "});\n";
        $HTML .="\n\n";
        return $HTML;
    }
    
}