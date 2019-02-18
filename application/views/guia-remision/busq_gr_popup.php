<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form name="formBusquedaGR" class=""> 
		<div class="row mb"> 
			<div class="col-xs-12">
				<div class="inline w-md mr-xs"> 
		      <label class="control-label text-ellipsis"> Desde </label> 
		      <div class="input-group block clearfix"> 
		        <input tabindex="110" type="text" class="form-control input-sm mask" ng-model="fBusqueda.desde" style="width: 50%;" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
		        <input tabindex="115" type="text" class="form-control input-sm" ng-model="fBusqueda.desdeHora" style="width: 22%; margin-left: 4px;" />
		        <input tabindex="116" type="text" class="form-control input-sm" ng-model="fBusqueda.desdeMinuto" style="width: 22%; margin-left: 4px;" />
		      </div>
		    </div>
		    <div class="inline w-md mr-xs"> 
		      <label class="control-label text-ellipsis"> Hasta </label> 
		      <div class="input-group block clearfix"> 
		        <input tabindex="120" type="text" class="form-control input-sm mask" ng-model="fBusqueda.hasta" style="width: 50%;" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
		        <input tabindex="122" type="text" class="form-control input-sm" ng-model="fBusqueda.hastaHora" style="width: 22%; margin-left: 4px;" />
		        <input tabindex="123" type="text" class="form-control input-sm" ng-model="fBusqueda.hastaMinuto" style="width: 22%; margin-left: 4px;" />
		      </div> 
		    </div>
		    <div class="inline w-xs mr" > 
          <label class="control-label text-ellipsis">  </label> 
          <div class="input-group block clearfix"> 
            <button type="button" class="btn btn-info btn-sm" style="position:absolute;" ng-click="metodos.getPaginationServerSideGR(true);"> PROCESAR </button> 
          </div>
        </div>
			</div>
	  </div>
		<div class="row"> 
			<div class="col-xs-12">
				<div ui-grid="gridOptionsGR" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formBusquedaGR.$invalid">ASOCIAR SELECCIONADOS</button> 
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 