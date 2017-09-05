<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuracion extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('config')); 
        $this->load->model(array('model_configuracion')); 
    }

	 public function getConfigSys(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$fConfig = obtener_parametros_configuracion();
    	$arrData['datos'] = $fConfig;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($fConfig)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 
	public function ver_popup_reporte()
	{
		$this->load->view('reportes/popup_reporte_pdf');
	}	
}