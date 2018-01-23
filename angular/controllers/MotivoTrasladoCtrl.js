app.service("MotivoTrasladoServices",function($http, $q, handleBehavior) {
    return({ 
        sListarCbo: sListarCbo
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"MotivoTraslado/listar_motivos_traslado_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});