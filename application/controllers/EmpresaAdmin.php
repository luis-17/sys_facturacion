<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpresaAdmin extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_empresa_admin')); 
    }

	public function listar_empresa_admin(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_empresa_admin->m_cargar_empresa_admin($paramPaginate);
		$fCount = $this->model_empresa_admin->m_count_empresa_admin($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			if( $row['moneda'] == 'S' ){
				$row['desc_moneda'] = 'S/.';
			}
			if( $row['moneda'] == 'D' ){
				$row['desc_moneda'] = 'US$';
			}			
			array_push($arrListado,
				array(

					'id' => $row['idbancoempresaadmin'],		
					'cuenta_bancaria' => array(
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
		$this->load->view('empresa-admin/mant_empresaAdmin');
	}	

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	
    	$this->db->trans_start();
		if($this->model_empresa_admin->m_registrar($allInputs)) { // registro de elemento
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
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	
    	$this->db->trans_start();
		if($this->model_empresa_admin->m_editar($allInputs)) { // edicion de elemento
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
		if( $this->model_empresa_admin->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}	

}