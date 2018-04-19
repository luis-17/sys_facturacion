<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formTransporte">
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Nombre o Razón Social <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombre_razon_social" placeholder="Nombre o Razón social" required tabindex="100" />
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> R.U.C: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.ruc_transport" placeholder="R.U.C" tabindex="200" />
		</div> 
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Domicilio: <small class="text-danger">(*)</small> </label> 
			<input type="text" class="form-control input-sm" ng-model="fData.domicilio" placeholder="Domicilio" required tabindex="300" />
		</div>
		
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> N° Lic de Conducir: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.num_lic_conducir" placeholder="N° de Licencia de Conducir" tabindex="400" />
		</div> 
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formTransporte.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 