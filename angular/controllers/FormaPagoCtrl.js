app.controller('FormaPagoCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'FormaPagoFactory',
  'FormaPagoServices',
  'PlazoFormaPagoServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  FormaPagoFactory,
  FormaPagoServices,
  PlazoFormaPagoServices
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
        { field: 'id', name: 'fp.idformapago', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'descripcion_fp', name: 'fp.descripcion_fp', displayName: 'Descripción', minWidth: 160 },
         { field: 'descripcion_modo_fp', name: 'descripcion_modo_fp', displayName: 'Modo', minWidth: 160 }
      ],
      onRegisterApi: function(gridApi) { 
        $scope.gridApi = gridApi;
        gridApi.selection.on.rowSelectionChanged($scope,function(row){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
          $scope.mySelectionGrid.plazo = $scope.mySelectionGrid[0].modo_fp;

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
            'fp.idformapago' : grid.columns[1].filters[0].term,
            'fp.descripcion_fp' : grid.columns[2].filters[0].term,
            'fp.descripcion_modo_fp' : grid.columns[3].filters[0].term    
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
      FormaPagoServices.sListar(arrParams).then(function (rpta) { 
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
      FormaPagoFactory.regFormaPagoModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      }
      FormaPagoFactory.editFormaPagoModal(arrParams); 
    }
    $scope.btnPlazo = function() { 
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'PlazoFormaPago/ver_popup_plazo_forma_pago',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        scope: $scope,
        controller: function ($scope, $uibModalInstance) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.fPlazo = {};
          $scope.fArr = {}; 
          $scope.metodos = {};
          $scope.editClassForm = null;
          $scope.tituloBloque = 'Agregar Plazo';
          $scope.contBotonesReg = true;
          $scope.contBotonesEdit = false;
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fData = $scope.mySelectionGrid[0];
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Plazos';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          } 
          $scope.metodos.getPaginationServerSidePlazo = function(loader) {

            if( loader ){
              blockUI.start('Procesando información...');
            }
            var arrParams = {       
              datos: $scope.fData 
            };

            PlazoFormaPagoServices.sListarPlazoFormaPago(arrParams).then(function (rpta) { 
               console.log(rpta.datos,'rpta');
               $scope.fPlazo.plazolista=rpta.datos;
                  var total = 0;
                  var totaldia = 0;
                  angular.forEach($scope.fPlazo.plazolista,function (value, key) { 
                     total += parseFloat($scope.fPlazo.plazolista[key].porcentaje_importe); 
                     totaldia += parseFloat($scope.fPlazo.plazolista[key].dias_transcurridos);                
                  });
                  $scope.fPlazo.totalimporte=total;
                  if($scope.fPlazo.totalimporte==100){              
                    $scope.completado=false;
                  }else{
                    $scope.completado=true;
                  }
                  $scope.focusdt = function () {              
                    $scope.focus = true;
                    $scope.fPlazo.dias_transcurridos=totaldia;           
                  }
                  $scope.focuspi = function () {                
                    $scope.focus = true;  
                    $scope.fPlazo.porcentaje_importe=100-$scope.fPlazo.totalimporte;
                  }
              if( loader ){
                blockUI.stop(); 
              }
            });
          };
          $scope.metodos.getPaginationServerSidePlazo(true); 
          $scope.quitarplazo = function(index){
            angular.forEach($scope.fPlazo.plazolista, function(value, key){              
                if(key==index){
                  console.log(value);
                  var pMensaje = '¿Realmente desea anular el registro?';
                    $bootbox.confirm(pMensaje, function(result) {
                      if(result){
                        var arrParams = {
                          id: value.id 
                        }
                        blockUI.start('Procesando información...');
                        PlazoFormaPagoServices.sQuitarPlazoFormaPago(arrParams).then(function (rpta) {
                          if(rpta.flag == 1){
                            var pTitle = 'OK!';
                            var pType = 'success';
                           $scope.metodos.getPaginationServerSidePlazo(true); 
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
            });                        
          }
          $scope.editarplazo = function(index){
            var total = 0;
            angular.forEach($scope.fPlazo.plazolista,function (value, key) { 
                     total += parseFloat($scope.fPlazo.plazolista[key].porcentaje_importe);                
            });             
            angular.forEach($scope.fPlazo.plazolista, function(value, key){             
                if(key==index){     
                  $scope.fPlazo.datos= value;   
                  $scope.fPlazo.total= total;             
                  PlazoFormaPagoServices.EditarPlazoFormaPago($scope.fPlazo).then(function (rpta) {
                    if(rpta.flag == 1){
                      var pTitle = 'OK!';
                      var pType = 'success';
                      $scope.metodos.getPaginationServerSidePlazo(true); 
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
            });                        
          }

          $scope.agregarPlazo = function () { 
            blockUI.start('Procesando información...');
            $scope.fPlazo.idformapago = $scope.fData.id;     
            PlazoFormaPagoServices.sAgregaPlazoFormaPago($scope.fPlazo).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $scope.fPlazo = {};
                $scope.metodos.getPaginationServerSidePlazo(true); 
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
        }
      });
    } 

    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            id: $scope.mySelectionGrid[0].id 
          };
          blockUI.start('Procesando información...');
          FormaPagoServices.sAnular(arrParams).then(function (rpta) {
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

app.service("FormaPagoServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular,      
        sListarCbo: sListarCbo,
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FormaPago/listar_forma_pago",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FormaPago/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FormaPago/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FormaPago/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FormaPago/listar_formas_pago_cbo", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("FormaPagoFactory", function($uibModal, pinesNotifications, blockUI, FormaPagoServices) { 
  var interfaz = {
    regFormaPagoModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'FormaPago/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Forma Pago';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }

          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            FormaPagoServices.sRegistrar($scope.fData).then(function (rpta) {
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
    editFormaPagoModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'FormaPago/ver_popup_formulario',
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
          $scope.titleForm = 'Edición de Forma Pago';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            FormaPagoServices.sEditar($scope.fData).then(function (rpta) {
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

