app.controller('HistorialNotaPedidoCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'NotaPedidoServices',
    'SedeServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		NotaPedidoServices,
    SedeServices
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

  // ESTADO DE NOTA DE PEDIDO  
  $scope.fArr.listaEstadosNP = [
    {'id' : 'ALL', 'descripcion' : '--TODOS--'},
    {'id' : 1, 'descripcion' : 'REGISTRADO'},
    {'id' : 2, 'descripcion' : 'FACTURADO'}
  ]; 
  $scope.fBusqueda.estado_np = $scope.fArr.listaEstadosNP[0]; 

  $scope.tabs = [true, false];
  $scope.tab = function(index){
    angular.forEach($scope.tabs, function(i, v) {
      $scope.tabs[v] = false;
    });
    $scope.tabs[index] = true;
  }

  $scope.btnBuscar = function(){ 
    $scope.gridOptionsNP.enableFiltering = !$scope.gridOptionsNP.enableFiltering;
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
    enableFiltering: false,
    enableFullRowSelection: true,
    multiSelect: false,
    columnDefs: [ 
      { field: 'idmovimiento', name: 'np.idmovimiento', displayName: 'ID', width: '75', visible: false },
      { field: 'num_nota_pedido', name: 'np.num_nota_pedido', displayName: 'COD. NOTA PEDIDO', width: '120' },
      { field: 'fecha_emision', name: 'np.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false,  sort: { direction: uiGridConstants.DESC} },
      { field: 'fecha_registro', name: 'np.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
      { field: 'colaborador', name: 'colaborador', displayName: 'Colaborador', minWidth: 160 },
      { field: 'usuario', name: 'us.username', displayName: 'Usuario', minWidth: 160, visible: false },
      { field: 'forma_pago', name: 'fp.descripcion_fp', displayName: 'Forma de Pago', minWidth: 120 },
      { field: 'sede', name: 'se.descripcion_se', displayName: 'Sede', minWidth: 105 },
      { field: 'moneda', name: 'np.moneda', displayName: 'Moneda', minWidth: 76, enableFiltering: false },
      { field: 'subtotal', name: 'np.subtotal', displayName: 'Subtotal', minWidth: 90 },
      { field: 'igv', name: 'np.igv', displayName: 'IGV', minWidth: 80 },
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
        $scope.metodos.getPaginationServerSide();
      });
    }
  };
  paginationOptions.sortName = $scope.gridOptionsNP.columnDefs[2].name; 
  $scope.metodos.getPaginationServerSide = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptions,
      datos: $scope.fBusqueda 
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
  $scope.metodos.getPaginationServerSide(true); 
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