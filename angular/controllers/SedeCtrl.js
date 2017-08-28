app.service("SedeServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarCbo: sListarCbo,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Sede/listar_sedes_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Sede/listar_sedes_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Sede/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Sede/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Sede/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});