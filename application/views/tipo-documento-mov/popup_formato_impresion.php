<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form name="formFormatoImpresion" class=""> 
		<div class="row">
			<div class="col-xs-12">
		        <label class="text-info"> Configure el formato de impresión para: <strong> {{ fData.tipo_documento }} </strong> </label>
			</div>
		</div> 
		<div class="row"> 
			<div class="col-xs-12">
				<div ui-grid="fArr.gridOptionsFI" ui-grid-edit ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div> 
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel();">Salir</button>
</div> 