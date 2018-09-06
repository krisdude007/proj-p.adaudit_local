"use strict";
versionApp.factory('loadData', function($http){
    return{
        getData: function(url, params){
          return  $http({
                url: url,
                method: 'GET',
                params: params
            });
        }
    };
});

versionApp.factory('saveData', function($http){
    return{
        postData: function(url, data){
          return  $http({
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                url: url,
                method: 'POST',
                data: $.param({data:data})
            })
            .success(function(dataResponse){
            });
        }
    };
});