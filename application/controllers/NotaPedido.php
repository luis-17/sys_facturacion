<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotaPedido extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_nota_pedido','model_cliente_persona','model_cliente_empresa','model_configuracion', 'model_cotizacion','model_caracteristica')); 
        $this->load->library('excel');
    	$this->load->library('Fpdfext');
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
    public function lista_notas_de_pedido_historial() 
    {
    	$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_nota_pedido->m_cargar_nota_pedido($paramPaginate,$paramDatos); 
		$totalRows = $this->model_nota_pedido->m_count_nota_pedido($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['estado_movimiento'] == 1 ){ // REGISTRADO 
				$objEstado['claseIcon'] = 'fa-file-archive-o';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'REGISTRADO';
			}
			if( $row['estado_movimiento'] == 2 ){ // FACTURADO 
				$objEstado['claseIcon'] = 'fa-send';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'FACTURADO';
			}
			$strCliente = NULL; 
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
					'num_nota_pedido' => $row['num_nota_pedido'],
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
					'usuario'=> $row['username'],
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
    public function lista_notas_de_pedido_historial_detalle()
    {
    	$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_nota_pedido->m_cargar_nota_pedido_detalle($paramPaginate,$paramDatos); 
		$totalRows = $this->model_nota_pedido->m_count_nota_pedido_detalle($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['estado_movimiento'] == 1 ){ // REGISTRADO 
				$objEstado['claseIcon'] = 'fa-file-archive-o';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'REGISTRADO';
			}
			if( $row['estado_movimiento'] == 2 ){ // FACTURADO 
				$objEstado['claseIcon'] = 'fa-send';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'FACTURADO';
			}
			$strCliente = NULL; 
			$strMoneda = NULL;
			if( $row['moneda'] == 'S' ){ 
				$strMoneda = 'SOLES'; 
			}
			if( $row['moneda'] == 'D' ){ 
				$strMoneda = 'DÓLARES'; 
			}
			array_push($arrListado, 
				array(			
					'iddetallemovimiento' => $row['iddetallemovimiento'],
					'num_nota_pedido' => $row['num_nota_pedido'],
					'fecha_registro' => darFormatoDMY($row['fecha_registro']),
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'cliente' => trim($row['cliente_persona_empresa']),
					'elemento' => $row['descripcion_ele'], 
					'categoria_elemento' => array(
							'id'=> $row['idcategoriaelemento'],
							'descripcion'=> strtoupper($row['descripcion_cael'])				
					),	
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
					'usuario'=> $row['username'],
					'cantidad' => $row['cantidad'], 
					'precio_unitario' => $row['precio_unitario'], 
					'importe_con_igv' => $row['importe_con_igv'],				
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
    public function obtener_esta_nota_pedido()
    {

    	$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
		$idnotapedido = $allInputs['identify']; 
		$fila = $this->model_nota_pedido->m_cargar_nota_pedido_por_id($idnotapedido);
		$detalleLista = $this->model_nota_pedido->m_cargar_detalle_nota_pedido_por_id($idnotapedido); 
		if( $fila['estado_movimiento'] == 2 ){ // facturado 
			$arrData['message'] = 'La nota de pedido ya ha sido facturada con anterioridad.'; 
			$arrData['flag'] = 2; 
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
		}
		

		$rowEstadoId = $fila['estado_movimiento']; 
		if( $fila['estado_movimiento'] == 0 ){ // anulado
			$rowEstadoDescripcion = 'ANULADO'; 
		}
		if( $fila['estado_movimiento'] == 1 ){ // registrado
			$rowEstadoDescripcion = 'REGISTRADO'; 
		}
		if( $fila['estado_movimiento'] == 2 ){ // facturado 
			$rowEstadoDescripcion = 'FACTURADO'; 
		}
		if($fila['moneda'] == 'S'){
			$strIdMoneda = 1; 
			$strDescripcion = 'S/.';
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
				'id'=> (int)$fila['idtipodocumentocliente'],
				'descripcion'=> $fila['tipo_documento_abv']
			),
			'idnotapedido'=> $fila['idmovimiento'],
			'num_nota_pedido'=> $fila['num_nota_pedido'],
			'sede'=> array(
				'id'=> $fila['idsede'],
				'descripcion'=> strtoupper($fila['descripcion_se']),
			),
			'fecha_registro'=> darFormatoDMY($fila['fecha_registro']),
			'fecha_emision'=> darFormatoDMY($fila['fecha_emision']),
			'estado_movimiento'=> array(
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
			'idcontacto'=> $fila['idcontacto'],
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
			'temporal' => array( 
				'cantidad'=> 1,
				'unidad_medida'=> array(),
				'caracteristicas'=> array()
			)
		); 
		if( $fila['tipo_cliente'] === 'E' ){ // empresa 
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
				// 'categoria_cliente' => array(
				// 	'id'=> $fila['idcategoriacliente'],
				// 	'descripcion'=> $fila['descripcion_cc']
				// ),
				// 'colaborador' => array(
				// 	'id'=> $fila['idcolaborador'],
				// 	'descripcion'=> $fila['colaborador']
				// ),
				'ruc' => $fila['ruc_ce'],
				'num_documento' => $fila['ruc_ce'],
				'representante_legal' => $fila['representante_legal_ce'],
				'dni_representante_legal' => $fila['dni_representante_legal_ce'],
				'direccion_legal' => $fila['direccion_legal_ce'],
				'telefono' => $fila['telefono_ce']
			);
		}
		if( $fila['tipo_cliente'] === 'P' ){ // PERSONA 
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
				// 'edad' => devolverEdad($fila['fecha_nacimiento']),
				// 'fecha_nacimiento' => darFormatoDMY($fila['fecha_nacimiento']),
				// 'fecha_nacimiento_str' => formatoFechaReporte3($fila['fecha_nacimiento']),
				'telefono_fijo' => $fila['telefono_movil_cp'],
				'telefono_movil' => $fila['telefono_movil_cp'],
				'email' => $fila['email']
			);
		}
		foreach ($detalleLista as $key => $row) { 
			$arrAux = array( 
				'iddetallenotapedido' => $row['iddetallemovimiento'],
				'idnotapedido' => $row['idmovimiento'],
				'num_nota_pedido' => $row['num_nota_pedido'],
				'idempresaadmin' => $row['idempresaadmin'],
				'idelemento' => $row['idelemento'], 
				'descripcion'=> $row['descripcion_ele'], 
				'elemento' => $row['descripcion_ele'], 
				'cantidad' => $row['cantidad'], 
				'precio_unitario' => number_format($row['precio_unitario'],$fConfig['num_decimal_precio_key'],'.',''), 
				'importe_con_igv' => number_format($row['importe_con_igv'],$fConfig['num_decimal_total_key'],'.',''), 
				'importe_sin_igv' => number_format($row['importe_sin_igv'],$fConfig['num_decimal_total_key'],'.',''), 
				'excluye_igv' => $row['excluye_igv'], 
				'igv_detalle' => number_format($row['igv_detalle'],$fConfig['num_decimal_total_key'],'.',''),
				'igv' => $row['igv_detalle'], 
				'unidad_medida' => array( 
					'id'=> $row['idunidadmedida'],
					'descripcion'=> $row['descripcion_um'] 
				),
				//'agrupacion' => (int)$row['agrupador_totalizado'],
				'caracteristicas' => array() 
			);
			$arrListadoDetalle[$row['iddetallemovimiento']] = $arrAux; 
		}
		foreach ($detalleLista as $key => $row) { 
			$arrAux2 = array(
				'iddetallecaracteristica'=> $row['iddetallecaracteristica'],
				'idcaracteristica'=> $row['idcaracteristica'],
				'id'=> $row['idcaracteristica'],
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
		//$resultado = array_merge_recursive($m1, $m2);
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
    public function ver_popup_busqueda_nota_pedido()
	{
		$this->load->view('nota-pedido/busq_nota_pedido_popup'); 
	}
	public function generar_numero_nota_pedido() 
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
		if( empty($allInputs['sede']) ){ 
			$arrData['message'] = '';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		// Codigo para uso interno. 
		// NOMENCLATURA: 
		// NP + ABV_SEDE(2) + AÑO(2) + MES(2) + DIA(2) + "X" CARACTERES (DINAMICO)
		// Ejm: CUC170827001 
		$sede = strtoupper($allInputs['sede']['abreviatura']); 
		$numCaracteres = $fConfig['cant_caracteres_correlativo_np']; 
		$numNotaPedido = 'NP'.$sede.date('y'); 
		if($fConfig['incluye_mes_en_codigo_np'] == 'si'){
			$numNotaPedido .= date('m'); 
		}
		if($fConfig['incluye_dia_en_codigo_np'] == 'si'){
			$numNotaPedido .= date('d'); 
		}
		// OBTENER ULTIMA COTIZACION SEGÚN LOGICA DE CONFIGURACIÓN. 
		$allInputs['config'] = $fConfig; 
		$fNotaPedido = $this->model_nota_pedido->m_cargar_ultima_nota_pedido_segun_config($allInputs);
		if( empty($fNotaPedido) ){
			$numCorrelativo = 1;
		}else{
			$numCorrelativo = substr($fNotaPedido['num_nota_pedido'], ($numCaracteres * -1), $numCaracteres); 
			$numCorrelativo = (int)$numCorrelativo + 1;
		}
		$numNotaPedido .= str_pad($numCorrelativo, $numCaracteres, '0', STR_PAD_LEFT);
	 	$arrDatos['num_nota_pedido'] = $numNotaPedido; 
    	$arrData['datos'] = $arrDatos;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($numNotaPedido)){ 
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
		// print_r($allInputs); exit(); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		/* VALIDACIONES */

		if( $allInputs['isRegisterSuccess'] === TRUE ){ 
    		$arrData['message'] = 'Ya se registró esta nota de pedido.';
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
    	if( empty($allInputs['num_nota_pedido']) ){ 
    		$arrData['message'] = 'No se ha generado un COD. DE NOTA DE PEDIDO. Genere la NOTA DE PEDIDO.';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	if( $allInputs['tipo_documento_cliente']['destino_str'] == 'ce' ){ // si es cliente empresa 
    		if( empty($allInputs['contacto']) || empty($allInputs['idcontacto']) ){ 
	    		$arrData['message'] = 'No se ha asociado un CONTACTO válido. Asocie el CONTACTO.';
	    		$arrData['flag'] = 0;
	    		$this->output
			    	->set_content_type('application/json')
			    	->set_output(json_encode($arrData));
			    return;
	    	}
    	}
    	
    	$fNotaPedido = $this->model_nota_pedido->m_cargar_esta_nota_pedido_por_codigo($allInputs['num_nota_pedido']);
    	if( !empty($fNotaPedido) ){ 
    		$arrData['message'] = 'Ya se a registrado una nota de pedido, usando el código <strong>'.$allInputs['num_nota_pedido'].'</strong>'; 
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
    	$arrCotizaciones = array(); 
		if( $this->model_nota_pedido->m_registrar_nota_pedido($allInputs) ){ 
			$arrData['idnotapedido'] = GetLastId('idmovimiento','movimiento');
			foreach ($allInputs['detalle'] as $key => $elemento) { 
				$elemento['idnotapedido'] = $arrData['idnotapedido'];
				if( empty($elemento['agrupacion']) ){ 
					$elemento['agrupacion'] = 1; // por defecto 
				} 
				if( $this->model_nota_pedido->m_registrar_detalle_nota_pedido($elemento) ){ 
					$arrCotizaciones[] = $elemento['idcotizacion']; 
					$arrData['message'] = 'Los datos se registraron correctamente - (no caracteristicas)'; 
					$arrData['flag'] = 1; 
					$arrData['iddetallenotapedido'] = GetLastId('iddetallemovimiento','detalle_movimiento');
					if( !empty($elemento['caracteristicas']) ){ 
						foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
							if( !empty($caracteristica['valor']) ){ 
								$caracteristica['iddetallenotapedido'] = $arrData['iddetallenotapedido']; 
								if( $this->model_nota_pedido->m_registrar_detalle_caracteristica_nota_pedido($caracteristica) ){ 
									$arrData['message'] = 'Los datos se registraron correctamente'; 
									$arrData['flag'] = 1; 
								} 
							} 
						} 
					}
				} 
			}
			// actualizar las cotizaciones a "PEDIDO" 
			$boolEstado = 3; //PEDIDO 
			$arrCotizaciones = array_unique($arrCotizaciones); 
			if( $this->model_cotizacion->m_actualizar_estado_cotizaciones($arrCotizaciones,$boolEstado) ){ 
				$arrData['message'] .= '<br/> - Se actualizó el estado de las cotizaciones seleccionadas.';  
			}
		} 
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
