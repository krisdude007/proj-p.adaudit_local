<?php
abstract class PHPSlickGrid_Plugins_Abstract
{  
    public $alert="";
    public $refresh=false;
     
    public $Javascript_File = array();
    
    public $CSS_Files = array();
    
    public $grid_registerPlugin = array();
    
    // Function Called before render javascript
    public function PreGridRinder($grid_name, $NameSp, $view) {
        return "";
    }
    
    public function GridRender($grid_name,$NameSp,$view) {
        return "";
    }
    
    public function WireEvents($grid_name, $NameSp, $view) {
        return "";
    }
    
    public function WireEvents2($grid_name, $NameSp, $view) {
        return "";
    }
    
    public function PageReload() {
        return "";
    }
}

