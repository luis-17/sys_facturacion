<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GuiaRemision extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_guia_remision','model_variable_car','model_serie', 'model_configuracion','model_caracteristica'
        	,'model_motivo_traslado','model_tipo_documento_mov')); 
        $this->load->library('excel');
    	$this->load->library('Fpdfext');
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
    public function ver_popup_busqueda_gr()
	{
		$this->load->view('guia-remision/busq_gr_popup'); 
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
			if( $row['estado_gr'] == 2 ){ // ASOCIADO  
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'ASOCIADO';
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
			'transportista' => array( 
				'id'=> $fila['idtransportista'],
				'descripcion' => $fila['nombres_trans'].' - '.$fila['ruc_trans'] 
			),
			'transporte' => array(
				'id'=> $fila['idtransporte'],
				'descripcion' => $fila['placa_transporte'].' '.$fila['marca_transporte'] 
			),
			'punto_partida'=> $fila['punto_partida'],
			'punto_llegada'=> $fila['punto_llegada'],
			'orden_compra'=> $fila['numero_orden_compra'],
			'nombres_razon_social_trans'=> $fila['nombres_trans'],
			'domicilio_trans'=> $fila['domicilio_trans'],
			'ruc_dni_trans'=> $fila['ruc_trans'],
			'num_licencia_conducir'=> $fila['num_lic_conducir'],
			'cert_inscripcion'=> $fila['num_cert_inscripcion'],
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
		// print_r($allInputs); exit(); 
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
								// NO GRABAR CARACTERISTICAS REPETIDAS EN COTIZACION 
								$fDetCarac = $this->model_guia_remision->m_validar_caracteristicas_repetidas($caracteristica['idcaracteristica'],$caracteristica['idguiaremisiondetalle']);
								if( empty($fDetCarac) ){ 
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
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// var_dump($allInputs); exit(); 
		/* VALIDACIONES */
		//validar que no sea una venta enviada 
    	$fGuia = $this->model_guia_remision->m_cargar_esta_guia_remision_por_id_simple($allInputs['idguiaremision']);
    	// validar que no sea una venta anulada 
    	if( $fGuia['estado_gr'] == 0 ){ // anulado 
    		$arrData['message'] = 'Esta Guia de Remisión ya ha sido anulada anteriormente. No se puede modificar.'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	$arrItemDetalle = array();
    	$this->db->trans_start();
    	if( $this->model_guia_remision->m_editar($allInputs) ){ 
    		foreach ($allInputs['detalle'] as $key => $elemento) {
    			if( !empty($elemento['idguiaremisiondetalle']) ){ 
					$arrItemDetalle[] = $elemento['idguiaremisiondetalle'];
				}
    		} 
    		// ANULAR ITEMS QUE HAN SIDO QUITADOS.
    		$listaDetalle = $this->model_guia_remision->m_cargar_detalle_guia_remision_por_id($allInputs['idguiaremision']); 
    		$arrDetalleAEliminar = array();
    		foreach ($listaDetalle as $key => $row) {
    			if( !(in_array($row['idguiaremisiondetalle'],$arrItemDetalle)) ){
    				$arrDetalleAEliminar[] = $row['idguiaremisiondetalle']; 
    			}
    		}
    		foreach ($arrDetalleAEliminar as $key => $val) { 
    			$arrDatos = array(
    				'idguiaremisiondetalle'=> $val 
    			);
    			$this->model_guia_remision->m_anular_guia_remision_detalle($arrDatos); 
    		}
    		foreach ($allInputs['detalle'] as $key => $elemento) { 
    			$elemento['idguiaremision'] = $allInputs['idguiaremision'];
    			/* fix clon */
				if( empty($elemento['id']) ){
					$elemento['id'] = @$elemento['idelemento']; 
				}
				if( empty($elemento['unidad_medida']['id']) && empty($elemento['unidad_medida']) ){ 
					$elemento['unidad_medida'] = NULL; 
				}
    			if( empty($elemento['idguiaremisiondetalle']) ){
    				// agregar un detalle a guia 
    				if($this->model_guia_remision->m_registrar_detalle($elemento)){
    					$arrData['message'] = 'Los datos se editaron correctamente - (no caracteristicas)'; 
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
									// NO GRABAR CARACTERISTICAS REPETIDAS EN GUIA  
									$fDetCarac = $this->model_guia_remision->m_validar_caracteristicas_repetidas($caracteristica['idcaracteristica'],$caracteristica['idguiaremisiondetalle']);
									if( empty($fDetCarac) ){
										if( $this->model_guia_remision->m_registrar_detalle_caracteristica_gr($caracteristica) ){ 
											$arrData['message'] = 'Los datos se agregaron correctamente'; 
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
    			}else{ 
    				// editar detalle de guia 
    				
					if( $this->model_guia_remision->m_editar_detalle($elemento) ){ 
						$arrData['message'] = 'Los datos se editaron correctamente - (no caracteristicas)'; 
						$arrData['flag'] = 1; 
						if( !empty($elemento['caracteristicas']) ){ 
							foreach ($elemento['caracteristicas'] as $keyCa => $caracteristica) { 
								//var_dump($caracteristica['iddetallecaracteristica']); exit();
								//print_r($caracteristica);
								if( !empty($caracteristica['iddetallecaracteristica']) ){ 
									//print_r($caracteristica); 
									if( $this->model_guia_remision->m_editar_detalle_caracteristica_gr($caracteristica) ){ 
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
										$caracteristica['idguiaremisiondetalle'] = $elemento['idguiaremisiondetalle'];
										/* fix clon */
										if( empty($caracteristica['idcaracteristica']) ){ 
											$caracteristica['idcaracteristica'] = @$caracteristica['id']; 
										}
										// NO GRABAR CARACTERISTICAS REPETIDAS EN GUIA  
										$fDetCarac = $this->model_guia_remision->m_validar_caracteristicas_repetidas($caracteristica['idcaracteristica'],$caracteristica['idguiaremisiondetalle']);
										if( empty($fDetCarac) ){
											if( $this->model_guia_remision->m_registrar_detalle_caracteristica_gr($caracteristica) ){ 
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
    	
    	$fGuia = $this->model_guia_remision->m_cargar_esta_guia_remision_por_id_simple($allInputs['idguiaremision']);
		// validar que no sea una guia anulada 
    	if( $fGuia['estado_gr'] == 0 ){ // anulado 
    		$arrData['message'] = 'Esta Guía de Remisión ya ha sido anulada anteriormente. No se puede anular.'; 
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}

		if( $this->model_guia_remision->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function imprimir_comprobante_guia_remision_html() 
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
		
 		$fila = $this->model_guia_remision->m_cargar_guia_remision_por_id($allInputs['id']); 
 		$detalleLista = $this->model_guia_remision->m_cargar_detalle_guia_remision_por_id($allInputs['id']); 
 		// print_r($fila); exit(); 
 		$allInputs['idtipodocumentomov'] = 6; // GUIA DE REMISIÓN 
 		$unidadMedidaPos = 'mm'; // UNIDAD DE MEDIDA DE POSICIÓN. 
 		$configTD = $this->model_tipo_documento_mov->m_cargar_configuracion_td($allInputs); // CONFIGURACION DE comprobante 
 		//print_r($configTD); exit(); 
 		$auxDetConfig = $this->model_tipo_documento_mov->m_cargar_configuracion_detalle_td($configTD); 
 		foreach ($auxDetConfig as $key => $row) {
 			$configTD['detalle'][$row['key_config_detalle']] = array(
 				'x'=> $row['valor_x'],
 				'y'=> $row['valor_y'],
 				'w'=> $row['valor_w'],
 				'visible'=> $row['visible']
 			);
 		}
 		// $configTD['detalle'] = 
 		// print_r($configTD); exit(); 
 		// PREPARACIÓN DE DATA: 
 		$arrListadoDetalle = array(); 
 		foreach ($detalleLista as $key => $row) { 
			$arrAux = array( 
				'idguiaremisiondetalle' => $row['idguiaremisiondetalle'], 
				'idguiaremision' => $row['idguiaremision'], 
				'idelemento' => $row['idelemento'], 
				'elemento' => $row['descripcion_ele'], 
				'descripcion' => $row['descripcion_ele'], 
				'cantidad' => $row['cantidad'], 
				'num_paquetes' => $row['num_paquetes'], 
				'unidad_medida' => array( 
					'id'=> $row['idunidadmedida'], 
					'descripcion'=> $row['descripcion_um'], 
					'abreviatura'=> $row['abreviatura_um'] 
				),
				'caracteristicas' => array() 
			);
			$arrListadoDetalle[$row['idguiaremisiondetalle']] = $arrAux; 
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
				$arrListadoDetalle[$row['idguiaremisiondetalle']]['caracteristicas'][$row['iddetallecaracteristica']] = $arrAux2; 
				$arrListadoDetalle[$row['idguiaremisiondetalle']]['caracteristicas'] = array_values($arrListadoDetalle[$row['idguiaremisiondetalle']]['caracteristicas']);
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
  		$configTD['unidad_medida'] = 'px'; 
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
				$posX_serieCorrelativo = $configTD['detalle']['serie_correlativo_key']['x'].$unidadMedidaPos; // '250mm'; 
		    	$posY_serieCorrelativo = $configTD['detalle']['serie_correlativo_key']['y'].$unidadMedidaPos; // '42mm'; 
		    	$htmlData .= '<div class="item" style="font-size:14px;top:'.$posY_serieCorrelativo.';left:'.$posX_serieCorrelativo.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_serie'])).'   -   ';
		    	$htmlData .= '</div>';
    		} 

	    	// set numero correlativo 
	    	if( $configTD['detalle']['num_correlativo_key']['visible'] == 1 ){ 
		    	$posX_numCorrelativo = $configTD['detalle']['num_correlativo_key']['x'].$unidadMedidaPos; // '260mm'; 
		    	$posY_numCorrelativo = $configTD['detalle']['num_correlativo_key']['y'].$unidadMedidaPos; // '42mm'; 
		    	$htmlData .= '<div class="item" style="font-size:14px;top:'.$posY_numCorrelativo.';left:'.$posX_numCorrelativo.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_correlativo']));
		    	$htmlData .= '</div>';
		    }

	    	// set position nombre de cliente 
	    	if( $configTD['detalle']['nombre_cliente_key']['visible'] == 1 ){ 
		    	$posX_nombreCliente = $configTD['detalle']['nombre_cliente_key']['x'].$unidadMedidaPos; //'30mm';
		    	$posY_nombreCliente = $configTD['detalle']['nombre_cliente_key']['y'].$unidadMedidaPos; //'31mm';
		    	$posW_nombreCliente = $configTD['detalle']['nombre_cliente_key']['w'].$unidadMedidaPos; //'200mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_nombreCliente.';left:'.$posX_nombreCliente.';width:'.$posW_nombreCliente.'">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['cliente_persona_empresa']));
		    	$htmlData .= '</div>';
		    }

	    	// set position punto de llegada 
	    	if( $configTD['detalle']['punto_llegada_key']['visible'] == 1 ){ 
		    	$posX_puntoLlegada = $configTD['detalle']['punto_llegada_key']['x'].$unidadMedidaPos;// '30mm';
		    	$posY_puntoLlegada = $configTD['detalle']['punto_llegada_key']['y'].$unidadMedidaPos;// '39mm';
		    	$posW_puntoLlegada = $configTD['detalle']['punto_llegada_key']['w'].$unidadMedidaPos;// '200mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_puntoLlegada.';left:'.$posX_puntoLlegada.';width:'.$posW_puntoLlegada.'">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['punto_llegada']));
		    	$htmlData .= '</div>';
		    }

	    	// set position RUC cliente 
	    	if( $configTD['detalle']['ruc_cliente_key']['visible'] == 1 ){ 
		    	$posX_RUC = $configTD['detalle']['ruc_cliente_key']['x'].$unidadMedidaPos; //'30mm';
		    	$posY_RUC = $configTD['detalle']['ruc_cliente_key']['y'].$unidadMedidaPos; //'47mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_RUC.';left:'.$posX_RUC.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['num_documento_persona_empresa']));
		    	$htmlData .= '</div>'; 
		    }

	    	// set position punto de partida 
	    	if( $configTD['detalle']['punto_partida_key']['visible'] == 1 ){ 
		    	$posX_puntoPartida = $configTD['detalle']['punto_partida_key']['x'].$unidadMedidaPos; //'30mm';
		    	$posY_puntoPartida = $configTD['detalle']['punto_partida_key']['y'].$unidadMedidaPos; //'56mm';
		    	$posW_puntoPartida = $configTD['detalle']['punto_partida_key']['w'].$unidadMedidaPos; //'56mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_puntoPartida.';left:'.$posX_puntoPartida.';width:'.$posW_puntoPartida.'">';
		    	$htmlData .= $fila['punto_partida'];
		    	$htmlData .= '</div>';
		    }

	    	// set position telefono 
	    	if( $configTD['detalle']['telefono_key']['visible'] == 1 ){ 
		    	$posX_telefono = $configTD['detalle']['telefono_key']['x'].$unidadMedidaPos; // '100mm';
		    	$posY_telefono = $configTD['detalle']['telefono_key']['y'].$unidadMedidaPos; // '47mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_telefono.';left:'.$posX_telefono.';">';
		    	$htmlData .= $fila['telefono_ce'];
		    	$htmlData .= '</div>';
		    }

	    	// set O/C 
	    	if( $configTD['detalle']['orden_compra_key']['visible'] == 1 ){ 
		    	$posX_OC = $configTD['detalle']['orden_compra_key']['x'].$unidadMedidaPos; // '164mm';
		    	$posY_OC = $configTD['detalle']['orden_compra_key']['y'].$unidadMedidaPos; // '47mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_OC.';left:'.$posX_OC.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['numero_orden_compra']));
		    	$htmlData .= '</div>';
		    }

	    	// set position fecha inicio traslado 
	    	if( $configTD['detalle']['fecha_ini_traslado_key']['visible'] == 1 ){
				$posX_fechaInicioTraslado = $configTD['detalle']['fecha_ini_traslado_key']['x'].$unidadMedidaPos;// '234mm'; 
		    	$posY_fechaInicioTraslado = $configTD['detalle']['fecha_ini_traslado_key']['y'].$unidadMedidaPos;// '47mm'; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_fechaInicioTraslado.';left:'.$posX_fechaInicioTraslado.';">';
		    	$htmlData .= formatoFechaReporte3($fila['fecha_inicio_traslado']);
		    	$htmlData .= '</div>';
	    	} 

	    	// set position fecha emision 
	    	if( $configTD['detalle']['fecha_emision_key']['visible'] == 1 ){ 
		    	$posX_fechaEmision = $configTD['detalle']['fecha_emision_key']['x'].$unidadMedidaPos; // '234mm'; 
		    	$posY_fechaEmision = $configTD['detalle']['fecha_emision_key']['y'].$unidadMedidaPos; // '51.5mm'; 
		    	$htmlData .= '<div class="item" style="top:'.$posY_fechaEmision.';left:'.$posX_fechaEmision.';">';
		    	$htmlData .= formatoFechaReporte3($fila['fecha_emision']);
		    	$htmlData .= '</div>';
		    } 

	    	// set peso total 
	    	if( $configTD['detalle']['peso_total_key']['visible'] == 1 ){ 
	    		$posX_pesoTotal = $configTD['detalle']['peso_total_key']['x'].$unidadMedidaPos; // '280mm';
		    	$posY_pesoTotal = $configTD['detalle']['peso_total_key']['y'].$unidadMedidaPos; // '47mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_pesoTotal.';left:'.$posX_pesoTotal.';">';
		    	$htmlData .= utf8_decode($fila['peso_total']);
		    	$htmlData .= '</div>';
	    	} 

	    	// set costo min. traslado
	    	if( $configTD['detalle']['costo_min_key']['visible'] == 1 ){ 
		    	$posX_costoMinTraslado = $configTD['detalle']['costo_min_key']['x'].$unidadMedidaPos; // '280mm';
		    	$posY_costoMinTraslado = $configTD['detalle']['costo_min_key']['y'].$unidadMedidaPos; // '51.5mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_costoMinTraslado.';left:'.$posX_costoMinTraslado.';">';
		    	$htmlData .= utf8_decode($fila['costo_minimo']);
		    	$htmlData .= '</div>';
		    }

	    	// set Vendedor 
	    	if( $configTD['detalle']['vendedor_key']['visible'] == 1 ){ 
		    	$posX_vendedor = $configTD['detalle']['vendedor_key']['x'].$unidadMedidaPos;// '234mm';
		    	$posY_vendedor = $configTD['detalle']['vendedor_key']['y'].$unidadMedidaPos;// '56mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_vendedor.';left:'.$posX_vendedor.';">';
		    	$htmlData .= utf8_decode(strtoupper_total($fila['abreviatura_nombre'])); 
		    	$htmlData .= '</div>';
		    }

	    	/* DETALLE DE ITEMS */
	    	$arrCol = array('60','64','975','90'); // ancho de las columnas 
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
					    	$htmlData .= $row['num_paquetes'];
					    	$htmlData .= '</div>';

				    	$htmlData .= '</div>';
			    	}
			    $htmlData .= '</div>'; 
			} 

		    // set motivo de traslado 
		    $listaMotivoTraslado = $this->model_motivo_traslado->m_cargar_motivo_traslado_cbo(); 
		    foreach ($listaMotivoTraslado as $key => $row) { 
		    	if($row['idmotivotraslado'] == $fila['idmotivotraslado'] ){ 
		    		$posX_montoEnLetras = $row['posicion_guia_x'].'mm';
			    	$posY_montoEnLetras = $row['posicion_guia_y'].'mm';
			    	$htmlData .= '<div class="item" style="top:'.$posY_montoEnLetras.';left:'.$posX_montoEnLetras.';">';
			    	$htmlData .= 'X'; 
			    	$htmlData .= '</div>';
		    	}
		    	
		    }
			
			// set nombres transp
			if( $configTD['detalle']['nombre_trans_key']['visible'] == 1 ){ 
		    	$posX_nombresTrans = $configTD['detalle']['nombre_trans_key']['x'].$unidadMedidaPos; // '112mm';
		    	$posY_nombresTrans = $configTD['detalle']['nombre_trans_key']['y'].$unidadMedidaPos; // '180.5mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_nombresTrans.';left:'.$posX_nombresTrans.';">';
		    	$htmlData .= $fila['nombres_trans']; 
		    	$htmlData .= '</div>';
		    }

	    	// set domiclio transp 
	    	if( $configTD['detalle']['domicilio_trans_key']['visible'] == 1 ){ 
		    	$posX_domicilioTrans = $configTD['detalle']['domicilio_trans_key']['x'].$unidadMedidaPos; //'112mm';
		    	$posY_domicilioTrans = $configTD['detalle']['domicilio_trans_key']['y'].$unidadMedidaPos; //'185mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_domicilioTrans.';left:'.$posX_domicilioTrans.';">';
		    	$htmlData .= $fila['domicilio_trans']; 
		    	$htmlData .= '</div>';
		    }

	    	// set ruc  
	    	if( $configTD['detalle']['ruc_trans_key']['visible'] == 1 ){ 
		    	$posX_rucTrans = $configTD['detalle']['ruc_trans_key']['x'].$unidadMedidaPos; // '112mm';
		    	$posY_rucTrans = $configTD['detalle']['ruc_trans_key']['y'].$unidadMedidaPos; // '189mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_rucTrans.';left:'.$posX_rucTrans.';">';
		    	$htmlData .= $fila['ruc_trans']; 
		    	$htmlData .= '</div>';
		    }

	    	// set marca y placa 
	    	if( $configTD['detalle']['marca_placa_key']['visible'] == 1 ){ 
				$posX_marcaPlaca = $configTD['detalle']['marca_placa_key']['x'].$unidadMedidaPos; // '260mm';
		    	$posY_marcaPlaca = $configTD['detalle']['marca_placa_key']['y'].$unidadMedidaPos; // '180.5mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_marcaPlaca.';left:'.$posX_marcaPlaca.';">';
		    	$htmlData .= $fila['marca_transporte'].' '.$fila['placa_transporte']; 
		    	$htmlData .= '</div>';
	    	}

	    	// set cert. inscripcion
	    	if( $configTD['detalle']['cert_inscripcion_key']['visible'] == 1 ){ 
		    	$posX_certInscripcion = $configTD['detalle']['cert_inscripcion_key']['x'].$unidadMedidaPos; //'260mm';
		    	$posY_certInscripcion = $configTD['detalle']['cert_inscripcion_key']['y'].$unidadMedidaPos; //'185mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_certInscripcion.';left:'.$posX_certInscripcion.';">';
		    	$htmlData .= $fila['num_cert_inscripcion']; 
		    	$htmlData .= '</div>';
		    }

	    	// set lic. conducir 
	    	if( $configTD['detalle']['lic_conducir_key']['visible'] == 1 ){ 
		    	$posX_licConducir = $configTD['detalle']['lic_conducir_key']['x'].$unidadMedidaPos; // '260mm';
		    	$posY_licConducir = $configTD['detalle']['lic_conducir_key']['y'].$unidadMedidaPos; // '189.5mm';
		    	$htmlData .= '<div class="item" style="top:'.$posY_licConducir.';left:'.$posX_licConducir.';">';
		    	$htmlData .= $fila['num_lic_conducir']; 
		    	$htmlData .= '</div>'; 
		    }
		$htmlData .= '</div>';
		$arrData['flag'] = 1;
		$arrData['html'] = $htmlData;

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function generar_pdf()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
	    // RECUPERACIÓN DE DATOS 
	    $fConfig = obtener_parametros_configuracion();
	    $fila = $this->model_guia_remision->m_cargar_guia_remision_por_id($allInputs['id']); 
	    
	    // CONFIGURACION DEL PDF
	    $this->pdf = new Fpdfext();
	    $this->pdf->SetMargins(8,8);
	    $this->pdf->setImagenCab('assets/dinamic/empresa/'.$fila['nombre_logo']); 
	    // $this->pdf->setEstado($fila['estado_cot']);
	    $this->pdf->AddPage('P','A4'); 
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
        $this->pdf->Cell(120,10,utf8_decode( 'CONTROL - GUIA DE REMISIÓN N° '.$fila['numero_serie'].'-'.$fila['numero_correlativo'] ),0,0);
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
				utf8_decode('   DATOS DEL CLIENTE: ')
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
		$y = $this->pdf->GetY();
		$this->pdf->SetXY(8,$y); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(24,4,'CLIENTE '); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
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
      	$this->pdf->Cell(24,4,utf8_decode(strtoupper_total('TELÉFONO '))); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->MultiCell(55,4,utf8_decode($fila['telefono_ce'])); 
  		
      	$y3 = $this->pdf->GetY();

      	$this->pdf->SetXY(96,$y); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(30,4,utf8_decode('PUNTO DE LLEGADA ')); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',7); 
      	$this->pdf->MultiCell(75,4,utf8_decode(strtoupper_total($fila['punto_llegada'])));

      	$y1a = $this->pdf->GetY();
      	$this->pdf->SetXY(96,$y1a); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(30,4,utf8_decode('PUNTO DE PARTIDA')); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',7); 
      	$this->pdf->MultiCell(75,4,utf8_decode(strtoupper_total($fila['punto_partida'])));

      	$this->pdf->SetXY(8,$y3+2); 
      	$this->pdf->SetFillColor($r,$g,$b);
		$this->pdf->SetWidths(array(60));
		$arrBarra = array(
			'data'=> array(
				utf8_decode('   DATOS DE LA GUIA: ') 
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
      	$this->pdf->MultiCell(45,4,strtoupper(strtoupper_total($fila['colaborador'])));

      	$y5 = $this->pdf->GetY();
      	$this->pdf->SetXY(8,$y5-1); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(38,6,utf8_decode('FECHA DE EMISIÓN')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,darFormatoDMY($fila['fecha_emision'])); 

      	$this->pdf->SetXY(8,$y5+3); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(38,6,'O/C '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['numero_orden_compra'])); 

      	$this->pdf->SetXY(8,$y5+7); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(38,6,utf8_decode('F. INIC. TRASLADO ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['fecha_inicio_traslado'])); 

      	$yToTransportista = $this->pdf->GetY();

      	$this->pdf->SetXY(96,$y4+3); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(34,6,utf8_decode('C. MIN. TRASL. ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,$fila['costo_minimo']); 

      	$this->pdf->SetXY(96,$y4+7); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(34,6,utf8_decode('PESO TOTAL ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,$fila['peso_total']); 

      	$this->pdf->SetXY(96,$y4+11); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(34,6,utf8_decode('MOTIVO DE TRASLADO ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['descripcion_mt'])); 

      	$this->pdf->SetXY(8,$yToTransportista + 7); 
      	$this->pdf->SetFillColor($r,$g,$b);
		$this->pdf->SetWidths(array(60));
		$arrBarra = array(
			'data'=> array(
				utf8_decode('   DATOS DE TRANSPORTISTA: ') 
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
		$ytre4 = $this->pdf->GetY();
		$this->pdf->SetXY(8,$ytre4); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(21,4,'NOMBRE '); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->MultiCell(45,4,strtoupper(strtoupper_total($fila['nombres_trans'])));

      	$ytre5 = $this->pdf->GetY();
      	$this->pdf->SetXY(8,$ytre5-1); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(21,6,utf8_decode('DOMICILIO')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,strtoupper_total($fila['domicilio_trans'])); 

      	$ytre6 = $this->pdf->GetY();
      	$this->pdf->SetXY(8,$ytre6+4); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(21,6,utf8_decode('RUC')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,$fila['ruc_trans']); 

      	$this->pdf->SetXY(96,$yToTransportista + 7); 
      	$this->pdf->SetFillColor($r,$g,$b);
		$this->pdf->SetWidths(array(60));
		$arrBarra = array(
			'data'=> array(
				utf8_decode('   DATOS DE TRANSPORTE: ') 
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
		$ytra4 = $this->pdf->GetY();
		$this->pdf->SetXY(96,$ytra4); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(34,4,utf8_decode('MARCA/PLACA N° ')); 
      	$this->pdf->Cell(3,4,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->MultiCell(45,4,strtoupper(strtoupper_total($fila['placa_transporte'])));

      	$ytra5 = $this->pdf->GetY();
      	$this->pdf->SetXY(96,$ytra5-1); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(34,6,utf8_decode('CERT. INSCRIPCIÓN N°')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,strtoupper_total($fila['num_cert_inscripcion'])); 

      	$ytra6 = $this->pdf->GetY();
      	$this->pdf->SetXY(96,$ytra6+4); 
      	$this->pdf->SetFont('Arial','B',8); 
      	$this->pdf->Cell(34,6,utf8_decode('LIC. CONDUCIR N°')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,strtoupper_total($fila['num_lic_conducir'])); 

      	// $this->pdf->SetXY(8,$y5+13); 
      	// $this->pdf->Cell(100,6,utf8_decode('Tenemos el agrado de presentar la siguiente nota pedido: ')); 

      	$this->pdf->SetXY(8,$ytre6+12); 
      	//$this->pdf->Ln(4);
      	$x_final_izquierda = $this->pdf->GetX();
      	$y_final_izquierda = $this->pdf->GetY();

      	// APARTADO: DATOS DEL DETALLE

      	// LOGICA POSICION 
      	$this->pdf->SetFont('Arial','B',8);
      	$this->pdf->SetFillColor($r,$g,$b);
      	$this->pdf->SetTextColor(255,255,255);
      	$this->pdf->Cell(20,6,'CANT.',1,0,'C',TRUE);
      	$this->pdf->Cell(24,6,'UNID. MED.',1,0,'L',TRUE);
      	$this->pdf->Cell(120,6,utf8_decode('DESCRIPCIÓN'),1,0,'L',TRUE);
      	$this->pdf->Cell(32,6,'PAQUETES',1,0,'C',TRUE); 
      	$this->pdf->Ln(7);

      	$this->pdf->SetFont('Arial','',7);
	    
	    $i = 1;
	    $detalleEle = $this->model_guia_remision->m_cargar_detalle_guia_remision_por_id($allInputs['id']);

	    $arrGroupBy = array(); 

	    foreach ($detalleEle as $key => $value) { 
	    		$rowAux = array(
		    		'idguiaremisiondetalle' =>$value['idguiaremisiondetalle'],
					'idguiaremision' => $value['idguiaremision'], 
					'idelemento' => $value['idelemento'], 
					'elemento' => $value['descripcion_ele'], 
					'descripcion' => $value['descripcion_ele'], 
					'cantidad' => $value['cantidad'], 
					'num_paquetes' => $value['num_paquetes'], 
					'unidad_medida' => array( 
						'id'=> $value['idunidadmedida'], 
						'descripcion'=> $value['descripcion_um'], 
						'abreviatura'=> $value['abreviatura_um'] 
					),
		    		'agrupado' => FALSE,
		    		'detallesubitems' => array(),
		    		'detallecaracteristica' =>array()
		    	); 
	    		$arrGroupBy[$value['idguiaremisiondetalle']] = $rowAux;
    	
	    } 

	    // caracteristicas
		foreach ($detalleEle as $key => $value) { 
			if( !empty($value['iddetallecaracteristica']) ){ 
				$rowAux = array( 
		    		'iddetallecaracteristica' => $value['iddetallecaracteristica'],
		    		'descripcion_car' => $value['descripcion_car'],
		    		'valor' => $value['valor'] 
		    	);
		    	$arrGroupBy[$value['idguiaremisiondetalle']]['detallecaracteristica'][$value['iddetallecaracteristica']] = $rowAux; 
	    		

	    	} 
		}
		// subitems 
		foreach ($detalleEle as $key => $value) {
			if( !empty($value['agrupador_totalizado']) ){ 
				$rowAux = array( 
					'elemento' => $value['descripcion_ele'], 
					'descripcion' => $value['descripcion_ele'], 
					'cantidad' => $value['cantidad'], 
					'num_paquetes' => $value['num_paquetes'], 
					'unidad_medida' => array( 
						'id'=> $value['idunidadmedida'], 
						'descripcion'=> $value['descripcion_um'], 
						'abreviatura'=> $value['abreviatura_um'] 
					),
		    	);
		    	$arrGroupBy[$value['agrupador_totalizado'].'.dif']['detallesubitems'][$value['idguiaremisiondetalle']] = $rowAux; 
	    	} 
		}

	    $exonerado = 0;
	    $fill = TRUE;
	    $this->pdf->SetDrawColor($r_sec,$g_sec,$b_sec); // gris fill 
	    $this->pdf->SetLineWidth(.1);
	    foreach ($arrGroupBy as $key => $value) { 

		    $fill = !$fill;		
		    $this->pdf->SetWidths(array(20, 24, 120, 32));
		    $this->pdf->SetAligns(array('L', 'L', 'L', 'C', 'R'));
		    $this->pdf->SetFillColor($r_sec,$g_sec,$b_sec);
		    $this->pdf->SetTextColor(0,3,6);
		    $this->pdf->SetFont('Arial','B',6); 
		    $arrItemDetalle = array(
				'fontSize'=> array(
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 ),
					array('family'=> NULL, 'weight'=> NULL, 'size'=> 8 )
				)			);
	
				$this->pdf->Row( 
			      array(
			        $value['cantidad'],
			        strtoupper($value['unidad_medida']['abreviatura']),
			        utf8_decode($value['descripcion']),			       
			        utf8_decode($value['num_paquetes']),
			   
			      ),
			      FALSE, 0, FALSE, 4, FALSE, FALSE, FALSE, FALSE, $arrItemDetalle['fontSize'] 
			    );
			    
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
			$this->pdf->Cell(194,0.8,'','B',1,'C',0); 
	    }

	 //    $this->pdf->SetFont('Arial','B',9);
	 //    $this->pdf->SetXY(8,-40);
	 //    $this->pdf->Cell(20,6,"OBSERVACIONES:"); 
	 //    $this->pdf->SetXY(8,-35); 
		// $this->pdf->SetFont('Arial','',8);
		// $this->pdf->SetWidths(array(138));
		// $this->pdf->TextArea(array(empty($fila['nombres_trans'])? '':$fila['nombres_trans']),0,0,FALSE,5,20);

		// $this->pdf->SetXY(8,-35); 
		// $this->pdf->Cell(150,20,'');
		// $this->pdf->Cell(20,6,'SUBTOTAL:','LT',0,'R');
		// $this->pdf->SetFont('Arial','',8);
		// $this->pdf->Cell(20,6,$simbolo . number_format($fila['subtotal'],$fConfig['num_decimal_total_key'],'.',' '),'TR',0,'R');
		// $this->pdf->Ln(6);
		// $this->pdf->SetFont('Arial','',8);
		// $this->pdf->Cell(150,6,'');
		// $this->pdf->Cell(20,6,'IGV:','L',0,'R');
		// $this->pdf->SetFont('Arial','',8);
		// $this->pdf->Cell(20,6,$simbolo . number_format($fila['igv'],$fConfig['num_decimal_total_key'],'.',' '),'R',0,'R');
		// $this->pdf->Ln(6);
		// $this->pdf->SetFont('Arial','B',9);
		// $this->pdf->Cell(150,8,'');
		// $this->pdf->Cell(20,8,'TOTAL:','TLB',0,'R');
		// $this->pdf->Cell(20,8,$simbolo . number_format($fila['total'],$fConfig['num_decimal_total_key'],'.',' '),'TRB',0,'R');
	    $arrData['message'] = 'ERROR';
	    $arrData['flag'] = 2;

	    if($this->pdf->Output( 'F','assets/dinamic/pdfTemporales/GR_'. $fila['numero_serie'].$fila['numero_correlativo'] .'.pdf' )){
	      $arrData['message'] = 'OK';
	      $arrData['flag'] = 1;
	    }
	    $arrData = array(
	      'urlTempPDF'=> 'assets/dinamic/pdfTemporales/GR_'. $fila['numero_serie'].$fila['numero_correlativo'] .'.pdf'
	    );
	    $this->output
	        ->set_content_type('application/json')
	        ->set_output(json_encode($arrData));
	}
}
