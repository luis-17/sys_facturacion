app.controller('NuevaCotizacionCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ClientePersonaFactory',
    'ClienteEmpresaFactory',
    'ServicioFactory',
    'ProductoFactory',
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
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ClientePersonaFactory,
    ClienteEmpresaFactory,
    ServicioFactory,
    ProductoFactory,
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
    ElementoServices
) {
   
  $scope.metodos = {}; // contiene todas las funciones 
  $scope.fData = {}; // contiene todas las variables de formulario 
	$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  
  $scope.fData.classEditCliente = 'disabled';
  $scope.fData.fecha_registro = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  $scope.fData.fecha_emision = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  $scope.fData.num_cotizacion = '[ ............... ]';
  $scope.fData.modo_igv = 1; // INCLUYE IGV 
  $scope.fData.plazo_entrega = 5;
  $scope.fData.validez_oferta = 10;
  $scope.fData.temporal = {};
  $scope.fData.temporal.cantidad = 1;
  $scope.fData.temporal.caracteristicas = []; 
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
    {'id' : 1, 'descripcion' : 'S/.'},
    {'id' : 2, 'descripcion' : 'US$'}
  ];
  $scope.fData.moneda = $scope.fArr.listaMoneda[0];

  // ESTADO DE COTIZACION 
  $scope.fArr.listaEstadosCotizacion = [
    {'id' : 1, 'descripcion' : 'POR ENVIAR'},
    {'id' : 2, 'descripcion' : 'ENVIADO'}
  ]; 
  $scope.fData.estado_cotizacion = $scope.fArr.listaEstadosCotizacion[0];

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
  var myCallback = function() { 
    $scope.fData.tipo_documento_cliente = $scope.fArr.listaTiposDocumentoCliente[0];
  }
  $scope.metodos.listaTiposDocumentoCliente(myCallback); 

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
    $scope.fData.sede = $scope.fArr.listaSedes[0]; 
    $scope.metodos.generarNumeroCotizacion();
  }
  $scope.metodos.listaSedes(myCallback); 

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

  // GENERACION DE NUMERO DE COTIZACION 
  $scope.metodos.generarNumeroCotizacion = function(loader) { 
    if(loader){
      blockUI.start('Generando numero de cotización...'); 
    }
    var arrParams = {
      'sede': $scope.fData.sede 
    }; 
    CotizacionServices.sGenerarNumeroCotizacion(arrParams).then(function(rpta) { 
      $scope.fData.num_cotizacion = '[ ............... ]';
      if( rpta.flag == 1){ 
        $scope.fData.num_cotizacion = rpta.datos.num_cotizacion; 
      }
      if(loader){
        blockUI.stop(); 
      }
    });
  }
  
  // OBTENER DATOS DE CLIENTE 
  $scope.obtenerDatosCliente = function() { 
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
        $scope.gridOptionsBC = {
          //rowHeight: 36,
          paginationPageSizes: [100, 500, 1000, 10000],
          paginationPageSize: 100,
          useExternalPagination: true,
          useExternalSorting: true,
          enableGridMenu: true,
          enableRowSelection: false,
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
            $scope.gridOptionsBC.columnDefs = [
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
            $scope.gridOptionsBC.columnDefs = [
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
          paginationOptionsBC.sortName = $scope.gridOptionsBC.columnDefs[0].name;
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
            $scope.gridOptionsBC.totalItems = rpta.paginate.totalRows;
            $scope.gridOptionsBC.data = rpta.datos;
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
    // console.log(item, model, 'item, model')
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
          enableRowSelection: false,
          enableSelectAll: false,
          enableFiltering: true,
          enableFullRowSelection: true,
          multiSelect: false,
          columnDefs: [],
          onRegisterApi: function(gridApi) { // gridComboOptions
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope,function(row){
              $scope.mySelectionGridBELE = gridApi.selection.getSelectedRows();
              $scope.fData.temporal.elemento = $scope.mySelectionGridBELE[0]; 
              $uibModalInstance.dismiss('cancel');
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
  // CESTA DE ELEMENTOS 
  $scope.mySelectionGrid = [];
  $scope.gridOptions = { 
    paginationPageSize: 50,
    enableRowSelection: false,
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
        cellTooltip: function( row, col ) {
          return row.entity.descripcion;
        }
      },
      { field: 'unidad_medida', type:'object', displayName: 'U. MED.', width: 90, enableCellEdit: false, enableSorting: false, 
        cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ '{{ COL_FIELD.descripcion }}</div>' 
      },
      { field: 'cantidad', displayName: 'CANT.', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-center' },
      { field: 'precio_unitario', displayName: 'P. UNIT', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-right' },
      { field: 'importe_sin_igv', displayName: 'IMPORTE SIN IGV', width: 120, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible: true },
      { field: 'igv', displayName: 'IGV', width: 100, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible:true },
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
      { field: 'accion', displayName: 'ACCIÓN', width: 60, enableCellEdit: false, enableSorting: false, 
        cellTemplate:'<div class="m-xxs text-center">'+ 
          '<button type="button" class="btn btn-xs btn-danger" ng-click="grid.appScope.btnQuitarDeLaCesta(row)"> <i class="fa fa-trash"></i> </button> </div>' 
      }
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
        // rowEntity.valor = parseFloat(rowEntity.precio_unitario) * parseFloat(rowEntity.cantidad);
        //rowEntity.descuento_valor = rowEntity.valor * rowEntity.descuento / 100;
        if( $scope.fData.modo_igv == 2 ){ 
          console.log('Calculando modo NO INCLUYE IGV');
          rowEntity.importe_sin_igv = (parseFloat(rowEntity.precio_unitario) * parseFloat(rowEntity.cantidad)).toFixed(2);
          if(rowEntity.excluye_igv == 1){
           rowEntity.igv = 0.00;
          }else{
           rowEntity.igv = (0.18 * rowEntity.importe_sin_igv).toFixed(2);
          }
          rowEntity.importe_con_igv = (parseFloat(rowEntity.importe_sin_igv) + parseFloat(rowEntity.igv)).toFixed(2);
        }
        if( $scope.fData.modo_igv == 1 ){ 
          console.log('Calculando modo INCLUYE IGV');
          rowEntity.importe_con_igv = (parseFloat(rowEntity.precio_unitario) * parseFloat(rowEntity.cantidad)).toFixed(2);
          if(rowEntity.excluye_igv == 1){
            rowEntity.importe_sin_igv = rowEntity.importe_con_igv;
            rowEntity.igv = 0.00;
          }else{
            rowEntity.importe_sin_igv = (rowEntity.importe_con_igv / 1.18).toFixed(2);
            rowEntity.igv = (0.18 * rowEntity.importe_sin_igv).toFixed(2);
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
        height: (6 * rowHeight + headerHeight + 20) + "px"
     };
  };
  $scope.agregarItem = function () {
    $('#temporalElemento').focus();
    if( !angular.isObject($scope.fData.temporal.elemento) ){ 
      $scope.fData.temporal.idmedicamento = null;
      $scope.fData.temporal = {
        cantidad: 1,
        descuento: 0,
        importe_con_igv: null,
        importe_sin_igv: null,
        elemento: null,
        excluye_igv: 2,
        agrupacion: 0,
        unidad_medida : $scope.fArr.listaUnidadMedida[0]
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
    var elementoNew = true;
    angular.forEach($scope.gridOptions.data, function(value, key) { 
      if(value.id == $scope.fData.temporal.elemento.id ){ 
        elementoNew = false;
      }
    });
    if( elementoNew === false ){
      $scope.fData.temporal.idmedicamento = null;
      $scope.fData.temporal = {
        cantidad: 1,
        descuento: 0,
        importe_con_igv: null,
        importe_sin_igv: null,
        elemento: null,
        excluye_igv: 2,
        agrupacion: 0,
        unidad_medida : $scope.fArr.listaUnidadMedida[0]
      };
      $('#temporalElemento').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'El elemento ya ha sido agregado a la cesta.', type: 'warning', delay: 2000 });
      return false;
    } 
    $scope.arrTemporal = { 
      'id' : $scope.fData.temporal.elemento.id,
      'descripcion' : $scope.fData.temporal.elemento.elemento,
      'cantidad' : $scope.fData.temporal.cantidad,
      'precio_unitario' : $scope.fData.temporal.precio_unitario,
      'importe_sin_igv' : $scope.fData.temporal.importe_sin_igv,
      'igv' : $scope.fData.temporal.igv,
      'importe_con_igv' : $scope.fData.temporal.importe_con_igv,
      'excluye_igv' : 2,
      'unidad_medida' : angular.copy($scope.fData.temporal.elemento.unidad_medida), 
      'agrupacion': 0
    };
    
    $scope.gridOptions.data.push($scope.arrTemporal);
    $scope.calcularTotales(); 
    $scope.fData.temporal = {
      cantidad: 1,
      importe_con_igv: null,
      elemento: null,
      excluye_igv: 2,
      agrupacion: 0,
      unidad_medida : $scope.fArr.listaUnidadMedida[0]
    };
    $scope.fData.classValid = ' input-normal-border'; 
    // console.log($scope.fData.classValid,'$scope.fData.classValid');
  }
  $scope.cambiarModo = function(){
    if( $scope.fData.modo_igv == 2){
      console.log('Calculando modo NO INCLUYE IGV');
      angular.forEach($scope.gridOptions.data,function (value, key) { 
        $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].precio_unitario) * parseFloat($scope.gridOptions.data[key].cantidad)).toFixed(2);
        if( $scope.gridOptions.data[key].excluye_igv == 1 ){
          $scope.gridOptions.data[key].igv = 0.00;
        }else{
          $scope.gridOptions.data[key].igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv)*0.18).toFixed(2);
        }
        $scope.gridOptions.data[key].importe_con_igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv) + parseFloat($scope.gridOptions.data[key].igv)).toFixed(2);
        
      });
    }
    if( $scope.fData.modo_igv == 1 ){ 
      console.log('Calculando modo INCLUYE IGV');
      angular.forEach($scope.gridOptions.data,function (value, key) {
        $scope.gridOptions.data[key].importe_con_igv = (parseFloat($scope.gridOptions.data[key].precio_unitario) * parseFloat($scope.gridOptions.data[key].cantidad)).toFixed(2);
        if( $scope.gridOptions.data[key].excluye_igv == 1 ){
          $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].importe_con_igv)).toFixed(2);
          $scope.gridOptions.data[key].igv = 0.00;
        } else{
          $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].importe_con_igv) / 1.18).toFixed(2);
          $scope.gridOptions.data[key].igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv)*0.18).toFixed(2);
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
    $scope.fData.subtotal = MathFactory.redondear(subtotal).toFixed(2);
    $scope.fData.igv = MathFactory.redondear(igv).toFixed(2);
    $scope.fData.total = MathFactory.redondear(total).toFixed(2);
  }
  $scope.calcularImporte = function (){
    if($scope.fData.temporal.precio_unitario != '' && $scope.fData.temporal.cantidad != '' && angular.isObject($scope.fData.temporal.elemento) ){
      //$scope.fData.temporal.valor = (parseFloat($scope.fData.temporal.precio_unitario) * $scope.fData.temporal.cantidad).toFixed(2);
      //$scope.fData.temporal.descuento_valor = ($scope.fData.temporal.valor * $scope.fData.temporal.descuento / 100).toFixed(2);
      if( $scope.fData.modo_igv == 2 ){ 
        console.log('Calculando modo NO INCLUYE IGV');
        $scope.fData.temporal.importe_sin_igv = (parseFloat($scope.fData.temporal.precio_unitario) * parseFloat($scope.fData.temporal.cantidad)).toFixed(2);
        $scope.fData.temporal.igv = ($scope.fData.temporal.importe_sin_igv * 0.18).toFixed(2);
        $scope.fData.temporal.importe_con_igv = (parseFloat($scope.fData.temporal.importe_sin_igv) + parseFloat($scope.fData.temporal.igv)).toFixed(2);
      }
      if( $scope.fData.modo_igv == 1 ){ 
        console.log('Calculando modo INCLUYE IGV');
        console.log();
        $scope.fData.temporal.importe_con_igv = (parseFloat($scope.fData.temporal.precio_unitario) * parseFloat($scope.fData.temporal.cantidad)).toFixed(2);
        $scope.fData.temporal.importe_sin_igv = ($scope.fData.temporal.importe_con_igv / 1.18).toFixed(2);
        $scope.fData.temporal.igv =($scope.fData.temporal.importe_sin_igv * 0.18).toFixed(2);
      }
    }else{
      $scope.fData.temporal.importe_sin_igv = null;
      $scope.fData.temporal.importe_con_igv = null;
      $scope.fData.temporal.elemento = null;
    }
  } 
}]);

app.service("CotizacionServices",function($http, $q, handleBehavior) {
    return({
        sGenerarNumeroCotizacion: sGenerarNumeroCotizacion,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sGenerarNumeroCotizacion(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cotizacion/generar_numero_cotizacion",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cotizacion/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cotizacion/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cotizacion/anular",
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