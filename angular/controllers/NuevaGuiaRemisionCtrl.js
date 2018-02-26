app.controller('NuevaGuiaRemisionCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ClientePersonaFactory',
    'ClienteEmpresaFactory',
    'ServicioFactory',
    'ProductoFactory',
    'CaracteristicaFactory',
    'ContactoEmpresaFactory',
    'ModalReporteFactory',
    'MathFactory',
		'GuiaRemisionServices',
    'VentaServices',
		'ClienteEmpresaServices',
		'ClientePersonaServices', 
		'ColaboradorServices',
    'TipoDocumentoClienteServices',
    'ClienteServices', 
    'CategoriaClienteServices',
    'CategoriaElementoServices',
    'MotivoTrasladoServices',
    'SerieServices',
    'SedeServices',
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
    ModalReporteFactory,
    MathFactory,
		GuiaRemisionServices,
    VentaServices,
		ClienteEmpresaServices,
		ClientePersonaServices,
		ColaboradorServices,
    TipoDocumentoClienteServices,
    ClienteServices,
    CategoriaClienteServices,
    CategoriaElementoServices,
    MotivoTrasladoServices,
    SerieServices,
    SedeServices,
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
  $scope.fArr.fNotaPedido = {};
  $scope.fArr.fNotaPedidoTemp = {};
  $scope.fData.classEditCliente = 'disabled';
  $scope.fData.punto_llegada_disabled = true;
  $scope.fData.fecha_emision = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  $scope.fData.fecha_inicio_traslado = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 

  $scope.fData.idguiaanterior = null;
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
  // colaboradores 
  $scope.metodos.listaColaboradores = function(myCallbackCol) {
    var myCallbackCol = myCallbackCol || function() { };
    ColaboradorServices.sListarCbo().then(function(rpta) {
      $scope.fArr.listaColaboradores = rpta.datos; 
      myCallbackCol();
    });
  };
  var myCallbackCol = function() { 
    var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
      return obj.id == $scope.fSessionCI.idcolaborador; 
    }).shift(); 
    $scope.fData.colaborador = objIndex; 
  } 
  $scope.metodos.listaColaboradores(myCallbackCol); 
  // puntos de partida 
  $scope.metodos.listaPuntosDePartida = function(myCallbackPP) { 
    var myCallbackPP = myCallbackPP || function() { 
      $scope.fData.punto_partida = $scope.fArr.listaPuntosDePartida[0].descripcion;
    }; 
    SedeServices.sListarDireccionCbo().then(function(rpta) {
      if( rpta.flag == 1 ){
        $scope.fArr.listaPuntosDePartida = rpta.datos; 
        myCallbackPP();
      }
    });
  };
  $scope.metodos.listaPuntosDePartida(null);


  // puntos de llegada 
  $scope.metodos.listaPuntosDeLlegada = function(myCallbackPLL,idclienteempresa) { 
    if( !(idclienteempresa) ){ 
      $scope.fData.punto_llegada_disabled = true;
      return false; 
    }
    var myCallbackPLL = myCallbackPLL || function() { 
      $scope.fData.punto_llegada = $scope.fArr.listaPuntosDeLlegada[0].descripcion;
    }; 
    var arrParams = {
      'idclienteempresa': idclienteempresa 
    }; 
    ClienteEmpresaServices.sListarPuntosLlegada(arrParams).then(function(rpta) {
      if( rpta.flag == 1 ){
        $scope.fData.punto_llegada_disabled = false;
        $scope.fArr.listaPuntosDeLlegada = rpta.datos; 
      }
      myCallbackPLL();
    });
  };
  $scope.metodos.listaPuntosDeLlegada(null,null); 

  // sexos 
  $scope.fArr.listaSexo = [ 
    { id:'M', descripcion:'MASCULINO' },
    { id:'F', descripcion:'FEMENINO' }
  ]; 
  $scope.mySelectionGrid = [];
  $scope.fData.cliente = {}; 

  // COMPROBANTE UNICO: GUIA   
  $scope.fArr.listaTiposDocumentoMov = [
    {'id' : 6, 'descripcion' : 'GUIA DE REMISIÓN'} 
  ]; 
  $scope.fData.tipo_documento_mov = $scope.fArr.listaTiposDocumentoMov[0];

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

  // MOTIVO DE TRASLADO  
  $scope.metodos.listaMotivosTraslado = function(myCallback) { 
    var myCallback = myCallback || function() { };
    MotivoTrasladoServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaMotivosTraslado = rpta.datos; 
        myCallback();
      } 
    });
  }
  var myCallbackMT = function() { 
    $scope.fArr.listaMotivosTraslado.splice(0,0,{ id : '0', descripcion:'--Seleccione motivo de traslado--'}); 
    $scope.fData.motivo_traslado = $scope.fArr.listaMotivosTraslado[0]; 
  }
  $scope.metodos.listaMotivosTraslado(myCallbackMT); 

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

  // UNIDADES DE MEDIDA // tipo_documento_mov
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

  // WATCHERS 
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

        // actualizamos vendedor / colaborador 
        var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
          return obj.id == rpta.datos.cliente.colaborador.id; 
        }).shift(); 
        $scope.fData.colaborador = objIndex; 
        // cargar puntos de llegada 
        // var myCallbackPLL = function() { 
        //   var objIndex = $scope.fArr.listaPuntosDeLlegada.filter(function(obj) { 
        //     return obj.id == rpta.datos.idclienteempresa; 
        //   }).shift(); 
        //   $scope.fData.colaborador = objIndex; 
        // } 
        $scope.metodos.listaPuntosDeLlegada(null, rpta.datos.cliente.idclienteempresa); 

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

              // actualizamos vendedor / colaborador 
              var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
                return obj.id == $scope.mySelectionGridBC[0].colaborador.id; 
              }).shift(); 
              $scope.fData.colaborador = objIndex; 

              // cargar direcciones: 
              $scope.metodos.listaPuntosDeLlegada(null, $scope.mySelectionGridBC[0].idclienteempresa); 

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
      $scope.noResultsELE = false;
      if( rpta.flag === 0 ){
        $scope.noResultsELE = true;
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
    $scope.fData.cliente.tipo_cliente = 'ce'; // empresa 
    $scope.fData.cliente.descripcion = model.razon_social;
    $scope.fData.cliente.razon_social = model.razon_social;
    $scope.fData.cliente.representante_legal = model.representante_legal;
    $scope.fData.cliente.dni_representante_legal = model.dni_representante_legal; 

    $scope.fData.cliente.telefono_contacto = model.telefono_fijo; 
    $scope.fData.cliente.anexo_contacto = model.anexo; 
    // actualizamos vendedor / colaborador 
    var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
      return obj.id == model.colaborador.id; 
    }).shift(); 
    $scope.fData.colaborador = objIndex; 
    // cargar direcciones: 
    $scope.metodos.listaPuntosDeLlegada(null, model.idclienteempresa); 
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

  // BUSCAR Contactos  
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
              // actualizamos vendedor / colaborador 
              var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
                return obj.id == $scope.mySelectionGridCO[0].cliente_empresa.colaborador.id; 
              }).shift(); 
              $scope.fData.colaborador = objIndex; 
              // cargar direcciones: 
              $scope.metodos.listaPuntosDeLlegada(null, $scope.mySelectionGridCO[0].idclienteempresa); 
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
          enableColumnMenus: false, 
          enableColumnMenu: false,
          multiSelect: false,
          data: $scope.fData.temporal.caracteristicas || [],
          columnDefs: [ 
            { field: 'idcaracteristica', enableSorting: false, displayName: 'ID', width: '75', enableCellEdit: false, visible: false }, 
            { field: 'orden', displayName: 'ORDEN', width: '100', enableCellEdit: false, enableColumnMenus: false, enableColumnMenu: false, type:'number', 
              enableFiltering: false, enableSorting: true, sort: { direction: uiGridConstants.ASC } }, 
            { field: 'descripcion', enableSorting: true, displayName: 'Descripción', minWidth: 160, enableCellEdit: false }, 
            { field: 'valor', displayName: 'Valor', minWidth: 160, cellClass:'ui-editCell', enableCellEdit: true, enableSorting: true, 
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
        cellTemplate:'<div class="ui-grid-cell-contents "> <a class="text-info block" href="" ng-click="grid.appScope.btnGestionCaracteristicasDetalle(row,grid.renderContainers.body.visibleRowCache.indexOf(row))">'+ '{{ COL_FIELD }}</a></div>', 
        cellTooltip: function( row, col ) {
          return row.entity.descripcion;
        }
      },
      { field: 'cantidad', displayName: 'CANT.', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-center' },
      { field: 'unidad_medida', displayName: 'U. MED.', width: 90, editableCellTemplate: 'ui-grid/dropdownEditor', 
        editDropdownValueLabel: 'descripcion', editDropdownOptionsArray: $scope.unidadMedidaOptions,cellFilter: 'griddropdown:this',cellClass:'ui-editCell text-center'},
      { field: 'num_paquetes', displayName: 'N° PAQUETES', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-center' },
      { field: 'accion', displayName: 'ACCIÓN', width: 110, enableCellEdit: false, enableSorting: false, 
        cellTemplate:'<div class="m-xxs text-center">'+ 
          '<button uib-tooltip="Clonar" tooltip-placement="left" type="button" class="btn btn-xs btn-gray mr-xs" ng-click="grid.appScope.btnClonarFila(row)"> <i class="fa fa-plus"></i> </button>' + 
          '<button uib-tooltip="Ver Características" tooltip-placement="left" type="button" class="btn btn-xs btn-info mr-xs" ng-click="grid.appScope.btnGestionCaracteristicasDetalle(row,grid.renderContainers.body.visibleRowCache.indexOf(row))"> <i class="fa fa-eye"></i> </button>' +
          '<button uib-tooltip="Quitar" tooltip-placement="left" type="button" class="btn btn-xs btn-danger" ng-click="grid.appScope.btnQuitarDeLaCesta(row)"> <i class="fa fa-trash"></i> </button>' + 
          '</div>' 
      } // uib-tooltip
    ]
    ,onRegisterApi: function(gridApi) { 
      $scope.gridApi = gridApi;
      gridApi.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
        rowEntity.column = colDef.field;
        if(rowEntity.column == 'cantidad'){
          if( !(rowEntity.cantidad >= 1) ){
            var pTitle = 'Advertencia!';
            var pType = 'warning';
            rowEntity.cantidad = oldValue;
            pinesNotifications.notify({ title: pTitle, text: 'La cantidad debe ser mayor o igual a 1', type: pType, delay: 3500 });
            return false;
          }
        }
        $scope.$apply();
      });
    }
  };
  $scope.getTableHeight = function() {
     var rowHeight = 26; // your row height 
     var headerHeight = 25; // your header height 
     return { 
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
      'unidad_medida' : angular.copy(row.entity.unidad_medida), 
      'num_paquetes' : row.entity.num_paquetes,
      'caracteristicas': angular.copy(row.entity.caracteristicas)
    }; 
    $scope.gridOptions.data.push(arrFClon); 
  }
  $scope.agregarItem = function () {
    $('#temporalElemento').focus();
    if( !angular.isObject($scope.fData.temporal.elemento) ){ 
      $scope.fData.temporal = {
        cantidad: 1,
        elemento: null,
        descripcion: null,
        unidad_medida : $scope.fArr.listaUnidadMedida[0],
        num_paquetes: null,
        caracteristicas: null
      };
      $('#temporalElemento').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'No se ha seleccionado el elemento', type: 'warning', delay: 2000 });
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
      'elemento' : $scope.fData.temporal.elemento.elemento,
      'descripcion' : $scope.fData.temporal.elemento.elemento,
      'cantidad' : $scope.fData.temporal.cantidad,
      'unidad_medida' : angular.copy($scope.fData.temporal.unidad_medida), 
      'num_paquetes': $scope.fData.temporal.num_paquetes,
      'caracteristicas': angular.copy($scope.fData.temporal.caracteristicas)
    };
    if( $scope.gridOptions.data === null ){
      $scope.gridOptions.data = [];
    }
    $scope.gridOptions.data.push($scope.arrTemporal);
    $scope.calcularTotales(); 
    $scope.fData.temporal = {
      cantidad: 1,
      descripcion: null,
      elemento: null,
      unidad_medida : $scope.fArr.listaUnidadMedida[0],
      num_paquetes: null,
      caracteristicas: null 
    };
    $scope.fData.classValid = ' input-normal-border'; 
    // $scope.fData.temporal.caracteristicas = null;
    // console.log($scope.fData.classValid,'$scope.fData.classValid');
  }
  $scope.agregarItemDesdeNotaPedido = function() { 
    blockUI.start('Procesando información...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'NotaPedido/ver_popup_busqueda_nota_pedido',
      size: 'lg',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.titleForm = 'Selección de Nota de Pedido'; 

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
          $scope.fBusquedaNP.cliente.descripcion = $scope.fData.cliente.descripcion || $scope.fData.cliente.cliente; 
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
            { field: 'forma_pago', name: 'fp.descripcion_fp', displayName: 'Forma de Pago', minWidth: 120, visible: false },
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
                  'identify' : $scope.mySelectionGridNP[0].idmovimiento, 
                  'flag_all': true 
                }; 
                if($scope.mySelectionGridNP[0].estado.valor == 0){ 
                  pinesNotifications.notify({ title: 'Advertencia.', text: 'La nota de pedido a sido anulada. No se puede seleccionar', type: 'warning', delay: 3000 });
                  return false;
                }
                NotaPedidoServices.sObtenerEstaNotaPedido(arrParams).then(function(rpta) { 
                  if( rpta.flag == 1 || rpta.flag == 2 ){ // normal  
                    $timeout(function() { 
                      // llenar info de cliente 
                      $scope.fData.cliente = rpta.datos.cliente; 
                      $scope.fData.num_documento = rpta.datos.num_documento; 
                      $scope.fData.contacto = rpta.datos.contacto; 
                      $scope.fData.idcontacto = rpta.datos.idcontacto; 
                      $scope.fData.idnotapedido = rpta.datos.idnotapedido; 
                      // tipo documento cliente 
                      var myCallBackTD = function() { 
                        var objIndex = $scope.fArr.listaTiposDocumentoCliente.filter(function(obj) { 
                          return obj.id == rpta.datos.tipo_documento_cliente.id; 
                        }).shift(); 
                        $scope.fData.tipo_documento_cliente = objIndex; 
                      }
                      // vendedor asignado desde NP 
                      var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
                        return obj.id == rpta.datos.idcolaborador; 
                      }).shift(); 
                      $scope.fData.colaborador = objIndex; 

                      $scope.metodos.listaTiposDocumentoCliente(myCallBackTD); 
                      // cargar direcciones: 
                      $scope.metodos.listaPuntosDeLlegada(null, rpta.datos.cliente.idclienteempresa); 
                      // llenar detalle 
                      $scope.gridOptions.data = rpta.detalle; 
                    }, 200);
                    pinesNotifications.notify({ title: 'OK!', text: 'Se cargó la nota de pedido.', type: 'success', delay: 3000 }); 
                    $uibModalInstance.dismiss('cancel');
                  }else if( rpta.flag == 2 ){ // facturado 
                    pinesNotifications.notify({ title: 'OK!', text: 'La Nota de Pedido ya ha sido facturada con anterioridad.', type: 'warning', delay: 3000 }); 
                  }
                  blockUI.stop(); 
                }); 
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
          // $scope.metodos.listaSedes(myCallbackSede); 
        } 
      }
    });
  }
  $scope.btnGestionCaracteristicasDetalle = function(row,indice) { 
    console.log(row,'row',indice,'indiceew');
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
        $scope.metodos.getPaginationServerSideCR = function(loader,callback) { // getPaginationServerSideCR
          var callback = callback || function() { }; 
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
        } 
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
          data: [],
          columnDefs: [ 
            { field: 'id', enableSorting: false, displayName: 'ID', width: '75', enableCellEdit: false, visible: false }, 
            { field: 'orden', displayName: 'ORDEN', width: '100', enableCellEdit: false, enableColumnMenus: false, enableColumnMenu: false, type:'number', 
              enableFiltering: false, enableSorting: true, sort: { direction: uiGridConstants.ASC } }, 
            { field: 'descripcion', enableSorting: true, displayName: 'Descripción', minWidth: 160, enableCellEdit: false }, 
            { field: 'valor', displayName: 'Valor', minWidth: 160, cellClass:'ui-editCell', enableCellEdit: true, enableSorting: true, 
              editableCellTemplate: '<input type="text" ui-grid-editor ng-model="MODEL_COL_FIELD" uib-typeahead="item.descripcion as item.descripcion for item in grid.appScope.getVariableAutocomplete($viewValue)" class="" >'
            }             
          ], 
          onRegisterApi: function(gridApi) { 
            $scope.gridApi = gridApi; 
          }
        }; 
        var myCallbackCaract = function() { 
          //console.log(row.entity.caracteristicas,'row.entity.caracteristicas');
          angular.forEach(row.entity.caracteristicas, function(val,key) { 
            console.log(val,'val');
            var arrFilaTemp = { 
              'id' : val.id,
              'orden' : val.orden,
              'descripcion' : val.descripcion,
              'valor' : val.valor
            };
            $scope.fArr.gridOptionsCRDet.data.push(arrFilaTemp);
          });
          // $scope.fArr.gridOptionsCRDet.data = row.entity.caracteristicas;
        }
        //console.log(row.entity.caracteristicas,'row.entity.caracteristicas');
        if( !(row.entity.caracteristicas) ){ 
          $scope.metodos.getPaginationServerSideCR(true,myCallbackCaract); 
        }else{ 
          myCallbackCaract();
        } 
        //var rowCaracteristicas = row.caracteristicas; 
        $scope.cancel = function () { 
          $scope.gridOptions.data[indice].caracteristicas = $scope.fArr.gridOptionsCRDet.data; 
          console.log($scope.gridOptions.data[indice].caracteristicas,'$scope.gridOptions.data[indice].caracteristicas')
          $uibModalInstance.dismiss('cancel');
        } 
      }
    });
  }
  
  $scope.btnQuitarDeLaCesta = function (row) { 
    var index = $scope.gridOptions.data.indexOf(row.entity); 
    $scope.gridOptions.data.splice(index,1);
  }
  $scope.btnAsociarComprobante = function() {
    blockUI.start('Cargando comprobantes...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'Venta/ver_popup_busqueda_venta',
      size: 'lg',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fBusqueda = {}; 
        $scope.fBusqueda.desde = $filter('date')(new Date(),'01-MM-yyyy');
        $scope.fBusqueda.desdeHora = '00';
        $scope.fBusqueda.desdeMinuto = '00';
        $scope.fBusqueda.hastaHora = 23;
        $scope.fBusqueda.hastaMinuto = 59;
        $scope.fBusqueda.hasta = $filter('date')(new Date(),'dd-MM-yyyy');
          
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
          $scope.fBusqueda.sede = $scope.fArr.listaSedes[0]; 
        }
        $scope.metodos.listaSedes(myCallback); 

        $scope.titleForm = 'Búsqueda de Comprobantes'; 
        var paginationOptionsCOMPR = {
          pageNumber: 1,
          firstRow: 0,
          pageSize: 100,
          sort: uiGridConstants.DESC,
          sortName: null,
          search: null
        };
        $scope.mySelectionGridCOMPR = [];
        $scope.gridOptionsCOMPR = {
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
            { field: 'idmovimiento', name: 've.idmovimiento', displayName: 'ID', width: '75', visible: false,  sort: { direction: uiGridConstants.DESC} },
            { field: 'serie', name: 've.numero_serie', displayName: 'SERIE', width: 60 },
            { field: 'correlativo', name: 've.numero_correlativo', displayName: 'CORRELATIVO', width: 100 },
            { field: 'fecha_emision', name: 've.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false },
            { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
            { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: '95', enableFiltering: false, enableSorting: false, enableColumnMenus: false, 
              enableColumnMenu: false, cellTemplate:'<div class="">' + 
                '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
                '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
                '</div>' 
            }
          ],
          onRegisterApi: function(gridApi) { // gridComboOptions 
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope,function(row){
              $scope.mySelectionGridCOMPR = gridApi.selection.getSelectedRows();
              if( $scope.mySelectionGridCOMPR[0].estado_movimiento == 1 ){
                $scope.fData.idmovimiento = $scope.mySelectionGridCOMPR[0].idmovimiento;
                $scope.fData.tipo_documento_venta = $scope.mySelectionGridCOMPR[0].descripcion_tdm; 
                $scope.fData.num_serie_venta = $scope.mySelectionGridCOMPR[0].serie; 
                $scope.fData.num_correlativo_venta = $scope.mySelectionGridCOMPR[0].correlativo; 
              }else{
                pinesNotifications.notify({ title: 'Advertencia.', text: 'No puede seleccionar un comprobante anulado', type: 'warning', delay: 3000 });
                return false;
              }
              // actualizamos vendedor / colaborador 
              // var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
              //   return obj.id == $scope.mySelectionGridCOMPR[0].cliente_empresa.colaborador.id; 
              // }).shift(); 
              // $scope.fData.colaborador = objIndex; 
              $uibModalInstance.dismiss('cancel');
              return; 
            });
            $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
              if (sortColumns.length == 0) {
                paginationOptionsCOMPR.sort = null;
                paginationOptionsCOMPR.sortName = null;
              } else {
                paginationOptionsCOMPR.sort = sortColumns[0].sort.direction;
                paginationOptionsCOMPR.sortName = sortColumns[0].name;
              }
              $scope.metodos.getPaginationServerSideCOMPR(true);
            });
            gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
              paginationOptionsCOMPR.pageNumber = newPage;
              paginationOptionsCOMPR.pageSize = pageSize;
              paginationOptionsCOMPR.firstRow = (paginationOptionsCOMPR.pageNumber - 1) * paginationOptionsCOMPR.pageSize;
              $scope.metodos.getPaginationServerSideCOMPR(true);
            });
            $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
              var grid = this.grid;
              paginationOptionsCOMPR.search = true;
              paginationOptionsCOMPR.searchColumn = { 
                've.idmovimiento' : grid.columns[1].filters[0].term,
                've.numero_serie' : grid.columns[2].filters[0].term,
                've.numero_correlativo' : grid.columns[3].filters[0].term,
                "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[5].filters[0].term 
              }; 
              $scope.metodos.getPaginationServerSideCOMPR();
            });
          }
        }; 
        paginationOptionsCOMPR.sortName = $scope.gridOptionsCOMPR.columnDefs[0].name; 
        $scope.metodos.getPaginationServerSideCOMPR = function(loader) { 
          if(loader){
            blockUI.start('Procesando información...'); 
          }
          var arrParams = {
            paginate : paginationOptionsCOMPR,
            datos: $scope.fBusqueda 
          };
          VentaServices.sListarHistorialVentas(arrParams).then(function (rpta) { 
            if( rpta.datos.length == 0 ){
              rpta.paginate = { totalRows: 0 };
            }
            $scope.gridOptionsCOMPR.totalItems = rpta.paginate.totalRows;
            $scope.gridOptionsCOMPR.data = rpta.datos; 
            if( loader ){
              blockUI.stop(); 
            }
          });
          $scope.mySelectionGridCOMPR = [];
        }
        $scope.metodos.getPaginationServerSideCOMPR(true); 
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        }
        
      }
    });  
  }
  $scope.mismoCliente = function() { 
    $scope.fData.temporal = {
      cantidad: 1,
      elemento: null,
      descripcion: null,
      unidad_medida : $scope.fArr.listaUnidadMedida[0],
      num_paquetes: null,
      caracteristicas: null 
    };
    // console.log('mismo cliente');
    $scope.gridOptions.data = [];
    $scope.fData.isRegisterSuccess = false;
    $('#temporalElemento').focus();
  }
  $scope.grabar = function() { 
    if($scope.fData.isRegisterSuccess){
      pinesNotifications.notify({ title: 'Advertencia.', text: 'La guia de remisión ya fue registrada', type: 'warning', delay: 3000 });
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
    GuiaRemisionServices.sRegistrar($scope.fData).then(function (rpta) { 
      blockUI.stop();
      if(rpta.flag == 1){
        pTitle = 'OK!';
        pType = 'success'; 
        $scope.fData.isRegisterSuccess = true;
        $scope.fData.idguiaanterior = rpta.idguiaremision;
      }else if(rpta.flag == 0){
        var pTitle = 'Advertencia!';
        var pType = 'warning';
      }else{
        alert('Algo salió mal...');
      }
      pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
    });
  }
  $scope.btnImprimirHTML = function() { 
    var arrParams = {
      id: $scope.fData.idguiaanterior, 
      codigo_reporte: 'GR-COMPR' 
    } 
    GuiaRemisionServices.sImprimirComprobanteHTML(arrParams).then(function (rpta) { 
      if(rpta.flag == 1){
        var printContents = rpta.html; 
        var popupWin = window.open('', 'windowName', 'width=1270,height=847'); 
        popupWin.document.open()
        popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="assets/css/stylePrint.css" /></head><body onload="window.print()">' + printContents + '</html>');
        popupWin.document.close();
      }else { 
        if(rpta.flag == 0) { // ALGO SALIÓ MAL
          var pTitle = 'Error';
          var pText = 'No se pudo realizar la impresión. Contacte con el Area de Sistemas.';
          var pType = 'warning';
        }
        
        pinesNotifications.notify({ title: pTitle, text: pText, type: pType, delay: 3500 });
      }
    });
  }

}]);

app.service("GuiaRemisionServices",function($http, $q, handleBehavior) {
    return({
        sObtenerEstaGuiaRemision: sObtenerEstaGuiaRemision,
        sListarDetalleEstaGuiaRemision: sListarDetalleEstaGuiaRemision, 
        sListarHistorialGuiaRemision: sListarHistorialGuiaRemision,
        sListarHistorialDetalleGuiaRemision: sListarHistorialDetalleGuiaRemision,
        sImprimirComprobanteHTML: sImprimirComprobanteHTML,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sObtenerEstaGuiaRemision(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/obtener_esta_guia_remision",  
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarDetalleEstaGuiaRemision(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/listar_detalle_esta_guia_remision",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarHistorialGuiaRemision(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/listar_guias_remision_historial",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarHistorialDetalleGuiaRemision(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/listar_detalle_guias_remision_historial",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sImprimirComprobanteHTML(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/imprimir_comprobante_guia_remision_html",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"GuiaRemision/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});
app.filter('griddropdown', function() {
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