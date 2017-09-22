<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formFormaPago">
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Descripción: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.descripcion_fp" placeholder="Descripción forma pago" required tabindex="60" />
		</div>
		<div class="form-group col-md-8 mb-md">
				<div class="inline"> 
		            <label class="radio-inline">
		              <input type="radio" value="1" ng-model="fData.modo_fp"> Al Contado 
		            </label>
		            <label class="radio-inline">
		              <input type="radio" value="2" ng-model="fData.modo_fp"> Al Crédito 
		            </label>
		        </div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formFormaPago.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 