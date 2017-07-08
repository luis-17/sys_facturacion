app.controller('PersonaJuridicaCtrl', ['$scope', '$filter', '$modal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
		'ClienteEmpresaServices',
		// 'CategoriaClienteServices',
		function($scope, $filter, $modal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
		ClienteEmpresaServices
		// CategoriaClienteServices
	) {
	    $scope.fData = {}; 
	    var paginationOptions = {
	        pageNumber: 1,
	        firstRow: 0,
	        pageSize: 10,
	        sort: uiGridConstants.DESC,
	        sortName: null,
	        search: null
	    };
	    $scope.mySelectionGrid = [];
	    $scope.btnToggleFiltering = function(){
		   $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
		   $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
			};
			$scope.navegateToCell = function( rowIndex, colIndex ) {
			   $scope.gridApi.cellNav.scrollToFocus( $scope.gridOptions.data[rowIndex], $scope.gridOptions.columnDefs[colIndex]);
			}; 
		  $scope.gridOptions = {
		    rowHeight: 30,
		    paginationPageSizes: [10, 50, 100, 500, 1000],
		    paginationPageSize: 10,
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
		      { field: 'id', name: 'idclienteempresa', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
		      { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 140 },
		      { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 160 },
		      { field: 'ruc', name: 'ruc', displayName: 'RUC', minWidth: 100 },
		      { field: 'representante_legal', name: 'representante_legal', displayName: 'Rep. Legal', minWidth: 180 },
		      { field: 'direccion_legal', name: 'direccion_legal', displayName: 'Dirección Legal', minWidth: 180 },
		      { field: 'telefono', name: 'telefono', displayName: 'Teléfono', minWidth: 90 },
		      { field: 'categoria', name: 'categoria', displayName: 'Categoria', minWidth: 100, enableColumnMenus: false, enableColumnMenu: false, 
		          cellTemplate:'<div class="ui-grid-cell-contents">'+
		            '<label class="label bg-dark">{{ COL_FIELD }}</label></div>'
		      }
		    ],
		    onRegisterApi: function(gridApi) { // gridComboOptions
		      $scope.gridApi = gridApi;
		      gridApi.selection.on.rowSelectionChanged($scope,function(row){
		        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
		      });
		      gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
		        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
		      });

		      $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
		        //console.log(sortColumns);
		        if (sortColumns.length == 0) {
		          paginationOptions.sort = null;
		          paginationOptions.sortName = null;
		        } else {
		          paginationOptions.sort = sortColumns[0].sort.direction;
		          paginationOptions.sortName = sortColumns[0].name;
		        }
		        $scope.getPaginationServerSide();
		      });
		      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
		        paginationOptions.pageNumber = newPage;
		        paginationOptions.pageSize = pageSize;
		        paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
		        $scope.getPaginationServerSide();
		      });
		      $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
		        var grid = this.grid;
		        paginationOptions.search = true; 
		        paginationOptions.searchColumn = {
		          'ce.idclienteempresa' : grid.columns[1].filters[0].term,
		          'ce.nombre_comercial' : grid.columns[2].filters[0].term,
		          'ce.razon_social' : grid.columns[3].filters[0].term,
		          'ce.ruc' : grid.columns[4].filters[0].term,
		          'ce.representante_legal' : grid.columns[5].filters[0].term,
		          'ce.direccion_legal' : grid.columns[6].filters[0].term,
		          'ce.telefono' : grid.columns[7].filters[0].term,
		          'cc.descripcion_cc' : grid.columns[8].filters[0].term 
		        }
		        $scope.getPaginationServerSide();
		      });
		    }
			};
			paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name;
			$scope.getPaginationServerSide = function() {
			  //$scope.$parent.blockUI.start();
			  var arrParams = {
			    paginate : paginationOptions
			  };
			  ClienteEmpresaServices.sListar(arrParams).then(function (rpta) {
			    // console.log(rpta);
			    $scope.gridOptions.totalItems = rpta.paginate.totalRows;
			    $scope.gridOptions.data = rpta.datos;
			  });
			  $scope.mySelectionGrid = [];
			};
			$scope.getPaginationServerSide();
	}]);

app.service("ClienteEmpresaServices",function($http, $q) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/lista_cliente_empresa",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/registrar",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/editar",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/anular",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });