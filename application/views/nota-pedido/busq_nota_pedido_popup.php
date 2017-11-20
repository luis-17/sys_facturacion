<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">
	<div class="row"> 
		<div class="col-xs-12"> 
      <label class="control-label text-ellipsis inline"> <b> Cliente : </b> </label> 
      <div class="block clearfix inline"> 
        <label class="control-label text-ellipsis ">{{fBusquedaNP.cliente.descripcion}} </label>
      </div>
		</div>
		<div class="col-xs-12">
			<div class="form-group mb-sm"> 
			    <div class="inline w-sm mr" > 
			      <label class="control-label text-ellipsis"> <b>Sede</b> </label> 
			      <div class="input-group block clearfix"> 
			        <select class="form-control input-sm" ng-model="fBusquedaNP.sede" ng-options="item as item.descripcion for item in fArr.listaSedes" 
			          ng-change="metodos.getPaginationServerSideNP(true);" > </select> 
			      </div>
			    </div>
			    <div class="inline w-md mr-xs"> 
			      <label class="control-label text-ellipsis"> <b>Desde</b> </label> 
			      <div class="input-group block clearfix"> 
			        <input tabindex="110" type="text" class="form-control input-sm mask" ng-model="fBusquedaNP.desde" style="width: 50%;" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
			        <input tabindex="115" type="text" class="form-control input-sm" ng-model="fBusquedaNP.desdeHora" style="width: 22%; margin-left: 4px;" />
			        <input tabindex="116" type="text" class="form-control input-sm" ng-model="fBusquedaNP.desdeMinuto" style="width: 22%; margin-left: 4px;" />
			      </div>
			    </div>
			    <div class="inline w-md mr-xs"> 
			      <label class="control-label text-ellipsis"> <b>Hasta</b> </label> 
			      <div class="input-group block clearfix"> 
			        <input tabindex="120" type="text" class="form-control input-sm mask" ng-model="fBusquedaNP.hasta" style="width: 50%;" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
			        <input tabindex="122" type="text" class="form-control input-sm" ng-model="fBusquedaNP.hastaHora" style="width: 22%; margin-left: 4px;" />
			        <input tabindex="123" type="text" class="form-control input-sm" ng-model="fBusquedaNP.hastaMinuto" style="width: 22%; margin-left: 4px;" />
			      </div> 
			    </div> 
			    <div class="inline w-xs mr" > 
			      <label class="control-label text-ellipsis">  </label> 
			      <div class="input-group block clearfix"> 
			        <button type="button" class="btn btn-info btn-sm" style="position:absolute;" ng-click="metodos.getPaginationServerSideNP(true);"> PROCESAR </button> 
			      </div>
			    </div>
			</div>
		</div>
		<div class="col-xs-12">
			<div ui-grid="gridOptionsNP" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="modal-footer">
    <!-- <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" >Agregar</button> -->
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 