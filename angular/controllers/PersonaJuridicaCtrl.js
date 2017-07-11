app.controller('PersonaJuridicaCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
	'ClienteEmpresaServices',
	'CategoriaClienteServices', 
	'ContactoEmpresaServices', 
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
	ClienteEmpresaServices,
	CategoriaClienteServices,
	ContactoEmpresaServices
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
	      { field: 'id', name: 'idclienteempresa', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
	      { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 140 },
	      { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 160 },
	      { field: 'ruc', name: 'ruc', displayName: 'RUC', minWidth: 100 },
	      { field: 'representante_legal', name: 'representante_legal', displayName: 'Rep. Legal', minWidth: 180 },
	      { field: 'direccion_legal', name: 'direccion_legal', displayName: 'Dirección Legal', minWidth: 180 },
	      { field: 'telefono', name: 'telefono', displayName: 'Teléfono', minWidth: 90 },
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
	        //console.log(sortColumns);
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
	$scope.getPaginationServerSide = function(loader) {
	  if( loader ){
	  	blockUI.start('Procesando información...');
	  }
	  var arrParams = {
	    paginate : paginationOptions
	  };
	  ClienteEmpresaServices.sListar(arrParams).then(function (rpta) { 
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
      templateUrl: angular.patchURLCI+'ClienteEmpresa/ver_popup_formulario',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
      	blockUI.stop(); 
      	$scope.fData = {};
      	$scope.titleForm = 'Registro de Cliente Empresa';
      	$scope.cancel = function () {
      	  $uibModalInstance.dismiss('cancel');
      	}
      	var myCallBack = function() { 
      		$scope.fArr.listaCategoriaCliente.splice(0,0,{ id : '0', descripcion:'--Seleccione la categoría de cliente--'}); 
      		$scope.fData.categoria_cliente = $scope.fArr.listaCategoriaCliente[0]; 
      	}
      	$scope.metodos.listaCategoriasCliente(myCallBack); 
      	$scope.aceptar = function () { 
      		blockUI.start('Procesando información...');
          ClienteEmpresaServices.sRegistrar($scope.fData).then(function (rpta) {
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
      templateUrl: angular.patchURLCI+'ClienteEmpresa/ver_popup_formulario',
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
      	$scope.titleForm = 'Edición de Cliente Empresa';
      	$scope.cancel = function () {
      	  $uibModalInstance.dismiss('cancel');
      	}
      	var myCallBack = function() { 
      		var objIndex = $scope.fArr.listaCategoriaCliente.filter(function(obj) { 
            return obj.id == $scope.fData.categoria_cliente.id;
          }).shift(); 
      		$scope.fData.categoria_cliente = objIndex; 
      	}
      	$scope.metodos.listaCategoriasCliente(myCallBack); 
      	$scope.aceptar = function () { 
      		blockUI.start('Procesando información...');
          ClienteEmpresaServices.sEditar($scope.fData).then(function (rpta) {
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
	$scope.btnContactos = function() { 
		blockUI.start('Abriendo formulario...');
		$uibModal.open({ 
      templateUrl: angular.patchURLCI+'ClienteEmpresa/ver_popup_contactos',
      size: 'lg',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
      	blockUI.stop(); 
      	$scope.fData = {};
      	$scope.fContacto = {};
      	$scope.editClassForm = null;
      	$scope.tituloBloque = 'Agregar Contacto';
      	$scope.contBotonesReg = true;
      	$scope.contBotonesEdit = false;
      	if( $scope.mySelectionGrid.length == 1 ){ 
          $scope.fData = $scope.mySelectionGrid[0];
        }else{
          alert('Seleccione una sola fila');
        }
      	$scope.titleForm = 'Contactos';
      	$scope.cancel = function () {
      	  $uibModalInstance.dismiss('cancel');
      	} 
      	$scope.btnBuscarContactos = function(){
				  $scope.gridOptionsContactos.enableFiltering = !$scope.gridOptionsContactos.enableFiltering;
				  $scope.gridApiContacto.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
				};
      	var paginationOptionsContactos = {
		      pageNumber: 1,
		      firstRow: 0,
		      pageSize: 10,
		      sort: uiGridConstants.DESC,
		      sortName: null,
		      search: null
			  };
				$scope.gridOptionsContactos = { 
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
			      { field: 'id', name: 'idcontacto', displayName: 'ID', visible: false, width: '50',  sort: { direction: uiGridConstants.DESC} },
			      { field: 'nombres', name: 'nombres', displayName: 'Contacto', width: 140 },
			      { field: 'telefono_fijo', name: 'telefono_fijo', displayName: 'Tel. Fijo', width: 100 },
			      { field: 'telefono_movil', name: 'telefono_movil', displayName: 'Tel. Movil', width: 100 },
			      { field: 'email', name: 'email', displayName: 'E-mail', width: 120 },
			      { field: 'fecha_nacimiento_str', name: 'fecha_nacimiento_str', displayName: 'Fecha de Nacimiento', width: 100, enableFiltering: false } 
			    ],
			    onRegisterApi: function(gridApiContacto) { 
			      $scope.gridApiContacto = gridApiContacto;
			      gridApiContacto.selection.on.rowSelectionChanged($scope,function(row){
			        $scope.mySelectionGridContacto = gridApiContacto.selection.getSelectedRows(); 
			        // EDICIÓN DE CONTACTO 
				      if( $scope.mySelectionGridContacto.length == 1 ){
				      	$scope.editClassForm = ' edit-form'; 
				      	$scope.tituloBloque = 'Edición de Contacto';
				      	$scope.contBotonesReg = false;
				      	$scope.contBotonesEdit = true;
				      	$scope.fContacto = $scope.mySelectionGridContacto[0];
				      }else{
				      	$scope.editClassForm = null; 
				      	$scope.tituloBloque = 'Agregar Contacto';
				      	$scope.contBotonesReg = true;
				      	$scope.contBotonesEdit = false;
				      }
				      /* END */
			      });
			      gridApiContacto.selection.on.rowSelectionChangedBatch($scope,function(rows){
			        $scope.mySelectionGridContacto = gridApiContacto.selection.getSelectedRows();
			      });

			      $scope.gridApiContacto.core.on.sortChanged($scope, function(grid, sortColumns) { 
			        if (sortColumns.length == 0) {
			          paginationOptionsContactos.sort = null;
			          paginationOptionsContactos.sortName = null;
			        } else {
			          paginationOptionsContactos.sort = sortColumns[0].sort.direction;
			          paginationOptionsContactos.sortName = sortColumns[0].name;
			        }
			        $scope.getPaginationServerSideContactos(true);
			      });
			      gridApiContacto.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
			        paginationOptionsContactos.pageNumber = newPage;
			        paginationOptionsContactos.pageSize = pageSize;
			        paginationOptionsContactos.firstRow = (paginationOptionsContactos.pageNumber - 1) * paginationOptionsContactos.pageSize;
			        $scope.getPaginationServerSideContactos(true);
			      });
			      $scope.gridApiContacto.core.on.filterChanged( $scope, function(grid, searchColumns) {
			        var grid = this.grid;
			        paginationOptionsContactos.search = true; 
			        paginationOptionsContactos.searchColumn = {
			          'co.idcontacto' : grid.columns[1].filters[0].term,
			          'co.nombres' : grid.columns[2].filters[0].term,
			          'co.apellidos' : grid.columns[3].filters[0].term,
			          'co.telefono_fijo' : grid.columns[4].filters[0].term,
			          'co.telefono_movil' : grid.columns[5].filters[0].term,
			          'co.email' : grid.columns[6].filters[0].term 
			        }
			        $scope.getPaginationServerSideContactos();
			      }); 
			    }
				};
				$scope.quitarContacto = function() {
					console.log('click me quitarContactos');
					var pMensaje = '¿Realmente desea anular el registro?';
		      $bootbox.confirm(pMensaje, function(result) {
		        if(result){
		        	var arrParams = {
		        		idcontacto: $scope.fContacto.id 
		        	}
		        	blockUI.start('Procesando información...');
		          ContactoEmpresaServices.sQuitarContacto(arrParams).then(function (rpta) {
		            if(rpta.flag == 1){
		              var pTitle = 'OK!';
		              var pType = 'success';
		              $scope.getPaginationServerSideContactos();
		              $scope.editClassForm = null; 
					      	$scope.tituloBloque = 'Agregar Contacto';
					      	$scope.contBotonesReg = true;
					      	$scope.contBotonesEdit = false;
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
				$scope.actualizarContacto = function() { 
					console.log('click me');
					blockUI.start('Procesando información...'); // ContactoEmpresaServices.sActualizarContacto
          ContactoEmpresaServices.sActualizarContacto($scope.fContacto).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              $scope.fContacto = {};
              $scope.getPaginationServerSideContactos(true); 
              $scope.editClassForm = null; 
			      	$scope.tituloBloque = 'Agregar Contacto';
			      	$scope.contBotonesReg = true;
			      	$scope.contBotonesEdit = false;
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
				paginationOptionsContactos.sortName = $scope.gridOptionsContactos.columnDefs[0].name;
				$scope.getPaginationServerSideContactos = function(loader) {
				  if( loader ){
				  	blockUI.start('Procesando información...');
				  }
				  var arrParams = { 
				    paginate : paginationOptionsContactos,
				    datos: $scope.fData 
				  };
				  ContactoEmpresaServices.sListarContactosDeEstaEmpresa(arrParams).then(function (rpta) { 
				    $scope.gridOptionsContactos.totalItems = rpta.paginate.totalRows;
				    $scope.gridOptionsContactos.data = rpta.datos; 
				    if( loader ){
				    	blockUI.stop(); 
				    }
				  });
				  $scope.mySelectionGridContacto = [];
				};
				$scope.getPaginationServerSideContactos(true); 
      	$scope.agregarContacto = function () { 
      		blockUI.start('Procesando información...');
      		$scope.fContacto.idclienteempresa = $scope.fData.id; 
          ContactoEmpresaServices.sAgregarContacto($scope.fContacto).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              $scope.fContacto = {};
              $scope.getPaginationServerSideContactos(true); 
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
            url : angular.patchURLCI+"ClienteEmpresa/listar_cliente_empresa",
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