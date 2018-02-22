<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BancoEmpresaAdmin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','otros_helper','fechas_helper'));
		$this->load->model(array('model_banco_empresa_admin'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_contactos_este_banco_empresa()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		// var_dump($paramDatos);
		$lista = $this->model_banco_empresa_admin->m_cargar_banco_empresa_admin($paramPaginate,$paramDatos);
		$fCount = $this->model_banco_empresa_admin->m_count_banco_empresa_admin($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 
			if( $row['moneda'] == 'S' ){
				$row['desc_moneda'] = 'S/';
			}
			if( $row['moneda'] == 'D' ){
				$row['desc_moneda'] = 'US$';
			}			
			array_push($arrListado,
				array(

					'id' => $row['idbancoempresaadmin'],		
					'banco' => array(
						'id'=> $row['idbanco'],
						'descripcion'=> strtoupper($row['descripcion_ba'])				
					),	
					'moneda'=> array(
						'id'=> $row['moneda'],
						'descripcion'=> $row['desc_moneda'] 
					),									
					'nombre_comercial' => strtoupper($row['nombre_comercial']),
					'num_cuenta' => strtoupper($row['num_cuenta']),
					'num_cuenta_inter' => strtoupper($row['num_cuenta_inter'])	
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
		$this->load->view('contacto-empresa/mant_contacto_empresa');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
	    
    	$this->db->trans_start();
		if($this->model_banco_empresa_admin->m_registrar($allInputs)) { // registro de contacto 
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

		if($this->model_banco_empresa_admin->m_editar($allInputs)){
			$arrData['message'] = 'Se editaron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['message'] = 'No se pudo anular los datos';
    	$arrData['flag'] = 0;
		if( $this->model_banco_empresa_admin->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	
}
