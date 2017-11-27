<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClientePersona extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_cliente_persona','model_categoria_cliente'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_cliente_persona()
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_cliente_persona->m_cargar_cliente_persona($paramPaginate);
		$fCount = $this->model_cliente_persona->m_count_cliente_persona($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			$row['desc_sexo'] = NULL; 
			if( @$row['sexo'] == 'M' ){ 
				$row['desc_sexo'] = 'MASCULINO'; 
			} 
			if( @$row['sexo'] == 'F' ){
				$row['desc_sexo'] = 'FEMENINO';
			}
			array_push($arrListado,
				array(
					'id' => trim($row['idclientepersona']),
					'nombres' => strtoupper($row['nombres']),
					'apellidos' => strtoupper($row['apellidos']),
					'num_documento' => $row['num_documento'],
					'categoria_cliente' => array(
						'id'=> $row['idcategoriacliente'],
						'descripcion'=> $row['descripcion_cc']
					),
					'colaborador' => array(
						'id'=> $row['idcolaborador'],
						'descripcion'=> $row['colaborador']
					),
					'sexo'=> array(
						'id'=> $row['sexo'],
						'descripcion'=> $row['desc_sexo'] 
					),
					'edad' => devolverEdad($row['fecha_nacimiento']),
					'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento']),
					'fecha_nacimiento_str' => formatoFechaReporte3($row['fecha_nacimiento']),
					'telefono_fijo' => $row['telefono_fijo'],
					'telefono_movil' => $row['telefono_movil'],
					'email' => strtoupper($row['email'])
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
		$this->load->view('cliente-persona/mant_clientePersona');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES 
    	if( empty($allInputs['sexo']['id']) ){
    		$arrData['message'] = 'Debe tener sexo para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['nombres']) ){ 
    		$arrData['message'] = 'Debe llenar el campo nombre para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
	    /* VALIDAR SI EL DNI YA EXISTE */ 
    	$fCliente = $this->model_cliente_persona->m_validar_cliente_persona_num_documento($allInputs['num_documento']); 
    	if( !empty($fCliente) ) {
    		$arrData['message'] = 'El Documento de Identidad ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
    	$this->db->trans_start();
		if($this->model_cliente_persona->m_registrar($allInputs)) { // registro de cliente empresa 
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
    	if( empty($allInputs['sexo']['id']) ){
    		$arrData['message'] = 'Debe tener sexo para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['nombres']) ){ 
    		$arrData['message'] = 'Debe llenar el campo nombre para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
		/* VALIDAR SI EL RUC YA EXISTE */
    	$fCliente = $this->model_cliente_persona->m_validar_cliente_persona_num_documento($allInputs['num_documento'],TRUE,$allInputs['id']);
    	if( $fCliente ) {
    		$arrData['message'] = 'El RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
		if($this->model_cliente_persona->m_editar($allInputs)){
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
		if( $this->model_cliente_persona->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	
}