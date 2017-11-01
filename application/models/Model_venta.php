<?php
class Model_venta extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_ventas($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('ve.idmovimiento, ve.fecha_registro, ve.fecha_emision, ve.fecha_vencimiento, ve.fecha_ultimo_pago, ve.numero_serie, ve.numero_correlativo, 
			ve.tipo_cliente, ve.plazo_entrega, ve.numero_orden_compra, ve.incluye_entrega_domicilio, ve.validez_oferta, ve.moneda, ve.modo_igv, ve.subtotal, 
			ve.igv, ve.total, ve.estado_movimiento, tdm.idtipodocumentomov, tdm.descripcion_tdm, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->join('tipo_documento_mov tdm','ve.idtipodocumentomov = tdm.idtipodocumentomov'); 
		$this->db->join('usuario us','ve.idusuarioventa = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','ve.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('ve.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_ve']) && $paramDatos['estado_ve']['id'] !== 'ALL' ){ 
			$this->db->where('ve.estado_movimiento', $paramDatos['estado_ve']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_ventas($paramPaginate,$paramDatos)
	{ 
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->join('tipo_documento_mov tdm','ve.idtipodocumentomov = tdm.idtipodocumentomov'); 
		$this->db->join('usuario us','ve.idusuarioventa = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','ve.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('ve.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_ve']) && $paramDatos['estado_ve']['id'] !== 'ALL' ){ 
			$this->db->where('ve.estado_movimiento', $paramDatos['estado_ve']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_ventas_detalle($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(col.nombres, ' ', col.apellidos) AS colaborador",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('ve.idmovimiento, ve.fecha_registro, ve.fecha_emision, ve.tipo_cliente, ve.plazo_entrega, ve.validez_oferta, 
			ve.moneda, ve.modo_igv, ve.subtotal, ve.igv, ve.total, ve.estado_movimiento, ve.num_cotizacion, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, 
			dve.iddetallecotizacion, dve.cantidad, dve.precio_unitario, dve.importe_con_igv, dve.importe_sin_igv, 
			dve.excluye_igv, dve.igv_detalle, dve.agrupador_totalizado, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->join('colaborador col','ve.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','ve.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'p'",'left'); 
		// $this->db->join('sede se','ve.idsede = se.idsede'); 
		// $this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		$this->db->join('detalle_cotizacion dcot','ve.idmovimiento = dve.idmovimiento'); 
		$this->db->join('elemento ele','dve.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		if( !empty($paramDatos['desde']) || !empty($paramDatos['hasta'])){
			$this->db->where('ve.fecha_emision BETWEEN '. $this->db->escape($paramDatos['desde'].' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
				. $this->db->escape($paramDatos['hasta'].' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		}
		if(!empty($paramDatos['estado_movimiento']) && $paramDatos['estado_movimiento']['id'] !== 'ALL' ){ 
			$this->db->where('ve.estado_movimiento', $paramDatos['estado_movimiento']['id']);
		} 
		$this->db->where_in('ve.estado_movimiento', array(1,2)); // por enviar y enviado 
		$this->db->where_in('dve.estado_dcot', array(1)); // habilitado 
		$this->db->where_in('ele.estado_ele', array(1)); // habilitado 
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_ventas_detalle($paramPaginate,$paramDatos)
	{ 
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('movimiento ve'); 
		//$this->db->join('colaborador col','ve.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','ve.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'p'",'left'); 
		// $this->db->join('sede se','ve.idsede = se.idsede'); 
		// $this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		$this->db->join('detalle_cotizacion dcot','ve.idmovimiento = dve.idmovimiento'); 
		$this->db->join('elemento ele','dve.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		if( !empty($paramDatos['desde']) || !empty($paramDatos['hasta'])){
			$this->db->where('ve.fecha_emision BETWEEN '. $this->db->escape($paramDatos['desde'].' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
				. $this->db->escape($paramDatos['hasta'].' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		}
		if(!empty($paramDatos['estado_movimiento']) && $paramDatos['estado_movimiento']['id'] !== 'ALL' ){ 
			$this->db->where('ve.estado_movimiento', $paramDatos['estado_movimiento']['id']);
		} 
		$this->db->where_in('ve.estado_movimiento', array(1,2)); // por enviar y enviado 
		$this->db->where_in('dve.estado_dcot', array(1)); // habilitado 
		$this->db->where_in('ele.estado_ele', array(1)); // habilitado 
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_ultima_venta_segun_config($datos)
	{
		$this->db->select('co.idmovimiento, co.num_cotizacion');
		$this->db->from('cotizacion co');
		$this->db->join('sede se', 'co.idsede = se.idsede');
		$this->db->where_in('co.estado_movimiento',array(1,2)); // solo "por enviar" y "enviado" 
		//$this->db->where('se.idsede',$datos['sede']['id']);
		if($datos['config']['incluye_mes_en_codigo_cot'] == 'no' && $datos['config']['incluye_dia_en_codigo_cot'] == 'no'){
			$this->db->where('YEAR(DATE(co.fecha_registro))', (int)date('Y')); // año 
		}
		if($datos['config']['incluye_mes_en_codigo_cot'] == 'si' && $datos['config']['incluye_dia_en_codigo_cot'] == 'no'){
			$this->db->where('YEAR(DATE(co.fecha_registro))', (int)date('Y')); // año 
			$this->db->where("DATE_FORMAT(DATE(co.fecha_registro),'%m')",date('m')); // mes 
		}
		if($datos['config']['incluye_mes_en_codigo_cot'] == 'si' && $datos['config']['incluye_dia_en_codigo_cot'] == 'si'){
			$this->db->where('DATE(co.fecha_registro)',date('Y-m-d')); // año, mes y dia
		}
		$this->db->where('co.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->order_by('co.fecha_registro','DESC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_validar_venta_por_correlativo($numSerie,$numCorrelativo,$idtipodocumentomov)
	{
		$this->db->select('ve.idmovimiento, ve.numero_serie, ve.numero_correlativo', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->where('ve.numero_serie', $numSerie ); 
		$this->db->where('ve.numero_correlativo', $numCorrelativo ); 
		$this->db->where('ve.idtipodocumentomov',$idtipodocumentomov);
		$this->db->where('ve.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where('tipo_movimiento',2); // facturacion 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_venta_por_id($idmovimiento)
	{
		$this->db->select("CONCAT(COALESCE(ct.nombres,''), ' ', COALESCE(ct.apellidos,'')) AS contacto",FALSE);
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) AS colaborador",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))) AS cliente_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.email,''), ' ', COALESCE(ct.email,''))) AS email_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.abreviatura_tdc,''), ' ', COALESCE(tdc_cp.abreviatura_tdc,''))) AS tipo_documento_abv",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(ce.ruc,''), ' ', COALESCE(cp.num_documento,''))) AS num_documento_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('ve.idmovimiento, ve.num_cotizacion, ve.fecha_registro, ve.fecha_emision, ve.tipo_cliente, ve.plazo_entrega, 
			ve.validez_oferta, ve.moneda, ve.modo_igv, ve.subtotal, ve.igv, ve.total, ve.estado_movimiento, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, col.email, col.cargo, col.telefono ,us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ea.nombre_logo, ea.direccion_legal, ea.pagina_web, (ea.telefono) AS telefono_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, (ce.telefono) AS telefono_ce, 
			ce.direccion_guia, (ce.direccion_legal) AS direccion_legal_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, 
			se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp, ct.idcontacto', FALSE); 
		$this->db->from('movimiento ve'); 
		//$this->db->join('colaborador col','ve.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','ve.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_ce","ce.idtipodocumentocliente = tdc_ce.idtipodocumentocliente",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'P'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_cp","cp.idtipodocumentocliente = tdc_cp.idtipodocumentocliente",'left'); 
		$this->db->join('sede se','ve.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		$this->db->join('contacto ct','ve.idcontacto = ct.idcontacto','left'); 
		$this->db->where_in( 've.idmovimiento', array($idmovimiento) ); 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_detalle_venta_por_id($idmovimiento,$iddetallecotizacion=NULL)
	{ 
		$this->db->select('dve.iddetallecotizacion, ve.idmovimiento, ve.num_cotizacion, ve.fecha_registro, ve.subtotal, ve.igv, ve.total, ve.estado_movimiento, ve.idempresaadmin, 
			dve.cantidad, dve.precio_unitario, dve.importe_con_igv, dve.importe_sin_igv, 
			dve.excluye_igv, dve.igv_detalle, dve.agrupador_totalizado, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento, c.idcaracteristica, c.orden_car, c.descripcion_car, dc.iddetallecaracteristica,dc.valor', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->join('detalle_cotizacion dcot','ve.idmovimiento = dve.idmovimiento'); 
		$this->db->join('elemento ele','dve.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida'); 
		$this->db->join("detalle_caracteristica dc","dc.iddetalle = dve.iddetallecotizacion AND dc.tipo_detalle = 'C'",'left'); 
		$this->db->join('caracteristica c','dc.idcaracteristica = c.idcaracteristica','left'); 
		$this->db->where('ve.idmovimiento',$idmovimiento); 
		if( $iddetallecotizacion ){
			$this->db->where('dve.iddetallecotizacion',$iddetallecotizacion); 
		}
		$this->db->where('estado_dcot',1); // detalle ve. habilitado 
		$this->db->order_by('c.orden_car','ASC');
		$this->db->order_by('c.descripcion_car','ASC');
		return $this->db->get()->result_array();
	}
	public function m_registrar_venta($datos)
	{
		$data = array( 

			'tipo_cliente' => $datos['tipo_cliente'],
			'idcliente' => $datos['cliente']['id'],
			'idaperturacaja' => empty($datos['idaperturacaja']) ? NULL : $datos['idaperturacaja'],
			'idusuarioventa' => $this->sessionFactur['idusuario'],
			'dir_movimiento' => 'E',
			'tipo_movimiento' => 2,
			'idtipodocumentomov' => $datos['tipo_documento_mov']['id'],
			'numero_orden_compra' => empty($datos['orden_compra']) ? NULL : $datos['orden_compra'],
			'idempresaadmin' => $this->sessionFactur['idempresaadmin'],
			'idsede' => $datos['sede']['id'],
			'idcontacto' => empty($datos['idcontacto']) ? NULL : $datos['idcontacto'], 
			'fecha_registro' => date('Y-m-d H:i:s'),
			'fecha_emision' => darFormatoYMD($datos['fecha_emision']),
			'numero_serie' => $datos['num_serie'],
			'numero_correlativo' => $datos['num_correlativo'],
			'idformapago' => $datos['forma_pago']['id'],
			'moneda' => $datos['moneda']['str_moneda'],
			'modo_igv' => $datos['modo_igv'],
			'subtotal' => $datos['subtotal'],
			'igv' => $datos['igv'],
			'total' => $datos['total'],
			'incluye_traslado_prov' => $datos['incluye_tras_prov'],
			'incluye_entrega_domicilio' => $datos['incluye_entr_dom'], 
			'plazo_entrega' => $datos['plazo_entrega'],
			'validez_oferta' => $datos['validez_oferta']
		); 
		return $this->db->insert('movimiento', $data); 
	}
	public function m_registrar_detalle_venta($datos)
	{
		$data = array(
			'idmovimiento' => $datos['idmovimiento'],	
			'idelemento' => $datos['idelemento'],
			'idunidadmedida' => $datos['unidad_medida']['id'], 
			'cantidad' => $datos['cantidad'],
			'precio_unitario' => $datos['precio_unitario'],
			'importe_con_igv' => $datos['importe_con_igv'],
			'importe_sin_igv' => $datos['importe_sin_igv'],
			'excluye_igv' => $datos['excluye_igv'],
			'igv_detalle' => $datos['igv'],
			'idmovimiento' => empty($datos['idmovimiento']) ? NULL : $datos['idmovimiento'], 
			'iddetallecotizacion' => empty($datos['iddetallecotizacion']) ? NULL : $datos['iddetallecotizacion'] 
		);
		return $this->db->insert('detalle_movimiento', $data); 
	}
	public function m_registrar_detalle_caracteristica_venta($datos) 
	{ 
		$data = array(
			'tipo_detalle' => 'VE', // VENTA  
			'iddetalle' => $datos['iddetalleventa'],
			'idcaracteristica' => $datos['id'],
			'valor' => strtoupper($datos['valor'])
		);
		return $this->db->insert('detalle_caracteristica', $data); 
	} 
	public function m_actualizar_venta_anulado($arrIdVentas)
	{
		$data = array(
			'estado_movimiento' => 0, //anulado 
			'fecha_anulacion' => date('Y-m-d H:i:s')
		);
		$this->db->where_in('idmovimiento',$arrIdVentas); 
		return $this->db->update('movimiento', $data); 
	}
} 
?>