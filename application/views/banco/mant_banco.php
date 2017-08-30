<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formBanco">
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Descripción: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.descripcion_ba" placeholder="Descripción banco" required tabindex="60" />
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Abreviatura: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.abreviatura_ba" placeholder="Ingrese abreviatura" required tabindex="70" />
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formBanco.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 