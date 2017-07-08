<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClienteEmpresa extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_cliente_empresa','model_categoria_cliente'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		//$this->sessionHospital = @$this->session->userdata('sess_vs_'.substr(base_url(),-8,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function lista_cliente_empresa()
	{
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_cliente_empresa->m_cargar_cliente_empresa($paramPaginate);
		$fCount = $this->model_cliente_empresa->m_count_cliente_empresa($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) {
			// $fechaNacimiento = $row['fecha_nacimiento']; descripcion_cc
			array_push($arrListado,
				array(
					'id' => trim($row['idclienteempresa']),
					'nombre_comercial' => strtolower($row['nombre_comercial']),
					'nombre_corto' => strtolower($row['nombre_corto']),
					'razon_social' => strtolower($row['razon_social']),
					'categoria' => strtolower($row['descripcion_cc']),
					'ruc' => $row['ruc'],
					'representante_legal' => $row['representante_legal'],
					'direccion_legal' => $row['direccion_legal'],
					'telefono' => $row['telefono']
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
		$this->load->view('cliente/cliente_formView');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
	    /* VALIDAR SI EL RUC YA EXISTE */ 
    	$fCliente = $this->model_cliente_empresa->m_validar_cliente_empresa_num_documento($allInputs['ruc']);
    	if( !empty($fCliente) ) {
    		$arrData['message'] = 'El RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
	   	//}
    	$this->db->trans_start();
		if($this->model_cliente_empresa->m_registrar($allInputs)) { // registro de cliente empresa 
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
		/* VALIDAR SI EL RUC YA EXISTE */
    	$fCliente = $this->model_cliente_empresa->m_validar_cliente_empresa_num_documento($allInputs['ruc'],TRUE,$allInputs['idempresacliente']);
    	if( $fCliente ) {
    		$arrData['message'] = 'El RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
		if($this->model_cliente_empresa->m_editar($allInputs)){
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
    	foreach ($allInputs as $row) {
			if( $this->model_cliente_empresa->m_anular($row['idempresacliente']) ){ 
				$arrData['message'] = 'Se anularon los datos correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	
}