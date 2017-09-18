app.service("UsuarioEmpresaAdminServices",function($http, $q, handleBehavior) {
    return({
        sListarUsuarioEmpresaAdmin: sListarUsuarioEmpresaAdmin, 
        sAgregarUsuarioEmpresaAdmin: sAgregarUsuarioEmpresaAdmin,
        sQuitarEmpresa: sQuitarEmpresa,
        EditarSelectPorDefecto:EditarSelectPorDefecto
    });
    function sListarUsuarioEmpresaAdmin(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"UsuarioEmpresaAdmin/listar_usuario_empresa_admin",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAgregarUsuarioEmpresaAdmin (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"UsuarioEmpresaAdmin/registrar", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sQuitarEmpresa (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"UsuarioEmpresaAdmin/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function EditarSelectPorDefecto (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"UsuarioEmpresaAdmin/editar_selectpordefecto",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }    
});