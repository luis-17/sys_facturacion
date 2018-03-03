<?php
class Model_venta extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_ventas($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) AS colaborador_gen",FALSE);
		$this->db->select("CONCAT(COALESCE(col_asig.nombres,''), ' ', COALESCE(col_asig.apellidos,'')) AS colaborador_asig",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) AS cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('ve.idmovimiento, ve.fecha_registro, ve.fecha_emision, ve.fecha_vencimiento, ve.fecha_ultimo_pago, ve.numero_serie, ve.numero_correlativo, 
			ve.tipo_cliente, ve.plazo_entrega, ve.numero_orden_compra, ve.incluye_entrega_domicilio, ve.validez_oferta, ve.moneda, ve.modo_igv, ve.subtotal, 
			ve.igv, ve.total, ve.estado_movimiento, ve.tiene_detraccion, tdm.idtipodocumentomov, tdm.descripcion_tdm, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->join('tipo_documento_mov tdm','ve.idtipodocumentomov = tdm.idtipodocumentomov'); 
		$this->db->join('usuario us','ve.idusuarioventa = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('colaborador col_asig','ve.idcolaboradorv = col_asig.idcolaborador','left'); 
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
		$this->db->join('colaborador col_asig','ve.idcolaboradorv = col_asig.idcolaborador','left'); 
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
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('ved.iddetallemovimiento, ve.fecha_registro, ve.fecha_emision, ve.fecha_vencimiento, ve.fecha_ultimo_pago, ve.numero_serie, ve.numero_correlativo, ve.tipo_cliente, ve.plazo_entrega, ve.numero_orden_compra, ve.incluye_entrega_domicilio, ve.validez_oferta, ve.moneda, ve.modo_igv, ve.subtotal,ve.igv, ve.total, ve.estado_movimiento, ve.tiene_detraccion, tdm.idtipodocumentomov, tdm.descripcion_tdm, col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username,ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea,ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se,fp.idformapago, fp.descripcion_fp,ele.idelemento, ele.descripcion_ele, ele.tipo_elemento,cael.idcategoriaelemento,cael.descripcion_cael,ved.cantidad,ved.importe_sin_igv,ved.importe_con_igv,ved.precio_unitario,ved.igv_detalle', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->join('tipo_documento_mov tdm','ve.idtipodocumentomov = tdm.idtipodocumentomov'); 
		$this->db->join('usuario us','ve.idusuarioventa = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','ve.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		$this->db->join('detalle_movimiento ved','ved.idmovimiento = ve.idmovimiento'); 
		$this->db->join('elemento ele','ved.idelemento = ele.idelemento'); 
		$this->db->join('categoria_elemento cael','ele.idcategoriaelemento = cael.idcategoriaelemento'); 

		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		if( !empty($paramDatos['desde']) || !empty($paramDatos['hasta'])){
			$this->db->where('ve.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		}
		if(!empty($paramDatos['estado_movimiento']) && $paramDatos['estado_movimiento']['id'] !== 'ALL' ){ 
			$this->db->where('ve.estado_movimiento', $paramDatos['estado_movimiento']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}		
		if(!empty($paramDatos['categoria_elemento']) && $paramDatos['categoria_elemento']['id'] !== 'ALL' ){ 
			$this->db->where('cael.idcategoriaelemento', $paramDatos['categoria_elemento']['id']);
		}
		$this->db->where_in('ve.estado_movimiento', array(1,2)); // por enviar y enviado 
		// $this->db->where_in('dve.estado_dcot', array(1)); // habilitado 
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
		$this->db->join('tipo_documento_mov tdm','ve.idtipodocumentomov = tdm.idtipodocumentomov'); 
		$this->db->join('usuario us','ve.idusuarioventa = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','ve.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		$this->db->join('detalle_movimiento ved','ved.idmovimiento = ve.idmovimiento'); 
		$this->db->join('elemento ele','ved.idelemento = ele.idelemento'); 
		$this->db->join('categoria_elemento cael','ele.idcategoriaelemento = cael.idcategoriaelemento'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		if( !empty($paramDatos['desde']) || !empty($paramDatos['hasta'])){
			$this->db->where('ve.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		}
		if(!empty($paramDatos['estado_movimiento']) && $paramDatos['estado_movimiento']['id'] !== 'ALL' ){ 
			$this->db->where('ve.estado_movimiento', $paramDatos['estado_movimiento']['id']);
		} 
		if(!empty($paramDatos['categoria_elemento']) && $paramDatos['categoria_elemento']['id'] !== 'ALL' ){ 
			$this->db->where('cael.idcategoriaelemento', $paramDatos['categoria_elemento']['id']);
		}
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where_in('ve.estado_movimiento', array(1,2)); // por enviar y enviado 
		// $this->db->where_in('dve.estado_dcot', array(1)); // habilitado 
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
	public function m_cargar_esta_venta_por_id_simple($idventa)
	{
		$this->db->select('ve.idmovimiento, ve.estado_movimiento'); 
		$this->db->from('movimiento ve');
		$this->db->join('sede se', 've.idsede = se.idsede');
		$this->db->where_in('ve.estado_movimiento', array(0,1,2,3)); // anulado, por enviar, enviado y nota de pedido numero_serie
		$this->db->where('ve.idmovimiento',$idventa);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_venta_por_id($idmovimiento)
	{
		$this->db->select("CONCAT(COALESCE(col_asig.nombres,''), ' ', COALESCE(col_asig.apellidos,'')) AS colaborador_asig",FALSE);
		$this->db->select("CONCAT(COALESCE(ct.nombres,''), ' ', COALESCE(ct.apellidos,'')) AS contacto",FALSE); 
		$this->db->select("TRIM(CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))) AS cliente_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.email,''), ' ', COALESCE(ct.email,''))) AS email_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.idtipodocumentocliente,''), ' ', COALESCE(tdc_cp.idtipodocumentocliente,''))) AS idtipodocumentocliente",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.abreviatura_tdc,''), ' ', COALESCE(tdc_cp.abreviatura_tdc,''))) AS tipo_documento_abv",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(ce.ruc,''), ' ', COALESCE(cp.num_documento,''))) AS num_documento_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('ve.idmovimiento,ve.num_nota_pedido,ve.fecha_registro,ve.fecha_emision,ve.tipo_cliente,ve.plazo_entrega,ve.incluye_traslado_prov, 
			ve.incluye_entrega_domicilio,ve.validez_oferta, ve.tiene_detraccion, 
			ve.moneda,ve.modo_igv,ve.subtotal,ve.igv,ve.total,ve.estado_movimiento, ve.numero_serie, ve.numero_correlativo, ve.numero_orden_compra, 
			us.idusuario,	us.username,ea.idempresaadmin,(ea.razon_social) AS razon_social_ea,(ea.nombre_comercial) AS nombre_comercial_ea,(ea.ruc) AS ruc_ea,	ea.nombre_logo,ea.direccion_legal, 
			ea.pagina_web,(ea.telefono) AS telefono_ea,ce.idclienteempresa,(ce.razon_social) AS razon_social_ce,(ce.nombre_comercial) AS nombre_comercial_ce,(ce.ruc) AS ruc_ce,(ce.telefono) AS telefono_ce,	ce.direccion_guia,(ce.direccion_legal) AS direccion_legal_ce,ce.nombre_corto,ce.representante_legal,ce.dni_representante_legal,cp.idclientepersona,(cp.num_documento) AS num_documento_cp,se.idsede,se.descripcion_se,se.abreviatura_se,fp.idformapago,fp.descripcion_fp,fp.modo_fp,ct.idcontacto,ct.anexo,ct.telefono_fijo,
				tdm.idtipodocumentomov,tdm.descripcion_tdm, s.idserie', FALSE); 
		$this->db->from('movimiento ve'); 
		$this->db->join('tipo_documento_mov tdm','ve.idtipodocumentomov = tdm.idtipodocumentomov'); 
		$this->db->join('usuario us','ve.idusuarioventa = us.idusuario'); 
		$this->db->join('colaborador col_asig','ve.idcolaboradorv = col_asig.idcolaborador','left'); 
		$this->db->join('empresa_admin ea','ve.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","ve.idcliente = ce.idclienteempresa AND ve.tipo_cliente = 'E'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_ce","ce.idtipodocumentocliente = tdc_ce.idtipodocumentocliente",'left'); 
		$this->db->join("cliente_persona cp","ve.idcliente = cp.idclientepersona AND ve.tipo_cliente = 'P'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_cp","cp.idtipodocumentocliente = tdc_cp.idtipodocumentocliente",'left'); 
		$this->db->join('sede se','ve.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','ve.idformapago = fp.idformapago'); 
		$this->db->join('contacto ct','ve.idcontacto = ct.idcontacto','left'); 

		$this->db->join('serie s','s.idempresaadmin = ea.idempresaadmin','left'); 
		$this->db->where_in( 've.idmovimiento', array($idmovimiento) ); 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_detalle_venta_por_id($idmovimiento=NULL,$iddetalleventa=NULL)
	{ 
		$this->db->select('dve.iddetallemovimiento,ve.idmovimiento,ve.fecha_registro,ve.subtotal,ve.igv,ve.total,ve.idempresaadmin,dve.cantidad,dve.precio_unitario,dve.importe_con_igv,dve.importe_sin_igv,dve.excluye_igv,dve.igv_detalle,dve.agrupador_totalizado,um.idunidadmedida,um.descripcion_um,um.abreviatura_um,ele.idelemento,ele.descripcion_ele,ele.tipo_elemento,c.idcaracteristica,c.orden_car,c.descripcion_car,dc.iddetallecaracteristica,dc.valor', FALSE); 		
		$this->db->from('movimiento ve'); 
		$this->db->join('detalle_movimiento dve','ve.idmovimiento = dve.idmovimiento'); 
		$this->db->join('elemento ele','dve.idelemento = ele.idelemento'); 
		//$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida','left'); 
		$this->db->join('unidad_medida um','dve.idunidadmedida = um.idunidadmedida','left'); 
		$this->db->join("detalle_caracteristica dc","dc.iddetalle = dve.iddetallemovimiento AND dc.tipo_detalle = 'C'",'left'); 
		$this->db->join('caracteristica c','dc.idcaracteristica = c.idcaracteristica','left'); 
		if( $idmovimiento ){ 
			$this->db->where('ve.idmovimiento',$idmovimiento); 
		}
		// para cotizacion múltiple en N.P.
		if( $iddetalleventa ){
			$this->db->where('dve.iddetallemovimiento',$iddetalleventa); 
		}
		$this->db->where_in('dve.estado_dmov', array(1)); // habilitado 
		$this->db->order_by('dve.iddetallemovimiento','ASC');
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
			'idcolaboradorv' => $datos['colaborador']['id'],
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
			'validez_oferta' => $datos['validez_oferta'], 
			'tiene_detraccion'=> empty($datos['tiene_detraccion']) ? 2 : $datos['tiene_detraccion'] // 2: NO TIENE DETRACCIÓN 
		); 
		return $this->db->insert('movimiento', $data); 
	}
	public function m_registrar_detalle_venta($datos)
	{	
		// var_dump($datos);exit();
		$data = array(
			'idmovimiento' => $datos['idmovimiento'],	
			'idelemento' => $datos['id'],
			'idunidadmedida' => is_array($datos['unidad_medida']) ? $datos['unidad_medida']['id'] : $datos['unidad_medida'], 
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
		// var_dump($datos);exit();
		$data = array(
			'tipo_detalle' => 'VE', // VENTA  
			'iddetalle' => $datos['iddetalleventa'],
			'idcaracteristica' => $datos['idcaracteristica'],
			// 'idcaracteristica' => $datos['id'],
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
	public function m_editar_venta($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'fecha_emision'=> darFormatoYMD($datos['fecha_emision']),
			'idtipodocumentomov' => $datos['tipo_documento_mov']['id'], 
			'numero_orden_compra' => $datos['orden_compra'],
			// 'estado_cot' => $datos['estado_cotizacion']['id'], 
			'idsede'=> $datos['sede']['id'],
			'plazo_entrega'=> $datos['plazo_entrega'],
			'validez_oferta'=> $datos['validez_oferta'],
			'incluye_traslado_prov'=> $datos['incluye_tras_prov'],
			'incluye_entrega_domicilio'=> $datos['incluye_entr_dom'],
			'idformapago' => $datos['forma_pago']['id'],
			'moneda' => $datos['moneda']['str_moneda'],
			'modo_igv' => $datos['modo_igv'],
			'subtotal' => $datos['subtotal'],
			'igv' => $datos['igv'],
			'total' => $datos['total'],
		);
		$this->db->where('idmovimiento',$datos['idmovimiento']); 
		return $this->db->update('movimiento', $data); 
	}
	public function m_editar_venta_detalle($datos) 
	{
		// var_dump($datos);exit();
		$data = array( 
			'cantidad' => $datos['cantidad'],
			'idunidadmedida' => is_array($datos['unidad_medida']) ? $datos['unidad_medida']['id'] : $datos['unidad_medida'], 
			'precio_unitario' => $datos['precio_unitario'],
			'importe_con_igv' => $datos['importe_con_igv'],
			'importe_sin_igv' => $datos['importe_sin_igv'],
			'importe_sin_igv' => $datos['importe_sin_igv'],
			'excluye_igv' => $datos['excluye_igv'],
			'igv_detalle' => $datos['igv'],
			'agrupador_totalizado' => $datos['agrupacion']
		);
		$this->db->where('iddetallemovimiento',$datos['iddetallemovimiento']); 
		return $this->db->update('detalle_movimiento', $data); 
	}
	public function m_editar_detalle_caracteristica_venta($datos)
	{
		$data = array( 
			'valor' => strtoupper($datos['valor']) 
		);
		$this->db->where('iddetallecaracteristica',$datos['iddetallecaracteristica']); 
		return $this->db->update('detalle_caracteristica', $data); 
	}
	public function m_anular_venta_detalle($datos)
	{
		$data = array(
			'estado_dmov' => 0 // anulado 
		);
		$this->db->where('iddetallemovimiento',$datos['iddetallemovimiento']); 
		return $this->db->update('detalle_movimiento', $data); 
	}	
	public function m_anular($datos)
	{
		$data = array(
			'estado_movimiento' => 0, // anulado 
			'fecha_anulacion' => date('Y-m-d H:i:s')
		);
		$this->db->where('idmovimiento',$datos['idventa']); 
		return $this->db->update('movimiento', $data); 
	}


} 
?>