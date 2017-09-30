<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="block col-sm-12">
			<label class="control-label"> Descripción: </label>
            <p class="text-bold"> {{ fData.descripcion_fp }} </p> 
		</div>
		<div class="col-sm-12 col-xs-12">
			<div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Días transcurridos</th>
                                    <th>% del importe</th>
                                    <th width="120"></th>
                                </tr>
                            </thead>                  
                            <tbody><tr ng-repeat="arr in fPlazo.plazolista" class="ng-scope">
                                    <td><input type="text" class="form-control input-sm" ng-model="arr.dias_transcurridos" placeholder="Ingrese días transcurridos" required tabindex="100" /></td>
                                    <td><input type="text" class="form-control input-sm" ng-model="arr.porcentaje_importe" placeholder="Ingrese porcentaje importe" required tabindex="110" /></td>                 
                                    <td class="text-right">
                                        <div class="btn-group">
                                    		<button class="btn btn-sm btn-danger" type="submit" ng-click="quitarplazo($index)" title="Anular">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </button>                                   
                                            <button class="btn btn-sm btn-primary" type="submit" ng-click="editarplazo($index)" title="Guardar">
                                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>                 
                                <tr class="bg-info" ng-show="completado">                            
                                    <td><input type="text" ng-focus="focusdt()" class="form-control input-sm" ng-model="fPlazo.dias_transcurridos" placeholder="Ingrese días transcurridos" required tabindex="120" /></td>
                                    <td><input type="text" ng-focus="focuspi()" class="form-control input-sm" ng-model="fPlazo.porcentaje_importe" placeholder="Ingrese porcentaje importe" required tabindex="120" /></td>                                
                                    <td class="text-right">
                                        <div class="btn-group" >
                                            <button class="btn btn-sm btn-primary" type="submit" ng-disabled="formPlazo.$invalid" ng-click="agregarPlazo()" title="Guardar">
                                                <span class="glyphicon glyphicon-plus-sign"></span>
                                                <span class="hidden-xs">&nbsp;Nuevo</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>                 
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>            
            <form ng-hide="" name="formPlazo">         
                    <input type="hidden" class="form-control input-sm"  ng-model="fPlazo.dias_transcurridos" placeholder="Ingrese días transcurridos" required tabindex="100" />       
                    <input type="hidden" class="form-control input-sm" ng-model="fPlazo.porcentaje_importe" placeholder="Ingrese porcentaje importe" required tabindex="120" />                              
            </form>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 


