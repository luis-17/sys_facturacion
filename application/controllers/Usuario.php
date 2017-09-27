<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP. 
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_usuario')); 
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
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
					'idusuario' => $row['idusuario'],
					'tipo_usuario' => array(
						'id'=> $row['idtipousuario'],
						'descripcion'=> $row['descripcion_tu']
					),					
					'username' => strtoupper($row['username']),
					'ult_inicio_sesion' => formatoFechaReporte4($row['ultimo_inicio_sesion'])
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

    	/* VALIDAR QUE SE HAYA REGISTRADO CLAVE */
		if( empty($allInputs['password']) || empty($allInputs['password_view']) ){ 
			$arrData['message'] = 'Los campos de contraseña están vacios.';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}

    	/* VALIDAR QUE LAS CLAVES COINCIDAN */
		if($allInputs['password'] != $allInputs['password_view']){
			$arrData['message'] = 'Las contraseñas no coinciden, inténtelo nuevamente';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}
		/* VALIDAR SI EL USUARIO YA EXISTE */	
    	$fUsuario = $this->model_usuario->m_validar_usuario_username($allInputs['username']);
    	if( !empty($fUsuario) ) {
    		$arrData['message'] = 'El Usuario ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}   	

		$this->db->trans_start();
		if($this->model_usuario->m_registrar($allInputs)) { // registro de usuario 
			$arrData['idusuario'] = GetLastId('idusuario','usuario');
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
		/* VALIDAR SI EL USUARIO YA EXISTE */
    	$fUsuario = $this->model_usuario->m_validar_usuario_username($allInputs['username'],TRUE,$allInputs['idusuario']);
    	if( $fUsuario ) {
    		$arrData['message'] = 'El Usuario ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
    	$this->db->trans_start();

		if($this->model_usuario->m_editar($allInputs)) { // edicion de elemento
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
		if( $this->model_usuario->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 	

	 public function listar_tipo_usuario_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_usuario->m_cargar_tipo_usuario_cbo();
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