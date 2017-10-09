app.service("SerieServices",function($http, $q, handleBehavior) {
    return({
        sListarCbo: sListarCbo
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Serie/listar_serie_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }    
});