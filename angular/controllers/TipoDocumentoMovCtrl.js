app.controller('TipoDocumentoMovCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'TipoDocumentoMovFactory',
  'SerieFactory',
  'TipoDocumentoMovServices',
  'SerieServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  TipoDocumentoMovFactory,
  SerieFactory,
  TipoDocumentoMovServices,
  SerieServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones 
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones 
    $scope.mySelectionGrid = [];
    $scope.fArr.columnDefs = [ 
        { field: 'idtipodocumentomov', name: 'tdm.idtipodocumentomov', displayName: 'ID', width: 75, 
          sort: { direction: uiGridConstants.ASC}, enableCellEdit: false 
        },
        { field: 'tipo_documento', name: 'descripcion_tdm', displayName: 'Descripción', width: 250, enableCellEdit: false }, 
        { field: 'abreviatura', name: 'abreviatura_tdm', displayName: 'Abreviatura', width: 140, enableCellEdit: false }
    ];
    $scope.gridOptions = {
      rowHeight: 30,
      paginationPageSizes: [100, 500, 1000],
      paginationPageSize: 100,
      enableRowSelection: true,
      //enableSelectAll: true,
      enableFiltering: false,
      enableFullRowSelection: true,
      multiSelect: false,
      columnDefs: $scope.fArr.columnDefs,
      onRegisterApi: function(gridApi) { 
        $scope.gridApi = gridApi;
        gridApi.selection.on.rowSelectionChanged($scope,function(row){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        });
        gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        }); 
        gridApi.edit.on.afterCellEdit($scope,function(rowEntity, colDef, newValue, oldValue){ 
          var arrEditCell = {
            'serie' : colDef.field,
            'correlativo' : newValue,
            'idtipodocumentomov' : rowEntity.idtipodocumentomov
          }
          SerieServices.sEditarCorrelativoActual(arrEditCell).then(function (rpta) { 
            if(rpta.flag == 1){
              pTitle = 'OK!';
              pType = 'success'; 
            }else if(rpta.flag == 0){
              var pTitle = 'Error!';
              var pType = 'danger';
            }else{
              alert('Error inesperado');
            }
            $scope.metodos.listaTipoDocumentoMovFormat();
            $scope.fData = {};
            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
          });
          $scope.$apply();
        });
      }
    };
    // $scope.metodos.listaTipoDocumentoMov = function() { 
    //   TipoDocumentoMovServices.sListarParaGrilla().then(function (rpta) { 
    //     $scope.gridOptions.data = rpta.datos;
    //   });
    // }
    $scope.metodos.listaTipoDocumentoMovFormat = function () { 
      TipoDocumentoMovServices.sListarParaGrilla().then(function (rpta) { 
        $scope.gridOptions.data = rpta.datos; 
        var arrColumns = $scope.gridOptions.data[0]; 
        var i = 0; 
        console.log('datos cajas', $scope.gridOptions.data); 
        angular.forEach(arrColumns,function (val,key) { 
          i++;
          if( i > 5 ){ 
            var arrObjectColumns = { 
              field: key, 
              name: 'campo_' + key,
              displayName: 'SERIE N° ' + key, 
              cellTemplate: '<span>{{ COL_FIELD }}</span>',
              type: 'number', 
              cellClass:'ui-editCell text-center', 
              enableColumnMenus: false, 
              enableColumnMenu: false,
              enableCellEdit: true, 
              enableSorting: false,
              enableCellEditOnFocus: false 
            }
            $scope.fArr.columnDefs.push(arrObjectColumns);
          }
        });
        // $scope.$apply();
      });
    }
    $scope.metodos.listaTipoDocumentoMovFormat(); 
    $scope.btnFormatoImpresion = function() {
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      };
      arrParams.myCallbackFI = function() { 
        $scope.$parent.reloadPage(); 
      };
      TipoDocumentoMovFactory.formatoImpresionModal(arrParams); 
    }
    // MAS ACCIONES
    $scope.btnNuevo = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
      };
      arrParams.myCallbackSerie = function() { 
        $scope.$parent.reloadPage(); 
      };
      TipoDocumentoMovFactory.regTipoDocumentoMovModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      };
      arrParams.myCallbackSerie = function() { 
        $scope.$parent.reloadPage(); 
      };
      TipoDocumentoMovFactory.editTipoDocumentoModal(arrParams); 
    }
    $scope.btnNuevaSerie = function() { 
      var arrParams = { 
        'metodos': $scope.metodos,
        'fArr': $scope.fArr
      }; 
      arrParams.myCallbackSerie = function() { 
        $scope.$parent.reloadPage(); 
      }
      SerieFactory.regSerieModal(arrParams); 
    };
    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            idtipodocumentomov: $scope.mySelectionGrid[0].idtipodocumentomov 
          };
          blockUI.start('Procesando información...');
          TipoDocumentoMovServices.sAnular(arrParams).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              $scope.$parent.reloadPage(); 
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

app.service("TipoDocumentoMovServices",function($http, $q, handleBehavior) {
    return({
        sListarParaGrilla: sListarParaGrilla,
        sListarFormatoImpresion: sListarFormatoImpresion,
        sListarTipoDocParaVentaCbo: sListarTipoDocParaVentaCbo,
        sEditarFormatoImpresion: sEditarFormatoImpresion,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListarParaGrilla(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/listar_tipo_documento_mov",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarFormatoImpresion(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/listar_formato_impresion",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarTipoDocParaVentaCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/listar_tipo_documento_mov_para_venta_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditarFormatoImpresion(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/editar_formato_impresion",
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

app.factory("TipoDocumentoMovFactory", function($uibModal, pinesNotifications, blockUI, TipoDocumentoMovServices, uiGridConstants) { 
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
                arrParams.myCallbackSerie();
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
                arrParams.myCallbackSerie();
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
    formatoImpresionModal: function(arrParams) {
      blockUI.start('Abriendo configuración...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'TipoDocumentoMov/ver_popup_formato_impresion',
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
          $scope.titleForm = 'Configuración de Formato de Impresión';
          $scope.fArr.gridOptionsFI = { 
            useExternalPagination: false,
            useExternalSorting: false,
            enableGridMenu: false,
            enableRowSelection: true,
            enableSelectAll: false,
            enableFiltering: false,
            enableFullRowSelection: false,
            enableCellEditOnFocus: true,
            enableColumnMenus: false, 
            enableColumnMenu: false,
            multiSelect: false, 
            columnDefs: [ 
              { field: 'idtdconfigdetalle', name: 'tcd.idtdconfigdetalle', displayName: 'ID', width: 75, sort: { direction: uiGridConstants.ASC}, enableCellEdit: false 
              },
              { field: 'descripcion', name: 'descripcion_elemento', displayName: 'Descripción', width: 250, enableCellEdit: false }, 
              { field: 'valor_x', name: 'valor_x', displayName: 'Pos. X', width: 80, enableCellEdit: true, cellClass:'ui-editCell' },
              { field: 'valor_y', name: 'valor_y', displayName: 'Pos. Y', width: 80, enableCellEdit: true, cellClass:'ui-editCell' },
              { field: 'visible', name: 'visible', displayName: 'Visible', width: 80, enableCellEdit: true, cellClass:'ui-editCell', 
                editableCellTemplate: 'ui-grid/dropdownEditor', 
                cellFilter: 'mapVisible', editDropdownValueLabel: 'visible', editDropdownOptionsArray: [
                  { id: 1, visible: 'SI' },
                  { id: 2, visible: 'NO' }
                ],cellTemplate: '<div class="text-center ui-grid-cell-contents" ng-if="COL_FIELD == 1"> SI </div><div class="text-center" ng-if="COL_FIELD == 2"> NO </div>' }
            ], 
            onRegisterApi: function(gridApi) { 
              $scope.gridApi = gridApi;
              gridApi.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
                var arrEditCell = {
                  'campo' : colDef.field,
                  'nuevo_valor' : newValue,
                  'idtdconfigdetalle' : rowEntity.idtdconfigdetalle 
                }
                TipoDocumentoMovServices.sEditarFormatoImpresion(arrEditCell).then(function (rpta) { 
                  if(rpta.flag == 1){
                    pTitle = 'OK!';
                    pType = 'success'; 
                  }else if(rpta.flag == 0){
                    var pTitle = 'Error!';
                    var pType = 'danger';
                  }else{
                    alert('Error inesperado');
                  }
                  $scope.metodos.getPaginationServerSideFI();
                  //$scope.fData = {};
                  pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
                });
                $scope.$apply();
              });
            }
          }; 
          $scope.metodos.getPaginationServerSideFI = function(loader) { 
            if(loader){
              blockUI.start('Procesando información...'); 
            }
            var arrParamsAux = {
              'idtipodocumentomov': $scope.fData.idtipodocumentomov 
            }; 
            TipoDocumentoMovServices.sListarFormatoImpresion(arrParamsAux).then(function (rpta) { 
              $scope.fArr.gridOptionsFI.data = rpta.datos.detalle;
              // $scope.fData.
              if(loader){
                blockUI.stop(); 
              }
            });
          }
          $scope.metodos.getPaginationServerSideFI(); 
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
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
}); 

app.filter('mapVisible', function() { 
  var visibleHash = { 
    1: 'SI',
    2: 'NO'
  };
  return function(input) {
    if (!input){
      return '';
    } else {
      return visibleHash[input];
    }
  };
});

