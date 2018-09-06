<?php


Class PHPSlickGrid_Plugins_ColumnSticky extends PHPSlickGrid_Plugins_Abstract { 
    
    public $Javascript_File = array("/js/store.min.js","/phpslickgrid/plugins/columnsticky.js");
    
    function __construct($ColumnConfig) {
        
    }
    
    
    
    // Function Called before render javascript
    public function PreGridRinder($grid_name, $NameSp, $view) {
    	
    	$HTML = $grid_name."Columns = ColumnSticky('$grid_name',".$grid_name."Columns)\n";
    	 
    	return $HTML;
    }
    
    
    public function WireEvents($grid_name, $NameSp, $view) { 

    	
    	$HTML = "\n";
    	$HTML .= "var ".$grid_name."ColumnSticky = new PHPSlick.Plugins.ColumnSticky();\n";
    	$HTML .= $grid_name.".registerPlugin(".$grid_name."ColumnSticky);\n";
    	return $HTML;
    }
    
}