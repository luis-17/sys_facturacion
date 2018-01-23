<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MotivoTraslado extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_motivo_traslado')); 
    }
	 public function listar_motivos_traslado_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_motivo_traslado->m_cargar_motivo_traslado_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idmotivotraslado'],
					'descripcion' => strtoupper($row['descripcion_mt']),
					'orden' => $row['orden_en_guia'],
					'key' => $row['key_motivo_traslado'] 
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