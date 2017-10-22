<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotaPedido extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_nota_pedido','model_cliente_persona','model_cliente_empresa','model_configuracion', 'model_cotizacion')); 
        $this->load->library('excel');
    	$this->load->library('Fpdfext');
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
    public function lista_notas_de_pedido_historial() // CONTINUAR DESDE AQUIIIIIIIIIIIIIIIIIIIIIII 
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
			// actualizar las cotizaciones a "ENVIADAS" 
			$boolEstado = 2; 
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
