app.controller('ProductoCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
	'ProductoFactory',
	'ProductoServices',
	'UnidadMedidaServices',
	'CategoriaElementoServices', 
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
	ProductoFactory,
	ProductoServices,
	UnidadMedidaServices,
	CategoriaElementoServices
	) {
		$scope.metodos = {}; // contiene todas las funciones 
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  	$scope.mySelectionGrid = [];
	  $scope.btnBuscar = function(){ 
		  $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
		  $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
		};
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

		var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 100,
      sort: uiGridConstants.DESC,
      sortName: null,
      search: null
  	};
  	$scope.gridOptions = {
	    rowHeight: 30,
	    paginationPageSizes: [100, 500, 1000],
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
	      { field: 'id', name: 'el.idelemento', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
	      { field: 'descripcion_ele', name: 'el.descripcion_ele', displayName: 'Producto', minWidth: 160 },
	      { field: 'unidad_medida', type: 'object', name: 'um.descripcion_um', displayName: 'Unidad Medida Ref.', minWidth: 100,
	      	cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
	            '{{ COL_FIELD.descripcion }}</div>' 
	      },
	      { field: 'precio_referencial', name: 'el.precio_referencial', displayName: 'Precio Ref.', minWidth: 100 },
	      { field: 'categoria_elemento', type: 'object', name: 'cael.descripcion_cael', displayName: 'Categoria', minWidth: 80, enableColumnMenus: false, enableColumnMenu: false, 
	          cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
	            '<label class="label bg-primary block" style="background-color:{{COL_FIELD.color}}">{{ COL_FIELD.descripcion }}</label></div>' 
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
	          'el.idelemento' : grid.columns[1].filters[0].term,
	          'el.descripcion_ele' : grid.columns[2].filters[0].term,
	          'um.descripcion_um' : grid.columns[3].filters[0].term,
	          'el.precio_referencial' : grid.columns[4].filters[0].term,
	          'cael.descripcion_cael' : grid.columns[5].filters[0].term 
	        }
	        $scope.metodos.getPaginationServerSide();
	      });
	    }
		};
		paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name; 
		$scope.metodos.getPaginationServerSide = function(loader) {
		  if( loader ){
		  	blockUI.start('Procesando información...');
		  }
		  var arrParams = {
		    paginate : paginationOptions
		  };
		  ProductoServices.sListar(arrParams).then(function (rpta) { 
		  	if( rpta.datos.length == 0 ){
		  		rpta.paginate = { totalRows: 0 };
		  	}
		    $scope.gridOptions.totalItems = rpta.paginate.totalRows;
		    $scope.gridOptions.data = rpta.datos; 
		    if( loader ){
		    	blockUI.stop(); 
		    }
		  });
		  $scope.mySelectionGrid = [];
		};
		$scope.metodos.getPaginationServerSide(true); 
		// MAS ACCIONES
		$scope.btnNuevo = function() { 
			var arrParams = {
				'metodos': $scope.metodos,
				'fArr': $scope.fArr 
			}
			ProductoFactory.regProductoModal(arrParams); 
		}
		$scope.btnEditar = function() { 
			var arrParams = {
				'metodos': $scope.metodos,
				'mySelectionGrid': $scope.mySelectionGrid,
				'fArr': $scope.fArr 
			}
			ProductoFactory.editProductoModal(arrParams); 
		}
		$scope.btnAnular = function() { 
	    var pMensaje = '¿Realmente desea anular el registro?';
	    $bootbox.confirm(pMensaje, function(result) {
	      if(result){
	        var arrParams = {
	          id: $scope.mySelectionGrid[0].id 
	        };
	        blockUI.start('Procesando información...');
	        ProductoServices.sAnular(arrParams).then(function (rpta) {
	          if(rpta.flag == 1){
	            var pTitle = 'OK!';
	            var pType = 'success';
	            $scope.metodos.getPaginationServerSide();
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
}]);

app.service("ProductoServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Producto/listar_producto",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Producto/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Producto/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Producto/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("ProductoFactory", function($uibModal, pinesNotifications, blockUI, ProductoServices) { 
	var interfaz = {
		regProductoModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({ 
	      templateUrl: angular.patchURLCI+'Producto/ver_popup_formulario',
	      size: 'md',
	      backdrop: 'static',
	      keyboard:false,
	      controller: function ($scope, $uibModalInstance, arrParams) { 
	      	blockUI.stop(); 
	      	$scope.fData = {};
	      	$scope.metodos = arrParams.metodos;
	      	$scope.fArr = arrParams.fArr;
	      	$scope.titleForm = 'Registro de Producto';
	      	$scope.cancel = function () {
	      	  $uibModalInstance.dismiss('cancel');
	      	}
	      	// BINDEO TIPO DE ELEMENTO 
	      	var objIndex = $scope.fArr.listaTipoElemento.filter(function(obj) { 
            return obj.id == 'P';
          }).shift(); 
          $scope.fData.tipo_elemento = objIndex; 

	      	// BINDEO CATEGORIA DE ELEMENTO 
	      	var myCallBackCC = function() { 
	      		$scope.fArr.listaCategoriasElemento.splice(0,0,{ id : '0', descripcion:'--Seleccione categoría de producto--'}); 
	      		$scope.fData.categoria_elemento = $scope.fArr.listaCategoriasElemento[0]; 
	      	}
	      	$scope.metodos.listaCategoriasElemento(myCallBackCC); 
	      	// BINDEO UNIDAD DE MEDIDA  
	      	var myCallBackCO = function() { 
	      		$scope.fArr.listaUnidadMedida.splice(0,0,{ id : '0', descripcion:'--Seleccione unidad de medida--'}); 
	      		$scope.fData.unidad_medida = $scope.fArr.listaUnidadMedida[0]; 
	      	}
	      	$scope.metodos.listaUnidadMedida(myCallBackCO); 

	      	$scope.aceptar = function () { 
	      		blockUI.start('Procesando información...');
	          ProductoServices.sRegistrar($scope.fData).then(function (rpta) {
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
	      },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
	    });
		},
		editProductoModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({ 
	      templateUrl: angular.patchURLCI+'Producto/ver_popup_formulario',
	      size: 'md',
	      backdrop: 'static',
	      keyboard:false,
	      controller: function ($scope, $uibModalInstance, arrParams) { 
	      	blockUI.stop(); 
	      	$scope.fData = {};
	      	$scope.metodos = arrParams.metodos;
	      	$scope.fArr = arrParams.fArr; 
	      	if( arrParams.mySelectionGrid.length == 1 ){ 
	          $scope.fData = arrParams.mySelectionGrid[0];
	        }else{
	          alert('Seleccione una sola fila');
	        }
	      	$scope.titleForm = 'Edición de Producto';
	      	$scope.cancel = function () {
	      	  $uibModalInstance.dismiss('cancel');
	      	}
	      	// BINDEO TIPO DE ELEMENTO 
	      	var objIndex = $scope.fArr.listaTipoElemento.filter(function(obj) { 
            return obj.id == $scope.fData.tipo_elemento.id;
          }).shift(); 
          $scope.fData.tipo_elemento = objIndex; 

	      	// BINDEO CATEGORIA DE ELEMENTO 
	      	var myCallBackCC = function() { 
	      		var objIndex = $scope.fArr.listaCategoriasElemento.filter(function(obj) { 
	            return obj.id == $scope.fData.categoria_elemento.id;
	          }).shift(); 
	      		$scope.fData.categoria_elemento = objIndex; 
	      	}
	      	$scope.metodos.listaCategoriasElemento(myCallBackCC); 

	      	// BINDEO UNIDAD DE MEDIDA  
	      	var myCallBackCO = function() { 
	      		$scope.fArr.listaUnidadMedida.splice(0,0,{ id : '0', descripcion:'--Seleccione vendedor--'}); 
	          var objIndex = $scope.fArr.listaUnidadMedida.filter(function(obj) { 
	            return obj.id == $scope.fData.unidad_medida.id;
	          }).shift(); 
	          $scope.fData.unidad_medida = objIndex; 
	          if( angular.isUndefined(objIndex)){ 
	            $scope.fData.unidad_medida = $scope.fArr.listaUnidadMedida[0]; 
	          }
	        } 
	        $scope.metodos.listaUnidadMedida(myCallBackCO); 
	      	$scope.aceptar = function () { 
	      		blockUI.start('Procesando información...');
	          ProductoServices.sEditar($scope.fData).then(function (rpta) {
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
	      },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
	    });
		}
	}
	return interfaz;
})