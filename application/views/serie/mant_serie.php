<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formSerie"> 
		<div class="form-group col-md-12 mb-md">
			<label class="control-label mb-n"> Número serie: <small class="text-danger">(*)</small> </label> 
			<input type="text" class="form-control input-sm" ng-model="fData.numero_serie" placeholder="Ingrese número serie" required tabindex="10" />
		</div>
		<div class="form-group col-md-12 mb-md">
			<label class="control-label mb-n"> Descripción: <small class="text-danger">(*)</small> </label> 
			<textarea class="form-control input-sm" placeholder="Descripción serie" ng-model="fData.descripcion_ser" tabindex="20" required> </textarea> 
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formSerie.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 