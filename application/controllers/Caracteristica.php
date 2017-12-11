<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Caracteristica extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_caracteristica')); 
    }

	public function listar_caracteristica(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_caracteristica->m_cargar_caracteristica($paramPaginate);
		$fCount = $this->model_caracteristica->m_count_caracteristica($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idcaracteristica'],
					'descripcion_car' => strtoupper($row['descripcion_car']),
					'orden_car' => (int)$row['orden_car']			
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
	public function listar_caracteristicas_agregar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_caracteristica->m_cargar_caracteristica_agregar();
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'idcaracteristica' => $row['idcaracteristica'], 
					'id' => $row['idcaracteristica'],
					'descripcion' => strtoupper($row['descripcion_car']), 
					'orden'=> (int)$row['orden_car'],
					'valor'=> NULL 
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
	public function ver_popup_formulario()
	{
		$this->load->view('caracteristica/mant_caracteristica');
	}	
	public function ver_popup_agregar_caracteristica()
	{
		$this->load->view('caracteristica/agregar_caracteristica');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	if(!is_numeric($allInputs['orden_car'])){ 
    		$arrData['message'] = 'Digite número , no letras'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}     	
    	$this->db->trans_start();
		if($this->model_caracteristica->m_registrar($allInputs)) { 
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
    	if(!is_numeric($allInputs['orden_car'])){ 
    		$arrData['message'] = 'Digite número , no letras'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}        	
    	$this->db->trans_start();
		if($this->model_caracteristica->m_editar($allInputs)) { 
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
		if( $this->model_caracteristica->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}	

}