app.controller('HistorialCotizacionCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'CotizacionServices',
    'SedeServices',
    'CategoriaElementoServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		CotizacionServices,
    SedeServices,
    CategoriaElementoServices
) {
   
  $scope.metodos = {}; // contiene todas las funciones 
  $scope.fData = {}; // contiene todas las variables de formulario 
	$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  $scope.mySelectionGrid = []; 
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

  // CATEGORIA ELEMENTO
  $scope.metodos.listaCategoriasElemento = function(myCallback) {
    var myCallback = myCallback || function() { };
    CategoriaElementoServices.sListarCbo().then(function(rpta) {
      $scope.fArr.listaCategoriasElemento = rpta.datos;
      myCallbackElemento();
    });
  };

  var myCallbackElemento = function() { 
    $scope.fArr.listaCategoriasElemento.splice(0,0,{ id : 'ALL', descripcion:'--TODOS--'}); 
    $scope.fBusqueda.categoria_elemento = $scope.fArr.listaCategoriasElemento[0]; 
  }
  $scope.metodos.listaCategoriasElemento(myCallbackElemento); 
  
  // ESTADO DE COTIZACION 
  $scope.fArr.listaEstadosCotizacion = [
    {'id' : 'ALL', 'descripcion' : '--TODOS--'},
    {'id' : 1, 'descripcion' : 'POR ENVIAR'},
    {'id' : 2, 'descripcion' : 'ENVIADO'},
    {'id' : 2, 'descripcion' : 'NOTA DE PEDIDO'}
  ]; 
  $scope.fBusqueda.estado_cotizacion = $scope.fArr.listaEstadosCotizacion[0]; 

  $scope.tabs = [true, false];
  $scope.tab = function(index){
    angular.forEach($scope.tabs, function(i, v) {
      $scope.tabs[v] = false;
    });
    $scope.tabs[index] = true;
  }

  $scope.btnBuscar = function(){ 
    $scope.gridOptionsCot.enableFiltering = !$scope.gridOptionsCot.enableFiltering;
    $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };

  $scope.btnBuscarCotDet = function(){ 
    $scope.gridOptionsCotDet.enableFiltering = !$scope.gridOptionsCotDet.enableFiltering;
    $scope.gridApiCotDet.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };

  var paginationOptions = { 
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsCot = {
    rowHeight: 30,
    paginationPageSizes: [100, 500, 1000, 10000],
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
      { field: 'idcotizacion', name: 'cot.idcotizacion', displayName: 'ID', width: '75', visible: false },
      { field: 'num_cotizacion', name: 'cot.num_cotizacion', displayName: 'COD. COTIZACION', width: '120',  sort: { direction: uiGridConstants.DESC} },
      { field: 'fecha_emision', name: 'cot.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false },
      { field: 'fecha_registro', name: 'cot.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
      { field: 'colaborador', name: 'col.colaborador', displayName: 'Asesor de Venta', minWidth: 160 },
      { field: 'colaborador_reg', name: 'colaborador_reg', displayName: 'Generado por:', minWidth: 160, visible: false },
      { field: 'plazo_entrega', name: 'cot.plazo_entrega', displayName: 'Plazo de Entrega', minWidth: 120 },
      { field: 'validez_oferta', name: 'cot.validez_oferta', displayName: 'Validez Oferta', minWidth: 120, visible: false },
      { field: 'forma_pago', name: 'fp.descripcion_fp', displayName: 'Forma de Pago', minWidth: 120 },
      { field: 'sede', name: 'se.descripcion_se', displayName: 'Sede', minWidth: 105 },
      { field: 'moneda', name: 'cot.moneda', displayName: 'Moneda', minWidth: 76, enableFiltering: false },
      { field: 'subtotal', name: 'cot.subtotal', displayName: 'Subtotal', minWidth: 90 },
      { field: 'igv', name: 'cot.igv', displayName: 'IGV', minWidth: 80 },
      { field: 'total', name: 'cot.total', displayName: 'Total', minWidth: 80 },
      { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: '95', enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
          cellTemplate:'<div class="ui-grid-cell-contents">' + 
            '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class=" label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
            '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
            '</div>' 
      }
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
          'cot.idcotizacion' : grid.columns[1].filters[0].term,
          'cot.num_cotizacion' : grid.columns[2].filters[0].term,
          "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[5].filters[0].term,
          "CONCAT(col.nombres, ' ', col.apellidos)" : grid.columns[6].filters[0].term,
          "CONCAT(col_reg.nombres, ' ', col_reg.apellidos)" : grid.columns[7].filters[0].term,
          'cot.plazo_entrega' : grid.columns[8].filters[0].term, 
          'cot.validez_oferta' : grid.columns[9].filters[0].term, 
          'fp.descripcion_fp' : grid.columns[10].filters[0].term, 
          'se.descripcion_se' : grid.columns[11].filters[0].term,
          'cot.subtotal' : grid.columns[13].filters[0].term,
          'cot.igv' : grid.columns[14].filters[0].term,
          'cot.total' : grid.columns[15].filters[0].term
        }
        $scope.metodos.getPaginationServerSide();
      });
    }
  };
  paginationOptions.sortName = $scope.gridOptionsCot.columnDefs[1].name; 
  $scope.metodos.getPaginationServerSide = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptions,
      datos: $scope.fBusqueda 
    };
    CotizacionServices.sListarHistorialCotizaciones(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsCot.totalItems = rpta.paginate.totalRows;
      $scope.gridOptionsCot.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSide(true); 
  $scope.btnImprimir = function() { 
    console.log($scope.mySelectionGrid[0],'$scope.mySelectionGrid[0]');
    var arrParams = { 
      titulo: 'VISTA PREVIA DE COTIZACIÓN',
      datos:{
        id: $scope.mySelectionGrid[0].idcotizacion,
        codigo_reporte: 'COT-FCOT'
      },
      envio_correo: 'si',
      salida: 'pdf',
      url: angular.patchURLCI + "Cotizacion/imprimir_cotizacion" 
    }
    ModalReporteFactory.getPopupReporte(arrParams);
  }
  $scope.btnAnular = function() {
    var pMensaje = '¿Realmente desea anular la cotización?';
    $bootbox.confirm(pMensaje, function(result) { 
      if(result){
        var arrParams = { 
          idcotizacion: $scope.mySelectionGrid[0].idcotizacion 
        };
        blockUI.start('Procesando información...');
        CotizacionServices.sAnular(arrParams).then(function (rpta) {
          if(rpta.flag == 1){
            var pTitle = 'OK!';
            var pType = 'success';
            $scope.metodos.getPaginationServerSide(true);
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
  $scope.btnEnviarCotizacion = function() {
    var pMensaje = '¿Realmente desea marcar la cotización como "ENVIADA"?';
    $bootbox.confirm(pMensaje, function(result) { 
      if(result){
        var arrParams = { 
          idcotizacion: $scope.mySelectionGrid[0].idcotizacion 
        };
        blockUI.start('Procesando información...');
        CotizacionServices.sMarcarComoEnviado(arrParams).then(function (rpta) {
          if(rpta.flag == 1){
            var pTitle = 'OK!';
            var pType = 'success';
            $scope.metodos.getPaginationServerSide(true);
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
  //***grid detalle cotizacion
  var paginationOptionsDet = { 
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsCotDet = {
    rowHeight: 30,
    paginationPageSizes: [100, 500, 1000, 10000],
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
      { field: 'iddetallecotizacion', name: 'dcot.iddetallecotizacion', displayName: 'ID', width: '75', visible: false },
      { field: 'num_cotizacion', name: 'cot.num_cotizacion', displayName: 'COD. COTIZACION', width: '120',  sort: { direction: uiGridConstants.DESC} },
      { field: 'fecha_emision', name: 'cot.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false },
      { field: 'fecha_registro', name: 'cot.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'sede', name: 'se.descripcion_se', displayName: 'Sede', minWidth: 105 },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 220 },
      { field: 'categoria_elemento', type: 'object', name: 'cael.descripcion_cael', displayName: 'Categoria Elemento', minWidth: 160, visible: false, enableColumnMenus: false, enableColumnMenu: false, 
        cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+'<label class="label bg-primary block" style="background-color:{{COL_FIELD.color}}">{{ COL_FIELD.descripcion }}</label></div>' 
      }, 
      { field: 'elemento', name: 'ele.descripcion_ele', displayName: 'Elemento', minWidth: 280 }, 
      { field: 'precio_unitario', name: 'dcot.precio_unitario', displayName: 'Precio Unitario', minWidth: 90 }, 
      { field: 'cantidad', name: 'dcot.cantidad', displayName: 'Cantidad', minWidth: 90 },
      { field: 'importe_con_igv', name: 'dcot.importe_con_igv', displayName: 'Importe', minWidth: 90 }, 
      { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: '95', enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
          cellTemplate:'<div class="">' + 
            '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
            '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
            '</div>' 
      }
    ],
    onRegisterApi: function(gridApi) { 
      $scope.gridApiCotDet = gridApi;
      gridApi.selection.on.rowSelectionChanged($scope,function(row){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      $scope.gridApiCotDet.core.on.sortChanged($scope, function(grid, sortColumns) { 
        if (sortColumns.length == 0) {
          paginationOptionsDet.sort = null;
          paginationOptionsDet.sortName = null;
        } else {
          paginationOptionsDet.sort = sortColumns[0].sort.direction;
          paginationOptionsDet.sortName = sortColumns[0].name;
        }
        $scope.metodos.getPaginationServerSideCotDet(true);
      });
      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
        paginationOptionsDet.pageNumber = newPage;
        paginationOptionsDet.pageSize = pageSize;
        paginationOptionsDet.firstRow = (paginationOptionsDet.pageNumber - 1) * paginationOptionsDet.pageSize;
        $scope.metodos.getPaginationServerSideCotDet(true);
      });
      $scope.gridApiCotDet.core.on.filterChanged( $scope, function(grid, searchColumns) {
        var grid = this.grid;
        paginationOptionsDet.search = true; 
        paginationOptionsDet.searchColumn = {
          'dcot.iddetallecotizacion' : grid.columns[1].filters[0].term,
          'cot.num_cotizacion' : grid.columns[2].filters[0].term,
          "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[5].filters[0].term,
          "CONCAT(col.nombres, ' ', col.apellidos)" : grid.columns[6].filters[0].term,
          'cael.descripcion_cael' : grid.columns[7].filters[0].term, 
          'ele.descripcion_ele' : grid.columns[8].filters[0].term, 
          'dcot.precio_unitario' : grid.columns[9].filters[0].term, 
          'dcot.cantidad' : grid.columns[10].filters[0].term,
          'dcot.importe_con_igv' : grid.columns[11].filters[0].term
        }
        $scope.metodos.getPaginationServerSideCotDet();
      });
    }
  };
  paginationOptionsDet.sortName = $scope.gridOptionsCotDet.columnDefs[1].name; 
  $scope.metodos.getPaginationServerSideCotDet = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptionsDet,
      datos: $scope.fBusqueda  
    };
    CotizacionServices.sListarHistorialDetalleCotizaciones(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsCotDet.totalItems = rpta.paginate.totalRows;
      $scope.gridOptionsCotDet.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSideCotDet(true); 

}]); 