<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colaborador extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_colaborador','model_usuario')); 

    }

	public function listar_colaboradores(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_colaborador->m_cargar_colaborador($paramPaginate);
		$fCount = $this->model_colaborador->m_count_colaborador($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => trim($row['idcolaborador']),
					'nombres' => strtoupper($row['nombres']),
					'apellidos' => strtoupper($row['apellidos']),
					'num_documento' => $row['num_documento'],
					'telefono' => $row['telefono'],
					'email' => strtoupper($row['email']),
					'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento']),
					'tipo_usuario' => array(
							'id'=> $row['idtipousuario'],
							'descripcion'=> $row['descripcion_tu']
					),	
					'username' => $row['username'],
					'password' => $row['password'],
					'password_view' => $row['password_view'],
					'idusuario' => $row['idusuario']
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
		$this->load->view('colaborador/mant_colaborador');
	}	

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	if( empty($allInputs['idusuario']) ){ 
    		$arrData['message'] = 'Asigne un usuario para el colaborador.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	$this->db->trans_start();
		if($this->model_colaborador->m_registrar($allInputs)) { // registro de elemento
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
		if($this->model_colaborador->m_editar($allInputs)) { // edicion de elemento
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
    	// var_dump($allInputs);exit();
    	$fCotizacion = $this->model_colaborador->m_cargar_cotizacion_colaborador($allInputs);
    	if( !empty($fCotizacion) ){ 
    		$arrData['message'] = 'Ya se a registrado una cotización, no se puede anular'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
		if( $this->model_colaborador->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		if( $this->model_usuario->m_anular($allInputs) ){ 
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 

	 public function listar_colaboradores_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_colaborador->m_cargar_colaborador_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idcolaborador'],
					'descripcion' => strtoupper($row['colaborador']) 
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