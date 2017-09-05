<div class="modal-header">
	<h4 class="modal-title"> {{ titleModalReporte }} </h4>
</div> 
<div class="modal-body">
    <form class="row" name="formExamen"> 
		<div class="col-md-12"> 
			<iframe id="frameReporte" style="width: 100%; height: 650px;" type="application/pdf"></iframe> 
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Salir</button>
</div>