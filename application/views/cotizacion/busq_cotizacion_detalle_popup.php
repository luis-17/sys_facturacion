<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">
	<div class="row"> 
		<div class="col-xs-12"> 
		    <label class="control-label text-ellipsis inline"> <b> Cliente : </b> </label> 
		    <div class="block clearfix inline"> 
		      <label class="control-label text-ellipsis ">{{fBusquedaDETCOT.cliente.descripcion}} </label>
		    </div>
		</div>
		<div class="col-xs-12">
			<div ui-grid="gridOptionsDETCOT" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="modal-footer">
    <!-- <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" >Agregar</button> -->
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 