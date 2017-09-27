<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form name="formAgregarCaracteristica" class=""> 
		<div class="row">
			<div class="col-xs-12">
		        <div class="inline pull-right"> 
		        	<button type="button" class="btn m-b-xs btn-success btn-xs" ng-click="btnNuevaCaracteristica();">
	        			<i class="fa fa-file-text"></i> Nuevo </button> 
	        		<button type="button" class="btn m-b-xs btn-info btn-xs" ng-click="metodos.getPaginationServerSideCR(true);">
	        			<i class="fa fa-file-text"></i> Reestablecer </button> 
		        </div>
			</div>
		</div> 
		<div class="row"> 
			<div class="col-xs-12">
				<div ng-if="vista === 'agregar'" ui-grid="fArr.gridOptionsCR" ui-grid-edit ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
				<div ng-if="vista === 'detalle'" ui-grid="fArr.gridOptionsCRDet" ui-grid-edit ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel();">Guardar</button>
</div> 