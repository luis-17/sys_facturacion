<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formEmpresaAdmin">
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Razón Social: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.razon_social" placeholder="Ingrese razón social" required tabindex="70" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Nombre Comercial: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombre_comercial" placeholder="Ingrese nombre comercial" required tabindex="80" />
		</div>	
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> R.U.C: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.ruc" placeholder="Ingrese ruc" required tabindex="80" />
		</div>	
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Teléfono: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.telefono" placeholder="Ingrese teléfono" required tabindex="90" />
		</div>	
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Dirección Legal: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.direccion_legal" placeholder="Ingrese dirección legal" required tabindex="100" />
		</div>	
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Representante Legal: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.representante_legal" placeholder="Ingrese representante legal" required tabindex="110" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Página Web: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.pagina_web" placeholder="Ingrese página web"  tabindex="120" />
		</div>	
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formEmpresaAdmin.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 