app.controller('ColaboradorCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'ColaboradorFactory',
  'ColaboradorServices',
  'UsuarioServices',
  'UsuarioFactory',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  ColaboradorFactory,
  ColaboradorServices,
  UsuarioServices,
  UsuarioFactory
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
      UsuarioServices.sListarTipoUsuarioCbo().then(function(rpta) {
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
        { field: 'id', name: 'idcolaborador', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'nombres', name: 'nombres', displayName: 'Nombres', minWidth: 140 },
        { field: 'apellidos', name: 'apellidos', displayName: 'Apellidos', minWidth: 140 },
        { field: 'num_documento', name: 'num_documento', displayName: 'N° Documento', minWidth: 100 }, 
        { field: 'telefono', name: 'telefono', displayName: 'Telefono', minWidth: 120 }, 
        { field: 'email', name: 'email', displayName: 'Correo', minWidth: 180 },
        { field: 'fecha_nacimiento', name: 'fecha_nacimiento', displayName: 'Fecha de Nacimiento', minWidth: 100}, 
        { field: 'tipo_usuario', type: 'object', name: 'tipo_usuario', displayName: 'Tipo usuario', minWidth: 100, 
          cellTemplate:'<div class="ui-grid-cell-contents text-center "><label class="label bg-primary block">{{ COL_FIELD.descripcion }}</label></div>' 
        }         
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
            'co.idcolaborador' : grid.columns[1].filters[0].term,
            'co.nombres' : grid.columns[2].filters[0].term,
            'co.apellidos' : grid.columns[3].filters[0].term,
            'co.num_documento' : grid.columns[4].filters[0].term,
            'co.telefono' : grid.columns[5].filters[0].term,
            'co.email' : grid.columns[6].filters[0].term,
            'co.fecha_nacimiento' : grid.columns[7].filters[0].term,
            'tu.descripcion_tu' : grid.columns[8].filters[0].term 
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
      ColaboradorServices.sListar(arrParams).then(function (rpta) { 
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
      ColaboradorFactory.regColaboradorModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      }
      ColaboradorFactory.editColaboradorModal(arrParams); 
    }
    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            id: $scope.mySelectionGrid[0].id,
            idusuario: $scope.mySelectionGrid[0].idusuario  
          };
          blockUI.start('Procesando información...');
          ColaboradorServices.sAnular(arrParams).then(function (rpta) {
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

app.service("ColaboradorServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarCbo: sListarCbo,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Colaborador/listar_colaboradores",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Colaborador/listar_colaboradores_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Colaborador/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Colaborador/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Colaborador/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("ColaboradorFactory", function($uibModal, pinesNotifications, blockUI, ColaboradorServices,UsuarioServices,UsuarioFactory) { 
  var interfaz = {
    regColaboradorModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Colaborador/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams,UsuarioFactory) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Colaborador';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.btnNuevoUsuario = function() { 
            var arrParams = {
              'metodos': $scope.metodos,
              'fArr': $scope.fArr, 
              callback: function(datos,rpta) {
                $scope.fData.username = datos.username;
                $scope.fData.idusuario = rpta.idusuario;
              }
            }
            UsuarioFactory.regUsuarioModal(arrParams); 
          };
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            console.log($scope.fData,'$scope.fData');
            ColaboradorServices.sRegistrar($scope.fData).then(function (rpta) {
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
    },
    editColaboradorModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Colaborador/ver_popup_formulario',
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
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de Colaborador';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.btnNuevoUsuario = function() { 
            var arrParamss = {
              'metodos': $scope.metodos,
              'mySelectionGrid': arrParams.mySelectionGrid,
              'fArr': $scope.fArr,
              callback: function(datos) {
                console.log(datos,'datos');
              } 
            }
            console.log(arrParams,'arrParams');
            UsuarioFactory.editUsuarioModal(arrParamss); 
          }
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            ColaboradorServices.sEditar($scope.fData).then(function (rpta) {
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
