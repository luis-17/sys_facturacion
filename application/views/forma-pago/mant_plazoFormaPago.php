<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="form-group block col-sm-12">
			<label class="control-label"> Descripción: </label>
            <p class="text-bold"> {{ fData.descripcion_fp }} </p> 
		</div>
		<div class="col-sm-4 col-xs-12"> 
			<form name="formBancoEmpresa" class="pt-sm {{ editClassForm }} "> 
				<fieldset class="fieldset-sm">
					<legend class="mb-sm "> {{ tituloBloque }} </legend> 
					<div class="form-group">
						<label class="control-label"> Días transcurridos: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fPlazo.dias_transcurridos" placeholder="Ingrese días transcurridos" required tabindex="20" /> 
					</div>
					<div class="form-group">
						<label class="control-label"> Porcentaje importe: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fPlazo.porcentaje_importe" placeholder="Ingrese porcentaje importe" required tabindex="30" /> 
					</div>
					<div class="form-group" ng-if="contBotonesReg">
						<button type="button" ng-click="agregarPlazo(); $event.preventDefault();" ng-disabled="formBancoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-full"> <i class="fa fa-plus"></i> AGREGAR PLAZO </button>
					</div>
					<div class="form-group" ng-if="contBotonesEdit">
						<button type="button" ng-click="actualizarPlazo(); $event.preventDefault();" ng-disabled="formBancoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-block"> <i class="fa fa-edit"></i> ACTUALIZAR PLAZO </button>
						<button type="button" ng-click="quitarPlazo(); $event.preventDefault();" class="block btn btn-danger btn-sm btn-block"> <i class="fa fa-trash"></i> QUITAR PLAZO </button>
					</div> 
				</fieldset>	
			</form>
		</div>
		<div class="col-sm-8 col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div  ui-grid="gridOptionsPlazo" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 