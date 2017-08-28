<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoriaElemento extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionFactur.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_categoria_elemento')); 
    }

	 public function listar_categoria_elemento_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_categoria_elemento->m_cargar_categoria_elemento_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idcategoriaelemento'],
					'descripcion' => strtoupper($row['descripcion_cael']),
					'color'=> $row['color_cael']
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