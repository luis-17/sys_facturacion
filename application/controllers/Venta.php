<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_venta','model_categoria_cliente','model_cliente_persona','model_cliente_empresa','model_configuracion',
        	'model_variable_car','model_banco_empresa_admin','model_serie','model_nota_pedido','model_caracteristica','model_tipo_documento_mov')); 
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
					'identify_trunc'=> $row['idmovimiento'],
					'tipo_cliente' => $row['tipo_cliente'],
					'fecha_registro' => darFormatoDMY($row['fecha_registro']),
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'idtipodocumentomov'=> $row['idtipodocumentomov'],
					'descripcion_tdm'=> strtoupper($row['descripcion_tdm']),
					'serie'=> $row['numero_serie'],
					'correlativo'=> $row['numero_correlativo'],
					'cliente' => trim($row['cliente_persona_empresa']),
					'colaborador_gen' => strtoupper($row['colaborador_gen']),
					'colaborador_asig'=> strtoupper($row['colaborador_asig']),
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
					'estado_movimiento' => $row['estado_movimiento'],
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
	public function obtener_esta_venta()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		// var_dump($allInputs);exit();
		$fConfig = obtener_parametros_configuracion();
		$idventa = $allInputs['identify']; 
		$fila = $this->model_venta->m_cargar_venta_por_id($idventa);
		$detalleLista = $this->model_venta->m_cargar_detalle_venta_por_id($idventa);
		// var_dump($detalleLista);exit();
		$rowEstadoId = $fila['estado_movimiento']; 
		if( $fila['estado_movimiento'] == 0 ){ // por enviar
			$rowEstadoDescripcion = 'ANULADO'; 
		}
		if( $fila['estado_movimiento'] == 1 ){ // por enviar
			$rowEstadoDescripcion = 'POR ENVIAR'; 
		}
		if( $fila['estado_movimiento'] == 2 ){ // enviado
			$rowEstadoDescripcion = 'ENVIADO'; 
		}
		if($fila['moneda'] == 'S'){
			$strIdMoneda = 1; 
			$strDescripcion = 'S/';
			$strMoneda = $fila['moneda'];
		}
		if($fila['moneda'] == 'D'){
			$strIdMoneda = 2; 
			$strDescripcion = 'US$'; 
			$strMoneda = $fila['moneda']; 
		}
		$arrListadoDetalle = array();
		$arrListado = array( 
			'tipo_documento_cliente'=> array(
				'id'=> $fila['idtipodocumentocliente'],
				'descripcion'=> $fila['tipo_documento_abv']
			),
			'serie'=> array(
				'id'=> $fila['idserie'],
				'descripcion'=> $fila['numero_serie']
			),			
			'tipo_documento_mov'=> array(
				'id'=> $fila['idtipodocumentomov'],
				'descripcion'=> $fila['descripcion_tdm']
			),
			'num_serie_correlativo'=> $fila['numero_serie'].' - '.$fila['numero_correlativo'],
			'idmovimiento'=> $fila['idmovimiento'],
			'num_nota_pedido'=> $fila['num_nota_pedido'],
			'sede'=> array(
				'id'=> $fila['idsede'],
				'descripcion'=> strtoupper($fila['descripcion_se']),
			),
			'fecha_registro'=> darFormatoDMY($fila['fecha_registro']),
			'fecha_emision'=> darFormatoDMY($fila['fecha_emision']),
			'estado_venta'=> array(
				'id'=> (int)$rowEstadoId,
				'descripcion'=> $rowEstadoDescripcion 
			),
			'moneda'=> array( 
				'id'=> (int)$strIdMoneda,
				'descripcion'=> strtoupper($strDescripcion),
				'str_moneda'=> strtoupper($strMoneda)
			), 
			'forma_pago'=> array(
				'id'=> (int)$fila['idformapago'],
				'descripcion'=> strtoupper($fila['descripcion_fp']),
				'modo'=> $fila['modo_fp']
			),
			// 'colaborador'=> array(
			// 	'id'=> (int)$fila['idcolaborador'],
			// 	'colaborador'=> strtoupper($fila['colaborador']) 
			// ),
			'contacto'=> strtoupper($fila['contacto']),
			'incluye_tras_prov'=> (int)$fila['incluye_traslado_prov'],
			'incluye_entr_dom'=> (int)$fila['incluye_entrega_domicilio'],
			'plazo_entrega'=> $fila['plazo_entrega'],
			'validez_oferta'=> $fila['validez_oferta'],
			'modo_igv'=> (int)$fila['modo_igv'],
			'subtotal'=> number_format($fila['subtotal'],$fConfig['num_decimal_total_key'],'.',''),
			'igv'=> number_format($fila['igv'],$fConfig['num_decimal_total_key'],'.',''),
			'total'=> number_format($fila['total'],$fConfig['num_decimal_total_key'],'.',''),
			'cliente' => array(),
			//'detalle' => array(),
			'temporal' => array( 
				'cantidad'=> 1,
				'unidad_medida'=> array(),
				'caracteristicas'=> array()
			)
		); 
		if( $fila['tipo_cliente'] === 'E' ){ 
			$arrListado['num_documento'] = $fila['ruc_ce'];
			$arrListado['cliente'] = array(
				'id' => $fila['idclienteempresa'],
				'idclienteempresa' => $fila['idclienteempresa'],
				'cliente' => strtoupper($fila['razon_social_ce']),
				'tipo_cliente' => 'ce',
				'nombre_comercial' => strtoupper($fila['nombre_comercial_ce']),
				'nombre_corto' => strtoupper($fila['nombre_corto']),
				'razon_social' => strtoupper($fila['razon_social_ce']),
				'telefono_contacto'=> $fila['telefono_fijo'],
				'anexo_contacto'=> $fila['anexo'],
				'ruc' => $fila['ruc_ce'],
				'num_documento' => $fila['ruc_ce'],
				'representante_legal' => $fila['representante_legal'],
				'dni_representante_legal' => $fila['dni_representante_legal'],
				'direccion_legal' => $fila['direccion_legal'],
				// 'telefono' => $fila['telefono']
			);
		}
		if( $fila['tipo_cliente'] === 'P' ){ 
			$arrListado['num_documento'] = $fila['num_documento'];
			if( $fila['sexo'] == 'M' ){
				$fila['desc_sexo'] = 'MASCULINO';
			}
			if( $fila['sexo'] == 'F' ){
				$fila['desc_sexo'] = 'FEMENINO';
			}
			$arrListado['cliente'] = array( 
				'id' => $fila['idclientepersona'],
				'idclientepersona' => $fila['idclientepersona'],
				'nombres' => strtoupper($fila['nombres']),
				'apellidos' => strtoupper($fila['apellidos']),
				'cliente' => strtoupper($fila['nombres'].' '.$fila['apellidos']),
				'tipo_cliente' => 'cp',
				'num_documento' => $fila['num_documento'],
				// 'categoria_cliente' => array(
				// 	'id'=> $fila['idcategoriacliente'],
				// 	'descripcion'=> $fila['descripcion_cc'] telefono_contacto
				// ),
				// 'colaborador' => array(
				// 	'id'=> $fila['idcolaborador'],
				// 	'descripcion'=> $fila['colaborador']
				// ),
				'sexo'=> array(
					'id'=> $fila['sexo'],
					'descripcion'=> $fila['desc_sexo'] 
				),
				'edad' => devolverEdad($fila['fecha_nacimiento']),
				'fecha_nacimiento' => darFormatoDMY($fila['fecha_nacimiento']),
				'fecha_nacimiento_str' => formatoFechaReporte3($fila['fecha_nacimiento']),
				'telefono_fijo' => $fila['telefono_fijo'],
				'telefono_movil' => $fila['telefono_movil'],
				'email' => $fila['email']
			);
		}

		foreach ($detalleLista as $key => $row) { 
			$arrAux = array( 
				'iddetallemovimiento' => $row['iddetallemovimiento'],
				'idmovimiento' => $row['idmovimiento'],
				// 'num_cotizacion' => $row['num_cotizacion'],
				'idempresaadmin' => $row['idempresaadmin'],
				'idelemento' => $row['idelemento'], 
				'descripcion'=> $row['descripcion_ele'], 
				'elemento' => $row['descripcion_ele'], 
				'cantidad' => $row['cantidad'], 
				'precio_unitario' => number_format($row['precio_unitario'],$fConfig['num_decimal_precio_key'],'.',''), 
				'importe_con_igv' => number_format($row['importe_con_igv'],$fConfig['num_decimal_total_key'],'.',''), 
				'importe_sin_igv' => number_format($row['importe_sin_igv'],$fConfig['num_decimal_total_key'],'.',''), 
				'excluye_igv' => $row['excluye_igv'], 
				'igv_detalle' => number_format($row['igv_detalle'],$fConfig['num_decimal_total_key'],'.',''), // agrupacion
				'igv' => $row['igv_detalle'], 
				'unidad_medida' => array( 
					'id'=> $row['idunidadmedida'],
					'descripcion'=> $row['descripcion_um'] 
				),
				'agrupacion' => (int)$row['agrupador_totalizado'],
				'caracteristicas' => array() 
			);
			$arrListadoDetalle[$row['iddetallemovimiento']] = $arrAux; 
		}
		foreach ($detalleLista as $key => $row) { 
			$arrAux2 = array(
				'iddetallecaracteristica'=> $row['iddetallecaracteristica'],
				'idcaracteristica'=> $row['idcaracteristica'],
				'orden'=> $row['orden_car'],
				'descripcion'=> $row['descripcion_car'],
				'valor'=> $row['valor']
			);
			if( !empty($row['iddetallecaracteristica']) ){ 
				$arrListadoDetalle[$row['iddetallemovimiento']]['caracteristicas'][$row['iddetallecaracteristica']] = $arrAux2; 
				$arrListadoDetalle[$row['iddetallemovimiento']]['caracteristicas'] = array_values($arrListadoDetalle[$row['iddetallemovimiento']]['caracteristicas']);
			}

		} 
		// agregar caracteristicas sin valor 
		$arrCaractsAll = array();
		$listaCaracteristicas = $this->model_caracteristica->m_cargar_caracteristica_agregar(); 
		foreach ($listaCaracteristicas as $key => $row) {
			$arrAux = array(
				'iddetallecaracteristica'=> NULL,
				'idcaracteristica'=> $row['idcaracteristica'],
				'id'=> $row['idcaracteristica'],
				'orden'=> $row['orden_car'],
				'descripcion'=> $row['descripcion_car'],
				'valor'=> NULL 
			);
			$arrCaractsAll[] = $arrAux;
		}
		$arrCaractsSelect = array();
		foreach ($arrListadoDetalle as $key => $item) { 
			foreach ($arrCaractsAll as $key3 => $caracAll) { 
				$caracIgual = FALSE;
				foreach ($item['caracteristicas'] as $key2 => $carac) { 
					if( $caracAll['idcaracteristica'] == $carac['idcaracteristica'] ){ 
						$caracIgual = TRUE;
					}
				}
				if( $caracIgual === FALSE ){ 
					$arrListadoDetalle[$key]['caracteristicas'][] = $caracAll;
					// $arrCaractsSelect[] = $caracAll;
				}
			}
		}
		// print_r($arrListadoDetalle); exit(); 
		// $resultado = array_merge_recursive($m1, $m2);
		// reindexado 
		$arrListadoDetalle = array_values($arrListadoDetalle);
		$arrData['datos'] = $arrListado; 
		$arrData['detalle'] = $arrListadoDetalle; 

		$arrData['flag'] = 1; 
		if(empty($fila)){ 
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
		// var_dump($allInputs); exit(); 
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
    	if( empty($allInputs['colaborador']['id']) ){
    		$arrData['message'] = 'Debe tener asignado un colaborador para poder registrar los datos';
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
    	
		foreach ($allInputs['detalle'] as $key => $row) { 
			if( !empty($row['iddetallenotapedido']) ){ 
	    		$verify =$this->model_nota_pedido->m_verificar_existe_item_nota_pedido($row['iddetallenotapedido'],$row['idnotapedido']) ;
	    		if(empty($verify) ){
					$arrData['message'] = 'No se puede registrar la venta porque hay items que ya han sido usados';
					$arrData['message'] .= '<br /> - Vuelva cargar nuevamente'; 
		    		$arrData['flag'] = 0;
		    		$this->output
					    ->set_content_type('application/json')
					    ->set_output(json_encode($arrData));
				    return;
		    	}	
	    	}
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
				// var_dump($elemento);
				$elemento['idmovimiento'] = $arrData['idmovimiento'];
				if( empty($elemento['agrupacion']) ){ 
					$elemento['agrupacion'] = 1; // por defecto 
				} 
				/* fix clon */
				if( empty($elemento['id']) ){
					$elemento['id'] = @$elemento['idelemento']; 
				}				
				if( $this->model_venta->m_registrar_detalle_venta($elemento) ){ 
					$arrData['message'] = 'Los datos se registraron correctamente - (no caracteristicas)'; 
					$arrData['flag'] = 1; 
					$arrData['iddetalleventa'] = GetLastId('iddetallemovimiento','detalle_movimiento');
					if( !empty($elemento['caracteristicas']) ){ 
						foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
							if( !empty($caracteristica['valor']) ){ 
								$caracteristica['iddetalleventa'] = $arrData['iddetalleventa']; 
								/* fix clon */
								if( empty($caracteristica['idcaracteristica']) ){ 
									$caracteristica['idcaracteristica'] = @$caracteristica['id']; 
								}
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
				//ACTUALIZAR ESTADO USADO
				if( !empty( $allInputs['idnotapedido'] ) ){
					if( $this->model_nota_pedido->m_actualizar_nota_pedido($elemento) ){		
					}
				}
			}
			// ACTUALIZAR NUMERO DE SERIE 
			$arrDataSC = array( 
				'tipo_documento_mov'=> $allInputs['tipo_documento_mov'], 
				'serie'=> $allInputs['serie'] 
			);
			if( $this->model_serie->m_actualizar_serie_correlativo_por_movimiento($arrDataSC) ){ 
				$arrData['message'] .= '<br /> - Se actualizó el correlativo correctamente'; 
				$arrData['flag'] = 1; 
			}

			// ACTUALIZAR FECHA Y ESTADO DE NOTA DE PEDIDO 
			if( !empty( $allInputs['idnotapedido'] ) ){
				$arrDataNPV = array(
					'idnotapedido'=> $allInputs['idnotapedido'] 
				);
				$detPedido = $this->model_nota_pedido->m_cargar_detalle_nota_pedido_por_id($allInputs['idnotapedido']); 
				if(empty($detPedido)){ 
					if( $this->model_nota_pedido->m_actualizar_nota_pedido_a_venta($arrDataNPV) ){
					$arrData['message'] .= '<br /> - Se actualizó el estado de la nota de pedido correctamente'; 
					$arrData['flag'] = 1; 
					}
				}
			}			
		} 
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function editar()
	{
		ini_set('xdebug.var_display_max_depth', 5);
	    ini_set('xdebug.var_display_max_children', 256);
	    ini_set('xdebug.var_display_max_data', 1024);
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// var_dump($allInputs); exit(); 
		/* VALIDACIONES */
		//validar que no sea una venta enviada 
    	$fVenta = $this->model_venta->m_cargar_esta_venta_por_id_simple($allInputs['idmovimiento']);
    	// var_dump($fVenta);exit();
    	if( $fVenta['estado_movimiento'] == 2 ){ // enviado 
    		$arrData['message'] = 'Esta Venta ya ha sido enviada como Nota de Pedido. No se puede modificar.'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
    	// validar que no sea una venta anulada 
    	if( $fVenta['estado_movimiento'] == 0 ){ // anulado 
    		$arrData['message'] = 'Esta Venta ya ha sido anulada anteriormente. No se puede modificar.'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	// var_dump($allInputs);exit();
    	$arrItemDetalle = array();
    	$this->db->trans_start();
    	if( $this->model_venta->m_editar_venta($allInputs) ){ 
    		foreach ($allInputs['detalle'] as $key => $elemento) {
    			if( !empty($elemento['iddetallemovimiento']) ){ 
					$arrItemDetalle[] = $elemento['iddetallemovimiento'];
				}
    		} 
    		// ANULAR ITEMS QUE HAN SIDO QUITADOS.
    		$listaDetalle = $this->model_venta->m_cargar_detalle_venta_por_id($allInputs['idmovimiento']); 
    		$arrDetalleAEliminar = array();
    		foreach ($listaDetalle as $key => $row) {
    			if( !(in_array($row['iddetallemovimiento'],$arrItemDetalle)) ){
    				$arrDetalleAEliminar[] = $row['iddetallemovimiento']; 
    			}
    		}
    		foreach ($arrDetalleAEliminar as $key => $val) { 
    			$arrDatos = array(
    				'iddetallemovimiento'=> $val 
    			);
    			$this->model_venta->m_anular_venta_detalle($arrDatos); 
    		}
    		foreach ($allInputs['detalle'] as $key => $elemento) { 
    			$elemento['idmovimiento'] = $allInputs['idmovimiento'];
    			if( empty($elemento['agrupacion']) ){ 
					$elemento['agrupacion'] = NULL; // por defecto 
				}
    			if( empty($elemento['iddetallemovimiento']) ){
    				// agregar un detalle a venta 
    				if($this->model_venta->m_registrar_detalle_venta($elemento)){
    					$arrData['message'] = 'Los datos se editaron correctamente - (no caracteristicas)'; 
						$arrData['flag'] = 1; 
						$arrData['iddetallemovimiento'] = GetLastId('iddetallemovimiento','detalle_movimiento');

						if( !empty($elemento['caracteristicas']) ){ 
							foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
								if( !empty($caracteristica['valor']) ){ 
									$caracteristica['iddetalleventa'] = $arrData['iddetallemovimiento']; 
									if( $this->model_venta->m_registrar_detalle_caracteristica_venta($caracteristica) ){ 
										$arrData['message'] = 'Los datos se editaron correctamente'; 
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
    			}else{ 
    				// editar detalle de venta 
    				
					if( $this->model_venta->m_editar_venta_detalle($elemento) ){ 
						$arrData['message'] = 'Los datos se editaron correctamente - (no caracteristicas)'; 
						$arrData['flag'] = 1; 
						if( !empty($elemento['caracteristicas']) ){ 
							foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
								if( !empty($caracteristica['iddetallecaracteristica']) ){ 
									if( $this->model_venta->m_editar_detalle_caracteristica_venta($caracteristica) ){ 
										$arrData['message'] = 'Los datos se editaron correctamente'; 
										//$arrData['flag'] = 1; 
										$fVariable = $this->model_variable_car->m_buscar_variable($caracteristica); 
										if( empty($fVariable) ){ 
											// GRABAR COMO UNA VARIABLE 
											$caracteristica['descripcion_vcar'] = $caracteristica['valor'];
											$this->model_variable_car->m_registrar($caracteristica); 
										}
									} 
								}else{ 
									if( !empty($caracteristica['valor']) ){ 
										$caracteristica['iddetalleventa'] = $elemento['iddetallemovimiento'];
										if( $this->model_venta->m_registrar_detalle_caracteristica_venta($caracteristica) ){
											$arrData['message'] = 'Los datos se editaron correctamente'; 
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
    		}
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
    	// var_dump($allInputs);exit();
    	// validar que no sea una cotización enviada 
    	$fVenta = $this->model_venta->m_cargar_esta_venta_por_id_simple($allInputs['idventa']);

    	if( $fVenta['estado_movimiento'] == 2 ){ // enviado 
    		$arrData['message'] = 'Esta Venta ya ha sido enviada como Nota de Pedido. No se puede modificar.'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
    	// validar que no sea una venta anulada 
    	if( $fVenta['estado_movimiento'] == 0 ){ // anulado 
    		$arrData['message'] = 'Esta Venta ya ha sido anulada anteriormente. No se puede modificar.'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}

		if( $this->model_venta->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function imprimir_comprobante_venta()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
	    // RECUPERACIÓN DE DATOS 
	    $fConfig = obtener_parametros_configuracion();
	    $fila = $this->model_venta->m_cargar_venta_por_id($allInputs['id']); 

	    $this->pdf = new Fpdfext('L','mm',array(235,210)); 
		$this->pdf->setTituloAbr($allInputs['codigo_reporte']); 
		$this->pdf->SetAutoPageBreak(TRUE,0); 
		$this->pdf->SetMargins(0,0,0); 
		$this->pdf->addPage(); 
		$fontFamiliyDefault = 'Courier';
		$fontSizeDefault = '10';
		// set position nombre de cliente 
		$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(38,38); 
      	$this->pdf->MultiCell( 55, 4, utf8_decode(strtoupper_total($fila['cliente_persona_empresa'])) ); 

      	// set position direccion  
		$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(42,47); 
      	$this->pdf->MultiCell( 55, 4, utf8_decode(strtoupper_total($fila['direccion_legal_ce'])) ); 

      	// set position RUC 
		$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(38,57); 
      	$this->pdf->MultiCell( 55, 4, utf8_decode(strtoupper_total($fila['num_documento_persona_empresa'])) ); 

	 	// set position Fecha   
		$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(40,66); 
      	$this->pdf->MultiCell( 55, 4, formatoFechaReporte3($fila['fecha_emision']) ); 

      	// set position Telefono 
      	$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(88,57); 
      	$this->pdf->MultiCell( 55, 4, $fila['telefono_ce'] ); 

      	// set O/C 
      	$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(128,57); 
      	$this->pdf->MultiCell( 55, 4, $fila['numero_orden_compra'] ); 

      	// set Condición de Pago 
      	$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(178,57); 
      	$this->pdf->MultiCell( 55, 4, utf8_decode(strtoupper_total($fila['descripcion_fp'])) ); 

      	// set Vendedor 
      	$this->pdf->SetFont($fontFamiliyDefault,'',$fontSizeDefault); 
		$this->pdf->SetXY(178,66); 
      	$this->pdf->MultiCell( 55, 4, utf8_decode(strtoupper_total($fila['colaborador_asig'])) ); 

	    $arrData['message'] = 'ERROR';
	    $arrData['flag'] = 2;
	    // $timestamp = date('YmdHis');
	    if($this->pdf->Output( 'F','assets/dinamic/pdfTemporales/Comp_'. $fila['numero_serie'].'-'.$fila['numero_correlativo'] .'.pdf' )){ 
	      $arrData['message'] = 'OK';
	      $arrData['flag'] = 1;
	    }
	    $arrData = array(
	      'urlTempPDF'=> 'assets/dinamic/pdfTemporales/Comp_'. $fila['numero_serie'].'-'.$fila['numero_correlativo'] .'.pdf' 
	    );
	    $this->output
	        ->set_content_type('application/json')
	        ->set_output(json_encode($arrData));
	}
	public function imprimir_comprobante_venta_html()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
 		$fila = $this->model_venta->m_cargar_venta_por_id($allInputs['id']); 
 		$detalleLista = $this->model_venta->m_cargar_detalle_venta_por_id($allInputs['id']); 
 		$allInputs['idtipodocumentomov'] = $fila['idtipodocumentomov']; 
 		$unidadMedidaPos = 'mm'; // UNIDAD DE MEDIDA DE POSICIÓN. 
 		$configTD = $this->model_tipo_documento_mov->m_cargar_configuracion_td($allInputs); // CONFIGURACION DE comprobante 
 		//var_dump($allInputs['idtipodocumentomov']); 
 		//var_dump($configTD); exit(); 
 		$auxDetConfig = $this->model_tipo_documento_mov->m_cargar_configuracion_detalle_td($configTD); 
 		foreach ($auxDetConfig as $key => $row) {
 			$configTD['detalle'][$row['key_config_detalle']] = array(
 				'x'=> $row['valor_x'],
 				'y'=> $row['valor_y'],
 				'visible'=> $row['visible']
 			);
 		}
 		// PREPARACIÓN DE DATA: 
 		$arrListadoDetalle = array(); 
 		foreach ($detalleLista as $key => $row) { 
			$arrAux = array( 
				'iddetallemovimiento' => $row['iddetallemovimiento'],
				'idmovimiento' => $row['idmovimiento'],
				'idelemento' => $row['idelemento'], 
				'elemento' => $row['descripcion_ele'], 
				'descripcion' => $row['descripcion_ele'], 
				'cantidad' => $row['cantidad'], 
				'precio_unitario' => number_format($row['precio_unitario'],$fConfig['num_decimal_precio_key'],'.',''), 
				'importe_con_igv' => number_format($row['importe_con_igv'],$fConfig['num_decimal_total_key'],'.',''), 
				'importe_sin_igv' => number_format($row['importe_sin_igv'],$fConfig['num_decimal_total_key'],'.',''), 
				'excluye_igv' => $row['excluye_igv'], 
				'igv_detalle' => number_format($row['igv_detalle'],$fConfig['num_decimal_total_key'],'.',''), // agrupacion
				'igv' => $row['igv_detalle'], 
				'unidad_medida' => array( 
					'id'=> $row['idunidadmedida'],
					'descripcion'=> $row['descripcion_um'],
					'abreviatura'=> $row['abreviatura_um'] 
				),
				'caracteristicas' => array() 
			);
			$arrListadoDetalle[$row['iddetallemovimiento']] = $arrAux; 
		}
		foreach ($detalleLista as $key => $row) { 
			$arrAux2 = array(
				'iddetallecaracteristica'=> $row['iddetallecaracteristica'],
				'idcaracteristica'=> $row['idcaracteristica'],
				'orden'=> $row['orden_car'],
				'descripcion'=> $row['descripcion_car'],
				'valor'=> $row['valor']
			);
			if( !empty($row['iddetallecaracteristica']) ){ 
				$arrListadoDetalle[$row['iddetallemovimiento']]['caracteristicas'][$row['iddetallecaracteristica']] = $arrAux2; 
				$arrListadoDetalle[$row['iddetallemovimiento']]['caracteristicas'] = array_values($arrListadoDetalle[$row['iddetallemovimiento']]['caracteristicas']);
			}

		} 
		// acumular caracts. en descripcion 
		foreach ($arrListadoDetalle as $key => $row) { 
			$strCaracteristicasComma = '';
			foreach ($row['caracteristicas'] as $keyCT => $rowCT) {
				$comma = ', ';
				if( $keyCT == count($row['caracteristicas']) ){
					$comma = NULL;
				}
				$strCaracteristicasComma .= $rowCT['descripcion'].' : '.$rowCT['valor'].$comma; 
			}
			$arrListadoDetalle[$key]['descripcion'] .= ' - '.$strCaracteristicasComma; 
		}
		// print_r($arrListadoDetalle); exit();
  		/* ESTILOS */ 
    	$htmlData = '<style type="text/css">
    		@media print{ 
    			@page :bottom { margin: 0cm; }

    			.general{
    				letter-spacing:5px;
				   	font-size:'.$configTD['tamanio_fuente'].$configTD['unidad_medida'].'; 
				   	font-family: '.$configTD['tipo_fuente'].';
    			}
			}
			.general{
				letter-spacing:5px;
			   	font-size:'.$configTD['tamanio_fuente'].$configTD['unidad_medida'].'; 
			   	font-family: '.$configTD['tipo_fuente'].';
			}
			/** { outline: 2px dotted red }
			* * { outline: 2px dotted green }
			* * * { outline: 2px dotted orange }
			* * * * { outline: 2px dotted blue }
			* * * * * { outline: 1px solid red }
			* * * * * * { outline: 1px solid green }
			* * * * * * * { outline: 1px solid orange }
			* * * * * * * * { outline: 1px solid blue }*/
			.item{
				position: absolute;
				display: block;
			}
			.item-detalle{
				display: inline-block;
				padding-right: 6px;
				vertical-align: middle;
			}
			body { margin: 0; padding: 0; }
			.rowdt { width: 100%; text-align:left;font-size: '.$configTD['tamanio_fuente'].$configTD['unidad_medida'].';margin-bottom: 8px; }
			.rowdt.compressed { margin-bottom:0; }
			.hidden{ visibility: hidden; } 
    	</style>';

    	$htmlData .= '<div class="general">'; 
    		// set serie correlativo 
    		if( $configTD['detalle']['serie_correlativo_key']['visible'] == 1 ){ 
				$posX_serieCorrelativo = $configTD['detalle']['serie_correlativo_key']['x'].$unidadMedidaPos; 
		    	$posY_serieCorrelativo = $configTD['detalle']['serie_correlativo_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="font-size:14px;top:'.$posY_serieCorrelativo.';left:'.$posX_serieCorrelativo.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_serie'])).'   -   ';
		    	$htmlData .= '</div>';
    		} 

	    	// set numero correlativo 
	    	if( $configTD['detalle']['num_correlativo_key']['visible'] == 1 ){ 
		    	$posX_numCorrelativo = $configTD['detalle']['num_correlativo_key']['x'].$unidadMedidaPos; 
		    	$posY_numCorrelativo = $configTD['detalle']['num_correlativo_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="font-size:18px;top:'.$posY_numCorrelativo.';left:'.$posX_numCorrelativo.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_correlativo']));
		    	$htmlData .= '</div>';
		    }

	    	// set position nombre de cliente 
	    	if( $configTD['detalle']['nombre_cliente_key']['visible'] == 1 ){ 
		    	$posX_nombreCliente = $configTD['detalle']['nombre_cliente_key']['x'].$unidadMedidaPos; 
		    	$posY_nombreCliente = $configTD['detalle']['nombre_cliente_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_nombreCliente.';left:'.$posX_nombreCliente.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['cliente_persona_empresa']));
		    	$htmlData .= '</div>';
		    }

	    	// set position direccion 
	    	if( $configTD['detalle']['direccion_key']['visible'] == 1 ){ 
		    	$posX_direccion = $configTD['detalle']['direccion_key']['x'].$unidadMedidaPos; 
		    	$posY_direccion = $configTD['detalle']['direccion_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_direccion.';left:'.$posX_direccion.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['direccion_legal_ce']));
		    	$htmlData .= '</div>';
		    }

	    	// set position RUC  
	    	if( $configTD['detalle']['ruc_cliente_key']['visible'] == 1 ){ 
		    	$posX_RUC = $configTD['detalle']['ruc_cliente_key']['x'].$unidadMedidaPos; 
		    	$posY_RUC = $configTD['detalle']['ruc_cliente_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_RUC.';left:'.$posX_RUC.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['num_documento_persona_empresa']));
		    	$htmlData .= '</div>';
		    }

	    	// set position fecha 
	    	if( $configTD['detalle']['fecha_emision_key']['visible'] == 1 ){ 
		    	$posX_fecha = $configTD['detalle']['fecha_emision_key']['x'].$unidadMedidaPos; 
		    	$posY_fecha = $configTD['detalle']['fecha_emision_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_fecha.';left:'.$posX_fecha.';">';
		    	$htmlData .= formatoFechaReporte3($fila['fecha_emision']);
		    	$htmlData .= '</div>';
		    }

	    	// set position telefono 
	    	if( $configTD['detalle']['telefono_key']['visible'] == 1 ){ 
		    	$posX_telefono = $configTD['detalle']['telefono_key']['x'].$unidadMedidaPos; 
		    	$posY_telefono = $configTD['detalle']['telefono_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_telefono.';left:'.$posX_telefono.';">';
		    	$htmlData .= $fila['telefono_ce'];
		    	$htmlData .= '</div>';
		    }

	    	// set O/C 
	    	if( $configTD['detalle']['orden_compra_key']['visible'] == 1 ){ 
		    	$posX_OC = $configTD['detalle']['orden_compra_key']['x'].$unidadMedidaPos; 
		    	$posY_OC = $configTD['detalle']['orden_compra_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_OC.';left:'.$posX_OC.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_orden_compra']));
		    	$htmlData .= '</div>';
		    }
	    	// set Condición de Pago 
		    if( $configTD['detalle']['condicion_pago_key']['visible'] == 1 ){ 
		    	$posX_condicionPago = $configTD['detalle']['condicion_pago_key']['x'].$unidadMedidaPos; 
		    	$posY_condicionPago = $configTD['detalle']['condicion_pago_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_condicionPago.';left:'.$posX_condicionPago.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['descripcion_fp']));
		    	$htmlData .= '</div>';
		    }
	    	// set Vendedor 
	    	// $posX_vendedor = '234mm';
	    	// $posY_vendedor = '56mm';
	    	if( $configTD['detalle']['vendedor_key']['visible'] == 1 ){ 
		    	$posX_vendedor = $configTD['detalle']['vendedor_key']['x'].$unidadMedidaPos; 
		    	$posY_vendedor = $configTD['detalle']['vendedor_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_vendedor.';left:'.$posX_vendedor.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['colaborador_asig'])); 
		    	$htmlData .= '</div>';
		    }
			// $arrData['html'] = $htmlData;
			// $this->output
			//     ->set_content_type('application/json')
			//     ->set_output(json_encode($arrData));
			// return;
	    	/* DETALLE DE ITEMS */
	    	$arrCol = array('60','64','845','130','130'); // ancho de las columnas 
	    	// $posX_detalle = '0mm';
	    	// $posY_detalle = '69mm';
	    	if( $configTD['detalle']['detalle_items_key']['visible'] == 1 ){ 
		    	$posX_detalle = $configTD['detalle']['detalle_items_key']['x'].$unidadMedidaPos;// '0mm';
		    	$posY_detalle = $configTD['detalle']['detalle_items_key']['y'].$unidadMedidaPos;// '69mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_detalle.';left:'.$posX_detalle.';">';
			    	foreach ($arrListadoDetalle as $row) { 
			    		$htmlData .= '<div class="rowdt">'; 
				    		$htmlData .= '<div class="item-detalle" style="width:'. $arrCol[0] .'px;">';
					    	$htmlData .= $row['cantidad'];
					    	$htmlData .= '</div>';
					    	
					    	$htmlData .= '<div class="item-detalle" style="width:'. $arrCol[1] .'px;">';
					    	$htmlData .= $row['unidad_medida']['abreviatura'];
					    	$htmlData .= '</div>';
					    	
					    	$htmlData .= '<div class="item-detalle" style="width:'. $arrCol[2] .'px;">';
					    	$htmlData .= $row['descripcion'];
					    	$htmlData .= '</div>';

					    	$htmlData .= '<div class="item-detalle" style="width:'. $arrCol[3] .'px;">';
					    	$htmlData .= $row['precio_unitario'];
					    	$htmlData .= '</div>';

					    	$htmlData .= '<div class="item-detalle" style="width:'. $arrCol[4] .'px;">';
					    	$htmlData .= $row['importe_sin_igv'];
					    	$htmlData .= '</div>'; 
				    	$htmlData .= '</div>';
			    	}
			    $htmlData .= '</div>'; 
			}
		    $strMoneda = NULL;
		    $strSimbolo = NULL;
		    if( $fila['moneda']=='S' ){
		    	$strMoneda = ' SOLES';
		    	$strSimbolo = 'S/ ';
		    }
		    if( $fila['moneda']=='D' ){
		    	$strMoneda = ' DÓLARES';
		    	$strSimbolo = 'US$ ';
		    }

		    // set número de cuenta 
		    $bancoEmpresa = $this->model_banco_empresa_admin->m_cargar_cuentas_banco_por_filtros($fila['idempresaadmin'],$fila['moneda']); 
	    	if( $configTD['detalle']['num_cuenta_key']['visible'] == 1 ){ 
		    	$posX_numCuenta = $configTD['detalle']['num_cuenta_key']['x'].$unidadMedidaPos; 
		    	$posY_numCuenta = $configTD['detalle']['num_cuenta_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_numCuenta.';left:'.$posX_numCuenta.';">';
			    	$htmlData .= '<div class="rowdt compressed">'; 
			    	foreach ($bancoEmpresa as $keyBEA => $rowBEA) { 
			    		$htmlData .= '<div class="item-detalle" style="width:200px;">';
				    	$htmlData .= 'Cta. Cte. '.$rowBEA['abreviatura_ba'].' '. utf8_decode($strMoneda);
				    	$htmlData .= '</div>';  
				    }
				    $htmlData .= '</div>';
			    	$htmlData .= '<div class="rowdt compressed">'; 
			    	foreach ($bancoEmpresa as $keyBEA => $rowBEA) { 
			    		$htmlData .= '<div class="item-detalle" style="width:200px;">';
				    	$htmlData .= $rowBEA['num_cuenta'];
				    	$htmlData .= '</div>';  
				    }
				    $htmlData .= '</div>';
		    	$htmlData .= '</div>';
		    }

		    // set Monto en letras 
		    
		    $en_letra = ValorEnLetras($fila['total'],$strMoneda);
	    	// $posX_montoEnLetras = '12mm';
	    	// $posY_montoEnLetras = '178.5mm';
	    	if( $configTD['detalle']['monto_letras_key']['visible'] == 1 ){ 
		    	$posX_montoEnLetras = $configTD['detalle']['monto_letras_key']['x'].$unidadMedidaPos; 
		    	$posY_montoEnLetras = $configTD['detalle']['monto_letras_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_montoEnLetras.';left:'.$posX_montoEnLetras.';">';
		    	$htmlData .= $en_letra; 
		    	$htmlData .= '</div>';
		    }
	    	// set subtotal
	    	if( $configTD['detalle']['subtotal_key']['visible'] == 1 ){ 
		    	$posX_subTotal = $configTD['detalle']['subtotal_key']['x'].$unidadMedidaPos; 
		    	$posY_subTotal = $configTD['detalle']['subtotal_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_subTotal.';left:'.$posX_subTotal.';">';
		    	$htmlData .= $strSimbolo.$fila['subtotal']; 
		    	$htmlData .= '</div>';
		    }
	    	// set % IGV
	    	if( $configTD['detalle']['porc_igv_key']['visible'] == 1 ){ 
		    	$posX_porcIGV = $configTD['detalle']['porc_igv_key']['x'].$unidadMedidaPos; 
		    	$posY_porcIGV = $configTD['detalle']['porc_igv_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_porcIGV.';left:'.$posX_porcIGV.';">';
		    	$htmlData .= '18%'; 
		    	$htmlData .= '</div>';
		    }

	    	// set IGV 
	    	if( $configTD['detalle']['igv_key']['visible'] == 1 ){ 
		    	$posX_IGV = $configTD['detalle']['igv_key']['x'].$unidadMedidaPos; 
		    	$posY_IGV = $configTD['detalle']['igv_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_IGV.';left:'.$posX_IGV.';">';
		    	$htmlData .= $strSimbolo.$fila['igv']; 
		    	$htmlData .= '</div>';
		    }

	    	// set total
	    	// $posX_montoTotal = '296mm';
	    	// $posY_montoTotal = '195mm';
	    	if( $configTD['detalle']['total_key']['visible'] == 1 ){ 
		    	$posX_montoTotal = $configTD['detalle']['total_key']['x'].$unidadMedidaPos; 
		    	$posY_montoTotal = $configTD['detalle']['total_key']['y'].$unidadMedidaPos; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_montoTotal.';left:'.$posX_montoTotal.';">';
		    	$htmlData .= $strSimbolo.$fila['total']; 
		    	$htmlData .= '</div>';
		    }
		$htmlData .= '</div>';
		$arrData['flag'] = 1;
		$arrData['html'] = $htmlData;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
