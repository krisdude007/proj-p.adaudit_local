window.toolEvent = {
    'click .edit': function(e, value, row, index){
        doEditRecord({url:'/api/getrow', params:{ id: row.document_id, vid:row.document_version_id, rid:row.id}});
    },
    'click .viewInterval': function(e, value, row, index){
        doViewInterval(row);
    },
    'click .delete': function(e, value, row, index){
        BootstrapDialog.confirm({
            title:"Confirm",
            message:"Are you sure that you want to delete this row?",
            type: BootstrapDialog.TYPE_WARNING,
            callback: function(result){
                if(result){
                    doDelete(row);
                }else{

                }
            }
        });
    }
}

// function doEditRecord(data)
// {
//     var result = doAjax('post', data.url, data.params);
//     if(result.message == 'ok'){
//         var fields = new Bs3form();
//         var row = result.data.row;
//         var intervals = result.data.intervals;

//         var message = '<div class="container"><div class="col-md-9"><form id="editRecordForm" class="form-horizontal">';
//         message += fields.hiddenField({id: 'id', name: 'id' , value: row.id})
//         message += fields.hiddenField({id: 'document_id', name: 'document_id' , value: row.document_id})
//         message += fields.hiddenField({id: 'document_version_id', name: 'document_version_id' , value: row.document_version_id})
//         message += fields.navTabList({
//             tabList :[{id:'record',text:'Record',active:true},{id:'intervals',text:'Intervals'}]
//         });
//         message += fields.navTabContentStart();
//             message += fields.navTabPaneStart({id:'record',active:true});
//                 message += fields.textField({
//                     id:'identifier',
//                     name:'identifier',
//                     label:'Reference Number',
//                     value: row.identifier
//                 });
//                 message += fields.textareaField({
//                     id:'task_desc',
//                     name:'task_desc',
//                     label:'Task Description',
//                     value: row.task_desc
//                 });
//                 message += fields.textareaField({
//                     id:'effectivity',
//                     name:'effectivity',
//                     label:'Effectivity',
//                     value: row.effectivity
//                 });
//                 message += fields.textareaField({
//                     id:'access',
//                     name:'access',
//                     label:'Access',
//                     value: row.access
//                 });
//                 message += fields.textField({
//                     id:'amm',
//                     name:'amm',
//                     label:'AMM',
//                     value: row.amm
//                 });
//                 message += fields.textField({
//                     id:'int_repeat',
//                     name:'int_repeat',
//                     label:'Interval Repeat',
//                     value: row.int_repeat
//                 });
//                 message += fields.textField({
//                     id:'int_threshold',
//                     name:'int_threshold',
//                     label:'Interval Threshold',
//                     value: row.int_threshold
//                 });
//                 message += fields.helpText({text: 'Record ID: '+ row.id + ' Document ID: '+ row.document_id + ' Document Version ID: ' + row.document_version_id});
//             message += fields.navTabPaneEnd();

//             message += fields.navTabPaneStart({id:'intervals'});

//                 $.each(intervals, function(index, value){
//                     message += 'Method: ' + value.method + '<br>';
//                     message += 'Type: ' + value.interval_type + '<br>';
//                     message += 'Value: ' + value.interval_value + '<br>';
//                     message += 'Note: ' + value.interval_note + '<br>';
//                     message += 'Interval ID: ' + value.interval_id + '<br>';
//                     message += 'Document ID: ' + value.document_item_id + '<p><hr>';
//                 })
//                 message += fields.helpText({text: 'Record ID: '+ row.id + ' Document ID: '+ row.document_id + ' Document Version ID: ' + row.document_version_id});
//             message += fields.navTabPaneEnd();

//         message += fields.navTabContentEnd();
//         message += '</form></div></div>';
//         BootstrapDialog.show({
//             title:'Edit record',
//             message: message,
//             type: BootstrapDialog.TYPE_INFO,
//             size: BootstrapDialog.SIZE_WIDE,
//             buttons:[
//             {
//                 label: 'Cancel',
//                 cssClass: 'btn-default btn-xs',
//                 action: function(dialogRef){
//                     dialogRef.close();
//                 }
//             },{
//                 label: 'Save',
//                 cssClass: 'btn-primary btn-xs',
//                 action: function(dialogRef){
//                     var response = doAjax('post','/document/updaterecord',{data:$('#editRecordForm').serializeArray()});
//                     dialogRef.close();
//                    if(response.message == 'ok'){
//                         location.reload();
//                     }else{
//                          BootstrapDialog.alert({title:'Error', message:'There was an error deleting the document. ERR '+response.text, type: BootstrapDialog.TYPE_WARNING});
//                     }
//                 }
//             }
//             ],
//             onshown: function(){
//                 $('.chosen-select').chosen({width:'100%'});  
//                 $('#manufacturer_id').on('change', function() {
//                     var manufacturer_id = $(this).val();
//                     var selectx = fields.selectField({
//                             id:'applicability',
//                             name:'applicability',
//                             label:'Applicability',
//                             class:'chosen-select required',
//                             options: doAjax('post', '/api/getselectoptions/',{
//                                 options:{
//                                     dbTable:'Application_Model_DbTable_Aircraft',
//                                     method:'getAicraftSelectByManufacturer',
//                                     params: manufacturer_id
//                                 }
//                             }).data,
//                             required:true,
//                             placeholder:'Enter the Manufacturer Name'})
//                         $("#aircraft_id").html(selectx);
//                     $("#aircraft_id").trigger("chosen:updated");
//                 });

//                 // BOOTSTRAP 3 - DateTimePicker - ref. http://eonasdan.github.io/bootstrap-datetimepicker/
//                 $('.datetimepicker').datetimepicker({
//                     format: 'YYYY-MM-DD'
//                 });
//             }
//         });
//     }
// }

function doViewInterval(row) {
    var result = doAjax('get', '/versions/getintervals', {document_id: row.document_id, document_item_id: row.id});
    if(result.message = 'ok'){
        var intervalData = JSON.parse(result.data);
        var returnString;

        $.each(intervalData, function(index, value){
            returnString += "Method " + value.method;
            returnString += "Type: " + value.interval_type;
            returnString += "Value: " + value.interval_value;
            returnString += "Description: " + value.interval_description;
            returnString += "Note: " + value.interval_note;
            returnString += "Document Item ID: " + value.document_item_id;
            returnString += "Interval ID: " + value.interval_id;
            returnString += "<hr>";
        });

        // Now build the Dialog
        // 
        console.log(returnString);
    }else{
        console.log('ERROR');
    }
}

function doDelete(row){
    var result = doAjax('post', '/document/deleterecord', {did: row.document_id, vid: row.document_version_id, rid: row.id});
    if(result.message == 'ok'){
        BootstrapDialog.alert({
            title:'Success', 
            message:'Record was deleted.',
            type:BootstrapDialog.TYPE_SUCCESS
        });
      $('#table').bootstrapTable('refresh');
    }else{
        BootstrapDialog.alert({
            title:'Error', 
            message:'There was an error deleting the record.',
            type:BootstrapDialog.TYPE_DANGER
        });
    }
}




function changeBarCellStyle(value,row, index)
{
    return{
        classes: row.change_bar
    }
}
function testFormatter(value, row, index)
{
    return '<em>'+value+'</em>';
}
function nl2brFormatter(value, row, index)
{
    if(value == null){
        return '';
    }else{
        return nl2br(value);
    }
}
function intFormatter(value, row, index)
{
    if(value == null){
        return value;
    }else{
        return nl2br(value);
    }
}
function toolFormatter(value, row, index)
{
    return [
        '<a class="edit" href="javascript:void(0)" title="Edit this row">',
        '<i class="fa fa-pencil"></i>',
        '</a>',
        '&nbsp;',
        '<a class="viewInterval" href="javascript:void(0)" title="View parsed intervals for this row">',
        '<i class="fa fa-search"></i>',
        '</a>',
        '&nbsp;',
        '<a class="delete" href="javascript:void(0)" title="Delete row id '+row.id+'">',
        '<i class="fa fa-trash"></i>',
        '</a>'
    ].join('');
}

function getHeight() {
    return $(window).height() - $('.fixed-table-toolbar').outerHeight(true) - $('.row').outerHeight(true) - $('.navbar').outerHeight(true) - $('.container').outerHeight(true) ;
}
 $(window).resize(function () {
    $('#table').bootstrapTable('resetView', {
        height: getHeight()
    });
});