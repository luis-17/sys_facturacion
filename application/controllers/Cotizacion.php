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
	

	 public function generar_numero_cotizacion(){ 

	 	// var_dump($this->sessionFactur); exit();
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
}
