<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioEmpresaAdmin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','otros_helper','fechas_helper'));
		$this->load->model(array('model_usuario_empresa_admin','model_empresa_admin'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_usuario_empresa_admin(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		// var_dump($allInputs);exit();
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_usuario_empresa_admin->m_cargar_usuario_empresa_admin($paramPaginate,$paramDatos);
		$fCount = $this->model_usuario_empresa_admin->m_count_usuario_empresa_admin($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 	
			array_push($arrListado,
				array(
					'id' => $row['idusuarioempresaadmin'],	
					'razon_social' => strtoupper($row['razon_social']),
					'ruc' => strtoupper($row['ruc']),
					'select_por_defecto' => $row['select_por_defecto'],		
					'empresa' => array(
						'id'=> $row['idempresaadmin'],
						'descripcion'=> strtoupper($row['razon_social'])				
					),	
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

	public function ver_popup_usuario_empresa()
	{
		$this->load->view('usuario/mant_usuarioEmpresaAdmin');
	}

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	if( $allInputs['empresa']['id']==0){ 
    		$arrData['message'] = 'Seleccione una empresa'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}     	 
    	$fEmpresa = $this->model_usuario_empresa_admin->m_cargar_empresa($allInputs);
    	if( !empty($fEmpresa) ){ 
    		$arrData['message'] = 'Ya se a registrado esta empresa, no se puede registrar'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 

    	$this->db->trans_start();	
		if( $this->model_usuario_empresa_admin->m_registrar($allInputs) ){ 
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
		if( $this->model_usuario_empresa_admin->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function editar_selectpordefecto()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// var_dump($allInputs);exit();
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$fSelect = $this->model_usuario_empresa_admin->m_cargar_selectpordefecto($allInputs);
    	if( $fSelect['select_por_defecto']==1){ 
    		$arrData['message'] = 'Ya se encuentra registrado, seleccione otro'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 

		$this->db->trans_complete();
		if($this->model_usuario_empresa_admin->m_editar_selectpordefecto($allInputs)) {
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}		
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
