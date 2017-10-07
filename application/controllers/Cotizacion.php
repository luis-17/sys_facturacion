<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cotizacion extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_cotizacion','model_categoria_cliente','model_cliente_persona','model_cliente_empresa','model_configuracion','model_variable_car','model_banco_empresa_admin')); 
        $this->load->library('excel');
    	$this->load->library('Fpdfext');
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionFactur = @$this->session->userdata('sess_fact_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
	public function lista_cotizaciones_historial()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_cotizacion->m_cargar_cotizaciones($paramPaginate,$paramDatos); 
		$totalRows = $this->model_cotizacion->m_count_cotizaciones($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $row) { 
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
	public function generar_numero_cotizacion() 
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
		// C + ABV_SEDE(2) + AÑO(2) + MES(2) + DIA(2) + "X" CARACTERES (DINAMICO)
		// Ejm: CUC170827001 
		$sede = strtoupper($allInputs['sede']['abreviatura']); 
		$numCaracteres = $fConfig['cant_caracteres_correlativo_cot']; 
		$numCotizacion = 'C'.$sede.date('y'); 
		if($fConfig['incluye_mes_en_codigo_cot'] == 'si'){
			$numCotizacion .= date('m'); 
		}
		if($fConfig['incluye_dia_en_codigo_cot'] == 'si'){
			$numCotizacion .= date('d'); 
		}
		// OBTENER ULTIMA COTIZACION SEGÚN LOGICA DE CONFIGURACIÓN. 
		$allInputs['config'] = $fConfig; 
		$fCotizacion = $this->model_cotizacion->m_cargar_ultima_cotizacion_segun_config($allInputs);
		if( empty($fCotizacion) ){
			$numCorrelativo = 1;
		}else{
			$numCorrelativo = substr($fCotizacion['num_cotizacion'], ($numCaracteres * -1), $numCaracteres); 
			$numCorrelativo = (int)$numCorrelativo + 1;
		}
		$numCotizacion .= str_pad($numCorrelativo, $numCaracteres, '0', STR_PAD_LEFT);
	 	$arrDatos['num_cotizacion'] = $numCotizacion; 
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
	public function imprimir_cotizacion()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
	    // var_dump($allInputs); exit(); 
	    // RECUPERACIÓN DE DATOS 
	    $fConfig = obtener_parametros_configuracion();
	    $fila = $this->model_cotizacion->m_cargar_cotizacion_por_id($allInputs['id']);	 
	    $fila['moneda_str'] = NULL; 
	    $simbolo = NULL;
	    if($fila['moneda'] == 'S'){
	    	$fila['moneda_str'] = 'SOLES';
	    	$simbolo = 'S/. ';
	    }
	    if($fila['moneda'] == 'D'){
	    	$fila['moneda_str'] = 'DÓLARES';
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
	    $this->pdf->setEstado($fila['estado_cot']);
	    $this->pdf->AddPage('P','A4');//var_dump($allInputs['tituloAbv']); exit();
	    $this->pdf->AliasNbPages();
	    $this->pdf->SetAutoPageBreak(true,10);

	    $this->pdf->SetTextColor(95,95,95);
	    $this->pdf->SetFont('Arial','B',10);
        $this->pdf->SetXY(8,25);
        $this->pdf->MultiCell( 120,6,utf8_decode( $fila['razon_social_ea'] ) ); 
        $this->pdf->SetFont('Arial','',9);
        $this->pdf->SetXY(8,29);
        $this->pdf->MultiCell( 120,6,utf8_decode( $fila['direccion_legal'] ),0,'L' );
        $this->pdf->SetXY(8,33);
        $this->pdf->MultiCell( 120,6,'Sitio Web: ',0,'L' ); 
        	$this->pdf->SetXY(36,33);
        	$this->pdf->MultiCell( 120,6,utf8_decode(strtolower($fila['pagina_web']) ),0,'L' );
        $this->pdf->SetXY(8,37);
        $this->pdf->MultiCell( 120,6,utf8_decode('Teléfono: '),0,'L' );
	        $this->pdf->SetXY(36,37);
	        $this->pdf->MultiCell( 120,6,utf8_decode( $fila['telefono_ea'] ),0,'L' );

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('Arial','B',13);
        $this->pdf->SetXY(100,10);
        $this->pdf->Cell(120,10,utf8_decode( 'COTIZACIÓN N° '.$fila['num_cotizacion'] ),0,0);
        $this->pdf->Ln(15);
        if( @$this->estado == 1 ){ 
          $this->SetFont('Arial','B',50);
          $this->SetTextColor(255,192,203);
          $this->RotatedText(70,190,'A N U L A D O',45);  
        } 
      	
      	$this->pdf->SetXY(8,46);
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
		
		$this->pdf->SetXY(8,52); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(24,6,'CLIENTE '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,strtoupper(strtoupper_total($fila['cliente_persona_empresa'])));

      	$this->pdf->SetXY(8,56); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(24,6,strtoupper($fila['tipo_documento_abv'])); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,strtoupper($fila['num_documento_persona_empresa'])); 

		$this->pdf->SetXY(8,60); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(24,6,'CONTACTO '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,strtoupper(strtoupper_total(utf8_decode($fila['contacto']))));

      	$this->pdf->SetXY(8,64); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(24,6,'E-MAIL '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,strtoupper($fila['email_persona_empresa']));

      	$this->pdf->SetXY(96,52); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(36,6,utf8_decode('DIRECCIÓN ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,utf8_decode(strtoupper_total($fila['direccion_legal_ce'])));

      	$this->pdf->SetXY(96,56); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(36,6,utf8_decode('DIR. DESPACHO')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,strtoupper(strtoupper_total($fila['direccion_guia'])));
      	
      	$this->pdf->SetXY(96,60); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(36,6,utf8_decode('TELÉFONO ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,strtoupper($fila['telefono_ce']));

      	$this->pdf->SetXY(8,72); 
      	$this->pdf->SetFillColor($r,$g,$b);
		$this->pdf->SetWidths(array(60));
		$arrBarra = array(
			'data'=> array(
				utf8_decode('   DATOS DE LA COTIZACIÓN: ') 
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

		$this->pdf->SetXY(8,78); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(38,6,'ASESOR DE VENTA '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,strtoupper(strtoupper_total($fila['colaborador'])));

      	$this->pdf->SetXY(8,82); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(38,6,utf8_decode('FECHA EMISIÓN ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,darFormatoDMY($fila['fecha_emision'])); 

      	$this->pdf->SetXY(8,86); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(38,6,'MONEDA '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['moneda_str'])); 

      	$this->pdf->SetXY(8,90); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(38,6,utf8_decode('PLAZO DE ENTREGA(*) ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['plazo_entrega']. ' días útiles')); 

      	$this->pdf->SetXY(96,78); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(36,6,utf8_decode('CONDICIÓN DE PAGO ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['descripcion_fp'])); 

      	$this->pdf->SetXY(96,82); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(36,6,utf8_decode('VALIDEZ DE OFERTA ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,utf8_decode($fila['validez_oferta'].' días ')); 

      	$this->pdf->SetXY(96,86); 
      	$this->pdf->SetFont('Arial','B',9); 
      	$this->pdf->Cell(36,6,utf8_decode('PRECIO INCLUYE IGV ')); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(75,6,$strIncluyeIGV); 

      	$this->pdf->SetXY(8,97); 
      	$this->pdf->Cell(100,6,utf8_decode('Tenemos el agrado de presentar la siguiente cotización: ')); 

      	$this->pdf->SetXY(8,100); 
      	$this->pdf->Ln(4);
      	$x_final_izquierda = $this->pdf->GetX();
      	$y_final_izquierda = $this->pdf->GetY();

      	// APARTADO: DATOS DEL DETALLE

      	// LOGICA POSICION 
      	$this->pdf->SetFont('Arial','B',9);
      	$this->pdf->SetFillColor($r,$g,$b);
      	$this->pdf->SetTextColor(255,255,255);
      	$this->pdf->Cell(10,6,'ITEM',1,0,'L',TRUE);
      	$this->pdf->Cell(100,6,utf8_decode('DESCRIPCIÓN'),1,0,'L',TRUE);
      	$this->pdf->Cell(20,6,'U.M.',1,0,'C',TRUE);
      	$this->pdf->Cell(18,6,'CANT.',1,0,'C',TRUE);
      	$this->pdf->Cell(20,6,'P.U.',1,0,'C',TRUE);
      	$this->pdf->Cell(26,6,'IMPORTE',1,0,'C',TRUE); 
      	$this->pdf->Ln(8);

      	$this->pdf->SetFont('Arial','',8);
	    
	    $i = 1;
	    $detalleEle = $this->model_cotizacion->m_cargar_detalle_cotizacion_por_id($allInputs['id']);
	    //var_dump($detalleEle); exit(); 
	    $arrGroupBy=array();

	    foreach ($detalleEle as $key => $value) {
	    	// var_dump($detalleEle);
	    	$rowAux=array(
	    		'iddetallecotizacion' =>$value['iddetallecotizacion'],
	    		'descripcion_ele' =>$value['descripcion_ele'],
	    		'cantidad' =>$value['cantidad'],
	    		'abreviatura_um' =>$value['abreviatura_um'],
	    		'precio_unitario' =>$value['precio_unitario'],
	    		'importe_con_igv' =>$value['importe_con_igv'],
	    		'detallecaracteristica' =>array()
	    		);
	    	$arrGroupBy[$value['iddetallecotizacion']]=$rowAux;
	    }		
		foreach ($detalleEle as $key => $value) {
	    	$rowAux=array(
	    		'iddetallecaracteristica' =>$value['iddetallecaracteristica'],
	    		'descripcion_car' =>$value['descripcion_car'],
	    		'valor' =>$value['valor'],
	    		);
	    	$arrGroupBy[$value['iddetallecotizacion']]['detallecaracteristica'][$value['iddetallecaracteristica']]=$rowAux;
		}
	  	// var_dump($arrGroupBy);exit();
	    $exonerado = 0;
	    $fill = TRUE;
	    $this->pdf->SetDrawColor($r_sec,$g_sec,$b_sec); // gris fill 
	    $this->pdf->SetLineWidth(.1);
	    foreach ($arrGroupBy as $key => $value) { 
		// var_dump($arrGroupBy);exit();
	      $fill = !$fill;		
	      $this->pdf->SetWidths(array(10, 100, 20, 18, 20, 26));
	      $this->pdf->SetAligns(array('L', 'L', 'C', 'C', 'R', 'R'));
	      //$this->pdf->fill(array(TRUE, TRUE, TRUE, TRUE, TRUE, TRUE));
	      $this->pdf->SetFillColor($r_sec,$g_sec,$b_sec);
	      $this->pdf->SetTextColor(0,3,6);
	      $this->pdf->SetFont('Arial','B',6);
	      $this->pdf->RowSmall(   
	        array(
	          $i,
	          utf8_decode($value['descripcion_ele']),
	          strtoupper($value['abreviatura_um']),
	          $value['cantidad'],
	          $value['precio_unitario'],
	          $value['importe_con_igv']
	        ),
	        false, 0
	      );
	      $i++;
	      $this->pdf->SetTextColor(66,66,66);
	       $this->pdf->SetFont('Arial','',6);
	      		foreach ($value['detallecaracteristica'] as $key => $row) {
	      			$this->pdf->Cell(10,3,'',0,0,'C',0);  
	      			$this->pdf->Cell(184,3,utf8_decode($row['descripcion_car']).': '.$row['valor'],0,1,'L',0); 
	      		}
	      		$this->pdf->Cell(194,0,'','B',1,'C',0);  
	    }
	    $this->pdf->SetXY(8,-34); 
	    //$this->pdf->Ln(1);
	    $this->pdf->SetFont('Arial','B',9);
	    $en_letra = ValorEnLetras($fila['total'],$fila['moneda_str']);
	    $this->pdf->Cell(140,5,'TOTAL SON: ' . $en_letra);
	    $this->pdf->SetXY(8,-23); 
	    $this->pdf->SetFont('Arial','',8);
	    $bancoEmpresa = $this->model_banco_empresa_admin->m_cargar_banco_empresa_admin_por_id($fila['idempresaadmin']);
 		$this->pdf->SetTextColor(0,0,0);
   		$this->pdf->SetFont('Arial','B',9);
	    foreach ($bancoEmpresa as $key => $value) {
	    	$this->pdf->Cell(40,5,'Cta. Cte. '.$value['abreviatura_ba'].' '. $fila['moneda_str'],0,0,'L',0); 	  
	    }
	    $this->pdf->SetXY(8,-19); 
	    foreach ($bancoEmpresa as $key => $value) {
	    	$this->pdf->Cell(40,5,$value['num_cuenta'],0,0,'L',0); 	  
	    }
	    $this->pdf->SetXY(8,-35); 
	    $this->pdf->SetFont('Arial','',8);
	    $this->pdf->SetWidths(array(138));
	    // $this->pdf->TextArea(array(empty($fila['motivo_movimiento'])? '':$fila['motivo_movimiento']),0,0,FALSE,5,20);
	    $this->pdf->Cell(150,20,'');
	    $this->pdf->Cell(20,6,'SUBTOTAL:','LT',0,'R');
	    $this->pdf->SetFont('Arial','',8);
	    $this->pdf->Cell(20,6,$fila['subtotal'],'TR',0,'R');
	    $this->pdf->Ln(6);
	    $this->pdf->SetFont('Arial','',8);
	    $this->pdf->Cell(150,6,'');
	    $this->pdf->Cell(20,6,'IGV:','L',0,'R');
	    $this->pdf->SetFont('Arial','',8);
	    $this->pdf->Cell(20,6,$fila['igv'],'R',0,'R');
	    $this->pdf->Ln(6);
	    $this->pdf->SetFont('Arial','B',9);
	    $this->pdf->Cell(150,8,'');
	    $this->pdf->Cell(20,8,'TOTAL:','TLB',0,'R');
	    $this->pdf->Cell(20,8,$simbolo . $fila['total'],'TRB',0,'R');
	    // $this->pdf->Cell(30,8,$simbolo . substr($fila['total_a_pagar'], 4),'TRB',0,'R');
	    // $this->pdf->Ln(15);
	    // $monto = new EnLetras();
	    // $en_letra = ValorEnLetras($fila['total'],$fila['moneda_str']);
	    // $this->pdf->Cell(0,8,'TOTAL SON: ' . $en_letra ,'',0);
	    $arrData['message'] = 'ERROR';
	    $arrData['flag'] = 2;
	    // $timestamp = date('YmdHis');
	    if($this->pdf->Output( 'F','assets/dinamic/pdfTemporales/Cot_'. $fila['num_cotizacion'] .'.pdf' )){
	      $arrData['message'] = 'OK';
	      $arrData['flag'] = 1;
	    }
	    $arrData = array(
	      'urlTempPDF'=> 'assets/dinamic/pdfTemporales/Cot_'. $fila['num_cotizacion'] .'.pdf'
	    );
	    $this->output
	        ->set_content_type('application/json')
	        ->set_output(json_encode($arrData));
	}
}
