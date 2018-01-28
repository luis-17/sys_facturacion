app.controller('HistorialGuiaRemisionCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'GuiaRemisionServices',
    //'SedeServices',
    //'CategoriaElementoServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		GuiaRemisionServices
    //SedeServices
    //CategoriaElementoServices
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
  // $scope.metodos.listaSedes = function(myCallback) { 
  //   var myCallback = myCallback || function() { };
  //   SedeServices.sListarCbo().then(function(rpta) { 
  //     if( rpta.flag == 1){
  //       $scope.fArr.listaSedes = rpta.datos; 
  //       myCallback();
  //     } 
  //   });
  // }
  // var myCallback = function() { 
  //   $scope.fArr.listaSedes.splice(0,0,{ id : 'ALL', descripcion:'--TODOS--'}); 
  //   $scope.fBusqueda.sede = $scope.fArr.listaSedes[0]; 
  // }
  // $scope.metodos.listaSedes(myCallback); 

  // CATEGORIA ELEMENTO
  // $scope.metodos.listaCategoriasElemento = function(myCallback) {
  //   var myCallback = myCallback || function() { };
  //   CategoriaElementoServices.sListarCbo().then(function(rpta) {
  //     $scope.fArr.listaCategoriasElemento = rpta.datos;
  //     myCallbackElemento();
  //   });
  // };

  // var myCallbackElemento = function() { 
  //   $scope.fArr.listaCategoriasElemento.splice(0,0,{ id : 'ALL', descripcion:'--TODOS--'}); 
  //   $scope.fBusqueda.categoria_elemento = $scope.fArr.listaCategoriasElemento[0]; 
  // }
  // $scope.metodos.listaCategoriasElemento(myCallbackElemento); 
  
  // ESTADO DE COTIZACION 
  $scope.fArr.listaEstadosGuiaRemision = [
    {'id' : 'ALL', 'descripcion' : '--TODOS--'},
    {'id' : 0, 'descripcion' : 'ANULADO'},
    {'id' : 1, 'descripcion' : 'REGISTRADO'}
  ]; 
  $scope.fBusqueda.estado_guia_remision = $scope.fArr.listaEstadosGuiaRemision[0]; 

  $scope.tabs = [true, false];
  $scope.tab = function(index){
    angular.forEach($scope.tabs, function(i, v) {
      $scope.tabs[v] = false;
    });
    $scope.tabs[index] = true;
  }

  $scope.btnBuscar = function(){ 
    $scope.gridOptionsGR.enableFiltering = !$scope.gridOptionsGR.enableFiltering;
    $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };

  $scope.btnBuscarGRDet = function(){ 
    $scope.gridOptionsGRDet.enableFiltering = !$scope.gridOptionsGRDet.enableFiltering;
    $scope.gridApiGRDet.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };

  var paginationOptions = { 
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsGR = {
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
      { field: 'idguiaremision', name: 'gr.idguiaremision', displayName: 'ID', width: '75', visible: false,  sort: { direction: uiGridConstants.DESC} },
      { field: 'serie', name: 'gr.numero_serie', displayName: 'SERIE', width: 60 },
      { field: 'correlativo', name: 'gr.numero_correlativo', displayName: 'CORRELATIVO', width: 100 },
      { field: 'fecha_emision', name: 'gr.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false },
      { field: 'fecha_inicio_traslado', name: 'gr.fecha_inicio_traslado', displayName: 'F. Inicio Traslado', minWidth: 100, enableFiltering: false },
      //{ field: 'fecha_registro', name: 'gr.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
      { field: 'colaborador_asig', name: 'colaborador_asig', displayName: 'Asesor de Venta', minWidth: 160 },
      { field: 'colaborador_gen', name: 'colaborador_gen', displayName: 'Generado por:', minWidth: 160, visible: false },
      { field: 'motivo_traslado', name: 'mt.descripcion_mt', displayName: 'Motivo de Traslado', minWidth: 160 },
      { field: 'punto_partida', name: 'gr.punto_partida', displayName: 'Punto de Partida', minWidth: 160 },
      { field: 'punto_llegada', name: 'gr.punto_llegada', displayName: 'Punto de Llegada', minWidth: 160 },
      { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: '120', enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
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
          'gr.idguiaremision' : grid.columns[1].filters[0].term,
          'gr.numero_serie' : grid.columns[2].filters[0].term,
          'gr.numero_correlativo' : grid.columns[3].filters[0].term,
          "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[6].filters[0].term,
          "CONCAT(col_asig.nombres, ' ', col_asig.apellidos)" : grid.columns[7].filters[0].term,
          "CONCAT(col.nombres, ' ', col.apellidos)" : grid.columns[8].filters[0].term,
          'mt.descripcion_mt' : grid.columns[9].filters[0].term, 
          'gr.punto_partida' : grid.columns[10].filters[0].term, 
          'gr.punto_llegada' : grid.columns[11].filters[0].term
        }
        $scope.metodos.getPaginationServerSide();
      });
    }
  };
  paginationOptions.sortName = $scope.gridOptionsGR.columnDefs[0].name; 
  $scope.metodos.getPaginationServerSide = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptions,
      datos: $scope.fBusqueda 
    };
    GuiaRemisionServices.sListarHistorialGuiaRemision(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsGR.totalItems = rpta.paginate.totalRows;
      $scope.gridOptionsGR.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSide(true); 
  $scope.btnImprimirHTML = function() { 
    var arrParams = {
      id: $scope.mySelectionGrid[0].idguiaremision, 
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
  $scope.btnAnular = function() {
    var pMensaje = '¿Realmente desea anular la guia de remisión?';
    $bootbox.confirm(pMensaje, function(result) { 
      if(result){
        var arrParams = { 
          idguiaremision: $scope.mySelectionGrid[0].idguiaremision 
        };
        blockUI.start('Procesando información...');
        GuiaRemisionServices.sAnular(arrParams).then(function (rpta) {
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
  //***grid detalle guia remision 
  var paginationOptionsDet = { 
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsGRDet = {
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
      $scope.gridApiGRDet = gridApi;
      gridApi.selection.on.rowSelectionChanged($scope,function(row){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      $scope.gridApiGRDet.core.on.sortChanged($scope, function(grid, sortColumns) { 
        if (sortColumns.length == 0) {
          paginationOptionsDet.sort = null;
          paginationOptionsDet.sortName = null;
        } else {
          paginationOptionsDet.sort = sortColumns[0].sort.direction;
          paginationOptionsDet.sortName = sortColumns[0].name;
        }
        $scope.metodos.getPaginationServerSideGRDet(true);
      });
      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
        paginationOptionsDet.pageNumber = newPage;
        paginationOptionsDet.pageSize = pageSize;
        paginationOptionsDet.firstRow = (paginationOptionsDet.pageNumber - 1) * paginationOptionsDet.pageSize;
        $scope.metodos.getPaginationServerSideGRDet(true);
      });
      $scope.gridApiGRDet.core.on.filterChanged( $scope, function(grid, searchColumns) {
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
        $scope.metodos.getPaginationServerSideGRDet();
      });
    }
  };
  paginationOptionsDet.sortName = $scope.gridOptionsGRDet.columnDefs[1].name; 
  $scope.metodos.getPaginationServerSideGRDet = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptionsDet,
      datos: $scope.fBusqueda  
    };
    GuiaRemisionServices.sListarHistorialDetalleGuiaRemision(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsGRDet.totalItems = rpta.paginate.totalRows;
      $scope.gridOptionsGRDet.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSideGRDet(true); 

}]); 