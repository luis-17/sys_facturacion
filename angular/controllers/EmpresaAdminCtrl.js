app.controller('EmpresaAdminCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'EmpresaAdminFactory',
  'EmpresaAdminServices',
  'BancoServices',
  'BancoEmpresaAdminServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  EmpresaAdminFactory,
  EmpresaAdminServices,
  BancoServices,
  BancoEmpresaAdminServices
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
        { field: 'id', name: 'idempresaadmin', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 160 },
        { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 160 },
        { field: 'ruc', name: 'ruc', displayName: 'RUC', minWidth: 100 },
        { field: 'direccion_legal', name: 'direccion_legal', displayName: 'Dirección Legal', minWidth: 160 },
        { field: 'representante_legal', name: 'representante_legal', displayName: 'Representante Legal', minWidth: 160 },
        { field: 'telefono', name: 'telefono', displayName: 'Teléfono', minWidth: 100 },
        { field: 'pagina_web', name: 'pagina_web', displayName: 'Página Web', minWidth: 100, enableColumnMenus: false, enableColumnMenu: false}
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
            'ea.idempresaadmin' : grid.columns[1].filters[0].term,
            'ea.razon_social' : grid.columns[2].filters[0].term,
            'ea.nombre_comercial' : grid.columns[3].filters[0].term,
            'ea.ruc' : grid.columns[4].filters[0].term,
            'ea.direccion_legal' : grid.columns[5].filters[0].term,
            'ea.representante_legal' : grid.columns[6].filters[0].term,
            'ea.telefono' : grid.columns[7].filters[0].term,
            'ea.pagina_web' : grid.columns[8].filters[0].term
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
      EmpresaAdminServices.sListar(arrParams).then(function (rpta) { 
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
      EmpresaAdminFactory.regEmpresaAdminModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      }
      EmpresaAdminFactory.editEmpresaAdminModal(arrParams); 
    }
    $scope.btnAsignarBancos = function() { 
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'EmpresaAdmin/ver_popup_bancos',
        size: 'lg',
        backdrop: 'static',
        keyboard:false,
        scope: $scope,
        controller: function ($scope, $uibModalInstance) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.fBanco = {};
          $scope.fArr = {}; 
          $scope.metodos = {};
          $scope.editClassForm = null;
          $scope.tituloBloque = 'Agregar Banco';
          $scope.contBotonesReg = true;
          $scope.contBotonesEdit = false;
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fData = $scope.mySelectionGrid[0];
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Bancos';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          } 
          $scope.btnBuscarBancos = function(){
            $scope.gridOptionsBancos.enableFiltering = !$scope.gridOptionsBancos.enableFiltering;
            $scope.gridApiBanco.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
          };
          //TIPOS DE MONEDA
          $scope.fArr.listaMoneda = [ 
            {'id' : 'S', 'descripcion' : 'S/'},
            {'id' : 'D', 'descripcion' : 'US$'}
          ]; 
          $scope.metodos.listaBanco = function(myCallback) {
            var myCallback = myCallback || function() { };
            BancoServices.sListarCbo().then(function(rpta) {
              $scope.fArr.listaBanco = rpta.datos; 
              myCallback();
            });
          };
          var paginationOptionBancos = {
            pageNumber: 1,
            firstRow: 0,
            pageSize: 10,
            sort: uiGridConstants.DESC,
            sortName: null,
            search: null
          };
          $scope.gridOptionsBancos = { 
            rowHeight: 30,
            paginationPageSizes: [10, 50, 100, 500, 1000],
            paginationPageSize: 10,
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
              { field: 'id', name: 'idbancoempresaadmin', displayName: 'ID',  visible: false, width: '75',  sort: { direction: uiGridConstants.DESC} },
              { field: 'banco', name: 'descripcion_ba',cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Banco', minWidth: 160 },
              { field: 'num_cuenta', name: 'num_cuenta', displayName: 'N° Cuenta', minWidth: 160 },
              { field: 'num_cuenta_inter', name: 'num_cuenta_inter', displayName: 'N° cuenta Interbancaria', minWidth: 160 }, 
              { field: 'moneda', name: 'desc_moneda',cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>', displayName: 'Moneda', minWidth: 100 }
            ],
            onRegisterApi: function(gridApiBanco) { 
              $scope.gridApiBanco = gridApiBanco;
              gridApiBanco.selection.on.rowSelectionChanged($scope,function(row){
                $scope.mySelectionGridBanco = gridApiBanco.selection.getSelectedRows(); 
                // EDICIÓN DE BANCO
                if( $scope.mySelectionGridBanco.length == 1 ){
                  $scope.editClassForm = ' edit-form'; 
                  $scope.tituloBloque = 'Edición de Banco';
                  $scope.contBotonesReg = false;
                  $scope.contBotonesEdit = true;
                  $scope.fBanco = $scope.mySelectionGridBanco[0];
                  // BINDEO BANCO
                  var myCallBackBa = function() { 
                    var objIndex = $scope.fArr.listaBanco.filter(function(obj) { 
                      return obj.id == $scope.fBanco.banco.id;
                    }).shift(); 
                    $scope.fBanco.banco = objIndex; 
                  }
                  $scope.metodos.listaBanco(myCallBackBa);  
                  // BINDEO MONEDA     
                  var objIndex = $scope.fArr.listaMoneda.filter(function(obj) { 
                    return obj.id == $scope.fBanco.moneda.id;
                  }).shift(); 
                  $scope.fBanco.moneda = objIndex;                               
                }else{
                  console.log();
                  $scope.editClassForm = null; 
                  $scope.tituloBloque = 'Agregar Banco';
                  $scope.contBotonesReg = true;
                  $scope.contBotonesEdit = false;
                }
                /* END */
              });
              gridApiBanco.selection.on.rowSelectionChangedBatch($scope,function(rows){
                $scope.mySelectionGridBanco = gridApiBanco.selection.getSelectedRows();
              });

              $scope.gridApiBanco.core.on.sortChanged($scope, function(grid, sortColumns) { 
                if (sortColumns.length == 0) {
                  paginationOptionBancos.sort = null;
                  paginationOptionBancos.sortName = null;
                } else {
                  paginationOptionBancos.sort = sortColumns[0].sort.direction;
                  paginationOptionBancos.sortName = sortColumns[0].name;
                }
                $scope.metodos.getPaginationServerSideBancos(true);
              });
              gridApiBanco.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                paginationOptionBancos.pageNumber = newPage;
                paginationOptionBancos.pageSize = pageSize;
                paginationOptionBancos.firstRow = (paginationOptionBancos.pageNumber - 1) * paginationOptionBancos.pageSize;
                $scope.metodos.getPaginationServerSideBancos(true);
              });
              $scope.gridApiBanco.core.on.filterChanged( $scope, function(grid, searchColumns) {
                var grid = this.grid;
                paginationOptionBancos.search = true; 
                paginationOptionBancos.searchColumn = {
                  'bea.idbancoempresaadmin' : grid.columns[1].filters[0].term,
                  'ba.descripcion_ba' : grid.columns[2].filters[0].term,      
                  'bea.num_cuenta' : grid.columns[3].filters[0].term,
                  'bea.num_cuenta_inter' : grid.columns[4].filters[0].term
                  // 'co.email' : grid.columns[6].filters[0].term 
                }
                $scope.metodos.getPaginationServerSideBancos();
              }); 
            }
          };

          $scope.quitarBanco = function() {
            var pMensaje = '¿Realmente desea anular el registro?';
              $bootbox.confirm(pMensaje, function(result) {
                if(result){
                  var arrParams = {
                    idbancoempresaadmin: $scope.fBanco.id 
                  }
                  blockUI.start('Procesando información...');
                  BancoEmpresaAdminServices.sQuitarBanco(arrParams).then(function (rpta) {
                    if(rpta.flag == 1){
                      var pTitle = 'OK!';
                      var pType = 'success';
                      $scope.metodos.getPaginationServerSideBancos();
                      $scope.editClassForm = null; 
                      $scope.tituloBloque = 'Agregar Banco';
                      $scope.contBotonesReg = true;
                      $scope.contBotonesEdit = false;
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

          $scope.actualizarBanco = function() { 
            blockUI.start('Procesando información...'); 
            BancoEmpresaAdminServices.sActualizarBanco($scope.fBanco).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $scope.fBanco = {};
                $scope.metodos.getPaginationServerSideBancos(true); 
                $scope.editClassForm = null; 
                $scope.tituloBloque = 'Agregar Banco';
                $scope.contBotonesReg = true;
                $scope.contBotonesEdit = false;
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
          paginationOptionBancos.sortName = $scope.gridOptionsBancos.columnDefs[0].name;
          $scope.metodos.getPaginationServerSideBancos = function(loader) {
            if( loader ){
              blockUI.start('Procesando información...');
            }
            var arrParams = { 
              paginate : paginationOptionBancos,
              datos: $scope.fData 
            };
            BancoEmpresaAdminServices.sListarBancosDeEstaEmpresa(arrParams).then(function (rpta) { 
              $scope.gridOptionsBancos.totalItems = rpta.paginate.totalRows;
              $scope.gridOptionsBancos.data = rpta.datos; 
              if( loader ){
                blockUI.stop(); 
              }
            });
            $scope.mySelectionGridBanco = [];

            $scope.fBanco.moneda = $scope.fArr.listaMoneda[0];  
            var myCallBackBa = function() { 
              $scope.fArr.listaBanco.splice(0,0,{ id : '0', descripcion:'--Seleccione banco--'}); 
              $scope.fBanco.banco = $scope.fArr.listaBanco[0]; 
            }
            $scope.metodos.listaBanco(myCallBackBa); 
          };
          $scope.metodos.getPaginationServerSideBancos(true); 
          $scope.agregarBanco = function () { 
            blockUI.start('Procesando información...');
            $scope.fBanco.idempresaadmin = $scope.fData.id; 
            console.log( $scope.fBanco.idempresaadmin,' $scope.fBanco.idempresaadmin');
            BancoEmpresaAdminServices.sAgregarBanco($scope.fBanco).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $scope.fBanco = {};
                $scope.metodos.getPaginationServerSideBancos(true); 
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
          EmpresaAdminServices.sAnular(arrParams).then(function (rpta) {
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

app.service("EmpresaAdminServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular,
        sListarCbo:sListarCbo
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"EmpresaAdmin/listar_empresa_admin",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"EmpresaAdmin/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"EmpresaAdmin/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"EmpresaAdmin/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"EmpresaAdmin/listar_empresa_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    } 


});

app.factory("EmpresaAdminFactory", function($uibModal, pinesNotifications, blockUI, EmpresaAdminServices) { 
  var interfaz = {
    regEmpresaAdminModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'EmpresaAdmin/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Empresa';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }       
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            EmpresaAdminServices.sRegistrar($scope.fData).then(function (rpta) {
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
    editEmpresaAdminModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'EmpresaAdmin/ver_popup_formulario',
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
          $scope.titleForm = 'Edición de Empresa';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }         
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            EmpresaAdminServices.sEditar($scope.fData).then(function (rpta) {
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

