<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">
	<div class="row"> 
		<div class=" col-sm-12">
			<fieldset>
				<legend class="lead lead-sm"> Busque Nota de Pedido </legend>
				<div class="form-group"> 
		      <label class="control-label text-ellipsis inline"> N° de Nota de Pedido </label> 
		      <div class="input-group block"> 
	        	<input id="temporalNumNP" type="text" class="form-control input-sm" ng-model="num_nota_pedido_popup" 
	        		typeahead-select-on-blur="true" typeahead-loading="loadingNNP" 
	        		uib-typeahead="item as item.num_nota_pedido for item in getNumNPAutocomplete($viewValue)" typeahead-on-select="getSelectedNP($item, $model, $label)" 
	        		typeahead-min-length="2" autocomplete ="off" placeholder="Digite N° de Nota de Pedido" tabindex="240" ng-blur="validateNumNP();" /> 
	        </div>
	        <i ng-show="loadingNNP" class="fa fa-refresh"></i>
	        <div ng-show="noResultsNP" class="text-danger">
	            <i class="fa fa-remove"></i> No se encontró resultados 
	        </div>
				</div>
				<div class="clearfix"></div> 
			</fieldset>
			<fieldset class="mt" ng-show="fArr.fNotaPedidoTemp.cliente">
				<legend class="lead lead-sm"> Resultado de Búsqueda </legend>
				<div class="form-group mb-n"> 
		      <label class="control-label text-ellipsis inline"> <b> CLIENTE : </b> </label> 
		      <div class="block clearfix inline"> 
		        <label class="control-label text-ellipsis "> {{fArr.fNotaPedidoTemp.cliente}} </label>
		      </div>
				</div>
				<div class="form-group mb-n"> 
		      <label class="control-label text-ellipsis inline"> <b> FECHA EMISION : </b> </label> 
		      <div class="block clearfix inline"> 
		        <label class="control-label text-ellipsis "> {{fArr.fNotaPedidoTemp.fecha_emision}} </label>
		      </div>
				</div>
				<div class="form-group mb-n"> 
		      <label class="control-label text-ellipsis inline"> <b> TOTAL : </b> </label> 
		      <div class="block clearfix inline"> 
		        <label class="control-label text-ellipsis "> {{fArr.fNotaPedidoTemp.total}} </label>
		      </div>
				</div>
				<div class="form-group mb-n"> 
		      <label class="control-label text-ellipsis inline"> <b> ESTADO : </b> </label> 
		      <div class="block clearfix inline"> 
		        <label class="control-label text-ellipsis {{fArr.fNotaPedidoTemp.estado.claseLabel}}"> {{fArr.fNotaPedidoTemp.estado.labelText}} </label> 
		      </div> 
				</div> 
				<div class="clearfix"></div> 
			</fieldset>
			<div class="block" ng-show="!(fArr.fNotaPedidoTemp.cliente)"> Sin resultados...  </div>
		</div>
		
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="seleccionarNP(); $event.preventDefault();" > Seleccionar </button> 
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 