<?php

class PHPSlickGrid_Plugins_Headerdialog2 extends PHPSlickGrid_Plugins_Abstract { 
    
    public $Plugins = array();
    public $Name ='HeaderDialog';
    
    public $Options = array();
    
    public $CSS_Files = array("/phpslickgrid/plugins/headerdialog.css");
    
    public $Javascript_File = array("/phpslickgrid/plugins/headerdialog.js");
    
    function __construct($Name, $Plugins, $Icon=null, $ToolTip=null) {
        
        $this->Name=$Name;
        $this->Plugins=$Plugins;
        
        if ($Icon!=null)
            $this->Options["buttonImage"] = $Icon;
        
        if ($ToolTip!=null)
            $this->Options["hoverText"] = $ToolTip;
        
        $this->Options['buttonCssClass'] = 'phpslick-headerdialog-menubutton2';
        
        // Gather CSS files
        foreach($this->Plugins as $Key=>$Plugin) {
            $this->CSS_Files=array_merge($this->CSS_Files,$Plugin->CSS_Files);
        }
        
        // Gather JS files
        foreach($this->Plugins as $Key=>$Plugin) {
            $this->Javascript_File=array_merge($this->Javascript_File,$Plugin->Javascript_File);
        }
        //print_r($this->Javascript_File);
    }
    
    public function updateColumns(){
        foreach($this->Plugins as $Key=>$Plugin) {
            if (method_exists($Plugin,'UpdateColumns'))
                $Plugin->UpdateColumns();
        }
    }
    
    
    public function WireEvents($grid_name, $NameSp, $view) {
        
        $Options = json_encode($this->Options);
        
        $HTML = "\n// ****************************************************************\n";
        $HTML .= "// Wire up ".$this->Name."\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= $grid_name.$this->Name." = new PHPSlick.Plugins.HeaderDialog($Options);\n";
        
        foreach($this->Plugins as $Key=>$Plugin) {
            $HTML .= $Plugin->WireEvents($grid_name,$this->Name, $NameSp, $view);
        }
        
        $HTML .= $grid_name.".registerPlugin(".$grid_name.$this->Name.");\n";
        return $HTML;
        
    }
    
    public function WireEvents2($grid_name, $NameSp, $view) {
    
        $HTML = "";
    
        foreach($this->Plugins as $Key=>$Plugin) {
            $HTML .= $Plugin->WireEvents2($grid_name,$this->Name, $NameSp, $view);
        }
        
        return $HTML;
    
    }
    
}