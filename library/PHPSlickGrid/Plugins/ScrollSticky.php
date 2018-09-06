<?php


Class PHPSlickGrid_Plugins_ScrollSticky extends PHPSlickGrid_Plugins_Abstract { 
    
    public $Javascript_File = array("/js/store.min.js","/phpslickgrid/plugins/scrollsticky.js");
    
    function __construct($ColumnConfig) {
        
    }
    
    
    
    // Function Called before render javascript
    public function PreGridRinder($grid_name, $NameSp, $view) {
    	
    	//$HTML = "ScrollSticky('$grid_name',".$grid_name."Columns)\n";

    	$HTML ='';
    	return $HTML;
    }
    
    
    public function WireEvents($grid_name, $NameSp, $view) { 

    	$HTML = "var ".$grid_name."ScrollSticky = new PHPSlick.Plugins.ScrollSticky();\n";
    	$HTML .= $grid_name.".registerPlugin(".$grid_name."ScrollSticky);\n";
    	$HTML .= "var saved_scroll = store.get('".$grid_name."_scroll');\n";
    	$HTML .= "var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;\n";
    	$HTML .= "if (is_firefox)\n";
    	$HTML .= " setTimeout(function() { ScrollStickyUpdate('$grid_name',saved_scroll);},1000)\n";
    	$HTML .= "else\n";
    	$HTML .= " setTimeout(function() { ScrollStickyUpdate('$grid_name',saved_scroll);},2250)\n";
    	
    	return $HTML;
    }
    
}