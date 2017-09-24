app.controller('ContactoEmpresaCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'ContactoEmpresaFactory',
  'ContactoEmpresaServices',
  'ClienteEmpresaServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  ContactoEmpresaFactory,
  ContactoEmpresaServices,
  ClienteEmpresaServices
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
        { field: 'id', name: 'idcontacto', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'nombres', name: 'nombres', displayName: 'Nombre', minWidth: 160 },
        { field: 'apellidos', name: 'apellidos', displayName: 'Apellidos', minWidth: 160 },
        { field: 'fecha_nacimiento', name: 'fecha_nacimiento', displayName: 'Fecha Nacimiento', minWidth: 160, visible:false },
        { field: 'telefono_movil', name: 'telefono_movil', displayName: 'Teléfono Móvil', minWidth: 100 },
        { field: 'telefono_fijo', name: 'telefono_fijo', displayName: 'Teléfono Fijo', minWidth: 160 },
        { field: 'anexo', name: 'anexo', displayName: 'Anexo', width: 90 },
        { field: 'area_encargada', name: 'area_encargada', displayName: 'Area Encargada', minWidth: 140 },
        { field: 'nombre_comercial', name: 'nombre_comercial',  displayName: 'Cliente Empresa', minWidth: 160 },
        { field: 'email', name: 'email', displayName: 'E-mail', minWidth: 100, visible:false } 
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
            'co.idcontacto' : grid.columns[1].filters[0].term,
            'co.nombres' : grid.columns[2].filters[0].term,
            'co.apellidos' : grid.columns[3].filters[0].term,
            'co.fecha_nacimiento' : grid.columns[4].filters[0].term,
            'co.telefono_movil' : grid.columns[5].filters[0].term,
            'co.telefono_fijo' : grid.columns[6].filters[0].term,
            'co.anexo' : grid.columns[7].filters[0].term,
            'co.area_encargada' : grid.columns[8].filters[0].term,
            'co.nombre_comercial' : grid.columns[9].filters[0].term,
            'co.email' : grid.columns[10].filters[0].term
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
      ContactoEmpresaServices.sListar(arrParams).then(function (rpta) { 
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
      ContactoEmpresaFactory.regContactoModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr 
      }
      ContactoEmpresaFactory.editContactoModal(arrParams); 
    }
    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            id: $scope.mySelectionGrid[0].id 
          };
          blockUI.start('Procesando información...');
          ContactoEmpresaServices.sAnular(arrParams).then(function (rpta) {
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

app.service("ContactoEmpresaServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular,      
        sListarContactosDeEstaEmpresa: sListarContactosDeEstaEmpresa, 
        sAgregarContacto: sAgregarContacto,
        sActualizarContacto: sActualizarContacto,
        sQuitarContacto: sQuitarContacto,
        sListarContactoAutoComplete:sListarContactoAutoComplete,
        sListarContactoBusqueda:sListarContactoBusqueda
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/listar_contacto",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    } 
    function sListarContactosDeEstaEmpresa(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/listar_contactos_esta_empresa",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAgregarContacto (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/registrar", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sActualizarContacto (datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sQuitarContacto (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarContactoAutoComplete (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/listar_contacto_empresa_autocomplete",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }

    function sListarContactoBusqueda (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ContactoEmpresa/buscar_contacto_para_lista",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    
});

app.factory("ContactoEmpresaFactory", function($uibModal, pinesNotifications, blockUI, uiGridConstants, ContactoEmpresaServices,ClienteEmpresaServices, ClienteServices) { 
  var interfaz = {
    regContactoModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'ContactoEmpresa/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Contacto';
          $scope.btnBusquedaPersonaJuridica = function() {
            blockUI.start('Procesando información...'); 
            $uibModal.open({ 
              templateUrl: angular.patchURLCI+'Cliente/ver_popup_busqueda_clientes',
              size: 'md',
              backdrop: 'static',
              keyboard:false,
              scope: $scope,
              controller: function ($scope, $uibModalInstance) { 
                blockUI.stop(); 
                $scope.fBusqueda = {};
                $scope.fBusqueda.origen = 'contacto';
                //if($scope.fData.tipo_documento_cliente.destino == 1){ // empresa
                $scope.fBusqueda.tipo_cliente = 'ce'; 
                //}
                $scope.titleForm = 'Búsqueda de Clientes'; 
                var paginationOptionsBC = {
                  pageNumber: 1,
                  firstRow: 0,
                  pageSize: 100,
                  sort: uiGridConstants.ASC,
                  sortName: null,
                  search: null
                };
                $scope.mySelectionGridBC = [];
                $scope.fArr.gridOptionsBC = { 
                  paginationPageSizes: [100, 500, 1000, 10000],
                  paginationPageSize: 100,
                  useExternalPagination: true,
                  useExternalSorting: true,
                  enableGridMenu: true,
                  enableRowSelection: true,
                  enableSelectAll: false,
                  enableFiltering: true,
                  enableFullRowSelection: true,
                  multiSelect: false,
                  columnDefs: [
                      { field: 'id', name: 'ce.idclienteempresa', displayName: 'ID', width: 50,  sort: { direction: uiGridConstants.ASC}, visible: false },
                      { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 200 },
                      { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 150 },
                      { field: 'num_documento', name: 'num_documento', displayName: 'N° Documento', width: 90 }
                  ],
                  onRegisterApi: function(gridApi) { // gridComboOptions
                    $scope.gridApi = gridApi;
                    gridApi.selection.on.rowSelectionChanged($scope,function(row){
                      $scope.mySelectionGridBC = gridApi.selection.getSelectedRows();
                      $scope.fData.cliente_empresa = {
                        id: $scope.mySelectionGridBC[0].id,
                        descripcion: $scope.mySelectionGridBC[0].nombre_comercial 
                      };
                      $uibModalInstance.dismiss('cancel');
                    });
                    $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
                      if (sortColumns.length == 0) {
                        paginationOptionsBC.sort = null;
                        paginationOptionsBC.sortName = null;
                      } else {
                        paginationOptionsBC.sort = sortColumns[0].sort.direction;
                        paginationOptionsBC.sortName = sortColumns[0].name;
                      }
                      $scope.metodos.getPaginationServerSideBC(true);
                    });
                    gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                      paginationOptionsBC.pageNumber = newPage;
                      paginationOptionsBC.pageSize = pageSize;
                      paginationOptionsBC.firstRow = (paginationOptionsBC.pageNumber - 1) * paginationOptionsBC.pageSize;
                      $scope.metodos.getPaginationServerSideBC(true);
                    });
                    $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
                      var grid = this.grid;
                      paginationOptionsBC.search = true;
                      paginationOptionsBC.searchColumn = { 
                        'ce.idclienteempresa' : grid.columns[1].filters[0].term,
                        'nombre_comercial' : grid.columns[2].filters[0].term,
                        'razon_social' : grid.columns[3].filters[0].term,
                        'ruc' : grid.columns[4].filters[0].term 
                      }
                      $scope.metodos.getPaginationServerSideBC();
                    });
                  }
                }; 
                paginationOptionsBC.sortName = $scope.fArr.gridOptionsBC.columnDefs[0].name;
                // $scope.metodos.cambioColumnas(); 
                $scope.metodos.getPaginationServerSideBC = function(loader) { 
                  if(loader){
                    blockUI.start('Procesando información...'); 
                  }
                  var arrParams = {
                    paginate : paginationOptionsBC,
                    datos: $scope.fBusqueda 
                  };
                  ClienteServices.sListarClientesBusqueda(arrParams).then(function (rpta) {
                    $scope.fArr.gridOptionsBC.totalItems = rpta.paginate.totalRows;
                    $scope.fArr.gridOptionsBC.data = rpta.datos;
                    if(loader){
                      blockUI.stop(); 
                    }
                  });
                }
                $scope.metodos.getPaginationServerSideBC(true); 
                $scope.cancel = function () {
                  $uibModalInstance.dismiss('cancel');
                }
                
              }
            });
          }
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...'); 
            $scope.fData.origen = 'contactos';
            ContactoEmpresaServices.sRegistrar($scope.fData).then(function (rpta) {
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
    editContactoModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'ContactoEmpresa/ver_popup_formulario',
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
          $scope.titleForm = 'Edición de Contacto';
          $scope.btnBusquedaPersonaJuridica = function() {
            blockUI.start('Procesando información...'); 
            $uibModal.open({ 
              templateUrl: angular.patchURLCI+'Cliente/ver_popup_busqueda_clientes',
              size: 'md',
              backdrop: 'static',
              keyboard:false,
              scope: $scope,
              controller: function ($scope, $uibModalInstance) { 
                blockUI.stop(); 
                $scope.fBusqueda = {};
                $scope.fBusqueda.origen = 'contacto';
                //if($scope.fData.tipo_documento_cliente.destino == 1){ // empresa
                $scope.fBusqueda.tipo_cliente = 'ce'; 
                //}
                $scope.titleForm = 'Búsqueda de Clientes'; 
                var paginationOptionsBC = {
                  pageNumber: 1,
                  firstRow: 0,
                  pageSize: 100,
                  sort: uiGridConstants.ASC,
                  sortName: null,
                  search: null
                };
                $scope.mySelectionGridBC = [];
                $scope.fArr.gridOptionsBC = { 
                  paginationPageSizes: [100, 500, 1000, 10000],
                  paginationPageSize: 100,
                  useExternalPagination: true,
                  useExternalSorting: true,
                  enableGridMenu: true,
                  enableRowSelection: true,
                  enableSelectAll: false,
                  enableFiltering: true,
                  enableFullRowSelection: true,
                  multiSelect: false,
                  columnDefs: [
                      { field: 'id', name: 'ce.idclienteempresa', displayName: 'ID', width: 50,  sort: { direction: uiGridConstants.ASC}, visible: false },
                      { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 200 },
                      { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 150 },
                      { field: 'num_documento', name: 'num_documento', displayName: 'N° Documento', width: 90 }
                  ],
                  onRegisterApi: function(gridApi) { // gridComboOptions
                    $scope.gridApi = gridApi;
                    gridApi.selection.on.rowSelectionChanged($scope,function(row){
                      $scope.mySelectionGridBC = gridApi.selection.getSelectedRows();
                      $scope.fData.cliente_empresa = {
                        id: $scope.mySelectionGridBC[0].id,
                        descripcion: $scope.mySelectionGridBC[0].nombre_comercial 
                      };
                      $uibModalInstance.dismiss('cancel');
                    });
                    $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
                      if (sortColumns.length == 0) {
                        paginationOptionsBC.sort = null;
                        paginationOptionsBC.sortName = null;
                      } else {
                        paginationOptionsBC.sort = sortColumns[0].sort.direction;
                        paginationOptionsBC.sortName = sortColumns[0].name;
                      }
                      $scope.metodos.getPaginationServerSideBC(true);
                    });
                    gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                      paginationOptionsBC.pageNumber = newPage;
                      paginationOptionsBC.pageSize = pageSize;
                      paginationOptionsBC.firstRow = (paginationOptionsBC.pageNumber - 1) * paginationOptionsBC.pageSize;
                      $scope.metodos.getPaginationServerSideBC(true);
                    });
                    $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
                      var grid = this.grid;
                      paginationOptionsBC.search = true;
                      paginationOptionsBC.searchColumn = { 
                        'ce.idclienteempresa' : grid.columns[1].filters[0].term,
                        'nombre_comercial' : grid.columns[2].filters[0].term,
                        'razon_social' : grid.columns[3].filters[0].term,
                        'ruc' : grid.columns[4].filters[0].term 
                      }
                      $scope.metodos.getPaginationServerSideBC();
                    });
                  }
                }; 
                paginationOptionsBC.sortName = $scope.fArr.gridOptionsBC.columnDefs[0].name;
                // $scope.metodos.cambioColumnas(); 
                $scope.metodos.getPaginationServerSideBC = function(loader) { 
                  if(loader){
                    blockUI.start('Procesando información...'); 
                  }
                  var arrParams = {
                    paginate : paginationOptionsBC,
                    datos: $scope.fBusqueda 
                  };
                  ClienteServices.sListarClientesBusqueda(arrParams).then(function (rpta) {
                    $scope.fArr.gridOptionsBC.totalItems = rpta.paginate.totalRows;
                    $scope.fArr.gridOptionsBC.data = rpta.datos;
                    if(loader){
                      blockUI.stop(); 
                    }
                  });
                }
                $scope.metodos.getPaginationServerSideBC(true); 
                $scope.cancel = function () {
                  $uibModalInstance.dismiss('cancel');
                }
                
              }
            });
          }
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          } 
          // $scope.getClienteEmpresaAutocomplete = function (value) { 
          //   console.log('aqui');
          //   var params = {
          //     searchText: value, 
          //     searchColumn: "nombre_comercial",
          //     sensor: false
          //   }
          //   return ClienteEmpresaServices.sListarClienteEmpresaAutoComplete(params).then(function(rpta) {
          //     // console.log('Datos: ',rpta);
          //     $scope.noResultsELE = false;
          //     if( rpta.flag === 0 ){
          //       $scope.noResultsELE = true;
          //     }
          //     return rpta.datos;
          //   });
          // } 
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            $scope.fData.origen = 'contactos';
            ContactoEmpresaServices.sEditar($scope.fData).then(function (rpta) {
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

