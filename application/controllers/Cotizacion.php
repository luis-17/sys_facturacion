<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cotizacion extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_cotizacion','model_categoria_cliente','model_cliente_persona','model_cliente_empresa','model_configuracion')); 
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
		// C + ABV_SEDE(2) + AÑO(2) + MES(2) + DIA(2) + 3 CARACTERES (DINAMICO)
		// Ejm: CUC170827001 
		$sede = strtoupper($allInputs['sede']['abreviatura']); 
		$numCaracteres = 3; 
		$numCotizacion = 'C'.$sede.date('y').date('m').date('d'); 
		// OBTENER ULTIMA COTIZACION DE LA SEDE, Y DEL DÍA. 
		$fCotizacion = $this->model_cotizacion->m_cargar_ultima_cotizacion_sede_dia($allInputs);
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
	    // var_dump($orden); exit();  
	    // CONFIGURACION DEL PDF
	    $this->pdf = new Fpdfext();
	    $this->pdf->SetMargins(8,8);
	    $this->pdf->setImagenCab('assets/dinamic/empresa/'.$fila['nombre_logo']); 
	    $this->pdf->setEstado($fila['estado_cot']);
	    $this->pdf->AddPage('P','A4');//var_dump($allInputs['tituloAbv']); exit();
	    $this->pdf->AliasNbPages();
	    $this->pdf->SetAutoPageBreak(true,65);

	    $this->pdf->SetTextColor(95,95,95);
	    $this->pdf->SetFont('Arial','',9);
        $this->pdf->SetXY(8,25);
        $this->pdf->MultiCell( 120,6,utf8_decode( $fila['razon_social_ea'] ) ); 
        $this->pdf->SetXY(8,29);
        $this->pdf->MultiCell( 120,6,utf8_decode( $fila['direccion_legal'] ),0,'L' );
        $this->pdf->SetXY(8,33);
        $this->pdf->MultiCell( 120,6,utf8_decode( 'Sitio Web: '.strtolower($fila['pagina_web']) ),0,'L' );
        $this->pdf->SetXY(8,37);
        $this->pdf->MultiCell( 120,6,utf8_decode( 'Teléfono: '.$fila['telefono_ea'] ),0,'L' );
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFont('Arial','B',13);
        $this->pdf->SetXY(100,10);
        $this->pdf->Cell(120,10,utf8_decode( 'COTIZACIÓN N° '.$fila['num_cotizacion'] ),0,0);
        //$this->pdf->Line(292,20,4,20);
        $this->pdf->Ln(15);
        if( @$this->estado == 1 ){ 
          $this->SetFont('Arial','B',50);
          $this->SetTextColor(255,192,203);
          $this->RotatedText(70,190,'A N U L A D O',45);  
        }

	    // CABECERA 
	    // $this->SetFont('Arial','',6);
     //    $this->SetXY(-70,0);
     //  	//$this->MultiCell(120,6,'USUARIO:'.strtoupper($ci2->sessionHospital['username']).'    /   FECHA DE IMPRESION: '.date('Y-m-d H:i:s'));
     //  	//$this->Image($this->getImagenCab(),2,2,50); 
     //  	$varXPositionNE= 16;
     //  	$varXPositionDIR= 16;
     //  	// MODO FARMACIAA
     //  	if( $this->getModeReport() == 'F'  && $this->idEmpresaFarm == '12' ){ 
     //  	  $varXPositionNE= 2;
     //  	  $varXPositionDIR= 2;
     //  	  $this->SetTextColor(255,255,255);
     //  	}
     //  	$direccionOBJ = $this->getDireccion();
     //  	if( $ci2->sessionHospital['username'] === '46091867'  ){
     //  	  $direccionOBJ = 'CALLE ANDREA DEL SARTO NRO. 247 URB. LA CALERA DE LA MERCED - SURQUILLO - LIMA';
     //  	}
     //  	$this->SetFont('Arial','',5);
     //  	$this->SetXY($varXPositionNE,10);
     //  	$this->MultiCell( 120,6,strtoupper( $this->getNombreEmpresa() ) ); 
     //  	$this->SetXY($varXPositionDIR,12);
     //  	$this->SetFont('Arial','',4);
     //  	$this->MultiCell( 120,6,strtoupper( $direccionOBJ ) ); 
     //  	$this->SetTextColor(0,0,0); // texto para el titulo: color negro
     //  	$this->SetFont('Arial','B',13);
     //  	$this->SetXY(100,10);
     //  	$this->Cell(120,10,utf8_decode($this->getTitulo()),0,0);
     //  	$this->Line(350,20,4,20);
      	// $this->pdf->Ln(20);
      	$this->pdf->SetTextColor(0,0,0);
      	$this->pdf->SetXY(8,46);
      	// $this->pdf->SetFont('Arial','B',9);
      	// $this->pdf->Cell(24,6,utf8_decode('Proveedor'));
      	// $this->pdf->Cell(3,6,':',0,0,'C');
      	// $x=$this->pdf->GetX();
      	// $y=$this->pdf->GetY();
      	// $this->pdf->SetXY($x,$y+1);
      	$r = $fConfig['color_plantilla_reporte_r'];
		$g = $fConfig['color_plantilla_reporte_g'];
		$b = $fConfig['color_plantilla_reporte_b'];
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
				array('family'=> NULL, 'weight'=> NULL, 'size'=> 12),
				//array('family'=> NULL, 'weight'=> NULL, 'size'=> 10 )
			),
			'bgColor'=> array(
				array('r'=> $r, 'g'=> $g, 'b'=> $b ), 
				//array('r'=> 255, 'g'=> 255, 'b'=> 255 ) 
			)
		);
		$this->pdf->Row($arrBarra['data'],true,0,FALSE,6,$arrBarra['textColor'],$arrBarra['bgColor'],FALSE,FALSE,$arrBarra['fontSize']);

		$this->pdf->SetTextColor(0,0,0);
		
		$this->pdf->SetXY(8,52); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(24,6,'Contacto '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	//$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,$fila['contacto']);

      	$this->pdf->SetXY(8,58); 
      	$this->pdf->SetFont('Arial','',9); 
      	$this->pdf->Cell(24,6,'Cliente '); 
      	$this->pdf->Cell(3,6,':',0,0,'C'); 
      	//$this->pdf->SetFont('Arial','',8); 
      	$this->pdf->Cell(75,6,$fila['cliente_persona_empresa']);
      	// $this->pdf->Ln(4);
      	// $this->pdf->SetFont('Arial','B',9);
      	// $this->pdf->Cell(24,6,utf8_decode('Nombre Com.'));
      	// $this->pdf->Cell(3,6,':',0,0,'C');
      	$x=$this->pdf->GetX();
      	$y=$this->pdf->GetY();
      	$this->pdf->SetXY($x,$y+1);
      	$this->pdf->SetFont('Arial','',8);
      	$this->pdf->MultiCell(75,3,$fila['direccion_legal_ce']);
      	// $this->pdf->SetFont('Arial','B',9);
      	// $this->pdf->Cell(24,6, utf8_decode('Dirección'));
      	// $this->pdf->Cell(3,6,':',0,0,'C');
      	$this->pdf->MultiCell(75,3,$fila['telefono_ce']);
      	$x=$this->pdf->GetX();
      	$y=$this->pdf->GetY();
      	$this->pdf->SetXY($x,$y+1);
      	// $this->pdf->SetFont('Arial','B',9);
      	// $this->pdf->Cell(24,6, utf8_decode('Moneda'));
      	// $this->pdf->Cell(3,6,':',0,0,'C');
      	$this->pdf->SetFont('Arial','',8);
      	$this->pdf->Cell(75,6,$fila['moneda_str']);
      	$this->pdf->Ln(4);
      	$x_final_izquierda = $this->pdf->GetX();
      	$y_final_izquierda = $this->pdf->GetY();
      	// APARTADO: DATOS DE LA  ORDEN DE COMPRA
      	$this->pdf->SetXY($x,$y);

      	// APARTADO: DATOS DEL DETALLE

      	// LOGICA POSICION 
      	$y_final_derecha  = $this->pdf->GetY();
      
      	if($y_final_izquierda >= $y_final_derecha){
        	$y = $y_final_izquierda;
      	}else{
        	$y = $y_final_derecha;
      	}
      	$x = $x_final_izquierda;

      	$this->pdf->SetXY($x,$y+2);
      	$this->pdf->SetFont('Arial','',6);
      	$this->pdf->SetFillColor(128, 174, 220);
      	$this->pdf->Cell(8,10,'ITEM',1,0,'L',TRUE);
      	$this->pdf->Cell(53,10,'DESCRIPCIÓN',1,0,'L',TRUE);
      	$this->pdf->Cell(10,10,'CANT.',1,0,'C',TRUE);
      	$this->pdf->Cell(12,10,'P.U.',1,0,'C',TRUE);
      	$this->pdf->Cell(16,10,'IMPORTE',1,0,'C',TRUE); 
      	$this->pdf->Ln(1);

      	$this->pdf->SetFont('Arial','',8);
	    
	    $i = 1;
	    $detalleEle = $this->model_cotizacion->m_cargar_detalle_cotizacion_por_id($allInputs['id']);
	    //var_dump($detalleEle); exit();
	    $exonerado = 0;
	    $fill = TRUE;
	    $this->pdf->SetDrawColor(204,204,204); // gris fill 
	    $this->pdf->SetLineWidth(.2);

	    foreach ($detalleEle as $key => $value) { 
	      $fill = !$fill;
	      $this->pdf->SetWidths(array(8, 53, 15, 10, 12));
	      $this->pdf->SetAligns(array('L', 'L', 'C', 'R', 'R'));
	      //$this->pdf->fill(array(TRUE, TRUE, TRUE, TRUE, TRUE, TRUE));
	      $this->pdf->SetFillColor(230, 240, 250);
	      $this->pdf->SetFont('Arial','',6);
	      $this->pdf->RowSmall( 
	        array(
	          $i,
	          utf8_decode($value['descripcion_ele']),
	          $value['cantidad'],
	          $value['precio_unitario'],
	          $value['importe_con_igv']
	        ),
	        $fill,1
	      );
	      $i++;
	    }
	    $this->pdf->Ln(1);
	    $this->pdf->SetFont('Arial','B',9);
	    $this->pdf->Cell(140,5,'Observaciones');
	    $this->pdf->Ln(5);
	    $this->pdf->SetFont('Arial','',8);

	    $this->pdf->SetWidths(array(138));
	    $this->pdf->TextArea(array(empty($fila['motivo_movimiento'])? '':$fila['motivo_movimiento']),0,0,FALSE,5,20);

	    $this->pdf->Cell(2,20,'');
	    $this->pdf->Cell(20,6,'SUBTOTAL:','LT',0,'R');
	    $this->pdf->SetFont('Arial','',8);
	    $this->pdf->Cell(30,6,$fila['subtotal'],'TR',0,'R');
	    $this->pdf->Ln(6);
	    $this->pdf->SetFont('Arial','',8);
	    $this->pdf->Cell(140,6,'');
	    $this->pdf->Cell(20,6,'IGV:','L',0,'R');
	    $this->pdf->SetFont('Arial','',8);
	    $this->pdf->Cell(30,6,$fila['igv'],'R',0,'R');
	    $this->pdf->Ln(6);
	    $this->pdf->SetFont('Arial','B',9);
	    $this->pdf->Cell(140,8,'');
	    $this->pdf->Cell(20,8,'TOTAL:','TLB',0,'R');
	    $this->pdf->Cell(30,8,$simbolo . $fila['total'],'TRB',0,'R');
	    // $this->pdf->Cell(30,8,$simbolo . substr($fila['total_a_pagar'], 4),'TRB',0,'R');
	    $this->pdf->Ln(15);
	    // $monto = new EnLetras();
	    $en_letra = ValorEnLetras($fila['total'],$fila['moneda_str']);
	    $this->pdf->Cell(0,8,'TOTAL SON: ' . $en_letra ,'',0);

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
