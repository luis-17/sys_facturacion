<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formContacto">
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Nombres: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Ingres nombres" required tabindex="10" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Apellidos: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellidos" placeholder="Ingrese apellidos" tabindex="20" />
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Fecha de Nac.: </label>
			 <input type="text" input-mask mask-options="{alias: 'dd-mm-yyyy'}" placeholder="dd-mm-yyyy" class="form-control input-sm" ng-model="fData.fecha_nacimiento" tabindex="30" /> 
		</div> 
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Teléfono Móvil: </label>
			<input type="tel" class="form-control input-sm" ng-model="fData.telefono_movil" placeholder="Ingrese tel. movil" tabindex="50" /> 
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Teléfono fijo </label>
			 <input type="tel" class="form-control input-sm" ng-model="fData.telefono_fijo" placeholder="Ingrese tel. fijo" tabindex="60" /> 
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> Anexo </label>
			 <input type="text" class="form-control input-sm" ng-model="fData.anexo" placeholder="Anexo" tabindex="70" /> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Area encargada </label>
			<input type="text" class="form-control input-sm" ng-model="fData.area_encargada" placeholder="Ingrese área"  tabindex="80" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Correo: </label>
			<input type="email" class="form-control input-sm" ng-model="fData.email" placeholder="Ingrese correo"  tabindex="90" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Persona Jurídica <small class="text-danger">(*)</small> </label> 
			<div class="input-group"> 
				<input type="text" disabled="true" class="form-control input-sm" ng-model="fData.cliente_empresa.descripcion" placeholder="Busque cliente empresa"  tabindex="100" /> 
	        	<span class="input-group-btn">
	                <button class="btn btn-default btn-sm" type="button" ng-click="btnBusquedaPersonaJuridica();" tabindex="120"><i class="fa fa-search"></i> </button>
	            </span>
	        </div> 
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formContacto.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 