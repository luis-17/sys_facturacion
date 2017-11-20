<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoDocumentoCliente extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        // $this->sessionVP = @$this->session->userdata('sess_vp_'.substr(base_url(),-20,7));
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_tipo_documento_cliente')); 

    }
	public function listar_tipo_documento_cliente_cbo(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_tipo_documento_cliente->m_cargar_tipo_documento_cliente_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => (int)$row['idtipodocumentocliente'],
					'destino' => $row['destino_tdc'],
					'destino_str' => ($row['destino_tdc'] == 1) ? 'ce' : 'cp',
					'descripcion' => strtoupper($row['abreviatura_tdc']),
					'descripcion_larga' => strtoupper($row['descripcion_tdc']) 
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
