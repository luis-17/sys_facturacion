<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formContacto">
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Nombres: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Ingres nombres" required tabindex="60" />
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Apellidos: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellidos" placeholder="Ingrese apellidos" required tabindex="70" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Fecha de Nac.: <small class="text-danger">(*)</small></label>
			 <input type="text" input-mask mask-options="{alias: 'dd-mm-yyyy'}" placeholder="dd-mm-yyyy" class="form-control input-sm" ng-model="fData.fecha_nacimiento" required tabindex="70" /> 
		</div> 
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Teléfono fijo <small class="text-danger">(*)</small> </label>
			 <input type="tel" class="form-control input-sm" ng-model="fData.telefono_fijo" placeholder="Ingrese tel. movil" required tabindex="50" /> 
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Teléfono Móvil: <small class="text-danger">(*)</small></label>
			<input type="tel" class="form-control input-sm" ng-model="fData.telefono_movil" placeholder="Ingrese tel. movil" required tabindex="50" /> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Correo: </label>
			<input type="email" class="form-control input-sm" ng-model="fData.email" placeholder="Ingrese correo"  tabindex="70" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Persona Jurídica <small class="text-danger">(*)</small> </label>
			<input class="form-control input-sm" id="temporalElemento" type="text" ng-model="fData.nombre_comercial" placeholder="Busque persona juridica" typeahead-loading="loadingLocations" uib-typeahead="item as item.nombre_comercial for item in getElementoAutocomplete($viewValue)" autocomplete ="off" required tabindex="240"/> 
	        <div ng-show="noResultsELE" class="text-danger">
	            <i class="fa fa-remove"></i> No se encontró resultados 
	        </div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formContacto.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 