<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formClientePersona"> 
		<div class="form-group col-md-6 mb-md ">
			<label class="control-label mb-n"> Categoría: <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.categoria_cliente" ng-options="item as item.descripcion for item in fArr.listaCategoriaCliente" required tabindex="10" ></select> 
		</div> 
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° de Documento: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.num_documento" placeholder="Ingrese N° de Documento" required tabindex="30" maxlength="8" minlength="8" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Nombres: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Ingrese nombres" required tabindex="40" />
		</div>
    	<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Apellidos: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellidos" placeholder="Ingrese apellidos" tabindex="50" />
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Sexo <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.sexo" ng-options="item as item.descripcion for item in fArr.listaSexo" required tabindex="60" ></select> 
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Fecha de Nac.: </label>
			<input type="tel" class="form-control input-sm" ng-model="fData.fecha_nacimiento" tabindex="70" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Teléfono movil: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.telefono_movil" placeholder="Ingrese teléfono movil" tabindex="80" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Teléfono Fijo: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.telefono_fijo" placeholder="Ingrese teléfono fijo" tabindex="90" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> E-mail: </label>
			<input type="email" class="form-control input-sm" ng-model="fData.email" placeholder="Ingrese email" tabindex="100" />
		</div>
		<div class="form-group col-md-6 mb-md ">
			<label class="control-label mb-n"> Vendedor/Colaborador </label>
            <select ng-disabled="disabledVendedor" class="form-control input-sm" ng-model="fData.colaborador" ng-options="item as item.descripcion for item in fArr.listaColaboradores" tabindex="120" ></select> 
		</div>
		
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formClientePersona.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 