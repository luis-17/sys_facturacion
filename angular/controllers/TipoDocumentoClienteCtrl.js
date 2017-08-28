app.service("TipoDocumentoClienteServices",function($http, $q, handleBehavior) {
    return({ 
        sListarCbo: sListarCbo
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoCliente/listar_tipo_documento_cliente_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});