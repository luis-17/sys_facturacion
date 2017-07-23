app.service("ContactoEmpresaServices",function($http, $q, handleBehavior) {
    return({
        sListarContactosDeEstaEmpresa: sListarContactosDeEstaEmpresa, 
        sAgregarContacto: sAgregarContacto,
        sActualizarContacto: sActualizarContacto,
        sQuitarContacto: sQuitarContacto
    });
    function sListarContactosDeEstaEmpresa(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/listar_contactos_esta_empresa",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAgregarContacto (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/registrar", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sActualizarContacto (datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sQuitarContacto (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});