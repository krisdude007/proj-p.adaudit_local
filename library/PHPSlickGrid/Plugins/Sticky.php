<?php


Class PHPSlickGrid_Plugins_Sticky extends PHPSlickGrid_Plugins_Abstract { 
    
    public $Javascript_File = array("/js/store.min.js");
    
    function __construct($ColumnConfig) {
        $this->ColumnConfig = $ColumnConfig;
        
    }
    
    
    
    // Function Called before render javascript
    public function PreGridRinder($grid_name, $NameSp, $view) {
        $HTML = "\n";
        
        
        $ColumnString="";
        foreach($this->ColumnConfig->Columns as $DBColumn=>$Options) {
            if (!isset($Options->hidden))
                $ColumnString.=$DBColumn;
        }
        
        $MD5=md5($ColumnString);
        
        
        $HTML .= 
               @"
                var columns_md5 = '".$MD5."';
                var stored_md5 = store.get('".$grid_name."_columns_md5');
                if (stored_md5==columns_md5) {
                      if (store.get('".$grid_name."_columns')!=undefined) {
                         var savedcolumns = store.get('".$grid_name."_columns');
                         
                          columns= ".$grid_name."Columns;
                          columnsById = {};
                          for (var i = 0; i < columns.length; i++) {
                            var m = columns[i];// = $.extend({}, columnDefaults, columns[i]);
                            columnsById[m.id] = i;
                          }
                          var newcolumns = [];
                          for(var i = 0; i < savedcolumns.length; i++ ) {
                              if (columns[columnsById[savedcolumns[i].id]]!=undefined) {
                                columns[columnsById[savedcolumns[i].id]].width = savedcolumns[i].width;
    
                                //var cookies= document.cookie.split(';');
                                //console.log('cookies');
                                //console.log();
                                if (document.cookie.indexOf('filters') != -1) {
                                    columns[columnsById[savedcolumns[i].id]].Filters = savedcolumns[i].Filters;
                                    columns[columnsById[savedcolumns[i].id]].inFilters = savedcolumns[i].inFilters;
                                    columns[columnsById[savedcolumns[i].id]].inFiltersMode = savedcolumns[i].inFiltersMode;
                                }
                                else {
                                    delete columns[columnsById[savedcolumns[i].id]].Filters;
                                    delete columns[columnsById[savedcolumns[i].id]].inFilters;
                                    delete columns[columnsById[savedcolumns[i].id]].inFiltersMode;
                                }
                              
                                
                                newcolumns.push(columns[columnsById[savedcolumns[i].id]]);
                              }
                            
                          }
                          
                          
                          store.set('".$grid_name."_columns', newcolumns);
                          
                          
                          ".$grid_name."Columns = newcolumns;
//                          $grid_name.setColumns(".$grid_name."Columns); 
                          document.cookie = 'filters=true';
//                           console.log(".$grid_name."Columns);
//                           console.log(document.referrer);
//                           console.log(document.URL);
//                           console.log(location.pathname);
//                           console.log(document.URL.replace(location.pathname,'')+'/');
                      }
                       
                }
                else
                {
                     store.set('".$grid_name."_columns_md5', '".$MD5."');
                }
                             
                             
                             
            var Filters = new Array();
            for(i in ".$grid_name."Columns) {
              if (typeof ".$grid_name."Columns[i].Filters != 'undefined') { 
                Filters[".$grid_name."Columns[i].field]=".$grid_name."Columns[i].Filters;
              }
            }
            ".$grid_name."Data.setWhere(Filters);
            ".$grid_name."Data.invalidate();
//            ".$grid_name.".invalidate();
//            ".$grid_name.".render();
        ";
        
        
        
        return $HTML;
    }
    
    
    public function WireEvents($grid_name, $NameSp, $view) {
        //$project_id=+$NameSp->Config->project_id;
        
       // $JavaScriptGridSubscribe = $this->Rinder($grid_name, $NameSp, $view);
        
        $JavaScriptGridSubscribe =
        "
                $grid_name.onColumnsReordered.subscribe(function (e, args) {
    
                    columns = $grid_name.getColumns();
    
                    var  newcolumns = [];
                    columnsById = {};
                    for (var i = 0; i < columns.length; i++) {
                        var obj = {
                            width: columns[i].width,
                            id: columns[i].id,
                            Filters: columns[i].Filters,
                            inFilters: columns[i].inFilters,
                            inFiltersMode: columns[i].inFiltersMode
                        }
                        newcolumns.push(obj);
    
                    }
                    store.set('".$grid_name."_columns', newcolumns);
    });
    
    
    
    $grid_name.onColumnsResized.subscribe(function (e, args) {
    
        columns = $grid_name.getColumns();
        
        var  newcolumns = [];
        columnsById = {};
        for (var i = 0; i < columns.length; i++) {
            var obj = {
                width: columns[i].width,
                id: columns[i].id,
                Filters: columns[i].Filters,
                inFilters: columns[i].inFilters,
                inFiltersMode: columns[i].inFiltersMode
            }
            newcolumns.push(obj);
            
        }
        store.set('".$grid_name."_columns', newcolumns);
    } );
    
                
                
    ".$grid_name."HeaderdialogSimpleFilter.updateFilters.subscribe(function (e, args) {
    
        columns = $grid_name.getColumns();
        
        var  newcolumns = [];
        columnsById = {};
        for (var i = 0; i < columns.length; i++) {
            var obj = {
                width: columns[i].width,
                id: columns[i].id,
                Filters: columns[i].Filters,
                inFilters: columns[i].inFilters,
                inFiltersMode: columns[i].inFiltersMode
            }
            newcolumns.push(obj);
            
        }
        store.set('".$grid_name."_columns', newcolumns);
    } );
                
                
    ".$grid_name."HeaderdialogListFilter.updateFilters.subscribe(function (e, args) {
    
        columns = $grid_name.getColumns();
        
        var  newcolumns = [];
        var inFilters = [];
        columnsById = {};
        for (var i = 0; i < columns.length; i++) {
            
        
            
            var obj = {
                width: columns[i].width,
                id: columns[i].id,
                Filters: columns[i].Filters,
                inFilters: columns[i].inFilters,
                inFiltersMode: columns[i].inFiltersMode
            }
            
            
            //console.log('updating filters');
            //console.log(obj);
            newcolumns.push(obj);
            
        }
        //console.log(newcolumns);
        store.set('".$grid_name."_columns', newcolumns);
                
                
                
        savedcolumns = store.get('".$grid_name."_columns');
        //console.log(savedcolumns);         
    } );
         
                
                
    ";
    
    return $JavaScriptGridSubscribe;
    }
    
}