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
		<div class="col-sm-3 col-xs-12"> 
			<form name="formBancoEmpresa" class="pt-sm {{ editClassForm }} "> 
				<fieldset class="fieldset-sm">
					<legend class="mb-sm "> {{ tituloBloque }} </legend> 
					<div class="form-group">
						<label class="control-label"> Empresa: </label>
			            <select class="form-control input-sm" ng-model="fEmpresa.empresa" ng-options="item as item.descripcion for item in fArr.listaEmpresa" required tabindex="100" ></select> 
					</div>	
					<div class="form-group" ng-if="contBotonesReg">
						<button type="button" ng-click="agregarUsuarioEmpresa(); $event.preventDefault();" ng-disabled="formBancoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-full"> <i class="fa fa-plus"></i> AGREGAR EMPRESA </button>
					</div>
					<div class="form-group" ng-if="contBotonesEdit">
						<button type="button" ng-click="quitarEmpresa(); $event.preventDefault();" class="block btn btn-danger btn-sm btn-block"> <i class="fa fa-trash"></i> QUITAR EMPRESA </button>
					</div> 
				</fieldset>	
			</form>
		</div>
		<div class="col-sm-9 col-xs-12">
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
