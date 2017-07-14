app.controller('PersonaNaturalCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
	'ClientePersonaServices',
	'CategoriaClienteServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
	ClientePersonaServices,
	CategoriaClienteServices
) {
  // $scope.fData = {}; 
  $scope.metodos = {};
	$scope.fArr = {};
  
  $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){
	  $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
	  $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
	};
	$scope.metodos.listaCategoriasCliente = function(myCallback) {
		var myCallback = myCallback || function() { };
		CategoriaClienteServices.sListarCbo().then(function(rpta) {
			$scope.fArr.listaCategoriaCliente = rpta.datos; 
			myCallback();
		});
	};
  $scope.fArr.listaSexo = [ 
    { id:'M', descripcion:'MASCULINO' },
    { id:'F', descripcion:'FEMENINO' }
  ]; 
	var paginationOptions = { 
      pageNumber: 1,
      firstRow: 0,
      pageSize: 10,
      sort: uiGridConstants.DESC,
      sortName: null,
      search: null
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
	      { field: 'id', name: 'idclientepersona', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
	      { field: 'nombres', name: 'nombres', displayName: 'Nombres', minWidth: 140 },
	      { field: 'apellidos', name: 'apellidos', displayName: 'Apellidos', minWidth: 140 },
	      { field: 'num_documento', name: 'num_documento', displayName: 'N° Documento', minWidth: 100 },
	      { field: 'edad', name: 'fecha_nacimiento', displayName: 'Edad', minWidth: 90, enableFiltering: false },
	      { field: 'telefono_fijo', name: 'telefono_fijo', displayName: 'Telefono Fijo', minWidth: 120 },
	      { field: 'telefono_movil', name: 'telefono_movil', displayName: 'Teléfono Movil', minWidth: 120 },
	      { field: 'email', name: 'email', displayName: 'Correo', minWidth: 180 },
	      { field: 'categoria_cliente', type: 'object', name: 'categoria_cliente', displayName: 'Categoria', minWidth: 100, enableColumnMenus: false, enableColumnMenu: false, 
	          cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
	            '<label class="label bg-primary block">{{ COL_FIELD.descripcion }}</label></div>' 
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
	        $scope.getPaginationServerSide(true);
	      });
	      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
	        paginationOptions.pageNumber = newPage;
	        paginationOptions.pageSize = pageSize;
	        paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
	        $scope.getPaginationServerSide(true);
	      });
	      $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
	        var grid = this.grid;
	        paginationOptions.search = true; 
	        paginationOptions.searchColumn = {
	          'ce.idclientepersona' : grid.columns[1].filters[0].term,
	          'ce.nombres' : grid.columns[2].filters[0].term,
	          'ce.apellidos' : grid.columns[3].filters[0].term,
	          'ce.num_documento' : grid.columns[4].filters[0].term,
	          'ce.telefono_fijo' : grid.columns[6].filters[0].term,
	          'ce.telefono_movil' : grid.columns[7].filters[0].term,
	          'ce.email' : grid.columns[8].filters[0].term,
	          'cc.descripcion_cc' : grid.columns[9].filters[0].term 
	        }
	        $scope.getPaginationServerSide();
	      });
	    }
	};
	paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name;
	$scope.getPaginationServerSide = function(loader) {
	  if( loader ){
	  	blockUI.start('Procesando información...');
	  }
	  var arrParams = {
	    paginate : paginationOptions
	  };
	  ClientePersonaServices.sListar(arrParams).then(function (rpta) { 
	    $scope.gridOptions.totalItems = rpta.paginate.totalRows;
	    $scope.gridOptions.data = rpta.datos; 
	    if( loader ){
	    	blockUI.stop(); 
	    }
	  });
	  $scope.mySelectionGrid = [];
	};
	$scope.getPaginationServerSide(true); 
	// MAS ACCIONES
	$scope.btnNuevo = function() { 
		blockUI.start('Abriendo formulario...');
		$uibModal.open({ 
      templateUrl: angular.patchURLCI+'ClientePersona/ver_popup_formulario',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
      	blockUI.stop(); 
      	$scope.fData = {};
      	$scope.titleForm = 'Registro de Cliente - Persona Natural';
      	$scope.cancel = function () {
      	  $uibModalInstance.dismiss('cancel');
      	}
        $scope.fArr.listaSexo.splice(0,0,{ id : '0', descripcion:'--Seleccione sexo--'}); 
        $scope.fData.sexo = $scope.fArr.listaSexo[0]; 
      	var myCallBack = function() { 
      		$scope.fArr.listaCategoriaCliente.splice(0,0,{ id : '0', descripcion:'--Seleccione la categoría de cliente--'}); 
      		$scope.fData.categoria_cliente = $scope.fArr.listaCategoriaCliente[0]; 
      	}
      	$scope.metodos.listaCategoriasCliente(myCallBack); 
      	$scope.aceptar = function () { 
      		blockUI.start('Procesando información...');
          ClientePersonaServices.sRegistrar($scope.fData).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              $uibModalInstance.dismiss('cancel');
              $scope.getPaginationServerSide(true);
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
	$scope.btnEditar = function() { 
		blockUI.start('Abriendo formulario...');
		$uibModal.open({ 
      templateUrl: angular.patchURLCI+'ClientePersona/ver_popup_formulario',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
      	blockUI.stop(); 
      	$scope.fData = {};
      	if( $scope.mySelectionGrid.length == 1 ){ 
          $scope.fData = $scope.mySelectionGrid[0];
        }else{
          alert('Seleccione una sola fila');
        }
      	$scope.titleForm = 'Edición de Cliente - Persona Natural';
      	$scope.cancel = function () {
      	  $uibModalInstance.dismiss('cancel');
      	}
        // BINDEO CATEGORIA CLIENTE 
      	var myCallBack = function() { 
      		var objIndex = $scope.fArr.listaCategoriaCliente.filter(function(obj) { 
            return obj.id == $scope.fData.categoria_cliente.id;
          }).shift(); 
      		$scope.fData.categoria_cliente = objIndex; 
      	}
      	$scope.metodos.listaCategoriasCliente(myCallBack); 

        // BINDEO SEXO 
        var objIndex = $scope.fArr.listaSexo.filter(function(obj) { 
          return obj.id == $scope.fData.sexo.id;
        }).shift(); 
        $scope.fData.sexo = objIndex; 

      	$scope.aceptar = function () { 
      		blockUI.start('Procesando información...');
          ClientePersonaServices.sEditar($scope.fData).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              $uibModalInstance.dismiss('cancel');
              $scope.getPaginationServerSide(true);
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
  $scope.btnAnular = function() { 
    var pMensaje = '¿Realmente desea anular el registro?';
    $bootbox.confirm(pMensaje, function(result) {
      if(result){
        var arrParams = {
          id: $scope.mySelectionGrid[0].id  
        };
        blockUI.start('Procesando información...');
        ClientePersonaServices.sAnular(arrParams).then(function (rpta) {
          if(rpta.flag == 1){
            var pTitle = 'OK!';
            var pType = 'success';
            $scope.getPaginationServerSide();
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

app.service("ClientePersonaServices",function($http, $q) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClientePersona/listar_cliente_persona",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClientePersona/registrar",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClientePersona/editar",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClientePersona/anular",
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
});