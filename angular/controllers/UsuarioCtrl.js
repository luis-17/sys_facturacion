app.controller('UsuarioCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'UsuarioFactory',
  'UsuarioServices',
  'ColaboradorFactory',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  UsuarioFactory,
  UsuarioServices,
  ColaboradorFactory
  ) {
    $scope.metodos = {}; // contiene todas las funciones 
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones 
    $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){ 
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    $scope.metodos.listaTipoUsuario = function(myCallback) {
      var myCallback = myCallback || function() { };
      UsuarioServices.sListarCbo().then(function(rpta) {
        $scope.fArr.listaTipoUsuario = rpta.datos; 
        myCallback();
      });
    };    
    var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 100,
      sort: uiGridConstants.DESC,
      sortName: null,
      search: null
    };
    $scope.gridOptions = {
      rowHeight: 30,
      paginationPageSizes: [100, 500, 1000],
      paginationPageSize: 100,
      useExternalPagination: true,
      useExternalSorting: true,
      useExternalFiltering : true,
      enableGridMenu: true,
      enableRowSelection: true,
      enableSelectAll: true,
      enableFiltering: false,
      enableFullRowSelection: true,
      multiSelect: false,
      columnDefs: [ 
        { field: 'id', name: 'idusuario', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'tipo_usuario', name: 'descripcion_tu',cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Tipo Usuario', minWidth: 160 },
        { field: 'username', name: 'username', displayName: 'Username', minWidth: 100 },
         { field: 'password', name: 'password',visible: false, displayName: 'Password', minWidth: 100 },
        { field: 'password_view', name: 'password_view', displayName: 'Contraseña', minWidth: 100 }
      ],
      onRegisterApi: function(gridApi) { 
        $scope.gridApi = gridApi;
        gridApi.selection.on.rowSelectionChanged($scope,function(row){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        });
        gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        });
        $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) { 
          if (sortColumns.length == 0) {
            paginationOptions.sort = null;
            paginationOptions.sortName = null;
          } else {
            paginationOptions.sort = sortColumns[0].sort.direction;
            paginationOptions.sortName = sortColumns[0].name;
          }
          $scope.metodos.getPaginationServerSide(true);
        });
        gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
          paginationOptions.pageNumber = newPage;
          paginationOptions.pageSize = pageSize;
          paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
          $scope.metodos.getPaginationServerSide(true);
        });
        $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
          var grid = this.grid;
          paginationOptions.search = true; 
          paginationOptions.searchColumn = {
            'u.idusuario' : grid.columns[1].filters[0].term,
            'ut.descripcion_tu' : grid.columns[2].filters[0].term,
            'u.username' : grid.columns[4].filters[0].term,
            'u.password_view' : grid.columns[5].filters[0].term,
            'u.password_view' : grid.columns[6].filters[0].term
          }
          $scope.metodos.getPaginationServerSide();
        });
      }
    };
    paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name; 
    $scope.metodos.getPaginationServerSide = function(loader) {
      if( loader ){
        blockUI.start('Procesando información...');
      }
      var arrParams = {
        paginate : paginationOptions
      };
      UsuarioServices.sListar(arrParams).then(function (rpta) { 
        if( rpta.datos.length == 0 ){
          rpta.paginate = { totalRows: 0 };
        }
        $scope.gridOptions.totalItems = rpta.paginate.totalRows;
        $scope.gridOptions.data = rpta.datos; 
        if( loader ){
          blockUI.stop(); 
        }
      });
      $scope.mySelectionGrid = [];
    };
    $scope.metodos.getPaginationServerSide(true); 
    // MAS ACCIONES
    $scope.btnNuevo = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
      }
      UsuarioFactory.regUsuarioModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      }
      UsuarioFactory.editUsuarioModal(arrParams); 
    }
    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            id: $scope.mySelectionGrid[0].id 
          };
          blockUI.start('Procesando información...');
          UsuarioServices.sAnular(arrParams).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              $scope.metodos.getPaginationServerSide();
            }else if(rpta.flag == 0){
              var pTitle = 'Error!';
              var pType = 'danger';
            }else{
              alert('Error inesperado');
            }
            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
            blockUI.stop(); 
          });
        }
      });
    }
}]);

app.service("UsuarioServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular,
        sListarCbo: sListarCbo
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/listar_usuario",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }       
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/listar_usuario_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("UsuarioFactory", function($uibModal, pinesNotifications, blockUI, UsuarioServices) { 
  var interfaz = {
    regUsuarioModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Usuario/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          console.log($scope.fData,'$scope.fData');
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Usuario';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          var myCallBackCC = function() { 
            $scope.fArr.listaTipoUsuario.splice(0,0,{ id : '0', descripcion:'--Seleccione tipo usuario--'}); 
            $scope.fData.tipo_usuario = $scope.fArr.listaTipoUsuario[0]; 
          }
          $scope.metodos.listaTipoUsuario(myCallBackCC); 
          $scope.modoEdit = true;
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            console.log('aqui');
            UsuarioServices.sRegistrar($scope.fData).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                console.log($scope.fData,'$scope.fData');
                  $scope.fData.username='aqui';
                $uibModalInstance.dismiss('cancel');
                if(typeof $scope.metodos.getPaginationServerSide == 'function'){ 
                  $scope.metodos.getPaginationServerSide(true);
                }
              }else if(rpta.flag == 0){
                var pTitle = 'Error!';
                var pType = 'danger';
              }else{
                alert('Error inesperado');
              }
              blockUI.stop(); 
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
            });
          } 
        },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
      });
    },
    editUsuarioModal: function (arrParams) {
      console.log(arrParams,'arrParams');
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Usuario/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr; 

          if( arrParams.mySelectionGrid.length == 1 ){ 
            $scope.fData = arrParams.mySelectionGrid[0];
            console.log($scope.fData ,'$scope.fData ');
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de Usuario';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          //BINDEO TIPO USUARIO
          var myCallBackCC = function() { 
            var objIndex = $scope.fArr.listaTipoUsuario.filter(function(obj) { 

              return obj.id == $scope.fData.tipo_usuario.id;
            }).shift(); 
            $scope.fData.tipo_usuario = objIndex; 
          }
          $scope.metodos.listaTipoUsuario(myCallBackCC); 
          $scope.modoEdit = false;
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            UsuarioServices.sEditar($scope.fData).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $uibModalInstance.dismiss('cancel');
                if(typeof $scope.metodos.getPaginationServerSide == 'function'){
                  $scope.metodos.getPaginationServerSide(true);
                }
              }else if(rpta.flag == 0){
                var pTitle = 'Error!';
                var pType = 'danger';
              }else{
                alert('Error inesperado');
              }
              blockUI.stop(); 
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
            });
          } 
        },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
      });
    }
  }
  return interfaz;
})
