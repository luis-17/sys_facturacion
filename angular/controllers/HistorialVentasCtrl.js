app.controller('HistorialVentasCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
    'VentaServices',
    'SedeServices',
    'CategoriaElementoServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
    VentaServices,
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
  // ESTADO DE VENTA 
  $scope.fArr.listaEstadosVE = [
    {'id' : 'ALL', 'descripcion' : '--TODOS--'},
    {'id' : 1, 'descripcion' : 'REGISTRADO'},
    {'id' : 0, 'descripcion' : 'ANULADO'}
  ]; 
  $scope.fBusqueda.estado_ve = $scope.fArr.listaEstadosVE[0]; 

  $scope.tabs = [true, false];
  $scope.tab = function(index){
    angular.forEach($scope.tabs, function(i, v) {
      $scope.tabs[v] = false;
    });
    $scope.tabs[index] = true;
  }

  $scope.btnBuscar = function(){ 
    $scope.gridOptionsVE.enableFiltering = !$scope.gridOptionsVE.enableFiltering;
    $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };
  $scope.btnBuscarVEDet = function(){ 
    $scope.gridOptionsVEDet.enableFiltering = !$scope.gridOptionsVEDet.enableFiltering;
    $scope.gridApiVEDet.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };  
  var paginationOptions = { 
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsVE = {
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
      { field: 'idmovimiento', name: 've.idmovimiento', displayName: 'ID', width: '75', visible: false },
      { field: 'descripcion_tdm', name: 'tdm.descripcion_tdm', displayName: 'COMPROBANTE', width: 100 },
      { field: 'serie', name: 've.numero_serie', displayName: 'SERIE', width: 60 },
      { field: 'correlativo', name: 've.numero_correlativo', displayName: 'CORRELATIVO', width: 100 },
      { field: 'fecha_emision', name: 've.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false,  sort: { direction: uiGridConstants.DESC} },
      { field: 'fecha_registro', name: 've.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
      { field: 'colaborador', name: 'colaborador', displayName: 'Colaborador', minWidth: 160 },
      { field: 'usuario', name: 'us.username', displayName: 'Usuario', minWidth: 160, visible: false },
      { field: 'forma_pago', name: 'fp.descripcion_fp', displayName: 'Forma de Pago', minWidth: 100 },
      { field: 'sede', name: 'se.descripcion_se', displayName: 'Sede', minWidth: 100 },
      { field: 'moneda', name: 've.moneda', displayName: 'Moneda', minWidth: 76, enableFiltering: false },
      { field: 'subtotal', name: 've.subtotal', displayName: 'Subtotal', minWidth: 90 },
      { field: 'igv', name: 've.igv', displayName: 'IGV', minWidth: 70 },
      { field: 'total', name: 've.total', displayName: 'Total', minWidth: 80 },
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
      $scope.gridApi.core.on.filterChanged($scope, function(grid, searchColumns) {
        var grid = this.grid;
        paginationOptions.search = true; 
        paginationOptions.searchColumn = { 
          've.idmovimiento' : grid.columns[1].filters[0].term,
          'tdm.descripcion_tdm' : grid.columns[2].filters[0].term,
          've.numero_serie' : grid.columns[3].filters[0].term,
          've.numero_correlativo' : grid.columns[4].filters[0].term,
          "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[7].filters[0].term,
          "CONCAT(col.nombres, ' ', col.apellidos)" : grid.columns[8].filters[0].term,
          "us.username" : grid.columns[9].filters[0].term, 
          'fp.descripcion_fp' : grid.columns[10].filters[0].term, 
          'se.descripcion_se' : grid.columns[11].filters[0].term, 
          've.moneda' : grid.columns[12].filters[0].term, 
          've.subtotal' : grid.columns[13].filters[0].term, 
          've.igv' : grid.columns[14].filters[0].term, 
          've.total' : grid.columns[15].filters[0].term 
        } 
        $scope.metodos.getPaginationServerSide(); 
      });
    }
  };
  paginationOptions.sortName = $scope.gridOptionsVE.columnDefs[2].name; 
  $scope.metodos.getPaginationServerSide = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptions,
      datos: $scope.fBusqueda 
    };
    VentaServices.sListarHistorialVentas(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsVE.totalItems = rpta.paginate.totalRows;
      $scope.gridOptionsVE.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSide(true); 


  // *** Historial venta ***
  var paginationOptionsVEDet = { 
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsVEDet = {
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
      { field: 'iddetallemovimiento', name: 'ved.iddetallemovimiento', displayName: 'ID', width: '75', visible: false },
      { field: 'descripcion_tdm', name: 'tdm.descripcion_tdm', displayName: 'COMPROBANTE', width: 100 },
      { field: 'serie', name: 've.numero_serie', displayName: 'SERIE', width: 60 },
      { field: 'correlativo', name: 've.numero_correlativo', displayName: 'CORRELATIVO', width: 100 },
      { field: 'fecha_emision', name: 've.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false,  sort: { direction: uiGridConstants.DESC} },
      { field: 'fecha_registro', name: 've.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
      { field: 'usuario', name: 'us.username', displayName: 'Usuario', minWidth: 160, visible: false },
      { field: 'sede', name: 'se.descripcion_se', displayName: 'Sede', minWidth: 100 },
      { field: 'categoria_elemento', type: 'object', name: 'cael.descripcion_cael', displayName: 'Categoria Elemento', minWidth: 160, enableColumnMenus: false, enableColumnMenu: false,cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+'<label class="label bg-primary block" style="background-color:{{COL_FIELD.color}}">{{ COL_FIELD.descripcion }}</label></div>' 
      },  
      { field: 'elemento', name: 'ele.descripcion_ele', displayName: 'Elemento', minWidth: 160 },          
      { field: 'precio_unitario', name: 'npd.precio_unitario', displayName: 'P. Unitario', minWidth: 90 }, 
      { field: 'cantidad', name: 'npd.cantidad', displayName: 'Cantidad', minWidth: 90 },
      { field: 'importe_con_igv', name: 'npd.importe_con_igv', displayName: 'Importe', minWidth: 90 }, 
      { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: '95', enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
          cellTemplate:'<div class="">' + 
            '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
            '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
            '</div>' 
      }
    ],
    onRegisterApi: function(gridApi) { 
      $scope.gridApiVEDet = gridApi;
      gridApi.selection.on.rowSelectionChanged($scope,function(row){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      $scope.gridApiVEDet.core.on.sortChanged($scope, function(grid, sortColumns) { 
        if (sortColumns.length == 0) {
          paginationOptionsVEDet.sort = null;
          paginationOptionsVEDet.sortName = null;
        } else {
          paginationOptionsVEDet.sort = sortColumns[0].sort.direction;
          paginationOptionsVEDet.sortName = sortColumns[0].name;
        }
        $scope.metodos.getPaginationServerSideVEDet(true);
      });
      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
        paginationOptionsVEDet.pageNumber = newPage;
        paginationOptionsVEDet.pageSize = pageSize;
        paginationOptionsVEDet.firstRow = (paginationOptionsVEDet.pageNumber - 1) * paginationOptionsVEDet.pageSize;
        $scope.metodos.getPaginationServerSideVEDet(true);
      });
      $scope.gridApiVEDet.core.on.filterChanged($scope, function(grid, searchColumns) {
        var grid = this.grid;
        paginationOptionsVEDet.search = true; 
        paginationOptionsVEDet.searchColumn = { 
          'ved.iddetallemovimiento' : grid.columns[1].filters[0].term,
          'tdm.descripcion_tdm' : grid.columns[2].filters[0].term,
          've.numero_serie' : grid.columns[3].filters[0].term,
          've.numero_correlativo' : grid.columns[4].filters[0].term,
          "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[7].filters[0].term,
          "CONCAT(col.nombres, ' ', col.apellidos)" : grid.columns[8].filters[0].term,
          "us.username" : grid.columns[9].filters[0].term, 
          'fp.descripcion_fp' : grid.columns[10].filters[0].term, 
          'se.descripcion_se' : grid.columns[11].filters[0].term, 
          've.moneda' : grid.columns[12].filters[0].term, 
          've.subtotal' : grid.columns[13].filters[0].term, 
          've.igv' : grid.columns[14].filters[0].term, 
          've.total' : grid.columns[15].filters[0].term 
        } 
        $scope.metodos.getPaginationServerSideVEDet(); 
      });
    }
  };
  paginationOptionsVEDet.sortName = $scope.gridOptionsVEDet.columnDefs[2].name; 
  $scope.metodos.getPaginationServerSideVEDet = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptionsVEDet,
      datos: $scope.fBusqueda 
    };
    VentaServices.sListarHistorialDetalleVenta(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsVEDet.totalItems = rpta.paginate.totalRows;
      $scope.gridOptionsVEDet.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSideVEDet(true);   
  // $scope.btnImprimir = function() { 
  //   console.log($scope.mySelectionGrid[0],'$scope.mySelectionGrid[0]');
  //   var arrParams = { 
  //     titulo: 'VISTA PREVIA DE COTIZACIÓN',
  //     datos:{
  //       id: $scope.mySelectionGrid[0].idcotizacion,
  //       codigo_reporte: 'COT-FCOT'
  //     },
  //     envio_correo: 'si',
  //     salida: 'pdf',
  //     url: angular.patchURLCI + "NotaPedido/imprimir_cotizacion" 
  //   }
  //   ModalReporteFactory.getPopupReporte(arrParams);
  // }
}]); 