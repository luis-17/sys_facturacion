<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GuiaRemision extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_guia_remision','model_variable_car','model_serie', 'model_configuracion','model_caracteristica')); 
        $this->load->library('excel');
    	$this->load->library('Fpdfext');
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
    public function listar_guias_remision_historial()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_guia_remision->m_cargar_guias_remision($paramPaginate,$paramDatos); 
		$totalRows = $this->model_guia_remision->m_count_guias_remision($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['estado_gr'] == 1 ){ // REGISTRADO 
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'REGISTRADO';
			}
			if( $row['estado_gr'] == 0 ){ // ANULADO 
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'ANULADO';
			}
			array_push($arrListado, 
				array(
					'idguiaremision' => $row['idguiaremision'],
					'identify_trunc'=> $row['idguiaremision'],
					'num_serie_correlativo'=> $row['numero_serie'].'-'.$row['numero_correlativo'],
					'serie'=> $row['numero_serie'],
					'correlativo'=> $row['numero_correlativo'],
					'tipo_cliente' => $row['tipo_cliente'],
					//'fecha_registro' => darFormatoDMY($row['fecha_registro']),
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'fecha_inicio_traslado' => darFormatoDMY($row['fecha_inicio_traslado']),
					// 'idtipodocumentomov'=> $row['idtipodocumentomov'],
					// 'descripcion_tdm'=> strtoupper($row['descripcion_tdm']),
					'cliente' => trim($row['cliente_persona_empresa']),
					'colaborador_gen' => strtoupper($row['colaborador_gen']),
					'colaborador_asig'=> strtoupper($row['colaborador_asig']),
					'motivo_traslado'=> strtoupper($row['descripcion_mt']),
					'punto_partida'=> $row['punto_partida'],
					'punto_llegada'=> $row['punto_llegada'],
					'idempresaadmin' => $row['idempresaadmin'],
					'empresa_admin' => strtoupper($row['razon_social_ea']),
					'idusuario' => $row['idusuario'], 
					'estado_gr' => $row['estado_gr'],
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
    public function obtener_esta_guia_remision()
    {
    	$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
		$idguiaremision = $allInputs['identify']; 
		$fila = $this->model_guia_remision->m_cargar_guia_remision_por_id($idguiaremision);
		$detalleLista = $this->model_guia_remision->m_cargar_detalle_guia_remision_por_id($idguiaremision);
		$rowEstadoId = $fila['estado_gr']; 
		$rowEstadoDescripcion = NULL; 
		if( $fila['estado_gr'] == 0 ){ // ANULADO 
			$rowEstadoDescripcion = 'ANULADO'; 
		}
		if( $fila['estado_gr'] == 1 ){ // REGISTRADO 
			$rowEstadoDescripcion = 'REGISTRADO'; 
		}

		$arrListadoDetalle = array();
		$arrListado = array( 
			'tipo_documento_cliente'=> array(
				'id'=> $fila['idtipodocumentocliente'],
				'descripcion'=> $fila['tipo_documento_abv']
			),
			'num_serie'=> $fila['numero_serie'],
			// 'serie'=> array(
			// 	'id'=> $fila['idserie'],
			// 	'descripcion'=> $fila['numero_serie']
			// ),
			//'contacto'=> strtoupper($fila['contacto']),
			'idguiaremision'=> $fila['idguiaremision'],
			'num_serie_correlativo'=> $fila['numero_serie'].'-'.$fila['numero_correlativo'],
			'num_serie'=> $fila['numero_serie'],
			'num_correlativo'=> $fila['numero_correlativo'],
			'motivo_traslado'=> array( 
				'id'=> $fila['idmotivotraslado'],
				'descripcion'=> strtoupper($fila['descripcion_mt'])
			),
			'fecha_registro'=> darFormatoDMY($fila['fecha_registro']),
			'fecha_emision'=> darFormatoDMY($fila['fecha_emision']),
			// 'tipo_documento_mov'=> array(
			// 	'id'=> (int)$fila['idtipodocumentomov'],
			// 	'descripcion'=> strtoupper($fila['descripcion_tdm']) 
			// ),
			'colaborador'=> array(
				'id'=> (int)$fila['idcolaborador'],
				'colaborador'=> strtoupper($fila['colaborador']) 
			),
			'punto_partida'=> $fila['punto_partida'],
			'punto_llegada'=> $fila['punto_llegada'],
			'orden_compra'=> $fila['numero_orden_compra'],
			'nombres_razon_social_trans'=> $fila['nombres_razon_social_trans'],
			'domicilio_trans'=> $fila['domicilio_trans'],
			'ruc_dni_trans'=> $fila['ruc_trans'],
			'num_licencia_conducir'=> $fila['num_licencia_conducir'],
			'cert_inscripcion'=> $fila['num_constancia_inscripcion'],
			'fecha_inicio_traslado'=> $fila['fecha_inicio_traslado'],
			'marca_unidad'=> $fila['marca_transporte'],
			'placa_unidad'=> $fila['placa_transporte'],
			'peso_total'=> $fila['peso_total'],
			'costo_minimo'=> $fila['costo_minimo'],
			'idempresaadmin'=> $fila['idempresaadmin'],
			'idmovimiento'=> $fila['idmovimiento'], 
			'num_serie_venta'=> $fila['numero_serie_venta'], 
			'num_correlativo_venta'=> $fila['numero_correlativo_venta'], 
			'idtipodocumentoventa'=> $fila['idtipodocumentoventa'], 
			'tipo_documento_venta'=> $fila['tipodocumentoventa'], 
			'cliente' => array(),
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
				// 'telefono_contacto'=> $fila['telefono_fijo'],
				// 'anexo_contacto'=> $fila['anexo'],
				'colaborador' => array(
					'id'=> $fila['idcolaborador'],
					'descripcion'=> $fila['colaborador']
				),
				'ruc' => $fila['ruc_ce'],
				'num_documento' => $fila['ruc_ce'],
				'representante_legal' => $fila['representante_legal'],
				'dni_representante_legal' => $fila['dni_representante_legal'],
				'direccion_legal' => $fila['direccion_legal'],
				'telefono' => $fila['telefono']
			);
		}
		if( $fila['tipo_cliente'] === 'P' ){ 
			$arrListado['num_documento'] = $fila['num_documento_cp'];
			if( $fila['sexo'] == 'M' ){
				$fila['desc_sexo'] = 'MASCULINO';
			}
			if( $fila['sexo'] == 'F' ){
				$fila['desc_sexo'] = 'FEMENINO';
			}
			$arrListado['cliente'] = array( 
				'id' => $fila['idclientepersona'],
				'idclientepersona' => $fila['idclientepersona'],
				'nombres' => strtoupper($fila['nombres_cp']),
				'apellidos' => strtoupper($fila['apellidos_cp']),
				'cliente' => strtoupper($fila['cliente_persona']),
				'tipo_cliente' => 'cp',
				'num_documento' => $fila['num_documento_cp'],
				'colaborador' => array(
					'id'=> $fila['idcolaborador'],
					'descripcion'=> $fila['colaborador']
				),
				'sexo'=> array(
					'id'=> $fila['sexo'],
					'descripcion'=> $fila['desc_sexo'] 
				),
				'edad' => devolverEdad($fila['fecha_nacimiento']),
				'fecha_nacimiento' => darFormatoDMY($fila['fecha_nacimiento']),
				'fecha_nacimiento_str' => formatoFechaReporte3($fila['fecha_nacimiento']),
				'telefono_fijo' => $fila['telefono_fijo_cp'],
				'telefono_movil' => $fila['telefono_movil_cp'],
				'email' => $fila['email_persona_empresa']
			);
		}
		foreach ($detalleLista as $key => $row) { 
			$arrAux = array( 
				'idguiaremisiondetalle' => $row['idguiaremisiondetalle'],
				'idguiaremision' => $row['idguiaremision'],
				'idelemento' => $row['idelemento'], 
				'descripcion'=> $row['descripcion_ele'], 
				'elemento' => $row['descripcion_ele'], 
				'cantidad' => $row['cantidad'], 
				'unidad_medida' => array( 
					'id'=> $row['idunidadmedida'],
					'descripcion'=> $row['descripcion_um'] 
				),
				'caracteristicas' => array() 
			);
			$arrListadoDetalle[$row['idguiaremisiondetalle']] = $arrAux; 
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
				$arrListadoDetalle[$row['idguiaremisiondetalle']]['caracteristicas'][$row['iddetallecaracteristica']] = $arrAux2; 
				$arrListadoDetalle[$row['idguiaremisiondetalle']]['caracteristicas'] = array_values($arrListadoDetalle[$row['idguiaremisiondetalle']]['caracteristicas']);
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

		$arrData['flag'] = 1; 
		if(empty($fila)){ 
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
		// var_dump($allInputs); exit(); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		/* VALIDACIONES */ 
		if( count($allInputs['detalle']) < 1 ){
    		$arrData['message'] = 'No se ha agregado ningún elemento';
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
    	if( empty($allInputs['motivo_traslado']['id']) ){
    		$arrData['message'] = 'Debe tener asignado un motivo de traslado para poder registrar los datos';
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
    	$fGuiaRemision = $this->model_guia_remision->m_validar_guia_por_correlativo($allInputs['num_serie'],$allInputs['num_correlativo']); 
    	if( !empty($fGuiaRemision) ){ 
    		$arrData['message'] = 'Ya se a registrado una guia usando el correlativo <strong>'.$allInputs['num_serie'].'-'.$allInputs['num_correlativo'].'</strong>'; 
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
		if( $this->model_guia_remision->m_registrar($allInputs) ){ 
			$arrData['idguiaremision'] = GetLastId('idguiaremision','guia_remision'); 
			foreach ($allInputs['detalle'] as $key => $elemento) { 
				// var_dump($elemento);
				$elemento['idguiaremision'] = $arrData['idguiaremision']; 
				/* fix clon */
				if( empty($elemento['id']) ){ 
					$elemento['id'] = @$elemento['idelemento']; 
				}				
				if( $this->model_guia_remision->m_registrar_detalle($elemento) ){ 
					$arrData['message'] = 'Los datos se registraron correctamente - (no caracteristicas)'; 
					$arrData['flag'] = 1; 
					$arrData['idguiaremisiondetalle'] = GetLastId('idguiaremisiondetalle','guia_remision_detalle');
					if( !empty($elemento['caracteristicas']) ){ 
						foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
							if( !empty($caracteristica['valor']) ){ 
								$caracteristica['idguiaremisiondetalle'] = $arrData['idguiaremisiondetalle']; 
								/* fix clon */
								if( empty($caracteristica['idcaracteristica']) ){ 
									$caracteristica['idcaracteristica'] = @$caracteristica['id']; 
								}
								if( $this->model_guia_remision->m_registrar_detalle_caracteristica_gr($caracteristica) ){ 
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
				'tipo_documento_mov'=> array(
					'id'=> 6 // guia remision 
				), 
				'serie'=> $allInputs['serie'] 
			);
			if( $this->model_serie->m_actualizar_serie_correlativo_por_movimiento($arrDataSC) ){ 
				$arrData['message'] .= '<br /> - Se actualizó el correlativo correctamente'; 
				$arrData['flag'] = 1; 
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
	public function imprimir_guia_remision_html()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
 		$fila = $this->model_venta->m_cargar_venta_por_id($allInputs['id']); 
 		$detalleLista = $this->model_venta->m_cargar_detalle_venta_por_id($allInputs['id']); 
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
				   	width: 1270px;
				   	height: 1000px;
				   	font-size:10px; 
				   	font-family: sans-serif;
    			}
			}
			/** { outline: 2px dotted red }
			* * { outline: 2px dotted green }
			* * * { outline: 2px dotted orange }
			* * * * { outline: 2px dotted blue }
			* * * * * { outline: 1px solid red }
			* * * * * * { outline: 1px solid green }
			* * * * * * * { outline: 1px solid orange }
			* * * * * * * * { outline: 1px solid blue }*/
			.general{
				letter-spacing:4px;
			   	font-size:10px; 
			   	font-family: sans-serif;
			}
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
			.rowdt { width: 100%; text-align:left;font-size: 8px;margin-bottom: 8px; }
			.rowdt.compressed { margin-bottom:0; }
			.hidden{ visibility: hidden; }
    	</style>';

    	$htmlData .= '<div class="general">'; 
	    	// set numero correlativo 
	    	$posX_numCorrelativo = '266mm'; 
	    	$posY_numCorrelativo = '33mm'; 
	    	$htmlData .= '<div class="item" style="font-size:18px;top:'.$posY_numCorrelativo.';left:'.$posX_numCorrelativo.';">';
	    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_correlativo']));
	    	$htmlData .= '</div>';

	    	// set position nombre de cliente 
	    	$posX_nombreCliente = '30mm';
	    	$posY_nombreCliente = '31mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_nombreCliente.';left:'.$posX_nombreCliente.';">';
	    	$htmlData .= utf8_decode(strtoupper_total($fila['cliente_persona_empresa']));
	    	$htmlData .= '</div>';

	    	// set position direccion 
	    	$posX_direccion = '30mm';
	    	$posY_direccion = '39mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_direccion.';left:'.$posX_direccion.';">';
	    	$htmlData .= utf8_decode(strtoupper_total($fila['direccion_legal_ce']));
	    	$htmlData .= '</div>';

	    	// set position RUC  
	    	$posX_RUC = '30mm';
	    	$posY_RUC = '47mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_RUC.';left:'.$posX_RUC.';">';
	    	$htmlData .= utf8_decode(strtoupper_total($fila['num_documento_persona_empresa']));
	    	$htmlData .= '</div>';

	    	// set position fecha 
	    	$posX_fecha = '30mm';
	    	$posY_fecha = '56mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_fecha.';left:'.$posX_fecha.';">';
	    	$htmlData .= formatoFechaReporte3($fila['fecha_emision']);
	    	$htmlData .= '</div>';

	    	// set position telefono 
	    	$posX_telefono = '100mm';
	    	$posY_telefono = '47mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_telefono.';left:'.$posX_telefono.';">';
	    	$htmlData .= $fila['telefono_ce'];
	    	$htmlData .= '</div>';

	    	// set O/C 
	    	$posX_OC = '164mm';
	    	$posY_OC = '47mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_OC.';left:'.$posX_OC.';">';
	    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_orden_compra']));
	    	$htmlData .= '</div>';

	    	// set Condición de Pago 
	    	$posX_condicionPago = '234mm';
	    	$posY_condicionPago = '47mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_condicionPago.';left:'.$posX_condicionPago.';">';
	    	$htmlData .= utf8_decode(strtoupper_total($fila['descripcion_fp']));
	    	$htmlData .= '</div>';

	    	// set Vendedor 
	    	$posX_vendedor = '234mm';
	    	$posY_vendedor = '56mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_vendedor.';left:'.$posX_vendedor.';">';
	    	$htmlData .= utf8_decode(strtoupper_total($fila['colaborador_asig'])); 
	    	$htmlData .= '</div>';

			// $arrData['html'] = $htmlData;
			// $this->output
			//     ->set_content_type('application/json')
			//     ->set_output(json_encode($arrData));
			// return;
	    	/* DETALLE DE ITEMS */
	    	$arrCol = array('60','64','845','130','130'); // ancho de las columnas 
	    	$posX_detalle = '0mm';
	    	$posY_detalle = '69mm';
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

		    $strMoneda = NULL;
		    $strSimbolo = NULL;
		    if( $fila['moneda']=='S' ){
		    	$strMoneda = ' SOLES';
		    	$strSimbolo = 'S/. ';
		    }
		    if( $fila['moneda']=='D' ){
		    	$strMoneda = ' DÓLARES';
		    	$strSimbolo = 'US$ ';
		    }

		    // set número de cuenta 
		    $bancoEmpresa = $this->model_banco_empresa_admin->m_cargar_cuentas_banco_por_filtros($fila['idempresaadmin'],$fila['moneda']); 
		    $posX_numCuenta = '2mm'; 
	    	$posY_numCuenta = '168.5mm'; 
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


		    // set Monto en letras 
		    
		    $en_letra = ValorEnLetras($fila['total'],$strMoneda);
	    	$posX_montoEnLetras = '12mm';
	    	$posY_montoEnLetras = '178.5mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_montoEnLetras.';left:'.$posX_montoEnLetras.';">';
	    	$htmlData .= $en_letra; 
	    	$htmlData .= '</div>';

	    	// set subtotal
	    	$posX_subTotal = '296mm';
	    	$posY_subTotal = '185.5mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_subTotal.';left:'.$posX_subTotal.';">';
	    	$htmlData .= $strSimbolo.$fila['subtotal']; 
	    	$htmlData .= '</div>';

	    	// set % IGV
	    	$posX_IGV = '282mm';
	    	$posY_IGV = '190.5mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_IGV.';left:'.$posX_IGV.';">';
	    	$htmlData .= '18%'; 
	    	$htmlData .= '</div>';

	    	// set IGV
	    	$posX_IGV = '296mm';
	    	$posY_IGV = '190.5mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_IGV.';left:'.$posX_IGV.';">';
	    	$htmlData .= $strSimbolo.$fila['igv']; 
	    	$htmlData .= '</div>';

	    	// set total
	    	$posX_montoTotal = '296mm';
	    	$posY_montoTotal = '195mm';
	    	$htmlData .= '<div class="item" style="top:'.$posY_montoTotal.';left:'.$posX_montoTotal.';">';
	    	$htmlData .= $strSimbolo.$fila['total']; 
	    	$htmlData .= '</div>';

		$htmlData .= '</div>';
		$arrData['flag'] = 1;
		$arrData['html'] = $htmlData;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
