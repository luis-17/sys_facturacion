<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cotizacion extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_cotizacion','model_categoria_cliente','model_cliente_persona','model_cliente_empresa')); 
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
	

	public function generar_numero_cotizacion() 
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		if( empty($allInputs['sede']) ){ 
			$arrData['message'] = '';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		// Codigo para uso interno. 
		// NOMENCLATURA: 
		// C + ABV_SEDE(2) + AÑO(2) + MES(2) + DIA(2) + 3 CARACTERES (DINAMICO)
		// Ejm: CUC170827001 
		$sede = strtoupper($allInputs['sede']['abreviatura']); 
		$numCaracteres = 3; 
		$numCotizacion = 'C'.$sede.date('y').date('m').date('d'); 
		// OBTENER ULTIMA COTIZACION DE LA SEDE, Y DEL DÍA. 
		$fCotizacion = $this->model_cotizacion->m_cargar_ultima_cotizacion_sede_dia($allInputs);
		if( empty($fCotizacion) ){
			$numCorrelativo = 1;
		}else{
			$numCorrelativo = substr($fCotizacion['num_cotizacion'], ($numCaracteres * -1), $numCaracteres); 
			$numCorrelativo = (int)$numCorrelativo + 1;
		}
		$numCotizacion .= str_pad($numCorrelativo, $numCaracteres, '0', STR_PAD_LEFT);
	 	$arrDatos['num_cotizacion'] = $numCotizacion; 
    	$arrData['datos'] = $arrDatos;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($numCotizacion)){ 
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function registrar()
	{
		ini_set('xdebug.var_display_max_depth', 5);
	    ini_set('xdebug.var_display_max_children', 256);
	    ini_set('xdebug.var_display_max_data', 1024);
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		/* VALIDACIONES */

		if( $allInputs['isRegisterSuccess'] === TRUE ){ 
    		$arrData['message'] = 'Ya se registró esta cotización.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
		if( count($allInputs['detalle']) < 1){
    		$arrData['message'] = 'No se ha agregado ningún elemento';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['sede']['id']) ){
    		$arrData['message'] = 'Debe tener asignado una sede para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( $allInputs['total'] == 'NaN' || empty($allInputs['total']) ){
    		$arrData['message'] = 'No se puedo calcular el precio total de venta. Corrija los montos e intente nuevamente.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	$errorEnBucle = 'no'; 
    	foreach ($allInputs['detalle'] as $key => $row) {
    		if( empty($row['precio_unitario']) ){
    			$errorEnBucle = 'si';
    			break;
    		}
    	}
    	if( $errorEnBucle === 'si' ){ 
    		$arrData['message'] = 'No se puedo calcular el precio total de venta. Corrija los montos e intente nuevamente.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['num_cotizacion']) ){ 
    		$arrData['message'] = 'No se ha generado un COD. DE COTIZACIÓN. Genere la COTIZACIÓN.';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	$codigoCoti = $allInputs['num_cotizacion']; 
    	$fCotizacion = $this->model_cotizacion->m_cargar_esta_cotizacion_por_codigo($codigoCoti);
    	if( !empty($fCotizacion) ){ 
    		$arrData['message'] = 'Ya se a registrado una cotización, usando el código <strong>'.$allInputs['num_cotizacion'].'</strong>'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	// var_dump($allInputs); exit(); 
    	$this->db->trans_start();
    	if( $allInputs['tipo_documento_cliente']['destino'] == 1 ){ // cliente empresa 
    		$allInputs['tipo_cliente'] = 'E'; // empresa 
    	}
    	if( $allInputs['tipo_documento_cliente']['destino'] == 2 ){ // cliente persona 
    		$allInputs['tipo_cliente'] = 'P'; // persona 
    	}
		if( $this->model_cotizacion->m_registrar_cotizacion($allInputs) ){ 
			$arrData['idcotizacion'] = GetLastId('idcotizacion','cotizacion');
			foreach ($allInputs['detalle'] as $key => $elemento) { 
				$elemento['idcotizacion'] = $arrData['idcotizacion'];
				// $elemento['idmedicamento'] = $elemento['id'];
				if( $this->model_cotizacion->m_registrar_detalle_cotizacion($elemento) ){
					$arrData['message'] = 'Los datos se registaron correctamente';
					$arrData['flag'] = 1;
				} // registramos con el idmedicamentoalmacen del destino
			}
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
