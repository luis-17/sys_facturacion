app.controller('CajaCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'CajaFactory',
  'CajaServices',
  'SerieServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  CajaFactory,
  CajaServices,
  SerieServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones 
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones 
    $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){ 
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    $scope.metodos.listaSerie = function(myCallback) {
      var myCallback = myCallback || function() { };
      SerieServices.sListarCbo().then(function(rpta) {
        $scope.fArr.listaSerie = rpta.datos; 
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
        { field: 'id', name: 'idcaja', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'nombre_caja', name: 'nombre_caja', displayName: 'Nombre', minWidth: 160 },
        { field: 'maquina_reg', name: 'maquina_reg', displayName: 'Maquina registradora', minWidth: 100 },
        { field: 'serie', name: 'descripcion_ser', minWidth: 100, 
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Serie' },
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
            'c.idcaja' : grid.columns[1].filters[0].term,
            'c.nombre_caja' : grid.columns[2].filters[0].term,
            'c.maquina_reg' : grid.columns[3].filters[0].term
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
      CajaServices.sListar(arrParams).then(function (rpta) { 
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
      CajaFactory.regCajaModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      }
      CajaFactory.editCajaModal(arrParams); 
    }
    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            id: $scope.mySelectionGrid[0].id 
          };
          blockUI.start('Procesando información...');
          CajaServices.sAnular(arrParams).then(function (rpta) {
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

app.service("CajaServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular    
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Caja/listar_caja",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Caja/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Caja/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Caja/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }      
});

app.factory("CajaFactory", function($uibModal, pinesNotifications, blockUI, CajaServices) { 
  var interfaz = {
    regCajaModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Caja/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Caja';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          var myCallBackCC = function() { 
            $scope.fArr.listaSerie.splice(0,0,{ id : '0', descripcion:'--Seleccione serie--'}); 
            $scope.fData.serie = $scope.fArr.listaSerie[0]; 
          }
          $scope.metodos.listaSerie(myCallBackCC); 
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            CajaServices.sRegistrar($scope.fData).then(function (rpta) {
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
    editCajaModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Caja/ver_popup_formulario',
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
          $scope.titleForm = 'Edición de Caja';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          //BINDEO SERIE
          var myCallBackCC = function() { 
            var objIndex = $scope.fArr.listaSerie.filter(function(obj) { 
              console.log(obj,'obj');
              console.log($scope.fData,'$scope.fData');
              return obj.id == $scope.fData.serie.id;
            }).shift(); 
            $scope.fData.serie = objIndex; 
          }
          $scope.metodos.listaSerie(myCallBackCC); 
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            CajaServices.sEditar($scope.fData).then(function (rpta) {
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

