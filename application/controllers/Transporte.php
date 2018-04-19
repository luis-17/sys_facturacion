<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transporte extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_transporte')); 
    }

	public function listar_transporte(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_transporte->m_cargar_transporte($paramPaginate);
		$fCount = $this->model_transporte->m_count_transporte($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idtransporte'],
					'marca_transporte' => $row['marca_transporte'],
					'placa_transporte' => $row['placa_transporte'],
					'num_cert_inscripcion' => strtoupper($row['num_cert_inscripcion']),
					// transporte 
					'descripcion' => $row['placa_transporte'].' '.$row['marca_transporte'] 
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $fCount['contador'];
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function ver_popup_busqueda_transporte()
	{
		$this->load->view('transporte/busq_transporte_popup');
	}
	public function ver_popup_formulario()
	{
		$this->load->view('transporte/mant_transporte');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	  	
    	$this->db->trans_start();
		if($this->model_transporte->m_registrar($allInputs)) { 
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	// if(!is_numeric($allInputs['orden_car'])){ 
    	// 	$arrData['message'] = 'Digite número , no letras'; 
    	// 	$arrData['flag'] = 0;
    	// 	$this->output
		   //  	->set_content_type('application/json')
		   //  	->set_output(json_encode($arrData));
		   //  return;
    	// }        	
    	$this->db->trans_start();
		if($this->model_transporte->m_editar($allInputs)) { 
			$arrData['message'] = 'Se editaron los datos correctamente';
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
		if( $this->model_transporte->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}	

}