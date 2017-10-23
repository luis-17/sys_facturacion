<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PlazoFormaPago extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','otros_helper','fechas_helper'));
		$this->load->model(array('model_plazo_forma_pago'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_plazo_forma_pago(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);	
		$paramDatos = $allInputs['datos'];	
		$lista = $this->model_plazo_forma_pago->m_cargar_plazo_forma_pago($paramDatos);
		$fCount = $this->model_plazo_forma_pago->m_count_plazo_forma_pago($paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 	
			array_push($arrListado,
				array(
					'id' => $row['idplazoformapago'],	
					'dias_transcurridos' => strtoupper($row['dias_transcurridos']),
					'porcentaje_importe' => strtoupper($row['porcentaje_importe']),
					'idformapago' => $row['idformapago']
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function listar_plazo_forma_pago_detalle(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramDatos = $allInputs['datos'];
		$paramDatosFecha = $allInputs['fechaemision'];	
		$monto = @$allInputs['monto'];
		$lista = $this->model_plazo_forma_pago->m_cargar_plazo_forma_pago_detalle($paramDatos);
		$arrListado = array();
		foreach ($lista as $row) {
			if(isset($monto)){
				$totalmonto =  ($row['porcentaje_importe']*@$monto)/100;
			}else{
				$totalmonto='-';
			}
			array_push($arrListado,
				array(
					'id' => $row['idplazoformapago'],	
					'dias_transcurridos' => strtoupper($row['dias_transcurridos']),
					'porcentaje_importe' => strtoupper($row['porcentaje_importe']),
					'idformapago' => $row['idformapago'],
					'fechaemision' => agregardiasfecha($paramDatosFecha,$row['dias_transcurridos']),
					'montototales' => $totalmonto,
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}


	public function ver_popup_plazo_forma_pago()
	{
		$this->load->view('forma-pago/mant_plazoFormaPago');
	}

	public function ver_popup_plazo_pago()
	{
		$this->load->view('forma-pago/mant_plazoPago');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$suma=$allInputs['porcentaje_importe']+$allInputs['totalimporte'];    
    	if($suma>100){     
    		$arrData['message'] = 'No se puede registrar, se ha pasado del 100%'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	$this->db->trans_start();	
		if( $this->model_plazo_forma_pago->m_registrar($allInputs) ){ 
					$arrData['message'] = 'Se registraron los datos correctamente';
					$arrData['flag'] = 1;				
		} 			
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);		
		$arrData['message'] = 'No se pudo anular los datos';
    	$arrData['flag'] = 0;
		if( $this->model_plazo_forma_pago->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$paramDatos = $allInputs['datos'];	
    	if($allInputs['total']>100){     
    		$arrData['message'] = 'No se puede editar, se ha pasado del 100%'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
		$this->db->trans_complete();
		if($this->model_plazo_forma_pago->m_editar($paramDatos)) {
			$arrData['message'] = 'Se editaron los datos correctamente';
			$arrData['flag'] = 1;
		}		
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
