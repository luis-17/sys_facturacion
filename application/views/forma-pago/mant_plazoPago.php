<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="block col-sm-12">
			<label class="control-label"> Descripción: </label>
            <p class="text-bold"> {{ fData.forma_pago.descripcion }} </p> 
		</div>
		<div class="col-sm-12 col-xs-12">
			<div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>DÍAS TRANSCURRIDOS</th>                        
                                    <th>FECHA DE PAGO</th>
                                    <th>% DE PAGO</th>
                                    <th>MONTO</th>
                                </tr>
                            </thead>                  
                            <tbody><tr ng-repeat="arr in fPlazo.plazolista" class="ng-scope">
                                    <td >{{ arr.id }} </td>
                                    <td >{{ arr.dias_transcurridos }} </td>                         
                                    <td >{{ arr.fechaemision }} </td>
                                    <td >{{ arr.porcentaje_importe }} </td>
                                    <td >{{ arr.montototales }} </td>           
                                </tr>               
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>            
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 


