<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_venta','model_categoria_cliente','model_cliente_persona','model_cliente_empresa','model_configuracion','model_variable_car','model_banco_empresa_admin','model_serie')); 
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
		$lista = $this->model_venta->m_cargar_ventas($paramPaginate,$paramDatos); 
		$totalRows = $this->model_venta->m_count_ventas($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['estado_movimiento'] == 1 ){ // REGISTRADO 
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'REGISTRADO';
			}
			if( $row['estado_movimiento'] == 0 ){ // ANULADO 
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'ANULADO';
			}
			$strMoneda = NULL;
			if( $row['moneda'] == 'S' ){ 
				$strMoneda = 'SOLES'; 
			}
			if( $row['moneda'] == 'D' ){ 
				$strMoneda = 'DÓLARES'; 
			}
			array_push($arrListado, 
				array(
					'idmovimiento' => $row['idmovimiento'],
					'tipo_cliente' => $row['tipo_cliente'],
					'fecha_registro' => darFormatoDMY($row['fecha_registro']),
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'idtipodocumentomov'=> $row['idtipodocumentomov'],
					'descripcion_tdm'=> $row['descripcion_tdm'],
					'serie'=> $row['numero_serie'],
					'correlativo'=> $row['numero_correlativo'],
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
		$lista = $this->model_venta->m_cargar_detalle_cotizacion_por_id($allInputs['idcotizacion'],$allInputs['iddetallecotizacion']); 
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
		$lista = $this->model_venta->m_cargar_ventas_detalle($paramPaginate,$paramDatos); 
		$totalRows = $this->model_venta->m_count_ventas_detalle($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $key => $row) { 
			$objEstado = array();
			if( $row['estado_movimiento'] == 1 ){ // REGISTRADO  
				$objEstado['claseIcon'] = 'fa-file-archive-o';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'REGISTRADO';
			}
			if( $row['estado_movimiento'] == 0 ){ // ANULADO   
				$objEstado['claseIcon'] = 'fa-send';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'ANULADO';
			}
			$arrAux = array( 
					'iddetallemovimiento' => $row['iddetallemovimiento'],	
					'tipo_cliente' => $row['tipo_cliente'],
					'fecha_registro' => darFormatoDMY($row['fecha_registro']),
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'idtipodocumentomov'=> $row['idtipodocumentomov'],
					'descripcion_tdm'=> $row['descripcion_tdm'],
					'serie'=> $row['numero_serie'],
					'correlativo'=> $row['numero_correlativo'],
					'cliente' => trim($row['cliente_persona_empresa']),
					'categoria_elemento' => array(
							'id'=> $row['idcategoriaelemento'],
							'descripcion'=> strtoupper($row['descripcion_cael'])				
					),	
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
					'idempresaadmin' => $row['idempresaadmin'],
					'idelemento' => $row['idelemento'], 
					'elemento' => $row['descripcion_ele'], 
					'cantidad' => $row['cantidad'], 
					'precio_unitario' => $row['precio_unitario'], 
					'importe_con_igv' => $row['importe_con_igv'], 
					'importe_sin_igv' => $row['importe_sin_igv'], 
					'igv_detalle' => $row['igv_detalle'], 
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
			$arrData['message'] = 'No ha seleccionado serie'; 
    		$arrData['flag'] = 0; 
			$this->output 
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		if( empty($allInputs['tipo_documento_mov']) ){ 
			$arrData['message'] = 'No ha seleccionado comprobante'; 
    		$arrData['flag'] = 0; 
			$this->output 
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		if( empty($allInputs['tipo_documento_mov']['id']) ){
			$arrData['message'] = 'Seleccione un comprobante.'; 
    		$arrData['flag'] = 0; 
			$this->output 
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		// NOMENCLATURA: 
		// SERIE + "-" + X CARACTERES (DINAMICO) + CORRELATIVO 
		// Ejm: 001- 00002
		
		$numCaracteres = $fConfig['cant_caracteres_correlativo_compr']; 
		$numSerie = $allInputs['serie']['descripcion']; 
		// OBTENER CORRELATIVO ACTUAL. 
		$fCorrelativo = $this->model_serie->m_validar_serie_correlativo_existe($allInputs['serie']['id'],$allInputs['tipo_documento_mov']['id']); 
		if( empty($fCorrelativo) ){ 
			$arrData['message'] = 'No se ha configurado los números de serie para el comprobante elegido'; 
    		$arrData['flag'] = 0; 
			$this->output 
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		} 

		$numCorrelativo = str_pad(($fCorrelativo['correlativo_actual'] + 1), $numCaracteres, '0', STR_PAD_LEFT); 
		$numSerieCorrelativo = $allInputs['serie']['descripcion'].'-'.$numCorrelativo; 

	 	$arrDatos['num_serie_correlativo'] = $numSerieCorrelativo; 
	 	$arrDatos['num_serie'] = $numSerie; 
	 	$arrDatos['num_correlativo'] = $numCorrelativo; 
	 	//var_dump($numCotizacion); exit();
    	$arrData['datos'] = $arrDatos;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		
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
    		$arrData['message'] = 'Ya se registró esta venta.'; 
    		$arrData['flag'] = 0; 
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
		if( count($allInputs['detalle']) < 1 ){
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
    	if( empty($allInputs['num_serie_correlativo']) ){ 
    		$arrData['message'] = 'No se ha generado un CORRELATIVO.';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	// if( $allInputs['tipo_documento_cliente']['destino_str'] == 'ce' ){ // si es cliente empresa 
    	// 	if( empty($allInputs['contacto']['id']) ){ 
	    // 		$arrData['message'] = 'No se ha asociado un CONTACTO válido. Asocie el CONTACTO.';
	    // 		$arrData['flag'] = 0;
	    // 		$this->output
			  //   	->set_content_type('application/json') 
			  //   	->set_output(json_encode($arrData));
			  //   return;
	    // 	}
    	// }
    	/* Validar el numero de serie + numero de correlativo tienen que ser correlativo */ 
    	$fConfig = obtener_parametros_configuracion(); 
    	$numCaracteres = $fConfig['cant_caracteres_correlativo_compr']; 
    	$numeroDeSerieValido = FALSE; 
    	$numSerie = $allInputs['serie']['descripcion']; 
		// OBTENER CORRELATIVO ACTUAL. 
		$fCorrelativo = $this->model_serie->m_validar_serie_correlativo_existe($allInputs['serie']['id'],$allInputs['tipo_documento_mov']['id']); 
    	$numCorrelativo = str_pad(($fCorrelativo['correlativo_actual'] + 1), $numCaracteres, '0', STR_PAD_LEFT);
    	$numSerieCorrelativoBD = $numSerie.'-'.$numCorrelativo; 
    	if( $numSerieCorrelativoBD === $allInputs['num_serie_correlativo'] ){ 
    		$numeroDeSerieValido = TRUE; 
    	}
    	if( !$numeroDeSerieValido ){ 
    		$arrData['message'] = 'El número de serie/correlativo es erróneo, por favor refresque el formulario <span class="icon-bg"><i class="ti ti-reload"></i></span>';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	$fVenta = $this->model_venta->m_validar_venta_por_correlativo($allInputs['num_serie'],$allInputs['num_correlativo'],$allInputs['tipo_documento_mov']['id']); 
    	if( !empty($fVenta) ){ 
    		$arrData['message'] = 'Ya se a registrado una venta usando el correlativo <strong>'.$allInputs['num_serie'].'-'.$allInputs['num_correlativo'].'</strong>'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
    	$this->db->trans_start();
    	if( $allInputs['tipo_documento_cliente']['destino'] == 1 ){ // cliente empresa 
    		$allInputs['tipo_cliente'] = 'E'; // empresa 
    	}
    	if( $allInputs['tipo_documento_cliente']['destino'] == 2 ){ // cliente persona 
    		$allInputs['tipo_cliente'] = 'P'; // persona 
    	} 
		if( $this->model_venta->m_registrar_venta($allInputs) ){ 
			$arrData['idmovimiento'] = GetLastId('idmovimiento','movimiento');
			foreach ($allInputs['detalle'] as $key => $elemento) { 
				$elemento['idmovimiento'] = $arrData['idmovimiento'];
				if( empty($elemento['agrupacion']) ){ 
					$elemento['agrupacion'] = 1; // por defecto 
				} 
				if( $this->model_venta->m_registrar_detalle_venta($elemento) ){ 
					$arrData['message'] = 'Los datos se registraron correctamente - (no caracteristicas)'; 
					$arrData['flag'] = 1; 
					$arrData['iddetalleventa'] = GetLastId('iddetallemovimiento','detalle_movimiento');
					if( !empty($elemento['caracteristicas']) ){ 
						foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
							if( !empty($caracteristica['valor']) ){ 
								$caracteristica['iddetalleventa'] = $arrData['iddetalleventa']; 
								if( $this->model_venta->m_registrar_detalle_caracteristica_venta($caracteristica) ){ 
									$arrData['message'] = '- Los datos se registraron correctamente'; 
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
			// ACTUALIZAR NUMERO DE SERIE 
			$arrDataSC = array( 
				'tipo_documento_mov'=> $allInputs['tipo_documento_mov'], 
				'serie'=> $allInputs['serie'] 
			);
			if( $this->model_serie->actualizar_serie_correlativo_por_movimiento($arrDataSC) ){ 
				$arrData['message'] .= '<br /> - Se actualizó el correlativo correctamente'; 
				$arrData['flag'] = 1; 
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
