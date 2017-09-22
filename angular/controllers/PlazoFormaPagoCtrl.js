app.service("PlazoFormaPagoServices",function($http, $q, handleBehavior) {
    return({
        sListarPlazoFormaPago: sListarPlazoFormaPago, 
        sAgregaPlazoFormaPago: sAgregaPlazoFormaPago,
        sQuitarPlazoFormaPago: sQuitarPlazoFormaPago,
        EditarPlazoFormaPago:EditarPlazoFormaPago
    });
    function sListarPlazoFormaPago(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"PlazoFormaPago/listar_plazo_forma_pago",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAgregaPlazoFormaPago (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"PlazoFormaPago/registrar", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sQuitarPlazoFormaPago (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"PlazoFormaPago/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function EditarPlazoFormaPago (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"PlazoFormaPago/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }    
});