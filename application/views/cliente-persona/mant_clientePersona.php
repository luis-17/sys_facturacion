<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formClienteEmpresa"> 
		<div class="form-group col-md-4 mb-md ">
			<label class="control-label mb-n"> Categoría: <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.categoria_cliente" ng-options="item as item.descripcion for item in fArr.listaCategoriaCliente" required tabindex="10" ></select> 
		</div>
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Razón Social: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.razon_social" placeholder="Ingrese razón social" required tabindex="30" />
		</div>
    	
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> RUC: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.ruc" placeholder="Ingrese RUC" required tabindex="40" maxlength="40" />
		</div>
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Representante Legal <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.representante_legal" placeholder="Ingrese representante legal" required tabindex="50" />
		</div>

		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Nombre Comercial: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombre_comercial" placeholder="Ingrese nombre comercial" required tabindex="20" />
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Nombre Corto: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombre_corto" placeholder="Ingrese nombre corto" required tabindex="25" />
		</div>

		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Dirección Legal: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.direccion_legal" placeholder="Ingrese direción legal" tabindex="60" />
		</div>
		
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Teléfono: </label>
			<input type="tel" class="form-control input-sm" ng-model="fData.telefono" placeholder="Ingrese teléfono" tabindex="80" />
		</div>
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Dirección de la Guía: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.direccion_guia" placeholder="Ingrese Direccion que aparecerá en la guía" tabindex="70" />
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formClienteEmpresa.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 