app.controller('TipoDocumentoMovCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'TipoDocumentoMovFactory',
  'TipoDocumentoMovServices',
  'SerieServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  TipoDocumentoMovFactory,
  TipoDocumentoMovServices,
  SerieServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones 
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones 
    $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){ 
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 100,
      sort: uiGridConstants.DESC,
      sortName: null,
      search: null
    };
    $scope.columnDefs = [
        { field: 'id', name: 'idtipodocumentomov', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'descripcion_tdm', name: 'descripcion_tdm', displayName: 'Descripción', minWidth: 160 }
    ];
    $scope.gridOptions = {
      rowHeight: 30,
      paginationPageSizes: [10, 50, 100, 500, 1000],
      paginationPageSize: 10,
      enableRowSelection: true,
      enableSelectAll: true,
      enableFiltering: false,
      enableFullRowSelection: true,
      multiSelect: true,
      columnDefs: $scope.columnDefs,
      // columnDefs: [ 
      //   { field: 'id', name: 'idtipodocumentomov', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
      //   { field: 'descripcion_tdm', name: 'descripcion_tdm', displayName: 'Descripción', minWidth: 160 },
      //   { field: 'abreviatura_tdm', name: 'abreviatura_tdm', displayName: 'Abreviatura', minWidth: 100 },
      //   { field: 'porcentaje_imp', name: 'porcentaje_imp', displayName: 'Porcentaje', minWidth: 100 }
      // ],
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
            'tdm.idtipodocumentomov' : grid.columns[1].filters[0].term,
            'tdm.descripcion_tdm' : grid.columns[2].filters[0].term,
            'tdm.abreviatura_tdm' : grid.columns[3].filters[0].term,
            'tdm.porcentaje_imp' : grid.columns[4].filters[0].term
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
      TipoDocumentoMovServices.sListar(arrParams).then(function (rpta) { 
        console.log(rpta,'rpta');
        if( rpta.datos.length == 0 ){
          rpta.paginate = { totalRows: 0 };
        }
        $scope.gridOptions.totalItems = rpta.paginate.totalRows;
        $scope.gridOptions.data = rpta.datos; 
        $scope.gridOptions.data2 = rpta.datos2; 
        var arrColumns = $scope.gridOptions.data2[0];
        console.log(arrColumns,'arrColumns');
        var i = 0;    
        angular.forEach(arrColumns,function (val,key) { 
          i++;
          if( i >2){ 
            var arrObjectColumns = { 
              field: key, 
              displayName: key, 
              cellTemplate: '<span>{{ COL_FIELD }}</span>',
              type: 'number', 
              cellClass:'text-center', 
              enableColumnMenus: false, 
              enableColumnMenu: false,
              enableCellEdit: true, 
              enableSorting: false
            }
            $scope.columnDefs.push(arrObjectColumns);
          }
        });


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
      TipoDocumentoMovFactory.regTipoDocumentoMovModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      }
      TipoDocumentoMovFactory.editTipoDocumentoModal(arrParams); 
    }
    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            id: $scope.mySelectionGrid[0].id 
          };
          blockUI.start('Procesando información...');
          TipoDocumentoMovServices.sAnular(arrParams).then(function (rpta) {
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

    $scope.btnNuevaSerie = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr
    }
    SerieFactory.regSerieModal(arrParams); 
    };

}]);

app.service("TipoDocumentoMovServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarTipoDocParaVenta: sListarTipoDocParaVenta,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/listar_tipo_documento",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarTipoDocParaVenta(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/listar_tipo_documento_mov_para_venta_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }      
});

app.factory("TipoDocumentoMovFactory", function($uibModal, pinesNotifications, blockUI, TipoDocumentoMovServices) { 
  var interfaz = {
    regTipoDocumentoMovModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'TipoDocumentoMov/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Tipo Documento';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            TipoDocumentoMovServices.sRegistrar($scope.fData).then(function (rpta) {
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
    editTipoDocumentoModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'TipoDocumentoMov/ver_popup_formulario',
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
          $scope.titleForm = 'Edición de Tipo Documento';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            TipoDocumentoMovServices.sEditar($scope.fData).then(function (rpta) {
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

