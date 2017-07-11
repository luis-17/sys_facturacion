app.service("CategoriaClienteServices",function($http, $q) {
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
            url : angular.patchURLCI+"CategoriaCliente/listar_categoria_cliente",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CategoriaCliente/listar_categoria_cliente_cbo",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CategoriaCliente/registrar",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CategoriaCliente/editar",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CategoriaCliente/anular",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
});