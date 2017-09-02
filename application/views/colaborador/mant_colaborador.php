<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="ColaboradorCtrl">
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Nombres: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Ingrese nombres" required tabindex="40" />
		</div>
    	<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Apellidos: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellidos" placeholder="Ingrese apellidos" required tabindex="50" />
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Fecha de Nac.: </label>
			<input type="tel" class="form-control input-sm" ng-model="fData.fecha_nacimiento" tabindex="70" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> N° Documento: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.num_documento" placeholder="Ingrese n° número documento" required tabindex="80" />		
		</div>	
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Teléfono: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.telefono" placeholder="Ingrese teléfono " required tabindex="80" />
		</div>	

		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> E-mail: </label>
			<input type="email" class="form-control input-sm" ng-model="fData.email" placeholder="Ingrese correo electrónico" tabindex="60"  />
		</div>

	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="ColaboradorCtrl.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 