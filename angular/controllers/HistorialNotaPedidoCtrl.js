app.controller('HistorialNotaPedidoCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'NotaPedidoServices',
    'SedeServices',
    'CategoriaElementoServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		NotaPedidoServices,
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

  $scope.btnBuscar = function(){ 
    $scope.gridOptionsNP.enableFiltering = !$scope.gridOptionsNP.enableFiltering;
    $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };
  $scope.btnBuscarNPDet = function(){ 
    $scope.gridOptionsNPDet.enableFiltering = !$scope.gridOptionsNPDet.enableFiltering;
    $scope.gridApiNPDet.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
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
      { field: 'num_nota_pedido', name: 'np.num_nota_pedido', displayName: 'COD. NOTA PEDIDO', width: '120',  sort: { direction: uiGridConstants.DESC}  },
      { field: 'fecha_emision', name: 'np.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false},
      { field: 'fecha_registro', name: 'np.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
      { field: 'colaborador_cot', name: 'colaborador_cot', displayName: 'Asesor de Venta', minWidth: 160 },
      { field: 'colaborador', name: 'colaborador', displayName: 'Generado por:', minWidth: 160, visible: false },
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
          "us.username" : grid.columns[8].filters[0].term, 
          'fp.descripcion_fp' : grid.columns[9].filters[0].term, 
          'se.descripcion_se' : grid.columns[10].filters[0].term,
          'np.moneda' : grid.columns[11].filters[0].term,
          'np.subtotal' : grid.columns[12].filters[0].term,
          'np.igv' : grid.columns[13].filters[0].term,
          'np.total' : grid.columns[14].filters[0].term
        }
        $scope.metodos.getPaginationServerSide();
      });
    }
  };
  paginationOptions.sortName = $scope.gridOptionsNP.columnDefs[1].name; 
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
  
  // ** grid detalle nota pedido
  var paginationOptionsNPDet = { 
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsNPDet = {
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
      { field: 'iddetallemovimiento', name: 'npd.iddetallemovimiento', displayName: 'ID', width: '75', visible: false },
      { field: 'num_nota_pedido', name: 'np.num_nota_pedido', displayName: 'COD. NOTA PEDIDO', width: '120' },
      { field: 'fecha_emision', name: 'np.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false,  sort: { direction: uiGridConstants.DESC} },
      { field: 'fecha_registro', name: 'np.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 240 },  
      { field: 'usuario', name: 'us.username', displayName: 'Usuario', minWidth: 160, visible: false },
      { field: 'sede', name: 'se.descripcion_se', displayName: 'Sede', minWidth: 105 },
      { field: 'categoria_elemento', type: 'object', name: 'cael.descripcion_cael', displayName: 'Categoria Elemento', minWidth: 160, enableColumnMenus: false, visible: false, 
        enableColumnMenu: false,cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+'<label class="label bg-primary block" style="background-color:{{COL_FIELD.color}}">{{ COL_FIELD.descripcion }}</label></div>' 
      },  
      { field: 'elemento', name: 'ele.descripcion_ele', displayName: 'Elemento', minWidth: 220 },          
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
      $scope.gridApiNPDet = gridApi;
      gridApi.selection.on.rowSelectionChanged($scope,function(row){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
      });
      $scope.gridApiNPDet.core.on.sortChanged($scope, function(grid, sortColumns) { 
        if (sortColumns.length == 0) {
          paginationOptionsNPDet.sort = null;
          paginationOptionsNPDet.sortName = null;
        } else {
          paginationOptionsNPDet.sort = sortColumns[0].sort.direction;
          paginationOptionsNPDet.sortName = sortColumns[0].name;
        }
        $scope.metodos.getPaginationServerSideNPDet(true);
      });
      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
        paginationOptionsNPDet.pageNumber = newPage;
        paginationOptionsNPDet.pageSize = pageSize;
        paginationOptionsNPDet.firstRow = (paginationOptionsNPDet.pageNumber - 1) * paginationOptionsNPDet.pageSize;
        $scope.metodos.getPaginationServerSideNPDet(true);
      });
      $scope.gridApiNPDet.core.on.filterChanged( $scope, function(grid, searchColumns) {
        var grid = this.grid;
        paginationOptionsNPDet.search = true; 
        paginationOptionsNPDet.searchColumn = {
          'npd.iddetallemovimiento' : grid.columns[1].filters[0].term,
          'np.num_nota_pedido' : grid.columns[2].filters[0].term,
          "CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))" : grid.columns[5].filters[0].term,
          'se.descripcion_se' : grid.columns[7].filters[0].term, 
          'cael.descripcion_cael' : grid.columns[8].filters[0].term, 
          'ele.descripcion_ele' : grid.columns[9].filters[0].term,
          'npd.precio_unitario' : grid.columns[10].filters[0].term,
          'npd.cantidad' : grid.columns[11].filters[0].term,
          'npd.importe_con_igv' : grid.columns[12].filters[0].term
        }
        $scope.metodos.getPaginationServerSideNPDet();
      });
    }
  };
  paginationOptionsNPDet.sortName = $scope.gridOptionsNPDet.columnDefs[2].name; 
  $scope.metodos.getPaginationServerSideNPDet = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptionsNPDet,
      datos: $scope.fBusqueda 
    };
    NotaPedidoServices.sListarHistorialDetalleNotaPedidos(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsNPDet.totalItems = rpta.paginate.totalRows;
      $scope.gridOptionsNPDet.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSideNPDet(true); 
  $scope.btnImprimir = function() {
    // console.log($scope.fData,'$scope.fData');
    var arrParams = { 
      titulo: 'NOTA DE PEDIDO',
      datos:{
        id: $scope.mySelectionGrid[0].idmovimiento,
        codigo_reporte: 'NP-FNOTPED'
      },
      envio_correo: 'si',
      salida: 'pdf',
      url: angular.patchURLCI + "NotaPedido/imprimir_nota_pedido" 
    }
    ModalReporteFactory.getPopupReporte(arrParams);
  }
  $scope.btnAnular = function() {
    var pMensaje = '¿Realmente desea anular la nota de pedido?';
    $bootbox.confirm(pMensaje, function(result) { 
      if(result){
        var arrParams = { 
          idnotapedido: $scope.mySelectionGrid[0].idmovimiento 
        };
        blockUI.start('Procesando información...');
        NotaPedidoServices.sAnular(arrParams).then(function (rpta) {
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
  $scope.btnEditar = function() {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'NotaPedido/ver_popup_editar_nota_pedido',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        scope: $scope,
        controller: function ($scope, $uibModalInstance) { 
          blockUI.stop(); 
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fData = $scope.mySelectionGrid[0];
              console.log($scope.fData,'$scope.fData');
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Editar Nota pedido';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }     
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            NotaPedidoServices.sEditar($scope.fData).then(function (rpta) {
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
        }
      });
  }
}]); 