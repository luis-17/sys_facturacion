app.controller('ClienteEmpresaCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
	'ClienteEmpresaFactory',
	'ClienteEmpresaServices',
	'CategoriaClienteServices', 
	'ContactoEmpresaServices', 
	'ColaboradorServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
	ClienteEmpresaFactory,
	ClienteEmpresaServices,
	CategoriaClienteServices,
	ContactoEmpresaServices,
	ColaboradorServices
	) {
		$scope.metodos = {}; // contiene todas las funciones 
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  	$scope.mySelectionGrid = [];
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
	      { field: 'id', name: 'idclienteempresa', displayName: 'ID', width: '70',  sort: { direction: uiGridConstants.DESC} },
	      { field: 'nombre_comercial', name: 'nombre_comercial', displayName: 'Nombre Comercial', minWidth: 140 },
	      { field: 'razon_social', name: 'razon_social', displayName: 'Razón Social', minWidth: 160 },
	      { field: 'ruc', name: 'ruc', displayName: 'RUC', minWidth: 80 },
	      { field: 'representante_legal', name: 'representante_legal', displayName: 'Rep. Legal', minWidth: 180, visible:false },
	      { field: 'direccion_legal', name: 'direccion_legal', displayName: 'Dirección Legal', minWidth: 180 },
	      { field: 'primer_contacto', name: 'primer_contacto', displayName: '1er Contacto', minWidth: 130, enableFiltering: false },
	      { field: 'telefono', name: 'telefono', displayName: 'Teléfono', minWidth: 80 },
	      { field: 'colaborador_str', name: 'colaborador', displayName: 'Asesor de Venta', minWidth: 120 },
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
	          'ce.idclienteempresa' : grid.columns[1].filters[0].term,
	          'ce.nombre_comercial' : grid.columns[2].filters[0].term,
	          'ce.razon_social' : grid.columns[3].filters[0].term,
	          'ce.ruc' : grid.columns[4].filters[0].term,
	          'ce.representante_legal' : grid.columns[5].filters[0].term,
	          'ce.direccion_legal' : grid.columns[6].filters[0].term,
	          'ce.telefono' : grid.columns[7].filters[0].term,
	          'co.nombres' : grid.columns[8].filters[0].term,
	          'cc.descripcion_cc' : grid.columns[9].filters[0].term 
	        }
	        $scope.metodos.getPaginationServerSide();
	      });
	    }
		};
		paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name; 
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
		$scope.metodos.listaColaboradores = function(myCallback) { 
			var myCallback = myCallback || function() { };
			ColaboradorServices.sListarCbo().then(function(rpta) {
				$scope.fArr.listaColaboradores = rpta.datos; 
				myCallback();
			});
		};
		$scope.metodos.getPaginationServerSide = function(loader) {
		  if( loader ){
		  	blockUI.start('Procesando información...');
		  }
		  var arrParams = {
		    paginate : paginationOptions
		  };
		  ClienteEmpresaServices.sListar(arrParams).then(function (rpta) { 
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
			ClienteEmpresaFactory.regClienteEmpresaModal(arrParams); 
		}
		$scope.btnEditar = function() { 
			var arrParams = {
				'metodos': $scope.metodos,
				'mySelectionGrid': $scope.mySelectionGrid,
				'fArr': $scope.fArr,
				'fSessionCI': $scope.fSessionCI 
			}
			ClienteEmpresaFactory.editClienteEmpresaModal(arrParams); 
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
				      { field: 'anexo', name: 'anexo', displayName: 'Anexo', width: 75 },
				      { field: 'telefono_movil', name: 'telefono_movil', displayName: 'Tel. Movil', width: 100 },
				      { field: 'email', name: 'email', displayName: 'E-mail', width: 120 },
				      { field: 'fecha_nacimiento_str', name: 'fecha_nacimiento_str', displayName: 'Fecha de Nac.', width: 100, enableFiltering: false, visible: false } 
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
					      	$scope.fContacto = {};
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
				        $scope.metodos.getPaginationServerSideContactos(true);
				      });
				      gridApiContacto.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
				        paginationOptionsContactos.pageNumber = newPage;
				        paginationOptionsContactos.pageSize = pageSize;
				        paginationOptionsContactos.firstRow = (paginationOptionsContactos.pageNumber - 1) * paginationOptionsContactos.pageSize;
				        $scope.metodos.getPaginationServerSideContactos(true);
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
				        $scope.metodos.getPaginationServerSideContactos();
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
				              $scope.metodos.getPaginationServerSideContactos();
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
	              $scope.metodos.getPaginationServerSideContactos(true); 
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
					$scope.metodos.getPaginationServerSideContactos = function(loader) {
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
					$scope.metodos.getPaginationServerSideContactos(true); 
	      	$scope.agregarContacto = function () { 
	      		blockUI.start('Procesando información...');
	      		$scope.fContacto.idclienteempresa = $scope.fData.id; 
	          ContactoEmpresaServices.sAgregarContacto($scope.fContacto).then(function (rpta) {
	            if(rpta.flag == 1){
	              var pTitle = 'OK!';
	              var pType = 'success';
	              $scope.fContacto = {};
	              $scope.metodos.getPaginationServerSideContactos(true); 
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
	        ClienteEmpresaServices.sAnular(arrParams).then(function (rpta) {
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

app.service("ClienteEmpresaServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarPuntosLlegada: sListarPuntosLlegada,
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
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarPuntosLlegada(datos) {
    	var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/listar_puntos_llegada_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"ClienteEmpresa/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("ClienteEmpresaFactory", function($uibModal, pinesNotifications, blockUI, ClienteEmpresaServices) { 
	var interfaz = {
		regClienteEmpresaModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({ 
	      templateUrl: angular.patchURLCI+'ClienteEmpresa/ver_popup_formulario',
	      size: 'md',
	      backdrop: 'static',
	      keyboard:false,
	      controller: function ($scope, $uibModalInstance, arrParams) { 
	      	blockUI.stop(); 
	      	$scope.fData = {};
	      	$scope.metodos = arrParams.metodos;
	      	$scope.fArr = arrParams.fArr;
	      	$scope.titleForm = 'Registro de Cliente - Persona Jurídica';
	      	$scope.cancel = function () {
	      	  $uibModalInstance.dismiss('cancel');
	      	}
	      	var myCallBackCC = function() { 
	      		$scope.fArr.listaCategoriaCliente.splice(0,0,{ id : '0', descripcion:'--Seleccione la categoría de cliente--'}); 
	      		$scope.fData.categoria_cliente = $scope.fArr.listaCategoriaCliente[0]; 
	      	}
	      	$scope.metodos.listaCategoriasCliente(myCallBackCC); 

	      	var myCallBackCO = function() { 
	      		$scope.fArr.listaColaboradores.splice(0,0,{ id : '0', descripcion:'--Seleccione vendedor--'}); 
	      		$scope.fData.colaborador = $scope.fArr.listaColaboradores[0]; 
	      	}
	      	$scope.metodos.listaColaboradores(myCallBackCO); 

	      	$scope.aceptar = function () { 
	      		blockUI.start('Procesando información...');
	          ClienteEmpresaServices.sRegistrar($scope.fData).then(function (rpta) {
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
		editClienteEmpresaModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({ 
	      templateUrl: angular.patchURLCI+'ClienteEmpresa/ver_popup_formulario',
	      size: 'md',
	      backdrop: 'static',
	      keyboard:false,
	      controller: function ($scope, $uibModalInstance, arrParams) { 
	      	blockUI.stop(); 
	      	$scope.fData = {};
	      	$scope.metodos = arrParams.metodos;
	      	$scope.fArr = arrParams.fArr;
	      	$scope.disabledVendedor = false;
	      	if( arrParams.mySelectionGrid.length == 1 ){ 
	          $scope.fData = arrParams.mySelectionGrid[0];
	        }else{
	          alert('Seleccione una sola fila');
	        }
	      	$scope.titleForm = 'Edición de Cliente - Persona Jurídica';
	      	$scope.cancel = function () {
	      	  $uibModalInstance.dismiss('cancel');
	      	}
	      	
	      	var myCallBackCC = function() { 
	      		var objIndex = $scope.fArr.listaCategoriaCliente.filter(function(obj) { 
	            return obj.id == $scope.fData.categoria_cliente.id;
	          }).shift(); 
	      		$scope.fData.categoria_cliente = objIndex; 
	      	}
	      	$scope.metodos.listaCategoriasCliente(myCallBackCC); 

	      	// BINDEO COLABORADOR 
	      	var myCallBackCO = function() { 
	      		$scope.fArr.listaColaboradores.splice(0,0,{ id : '0', descripcion:'--Seleccione vendedor--'}); 
	          var objIndex = $scope.fArr.listaColaboradores.filter(function(obj) { 
	            return obj.id == $scope.fData.colaborador.id;
	          }).shift(); 
	          $scope.fData.colaborador = objIndex; 
	          if( angular.isUndefined(objIndex)){ 
	            $scope.fData.colaborador = $scope.fArr.listaColaboradores[0]; 
	          }
	        } 
	        $scope.metodos.listaColaboradores(myCallBackCO); 
	      	// bloquear combo de vendedor dependiendo del tipo de usuario 
	      	if( arrParams.fSessionCI.categoria == 2 ){
						$scope.disabledVendedor = true; 
	      	} 
	      	$scope.aceptar = function () { 
	      		blockUI.start('Procesando información...');
	          ClienteEmpresaServices.sEditar($scope.fData).then(function (rpta) {
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