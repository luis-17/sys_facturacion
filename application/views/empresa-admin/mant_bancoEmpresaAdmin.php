<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="form-group block col-sm-4">
			<label class="control-label"> Razón Social: </label>
            <p class="text-bold"> {{ fData.razon_social }} </p> 
		</div>
		<div class="form-group block col-sm-4">
			<label class="control-label"> Nombre Comercial: </label>
            <p class="text-bold"> {{ fData.nombre_comercial }} </p> 
		</div>
		<div class="form-group block col-sm-4">
			<label class="control-label"> RUC: </label>
            <p class="text-bold"> {{ fData.ruc }} </p> 
		</div>
		<div class="col-sm-4 col-xs-12"> 
			<form name="formBancoEmpresa" class="pt-sm {{ editClassForm }} "> 
				<fieldset class="fieldset-sm">
					<legend class="mb-sm "> {{ tituloBloque }} </legend> 
					<div class="form-group col-md-6 pl-n">
						<label class="control-label"> Banco: <small class="text-danger">(*)</small> </label>
			            <select class="form-control input-sm" ng-model="fBanco.banco" ng-options="item as item.descripcion for item in fArr.listaBanco" required tabindex="40" ></select> 
					</div>					
					<div class="form-group col-md-6 pr-n">
						<label class="control-label"> Moneda: <small class="text-danger">(*)</small> </label>
			            <select class="form-control input-sm" ng-model="fBanco.moneda" ng-options="item as item.descripcion for item in fArr.listaMoneda" required tabindex="40" ></select> 
					</div>		
					<div class="form-group">
						<label class="control-label"> N° Cuenta: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fBanco.num_cuenta" placeholder="Ingrese n° de cuenta" required tabindex="20" /> 
					</div>
					<div class="form-group">
						<label class="control-label"> N° Cuenta Interbancaria: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fBanco.num_cuenta_inter" placeholder="Ingrese n° cuenta interbancaria" required tabindex="30" /> 
					</div>
					<div class="form-group" ng-if="contBotonesReg">
						<button type="button" ng-click="agregarBanco(); $event.preventDefault();" ng-disabled="formBancoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-full"> <i class="fa fa-plus"></i> AGREGAR BANCO </button>
					</div>
					<div class="form-group" ng-if="contBotonesEdit">
						<button type="button" ng-click="actualizarBanco(); $event.preventDefault();" ng-disabled="formBancoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-block"> <i class="fa fa-edit"></i> ACTUALIZAR BANCO </button>
						<button type="button" ng-click="quitarBanco(); $event.preventDefault();" class="block btn btn-danger btn-sm btn-block"> <i class="fa fa-trash"></i> QUITAR BANCO </button>
					</div> 
				</fieldset>	
			</form>
		</div>
		<div class="col-sm-8 col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div  ui-grid="gridOptionsBancos" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 