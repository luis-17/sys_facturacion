app.service("ClienteServices",function($http, $q, handleBehavior) {
    return({
        sBuscarClientes: sBuscarClientes,
        sListarClientesBusqueda: sListarClientesBusqueda 
    });
    function sBuscarClientes(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cliente/buscar_cliente_para_formulario",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarClientesBusqueda(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cliente/buscar_cliente_para_lista",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});