<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Servicio extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_elemento')); 
    }

	public function listar_servicio(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		// $paramDatos = $allInputs['datos'];
		$paramDatos['tipo_elemento'] = 'S';
		$lista = $this->model_elemento->m_cargar_elemento($paramPaginate,$paramDatos);
		$fCount = $this->model_elemento->m_count_elemento($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 
			if( $row['tipo_elemento'] == 'P' ){
				$strTipoElemento = 'PRODUCTO';
			}
			if( $row['tipo_elemento'] == 'S' ){
				$strTipoElemento = 'SERVICIO';
			}
			array_push($arrListado,
				array(
					'id' => $row['idelemento'],
					'descripcion_ele' => strtoupper($row['descripcion_ele']),
					'categoria_elemento' => array(
						'id'=> $row['idcategoriaelemento'],
						'descripcion'=> strtoupper($row['descripcion_cael']),
						'color'=> $row['color_cael']
					),
					'tipo_elemento' => array(
						'id'=> $row['tipo_elemento'],
						'descripcion'=> $strTipoElemento 
					),
					'precio_referencial' => $row['precio_referencial']
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
		$this->load->view('servicio/mant_servicio'); 
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	
    	$this->db->trans_start();
    	$allInputs['unidad_medida'] = array(
    		'id'=> NULL 
    	);
		if($this->model_elemento->m_registrar($allInputs)) { // registro de elemento
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
    	$allInputs['unidad_medida'] = array(
    		'id'=> NULL 
    	); 
		if($this->model_elemento->m_editar($allInputs)) { // edicion de elemento
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
		if( $this->model_elemento->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}