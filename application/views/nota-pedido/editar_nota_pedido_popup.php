<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formNotaPedido">

		<div class="form-group block col-sm-3">
			<label class="control-label"> Codigo Nota de Ped: </label>
            <p class="text-bold ng-binding"> {{ fData.num_nota_pedido }} </p> 
		</div>

		<div class="form-group block col-sm-3">
			<label class="control-label"> Fecha de Emisión: </label>
            <p class="text-bold ng-binding"> {{ fData.fecha_emision }} </p> 
		</div>

		<div class="form-group block col-sm-3">
			<label class="control-label"> Vendedor: </label>
            <p class="text-bold ng-binding">{{ fData.colaborador_cot }} </p> 
		</div>

		<div class="form-group block col-sm-3">
			<label class="control-label"> Cliente: </label>
            <p class="text-bold ng-binding">{{ fData.cliente }} </p> 
		</div>				

		<div class="form-group col-md-12 mb-md">
			<label class="control-label mb-n"> Observaciones: <small class="text-danger">(*)</small> </label>
			<textarea type="text" class="form-control input-sm" ng-model="fData.observaciones" placeholder="Descripción observación" required tabindex="60" />
		</div>

	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formNotaPedido.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 