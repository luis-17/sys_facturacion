app.service("BancoEmpresaAdminServices",function($http, $q, handleBehavior) {
    return({
        sListarBancosDeEstaEmpresa: sListarBancosDeEstaEmpresa, 
        sAgregarBanco: sAgregarBanco,
        sActualizarBanco: sActualizarBanco,
        sQuitarBanco: sQuitarBanco
    });
    function sListarBancosDeEstaEmpresa(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"BancoEmpresaAdmin/listar_contactos_este_banco_empresa",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAgregarBanco (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"BancoEmpresaAdmin/registrar", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sActualizarBanco (datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"BancoEmpresaAdmin/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sQuitarBanco (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"BancoEmpresaAdmin/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});