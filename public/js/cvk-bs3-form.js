(function() {
  // Define our constructor
  this.Bs3form = function() {
    var defaults = {
        id: '',
        name: '',
        label: '',
        value: '',
        class: '',
        type: 'text',
        placeholder: '',
        required: false,
        multiple: false
    }
    this.defaults = defaults;
  }

    // Create a text field
    // .textField({type: fieldType, class: fieldClass, name: fieldName, value: fieldValue, placeholder: fieldPlaceholder, required: true/false })
    Bs3form.prototype.textField = function(){
        var options = extendDefaults(this.defaults, arguments[0]);
        var label = makeLabel(options);
        var required = makeRequired(options);
        var placeholder = makePlaceholder(options);
        return [
        '<div class="form-group">',
        label,
        '<input type="'+options.type+'"',
        ' class="form-control ' +options.class+'" ',
        'name="'+options.name+'"',
        'value="'+options.value+'"',
        placeholder,
        required,
        '>',
        '</div>'
        ].join('');
    }
    
    Bs3form.prototype.labelField = function(){
        var options = extendDefaults(this.defaults, arguments[0]);
        var label = makeLabel(options);
        var readonly = makeReadOnly(options);
        var placeholder = makePlaceholder(options);
        
        return [
        '<div class="form-group">',
        label,
        '<input type="'+options.type+'"',
        ' class="form-control ' +options.class+'" ',
        'name="'+options.name+'"',
        'value="'+options.value+'"',
        placeholder,
        readonly,
        '>',
        '</div>'
        ].join('');
    }
    
    Bs3form.prototype.textareaField = function(){
        var options = extendDefaults(this.defaults, arguments[0]);
        var label = makeLabel(options);
        var required = makeRequired(options);
        var placeholder = makePlaceholder(options);

        return [
        '<div class="form-group">',
        label,
        '<textarea rows="'+options.rows+'"',
        ' class="form-control ' +options.class+'" ',
        'id="'+options.id+'"',
        'name="'+options.name+'"',
        placeholder,
        required,
        '>',
        options.value,
        '</textarea>',
        '</div>'
        ].join('');
    }
    
    Bs3form.prototype.labelTextareaField = function(){
        var options = extendDefaults(this.defaults, arguments[0]);
        var label = makeLabel(options);
        var readonly = makeReadOnly(options);
        var placeholder = makePlaceholder(options);

        return [
        '<div class="form-group">',
        label,
        '<textarea rows="'+options.rows+'"',
        ' class="form-control ' +options.class+'" ',
        'id="'+options.id+'"',
        'name="'+options.name+'"',
        placeholder,
        readonly,
        '>',
        options.value,
        '</textarea>',
        '</div>'
        ].join('');
    }
    
    Bs3form.prototype.dateField = function (){
        var options = extendDefaults(this.defaults, arguments[0]);
        var label = makeLabel(options);
        var required = makeRequired(options);
        var placeholder = makePlaceholder(options);
        return [
        '<div class="form-group">',
        label,
        '<div id="'+options.id+'" class="input-group date '+options.class+'">',
        '<input type="text" name="'+options.name+'" class="form-control" ',
        'value="'+options.value+'"',
        placeholder,
        required,
        '>',
            '<span class="input-group-addon">',
                '<span class="glyphicon glyphicon-calendar"></span>',
            '</span>',
        '</div>',
        '</div>'
        ].join('');
    }

    // Create a hidden field
    // .hiddenField({name: fieldName, value: fieldValue})
    Bs3form.prototype.hiddenField = function(){
        data = arguments[0];
        return '<input type="hidden" name="'+data.name+'" value="'+data.value+'">';
    }
    // Create a submit button
    // .submitButton({text: buttonText})
    Bs3form.prototype.submitButton = function(){
        data = arguments[0];
        return '<button type="submit" class="btn btn-info">'+data.text+'</button>';
    }
    // Create a form button
    // .formButton({id: buttonID, label: buttonLabel})
    Bs3form.prototype.formButton = function(){
        data = arguments[0];
        return '<button id="'+data.id+'" class="btn btn-info">'+data.label+'</button>';
    }
    Bs3form.prototype.helpText = function(){
        data = arguments[0];
        return '<span id="'+data.id+'" class="help-block">'+data.text+'</span>';
    }

  /*
    This method will return a select dropdown
        var fields = new Bs3form();

        EXAMPLE pass in an object
            var select = fields.selectField({
                options: '[{value:"1",valueText:"Airbus"},{value:"2",valueText:"Boeing"}]',
                value: 2,
                label: 'Manufacturer',
                class: 'chosen-select'
            });

        EXAMPLE pass in a string
            var select = fields.selectField({
                options: 'Airbus,Boeing,Embraer,Lockheed',
                value: 'Embraer',
                label: 'Manufacturer',
                class: 'chosen-select',
                multiple: true
            });

        options: can be an object or a comma separated strings
            OBJECT: [{value:"1",valueText:"Airbus"},{value:"2",valueText:"Boeing"}]
            STRING: 'Airbus,Boeing,Embraer,Lockheed'
                Note: the string will be turned into an object with 'value' made to equal 'valueText'

        value: this is the user selected option. Option selection keys on 'value'

     */
    Bs3form.prototype.selectField = function(){
        data = arguments[0];
        var isSelected = '';
        if(data.value == null || data.value.length == 0) { 
            var selectedChoice = ''; 
        } else {
            var selectedChoice = data.value.split(',');
        }

        var selectString =  '<div class="form-group">';
        selectString += makeLabel(data);
        selectString += '<select ';
        selectString += makeRequired(data);
        selectString += data.multiple ? ' multiple ' : '';
        selectString += data.id ? ' id="'+data.id+'" ' : '';
        selectString += data.name ? ' name="'+data.name+'" ' : '';
        selectString += ' class="form-control '+data.class+'"';
        selectString += '>';

        if( typeof data.options == 'object'){
            var options = data.options;
        }else{
            // turn string into an object
            var options = [];
            var optionsArray = data.options.split(',');
            $.each(optionsArray, function(index, item){
                options.push({value:item, valueText:item});
            });
        }
        selectString += '<option value="">Select...</option>';
        $.each(options, function(index, item){
           isSelected = ($.inArray(item.value, selectedChoice)>=0) ? ' selected ' : '';
           selectString += '<option value="'+item.value+'" '+isSelected+'>'+item.valueText+'</option>';
        });
        selectString += '</select></div>';
        return selectString;
    }
    
   Bs3form.prototype.navTabList = function(){
        data = arguments[0];
        var string = '<!-- Nav Tabs --><ul class="nav nav-tabs" role="tablist">';
        $.each(data.tabList, function(index, value){
            var active = '';
            if(value.active){
                active = ' class="active" ';
            }
            string += '<li role="presentation"' +active+'><a href="#'+value.id+'" role="tab" data-toggle="tab">'+value.text+'</a></li>';
        });
        return string + '</ul>';
    }
    Bs3form.prototype.navTabContentStart = function(){
        return '<div class="tab-content">';
    }
    Bs3form.prototype.navTabContentEnd = function(){
        return '</div><!-- end .tab-content -->';
    }
    Bs3form.prototype.navTabPaneStart = function(){
        data = arguments[0];
        var active = '';
        if(data.active){
                active = 'active';
            }
        return '<div role="tabpanel" class="tab-pane '+active+'" id="'+ data.id +'">';
    }
    Bs3form.prototype.navTabPaneEnd = function(){
        return '</div><!-- end .tab-pane -->';
    }

    // Utility functions +++++++++++++++++++++++++++++++++++++++++++++++++++++++
    function makeLabel(data){
        return '<label for="'+ data.name + '">' + data.label + '</label>';
    }
    function makeRequired(data){
        if(data.required){
            return ' required ';
        }
    }
    function makeReadOnly(data){
        if(data.readonly){
            return ' readonly ';
        }
    }
    
    function makePlaceholder(data){
        if(data.placeholder){
            return ' placeholder="'+ data.placeholder+ '"';
        }
    }
    function extendDefaults(source, properties){
        if(properties && typeof properties !== "object"){
            return properties;
        }
        var property;
        for (property in properties) {
          if (properties.hasOwnProperty(property)) {
            source[property] = properties[property];
          }
        }
        return source;
    }
}());