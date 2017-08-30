<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Elemento extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_elemento'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_elementos_autocomplete()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$allInputs['limite'] = 15;
		$lista = $this->model_elemento->m_cargar_elementos_limite($allInputs);
		$hayStock = true;
		$arrListado = array();

		foreach ($lista as $row) { 
			if( $row['tipo_elemento'] == 'P' ){
				$strTipoElemento = 'PRODUCTO';
			}
			if( $row['tipo_elemento'] == 'S' ){
				$strTipoElemento = 'SERVICIO';
			}
			// $medicamento_stock = strtoupper($row['medicamento']) . ' | <span class="text-info">STOCK: ' . @$row['stock'] . ' UND.</span>';
			array_push($arrListado,
				array(
					'id' => $row['idelemento'],
					'elemento' => strtoupper($row['descripcion_ele']),
					'categoria_elemento' => array(
						'id'=> $row['idcategoriaelemento'],
						'descripcion'=> strtoupper($row['descripcion_cael']),
						'color'=> $row['color_cael']
					),
					'unidad_medida' => array(
						'id'=> $row['idunidadmedida'],
						'descripcion'=> strtoupper($row['descripcion_um'])
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
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData)); 
	}
	public function buscar_elemento_para_lista()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramDatos = @$allInputs['datos'];
		$paramPaginate = $allInputs['paginate'];
		$arrListado = array();
		$fCount = array();
		$lista = $this->model_elemento->m_cargar_elemento($paramPaginate,$paramDatos);
		$fCount = $this->model_elemento->m_count_elemento($paramPaginate,$paramDatos);
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
					'elemento' => strtoupper($row['descripcion_ele']),
					'categoria_elemento' => array(
						'id'=> $row['idcategoriaelemento'],
						'descripcion'=> strtoupper($row['descripcion_cael']),
						'color'=> $row['color_cael']
					),
					'unidad_medida' => array(
						'id'=> $row['idunidadmedida'],
						'descripcion'=> strtoupper($row['descripcion_um'])
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
	public function ver_popup_busqueda_elementos()
	{
		$this->load->view('elemento/busq_elemento_popup');
	}
}