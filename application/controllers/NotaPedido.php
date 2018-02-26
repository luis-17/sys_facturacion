<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotaPedido extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_nota_pedido','model_cliente_persona','model_cliente_empresa','model_configuracion', 'model_cotizacion','model_caracteristica','model_banco_empresa_admin')); 
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
			if( $row['estado_movimiento'] == 0 ){ // ANULADO 
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'ANULADO';
			}
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
			$objEstado['valor'] = $row['estado_movimiento'];
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
					'colaborador_cot' => strtoupper($row['colaborador_cot']),
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
					'estado' => $objEstado,
					'observaciones' => $row['observaciones_np'], 
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
					'estado' => $objEstado, 
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
		$arrData['flag'] = 1; 
		$idnotapedido = $allInputs['identify']; 
		$flagAll = @$allInputs['flag_all']; 
		$fila = $this->model_nota_pedido->m_cargar_nota_pedido_por_id($idnotapedido);
		// print_r($fila['estado_movimiento']); 
		// print_r($fila['estado_movimiento']);
		// exit();
		$detalleLista = $this->model_nota_pedido->m_cargar_detalle_nota_pedido_por_id($idnotapedido); 
		if( $fila['estado_movimiento'] == 2 && !($flagAll) ){ // facturado 
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
				'id'=> (int)$fila['idtipodocumentocliente'],
				'descripcion'=> $fila['tipo_documento_abv']
			),
			'idnotapedido'=> $fila['idmovimiento'],
			'num_nota_pedido'=> $fila['num_nota_pedido'],
			'idcolaborador'=> $fila['idcolaborador'], // colaborador asignado a la NP 
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
			$arrListado['num_documento'] = $fila['num_documento_persona_empresa'];
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
				'ruc' => $fila['num_documento_persona_empresa'],
				'num_documento' => $fila['num_documento_persona_empresa'],
				'representante_legal' => $fila['representante_legal_ce'],
				'dni_representante_legal' => $fila['dni_representante_legal_ce'],
				'direccion_legal' => $fila['direccion_legal_ce'],
				'telefono' => $fila['telefono_ce']
			);
		}
		if( $fila['tipo_cliente'] === 'P' ){ // PERSONA 
			$arrListado['num_documento'] = $fila['num_documento_persona_empresa'];
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
				'num_documento' => $fila['num_documento_persona_empresa'],
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
		//$resultado = array_merge_recursive($m1, $m2);
		// reindexado 
		$arrListadoDetalle = array_values($arrListadoDetalle);
		$arrData['datos'] = $arrListado; 
		$arrData['detalle'] = $arrListadoDetalle; 

		
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
    public function ver_popup_busqueda_nota_pedido_chico()
	{
		$this->load->view('nota-pedido/busq_nota_pedido_chico_popup'); 
	}
    public function ver_popup_editar_nota_pedido()
	{
		$this->load->view('nota-pedido/editar_nota_pedido_popup'); 
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
	public function buscar_numero_nota_pedido_autocomplete()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$lista = $this->model_nota_pedido->m_cargar_numero_nota_pedido_autocomplete($allInputs); 
		$hayStock = true;
		$arrListado = array();
		foreach ($lista as $row) { 
			if($row['moneda'] == 'S'){
				$strIdMoneda = 1; 
				$strDescripcion = 'S/';
				$strMoneda = $row['moneda'];
			}
			if($row['moneda'] == 'D'){
				$strIdMoneda = 2; 
				$strDescripcion = 'US$'; 
				$strMoneda = $row['moneda']; 
			}
			$objEstado = array();
			if( $row['estado_movimiento'] == 1 ){ // REGISTRADO 
				$objEstado['claseIcon'] = 'fa-file-archive-o';
				$objEstado['claseLabel'] = 'text-info';
				$objEstado['labelText'] = 'REGISTRADO';
			}
			if( $row['estado_movimiento'] == 2 ){ // FACTURADO 
				$objEstado['claseIcon'] = 'fa-send';
				$objEstado['claseLabel'] = 'text-success';
				$objEstado['labelText'] = 'FACTURADO';
			}
			array_push($arrListado, 
				array( 
					'idmovimiento'=> $row['idmovimiento'],
					'num_nota_pedido'=> $row['num_nota_pedido'], 
					'cliente' => $row['cliente_persona_empresa'],
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'email' => $row['email_persona_empresa'],
					'subtotal' => $row['subtotal'],
					'igv'=> $row['igv'], 
					'total'=> $row['total'], 
					'moneda'=> array( 
						'id'=> $strIdMoneda,
						'descripcion'=> $strDescripcion,
						'str_moneda'=> $strMoneda
					), 
					'estado' => $objEstado 
				)
			);
		}
		
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}else{
			if( @$allInputs['limit'] == 1 && @$lista[0]['estado_cot'] == 3 /*pedido*/ ){ 
				$arrData['flag'] = 3;
			}
		}
		
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData)); 
	}
	public function imprimir_nota_pedido()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
	    // RECUPERACIÓN DE DATOS 
	    $fConfig = obtener_parametros_configuracion();
	    $fila = $this->model_nota_pedido->m_cargar_nota_pedido_por_id($allInputs['id']);	 
	    $fila['moneda_str'] = NULL; 
	    $simbolo = NULL;
	    if($fila['moneda'] == 'S'){
	    	$fila['moneda_str'] = 'SOLES';
	    	$simbolo = 'S/ ';
	    	$fila['moneda_str_completo'] = 'SOLES';
	    }
	    if($fila['moneda'] == 'D'){
	    	$fila['moneda_str'] = 'DÓLARES';
	    	$fila['moneda_str_completo'] = 'DÓLARES AMERICANOS';
	    	$simbolo = 'US$ ';
	    } 
	    $strIncluyeIGV = NULL;
	    if($fila['modo_igv'] == 1){ 
	    	$strIncluyeIGV = 'SI';
	    }
	    if($fila['modo_igv'] == 2){ 
	    	$strIncluyeIGV = 'NO';
	    } 
	    // CONFIGURACION DEL PDF
	    $this->pdf = new Fpdfext();
	    $this->pdf->SetMargins(8,8);
	    $this->pdf->setImagenCab('assets/dinamic/empresa/'.$fila['nombre_logo']); 
	    // $this->pdf->setEstado($fila['estado_cot']);
	    $this->pdf->AddPage('P','A4');//var_dump($allInputs['tituloAbv']); exit();
	    $this->pdf->AliasNbPages();
	    $this->pdf->SetAutoPageBreak(true,10);

	    $this->pdf->SetTextColor(95,95,95);
	    $this->pdf->SetFont('Arial','B',9);
        $this->pdf->SetXY(8,21);
        $this->pdf->MultiCell( 120,6,utf8_decode( $fila['razon_social_ea'] ) ); 
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetXY(8,25);
        $this->pdf->MultiCell( 120,6,utf8_decode( $fila['direccion_legal'] ),0,'L' );
        $this->pdf->SetXY(8,29);
        $this->pdf->MultiCell( 120,6,'Sitio Web: ',0,'L' ); 
        $this->pdf->SetXY(36,29);
        $this->pdf->MultiCell( 120,6,utf8_decode(strtolower($fila['pagina_web']) ),0,'L' );
        $this->pdf->SetXY(8,33);
        $this->pdf->MultiCell( 120,6,utf8_decode('Teléfono: '),0,'L' );
	    $this->pdf->SetXY(36,33);
	    $this->pdf->MultiCell( 120,6,utf8_decode( $fila['telefono_ea'] ),0,'L' );

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->SetXY(100,8);
        $this->pdf->Cell(120,10,utf8_decode( 'NOTA PEDIDO N° '.$fila['num_nota_pedido'] ),0,0);
        $this->pdf->Ln(15);
        if( @$this->estado == 1 ){ 
          $this->SetFont('Arial','B',50);
          $this->SetTextColor(255,192,203);
          $this->RotatedText(70,190,'A N U L A D O',45); 
        } 
      	
      	$this->pdf->SetXY(8,40);
      	$r = $fConfig['color_plantilla_reporte_r'];
		$g = $fConfig['color_plantilla_reporte_g'];
		$b = $fConfig['color_plantilla_reporte_b'];
		$r_sec = $fConfig['color_plantilla_reporte_second_r'];
		$g_sec = $fConfig['color_plantilla_reporte_second_g'];
		$b_sec = $fConfig['color_plantilla_reporte_second_b'];
		$this->pdf->SetFillColor($r,$g,$b);
		$this->pdf->SetWidths(array(60));
		$arrBarra = array(
			'data'=> array(
				utf8_decode('   DATOS DEL CLIENTE: '),
				//utf8_decode('   '.$turno['hora'].':'.$turno['min'].' '.$turno['tiempo'].'.') 
			),
			'textColor'=> array(
				array('r'=> 255, 'g'=> 255, 'b'=> 255),
				//array('r'=> 83, 'g'=> 83, 'b'=> 83 )
			),
			'fontSize'=> array(
				array('family'=> NULL, 'weight'=> NULL, 'size'=> 10),
				//array('family'=> NULL, 'weight'=> NULL, 'size'=> 10 )
			),
			'bgColor'=> array(
				array('r'=> $r, 'g'=> $g, 'b'=> $b ), 
				//array('r'=> 255, 'g'=> 255, 'b'=> 255 ) 
			)
		);
		$this->pdf->Row($arrBarra['data'],true,0,FALSE,5,$arrBarra['textColor'],$arrBarra['bgColor'],FALSE,FALSE,$arrBarra['fontSize']);

		$this->pdf->SetTextColor(66,66,66);
		$y = $this->pdf->GetY();
		// var_dump($y);exit();
		$this->pdf->SetXY(8,$y); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(24,4,'CLIENTE '); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	// $this->pdf->Cell(75,6,strtoupper(strtoupper_total($fila['cliente_persona_empresa'])));
      	$this->pdf->MultiCell(55,4,strtoupper(strtoupper_total($fila['cliente_persona_empresa'])));
      	$y1 = $this->pdf->GetY();
      	$this->pdf->SetXY(8,$y1-1); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(24,6,strtoupper($fila['tipo_documento_abv'])); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,strtoupper($fila['num_documento_persona_empresa'])); 
      	$y2 = $this->pdf->GetY();
		$this->pdf->SetXY(8,$y2+5); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(24,4,'CONTACTO '); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->MultiCell(55,4,utf8_decode(strtoupper_total($fila['contacto'])));
  		// $this->pdf->Cell(75,6,strtoupper(strtoupper_total(utf8_decode($fila['contacto']))));
      	$y3 = $this->pdf->GetY();
      	$this->pdf->SetXY(8,$y3-1); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(24,6,'E-MAIL '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,strtoupper($fila['email_persona_empresa']));

      	$this->pdf->SetXY(96,$y); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(26,4,utf8_decode('DIRECCIÓN ')); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',7); 
  		// $this->pdf->Cell(75,6,utf8_decode(strtoupper_total($fila['direccion_legal_ce'])));
      	$this->pdf->MultiCell(75,4,utf8_decode(strtoupper_total($fila['direccion_legal_ce'])));
      	$y1a = $this->pdf->GetY();
      	$this->pdf->SetXY(96,$y1a); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(26,4,utf8_decode('DIR. DESPACHO')); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',7); 
  		// $this->pdf->Cell(75,6,strtoupper(strtoupper_total($fila['direccion_guia'])));
      	$this->pdf->MultiCell(75,4,utf8_decode(strtoupper_total($fila['direccion_guia'])));
  		// var_dump(strlen($fila['direccion_guia']));exit();
      	$y1b = $this->pdf->GetY();
      	$this->pdf->SetXY(96,$y1b); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(26,6,utf8_decode('TELÉFONO ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',7); 
      	$this->pdf->Cell(75,6,strtoupper($fila['telefono_ce']));

		if(strlen($fila['direccion_guia'])> 44){$sumY= 4;}else{$sumY= 0;} 
      	$this->pdf->SetXY(8,$y3+5+$sumY); 
      	$this->pdf->SetFillColor($r,$g,$b);
		$this->pdf->SetWidths(array(60));
		$arrBarra = array(
			'data'=> array(
				utf8_decode('   DATOS DE LA NOTA PEDIDO: ') 
			),
			'textColor'=> array(
				array('r'=> 255, 'g'=> 255, 'b'=> 255) 
			),
			'fontSize'=> array(
				array('family'=> NULL, 'weight'=> NULL, 'size'=> 10) 
			),
			'bgColor'=> array(
				array('r'=> $r, 'g'=> $g, 'b'=> $b ) 
			)
		);
		$this->pdf->Row($arrBarra['data'],true,0,FALSE,5,$arrBarra['textColor'],$arrBarra['bgColor'],FALSE,FALSE,$arrBarra['fontSize']);
		$this->pdf->SetTextColor(66,66,66);
		$y4 = $this->pdf->GetY();
		$this->pdf->SetXY(8,$y4); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(38,4,'ASESOR DE VENTA '); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->MultiCell(45,4,strtoupper(strtoupper_total($fila['nombres'].' '.$fila['apellidos'])));
      	$y5 = $this->pdf->GetY();
      	$this->pdf->SetXY(8,$y5-1); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(38,6,utf8_decode('FECHA EMISIÓN ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,darFormatoDMY($fila['fecha_emision'])); 

      	$this->pdf->SetXY(8,$y5+3); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(38,6,'MONEDA '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['moneda_str_completo'])); 

      	$this->pdf->SetXY(8,$y5+7); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(38,6,utf8_decode('PLAZO DE ENTREGA(*) ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['plazo_entrega']. ' días útiles')); 

      	$this->pdf->SetXY(96,$y4-1); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(26,6,utf8_decode('COND. DE PAGO ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['descripcion_fp'])); 

      	$this->pdf->SetXY(96,$y4+3); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(26,6,utf8_decode('VALIDEZ'));  // DE OFERTA
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['validez_oferta'].' días ')); 

      	$this->pdf->SetXY(96,$y4+7); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(26,6,utf8_decode('INCLUYE IGV ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,$strIncluyeIGV); 

      	$this->pdf->SetXY(96,$y4+11); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(26,6,utf8_decode('GENERADO POR: ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,$fila['nombres_gen'].' '.$fila['apellidos_gen']); 

      	$this->pdf->SetXY(8,$y5+13); 
      	$this->pdf->Cell(100,6,utf8_decode('Tenemos el agrado de presentar la siguiente nota pedido: ')); 

      	$this->pdf->SetXY(8,$y5+19); 
      	//$this->pdf->Ln(4);
      	$x_final_izquierda = $this->pdf->GetX();
      	$y_final_izquierda = $this->pdf->GetY();

      	// APARTADO: DATOS DEL DETALLE

      	// LOGICA POSICION 
      	$this->pdf->SetFont('Arial','B',8);
      	$this->pdf->SetFillColor($r,$g,$b);
      	$this->pdf->SetTextColor(255,255,255);
      	$this->pdf->Cell(10,6,'ITEM',1,0,'L',TRUE);
      	$this->pdf->Cell(100,6,utf8_decode('DESCRIPCIÓN'),1,0,'L',TRUE);
      	$this->pdf->Cell(20,6,'U.M.',1,0,'C',TRUE);
      	$this->pdf->Cell(18,6,'CANT.',1,0,'C',TRUE);
      	$this->pdf->Cell(20,6,'P.U.',1,0,'C',TRUE);
      	$this->pdf->Cell(26,6,'IMPORTE',1,0,'C',TRUE); 
      	$this->pdf->Ln(7);

      	$this->pdf->SetFont('Arial','',7);
	    
	    $i = 1;
	    $detalleEle = $this->model_nota_pedido->m_cargar_detalle_nota_pedido_por_id($allInputs['id']);
	    // var_dump($detalleEle); exit(); 
	    $arrGroupBy = array(); 
	    foreach ($detalleEle as $key => $value) { 
	    	if( !empty($value['agrupador_totalizado']) ){ 
	    		$rowAux = array(
		    		'iddetallemovimiento' =>$value['iddetallemovimiento'],
		    		'descripcion_ele' =>$value['descripcion_ele'],
		    		'num_agrupacion' =>$value['agrupador_totalizado'],
		    		'agrupado' => TRUE,
		    		'detallesubitems' => array(),
		    		'detallecaracteristica' =>array()
		    	); 
	    		$arrGroupBy[$value['agrupador_totalizado'].'.dif'] = $rowAux;
	    	}else{
	    		$rowAux = array(
		    		'iddetallemovimiento' =>$value['iddetallemovimiento'],
		    		'descripcion_ele' =>$value['descripcion_ele'],
		    		'cantidad' =>$value['cantidad'],
		    		'abreviatura_um' =>$value['abreviatura_um'],
		    		'precio_unitario' =>$value['precio_unitario'],
		    		'importe_con_igv' =>$value['importe_con_igv'],
		    		'importe_sin_igv' =>$value['importe_sin_igv'],
		    		'num_agrupacion' =>$value['agrupador_totalizado'],
		    		'agrupado' => FALSE,
		    		'detallesubitems' => array(),
		    		'detallecaracteristica' =>array()
		    	); 
	    		$arrGroupBy[$value['iddetallemovimiento']] = $rowAux;
	    	}
	    	
	    } 
	    // caracteristicas
		foreach ($detalleEle as $key => $value) { 
			if( !empty($value['iddetallecaracteristica']) ){ 
				$rowAux = array( 
		    		'iddetallecaracteristica' => $value['iddetallecaracteristica'],
		    		'descripcion_car' => $value['descripcion_car'],
		    		'valor' => $value['valor'] 
		    	);
		    	if( !empty($value['agrupador_totalizado']) ){ 
		    		$arrGroupBy[$value['agrupador_totalizado'].'.dif']['detallecaracteristica'][$value['idcaracteristica']] = $rowAux; 
		    	}else{
		    		$arrGroupBy[$value['iddetallemovimiento']]['detallecaracteristica'][$value['iddetallecaracteristica']] = $rowAux; 
	    		}

	    	} 
		}
		// subitems 
		foreach ($detalleEle as $key => $value) {
			if( !empty($value['agrupador_totalizado']) ){ 
				$rowAux = array( 
		    		'cantidad' =>$value['cantidad'],
	    			'abreviatura_um' =>$value['abreviatura_um'],
	    			'precio_unitario' =>$value['precio_unitario'],
	    			'importe_con_igv' =>$value['importe_con_igv'],
	    			'importe_sin_igv' =>$value['importe_sin_igv']
		    	);
		    	$arrGroupBy[$value['agrupador_totalizado'].'.dif']['detallesubitems'][$value['iddetallemovimiento']] = $rowAux; 
	    	} 
		}

	    $exonerado = 0;
	    $fill = TRUE;
	    $this->pdf->SetDrawColor($r_sec,$g_sec,$b_sec); // gris fill 
	    $this->pdf->SetLineWidth(.1);
	    foreach ($arrGroupBy as $key => $value) { 
	    	if( $value['agrupado'] === FALSE ){ 
		    	if( $fila['modo_igv'] == 1){ 
		    		$valImporte = $value['importe_con_igv'];
		    	}
		    	if( $fila['modo_igv'] == 2 ){
		    		$valImporte = $value['importe_sin_igv'];
		    	}
		    }
		    $fill = !$fill;		
		    $this->pdf->SetWidths(array(10, 100, 20, 18, 20, 26));
		    $this->pdf->SetAligns(array('L', 'L', 'C', 'C', 'R', 'R'));
		    $this->pdf->SetFillColor($r_sec,$g_sec,$b_sec);
		    $this->pdf->SetTextColor(0,3,6);
		    $this->pdf->SetFont('Arial','B',6); 
		    $arrItemDetalle = array(
				'fontSize'=> array(
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 )
				)
			);
			if( $value['agrupado'] === FALSE ){ 
				$this->pdf->Row( 
			      array(
			        $i,
			        utf8_decode($value['descripcion_ele']),
			        strtoupper($value['abreviatura_um']),
			        $value['cantidad'],
			        number_format($value['precio_unitario'],$fConfig['num_decimal_precio_key'],'.',' '),
			        number_format($valImporte,$fConfig['num_decimal_total_key'],'.',' ')
			      ),
			      FALSE, 0, FALSE, 4, FALSE, FALSE, FALSE, FALSE, $arrItemDetalle['fontSize'] 
			    );
			}
			if( $value['agrupado'] === TRUE ){ 
				$this->pdf->Row( 
			      array(
			        $i,
			        utf8_decode($value['descripcion_ele']),
			        '',
			        '',
			        '',
			        ''
			      ),
			      FALSE, 0, FALSE, 4, FALSE, FALSE, FALSE, FALSE, $arrItemDetalle['fontSize'] 
			    );
			    $GetYElement = $this->pdf->GetY()-2; 
			    $GetXElement = $this->pdf->GetX() + 110; 
			}
		    
		    $i++;
		  	$this->pdf->SetTextColor(66,66,66);
		   	$this->pdf->SetFont('Arial','',6);
			foreach ($value['detallecaracteristica'] as $key => $row) { 
				$this->pdf->SetWidths(array(10, 32, 5, 100));
		    	$this->pdf->SetAligns(array('L', 'L', 'L', 'L'));
				$arrCaracts = array( 
					'data'=> array(
						'',
						utf8_decode($row['descripcion_car']),
						':',
						utf8_decode($row['valor']) 
					),
					'fontSize'=> array(
						array('family'=> NULL, 'weight'=> NULL, 'size'=> 6 ),
						array('family'=> NULL, 'weight'=> NULL, 'size'=> 6 ),
						array('family'=> NULL, 'weight'=> NULL, 'size'=> 6 ),
						array('family'=> NULL, 'weight'=> NULL, 'size'=> 6 )
					)
				);
				$this->pdf->Row( $arrCaracts['data'],FALSE,'0',FALSE,3,FALSE,FALSE,FALSE,FALSE,$arrCaracts['fontSize'] ); 
			}
			$GetYElementCaracts = $this->pdf->GetY(); 
			if( $value['agrupado'] === TRUE ){ 
				$this->pdf->SetY($GetYElement);
				foreach ($value['detallesubitems'] as $key => $row) { 
					$this->pdf->SetX($GetXElement);
					$this->pdf->SetWidths(array(20, 18, 20, 26));
			    	$this->pdf->SetAligns(array('C', 'C', 'R', 'R')); 
			    	if( $fila['modo_igv'] == 1){ 
			    		$valImporte = $row['importe_con_igv'];
			    	}
			    	if( $fila['modo_igv'] == 2 ){
			    		$valImporte = $row['importe_sin_igv'];
			    	}
					$arrCaracts = array( 
						'data'=> array( 
							strtoupper($row['abreviatura_um']),
							$row['cantidad'],
							number_format($row['precio_unitario'],$fConfig['num_decimal_precio_key'],'.',' '),
			        		number_format($valImporte,$fConfig['num_decimal_total_key'],'.',' ')
						),
						'fontSize'=> array( 
							array('family'=> NULL, 'weight'=> NULL, 'size'=> 9 ),
							array('family'=> NULL, 'weight'=> NULL, 'size'=> 9 ),
							array('family'=> NULL, 'weight'=> NULL, 'size'=> 9 ),
							array('family'=> NULL, 'weight'=> NULL, 'size'=> 9 )
						)
					);
					$this->pdf->Row( $arrCaracts['data'],FALSE,'0',FALSE,6,FALSE,FALSE,FALSE,FALSE,$arrCaracts['fontSize'] ); 
				} 
			}
			$GetYElementSubItems = $this->pdf->GetY(); 
			if( $value['agrupado'] === TRUE ){ 
				if( $GetYElementSubItems > $GetYElementCaracts ){
					$GetYElementSelected = $GetYElementSubItems;
				}else{
					$GetYElementSelected = $GetYElementCaracts;
				}
				
				$this->pdf->SetY($GetYElementSelected); 
			}
			$this->pdf->Cell(194,0.8,'','B',1,'C',0); 
	    }
	    //$this->pdf->SetXY(8,-34); 
	    
	    //$en_letra = ValorEnLetras($fila['total'],$fila['moneda_str_completo']);
	    // if (array_values($arrGroupBy)[0]['agrupado']==false) {
	    // $this->pdf->Cell(140,5,'TOTAL SON: ' . utf8_decode($en_letra));
	    // }
	    //$this->pdf->SetXY(8,-23); 
	    //$this->pdf->SetFont('Arial','',8);
	    //$bancoEmpresa = $this->model_banco_empresa_admin->m_cargar_cuentas_banco_por_filtros($fila['idempresaadmin'],$fila['moneda']);
 		//$this->pdf->SetTextColor(0,0,0);
   		// $this->pdf->SetFont('Arial','',9);
	    // foreach ($bancoEmpresa as $key => $value) {
	    // 	$this->pdf->Cell(40,5,'Cta. Cte. '.$value['abreviatura_ba'].' '. utf8_decode($fila['moneda_str']),0,0,'L',0); 	  
	    // }
	    // $this->pdf->SetXY(8,-19); 
	    // foreach ($bancoEmpresa as $key => $value) {
	    // 	$this->pdf->Cell(40,5,$value['num_cuenta'],0,0,'L',0); 	  
	    // }
	    $this->pdf->SetFont('Arial','B',9);
	    $this->pdf->SetXY(8,-40);
	    $this->pdf->Cell(20,6,"OBSERVACIONES:"); 
	    $this->pdf->SetXY(8,-35); 
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->SetWidths(array(138));
		$this->pdf->TextArea(array(empty($fila['observaciones_np'])? '':$fila['observaciones_np']),0,0,FALSE,5,20);

		$this->pdf->SetXY(8,-35); 
		$this->pdf->Cell(150,20,'');
		$this->pdf->Cell(20,6,'SUBTOTAL:','LT',0,'R');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(20,6,$simbolo . number_format($fila['subtotal'],$fConfig['num_decimal_total_key'],'.',' '),'TR',0,'R');
		$this->pdf->Ln(6);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(150,6,'');
		$this->pdf->Cell(20,6,'IGV:','L',0,'R');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(20,6,$simbolo . number_format($fila['igv'],$fConfig['num_decimal_total_key'],'.',' '),'R',0,'R');
		$this->pdf->Ln(6);
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(150,8,'');
		$this->pdf->Cell(20,8,'TOTAL:','TLB',0,'R');
		$this->pdf->Cell(20,8,$simbolo . number_format($fila['total'],$fConfig['num_decimal_total_key'],'.',' '),'TRB',0,'R');
	    // $this->pdf->Cell(30,8,$simbolo . substr($fila['total_a_pagar'], 4),'TRB',0,'R');
	    // $this->pdf->Ln(15);
	    // $monto = new EnLetras();
	    // $en_letra = ValorEnLetras($fila['total'],$fila['moneda_str']);
	    // $this->pdf->Cell(0,8,'TOTAL SON: ' . $en_letra ,'',0);
	    $arrData['message'] = 'ERROR';
	    $arrData['flag'] = 2;
	    // $timestamp = date('YmdHis');
	    if($this->pdf->Output( 'F','assets/dinamic/pdfTemporales/NotP_'. $fila['num_nota_pedido'] .'.pdf' )){
	      $arrData['message'] = 'OK';
	      $arrData['flag'] = 1;
	    }
	    $arrData = array(
	      'urlTempPDF'=> 'assets/dinamic/pdfTemporales/NotP_'. $fila['num_nota_pedido'] .'.pdf'
	    );
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
    	if( empty($allInputs['vendedor']['idvendedor']) ){ 
    		$arrData['message'] = 'No se ha asignado al vendedor.';
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

    	$fConfig = obtener_parametros_configuracion(); 
		$cantCaracteres = 6; // NP(2) + SEDE(2) + AÑO(2) 
		if($fConfig['incluye_mes_en_codigo_np'] == 'si'){
			$cantCaracteres += 2;
		}
		if($fConfig['incluye_dia_en_codigo_np'] == 'si'){
			$cantCaracteres += 2;
		}
		$numCaracteres = $fConfig['cant_caracteres_correlativo_np']; 
		$numCorrelativo = substr($allInputs['num_nota_pedido'], ($numCaracteres * -1), $numCaracteres); 

		$numCorrelativoAnterior = $numCorrelativo - 1; 
		if($numCorrelativoAnterior >= 1 ){
			$numNPAnterior = substr($allInputs['num_nota_pedido'], 0,$cantCaracteres); 
			$numNPAnterior .= str_pad($numCorrelativoAnterior, $numCaracteres, '0', STR_PAD_LEFT); 
			// var_dump($numNPAnterior); exit();
			$fNPAnterior = $this->model_nota_pedido->m_cargar_esta_nota_pedido_por_codigo($numNPAnterior); 
			if( empty($fNPAnterior) ){ 
				$arrData['message'] = 'El N° de Nota de Pedido <strong>'.$allInputs['num_nota_pedido'].'</strong> está errado para la empresa <strong>'.$this->sessionFactur['nombre_comercial'].'</strong>'; 
	    		$arrData['flag'] = 0;
	    		$this->output
			    	->set_content_type('application/json')
			    	->set_output(json_encode($arrData));
			    return;
			}
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
								// NO GRABAR CARACTERISTICAS REPETIDAS EN NOTA PEDIDO
								$fDetCarac = $this->model_nota_pedido->m_validar_caracteristicas_repetidas($caracteristica['idcaracteristica'],$caracteristica['iddetallenotapedido']);
								if( empty($fDetCarac) ){
									if( $this->model_nota_pedido->m_registrar_detalle_caracteristica_nota_pedido($caracteristica) ){ 
										$arrData['message'] = 'Los datos se registraron correctamente'; 
										$arrData['flag'] = 1; 
									} 
								}
							} 
						} 
					}
				} 
			}
			// actualizar las cotizaciones a "PEDIDO" 
			$arrCotizaciones = array_unique($arrCotizaciones); 
			if( $this->model_cotizacion->m_actualizar_estado_cotizaciones_a_pedido($arrCotizaciones) ){ 
				$arrData['message'] .= '<br/> - Se actualizó el estado de las cotizaciones seleccionadas.';  
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
    	// validar que no sea una nota pedido facturada 
    	$fNP = $this->model_nota_pedido->m_validar_nota_pedido($allInputs['idnotapedido']);
    	if( $fNP['estado_movimiento'] == 2 ){ // facturado  
    		$arrData['message'] = 'Esta nota de pedido ya ha sido facturada. Operación rechazada'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
		if( $this->model_nota_pedido->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// var_dump($allInputs);exit();
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;  	
    	$this->db->trans_start();
		if($this->model_nota_pedido->m_editar($allInputs)) { // edicion de elemento
			$arrData['message'] = 'Se editaron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
