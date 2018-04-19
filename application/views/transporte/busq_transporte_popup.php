<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form name="formBusquedaTransporte" class=""> 
		<div class="row">
			<div class="col-xs-12"> 
		        <div class="inline pull-right"> 
		        	<button class="btn m-b-xs btn-success btn-xs" ng-click="btnNuevoTransporte();"> <i class="fa fa-file-text"></i> Nuevo </button> 
		        </div>
			</div>
		</div> 
		<div class="row"> 
			<div class="col-xs-12">
				<div ui-grid="fArr.gridOptionsTRE" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formBusquedaTransporte.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 