<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formTipoDocumento">
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Descripción: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.tipo_documento" placeholder="Descripción tipo documento" required tabindex="60" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Abreviatura: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.abreviatura" placeholder="Ingrese abreviatura" required tabindex="70" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Porcentaje: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.porcentaje" placeholder="Ingrese porcentaje" required tabindex="80" />
		</div>

	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formTipoDocumento.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 