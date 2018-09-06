function getManufacturers(type){
    var manufacturers = doAjax('get', '/api/getmanufacturers/type/'+type);
    var list = [];
    $.each(manufacturers.data, function(index, value){
        list.push({value:value.id, valueText:value.manufacturer_name});
    });
    return list;
}
function getAircraft(){
    var aircraft = doAjax('get', '/api/getaircraftlist');
    var list = [];
    $.each(aircraft.data, function(index, value){
        list.push({value:value.aircraft_id, valueText:value.aircraft_model});
    });
    return list;
}
function getEngines(){
    var engine = doAjax('get', '/api/getenginelist');
    var list = [];
    $.each(engine.data, function(index, value){
        list.push({value:value.id, valueText:value.engine_model});
    });
    return list;
}
function getPropellors(){
    var engine = doAjax('get', '/api/getpropellorlist');
    var list = [];
    $.each(engine.data, function(index, value){
        list.push({value:value.id, valueText:value.propellor_model});
    });
    return list;
}
function getTCDS(type){
    var tcds = doAjax('get', '/api/gettcdslist/type/'+type);
    var list = [];
    $.each(tcds.data, function(index, value){
        list.push({value:value.id, valueText:value.tcds});
    });
    return list;
}
function getAds(type){
    var ads = doAjax('get', '/api/getadlist/type/'+type);
    var list = [];
    $.each(ads.data, function(index, value){
        list.push({value:value.id, valueText:value.number});
    });
    return list;
}

function getOptionData(url){
    return doAjax('get', url).data;
    // var list = [];
    // $.each(response.data, function(index, value){
    //     list.push({value:value, valueText:valueText});
    // });
    // return response.data;
}