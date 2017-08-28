<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sede extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_sede')); 
    }

	 public function listar_sedes_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_sede->m_cargar_sede_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idsede'],
					'descripcion' => strtoupper($row['descripcion_se']),
					'abreviatura' => $row['abreviatura_se']  
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