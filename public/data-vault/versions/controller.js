"use strict";
versionApp.controller('versionController', ['$scope','$rootScope','loadData','saveData','$uibModal','uiGridConstants', function ($scope, $rootScope, loadData, saveData, $uibModal, uiGridConstants,$log) {

    // Load all of the data
    var loadTheData = function(id){
        $rootScope.$broadcast('loading-started');
        loadData
        .getData('/api/mpd', {id:documentID, vid: versionID})
        .then(function(response){
            if( response.status == 200 && response.data.success == 'ok' ){
                $scope.versionGrid.data = response.data.data;
                $scope.versionGrid.columnDefs = buildColumnDefinition(response.data);
                $scope.columns = JSON.parse(response.data.version.columns);
            }else{
                BootstrapDialog.alert({
                    title:'Error',
                    itemTitle:'There was an error retrieving the analysis data.',
                    type:BootstrapDialog.TYPE_DANGER
                });
            }
            $rootScope.$broadcast('loading-complete');
        });
    };

    var saveTheData = function(url, data){
        $rootScope.$broadcast('saving-started');
        saveData
            .postData(url, data)
            .then(function(response){
                $rootScope.$broadcast('saving-complete');
            })
    };

    $scope.doEditRecord = function(row){
        var result = doAjax('post', '/api/getrow', { id: row.document_id, vid:row.document_version_id, rid:row.id});
        if(result.message == 'ok'){
            var fields = new Bs3form();
            var row = result.data.row;
            var intervals = result.data.intervals;

            var message = '<div class="container"><div class="col-md-9"><form id="editRecordForm" class="form-horizontal">';
            message += fields.hiddenField({id: 'id', name: 'id' , value: row.id})
            message += fields.hiddenField({id: 'document_id', name: 'document_id' , value: row.document_id})
            message += fields.hiddenField({id: 'document_version_id', name: 'document_version_id' , value: row.document_version_id})
            message += fields.navTabList({
                tabList :[{id:'record',text:'Record',active:true},{id:'intervals',text:'Intervals'}]
            });
            message += fields.navTabContentStart();
                message += fields.navTabPaneStart({id:'record',active:true});
                angular.forEach($scope.columns, function(value, key){
                    var columnName = toColumnName(key+1);
                    message += fields.textareaField({
                        id: value,
                        name: columnName,
                        label: value,
                        value: row[columnName]
                    });
                });
                message += fields.helpText({text: 'Record ID: '+ row.id + ' Document ID: '+ row.document_id + ' Document Version ID: ' + row.document_version_id});
                message += fields.navTabPaneEnd();

                message += fields.navTabPaneStart({id:'intervals'});

                    $.each(intervals, function(index, value){
                        message += 'Method: ' + value.method + '<br>';
                        message += 'Type: ' + value.interval_type + '<br>';
                        message += 'Value: ' + value.interval_value + '<br>';
                        message += 'Note: ' + value.interval_note + '<br>';
                        message += 'Interval ID: ' + value.interval_id + '<br>';
                        message += 'Document ID: ' + value.document_item_id + '<p><hr>';
                    })
                    message += fields.helpText({text: 'Record ID: '+ row.id + ' Document ID: '+ row.document_id + ' Document Version ID: ' + row.document_version_id});
                message += fields.navTabPaneEnd();

            message += fields.navTabContentEnd();
            message += '</form></div></div>';
            BootstrapDialog.show({
                title:'Edit record',
                message: message,
                type: BootstrapDialog.TYPE_INFO,
                size: BootstrapDialog.SIZE_WIDE,
                buttons:[
                {
                    label: 'Cancel',
                    cssClass: 'btn-default btn-xs',
                    action: function(dialogRef){
                        dialogRef.close();
                    }
                },{
                    label: 'Save',
                    cssClass: 'btn-primary btn-xs',
                    action: function(dialogRef){
                        var response = doAjax('post','/document/updaterecord',{data:$('#editRecordForm').serializeArray()});
                        console.log('post new record update');
                        console.log(response);
                        dialogRef.close();
                       if(response.message == 'ok'){
                            // location.reload();
                        }else{
                             BootstrapDialog.alert({title:'Error', message:'There was an error updating the document. ERR '+response.text, type: BootstrapDialog.TYPE_WARNING});
                        }
                    }
                }
                ],
                onshown: function(){
                    $('.chosen-select').chosen({width:'100%'});  
                    $('#manufacturer_id').on('change', function() {
                        var manufacturer_id = $(this).val();
                        var selectx = fields.selectField({
                                id:'applicability',
                                name:'applicability',
                                label:'Applicability',
                                class:'chosen-select required',
                                options: doAjax('post', '/api/getselectoptions/',{
                                    options:{
                                        dbTable:'Application_Model_DbTable_Aircraft',
                                        method:'getAicraftSelectByManufacturer',
                                        params: manufacturer_id
                                    }
                                }).data,
                                required:true,
                                placeholder:'Enter the Manufacturer Name'})
                            $("#aircraft_id").html(selectx);
                        $("#aircraft_id").trigger("chosen:updated");
                    });

                    // BOOTSTRAP 3 - DateTimePicker - ref. http://eonasdan.github.io/bootstrap-datetimepicker/
                    $('.datetimepicker').datetimepicker({
                        format: 'YYYY-MM-DD'
                    });
                }
            });
        }
    };

    var toColumnName = function(num) {
        for (var ret = '', a = 1, b = 26; (num -= a) >= 0; a = b, b *= 26) {
            ret = String.fromCharCode(parseInt((num % b) / a) + 65) + ret;
        }
        return ret;
    }

    var buildColumnDefinition = function(columnData){
        var version = columnData.version;
        var returnObject = [];
        // // do not include these columns in the returned string
        var excludeColumnsArray = [
            'id',
            'crea_dtm',
            'crea_usr_id',
            'deleted',
            'document_id',
            'document_version_id',
            'updt_dtm',
            'updt_usr_id',
            '$$hashKey',
        ];
        returnObject.push({
            name: 'Action',
            width:100,
            enableSorting: false,
            enableFiltering: false,
            cellTemplate: '<div class="ui-grid-cell-contents text-center"><button ng-click="grid.appScope.doEditRecord(row.entity)" class="btn btn-default btn-xs">Manage</button></div>'});
        var columns = JSON.parse(columnData.version.columns);
        
        angular.forEach(columns, function(value, key){
            if(excludeColumnsArray.indexOf(key) == -1){
                var columnName = toColumnName(key+1);
                returnObject.push({ field: columnName, name: value, width: 100, cellClass: 'tool-tip', cellTooltip: true});
            }
        });

        return returnObject;
    };

    var init = function(){
        $scope.versionGrid = {
            enableFiltering: true,
            enableRowSelection: false,
            onRegisterApi :function (gridApi) {
                $scope.gridApi = gridApi;
            }
        }
        $scope.versionGrid.data = {};
        loadTheData(documentID, versionID);
    }

    init();
}]);