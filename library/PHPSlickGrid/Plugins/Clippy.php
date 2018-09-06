<?php


Class PHPSlickGrid_Plugins_Clippy extends PHPSlickGrid_Plugins_Abstract { 
    
	public $CSS_Files = array("/phpslick/lib/clippy/clippy.css");
    public $Javascript_File = array("/phpslick/lib/clippy/clippy.min.js");
    
    
    function __construct() {
        
    }
    
    
    
    // Function Called before render javascript
    public function PreGridRinder($grid_name, $NameSp, $view) {
    	
    	$HTML = @"
clippy.load('Clippy', function(agent) {
	// Do anything with the loaded agent
	agent.show();
});";
    	 
    	return $HTML;
    }
    
    
    public function WireEvents($grid_name, $NameSp, $view) { 

    	
    	$HTML = "";

    	return $HTML;
    }
    
}