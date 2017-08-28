app.service("UnidadMedidaServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarCbo: sListarCbo
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"UnidadMedida/xxx",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"UnidadMedida/listar_unidad_medida_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});