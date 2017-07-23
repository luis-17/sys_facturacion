<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoriaCliente extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        // $this->sessionVP = @$this->session->userdata('sess_vp_'.substr(base_url(),-8,7));
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-8,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_categoria_cliente')); 

    }
	public function listar_categoria_cliente()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_categoria_cliente->m_cargar_categoria_cliente($paramPaginate);
		$totalRows = $this->model_categoria_cliente->m_count_categoria_cliente($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'idcategoriacliente' => $row['idcategoriacliente'],
					'descripcion_cc' => strtoupper($row['descripcion_cc']),
					'estado_cc' => $row['estado_cc']
				)
			);
		}

    	$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $totalRows['contador'];
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	 public function listar_categoria_cliente_cbo(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_categoria_cliente->m_cargar_categoria_cliente_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idcategoriacliente'],
					'descripcion' => strtoupper($row['descripcion_cc']) 
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
