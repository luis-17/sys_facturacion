app.controller('EditarCotizacionCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$state', '$stateParams', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ClientePersonaFactory',
    'ClienteEmpresaFactory',
    'ServicioFactory',
    'ProductoFactory',
    'CaracteristicaFactory',
    'ContactoEmpresaFactory',
    'ModalReporteFactory',
    'MathFactory',
		'CotizacionServices',
		'ClienteEmpresaServices',
		'ClientePersonaServices', 
		'ColaboradorServices',
    'TipoDocumentoClienteServices',
    'ClienteServices', 
    'CategoriaClienteServices',
    'CategoriaElementoServices',
    'SedeServices',
    'FormaPagoServices',
    'UnidadMedidaServices',
    'ElementoServices',
    'CaracteristicaServices',
    'ContactoEmpresaServices',
    'VariableCarServices',
    'PlazoFormaPagoServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $state, $stateParams, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ClientePersonaFactory,
    ClienteEmpresaFactory,
    ServicioFactory,
    ProductoFactory,
    CaracteristicaFactory,
    ContactoEmpresaFactory,
    ModalReporteFactory,
    MathFactory,
		CotizacionServices,
		ClienteEmpresaServices,
		ClientePersonaServices,
		ColaboradorServices,
    TipoDocumentoClienteServices,
    ClienteServices,
    CategoriaClienteServices,
    CategoriaElementoServices,
    SedeServices,
    FormaPagoServices,
    UnidadMedidaServices,
    ElementoServices,
    CaracteristicaServices,
    ContactoEmpresaServices,
    VariableCarServices,
    PlazoFormaPagoServices 
) {
  
  $scope.metodos = {}; // contiene todas las funciones 
  $scope.fData = {}; // contiene todas las variables de formulario 
	$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  
  // console.log($stateParams,'$stateParams');

  

  // $scope.fData.fecha_registro = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  // $scope.fData.fecha_emision = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  // $scope.fData.num_cotizacion = '[ ............... ]';
  

  // $scope.fData.plazo_entrega = 5;
  // $scope.fData.validez_oferta = 10;
  // $scope.fData.incluye_tras_prov = 2; // no 
  // $timeout(function() { 
  //   console.log($scope.fConfigSys,'$scope.fConfigSys');
  //   $scope.fData.modo_igv = parseInt($scope.fConfigSys.precio_incluye_igv_cot); // INCLUYE IGV dinamico 
  //   $scope.fData.incluye_entr_dom = parseInt($scope.fConfigSys.incluye_entrega_dom_cot);  // dinamico 
  // }, 500); 

  $scope.fData.classEditCliente = 'disabled';
  $scope.fData.idcotizacionanterior = null;
  $scope.fData.isRegisterSuccess = false;
  $scope.fData.temporal = {};
  // $scope.fData.temporal.cantidad = 1;
  // $scope.fData.temporal.caracteristicas = null; 
  $scope.metodos.listaCategoriasCliente = function(myCallback) {
    var myCallback = myCallback || function() { };
    CategoriaClienteServices.sListarCbo().then(function(rpta) {
      $scope.fArr.listaCategoriaCliente = rpta.datos; 
      myCallback();
    });
  };
  $scope.metodos.listaColaboradores = function(myCallback) {
    var myCallback = myCallback || function() { };
    ColaboradorServices.sListarCbo().then(function(rpta) {
      $scope.fArr.listaColaboradores = rpta.datos; 
      myCallback();
    });
  };
  // sexos 
  $scope.fArr.listaSexo = [ 
    { id:'M', descripcion:'MASCULINO' },
    { id:'F', descripcion:'FEMENINO' }
  ]; 
  $scope.mySelectionGrid = [];
  $scope.fData.cliente = {};

  // TIPOS DE MONEDA 
  $scope.fArr.listaMoneda = [
    {'id' : 1, 'descripcion' : 'S/', 'str_moneda' : 'S'},
    {'id' : 2, 'descripcion' : 'US$', 'str_moneda' : 'D'}
  ];
  //$scope.fData.moneda = $scope.fArr.listaMoneda[0];

  // ESTADO DE COTIZACION 
  $scope.fArr.listaEstadosCotizacion = [
    {'id' : 1, 'descripcion' : 'POR ENVIAR'},
    {'id' : 2, 'descripcion' : 'ENVIADO'}
  ]; 
  // $scope.fData.estado_cotizacion = $scope.fArr.listaEstadosCotizacion[0];

  // TIPOS DE DOCUMENTO CLIENTE
  $scope.metodos.listaTiposDocumentoCliente = function(myCallback) { 
    var myCallback = myCallback || function() { };
    TipoDocumentoClienteServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaTiposDocumentoCliente = rpta.datos; 
        myCallback();
      } 
    });
  }
  // var myCallback = function() { 
  //   $scope.fData.tipo_documento_cliente = $scope.fArr.listaTiposDocumentoCliente[0];
  // }
  // $scope.metodos.listaTiposDocumentoCliente(myCallback); 

  // SEDE 
  $scope.metodos.listaSedes = function(myCallback) { 
    var myCallback = myCallback || function() { };
    SedeServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaSedes = rpta.datos; 
        myCallback();
      } 
    });
  }
  // var myCallback = function() { 
  //   $scope.fData.sede = $scope.fArr.listaSedes[0]; 
  //   $scope.metodos.generarNumeroCotizacion();
  // }
  // $scope.metodos.listaSedes(myCallback); 

  // FORMAS DE PAGO 
  $scope.metodos.listaFormaPago = function(myCallback) { 
    var myCallback = myCallback || function() { };
    FormaPagoServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaFormaPago = rpta.datos; 
        myCallback();
      } 
    });
  }
  // var myCallback = function() { 
  //   $scope.fData.forma_pago = $scope.fArr.listaFormaPago[0]; 
  // }
  // $scope.metodos.listaFormaPago(myCallback); 

  // TIPOS DE ELEMENTO 
  $scope.fArr.listaTipoElemento = [ 
    {'id' : 'P', 'descripcion' : 'PRODUCTO'},
    {'id' : 'S', 'descripcion' : 'SERVICIO'}
  ]; 
  // CATEGORIAS DE ELEMENTOS 
  $scope.metodos.listaCategoriasElemento = function(myCallback) {
    var myCallback = myCallback || function() { };
    CategoriaElementoServices.sListarCbo().then(function(rpta) {
      $scope.fArr.listaCategoriasElemento = rpta.datos; 
      myCallback();
    });
  };

  // UNIDADES DE MEDIDA 
  $scope.metodos.listaUnidadMedida = function(myCallback) { 
    var myCallback = myCallback || function() { };
    UnidadMedidaServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaUnidadMedida = rpta.datos; 
        myCallback();
      } 
    });
  }
        
  // OBTENER DATOS DE LA COTIZACION 
  $scope.obtenerDatosCotizacion = function() { 
    if( !($stateParams.identify) ){ 
      return; 
    } 
    blockUI.start('Procesando cotización...'); 
    var arrParams = {
      'identify': $stateParams.identify 
    }; 
    CotizacionServices.sObtenerEstaCotizacion(arrParams).then(function(rpta) { 
      if( rpta.flag == 1 ){
        $scope.fData = rpta.datos; 
        //$scope.gridOptions.data = rpta.datos.detalle;
        //console.log($scope.gridOptions.data,'$scope.gridOptions.data');
        // bindings 
        $timeout(function() {
          $scope.gridOptions.data = rpta.detalle;
        }, 200);

        // colaborador 
        var myCallbackCol = function() { 
          var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
            return obj.id == $scope.fData.colaborador.id; 
          }).shift(); 
          $scope.fData.colaborador = objIndex; 
        }
        $scope.metodos.listaColaboradores(myCallbackCol); 

        // tipo documento cliente 
        var myCallBackTD = function() { 
          var objIndex = $scope.fArr.listaTiposDocumentoCliente.filter(function(obj) { 
            return obj.id == $scope.fData.tipo_documento_cliente.id; 
          }).shift(); 
          $scope.fData.tipo_documento_cliente = objIndex; 
        }
        $scope.metodos.listaTiposDocumentoCliente(myCallBackTD); 

        // forma de pago 
        var myCallBackFP = function() { 
          var objIndex = $scope.fArr.listaFormaPago.filter(function(obj) { 
            return obj.id == $scope.fData.forma_pago.id;
          }).shift(); 
          $scope.fData.forma_pago = objIndex; 
        }
        $scope.metodos.listaFormaPago(myCallBackFP); 

        // sede 
        var myCallBackSE = function() { 
          var objIndex = $scope.fArr.listaSedes.filter(function(obj) { 
            return obj.id == $scope.fData.sede.id;
          }).shift(); 
          $scope.fData.sede = objIndex; 
        }
        $scope.metodos.listaSedes(myCallBackSE); 

        // moneda 
        var objIndex = $scope.fArr.listaMoneda.filter(function(obj) { 
          return obj.id == $scope.fData.moneda.id;
        }).shift(); 
        $scope.fData.moneda = objIndex; 

        // estado cotizacion
        var objIndex = $scope.fArr.listaEstadosCotizacion.filter(function(obj) { 
          return obj.id == $scope.fData.estado_cotizacion.id;
        }).shift(); 
        $scope.fData.estado_cotizacion = objIndex; 

        // unidad de medida 
        var myCallbackUM = function() { 
          $scope.fData.temporal.unidad_medida = $scope.fArr.listaUnidadMedida[0]; 
        }
        $scope.metodos.listaUnidadMedida(myCallbackUM); 
        //pinesNotifications.notify({ title: 'OK!', text: rpta.message, type: 'success', delay: 2500 });
      }else{
        // $scope.fData.cliente = {}; 
        // ABRIMOS EL MODAL DE BUSQUEDA DE CLIENTE 
        // pinesNotifications.notify({ title: 'Advertencia', text: rpta.message, type: 'warning', delay: 2500 });
        // $scope.fData.classEditCliente = 'disabled';
        // $scope.btnBusquedaCliente();
      }
      blockUI.stop(); 
    });
  }
  $timeout(function() {
    $scope.obtenerDatosCotizacion(); 
  }, 500);
  //WATCHERS 
  $scope.$watch('fData.num_documento', function(newValue,oldValue){ 
    if( oldValue == newValue ){
      return false; 
    }
    if( !(newValue) ){
      $scope.fData.cliente = {};
      $scope.fData.classEditCliente = 'disabled';
    }
  }, true);
  

  // NUEVO PRODUCTO 
  $scope.btnNuevoProducto = function() {
      var arrParams = { 
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
      }
      ProductoFactory.regProductoModal(arrParams); 
  }
  // NUEVO SERVICIO  
  $scope.btnNuevoServicio = function() {
      var arrParams = { 
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
      }
      ServicioFactory.regServicioModal(arrParams); 
  }
  $scope.getElementoAutocomplete = function (value) { 
    var params = {
      searchText: value, 
      searchColumn: "descripcion_ele",
      sensor: false
    }
    return ElementoServices.sListarElementosAutoComplete(params).then(function(rpta) {
      //console.log('Datos: ',rpta.datos);
      $scope.noResultsELE = false;
      // $scope.fData.classValid = ' input-success-border';
      if( rpta.flag === 0 ){
        $scope.noResultsELE = true;
        // $scope.fData.classValid = ' input-danger-border';
      }
      return rpta.datos;
    });
  } 
  $scope.getSelectedElemento = function (item, model) { 
    // console.log(item, model, 'item, model');
    $scope.fData.temporal.precio_unitario = model.precio_referencial;
    if( angular.isObject( $scope.fData.temporal.elemento ) ){
      $scope.fData.classValid = ' input-success-border';
    }else{
      $scope.fData.classValid = ' input-danger-border';
    }
    $timeout(function() {
      $scope.calcularImporte();
    },100);
  }
  $scope.validateElemento = function() { 
    if( angular.isObject( $scope.fData.temporal.elemento ) ){
      $scope.fData.classValid = ' input-success-border';
      $scope.noResultsELE = false;
    }else{
      if( $scope.fData.temporal.elemento ){
        $scope.fData.classValid = ' input-danger-border';
        $scope.noResultsELE = true;
      }else{
        $scope.fData.classValid = ' input-normal-border';
        $scope.noResultsELE = false;
      }
      
    }
  }
  // BUSCAR ELEMENTOS  
  $scope.btnBusquedaElemento = function() { 
    blockUI.start('Procesando información...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'Elemento/ver_popup_busqueda_elementos',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fBusqueda = {};
        if($scope.fData.tipo_documento_cliente.destino == 1){ // empresa
          $scope.fBusqueda.tipo_cliente = 'ce'; 
        }
        if($scope.fData.tipo_documento_cliente.destino == 2){ // persona 
          $scope.fBusqueda.tipo_cliente = 'cp'; 
        }
        $scope.titleForm = 'Búsqueda de Elementos'; 
        var paginationOptionsBELE = {
          pageNumber: 1,
          firstRow: 0,
          pageSize: 100,
          sort: uiGridConstants.ASC,
          sortName: null,
          search: null
        };
        $scope.mySelectionGridBELE = [];
        $scope.gridOptionsBELE = {
          //rowHeight: 36,
          paginationPageSizes: [100, 500, 1000, 10000],
          paginationPageSize: 100,
          useExternalPagination: true,
          useExternalSorting: true,
          enableGridMenu: true,
          enableSelectAll: false,
          enableFiltering: true,
          enableRowSelection: true,
          enableFullRowSelection: true,
          multiSelect: false,
          columnDefs: [],
          onRegisterApi: function(gridApi) { // gridComboOptions
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope,function(row){
              $scope.mySelectionGridBELE = gridApi.selection.getSelectedRows();
              $scope.fData.temporal.elemento = $scope.mySelectionGridBELE[0]; 
              $scope.fData.temporal.precio_unitario = $scope.mySelectionGridBELE[0].precio_referencial; 
              $timeout(function() {
                $scope.calcularImporte();
                $uibModalInstance.dismiss('cancel');
              },100);
              
              // $timeout(function() {
              //   $('#temporalElemento').focus(); //console.log('focus me',$('#temporalElemento'));
              // }, 1000);
            });
            $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
              if (sortColumns.length == 0) {
                paginationOptionsBELE.sort = null;
                paginationOptionsBELE.sortName = null;
              } else {
                paginationOptionsBELE.sort = sortColumns[0].sort.direction;
                paginationOptionsBELE.sortName = sortColumns[0].name;
              }
              $scope.metodos.getPaginationServerSideBELE(true);
            });
            gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
              paginationOptionsBELE.pageNumber = newPage;
              paginationOptionsBELE.pageSize = pageSize;
              paginationOptionsBELE.firstRow = (paginationOptionsBELE.pageNumber - 1) * paginationOptionsBELE.pageSize;
              $scope.metodos.getPaginationServerSideBELE(true);
            });
            $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
              var grid = this.grid;
              paginationOptionsBELE.search = true;
              paginationOptionsBELE.searchColumn = { 
                'el.idelemento' : grid.columns[1].filters[0].term,
                'el.descripcion_ele' : grid.columns[3].filters[0].term,
                'um.descripcion_um' : grid.columns[4].filters[0].term,
                'el.precio_referencial' : grid.columns[5].filters[0].term,
                'cael.descripcion_cael' : grid.columns[6].filters[0].term 
              }; 
              $scope.metodos.getPaginationServerSideBELE();
            });
          }
        }; 
        $scope.metodos.cambioColumnas = function() { 
          $scope.gridOptionsBELE.columnDefs = [ 
            { field: 'id', name: 'el.idelemento', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
            { field: 'tipo_elemento', type: 'object', name: 'el.tipo_elemento', displayName: 'TIPO', minWidth: 70,
              cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
                  '{{ COL_FIELD.descripcion }}</div>',
              enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false 
            },
            { field: 'descripcion_ele', name: 'el.descripcion_ele', displayName: 'Elemento', minWidth: 160 },
            { field: 'unidad_medida', type: 'object', name: 'um.descripcion_um', displayName: 'Unidad Medida Ref.', minWidth: 100, visible: false, 
              cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
                  '{{ COL_FIELD.descripcion }}</div>' 
            },
            { field: 'precio_referencial', name: 'el.precio_referencial', displayName: 'Precio Ref.', minWidth: 100, visible: false },
            { field: 'categoria_elemento', type: 'object', name: 'cael.descripcion_cael', displayName: 'Categoria', minWidth: 70, enableColumnMenus: false, enableColumnMenu: false, 
                cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
                  '<label class="label bg-primary block" style="background-color:{{COL_FIELD.color}}">{{ COL_FIELD.descripcion }}</label></div>' 
            }
          ]; 
          paginationOptionsBELE.sortName = $scope.gridOptionsBELE.columnDefs[0].name;
        }
        $scope.metodos.cambioColumnas(); 
        $scope.metodos.getPaginationServerSideBELE = function(loader) { 
          if(loader){
            blockUI.start('Procesando información...'); 
          }
          var arrParams = {
            paginate : paginationOptionsBELE 
          };
          ElementoServices.sListarElementosBusqueda(arrParams).then(function (rpta) {
            $scope.gridOptionsBELE.totalItems = rpta.paginate.totalRows;
            $scope.gridOptionsBELE.data = rpta.datos;
            if(loader){
              blockUI.stop(); 
            }
          });
          $scope.mySelectionClienteGrid = [];  
        }
        $scope.metodos.getPaginationServerSideBELE(true); 
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        }
        
      }
    });
  }
  // GESTIÓN DE CARACTERÍSTICAS 
  $scope.btnGestionCaracteristicas = function() {
    blockUI.start('Procesando información...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'Caracteristica/ver_popup_agregar_caracteristica',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fAdd = {};
        $scope.titleForm = 'Agregar Característica'; 
        $scope.vista = 'agregar';
        $scope.fArr.gridOptionsCR = { 
          useExternalPagination: false,
          useExternalSorting: false,
          enableGridMenu: false,
          enableRowSelection: true,
          enableSelectAll: false,
          enableFiltering: true,
          enableFullRowSelection: false,
          enableCellEditOnFocus: true,
          enableColumnMenus: false, 
          enableColumnMenu: false,
          multiSelect: false,
          data: $scope.fData.temporal.caracteristicas || [],
          columnDefs: [ 
            { field: 'idcaracteristica', enableSorting: false, displayName: 'ID', width: '75', enableCellEdit: false, visible: false }, 
            { field: 'orden', displayName: 'ORDEN', width: '100', enableCellEdit: false, enableColumnMenus: false, enableColumnMenu: false, 
              enableFiltering: false, enableSorting: true, sort: { direction: uiGridConstants.ASC }, type:'number' }, 
            { field: 'descripcion', enableSorting: true, displayName: 'Descripción', minWidth: 160, enableCellEdit: false }, 
            { field: 'valor', enableSorting: true, displayName: 'Valor', minWidth: 160, cellClass:'ui-editCell', enableCellEdit: true, 
              editableCellTemplate: '<input type="text" ui-grid-editor ng-model="MODEL_COL_FIELD" uib-typeahead="item.descripcion as item.descripcion for item in grid.appScope.getVariableAutocomplete($viewValue)" class="" >'
            } 
          ], 
          onRegisterApi: function(gridApi) { 
            $scope.gridApi = gridApi;
            gridApi.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
              $scope.fData.temporal.caracteristicas = [];
              var entraste = 'no';
              angular.forEach($scope.fArr.gridOptionsCR.data,function(row,key) { 
                  $scope.fData.temporal.caracteristicas[key] = row; 
                  entraste = 'si';
              });
              if( entraste === 'no' ){
                $scope.fData.temporal.caracteristicas = null;
              }
              //console.log($scope.fData.temporal.caracteristicas,'$scope.fData.temporal.caracteristicas'); 
            });
          }
        }; 
        $scope.getVariableAutocomplete = function(value) { 
          var params = { 
            searchText: value, 
            searchColumn: "descripcion_vcar",
            sensor: false 
          }; 
          return VariableCarServices.sListarVariableAutoComplete(params).then(function(rpta) { 
            $scope.noResultsCT = false;
            if( rpta.flag === 0 ){
              $scope.noResultsCT = true;
            }
            return rpta.datos;
          });
        }
        $scope.metodos.getPaginationServerSideCR = function(loader) { 
          if(loader){
            blockUI.start('Procesando información...'); 
          }
          CaracteristicaServices.sListarCaracteristicasAgregar().then(function (rpta) { 
            $scope.fArr.gridOptionsCR.data = rpta.datos;
            if(loader){
              blockUI.stop(); 
            }
          });
        }
        if( $scope.fData.temporal.caracteristicas === null ){
          $scope.metodos.getPaginationServerSideCR(true); 
        }
        
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        } 
      }
    });
  }
  $scope.btnNuevaCaracteristica = function() {
    var arrParams = { 
      'metodos': $scope.metodos,
      'fArr': $scope.fArr,
      callback: function() { 
        $scope.metodos.getPaginationServerSideCR();
      } 
    }; 
    CaracteristicaFactory.regCaracteristicaModal(arrParams); 
  }
  $scope.unidadMedidaOptions = [];
  UnidadMedidaServices.sListarCbo().then(function (rpta){
      console.log(rpta,'rpta')
      angular.forEach(rpta.datos, function (val,index) {
        $scope.arrTemporal = {
          'id': val.id,
          'descripcion': val.descripcion,
        }
      $scope.unidadMedidaOptions.push($scope.arrTemporal);
      });
  });
  // CESTA DE ELEMENTOS 
  $scope.mySelectionGrid = [];
  $scope.gridOptions = { 
    paginationPageSize: 50,
    enableRowSelection: true,
    enableSelectAll: false,
    enableFiltering: false,
    enableFullRowSelection: false,
    data: null,
    rowHeight: 26,
    enableCellEditOnFocus: true,
    multiSelect: false,
    columnDefs: [
      { field: 'idelemento', displayName: 'COD.', width: 50, enableCellEdit: false, enableSorting: false },
      { field: 'descripcion', displayName: 'DESCRIPCION', minWidth: 130, enableCellEdit: false, enableSorting: false,
        cellTemplate:'<div class="ui-grid-cell-contents "> <a class="text-info block" href="" ng-click="grid.appScope.btnGestionCaracteristicasDetalle(row)">'+ '{{ COL_FIELD }}</a></div>', 
        cellTooltip: function( row, col ) {
          return row.entity.descripcion;
        }
      },
      { field: 'cantidad', displayName: 'CANT.', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-center' },
      { field: 'unidad_medida', displayName: 'U. MED.', width: 90, editableCellTemplate: 'ui-grid/dropdownEditor', editDropdownValueLabel: 'descripcion',
        cellFilter: 'griddropdownedit:this',cellClass:'ui-editCell text-center',editDropdownOptionsArray: $scope.unidadMedidaOptions
        //, cellTemplate: '<div class="text-center ui-grid-cell-contents"> {{ COL_FIELD.descripcion }} </div>'
      },   
      // { field: 'unidad_medida', type:'object', displayName: 'U. MED.', width: 90, enableCellEdit: false, enableSorting: false, 
      //   cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ '{{ COL_FIELD.descripcion }}</div>' 
      // },
      { field: 'precio_unitario', displayName: 'P. UNIT', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-right' },
      { field: 'importe_sin_igv', displayName: 'IMPORTE SIN IGV', width: 120, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible: true },
      { field: 'igv', displayName: 'IGV', width: 80, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible:true },
      { field: 'importe_con_igv', displayName: 'IMPORTE', width: 120, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible:true },
      { field: 'excluye_igv', displayName: 'INAFECTO', width: 90, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell',
        editableCellTemplate: 'ui-grid/dropdownEditor',cellFilter: 'mapInafecto', editDropdownValueLabel: 'inafecto', editDropdownOptionsArray: [
          { id: 1, inafecto: 'SI' },
          { id: 2, inafecto: 'NO' }
        ],cellTemplate: '<div class="text-center ui-grid-cell-contents" ng-if="COL_FIELD == 1"> SI </div><div class="text-center" ng-if="COL_FIELD == 2"> NO </div>'
      },
      { field: 'agrupacion', displayName: 'AGRUPAR', width: 90, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-right', visible:true,
        editableCellTemplate: 'ui-grid/dropdownEditor',cellFilter: 'mapAgrupacion', editDropdownValueLabel: 'agrupacion', editDropdownOptionsArray: [
          { id: 0, agrupacion: 'SIN GRUPO' },
          { id: 1, agrupacion: 'GRUPO 1' },
          { id: 2, agrupacion: 'GRUPO 2' },
          { id: 3, agrupacion: 'GRUPO 3' },
          { id: 4, agrupacion: 'GRUPO 4' }
        ]//,cellTemplate: '<div class="ui-grid-cell-contents text-center ">'+ '{{ COL_FIELD }}</div>' agrupador_totalizado
      },
      { field: 'accion', displayName: 'ACCIÓN', width: 110, enableCellEdit: false, enableSorting: false, 
        cellTemplate:'<div class="m-xxs text-center">'+ 
          '<button uib-tooltip="Clonar" tooltip-placement="left" type="button" class="btn btn-xs btn-gray mr-xs" ng-click="grid.appScope.btnClonarFila(row)"> <i class="fa fa-plus"></i> </button>' + 
          '<button uib-tooltip="Ver Características" tooltip-placement="left" type="button" class="btn btn-xs btn-info mr-xs" ng-click="grid.appScope.btnGestionCaracteristicasDetalle(row)"> <i class="fa fa-eye"></i> </button>' +
          '<button uib-tooltip="Eliminar" tooltip-placement="left" type="button" class="btn btn-xs btn-danger" ng-click="grid.appScope.btnQuitarDeLaCesta(row)"> <i class="fa fa-trash"></i> </button>' + 
          '</div>' 
      } // uib-tooltip
    ]
    ,onRegisterApi: function(gridApi) { 
      $scope.gridApi = gridApi;
      gridApi.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
        rowEntity.column = colDef.field;
        console.log($scope.fConfigSys.num_decimal_precio_key,'$scope.fConfigSys.num_decimal_precio_key',rowEntity,'rowEntity');
        if(rowEntity.column == 'cantidad'){
          if( !(rowEntity.cantidad >= 1) ){
            var pTitle = 'Advertencia!';
            var pType = 'warning';
            rowEntity.cantidad = oldValue;
            pinesNotifications.notify({ title: pTitle, text: 'La cantidad debe ser mayor o igual a 1', type: pType, delay: 3500 });
            return false;
          }
        }
        if(rowEntity.column == 'precio_unitario'){
          if( !(rowEntity.precio_unitario >= 0) ){
            var pTitle = 'Advertencia!';
            var pType = 'warning';
            rowEntity.precio_unitario = oldValue;
            pinesNotifications.notify({ title: pTitle, text: 'El Precio debe ser mayor o igual a 0', type: pType, delay: 3500 });
            return false;
          }
        }
        if( $scope.fData.modo_igv == 2 ){ 
          console.log('Calculando modo NO INCLUYE IGV');
          rowEntity.importe_sin_igv = (parseFloat(rowEntity.precio_unitario) * parseFloat(rowEntity.cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
          if(rowEntity.excluye_igv == 1){
           rowEntity.igv = 0.00;
          }else{
           rowEntity.igv = (0.18 * rowEntity.importe_sin_igv).toFixed($scope.fConfigSys.num_decimal_precio_key);
          }
          rowEntity.importe_con_igv = (parseFloat(rowEntity.importe_sin_igv) + parseFloat(rowEntity.igv)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        }
        if( $scope.fData.modo_igv == 1 ){ 
          console.log('Calculando modo INCLUYE IGV');
          rowEntity.importe_con_igv = (parseFloat(rowEntity.precio_unitario) * parseFloat(rowEntity.cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
          if(rowEntity.excluye_igv == 1){
            rowEntity.importe_sin_igv = rowEntity.importe_con_igv;
            rowEntity.igv = 0.00;
          }else{
            rowEntity.importe_sin_igv = (rowEntity.importe_con_igv / 1.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
            rowEntity.igv = (0.18 * rowEntity.importe_sin_igv).toFixed($scope.fConfigSys.num_decimal_precio_key);
          }
        }
        $scope.calcularTotales();
        $scope.$apply();
      });
    }
  };
  $scope.getTableHeight = function() {
     var rowHeight = 26; // your row height 
     var headerHeight = 25; // your header height 
     return {
        // height: ($scope.gridOptions.data.length * rowHeight + headerHeight + 40) + "px"
        height: (4 * rowHeight + headerHeight + 20) + "px"
     };
  };
  $scope.btnClonarFila = function(row) { 
    console.log(row,'row');
    var arrFClon = { 
      'id' : row.entity.idelemento,
      'idelemento' : row.entity.idelemento,
      'elemento' : row.entity.elemento,
      'descripcion' : row.entity.descripcion,
      'cantidad' : row.entity.cantidad,
      'precio_unitario' : row.entity.precio_unitario,
      'importe_sin_igv' : row.entity.importe_sin_igv,
      'igv' : row.entity.igv,
      'importe_con_igv' : row.entity.importe_con_igv,
      'excluye_igv' : row.entity.excluye_igv,
      'unidad_medida' : angular.copy(row.entity.unidad_medida), 
      'agrupacion': row.entity.agrupacion,
      'caracteristicas': angular.copy(row.entity.caracteristicas)
    }; 
    $scope.gridOptions.data.push(arrFClon); 
  }
  $scope.agregarItem = function () {
    $('#temporalElemento').focus();
    if( !angular.isObject($scope.fData.temporal.elemento) ){ 
      $scope.fData.temporal = {
        cantidad: 1,
        descuento: 0,
        importe_con_igv: null,
        importe_sin_igv: null,
        elemento: null,
        excluye_igv: 2,
        agrupacion: 0,
        unidad_medida : $scope.fArr.listaUnidadMedida[0],
        caracteristicas: null
      };
      $('#temporalElemento').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'No se ha seleccionado el elemento', type: 'warning', delay: 2000 });
      return false;
    }
    if( !($scope.fData.temporal.precio_unitario >= 0) ){
      $scope.fData.temporal.precio_unitario = null;
      $('#temporalPrecioUnit').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'Ingrese un precio válido', type: 'warning', delay: 2000 });
      return false;
    }
    if( !($scope.fData.temporal.cantidad >= 1) ){
      $scope.fData.temporal.cantidad = null;
      $('#temporalCantidad').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'Ingrese una cantidad válida', type: 'warning', delay: 2000 });
      return false;
    }
    // empieza el juego... 
    $scope.arrTemporal = { 
      'id' : $scope.fData.temporal.elemento.id,
      'descripcion' : $scope.fData.temporal.elemento.elemento,
      'cantidad' : $scope.fData.temporal.cantidad,
      'precio_unitario' : $scope.fData.temporal.precio_unitario,
      'importe_sin_igv' : $scope.fData.temporal.importe_sin_igv,
      'igv' : $scope.fData.temporal.igv,
      'importe_con_igv' : $scope.fData.temporal.importe_con_igv,
      'excluye_igv' : 2,
      'unidad_medida' : angular.copy($scope.fData.temporal.unidad_medida), 
      'agrupacion': 0,
      'caracteristicas': angular.copy($scope.fData.temporal.caracteristicas)
    };
    console.log($scope.gridOptions,'$scope.gridOptions xd');
    if( $scope.gridOptions.data === null ){
      $scope.gridOptions.data = [];
    }
    $scope.gridOptions.data.push($scope.arrTemporal);
    $scope.calcularTotales(); 
    $scope.fData.temporal = {
      cantidad: 1,
      importe_con_igv: null,
      elemento: null,
      excluye_igv: 2,
      agrupacion: 0,
      unidad_medida : $scope.fArr.listaUnidadMedida[0],
      caracteristicas: null 
    };
    $scope.fData.classValid = ' input-normal-border'; 
    // $scope.fData.temporal.caracteristicas = null;
    // console.log($scope.fData.classValid,'$scope.fData.classValid');
  }
  $scope.btnGestionCaracteristicasDetalle = function(row) { 
    // console.log(row,'row');
    blockUI.start('Procesando información...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'Caracteristica/ver_popup_agregar_caracteristica',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fAdd = {};
        $scope.vista = 'detalle';
        $scope.titleForm = 'Característica del Elemento';

        $scope.getVariableAutocomplete = function(value) { 
          var params = { 
            searchText: value, 
            searchColumn: "descripcion_vcar",
            sensor: false 
          }; 
          return VariableCarServices.sListarVariableAutoComplete(params).then(function(rpta) { 
            $scope.noResultsCT = false;
            if( rpta.flag === 0 ){
              $scope.noResultsCT = true;
            }
            return rpta.datos;
          });
        }
        $scope.metodos.getPaginationServerSideCR = function(loader,callback) { 
          if(loader){
            blockUI.start('Procesando información...'); 
          }
          CaracteristicaServices.sListarCaracteristicasAgregar().then(function (rpta) { 
            row.entity.caracteristicas = rpta.datos; 
            callback();
            if(loader){
              blockUI.stop(); 
            }
          });
        }; 
        console.log('init mee');
        $scope.fArr.gridOptionsCRDet = { 
          useExternalPagination: false,
          useExternalSorting: false,
          enableGridMenu: false,
          enableRowSelection: true,
          enableSelectAll: false,
          enableFiltering: true,
          enableFullRowSelection: false,
          enableCellEditOnFocus: true,
          enableColumnMenus: false, 
          enableColumnMenu: false,
          multiSelect: false,
          columnDefs: [ 
            { field: 'idcaracteristica', enableSorting: false, displayName: 'ID', width: '75', enableCellEdit: false, visible: false }, 
            { field: 'orden', displayName: 'ORDEN', width: '100', enableCellEdit: false, enableColumnMenus: false, enableColumnMenu: false, 
              enableFiltering: false, enableSorting: true, sort: { direction: uiGridConstants.ASC }, type:'number' }, 
            { field: 'descripcion', enableSorting: true, displayName: 'Descripción', minWidth: 160, enableCellEdit: false }, 
            { field: 'valor', enableSorting: true, displayName: 'Valor', minWidth: 160, cellClass:'ui-editCell', enableCellEdit: true, 
              editableCellTemplate: '<input type="text" ui-grid-editor ng-model="MODEL_COL_FIELD" uib-typeahead="item.descripcion as item.descripcion for item in grid.appScope.getVariableAutocomplete($viewValue)" class="" >'
            }
          ], 
          onRegisterApi: function(gridApi) { 
            $scope.gridApi = gridApi; 
          }
        }; 
        var myCallback = function() {
          $scope.fArr.gridOptionsCRDet.data = row.entity.caracteristicas;
        }
        //console.log(row.entity.caracteristicas.length,'row.entity.caracteristicas.length');
        if( !(row.entity.caracteristicas) || row.entity.caracteristicas.length == 0 ){ 
          $scope.metodos.getPaginationServerSideCR(true,myCallback); 
          //console.log(111,row.entity.caracteristicas,'row.entity.caracteristicas');
        }else{
          myCallback();
          //console.log(222);
        }
        //var rowCaracteristicas = row.caracteristicas; 
        
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        } 
      }
    });
  }
  $scope.btnQuitarDeLaCesta = function (row) { 
    var index = $scope.gridOptions.data.indexOf(row.entity); 
    $scope.gridOptions.data.splice(index,1);
    $scope.calcularTotales(); 
  }
  $scope.cambiarModo = function(){ // 
    if( $scope.fData.modo_igv == 2){
      console.log('Calculando modo NO INCLUYE IGV');
      angular.forEach($scope.gridOptions.data,function (value, key) { 
        $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].precio_unitario) * parseFloat($scope.gridOptions.data[key].cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        if( $scope.gridOptions.data[key].excluye_igv == 1 ){
          $scope.gridOptions.data[key].igv = 0.00;
        }else{
          $scope.gridOptions.data[key].igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv)*0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        }
        $scope.gridOptions.data[key].importe_con_igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv) + parseFloat($scope.gridOptions.data[key].igv)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        
      });
    }
    if( $scope.fData.modo_igv == 1 ){ 
      console.log('Calculando modo INCLUYE IGV');
      angular.forEach($scope.gridOptions.data,function (value, key) {
        $scope.gridOptions.data[key].importe_con_igv = (parseFloat($scope.gridOptions.data[key].precio_unitario) * parseFloat($scope.gridOptions.data[key].cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        if( $scope.gridOptions.data[key].excluye_igv == 1 ){
          $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].importe_con_igv)).toFixed($scope.fConfigSys.num_decimal_precio_key);
          $scope.gridOptions.data[key].igv = 0.00;
        } else{
          $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].importe_con_igv) / 1.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
          $scope.gridOptions.data[key].igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv)*0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        } 
      });
    }
    $scope.calcularTotales();
  };
  $scope.calcularTotales = function () { 
    var subtotal = 0;
    var igv = 0;
    var total = 0;
    angular.forEach($scope.gridOptions.data,function (value, key) { 
      total += parseFloat($scope.gridOptions.data[key].importe_con_igv);
      igv += parseFloat($scope.gridOptions.data[key].igv);
      subtotal += parseFloat($scope.gridOptions.data[key].importe_sin_igv);
    });
    //$scope.fData.subtotal_temp = (total / 1.18);
    $scope.fData.subtotal = MathFactory.redondear(subtotal).toFixed($scope.fConfigSys.num_decimal_total_key);
    $scope.fData.igv = MathFactory.redondear(igv).toFixed($scope.fConfigSys.num_decimal_total_key);
    $scope.fData.total = MathFactory.redondear(total).toFixed($scope.fConfigSys.num_decimal_total_key);
  }
  $scope.calcularImporte = function (){ 
    if( !$scope.fData.temporal.precio_unitario ){
      return false; 
    }
    if(angular.isObject($scope.fData.temporal.elemento) ){ 
      if( $scope.fData.modo_igv == 2 ){ 
        console.log('Calculando modo NO INCLUYE IGV');
        $scope.fData.temporal.importe_sin_igv = (parseFloat($scope.fData.temporal.precio_unitario) * parseFloat($scope.fData.temporal.cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.igv = ($scope.fData.temporal.importe_sin_igv * 0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.importe_con_igv = (parseFloat($scope.fData.temporal.importe_sin_igv) + parseFloat($scope.fData.temporal.igv)).toFixed($scope.fConfigSys.num_decimal_precio_key);
      }
      if( $scope.fData.modo_igv == 1 ){ 
        console.log('Calculando modo INCLUYE IGV');
        $scope.fData.temporal.importe_con_igv = (parseFloat($scope.fData.temporal.precio_unitario) * parseFloat($scope.fData.temporal.cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.importe_sin_igv = ($scope.fData.temporal.importe_con_igv / 1.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.igv =($scope.fData.temporal.importe_sin_igv * 0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
      }
    }else{
      $scope.fData.temporal.importe_sin_igv = null;
      $scope.fData.temporal.importe_con_igv = null;
      $scope.fData.temporal.elemento = null;
    }
  } 
  $scope.mismoCliente = function() { 
    $scope.fData.temporal = {
      cantidad: 1,
      descuento: 0,
      importe_con_igv: null,
      importe_sin_igv: null,
      elemento: null,
      excluye_igv: 2,
      agrupacion: 0,
      unidad_medida : $scope.fArr.listaUnidadMedida[0],
      caracteristicas: null
    };
    // console.log('mismo cliente');
    $scope.gridOptions.data = [];
    $scope.fData.subtotal = null;
    $scope.fData.igv = null;
    $scope.fData.total = null;
    $scope.fData.isRegisterSuccess = false;
    $scope.metodos.generarNumeroCotizacion();
    $('#temporalElemento').focus();
  }
  $scope.grabar = function() { 
    if($scope.fData.isRegisterSuccess){
      pinesNotifications.notify({ title: 'Advertencia.', text: 'La cotización ya fue registrada', type: 'warning', delay: 3000 });
      return false;
    }
    if( $scope.fData.tipo_documento_cliente.destino == 1 ){ // empresa 
      if( $scope.fData.cliente.razon_social == '' || $scope.fData.cliente.razon_social == null || $scope.fData.cliente.razon_social == undefined ){
        $scope.fData.num_documento = null;
        $('#numDocumento').focus();
        pinesNotifications.notify({ title: 'Advertencia.', text: 'No ha ingresado un cliente', type: 'warning', delay: 3000 });
        return false;
      }
    }
    if( $scope.fData.tipo_documento_cliente.destino == 2 ){ // persona 
      if( $scope.fData.cliente.cliente == '' || $scope.fData.cliente.cliente == null || $scope.fData.cliente.cliente == undefined ){
        $scope.fData.num_documento = null;
        $('#numDocumento').focus();
        pinesNotifications.notify({ title: 'Advertencia.', text: 'No ha ingresado un cliente', type: 'warning', delay: 3000 });
        return false;
      }
    }
    $scope.fData.detalle = angular.copy($scope.gridOptions.data);
    if( $scope.fData.detalle.length < 1 ){ 
      $('#temporalElemento').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'No se ha agregado ningún elemento', type: 'warning', delay: 3000 }); 
      return false; 
    }
    blockUI.start('Ejecutando proceso...');
    CotizacionServices.sEditar($scope.fData).then(function (rpta) { 
      blockUI.stop();
      if(rpta.flag == 1){
        pTitle = 'OK!';
        pType = 'success'; 
        // $scope.fData.isRegisterSuccess = true;
        // $scope.fData.idcotizacionanterior = rpta.idcotizacion;
      }else if(rpta.flag == 0){
        var pTitle = 'Advertencia!';
        var pType = 'warning';
      }else{
        alert('Algo salió mal...');
      }
      pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
    });
  }
  $scope.imprimir = function() { 
    var arrParams = { 
      titulo: 'VISTA PREVIA DE COTIZACIÓN',
      datos:{
        id: $scope.fData.idcotizacion,
        codigo_reporte: 'COT-FCOT'
      },
      envio_correo: 'si',
      salida: 'pdf',
      url: angular.patchURLCI + "Cotizacion/imprimir_cotizacion" 
    }
    ModalReporteFactory.getPopupReporte(arrParams);
  }
  $scope.metodos.verPlazosPago = function() {
    //console.log($scope.fData.total,'$scope.fData.total');
      // console.log($scope.fData.fecha_emision,'$scope.fData.fecha_emision');
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'PlazoFormaPago/ver_popup_plazo_pago',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        scope: $scope,
        controller: function ($scope, $uibModalInstance) { 
          blockUI.stop();      
          $scope.fPlazo = {};
          $scope.fArr = {}; 
          $scope.metodos = {};
          $scope.titleForm = 'Plazos';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          } 
          $scope.metodos.getPaginationServerSidePlazo = function(loader) {

            if( loader ){
              blockUI.start('Procesando información...');
            }
            var arrParams = {       
              datos: $scope.fData.forma_pago.id,
              fechaemision:$scope.fData.fecha_emision,
              monto:$scope.fData.total
            };
            console.log(arrParams,'arrParams');
            PlazoFormaPagoServices.sListarPlazoFormaPagoDetalle(arrParams).then(function (rpta) {         
               $scope.fPlazo.plazolista=rpta.datos;             
              if( loader ){
                blockUI.stop(); 
              }
            });
          };
          $scope.metodos.getPaginationServerSidePlazo(true); 
        }
      });  
  }
}]);

app.filter('mapInafecto', function() { 
  var inafectoHash = { 
    1: 'SI',
    2: 'NO'
  };
  return function(input) {
    if (!input){
      return '';
    } else {
      return inafectoHash[input];
    }
  };
});
app.filter('mapAgrupacion', function() {
  var agrupacionHash = { 
    0: 'SIN GRUPO',
    1: 'GRUPO 1',
    2: 'GRUPO 2',
    3: 'GRUPO 3',
    4: 'GRUPO 4'
  };
  return function(input) {
    if (!input){
      return '';
    } else {
      return agrupacionHash[input];
    }
  };
});
app.filter('griddropdownedit', function() {
  return function (input, context) { 
    var map = context.col.colDef.editDropdownOptionsArray;
    var idField = context.col.colDef.editDropdownIdLabel;
    var valueField = context.col.colDef.editDropdownValueLabel;
    var initial = context.row.entity[context.col.field]; 
    if (typeof map !== "undefined") {
      for (var i = 0; i < map.length; i++) {
        if (map[i][valueField] == input.descripcion) { 
          return map[i][valueField];
        }
      }
    } else if (initial) {
      return initial;
    }
    var objIndex = map.filter(function(obj) { 
      return obj.id == input; 
    }).shift(); 
    if (typeof objIndex === "undefined") { 
      return null;
    }else{
      return objIndex[valueField]; 
    }
  };
});