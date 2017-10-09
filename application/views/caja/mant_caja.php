<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formCaja"> 

    	<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Serie: <small class="text-danger">(*)</small> </label>
 			<select class="form-control input-sm" ng-model="fData.serie" ng-options="item as item.descripcion for item in fArr.listaSerie" required tabindex="110" ></select> 
		</div>

		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Nombre Caja: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.nombre_caja" placeholder="Ingrese nombre caja" required tabindex="100" />
		</div>

		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Maquina registradora: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.maquina_reg" placeholder="Ingrese maquina registradora" required tabindex="100" />
		</div>

	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formCaja.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 