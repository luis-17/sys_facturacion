<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sede extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_sede')); 
    }

	public function listar_sede(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_sede->m_cargar_sede($paramPaginate);
		$fCount = $this->model_sede->m_count_sede($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idsede'],
					'descripcion_se' => strtoupper($row['descripcion_se']),
					'direccion_se' => strtoupper($row['direccion_se']),
					'abreviatura_se' => strtoupper($row['abreviatura_se'])  
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

	public function ver_popup_formulario()
	{
		$this->load->view('sede/mant_sede');
	}	

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	
    	$this->db->trans_start();
		if($this->model_sede->m_registrar($allInputs)) { // registro de elemento
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
    	
    	$this->db->trans_start();
		if($this->model_sede->m_editar($allInputs)) { // edicion de elemento
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
		if( $this->model_sede->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	 public function listar_sedes_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_sede->m_cargar_sede_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idsede'],
					'descripcion' => strtoupper($row['descripcion_se']),
					'abreviatura' => $row['abreviatura_se']  
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
	public function listar_sedes_direccion_cbo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_sede->m_cargar_sede_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idsede'],
					'descripcion' => strtoupper($row['direccion_se']) 
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
}