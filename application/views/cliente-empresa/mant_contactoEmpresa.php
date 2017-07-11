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
			<form name="formContactoEmpresa" class="pt-sm {{ editClassForm }} "> 
				<fieldset class="fieldset-sm">
					<legend class="mb-sm "> {{ tituloBloque }} </legend> 
					<div class="form-group">
						<label class="control-label"> Nombres: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fContacto.nombres" placeholder="Ingrese nombres" required tabindex="20" /> 
					</div>
					<div class="form-group">
						<label class="control-label"> Apellidos: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fContacto.apellidos" placeholder="Ingrese apellidos" required tabindex="30" /> 
					</div>
					<div class="form-group col-md-6 pl-n">
						<label class="control-label"> Teléfono Fijo: <small class="text-danger">(*)</small> </label>
			            <input type="tel" class="form-control input-sm" ng-model="fContacto.telefono_fijo" placeholder="Ingrese tel. fijo" required tabindex="40" /> 
					</div>
					<div class="form-group col-md-6 pr-n">
						<label class="control-label"> Teléfono Movil: <small class="text-danger">(*)</small> </label>
			            <input type="tel" class="form-control input-sm" ng-model="fContacto.telefono_movil" placeholder="Ingrese tel. movil" required tabindex="50" /> 
					</div>
					<div class="form-group col-md-6 pl-n">
						<label class="control-label"> E-mail: </label>
			            <input type="email" class="form-control input-sm" ng-model="fContacto.email" placeholder="Ingrese correo electrónico" tabindex="60" /> 
					</div>
					<div class="form-group col-md-6 pr-n">
						<label class="control-label"> Fecha de Nac.: </label>
			            <input type="text" input-mask mask-options="{alias: 'dd-mm-yyyy'}" placeholder="dd-mm-yyyy" class="form-control input-sm" ng-model="fContacto.fecha_nacimiento" tabindex="70" /> 
					</div> 
					<div class="form-group" ng-if="contBotonesReg">
						<button type="button" ng-click="agregarContacto(); $event.preventDefault();" ng-disabled="formContactoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-full"> <i class="fa fa-plus"></i> AGREGAR CONTACTO </button>
					</div> 
					<div class="form-group" ng-if="contBotonesEdit">
						<button type="button" ng-click="actualizarContacto(); $event.preventDefault();" ng-disabled="formContactoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-block"> <i class="fa fa-edit"></i> ACTUALIZAR CONTACTO </button>
						<button type="button" ng-click="quitarContacto(); $event.preventDefault();" class="block btn btn-danger btn-sm btn-block"> <i class="fa fa-trash"></i> QUITAR CONTACTO </button>
					</div> 
				</fieldset>	
			</form>
		</div>
		<div class="col-sm-8 col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div  ui-grid="gridOptionsContactos" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 