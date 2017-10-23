<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_venta','model_categoria_cliente','model_cliente_persona','model_cliente_empresa','model_configuracion','model_variable_car','model_banco_empresa_admin')); 
        $this->load->library('excel');
    	$this->load->library('Fpdfext');
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
	public function listar_ventas_historial()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_cotizacion->m_cargar_cotizaciones($paramPaginate,$paramDatos); 
		$totalRows = $this->model_cotizacion->m_count_cotizaciones($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['estado_cot'] == 1 ){ // REGISTRADO  
				$objEstado['claseIcon'] = 'fa-file-archive-o';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'POR ENVIAR';
			}
			if( $row['estado_cot'] == 2 ){ // FACTURADO   
				$objEstado['claseIcon'] = 'fa-send';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'ENVIADO';
			}
			$strCliente = NULL;
			// if( $row['tipo_cliente'] == 'E' ){ 
			// 	$strCliente = $row['razon_social_ce']; 
			// }
			// if( $row['tipo_cliente'] == 'P' ){ 
			// 	$strCliente = $row['cliente_persona']; 
			// }
			$strMoneda = NULL;
			if( $row['moneda'] == 'S' ){ 
				$strMoneda = 'SOLES'; 
			}
			if( $row['moneda'] == 'D' ){ 
				$strMoneda = 'DÓLARES'; 
			}
			array_push($arrListado, 
				array(
					'idcotizacion' => $row['idcotizacion'],
					'num_cotizacion' => $row['num_cotizacion'],
					'fecha_registro' => darFormatoDMY($row['fecha_registro']),
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'cliente' => trim($row['cliente_persona_empresa']),
					'colaborador' => strtoupper($row['colaborador']),
					'moneda' => $strMoneda,
					'plazo_entrega' => $row['plazo_entrega'].' días útiles', 
					'validez_oferta' => $row['validez_oferta'].' días útiles', 
					'idformapago' => $row['idformapago'],
					'forma_pago' => strtoupper($row['descripcion_fp']),
					'idsede' => $row['idsede'],
					'sede' => strtoupper($row['descripcion_se']),
					'idempresaadmin' => $row['idempresaadmin'],
					'empresa_admin' => strtoupper($row['razon_social_ea']),
					'idusuario' => $row['idusuario'], 
					'subtotal' => $row['subtotal'], 
					'igv' => $row['igv'], 
					'total' => $row['total'],
					'estado' => $objEstado 
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
	public function listar_detalle_esta_venta()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		if(empty($allInputs['iddetallecotizacion'])){
			$allInputs['iddetallecotizacion'] = NULL;
		}
		$lista = $this->model_cotizacion->m_cargar_detalle_cotizacion_por_id($allInputs['idcotizacion'],$allInputs['iddetallecotizacion']); 
		$arrListado = array(); 
		foreach ($lista as $key => $row) { 
			$arrAux = array( 
				'iddetallecotizacion' => $row['iddetallecotizacion'],
				'idcotizacion' => $row['idcotizacion'],
				'num_cotizacion' => $row['num_cotizacion'],
				'idempresaadmin' => $row['idempresaadmin'],
				'idelemento' => $row['idelemento'], 
				'elemento' => $row['descripcion_ele'], 
				'cantidad' => $row['cantidad'], 
				'precio_unitario' => $row['precio_unitario'], 
				'importe_con_igv' => $row['importe_con_igv'], 
				'importe_sin_igv' => $row['importe_sin_igv'], 
				'excluye_igv' => $row['excluye_igv'], 
				'igv_detalle' => $row['igv_detalle'], 
				'unidad_medida' => array( 
					'id'=> $row['idunidadmedida'],
					'descripcion'=> $row['descripcion_um'] 
				),
				'agrupador_totalizado' => $row['agrupador_totalizado'],
				'caracteristicas' => array() 
			);
			$arrListado[$row['iddetallecotizacion']] = $arrAux; 
		}
		foreach ($lista as $key => $row) { 
			$arrAux2 = array(
				'id'=> $row['iddetallecaracteristica'],
				'idcaracteristica'=> $row['idcaracteristica'],
				'orden'=> $row['orden_car'],
				'descripcion'=> $row['descripcion_car'],
				'valor'=> $row['valor']
			);
			$arrListado[$row['iddetallecotizacion']]['caracteristicas'][$row['iddetallecaracteristica']] = $arrAux2; 
		} 
		$arrData['datos'] = $arrListado; 
    	// $arrData['paginate']['totalRows'] = $totalRows['contador']; 
    	$arrData['message'] = ''; 
    	$arrData['flag'] = 1; 
		if(empty($lista)){ 
			$arrData['flag'] = 0; 
		} 
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function listar_detalle_ventas_historial()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_cotizacion->m_cargar_cotizaciones_detalle($paramPaginate,$paramDatos); 
		$totalRows = $this->model_cotizacion->m_count_cotizaciones_detalle($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $key => $row) { 
			$objEstado = array();
			if( $row['estado_cot'] == 1 ){ // POR ENVIAR 
				$objEstado['claseIcon'] = 'fa-file-archive-o';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'POR ENVIAR';
			}
			if( $row['estado_cot'] == 2 ){ // ENVIADO   
				$objEstado['claseIcon'] = 'fa-send';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'ENVIADO';
			}
			$arrAux = array( 
				'iddetallecotizacion' => $row['iddetallecotizacion'],
				'num_cotizacion' => $row['num_cotizacion'],
				'fecha_emision' => $row['fecha_emision'],
				'idempresaadmin' => $row['idempresaadmin'],
				'idelemento' => $row['idelemento'], 
				'elemento' => $row['descripcion_ele'], 
				'cantidad' => $row['cantidad'], 
				'precio_unitario' => $row['precio_unitario'], 
				'importe_con_igv' => $row['importe_con_igv'], 
				'importe_sin_igv' => $row['importe_sin_igv'], 
				'excluye_igv' => $row['excluye_igv'], 
				'igv_detalle' => $row['igv_detalle'], 
				'unidad_medida' => $row['descripcion_um'], 
				'agrupador_totalizado' => $row['agrupador_totalizado'],
				'estado' => $objEstado, 
				'caracteristicas' => array() 

			);

			$arrListado[] = $arrAux; 
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
	public function ver_popup_busqueda_venta()
	{
		$this->load->view('venta/busq_venta_popup'); 
	}
	public function ver_popup_busqueda_venta_detalle()
	{
		$this->load->view('venta/busq_venta_detalle_popup'); 
	}
	public function generar_numero_serie_correlativo() 
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion(); 
		if( empty($allInputs['serie']) ){ 
			$arrData['message'] = ''; 
    		$arrData['flag'] = 0; 
			$this->output 
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		if( empty($allInputs['tipo_documento_mov']) ){ 
			$arrData['message'] = ''; 
    		$arrData['flag'] = 0; 
			$this->output 
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		// NOMENCLATURA: 
		// SERIE + "-" + X CARACTERES (DINAMICO) + CORRELATIVO 
		// Ejm: 001- 00002
		
		//var_dump($numCotizacion); exit();
		// OBTENER CORRELATIVO ACTUAL. 
		$allInputs['config'] = $fConfig; 
		$fCotizacion = $this->model_cotizacion->m_cargar_ultima_cotizacion_segun_config($allInputs);
		if( empty($fCotizacion) ){
			$numCorrelativo = 1;
		}else{
			$numCorrelativo = substr($fCotizacion['num_cotizacion'], ($numCaracteres * -1), $numCaracteres); 
			//var_dump($numCorrelativo); 
			$numCorrelativo = (int)$numCorrelativo + 1;
			//var_dump($numCorrelativo); exit();
		}
		//var_dump($numCotizacion); exit();
		$numCotizacion .= str_pad($numCorrelativo, $numCaracteres, '0', STR_PAD_LEFT);
	 	$arrDatos['num_cotizacion'] = $numCotizacion; 
	 	//var_dump($numCotizacion); exit();
    	$arrData['datos'] = $arrDatos;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($numCotizacion)){ 
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function registrar()
	{
		ini_set('xdebug.var_display_max_depth', 5);
	    ini_set('xdebug.var_display_max_children', 256);
	    ini_set('xdebug.var_display_max_data', 1024);
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		/* VALIDACIONES */

		if( $allInputs['isRegisterSuccess'] === TRUE ){ 
    		$arrData['message'] = 'Ya se registró esta cotización.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
		if( count($allInputs['detalle']) < 1){
    		$arrData['message'] = 'No se ha agregado ningún elemento';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['sede']['id']) ){
    		$arrData['message'] = 'Debe tener asignado una sede para poder registrar los datos';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( $allInputs['total'] == 'NaN' || empty($allInputs['total']) ){
    		$arrData['message'] = 'No se puedo calcular el precio total de venta. Corrija los montos e intente nuevamente.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	$errorEnBucle = 'no'; 
    	foreach ($allInputs['detalle'] as $key => $row) {
    		if( empty($row['precio_unitario']) ){
    			$errorEnBucle = 'si';
    			break;
    		}
    	}
    	if( $errorEnBucle === 'si' ){ 
    		$arrData['message'] = 'No se puedo calcular el precio total de venta. Corrija los montos e intente nuevamente.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['num_cotizacion']) ){ 
    		$arrData['message'] = 'No se ha generado un COD. DE COTIZACIÓN. Genere la COTIZACIÓN.';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	if( $allInputs['tipo_documento_cliente']['destino_str'] == 'ce' ){ // si es cliente empresa 
    		if( empty($allInputs['contacto']['id']) ){ 
	    		$arrData['message'] = 'No se ha asociado un CONTACTO válido. Asocie el CONTACTO.';
	    		$arrData['flag'] = 0;
	    		$this->output
			    	->set_content_type('application/json')
			    	->set_output(json_encode($arrData));
			    return;
	    	}
    	}
    	
    	$fCotizacion = $this->model_cotizacion->m_cargar_esta_cotizacion_por_codigo($allInputs['num_cotizacion']);
    	if( !empty($fCotizacion) ){ 
    		$arrData['message'] = 'Ya se a registrado una cotización, usando el código <strong>'.$allInputs['num_cotizacion'].'</strong>'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
    	// var_dump($allInputs); exit(); 
    	$this->db->trans_start();
    	if( $allInputs['tipo_documento_cliente']['destino'] == 1 ){ // cliente empresa 
    		$allInputs['tipo_cliente'] = 'E'; // empresa 
    	}
    	if( $allInputs['tipo_documento_cliente']['destino'] == 2 ){ // cliente persona 
    		$allInputs['tipo_cliente'] = 'P'; // persona 
    	} 
		if( $this->model_cotizacion->m_registrar_cotizacion($allInputs) ){ 
			$arrData['idcotizacion'] = GetLastId('idcotizacion','cotizacion');
			foreach ($allInputs['detalle'] as $key => $elemento) { 
				$elemento['idcotizacion'] = $arrData['idcotizacion'];
				if( empty($elemento['agrupacion']) ){ 
					$elemento['agrupacion'] = 1; // por defecto 
				} 
				if( $this->model_cotizacion->m_registrar_detalle_cotizacion($elemento) ){ 
					$arrData['message'] = 'Los datos se registraron correctamente - (no caracteristicas)'; 
					$arrData['flag'] = 1; 
					$arrData['iddetallecotizacion'] = GetLastId('iddetallecotizacion','detalle_cotizacion');
					if( !empty($elemento['caracteristicas']) ){ 
						foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
							if( !empty($caracteristica['valor']) ){ 
								$caracteristica['iddetallecotizacion'] = $arrData['iddetallecotizacion']; 
								if( $this->model_cotizacion->m_registrar_detalle_caracteristica_cotizacion($caracteristica) ){ 
									$arrData['message'] = 'Los datos se registraron correctamente'; 
									$arrData['flag'] = 1; 
									$fVariable = $this->model_variable_car->m_buscar_variable($caracteristica); 
									if( empty($fVariable) ){ 
										// GRABAR COMO UNA VARIABLE 
										$caracteristica['descripcion_vcar'] = $caracteristica['valor'];
										$this->model_variable_car->m_registrar($caracteristica); 
									}
								} 
							} 
						} 
					}
				} 
			} 
		} 
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function imprimir_venta()
	{

	}
}
