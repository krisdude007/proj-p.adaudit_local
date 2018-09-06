<?php
class PHPSlickgrid_View_Helper_PHPSlickgrid extends Zend_View_Helper_Abstract
{
    private $name=null;
    private $value=null;
    private $attribs=null;
    private $options=null;
    
    private $live_data=false;
    
    public function PHPSlickgrid($name, $value = null, $attribs = null, $options=null)
    {
        $this->name      = $name;
        $this->value     = $value;
        $this->attribs   = $attribs;
        $this->options   = $options;
        
        $this->options->gridName = $name;
        
        
        /**
         * Setup a session for this plugin
         */
        $this->session = new Zend_Session_Namespace($this->name);
        
        
        /**
         * Load all the css required for the core slickgrid style
         */
        $this->loadCss();
        
        /**
         * Load all the javascript requird for the core slickgrid functionality
         */
        //$this->view->headScript()->prependScript('/slickgrid/lib/jquery-1.7.min.js');
        //$this->view->headScript()->appendFile('/slickgrid/lib/jquery-1.7.min.js');
        $this->loadJS();
        
        /**
         * Give the GridColumnConfiguration a chance to update it's setting using the
         * data from the view.  IE, let it load meta data using the project_id and 
         * grid_name.
         */       
        $attribs->UpdateColumnsFromMeta($this->options->project_id,$name);
        
        /**
         * Iterate over the plugins and call the plugins PreRender if it exists. 
         * 
         * Each plugin should:
         * 1) load any css if needed
         * 2) load any javascrip if needed
         * 3) setup/recover any session data if needed
         * 4) render any pre-render html to the view if needed
         */
        
        
        /**
         * Render the core slickgrid javascrip.
         */
        $HTML=$this->RenderCoreSlickgrid();
        
        /**
         * Iterate over the plugins and call the plugins PostRender if it exists.
         * 
         * Each plugin should cleanup if needed.
         */
        
        
        return $HTML;
    }
    
    
    private function RenderCoreSlickgrid() {
        $name=$this->name;
        
        $HTML  = "<div id='$name' style='height:100%;'></div>\n";
        $HTML .= "<script>\n";
        //$HTML .= "var $name;\n";
        $HTML .= $this->RenderColumns();
        $HTML .= $this->RenderOptions();
        $HTML .= $this->RenderValues($this->value);
        $HTML .= $this->PreGridRinder();
        //$HTML .= "$(function () {".$name." = new Slick.Grid('#$name', ".$name."Data, ".$name."Columns, ".$name."Options);})\n";
        $HTML .= "var ".$name." = new Slick.Grid('#$name', ".$name."Data, ".$name."Columns, ".$name."Options);\n";
        $HTML .= $this->PostGirdRinder();
        $HTML .= "</script>\n";
        
        return $HTML;
        
    }
    
    // Convert a Rowset of data into a javascrip array
    private function RowsetToJSArray($value){
        
        $HTML='[';
        foreach($value as $row) {
            $line='';
            foreach($this->attribs->Columns as $Column) {
                if (!isset($Column->hidden)) {
                    $ColumnName=$Column->data['field'];
                    $ColumnValue=$row->$ColumnName;
                    $line.=$ColumnName.":'".$ColumnValue."',";
                }
            }
            $HTML.='{'.rtrim($line,",")."},\n";
        }
        $HTML=rtrim($HTML,",\n")."];\n";
               
        return "var ".$this->name."Data=".$HTML;
    }
    
    private function RenderValues($value) {
        
        $HTML="alert('Unkown data value format passed to slickgrid helper');";
        
        if ($value==null) {
            $HTML = "var ".$this->name."Data = new PHPSlick.Data.DataCache(".$this->name."Options);\n";
            $this->live_data=true;
        }
        
        // If passed an object parse the object as
        // somthing we can pull the data from into 
        // a javascrip array.
        if (is_object($value)) {
            if (get_class($value)=='Zend_Db_Table_Rowset') {
                $HTML = $this->RowsetToJSArray($value);
            }
            if (get_class($value)=='PHPSlickGrid_DataConfig') {
                $url = '/';
                $url .= $value->controller."/";
                $url .= $value->action."/";
                
                if (isset($value->get_parameters))
                    foreach($value->get_parameters as $key=>$v) {
                        $url.=$key."/".$v."/";
                    }
                    
                $url = rtrim($url,"/");
                
                $options = "{'jasonrpc':'$url'}";
                
                $HTML = "var ".$this->name."Data = new Slick.Data.DataCache($options);\n";
                $this->live_data=true;
            }
            
            if (get_class($value)=='PHPSlickGrid_GridConfig') {
                $HTML = "var ".$this->name."Data = new PHPSlick.Data.DataCache(".$this->name."Options);\n";
                $this->live_data=true;
            }
        }
        
        // If passed an array, just turn the array into 
        // a javascrip array
        if (is_array($value)) {
            // TODO: implment 
            $HTML="alert('Array data to slickgrid is not implmented.');";
        }
        
        // if passed a string assume it is a jason RPC with
        // the requried methods: lengith and getItem
        if (is_string($value)) {
            $HTML = "var ".$this->name."Data = new Slick.Data.DataCache(".$this->name."Options);\n";
        }
        
        return $HTML;
        
    }
    
    
    private function PreGridRinder() {
        $HTML ='';
        foreach($this->options->plugins as $key=>$plugin) {
            $HTML .= $plugin->PreGridRinder($this->name,$this->session,$this->view);
        }
        return $HTML;
    }
    
    private function PostGirdRinder() {
        
        $HTML = "\n\n";
        
        //$HTML .= "// **************************************************************\n";
        //$HTML .= $this->name.".onScroll.subscribe(function (e, args) {\n";
        //$HTML .= "    console.log('scrolling');\n";
        //$HTML .= "    console.log(args);\n";
        //$HTML .= "    Grid2.scrollTo(args.scrollTop);\n";
        //$HTML .= "});\n\n";
        
        
        $HTML .= "// **************************************************************\n";
        $HTML .= "// Wire up model events to update grid from dataView on changes\n";
        $HTML .= "// **************************************************************\n";
        $HTML .= $this->name."Data.onRowCountChanged.subscribe(function (e, args) {\n";
        $HTML .= "    ".$this->name.".updateRowCount();\n";
        $HTML .= "    ".$this->name.".render();\n";
        $HTML .= "});\n\n";
        
        $HTML .= $this->name."Data.onRowsChanged.subscribe(function (e, args) {\n";
        $HTML .= "    ".$this->name.".invalidateRows(args.rows);\n";
        $HTML .= "    ".$this->name.".render();\n";
        $HTML .= "});\n\n";
        
        $HTML .= "// **************************************************************\n";
        $HTML .= "// Wire up model events to update grid from dataView on Tab between cells\n";
        $HTML .= "// **************************************************************\n";
        $HTML .= "    var d = new Date();\n";
        $HTML .= "var LastUpdate=d.getTime();\n";
        $HTML .= $this->name.".onBeforeEditCell.subscribe(function (e, args) {\n";
        $HTML .= "    var d = new Date();\n";
        //$HTML .= "    console.log('position changed '+(LastUpdate+1000)+' '+(d.getTime()));\n";
        $HTML .= "    // Keep updates from queing to fast.\n";
        $HTML .= "    if ((LastUpdate+2500)<(d.getTime())) {\n";
        //$HTML .= "        console.log('updating data '+LastUpdate)\n";
        
        $HTML .= "        ".$this->name."Data.updateDataSync();\n";
        $HTML .= "        LastUpdate=d.getTime();\n";
        $HTML .= "    }\n";
        $HTML .= "});\n\n";
        
        $HTML .= $this->name."Data.onRowsChanged.subscribe(function (e, args) {\n";
        $HTML .= "    ".$this->name.".invalidateRows(args.rows);\n";
        $HTML .= "    ".$this->name.".render();\n";
        $HTML .= "});\n\n";
        
        $HTML .= "\n\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= "// Wire up the sort to the data layer\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= $this->name.".onSort.subscribe(function (e, args) {\n";
        $HTML .= "  var cols = args.sortCols;\n";
        $HTML .= "  sortarray = [];\n";
        $HTML .= "  for (var i = 0, l = cols.length; i < l; i++) {\n";
        $HTML .= "    if (cols[i].sortAsc) \n";
        $HTML .= "      sortarray.push(cols[i].sortCol.field);\n";
        $HTML .= "    else\n";
        $HTML .= "      sortarray.push(cols[i].sortCol.field+' desc');\n";
        $HTML .= "  }\n";
        $HTML .= "  ".$this->name."Data.setSort(sortarray);\n";      
        $HTML .= "  ".$this->name."Data.invalidate();\n";
        $HTML .= "  ".$this->name.".invalidate();\n";
        $HTML .= "  ".$this->name.".render();\n";
        $HTML .= "});\n\n";
        
        $HTML .= "\n\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= "// Wire up row update\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= $this->name.".onCellChange.subscribe(function(e, args) {\n";
        //$HTML .= "console.log(args.item);\n";
        $HTML .= "  ".$this->name."Data.updateItem(args.item); // Send updated row to server\n";
        $HTML .= "});\n";
        
        $HTML .= "\n\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= "// Wire up row add\n";
        $HTML .= "// ****************************************************************\n";
        $HTML .= $this->name.".onAddNewRow.subscribe(function(e, args) {\n";
        //$HTML .= "console.log(args.item);\n";
        $HTML .= "  ".$this->name."Data.addItem(args.item); // Send updated row to server\n";
        $HTML .= "  ".$this->name."Data.invalidate();\n";
       // $HTML .= "  ".$this->name.".invalidate();\n";
       // $HTML .= "  ".$this->name.".render();\n";
        $HTML .= "});\n";
        
        
 
        
        foreach($this->options->plugins as $key=>$plugin) {          
            $HTML .= $plugin->WireEvents($this->name,$this->session,$this->view);
        }
        
        foreach($this->options->plugins as $key=>$plugin) {
            $HTML .= $plugin->WireEvents2($this->name,$this->session,$this->view);
        }
        
//         $HTML .= "// ****************************************************************\n";
//         $HTML .= "// Wire up the header dialog\n";
//         $HTML .= "// ****************************************************************\n";
//         $HTML .= $this->name."FilterDialog = new PHPSlick.Plugins.HeaderDialog({});\n";
//         $HTML .= $this->name."SimpleFilter = new PHPSlick.HeaderDialog.SimpleFilter();\n";
//         $HTML .= $this->name."FilterDialog.registerPlugin(".$this->name."SimpleFilter);\n";
        
//         $HTML .= $this->name."ListFilter = new PHPSlick.HeaderDialog.ListFilter(".$this->name."Options,".$this->name."Data);\n";
//         $HTML .= $this->name."FilterDialog.registerPlugin(".$this->name."ListFilter);\n";
        
//         $HTML .= $this->name.".registerPlugin(".$this->name."FilterDialog);\n";
        
//         $HTML .= "// ****************************************************************\n";
//         $HTML .= "// Wire up the simple filter to the gird refresh\n";
//         $HTML .= "// ****************************************************************\n";
//         $HTML .= $this->name."SimpleFilter.updateFilters.subscribe(function (e, args) {\n";
//         $HTML .= "  var Filters = new Array();\n";
//         $HTML .= "  for(i in ".$this->name."Columns) {\n";
//         $HTML .= "    if (typeof ".$this->name."Columns[i].Filters != 'undefined') { \n";
//         $HTML .= "      Filters[".$this->name."Columns[i].field]=".$this->name."Columns[i].Filters;\n";
//         $HTML .= "    }\n";
//         $HTML .= "  }\n";
//         $HTML .= "  ".$this->name."Data.setWhere(Filters);\n";
//         $HTML .= "  ".$this->name."Data.invalidate();\n";
//         $HTML .= "  ".$this->name.".invalidate();\n";
//         $HTML .= "  ".$this->name.".render();\n";
//         $HTML .= "  ".$this->name."ListFilter.invalidate(Filters);\n";
//         $HTML .= "});\n";
//         $HTML .="\n\n";
        
//         $HTML .= "// ****************************************************************\n";
//         $HTML .= "// Wire up the list filter to the gird refresh\n";
//         $HTML .= "// ****************************************************************\n";
//         $HTML .= $this->name."ListFilter.updateFilters.subscribe(function (e, args) {\n";
//         $HTML .= "  var currentFilter = ".$this->name."Data.getWhere();\n";
//         $HTML .= "  console.log('updatingFilters from Listfiltrs');\n";
//         $HTML .= "  for(var i=0;i<currentFilter.length;i++) {\n";
//         $HTML .= "    if (currentFilter[i].operator=='in') {\n";
//         $HTML .= "     console.log(currentFilter[i]);\n";    
//         $HTML .= "    }\n";
//         $HTML .= "  }\n";
//         $HTML .= "  console.log('args');\n";
//         $HTML .= "  console.log(args);\n";
        
//         $HTML .= "  ".$this->name."Data.setIn(args.column,args.value,args.mode);\n";
//         $HTML .= "  console.log(".$this->name."Data.getWhere());\n";
//         $HTML .= "  ".$this->name."Data.invalidate();\n";
//         $HTML .= "  ".$this->name.".invalidate();\n";
//         $HTML .= "  ".$this->name.".render();\n";
//         $HTML .= "});\n";
//         $HTML .="\n\n";
        
        
        // Admin Column Button
        //$HTML .= $this->name."AdminDialog = new PHPSlick.Plugins.HeaderDialog({buttonCssClass:'phpslick-powerfilter-menubutton2',buttonImage:'../images/columnadmin.png'});\n";
        //$HTML .= $this->name.".registerPlugin(".$this->name."AdminDialog);\n";
        
        $HTML .= "\n\n";
        
        

        
        
        
        return $HTML;
    }
    
    private function RenderColumns() {
        
        // Let plugins modify the column configurations
        foreach($this->options->plugins as $key=>$Plugin) {
            if (method_exists($Plugin,'UpdateColumns'))
                $Plugin->UpdateColumns();
        }
        
        
        // Hide any columns marked as hidden.
        foreach($this->attribs->Hidden as $Column) {
            if (isset($this->attribs->Columns[$Column]))
                $this->attribs->Columns[$Column]->hidden=true;
        }
        
        // Remove the editor from any column marked read only.
        foreach($this->attribs->ReadOnly as $Column) {
            if (isset($this->attribs->Columns[$Column]))
                unset($this->attribs->Columns[$Column]->editor);
        }
        
        
        // This is a hack to overcome the fact the PHP is typeless
        // and the parameters have impled types.
        $integers = array('width','sql_length');
        $objects = array('editor','formatter');
        $booleans = array('sortable','multiColumnSort');
        
        $dont_quote = array_merge($integers,$objects,$booleans);
        
        $column="";
        foreach($this->attribs->Columns as $Column) {
            if (!isset($Column->hidden)) {
                $line="";
                foreach($Column->data as $Key=>$setting) {
                    if (in_array($Key,$dont_quote))
                        if (in_array($Key, $booleans))
                            $line.=$Key.": ".($setting?'true':'false').", ";
                        else 
                            $line.=$Key.": ".$setting.", ";
                    else
                        $line.=$Key.": \"".$setting."\", ";
                }
                $line="\t{".rtrim($line,', ')."},\n";
                $column.=$line;
            }
        }
        $column=rtrim($column,",\n");
        
        $HTML = "var ".$this->name."Columns = [\n$column\n];\n";
         
        return $HTML;
    }
    
    private function RenderOptions() {
        $HTML = (json_encode($this->options->data));
        $ReturnHTML = "var ".$this->name."Options = $HTML;\n";
        
        return $ReturnHTML;
    }
    
    private function loadCss() {
        $this->view->headLink()->appendStylesheet('/slickgrid/slick.grid.css');
        $this->view->headLink()->appendStylesheet('/slickgrid/css/smoothness/jquery-ui-1.8.16.custom.css');
        $this->view->headLink()->appendStylesheet('/phpslickgrid/css/base.css');
        // $this->view->headLink()->appendStylesheet('/slickgrid/plugins/slick.checkboxselectcolumn.js');
        
        foreach($this->options->plugins as $key=>$plugin) {
            foreach($plugin->CSS_Files as $file) {
                $this->view->headLink()->appendStylesheet($file);
            }
        }
        
    }
    
    private function loadJS() {
        // Load the jQuery requried js files.
//        $this->view->headScript()->prependFile('/slickgrid/lib/jquery-1.7.min.js');
        $this->view->headScript()->appendFile('/js/json2.js');
        $this->view->headScript()->appendFile('/js/jquery.zend.jsonrpc.js');
        $this->view->headScript()->appendFile('/js/store.min.js');
        // $this->view->headScript()->appendFile('/slickgrid/lib/jquery-ui-1.8.16.custom.min.js');
        $this->view->headScript()->appendFile('/slickgrid/lib/jquery.event.drag-2.2.js');
        
        // Load the Slickgrid required js files
        $this->view->headScript()->appendFile('/slickgrid/slick.core.js');
        $this->view->headScript()->appendFile('/slickgrid/plugins/slick.cellrangedecorator.js');
        $this->view->headScript()->appendFile('/slickgrid/plugins/slick.cellrangeselector.js');
        $this->view->headScript()->appendFile('/slickgrid/plugins/slick.cellselectionmodel.js');
        
        // Load the PHPSlickgrid required js files
        $this->view->headScript()->appendFile('/phpslickgrid/editors/phpslick.editors.default.js');
        $this->view->headScript()->appendFile('/phpslickgrid/data/datacache.js');
        //$this->view->headScript()->appendFile('/phpslick/powerfilter.js');
        //$this->view->headScript()->appendFile('/phpslick/powerfilter2.js');
//        $this->view->headScript()->appendFile('/phpslick/headerdialog.js');
//        $this->view->headScript()->appendFile('/phpslick/simplefilter.js');
//        $this->view->headScript()->appendFile('/phpslick/listfilter.js');
//        $this->view->headLink()->appendStylesheet('/phpslick/powerfilter.css');
        
        foreach($this->options->plugins as $key=>$plugin) {      
            foreach($plugin->Javascript_File as $file) {
                $this->view->headScript()->appendFile($file);
            }
        }
        
        //$this->view->headScript()->appendFile('/phpslick/plugins/phpslick.rowselectionmodel.js');
        //$this->view->headScript()->appendFile('/phpslick/plugins/phpslick.checkboxselectcolumn.js');
        //$this->view->headScript()->appendFile('/phpslick/controls/phpslick.columnpicker.js');
        
        $this->view->headScript()->appendFile('/slickgrid/slick.formatters.js');
        $this->view->headScript()->appendFile('/slickgrid/slick.grid.js');
        
        //$this->view->headScript()->appendFile('/phpslick/editors/phpslick.editors.select.js');
        
        
        //$this->view->headScript()->appendFile('/phpslick/formatters/checkbox.js');
        //$this->view->headScript()->appendFile('/phpslick/editors/checkbox.js');
    }
}