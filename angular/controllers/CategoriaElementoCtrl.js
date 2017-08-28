app.service("CategoriaElementoServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarCbo: sListarCbo,
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CategoriaElemento/xxxx",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CategoriaElemento/listar_categoria_elemento_cbo", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});