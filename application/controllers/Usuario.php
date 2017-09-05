<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_usuario')); 
    }

	public function listar_usuario(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_usuario->m_cargar_usuario($paramPaginate);
		$fCount = $this->model_usuario->m_count_usuario($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idusuario'],
					'tipo_usuario' => array(
						'id'=> $row['idtipousuario'],
						'descripcion'=> $row['descripcion_tu']
					),					
					'username' => strtoupper($row['username']),
					'password_view' => strtoupper($row['password_view']),
					'password' => strtoupper($row['password'])  
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
		$this->load->view('usuario/mant_usuario');
	}

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES    	
    	$this->db->trans_start();

		if($allInputs['password']==$allInputs['password_view']){
			if($this->model_usuario->m_registrar($allInputs)) { // registro de elemento
				$arrData['message'] = 'Se registraron los datos correctamente';
				$arrData['flag'] = 1;
			}
		}else{
		$arrData['message'] = 'Las contraseñas no coinciden, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES    	
    	$this->db->trans_start();
		if($this->model_usuario->m_editar($allInputs)) { // edicion de elemento
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	 public function listar_usuario_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_usuario->m_cargar_usuario_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idtipousuario'],
					'descripcion' => strtoupper($row['descripcion_tu']) 
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