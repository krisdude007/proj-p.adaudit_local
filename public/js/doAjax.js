function doAjax(method, url, data){
  var result;
  $.ajax({
      method: method,
      url: url,
      data: data,
      dataType:'json',
      async: false,
      headers: {
        'X-API-TOKEN': '1528cdb1c39205850bdb6713f0b1ab1509bd52de'
      },
      success: function(response) {
          result = response;
          // console.log(result.message);
          // if(result.message=="fail") {
          //   result = '';
          // }
      },
      error: function (e, status) {
          var errorText;
          if (e.status != 200){
              errorText = 'There was a server error. Error code: ' + e.status;
          }
          if (e.status == 500){
              errorText = 'The server had an error and could not complete the task.';
          }
          if (e.status == 404){
              errorText = 'The server could not be found.';
          }
          if (e.status == 401){
            errorText = 'The connection to the server is not authorized.'
          }
          result = {'message':'error', 'text':errorText};
      }
  });
  return result;
};