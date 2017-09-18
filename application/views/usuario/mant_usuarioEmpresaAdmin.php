<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="form-group block col-sm-6">
			<label class="control-label"> Tipo Usuario: </label>
            <p class="text-bold"> {{ fData.tipo_usuario.descripcion }} </p> 
		</div>
		<div class="form-group block col-sm-5">
			<label class="control-label"> Usuario: </label>
            <p class="text-bold"> {{ fData.username }} </p> 
		</div>
		<div class="col-sm-4 col-xs-12"> 
			<form name="formBancoEmpresa" class="pt-sm {{ editClassForm }} "> 
				<fieldset class="fieldset-sm">
					<legend class="mb-sm "> {{ tituloBloque }} </legend> 

					<div class="form-group">
						<label class="control-label"> Razón Social: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fEmpresa.razon_social" placeholder="Ingrese razón social" required tabindex="20" /> 
					</div>
					<div class="form-group">
						<label class="control-label"> Nombre Comercial: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fEmpresa.nombre_comercial" placeholder="Ingrese nombre comercial" required tabindex="30" /> 
					</div>
					<div class="form-group col-md-6 pl-n">
						<label class="control-label"> R.U.C: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fEmpresa.ruc" placeholder="Ingrese ruc" required tabindex="40" />
					</div>					
					<div class="form-group col-md-6 pr-n">
						<label class="control-label"> Teléfono: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fEmpresa.telefono" placeholder="Ingrese teléfono" required tabindex="50" />
					</div>		
					<div class="form-group">
						<label class="control-label"> Dirección Legal: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fEmpresa.direccion_legal" placeholder="Ingrese dirección legal"  required tabindex="60" /> 
					</div>
					<div class="form-group">
						<label class="control-label"> Representante Legal: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fEmpresa.representante_legal" placeholder="Ingrese representante legal" required tabindex="70" /> 
					</div>
					<div class="form-group">
						<label class="control-label"> Página Web: </label>
			            <input type="text" class="form-control input-sm" ng-model="fEmpresa.pagina_web" placeholder="Ingrese página web"  tabindex="80" /> 
					</div>
					<div class="form-group" ng-if="contBotonesReg">
						<button type="button" ng-click="agregarUsuarioEmpresa(); $event.preventDefault();" ng-disabled="formBancoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-full"> <i class="fa fa-plus"></i> AGREGAR EMPRESA </button>
					</div>
					<div class="form-group" ng-if="contBotonesEdit">
				<!-- 		<button type="button" ng-click="actualizarEmpresa(); $event.preventDefault();" ng-disabled="formBancoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-block"> <i class="fa fa-edit"></i> ACTUALIZAR BANCO </button> -->
						<button type="button" ng-click="quitarEmpresa(); $event.preventDefault();" class="block btn btn-danger btn-sm btn-block"> <i class="fa fa-trash"></i> QUITAR EMPRESA </button>
					</div> 
				</fieldset>	
			</form>
		</div>
		<div class="col-sm-8 col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div  ui-grid="gridOptionsEmpresa" ui-grid-pagination ui-grid-selection ui-grid-edit ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 