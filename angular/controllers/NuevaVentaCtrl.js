app.controller('NuevaVentaCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ClientePersonaFactory',
    'ClienteEmpresaFactory',
    'ServicioFactory',
    'ProductoFactory',
    'CaracteristicaFactory',
    'ContactoEmpresaFactory',
    'MathFactory',
		'VentaServices',
		'ClienteEmpresaServices',
		'ClientePersonaServices', 
		'ColaboradorServices',
    'TipoDocumentoClienteServices',
    'TipoDocumentoMovServices', 
    'ClienteServices', 
    'CategoriaClienteServices',
    'CategoriaElementoServices',
    'SedeServices',
    'SerieServices',
    'FormaPagoServices',
    'UnidadMedidaServices',
    'ElementoServices',
    'CaracteristicaServices',
    'ContactoEmpresaServices',
    'VariableCarServices',
    'NotaPedidoServices', 
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ClientePersonaFactory,
    ClienteEmpresaFactory,
    ServicioFactory,
    ProductoFactory,
    CaracteristicaFactory,
    ContactoEmpresaFactory,
    MathFactory,
		VentaServices,
		ClienteEmpresaServices,
		ClientePersonaServices,
		ColaboradorServices,
    TipoDocumentoClienteServices,
    TipoDocumentoMovServices,
    ClienteServices,
    CategoriaClienteServices,
    CategoriaElementoServices,
    SedeServices,
    SerieServices,
    FormaPagoServices,
    UnidadMedidaServices,
    ElementoServices,
    CaracteristicaServices,
    ContactoEmpresaServices,
    VariableCarServices,
    NotaPedidoServices 
) {
   
  $scope.metodos = {}; // contiene todas las funciones 
  $scope.fData = {}; // contiene todas las variables de formulario 
	$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  
  $scope.fData.classEditCliente = 'disabled';
  $scope.fData.fecha_registro = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  $scope.fData.fecha_emision = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  $scope.fData.num_serie_correlativo = '[ ............... ]'; 

  if(angular.isUndefined($scope.fConfigSys.precio_incluye_igv_ve) || $scope.fConfigSys.precio_incluye_igv_ve === null){ 
    //console.log('entre');
    $scope.$parent.getConfiguracionSys();
  }

  $timeout(function() { 
    $scope.fData.modo_igv = parseInt($scope.fConfigSys.precio_incluye_igv_ve); // INCLUYE IGV dinamico 
    $scope.fData.incluye_entr_dom = parseInt($scope.fConfigSys.incluye_entrega_dom_ve);  // dinamico 
  }, 500); 

  $scope.fData.plazo_entrega = 5;
  $scope.fData.validez_oferta = 10;
  $scope.fData.incluye_tras_prov = 2; // no 
  
  $scope.fData.idventaanterior = null;
  $scope.fData.isRegisterSuccess = false;
  $scope.fData.temporal = {};
  $scope.fData.temporal.cantidad = 1;
  $scope.fData.temporal.caracteristicas = null; 
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
    {'id' : 1, 'descripcion' : 'S/.', 'str_moneda' : 'S'},
    {'id' : 2, 'descripcion' : 'US$', 'str_moneda' : 'D'}
  ];
  $scope.fData.moneda = $scope.fArr.listaMoneda[0];

  // ESTADO DE VENTA 
  $scope.fArr.listaEstadosVenta = [
    {'id' : 1, 'descripcion' : 'REGISTRADO'},
    {'id' : 0, 'descripcion' : 'ANULADO'}
  ]; 
  $scope.fData.estado_venta = $scope.fArr.listaEstadosVenta[0];

  // TIPOS DE DOCUMENTO CLIENTE
  $scope.metodos.listaTiposDocumentoCliente = function(myCallback) { 
    var myCallback = myCallback || function() { };
    TipoDocumentoClienteServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaTiposDocumentoCliente = rpta.datos; 
        myCallback();
        console.log($scope.fData.tipo_documento_cliente,'fData.tipo_documento_cliente');
      } 
    });
  }
  var myCallback = function() { 
    $scope.fData.tipo_documento_cliente = $scope.fArr.listaTiposDocumentoCliente[0];
  }
  $scope.metodos.listaTiposDocumentoCliente(myCallback); 

  // TIPO DE DOCUMENTO MOV 
  $scope.metodos.listaTiposDocumentoMov = function(myCallback) { 
    var myCallback = myCallback || function() { };
    TipoDocumentoMovServices.sListarTipoDocParaVentaCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaTiposDocumentoMov = rpta.datos; 
        myCallback();
      } 
    });
  }
  var myCallback = function() { 
    $scope.fArr.listaTiposDocumentoMov.splice(0,0,{ id : '0', descripcion:'--Seleccione comprobante--'}); 
    $scope.fData.tipo_documento_mov = $scope.fArr.listaTiposDocumentoMov[0]; 
  }
  $scope.metodos.listaTiposDocumentoMov(myCallback); 

  // SEDE 
  $scope.metodos.listaSedes = function(myCallbackSede) { 
    var myCallbackSede = myCallbackSede || function() { };
    SedeServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaSedes = rpta.datos; 
        myCallbackSede();
      } 
    });
  }
  var myCallbackSede = function() { 
    $scope.fData.sede = $scope.fArr.listaSedes[0]; 
  }
  $scope.metodos.listaSedes(myCallbackSede); 

  // SERIE 
  $scope.metodos.listaSeries = function(myCallback) { 
    var myCallback = myCallback || function() { };
    SerieServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaSeries = rpta.datos; 
        myCallback();
      } 
    });
  }
  var myCallback = function() { 
    $scope.fData.serie = $scope.fArr.listaSeries[0]; 
    $scope.metodos.generarSerieCorrelativo();
  }
  $scope.metodos.listaSeries(myCallback); 

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
  var myCallback = function() { 
    $scope.fData.forma_pago = $scope.fArr.listaFormaPago[0]; 
  }
  $scope.metodos.listaFormaPago(myCallback); 

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

  var myCallback = function() { 
    $scope.fData.temporal.unidad_medida = $scope.fArr.listaUnidadMedida[0]; 
  }
  $scope.metodos.listaUnidadMedida(myCallback); 

  //WATCHERS 
  $scope.$watch('fData.num_documento', function(newValue,oldValue){ 
    if( oldValue == newValue ){
      return false; 
    }
    if( !(newValue) ){
      $scope.fData.cliente = {};
      $scope.fData.contacto = null;
      $scope.fData.classEditCliente = 'disabled';
    }
  }, true);

  // GENERACION DE SERIE + CORRELATIVO 
  $scope.metodos.generarSerieCorrelativo = function(loader) { 
    if(loader){ 
      blockUI.start('Generando numero de serie/correlativo...'); 
    }; 
    var arrParams = { 
      'serie': $scope.fData.serie,
      'tipo_documento_mov': $scope.fData.tipo_documento_mov 
    }; 
    VentaServices.sGenerarNumeroSerieCorrelativo(arrParams).then(function(rpta) { 
      $scope.fData.num_serie_correlativo = '[ ............... ]'; 
      if( rpta.flag == 1){ 
        $scope.fData.num_serie_correlativo = rpta.datos.num_serie_correlativo; 
        $scope.fData.num_serie = rpta.datos.num_serie; 
        $scope.fData.num_correlativo = rpta.datos.num_correlativo; 
      }else{
        pinesNotifications.notify({ title: 'Advertencia', text: rpta.message, type: 'warning', delay: 3000 });
      }
      if(loader){ 
        blockUI.stop(); 
      } 
    });
  }
  
  // OBTENER DATOS DE CLIENTE 
  $scope.obtenerDatosCliente = function() { 
    if( !($scope.fData.num_documento) ){
      $scope.btnBusquedaCliente();
      return; 
    }
    blockUI.start('Procesando información...'); 
    $scope.fData.cliente = {};
    var arrParams = {
      'tipo_documento': $scope.fData.tipo_documento_cliente, 
      'num_documento': $scope.fData.num_documento 
    }; 
    ClienteServices.sBuscarClientes(arrParams).then(function(rpta) { 
      if( rpta.flag == 1 ){
        $scope.fData.cliente = rpta.datos.cliente; 
        $scope.fData.classEditCliente = '';
        pinesNotifications.notify({ title: 'OK!', text: rpta.message, type: 'success', delay: 2500 });
      }else{
        $scope.fData.cliente = {}; 
        // ABRIMOS EL MODAL DE BUSQUEDA DE CLIENTE 
        pinesNotifications.notify({ title: 'Advertencia', text: rpta.message, type: 'warning', delay: 2500 });
        $scope.fData.classEditCliente = 'disabled';
        $scope.btnBusquedaCliente();
      }
      blockUI.stop(); 
    }); 
  }
  // BUSCAR CLIENTE 
  $scope.btnBusquedaCliente = function() { 
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
        if($scope.fData.tipo_documento_cliente.destino == 1){ // empresa
          $scope.fBusqueda.tipo_cliente = 'ce'; 
        }
        if($scope.fData.tipo_documento_cliente.destino == 2){ // persona 
          $scope.fBusqueda.tipo_cliente = 'cp'; 
        }
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
          //rowHeight: 36,
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
          columnDefs: [],
          onRegisterApi: function(gridApi) { // gridComboOptions
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope,function(row){
              $scope.mySelectionGridBC = gridApi.selection.getSelectedRows();
              $scope.fData.cliente = $scope.mySelectionGridBC[0]; //console.log($scope.fData.Proveedor);
              $scope.fData.num_documento = $scope.mySelectionGridBC[0].num_documento; 
              $scope.fData.classEditCliente = '';
              $uibModalInstance.dismiss('cancel');
              // $timeout(function() {
              //   $('#temporalElemento').focus(); //console.log('focus me',$('#temporalElemento'));
              // }, 1000);
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
              if( $scope.fBusqueda.tipo_cliente == 'ce' ){ //empresa 
                paginationOptionsBC.searchColumn = { 
                  'ce.idclienteempresa' : grid.columns[1].filters[0].term,
                  'nombre_comercial' : grid.columns[2].filters[0].term,
                  'razon_social' : grid.columns[3].filters[0].term,
                  'ruc' : grid.columns[4].filters[0].term,
                  'representante_legal' : grid.columns[5].filters[0].term,
                  'dni_representante_legal' : grid.columns[6].filters[0].term,
                  'cc.descripcion_cc' : grid.columns[7].filters[0].term
                }
              }
              if( $scope.fBusqueda.tipo_cliente == 'cp' ){ //persona
                  paginationOptionsBC.searchColumn = { 
                  'cp.idclientepersona' : grid.columns[1].filters[0].term,
                  "UPPER(CONCAT(cp.nombres,' ',cp.apellidos))" : grid.columns[2].filters[0].term,
                  'num_documento' : grid.columns[3].filters[0].term,
                  'email' : grid.columns[4].filters[0].term,
                  'telefono_movil' : grid.columns[5].filters[0].term,
                  'telefono_fijo' : grid.columns[6].filters[0].term,
                  'cc.descripcion_cc' : grid.columns[7].filters[0].term
                }
              }
              $scope.metodos.getPaginationServerSideBC();
            });
          }
        }; 
        $scope.metodos.cambioColumnas = function() { 
          if( $scope.fBusqueda.tipo_cliente == 'ce' ){ // EMPRESA 
            $scope.fArr.gridOptionsBC.columnDefs = [
              { field: 'id', name: 'ce.idclienteempresa', displayName: 'ID', width: 50,  sort: { direction: uiGridConstants.ASC}, visible: false },
              { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 200, visible: false },
              { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 150 },
              { field: 'num_documento', name: 'num_documento', displayName: 'N° Documento', width: 90 },
              { field: 'representante_legal', name: 'representante_legal', displayName: 'Representante Legal', minWidth: 150 },
              { field: 'dni_representante_legal', name: 'dni_representante_legal', displayName: 'DNI Rep. Legal', minWidth: 140, visible: false },
              { field: 'categoria_cliente', type: 'object', name: 'categoria_cliente', displayName: 'Categoria', minWidth: 100, visible: false, 
                  enableColumnMenus: false, enableColumnMenu: false, 
                  cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
                    '<label class="label bg-primary block">{{ COL_FIELD.descripcion }}</label></div>' 
              }
            ];
          }
          if( $scope.fBusqueda.tipo_cliente == 'cp' ){ // PERSONA  
            $scope.fArr.gridOptionsBC.columnDefs = [
              { field: 'id', name: 'cp.idclientepersona', displayName: 'ID', width: 50,  sort: { direction: uiGridConstants.ASC}, visible: false },
              { field: 'cliente', name: 'cliente', displayName: 'Cliente', minWidth: 160 },
              { field: 'num_documento', name: 'num_documento', displayName: 'N° Documento', width: 100 },
              { field: 'email', name: 'email', displayName: 'Email', minWidth: 120 },
              { field: 'telefono_movil', name: 'telefono_movil', displayName: 'Tel. Móvil', minWidth: 100 },
              { field: 'telefono_fijo', name: 'telefono_fijo', displayName: 'Tel. Fijo', minWidth: 90, visible: false },
              { field: 'categoria_cliente', type: 'object', name: 'categoria_cliente', displayName: 'Categoria', minWidth: 100, visible: false, 
                  enableColumnMenus: false, enableColumnMenu: false, 
                  cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
                    '<label class="label bg-primary block">{{ COL_FIELD.descripcion }}</label></div>' 
              }
            ];
          }
          paginationOptionsBC.sortName = $scope.fArr.gridOptionsBC.columnDefs[0].name;
        }
        $scope.metodos.cambioColumnas(); 
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
          $scope.mySelectionClienteGrid = [];  
          // cambiamos documento de cliente si se cambia el radio 
          var objIndex = $scope.fArr.listaTiposDocumentoCliente.filter(function(obj) { 
            return obj.destino_str == $scope.fBusqueda.tipo_cliente;
          }).shift(); 
          $scope.fData.tipo_documento_cliente = objIndex; 
        }
        $scope.metodos.getPaginationServerSideBC(true); 
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        }
        
      }
    });
  }
  // NUEVO CLIENTE 
  $scope.btnNuevoCliente = function() {
    if($scope.fData.tipo_documento_cliente.destino == 1){ // empresa 
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
      }
      ClienteEmpresaFactory.regClienteEmpresaModal(arrParams); 
    }
    if($scope.fData.tipo_documento_cliente.destino == 2){ // persona 
      var arrParams = { 
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
      }
      ClientePersonaFactory.regClientePersonaModal(arrParams); 
    }
  }
  // EDITAR CLIENTE 
  $scope.btnEditarCliente = function() {
    if($scope.fData.classEditCliente == 'disabled'){ 

      return; 
    };
    if($scope.fData.tipo_documento_cliente.destino == 1){ // empresa 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': [$scope.fData.cliente],
        'fArr': $scope.fArr,
        'fSessionCI': $scope.fSessionCI 
      }; 
      ClienteEmpresaFactory.editClienteEmpresaModal(arrParams); 
    }
    if($scope.fData.tipo_documento_cliente.destino == 2){ // persona 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': [$scope.fData.cliente],
        'fArr': $scope.fArr,
        'fSessionCI': $scope.fSessionCI 
      }; 
      ClientePersonaFactory.editClientePersonaModal(arrParams); 
    }
  }

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
    console.log(item, model, 'item, model');
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

  // NUEVO CONTACTO 
  $scope.btnNuevoContacto = function() { 
    var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
    }
    ContactoEmpresaFactory.regContactoModal(arrParams); 
  }

  $scope.getContactoAutocomplete = function (value) { 
    var params = {
      searchText: value, 
      searchColumn: "contacto",
      sensor: false,
      datos: $scope.fData
    }
    return ContactoEmpresaServices.sListarContactoAutoComplete(params).then(function(rpta) { 
      $scope.noResultsCT = false;
      if( rpta.flag === 0 ){
        $scope.noResultsCT = true;
      }
      return rpta.datos;
    });
  } 

  $scope.getSelectedContacto = function (item, model) { 
    $scope.fData.num_documento = model.ruc;
    $scope.fData.cliente.id = model.idclienteempresa;
    $scope.fData.cliente.razon_social = model.razon_social;
    $scope.fData.cliente.representante_legal = model.representante_legal;
    $scope.fData.cliente.dni_representante_legal = model.dni_representante_legal; 

    $scope.fData.cliente.telefono_contacto = model.telefono_fijo; 
    $scope.fData.cliente.anexo_contacto = model.anexo; 
  }

  $scope.validateContacto = function() { 
    if( angular.isObject( $scope.fData.contacto ) ){
      $scope.fData.classValid = ' input-success-border';
      $scope.noResultsCT = false;
    }else{
      if( $scope.fData.temporal.elemento ){
        $scope.fData.classValid = ' input-danger-border';
        $scope.noResultsCT = true;
      }else{
        $scope.fData.classValid = ' input-normal-border';
        $scope.noResultsCT = false;
      }      
    }
  }  

  // BUSCAR CONTACTOS 
  $scope.btnBusquedaContacto = function() { 
    blockUI.start('Procesando información...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'ContactoEmpresa/ver_popup_busqueda_contacto',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fBusqueda = {};

        $scope.titleForm = 'Búsqueda de Contactos'; 
        var paginationOptionsCO = {
          pageNumber: 1,
          firstRow: 0,
          pageSize: 100,
          sort: uiGridConstants.DESC,
          sortName: null,
          search: null
        };
        $scope.mySelectionGridCO = [];
        $scope.gridOptionsCO = {
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
            { field: 'id', name: 'co.idcontacto', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
            { field: 'nombres', name: 'nombres', displayName: 'Nombre', minWidth: 120 },
            { field: 'apellidos', name: 'apellidos', displayName: 'Apellidos', minWidth: 120 },
            { field: 'razon_social', name: 'razon_social', displayName: 'Empresa', minWidth: 140 },
            { field: 'ruc', name: 'ruc', displayName: 'RUC', minWidth: 80 } 
          ],
          onRegisterApi: function(gridApi) { // gridComboOptions
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope,function(row){
              $scope.mySelectionGridCO = gridApi.selection.getSelectedRows();
              $scope.fData.contacto = $scope.mySelectionGridCO[0];
              $scope.fData.cliente = $scope.mySelectionGridCO[0].cliente_empresa; 
              $scope.fData.num_documento = $scope.mySelectionGridCO[0].cliente_empresa.ruc; 
              $scope.fData.classEditCliente = '';
              $uibModalInstance.dismiss('cancel');
            });
            $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
              if (sortColumns.length == 0) {
                paginationOptionsCO.sort = null;
                paginationOptionsCO.sortName = null;
              } else {
                paginationOptionsCO.sort = sortColumns[0].sort.direction;
                paginationOptionsCO.sortName = sortColumns[0].name;
              }
              $scope.metodos.getPaginationServerSideCO(true);
            });
            gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
              paginationOptionsCO.pageNumber = newPage;
              paginationOptionsCO.pageSize = pageSize;
              paginationOptionsCO.firstRow = (paginationOptionsCO.pageNumber - 1) * paginationOptionsCO.pageSize;
              $scope.metodos.getPaginationServerSideCO(true);
            });
            $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
              var grid = this.grid;
              paginationOptionsCO.search = true;
              paginationOptionsCO.searchColumn = { 
                'co.idcontacto' : grid.columns[1].filters[0].term,
                'co.nombres' : grid.columns[2].filters[0].term,
                'co.apellidos' : grid.columns[3].filters[0].term,
                'ce.razon_social' : grid.columns[4].filters[0].term,
                'ce.ruc' : grid.columns[5].filters[0].term
              }; 
              $scope.metodos.getPaginationServerSideCO();
            });
          }
        }; 
        paginationOptionsCO.sortName = $scope.gridOptionsCO.columnDefs[0].name; 
        $scope.metodos.getPaginationServerSideCO = function(loader) { 
          if(loader){
            blockUI.start('Procesando información...'); 
          }
          var arrParams = {
            paginate : paginationOptionsCO,
            datos: $scope.fData 
          }; 
          ContactoEmpresaServices.sListarContactoBusqueda(arrParams).then(function (rpta) {
            $scope.gridOptionsCO.totalItems = rpta.paginate.totalRows;
            $scope.gridOptionsCO.data = rpta.datos;
            if(loader){
              blockUI.stop(); 
            }
          });
          $scope.mySelectionClienteGrid = [];  
        }
        $scope.metodos.getPaginationServerSideCO(true); 
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
          multiSelect: false,
          data: $scope.fData.temporal.caracteristicas || [],
          columnDefs: [ 
            { field: 'id', displayName: 'ID', width: '75', enableCellEdit: false, visible: false }, 
            { field: 'orden', displayName: 'ORDEN', width: '100', enableCellEdit: false, enableColumnMenus: false, enableColumnMenu: false, type:'number', 
              enableFiltering: false, enableSorting: true, sort: { direction: uiGridConstants.ASC } },
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
      { field: 'unidad_medida', displayName: 'U. MED.', width: 90, editableCellTemplate: 'ui-grid/dropdownEditor', 
        editDropdownValueLabel: 'descripcion', editDropdownOptionsArray: $scope.unidadMedidaOptions,cellFilter: 'griddropdown:this',cellClass:'ui-editCell text-center'},
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
        ]//,cellTemplate: '<div class="ui-grid-cell-contents text-center ">'+ '{{ COL_FIELD }}</div>' 
      },
      { field: 'accion', displayName: 'ACCIÓN', width: 110, enableCellEdit: false, enableSorting: false, 
        cellTemplate:'<div class="m-xxs text-center">'+ 
          '<button uib-tooltip="Clonar" tooltip-placement="left" type="button" class="btn btn-xs btn-gray mr-xs" ng-click="grid.appScope.btnClonarFila(row)"> <i class="fa fa-plus"></i> </button>' + 
          '<button uib-tooltip="Ver Características" tooltip-placement="left" type="button" class="btn btn-xs btn-info mr-xs" ng-click="grid.appScope.btnGestionCaracteristicasDetalle(row)"> <i class="fa fa-eye"></i> </button>' +
          '<button uib-tooltip="Quitar" tooltip-placement="left" type="button" class="btn btn-xs btn-danger" ng-click="grid.appScope.btnQuitarDeLaCesta(row)"> <i class="fa fa-trash"></i> </button>' + 
          '</div>' 
      } // uib-tooltip
    ]
    ,onRegisterApi: function(gridApi) { 
      $scope.gridApi = gridApi;
      gridApi.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
        rowEntity.column = colDef.field;
        console.log(oldValue,newValue,'oldValue,newValue');
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
      'id' : row.entity.id,
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
        unidad_medida : $scope.fData.temporal.unidad_medida,
        // unidad_medida : $scope.fArr.listaUnidadMedida[0],
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
    // var elementoNew = true;
    // angular.forEach($scope.gridOptions.data, function(value, key) { 
    //   if(value.id == $scope.fData.temporal.elemento.id ){ 
    //     elementoNew = false;
    //   }
    // });
    // if( elementoNew === false ){
    //   $scope.fData.temporal = {
    //     cantidad: 1,
    //     descuento: 0,
    //     importe_con_igv: null,
    //     importe_sin_igv: null,
    //     elemento: null,
    //     excluye_igv: 2,
    //     agrupacion: 0,
    //     unidad_medida : $scope.fArr.listaUnidadMedida[0],
    //     caracteristicas: null
    //   };
    //   $('#temporalElemento').focus();
    //   pinesNotifications.notify({ title: 'Advertencia.', text: 'El elemento ya ha sido agregado a la cesta.', type: 'warning', delay: 2000 });
    //   return false;
    // } 
    // empieza el juego... 
    $scope.arrTemporal = { 
      'idelemento' : $scope.fData.temporal.elemento.id,
      'descripcion' : $scope.fData.temporal.elemento.elemento,
      'cantidad' : $scope.fData.temporal.cantidad,
      'precio_unitario' : $scope.fData.temporal.precio_unitario,
      'importe_sin_igv' : $scope.fData.temporal.importe_sin_igv,
      'igv' : $scope.fData.temporal.igv,
      'importe_con_igv' : $scope.fData.temporal.importe_con_igv,
      'excluye_igv' : 2,
      'unidad_medida' : angular.copy($scope.fData.temporal.unidad_medida.id), 
      // 'unidad_medida' : angular.copy($scope.fData.temporal.unidad_medida), 
      'agrupacion': 0,
      'caracteristicas': angular.copy($scope.fData.temporal.caracteristicas)
    };
    if( $scope.gridOptions.data === null ){
      $scope.gridOptions.data = [];
    }
        console.log($scope.fArr.listaUnidadMedida[0],'$scope.fArr.listaUnidadMedida[0]');
    $scope.gridOptions.data.push($scope.arrTemporal);
    $scope.calcularTotales(); 
    $scope.fData.temporal = {
      cantidad: 1,
      importe_con_igv: null,
      elemento: null,
      excluye_igv: 2,
      agrupacion: 0,
      unidad_medida : $scope.fData.temporal.unidad_medida,
      // unidad_medida : $scope.fArr.listaUnidadMedida[0],
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
            // row.entity.caracteristicas = rpta.datos;
            callback();
            if(loader){
              blockUI.stop(); 
            }
          });
        } 
        //console.log(row.entity.caracteristicas);
        $scope.fArr.gridOptionsCRDet = { 
          useExternalPagination: false,
          useExternalSorting: false,
          enableGridMenu: false,
          enableRowSelection: true,
          enableSelectAll: false,
          enableFiltering: true,
          enableFullRowSelection: false,
          enableCellEditOnFocus: true,
          multiSelect: false,
          //data: row.entity.caracteristicas,
          columnDefs: [ 
            { field: 'id', displayName: 'ID', width: '75', enableCellEdit: false, visible: false },
            { field: 'orden', displayName: 'ORDEN', width: '100', enableCellEdit: false },
            { field: 'descripcion', displayName: 'Descripción', minWidth: 160, enableCellEdit: false }, 
            { field: 'valor', displayName: 'Valor', minWidth: 160, cellClass:'ui-editCell', enableCellEdit: true, sort: { direction: uiGridConstants.ASC }, 
              editableCellTemplate: '<input type="text" ui-grid-editor ng-model="MODEL_COL_FIELD" uib-typeahead="item.descripcion as item.descripcion for item in grid.appScope.getVariableAutocomplete($viewValue)" class="" >'
            } 
          ], 
          onRegisterApi: function(gridApi) { 
            $scope.gridApi = gridApi; 
            // gridApi.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
            //   $scope.fData.temporal.caracteristicas = [];
            //   var entraste = 'no';
            //   angular.forEach($scope.fArr.gridOptionsCR.data,function(row,key) { 
            //       $scope.fData.temporal.caracteristicas[key] = row; 
            //       entraste = 'si';
            //   });
            //   if( entraste === 'no' ){
            //     $scope.fData.temporal.caracteristicas = null;
            //   }
            //   //console.log($scope.fData.temporal.caracteristicas,'$scope.fData.temporal.caracteristicas'); 
            // });
          }
        }; 
        var myCallback = function() {
          $scope.fArr.gridOptionsCRDet.data = row.entity.caracteristicas;
        }
        if( !(row.entity.caracteristicas) ){
          $scope.metodos.getPaginationServerSideCR(true,myCallback); 
        }else{
          myCallback();
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
  $scope.btnMostrarNotaPedido = function() { 
    blockUI.start('Procesando información...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'NotaPedido/ver_popup_busqueda_nota_pedido', 
      size: 'lg',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fAdd = {};
        $scope.vista = 'detalle';
        $scope.titleForm = 'Búsqueda de Notas de Pedido'; 
        $scope.mySelectionGridNP = [];
        $scope.fBusquedaNP = {}; 
        $scope.fBusquedaNP.cliente = {};
        $scope.fBusquedaNP.cliente.id = null;
        $scope.fBusquedaNP.cliente.tipo_cliente = null;
        $scope.fBusquedaNP.cliente.descripcion = '-- Todos --'; 
        //console.log($scope.fData.cliente,'$scope.fData.cliente');
        if( $scope.fData.cliente.id ){
          $scope.fBusquedaNP.cliente.id = $scope.fData.cliente.id; 
          $scope.fBusquedaNP.cliente.tipo_cliente = $scope.fData.cliente.tipo_cliente; 
          $scope.fBusquedaNP.cliente.descripcion = $scope.fData.cliente.cliente; 
        }
        $scope.fBusquedaNP.desde = $filter('date')(new Date(),'01-MM-yyyy');
        $scope.fBusquedaNP.desdeHora = '00';
        $scope.fBusquedaNP.desdeMinuto = '00';
        $scope.fBusquedaNP.hastaHora = 23;
        $scope.fBusquedaNP.hastaMinuto = 59;
        $scope.fBusquedaNP.hasta = $filter('date')(new Date(),'dd-MM-yyyy');
          
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
        var myCallback = function() { 
          $scope.fArr.listaSedes.splice(0,0,{ id : 'ALL', descripcion:'--TODOS--'}); 
          $scope.fBusquedaNP.sede = $scope.fArr.listaSedes[0]; 
        }
        $scope.metodos.listaSedes(myCallback); 

        var paginationOptions = {
          pageNumber: 1,
          firstRow: 0,
          pageSize: 100,
          sort: uiGridConstants.DESC,
          sortName: null,
          search: null
        };
        $scope.gridOptionsNP = {
          rowHeight: 30,
          paginationPageSizes: [100, 500, 1000, 10000],
          paginationPageSize: 100,
          useExternalPagination: true,
          useExternalSorting: true,
          useExternalFiltering : true,
          enableGridMenu: true,
          enableRowSelection: true,
          enableSelectAll: true,
          enableFiltering: true,
          enableFullRowSelection: true,
          multiSelect: false,
          columnDefs: [ 
            { field: 'idmovimiento', name: 'np.idmovimiento', displayName: 'ID', width: '75', visible: false },
            { field: 'num_nota_pedido', name: 'np.num_nota_pedido', displayName: 'COD. NOTA PEDIDO', width: '120' },
            { field: 'fecha_emision', name: 'np.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false,  sort: { direction: uiGridConstants.DESC} },
            { field: 'fecha_registro', name: 'np.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
            { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
            { field: 'colaborador', name: 'colaborador', displayName: 'Colaborador', minWidth: 160, visible: false },
            { field: 'usuario', name: 'us.username', displayName: 'Usuario', minWidth: 160, visible: false },
            { field: 'forma_pago', name: 'fp.descripcion_fp', displayName: 'Forma de Pago', minWidth: 120 },
            { field: 'sede', name: 'se.descripcion_se', displayName: 'Sede', minWidth: 105 },
            { field: 'moneda', name: 'np.moneda', displayName: 'Moneda', minWidth: 76, enableFiltering: false },
            { field: 'subtotal', name: 'np.subtotal', displayName: 'Subtotal', minWidth: 90, visible: false },
            { field: 'igv', name: 'np.igv', displayName: 'IGV', minWidth: 80, visible: false },
            { field: 'total', name: 'np.total', displayName: 'Total', minWidth: 80 },
            { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: '95', enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
                cellTemplate:'<div class="">' + 
                  '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
                  '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
                  '</div>' 
            }
          ],
          onRegisterApi: function(gridApi) { 
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope,function(row){ 
              $scope.mySelectionGridNP = gridApi.selection.getSelectedRows(); 
              if( $scope.mySelectionGridNP.length > 0 ){
                var arrParams = { 
                  'identify' : $scope.mySelectionGridNP[0].idmovimiento 
                };
                NotaPedidoServices.sObtenerEstaNotaPedido(arrParams).then(function(rpta) { 
                  if( rpta.flag == 1 ){ // normal 
                    $timeout(function() { 
                      /*bindeo*/ 
                      $scope.gridOptions.data = rpta.detalle; 
                      $scope.fData.cliente = rpta.datos.cliente; 
                      $scope.fData.num_documento = rpta.datos.num_documento; 
                      $scope.fData.contacto = rpta.datos.contacto; 
                      $scope.fData.idcontacto = rpta.datos.idcontacto; 
                      $scope.fData.idnotapedido = rpta.datos.idnotapedido; 
                      // tipo documento cliente 
                      var myCallBackTD = function() { 
                        var objIndex = $scope.fArr.listaTiposDocumentoCliente.filter(function(obj) { 
                          //console.log(obj.id == $scope.fData.tipo_documento_cliente.id,'obj.id == $scope.fData.tipo_documento_cliente.id')
                          return obj.id == rpta.datos.tipo_documento_cliente.id; 
                        }).shift(); 
                        $scope.fData.tipo_documento_cliente = objIndex; 
                        //console.log(objIndex,'objIndex');
                      }
                      $scope.metodos.listaTiposDocumentoCliente(myCallBackTD); 
                      // console.log(objIndex,'objIndex',$scope.fData.tipo_documento_cliente.id,'$scope.fData.tipo_documento_cliente.id',$scope.fArr.listaTiposDocumentoCliente,'$scope.fArr.listaTiposDocumentoCliente');
                      
                      // forma de pago 
                      var myCallBackFP = function() { 
                        var objIndex = $scope.fArr.listaFormaPago.filter(function(obj) { 
                          return obj.id == rpta.datos.forma_pago.id;
                        }).shift(); 
                        $scope.fData.forma_pago = objIndex; 
                      }
                      $scope.metodos.listaFormaPago(myCallBackFP); 

                      // sede 
                      var myCallBackSE = function() { 
                        var objIndex = $scope.fArr.listaSedes.filter(function(obj) { 
                          return obj.id == rpta.datos.sede.id;
                        }).shift(); 
                        $scope.fData.sede = objIndex; 
                      }
                      $scope.metodos.listaSedes(myCallBackSE); 

                      // moneda 
                      var objIndex = $scope.fArr.listaMoneda.filter(function(obj) { 
                        return obj.id == rpta.datos.moneda.id;
                      }).shift(); 
                      $scope.fData.moneda = objIndex; 
                      /*end bindeo*/
                      $scope.calcularTotales();
                    }, 200);
                    pinesNotifications.notify({ title: 'OK!', text: 'Se cargó la nota de pedido.', type: 'success', delay: 3000 }); 
                    $uibModalInstance.dismiss('cancel');
                  }else if( rpta.flag == 2 ){ // facturado 
                    pinesNotifications.notify({ title: 'OK!', text: 'La Nota de Pedido ya ha sido facturada con anterioridad.', type: 'warning', delay: 3000 }); 
                    // return false;
                  }
                  blockUI.stop(); 
                }); 
              }else{
                $scope.metodos.listaSedes(myCallbackSede); 
              }
              
            });
            gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
              $scope.mySelectionGridNP = gridApi.selection.getSelectedRows();
            });
            $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) { 
              if (sortColumns.length == 0) {
                paginationOptions.sort = null;
                paginationOptions.sortName = null;
              } else {
                paginationOptions.sort = sortColumns[0].sort.direction;
                paginationOptions.sortName = sortColumns[0].name;
              }
              $scope.metodos.getPaginationServerSideNP(true);
            });
            gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
              paginationOptions.pageNumber = newPage;
              paginationOptions.pageSize = pageSize;
              paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
              $scope.metodos.getPaginationServerSideNP(true);
            });
            $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
              var grid = this.grid;
              paginationOptions.search = true; 
              paginationOptions.searchColumn = {
                'np.idmovimiento' : grid.columns[1].filters[0].term,
                'np.num_nota_pedido' : grid.columns[2].filters[0].term,
                "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[5].filters[0].term,
                "us.username" : grid.columns[6].filters[0].term, 
                'fp.descripcion_fp' : grid.columns[7].filters[0].term, 
                'se.descripcion_se' : grid.columns[8].filters[0].term,
                'np.moneda' : grid.columns[9].filters[0].term,
                'np.subtotal' : grid.columns[10].filters[0].term,
                'np.igv' : grid.columns[11].filters[0].term,
                'np.total' : grid.columns[12].filters[0].term
              }
              $scope.metodos.getPaginationServerSideNP();
            });
          }
        };
        paginationOptions.sortName = $scope.gridOptionsNP.columnDefs[2].name; 
        $scope.metodos.getPaginationServerSideNP = function(loader) { 
          if( loader ){
            blockUI.start('Procesando información...');
          }
          var arrParams = { 
            paginate : paginationOptions,
            datos: $scope.fBusquedaNP 
          };
          NotaPedidoServices.sListarHistorialNotaPedidos(arrParams).then(function (rpta) { 
            if( rpta.datos.length == 0 ){
              rpta.paginate = { totalRows: 0 };
            }
            $scope.gridOptionsNP.totalItems = rpta.paginate.totalRows;
            $scope.gridOptionsNP.data = rpta.datos; 
            if( loader ){
              blockUI.stop(); 
            }
          });
          $scope.mySelectionGrid = [];
        };
        $scope.metodos.getPaginationServerSideNP(true); 
        
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel'); 
          $scope.metodos.listaSedes(myCallbackSede); 
        } 
      }
    });
  }
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
    if( angular.isObject($scope.fData.temporal.elemento) ){ 
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
    $scope.metodos.generarSerieCorrelativo();
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
    VentaServices.sRegistrar($scope.fData).then(function (rpta) { 
      blockUI.stop();
      if(rpta.flag == 1){
        pTitle = 'OK!';
        pType = 'success'; 
        $scope.fData.isRegisterSuccess = true;
        $scope.fData.idventaanterior = rpta.idventa;
        console.log($scope.fData.idventaanterior,'$scope.fData.idventaanterior');
      }else if(rpta.flag == 0){
        var pTitle = 'Advertencia!';
        var pType = 'warning';
      }else{
        alert('Algo salió mal...');
      }
      pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
    });
  }
}]);

app.service("VentaServices",function($http, $q, handleBehavior) {
    return({
        sGenerarNumeroSerieCorrelativo: sGenerarNumeroSerieCorrelativo,
        sObtenerEstaVenta: sObtenerEstaVenta,
        sListarDetalleEstaVenta: sListarDetalleEstaVenta, 
        sListarHistorialVentas: sListarHistorialVentas,
        sListarHistorialDetalleVenta: sListarHistorialDetalleVenta,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sGenerarNumeroSerieCorrelativo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/generar_numero_serie_correlativo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sObtenerEstaVenta(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/obtener_esta_venta",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }    
    function sListarDetalleEstaVenta(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/listar_detalle_esta_venta",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarHistorialVentas(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/listar_ventas_historial",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarHistorialDetalleVenta(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/listar_detalle_ventas_historial",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Venta/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

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
    1: 'GRUPassO 1',
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

app.filter('griddropdown', function() {
  return function (input, context) {
    var map = context.col.colDef.editDropdownOptionsArray;
    //var idField = context.col.colDef.editDropdownIdLabel;
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