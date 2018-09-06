<?php


Class PHPSlickGrid_Plugins_FilterSticky extends PHPSlickGrid_Plugins_Abstract { 
    
    public $Javascript_File = array("/js/store.min.js","/phpslickgrid/plugins/filtersticky.js");
    
    function __construct($ColumnConfig) {
        
    }
    
    
    
    // Function Called before render javascript
    public function PreGridRinder($grid_name, $NameSp, $view) {
    	
    	$HTML = $grid_name."Columns = FilterSticky('$grid_name',".$grid_name."Columns)\n";
    	 
    	return $HTML;
    }
    
    
    public function WireEvents($grid_name, $NameSp, $view) { 

    	$HTML = "var ".$grid_name."FilterSticky = new PHPSlick.Plugins.FilterSticky();\n";
    	$HTML .= $grid_name.".registerPlugin(".$grid_name."FilterSticky);\n";
    	
    	return $HTML;
    }
    
}