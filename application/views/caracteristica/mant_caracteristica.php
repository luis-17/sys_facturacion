<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formCaracteristica">
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Descripción: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.descripcion_car" placeholder="Descripción característica" required tabindex="100" />
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Orden: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.orden_car" placeholder="Digite orden" tabindex="110" />
		</div>

	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formCaracteristica.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 