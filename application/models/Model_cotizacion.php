<?php
class Model_cotizacion extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cotizaciones($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('cot.idcotizacion, cot.num_cotizacion, cot.fecha_registro, cot.fecha_emision, cot.tipo_cliente, cot.plazo_entrega, 
			cot.validez_oferta, cot.moneda, cot.modo_igv, cot.subtotal, cot.igv, cot.total, cot.estado_cot, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_cotizacion']) && $paramDatos['estado_cotizacion']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cotizacion']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where_in('cot.estado_cot', array(1,2)); // por enviar y enviado 
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
	public function m_count_cotizaciones($paramPaginate,$paramDatos)
	{ 
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_cotizacion']) && $paramDatos['estado_cotizacion']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cotizacion']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where_in('cot.estado_cot', array(1,2)); // por enviar y enviado 
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
	public function m_cargar_cotizaciones_detalle($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(col.nombres, ' ', col.apellidos) As colaborador",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('cot.idcotizacion, cot.fecha_registro, cot.fecha_emision, cot.tipo_cliente, cot.plazo_entrega, cot.validez_oferta, 
			cot.moneda, cot.modo_igv, cot.subtotal, cot.igv, cot.total, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp, 
			dcot.iddetallecotizacion, dcot.cantidad, dcot.precio_unitario, dcot.importe_con_igv, dcot.importe_sin_igv, 
			dcot.excluye_igv, dcot.igv_detalle, dcot.agrupador_totalizado, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce','cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'"); 
		$this->db->join("cliente_persona cp','cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'p'"); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->join('detalle_cotizacion dcot','cot.idcotizacion = dcot.idcotizacion'); 
		$this->db->join('elemento ele','dcot.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida'); 
		$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape($paramDatos['desde'].' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape($paramDatos['hasta'].' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_cot']) && $paramDatos['estado_cot']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cot']['id']);
		} 
		$this->db->where_in('cot.estado_cot', array(1,2)); // por enviar y enviado 
		$this->db->where_in('dcot.estado_dcot', array(1)); // habilitado 
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
	public function m_count_cotizaciones_detalle($paramPaginate,$paramDatos)
	{ 
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce','cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'"); 
		$this->db->join("cliente_persona cp','cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'p'"); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->join('detalle_cotizacion dcot','cot.idcotizacion = dcot.idcotizacion'); 
		$this->db->join('elemento ele','dcot.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida'); 
		$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape($paramDatos['desde'].' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape($paramDatos['hasta'].' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_cot']) && $paramDatos['estado_cot']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cot']['id']);
		} 
		$this->db->where_in('cot.estado_cot', array(1,2)); // por enviar y enviado 
		$this->db->where_in('dcot.estado_dcot', array(1)); // habilitado 
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
	public function m_cargar_ultima_cotizacion_sede_dia($datos)
	{
		$this->db->select('co.idcotizacion, co.num_cotizacion');
		$this->db->from('cotizacion co');
		$this->db->join('sede se', 'co.idsede = se.idsede');
		$this->db->where_in('co.estado_cot',array(1,2)); // solo "por enviar" y "enviado" 
		$this->db->where('se.idsede',$datos['sede']['id']);
		$this->db->where('DATE(co.fecha_registro)',date('Y-m-d'));
		$this->db->where('co.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->order_by('co.fecha_registro','DESC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_esta_cotizacion_por_codigo($numCoti)
	{
		$this->db->select('co.idcotizacion, co.num_cotizacion');
		$this->db->from('cotizacion co');
		$this->db->join('sede se', 'co.idsede = se.idsede');
		$this->db->where_in('co.estado_cot',array(1,2)); // solo "por enviar" y "enviado" 
		$this->db->where('co.num_cotizacion',$numCoti);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_cotizacion_por_id($idcotizacion)
	{
		$this->db->select("CONCAT(COALESCE(ct.nombres,''), ' ', COALESCE(ct.apellidos,'')) As contacto",FALSE);
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))) AS cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('cot.idcotizacion, cot.num_cotizacion, cot.fecha_registro, cot.fecha_emision, cot.tipo_cliente, cot.plazo_entrega, 
			cot.validez_oferta, cot.moneda, cot.modo_igv, cot.subtotal, cot.igv, cot.total, cot.estado_cot, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, col.email, col.cargo, col.telefono ,us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ea.nombre_logo, ea.direccion_legal, ea.pagina_web, (ea.telefono) AS telefono_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, (ce.telefono) AS telefono_ce, 
			(ce.direccion_legal) AS direccion_legal_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->join('contacto ct','cot.idcontacto = ct.idcontacto','left'); 
		$this->db->where_in( 'cot.idcotizacion', array($idcotizacion) ); 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_detalle_cotizacion_por_id($idcotizacion)
	{ 
		$this->db->select('dcot.iddetallecotizacion, cot.idcotizacion, cot.num_cotizacion, cot.fecha_registro, cot.subtotal, cot.igv, cot.total, cot.estado_cot, 
			dcot.cantidad, dcot.precio_unitario, dcot.importe_con_igv, dcot.importe_sin_igv, 
			dcot.excluye_igv, dcot.igv_detalle, dcot.agrupador_totalizado, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('detalle_cotizacion dcot','cot.idcotizacion = dcot.idcotizacion'); 
		$this->db->join('elemento ele','dcot.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida'); 
		$this->db->where('cot.idcotizacion',$idcotizacion); 
		$this->db->where('estado_dcot',1); // detalle cot. habilitado 
		return $this->db->get()->result_array();
	}
	public function m_registrar_cotizacion($datos)
	{
		$data = array( 
			'num_cotizacion' => $datos['num_cotizacion'], 
			'idcolaborador' => $this->sessionFactur['idcolaborador'], 
			'fecha_registro' => date('Y-m-d H:i:s'),
			'fecha_emision' => darFormatoYMD($datos['fecha_emision']),
			'tipo_cliente' => $datos['tipo_cliente'],
			'idcliente' => $datos['cliente']['id'],
			'idusuarioregistro' => $this->sessionFactur['idusuario'],
			'idempresaadmin' => $this->sessionFactur['idempresaadmin'],
			'idsede' => $datos['sede']['id'],
			'plazo_entrega' => $datos['plazo_entrega'],
			'validez_oferta' => $datos['validez_oferta'],
			'incluye_traslado_prov' => $datos['incluye_tras_prov'],
			'incluye_entrega_domicilio' => $datos['incluye_entr_dom'],
			'idformapago' => $datos['forma_pago']['id'],
			'moneda' => $datos['moneda']['str_moneda'],
			'modo_igv' => $datos['modo_igv'],
			'subtotal' => $datos['subtotal'],
			'igv' => $datos['igv'],
			'total' => $datos['total'],
			'estado_cot' => $datos['estado_cotizacion']['id'] 

		); 
		return $this->db->insert('cotizacion', $data); 
	}
	public function m_registrar_detalle_cotizacion($datos)
	{
		$data = array(
			'idcotizacion' => $datos['idcotizacion'],	
			'idelemento' => $datos['id'],
			'idunidadmedida' => $datos['unidad_medida']['id'],
			'cantidad' => $datos['cantidad'],
			'precio_unitario' => $datos['precio_unitario'],
			'importe_con_igv' => $datos['importe_con_igv'],
			'importe_sin_igv' => $datos['importe_sin_igv'],
			'excluye_igv' => $datos['excluye_igv'],
			'igv_detalle' => $datos['igv'],
			'agrupador_totalizado' => $datos['agrupacion']
		);
		return $this->db->insert('detalle_cotizacion', $data); 
	}
	public function m_registrar_detalle_caracteristica_cotizacion($datos) 
	{ 
		$data = array(
			'tipo_detalle' => 'C', // COTIZACIÓN 
			'iddetalle' => $datos['iddetallecotizacion'],
			'idcaracteristica' => $datos['id'],
			'valor' => $datos['valor']
		);
		return $this->db->insert('detalle_caracteristica', $data); 
	} 
} 
?>