<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoDocumento extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_tipo_documento')); 
    }

	public function listar_tipo_documento(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_tipo_documento->m_cargar_tipo_documento($paramPaginate);
		$listaSe = $this->model_tipo_documento->m_cargar_tipo_documento_serie();
		// var_dump($listaSe);exit();
		$fCount = $this->model_tipo_documento->m_count_tipo_documento($paramPaginate);

	// 	$arrListado = array();
	// 	$arrListadoOrden = array();
	// 	foreach ($lista as $row) {
	// 		array_push($arrListadoOrden, 
	// 			array(
	// 				'id' => $row['idtipodocumentomov'],
	// 				'descripcion_tdm' => strtoupper($row['descripcion_tdm']),	
	// 				'descripcion_ser' => $row['descripcion_ser'] 
	// 			)
	// 		);
	// 	}
		
	// 	$arrGroupBy = array();
	// 	foreach ($arrListadoOrden as $key => $row) { 
	// 		$otherRow = array(
	// 			'id' => $row['id'],
	// 			'descripcion_tdm' => strtoupper($row['descripcion_tdm']),
	// 			'descripcion_ser' => $row['descripcion_ser']
	// 			//'detalle' => array()
	// 		);
	// 		$arrGroupBy[$row['id']] = $otherRow;
	// 	}
	// 	//var_dump($arrGroupBy); exit();
	// 	foreach ($arrGroupBy as $key => $row) { 
	// 		foreach ($arrListadoOrden as $keyDet => $rowDet) { 
	// 			if( $rowDet['id'] == $row['id'] ){ 
	// 				$arrGroupBy[$key][$rowDet['descripcion_ser']] = (int)$rowDet['descripcion_ser'];
	// 			}
	// 		}
	// 	}

	// $arrListado = array_values($arrGroupBy);



		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idtipodocumentomov'],
					'descripcion_tdm' => strtoupper($row['descripcion_tdm']),
					'porcentaje_imp' => strtoupper($row['porcentaje_imp']) ,
					'abreviatura_tdm' => strtoupper($row['abreviatura_tdm']) 
				)
			);
		}

		$arrListado2 = array();
		$arrListadoSe = array();
		foreach ($listaSe as $row) {
			array_push($arrListadoSe, 
				array(
					'id' => $row['idempresaadmin'],
					'descripcion_ser' => $row['descripcion_ser']
				)
			);
		}
		
		$arrGroupBy = array();
		foreach ($arrListadoSe as $key => $row) { 
			$otherRow = array(
				'id' => $row['id'],
				'descripcion_ser' => $row['descripcion_ser']
				//'detalle' => array()
			);
			$arrGroupBy[$row['id']] = $otherRow;
		}
		//var_dump($arrGroupBy); exit();
		foreach ($arrGroupBy as $key => $row) { 
			foreach ($arrListadoSe as $keyDet => $rowDet) { 
				if( $rowDet['id'] == $row['id'] ){ 
					$arrGroupBy[$key][$rowDet['descripcion_ser']] = (int)$rowDet['descripcion_ser'];
				}
			}
		}
		//var_dump("<pre>",$arrGroupBy); exit();
		$arrListado2 = array_values($arrGroupBy);

// var_dump("<pre>",$arrListado); exit();


    	$arrData['datos'] = $arrListado;
    	$arrData['datos2'] = $arrListado2;
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
		$this->load->view('tipo-documento/mant_tipoDocumento');
	}	

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	
    	$this->db->trans_start();
		if($this->model_tipo_documento->m_registrar($allInputs)) { // registro de elemento
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
		if($this->model_tipo_documento->m_editar($allInputs)) { // edicion de elemento
			$arrData['message'] = 'Se editaron los datos correctamente';
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
		if( $this->model_tipo_documento->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}	

}