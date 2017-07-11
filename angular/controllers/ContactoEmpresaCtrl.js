app.service("ContactoEmpresaServices",function($http, $q) {
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
      return (request.then( handleSuccess,handleError ));
    }
    function sAgregarContacto (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/registrar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sActualizarContacto (datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/editar",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sQuitarContacto (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/anular",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
});