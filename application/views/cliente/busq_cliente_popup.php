<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form name="formBusquedaCliente" class=""> 
		<div class="row" ng-show="!(fBusqueda.origen == 'contacto')">
			<div class="col-xs-12">
				<div class="inline"> 
		            <label class="radio-inline">
		              <input type="radio" value="cp" ng-model="fBusqueda.tipo_cliente" ng-change="metodos.cambioColumnas();metodos.getPaginationServerSideBC(true);"> Cliente Persona 
		            </label>
		            <label class="radio-inline">
		              <input type="radio" value="ce" ng-model="fBusqueda.tipo_cliente" ng-change="metodos.cambioColumnas();metodos.getPaginationServerSideBC(true);"> Cliente Empresa 
		            </label>
		        </div>
		        <div class="inline pull-right"> 
		        	<button class="btn m-b-xs btn-success btn-xs" ng-click="btnNuevoCliente();">
	        			<i class="fa fa-file-text"></i> Nuevo </button> 
		        </div>
			</div>
		</div> 
		<div class="row"> 
			<div class="col-xs-12">
				<div ui-grid="fArr.gridOptionsBC" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formBusquedaCliente.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 