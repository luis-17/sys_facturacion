app.service("ElementoServices",function($http, $q, handleBehavior) {
    return({
        sListarElementosAutoComplete: sListarElementosAutoComplete,
        sListarElementosBusqueda: sListarElementosBusqueda
    });
    function sListarElementosAutoComplete(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Elemento/listar_elementos_autocomplete",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    } 
    function sListarElementosBusqueda(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Elemento/buscar_elemento_para_lista",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    } 
});