app.service("FormaPagoServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarCbo: sListarCbo,
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FormaPago/xxxx",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FormaPago/listar_formas_pago_cbo", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});