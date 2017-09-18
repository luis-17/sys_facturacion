app.controller('UsuarioCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'UsuarioFactory',
  'UsuarioServices',
  'UsuarioEmpresaAdminServices',
  'ColaboradorFactory',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  UsuarioFactory,
  UsuarioServices,
  UsuarioEmpresaAdminServices,
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
        { field: 'idusuario', name: 'idusuario', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'tipo_usuario', name: 'descripcion_tu',cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Tipo Usuario', minWidth: 160 },
        { field: 'username', name: 'username', displayName: 'Username', minWidth: 100 },
         { field: 'password', name: 'password',visible: false, displayName: 'Password', minWidth: 100 },
        { field: 'password_view', name: 'password_view',visible: false, displayName: 'Contraseña', minWidth: 100 }
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
        'fArr': $scope.fArr ,
        callback: function() {      
        }        
      }
      UsuarioFactory.regUsuarioModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr,
        callback: function() {      
        }     
      }
      UsuarioFactory.editUsuarioModal(arrParams); 
    }
    $scope.btnUsuarioEmpresa = function() { 
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'UsuarioEmpresaAdmin/ver_popup_usuario_empresa',
        size: 'lg',
        backdrop: 'static',
        keyboard:false,
        scope: $scope,
        controller: function ($scope, $uibModalInstance) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.fEmpresa = {};
          $scope.fArr = {}; 
          $scope.metodos = {};
          $scope.editClassForm = null;
          $scope.tituloBloque = 'Agregar Empresa';
          $scope.contBotonesReg = true;
          $scope.contBotonesEdit = false;
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fData = $scope.mySelectionGrid[0];
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Empresa';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          } 
          $scope.btnBuscarEmpresa = function(){
            $scope.gridOptionsEmpresa.enableFiltering = !$scope.gridOptionsEmpresa.enableFiltering;
            $scope.gridApiEmpresa.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
          };
          var paginationOptionEmpresa = {
            pageNumber: 1,
            firstRow: 0,
            pageSize: 10,
            sort: uiGridConstants.DESC,
            sortName: null,
            search: null
          };
          $scope.gridOptionsEmpresa = { 
            rowHeight: 30,
            paginationPageSizes: [10, 50, 100, 500, 1000],
            paginationPageSize: 10,
            useExternalPagination: true,
            useExternalSorting: true,
            useExternalFiltering : true,
            enableGridMenu: true,
            enableRowSelection: false,
            enableSelectAll: false,
            enableFiltering: true,
            enableFullRowSelection: false,
            enableCellEditOnFocus: true,
            multiSelect: false,
            columnDefs: [ 
              { field: 'id', name: 'idusuarioempresaadmin', displayName: 'ID',visible: false,enableCellEdit: false , width: '75',  sort: { direction: uiGridConstants.DESC} },
              { field: 'select_por_defecto', name: 'select_por_defecto', displayName: 'Seleccione', width: 90, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell',editableCellTemplate: 'ui-grid/dropdownEditor',              cellFilter: 'select_por_defecto', editDropdownValueLabel: 'select_por_defecto', editDropdownOptionsArray: [
                  { id: 1, select_por_defecto: 'SI' },
                  { id: 2, select_por_defecto: 'NO' }
                ],cellTemplate: '<div class="text-center ui-grid-cell-contents" ng-if="COL_FIELD == 1"> SI </div><div class="text-center" ng-if="COL_FIELD == 2"> NO </div>'
              },
              { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 160 ,enableCellEdit: false },
              { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 160,enableCellEdit: false  },
              { field: 'ruc', name: 'ruc', displayName: 'RUC', minWidth: 100,enableCellEdit: false  },
              { field: 'direccion_legal', name: 'direccion_legal', displayName: 'Dirección Legal', minWidth: 160,enableCellEdit: false  },
              { field: 'representante_legal', name: 'representante_legal', displayName: 'Representante Legal', minWidth: 160,enableCellEdit: false  },
              { field: 'telefono', name: 'telefono', displayName: 'Teléfono', minWidth: 100 ,enableCellEdit: false },
              { field: 'pagina_web', name: 'pagina_web', displayName: 'Página Web', minWidth: 100, enableColumnMenus: false, enableColumnMenu: false,enableCellEdit: false }
            ],
            onRegisterApi: function(gridApiEmpresa) { 
              $scope.gridApiEmpresa = gridApiEmpresa;
              $scope.msg = {};   
              gridApiEmpresa.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
                $scope.msg.lastCellEdited = [{'edited_row_id' : rowEntity.id} , {'Column' : colDef.field} , { 'newValue' : newValue} , {'oldValue' : oldValue} ,  {'id_cabecera' : colDef.name} , {'idusuario' : $scope.fData.idusuario} ];
                UsuarioEmpresaAdminServices.EditarSelectPorDefecto($scope.msg.lastCellEdited).then(function (rpta) {
                   console.log(rpta,'aquiss');       
                    if(rpta.flag == 1){
                      var pTitle = 'OK!';
                      var pType = 'success';
                      $scope.metodos.getPaginationServerSideEmpresa();
                    }else if(rpta.flag == 0){
                      var pTitle = 'Error!';
                      var pType = 'danger';
                      $scope.metodos.getPaginationServerSideEmpresa();
                    }else{
                      alert('Error inesperado');
                    }
                    pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
                    blockUI.stop();                     
                });
              });

              gridApiEmpresa.selection.on.rowSelectionChanged($scope,function(row){
                $scope.mySelectionGridEmpresa = gridApiEmpresa.selection.getSelectedRows(); 
                // EMPRESA         
                if( $scope.mySelectionGridEmpresa.length == 1 ){
                  $scope.editClassForm = ' edit-form'; 
                  $scope.tituloBloque = 'Edición de Empresa';
                  $scope.contBotonesReg = false;
                  $scope.contBotonesEdit = true;
                  $scope.fEmpresa = $scope.mySelectionGridEmpresa[0];                              
                }else{     
                  $scope.editClassForm = null; 
                  $scope.tituloBloque = 'Agregar Empresa';
                  $scope.contBotonesReg = true;
                  $scope.contBotonesEdit = false;
                }
                /* END */
              });
              gridApiEmpresa.selection.on.rowSelectionChangedBatch($scope,function(rows){
                $scope.mySelectionGridEmpresa = gridApiEmpresa.selection.getSelectedRows();
              });

              $scope.gridApiEmpresa.core.on.sortChanged($scope, function(grid, sortColumns) { 
                if (sortColumns.length == 0) {
                  paginationOptionEmpresa.sort = null;
                  paginationOptionEmpresa.sortName = null;
                } else {
                  paginationOptionEmpresa.sort = sortColumns[0].sort.direction;
                  paginationOptionEmpresa.sortName = sortColumns[0].name;
                }
                $scope.metodos.getPaginationServerSideEmpresa(true);
              });
              gridApiEmpresa.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                paginationOptionEmpresa.pageNumber = newPage;
                paginationOptionEmpresa.pageSize = pageSize;
                paginationOptionEmpresa.firstRow = (paginationOptionEmpresa.pageNumber - 1) * paginationOptionEmpresa.pageSize;
                $scope.metodos.getPaginationServerSideEmpresa(true);
              });
              $scope.gridApiEmpresa.core.on.filterChanged( $scope, function(grid, searchColumns) {
                var grid = this.grid;
                paginationOptionEmpresa.search = true; 
                paginationOptionEmpresa.searchColumn = {
                  'uea.idusuarioempresaadmin' : grid.columns[1].filters[0].term,
                  'ea.razon_social' : grid.columns[2].filters[0].term,      
                  'ea.nombre_comercial' : grid.columns[3].filters[0].term,
                  'ea.ruc' : grid.columns[4].filters[0].term,
                  'ea.direccion_legal' : grid.columns[5].filters[0].term,
                  'ea.representante_legal' : grid.columns[6].filters[0].term,
                  'ea.telefono' : grid.columns[7].filters[0].term,
                  'ea.pagina_web' : grid.columns[8].filters[0].term

                }
                $scope.metodos.getPaginationServerSideEmpresa();
              }); 
            }
          };

          $scope.quitarEmpresa = function() {
            var pMensaje = '¿Realmente desea anular el registro?';
              $bootbox.confirm(pMensaje, function(result) {
                if(result){
                  console.log($scope.fEmpresa,'$scope.fEmpresa');
                  var arrParams = {
                    id: $scope.fEmpresa.id 
                  }
                  blockUI.start('Procesando información...');
                  UsuarioEmpresaAdminServices.sQuitarEmpresa(arrParams).then(function (rpta) {
                    if(rpta.flag == 1){
                      var pTitle = 'OK!';
                      var pType = 'success';
                      $scope.metodos.getPaginationServerSideEmpresa();
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
          paginationOptionEmpresa.sortName = $scope.gridOptionsEmpresa.columnDefs[0].name;
          $scope.metodos.getPaginationServerSideEmpresa = function(loader) {
            if( loader ){
              blockUI.start('Procesando información...');
            }
            var arrParams = { 
              paginate : paginationOptionEmpresa,
              datos: $scope.fData 
            };
            UsuarioEmpresaAdminServices.sListarUsuarioEmpresaAdmin(arrParams).then(function (rpta) { 
              $scope.gridOptionsEmpresa.totalItems = rpta.paginate.totalRows;
              $scope.gridOptionsEmpresa.data = rpta.datos; 
              if( loader ){
                blockUI.stop(); 
              }
            });
            $scope.mySelectionGridEmpresa = [];
          };
          $scope.metodos.getPaginationServerSideEmpresa(true); 
          $scope.agregarUsuarioEmpresa = function () { 
            blockUI.start('Procesando información...');
            $scope.fEmpresa.idusuario = $scope.fData.idusuario; 
            UsuarioEmpresaAdminServices.sAgregarUsuarioEmpresaAdmin($scope.fEmpresa).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $scope.fEmpresa = {};
                $scope.metodos.getPaginationServerSideEmpresa(true); 
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
            idusuario: $scope.mySelectionGrid[0].idusuario 
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
              arrParams.callback($scope.fData,rpta);
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
            console.log(arrParams,'arrParams.mySelectionGrid');
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
              arrParams.callback($scope.fData);
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

