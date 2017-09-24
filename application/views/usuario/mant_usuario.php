<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="usuarioForm"> 

		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Usuario: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.username" placeholder="Ingrese usuario" required tabindex="100" />
		</div>
    	<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Tipo Usuario: <small class="text-danger">(*)</small> </label>
 			<select class="form-control input-sm" ng-model="fData.tipo_usuario" ng-options="item as item.descripcion for item in fArr.listaTipoUsuario" required tabindex="110" ></select> 
		</div>

		<div ng-show="modoEdit"> 
	    	<div class="form-group col-md-6 mb-md">
				<label class="control-label mb-n"> Ingrese Contraseña: <small class="text-danger">(*)</small> </label>
				<input type="password" class="form-control input-sm" ng-model="fData.password_view" placeholder="Registre contraseña" tabindex="120" />
			</div>
	    	<div class="form-group col-md-6 mb-md">
				<label class="control-label mb-n"> Repita la contraseña: <small class="text-danger">(*)</small> </label>
				<input type="password" class="form-control input-sm" ng-model="fData.password" placeholder="Repita contraseña" tabindex="130" />
			 </div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="usuarioForm.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 