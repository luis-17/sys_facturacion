<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formTransporte">
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Marca de Transporte: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.marca_transporte" placeholder="Marca" required tabindex="100" />
		</div>
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Placa de Transporte: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.placa_transporte" placeholder="Placa" required tabindex="200" />
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> N° Cert. Inscripción: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.num_cert_inscripcion" placeholder="Cert. Inscripción" tabindex="300" />
		</div> 
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formTransporte.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 