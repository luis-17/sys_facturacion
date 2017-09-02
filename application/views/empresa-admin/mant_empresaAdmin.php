<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formEmpresaAdmin">
		<div class="form-group col-md-6 mb-md ">
			<label class="control-label mb-n"> Cuentas Bancarias: <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.banco" ng-options="item as item.descripcion for item in fArr.listaBanco" required tabindex="10" ></select> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° Cuenta: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.num_cuenta" placeholder="Ingrese número de cuenta" required tabindex="70" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° Cuenta Interbancaria: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.num_cuenta_inter" placeholder="Ingrese número de cuenta interbancaria" required tabindex="70" />
		</div>		
		<div class="form-group col-md-6 mb-md ">
			<label class="control-label mb-n"> Moneda: <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.moneda" ng-options="item as item.descripcion for item in fArr.listaMoneda" required tabindex="10" ></select> 
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formEmpresaAdmin.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 