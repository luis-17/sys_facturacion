<?php
class Model_guia_remision extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_guias_remision($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) AS colaborador_gen",FALSE);
		$this->db->select("CONCAT(COALESCE(col_asig.nombres,''), ' ', COALESCE(col_asig.apellidos,'')) AS colaborador_asig",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) AS cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('gr.idguiaremision, gr.tipo_cliente, gr.numero_serie, gr.numero_correlativo, gr.marca_transporte, 
			gr.placa_transporte, gr.num_constancia_inscripcion, gr.num_licencia_conducir, gr.punto_partida, gr.punto_llegada, 
			gr.fecha_registro, gr.fecha_emision, gr.fecha_inicio_traslado, gr.costo_minimo, gr.motivo_otros, gr.numero_guia, 
			gr.peso_total, gr.numero_orden_compra, gr.nombres_razon_social_trans, gr.domicilio_trans, gr.ruc_trans, gr.estado_gr, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, mt.idmotivotraslado, mt.descripcion_mt', FALSE); 
		$this->db->from('guia_remision gr'); 
		$this->db->join('usuario us','gr.idusuarioregistro = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('colaborador col_asig','gr.idcolaborador = col_asig.idcolaborador','left'); 
		$this->db->join('empresa_admin ea','gr.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","gr.idcliente = ce.idclienteempresa AND gr.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","gr.idcliente = cp.idclientepersona AND gr.tipo_cliente = 'P'",'left'); 
		$this->db->join('motivo_traslado mt','gr.idmotivotraslado = mt.idmotivotraslado');
		$this->db->join('movimiento mov','gr.idmovimiento = mov.idmovimiento','left');
		$this->db->join('tipo_documento_mov tdmov','mov.idtipodocumentomov = tdmov.idtipodocumentomov','left');
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('gr.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_guia_remision']) && $paramDatos['estado_guia_remision']['id'] !== 'ALL' ){ 
			$this->db->where('gr.estado_gr', $paramDatos['estado_guia_remision']['id']);
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
	public function m_count_guias_remision($paramPaginate,$paramDatos)
	{ 
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('guia_remision gr'); 
		$this->db->join('usuario us','gr.idusuarioregistro = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('colaborador col_asig','gr.idcolaborador = col_asig.idcolaborador','left'); 
		$this->db->join('empresa_admin ea','gr.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","gr.idcliente = ce.idclienteempresa AND gr.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","gr.idcliente = cp.idclientepersona AND gr.tipo_cliente = 'P'",'left'); 
		$this->db->join('motivo_traslado mt','gr.idmotivotraslado = mt.idmotivotraslado');
		$this->db->join('movimiento mov','gr.idmovimiento = mov.idmovimiento','left');
		$this->db->join('tipo_documento_mov tdmov','mov.idtipodocumentomov = tdmov.idtipodocumentomov','left');
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('gr.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_guia_remision']) && $paramDatos['estado_guia_remision']['id'] !== 'ALL' ){ 
			$this->db->where('gr.estado_gr', $paramDatos['estado_guia_remision']['id']);
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
	public function m_cargar_guia_remision_por_id($idguiaremision) 
	{
		//$this->db->select("CONCAT(COALESCE(ct.nombres,''), ' ', COALESCE(ct.apellidos,'')) AS contacto",FALSE);
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) AS colaborador",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))) AS cliente_persona_empresa",FALSE);
		//$this->db->select("TRIM(CONCAT(COALESCE(cp.email,''), ' ', COALESCE(ct.email,''))) AS email_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.idtipodocumentocliente,''), ' ', COALESCE(tdc_cp.idtipodocumentocliente,''))) AS idtipodocumentocliente",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.abreviatura_tdc,''), ' ', COALESCE(tdc_cp.abreviatura_tdc,''))) AS tipo_documento_abv",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(ce.ruc,''), ' ', COALESCE(cp.num_documento,''))) AS num_documento_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('gr.idguiaremision, gr.tipo_cliente, gr.numero_serie, gr.numero_correlativo, gr.marca_transporte, 
			gr.placa_transporte, gr.num_constancia_inscripcion, gr.num_licencia_conducir, gr.punto_partida, gr.punto_llegada, 
			gr.fecha_registro, gr.fecha_emision, gr.fecha_inicio_traslado, gr.costo_minimo, gr.motivo_otros, gr.numero_guia, 
			gr.peso_total, gr.numero_orden_compra, gr.nombres_razon_social_trans, gr.domicilio_trans, gr.ruc_trans, gr.estado_gr, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, col.email, col.cargo, col.telefono, col.abreviatura_nombre, 
			us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ea.nombre_logo, ea.direccion_legal, ea.pagina_web, (ea.telefono) AS telefono_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, (ce.telefono) AS telefono_ce, 
			ce.direccion_guia, (ce.direccion_legal) AS direccion_legal_ce, ce.nombre_corto, ce.representante_legal, ce.dni_representante_legal, 
			ce.direccion_legal AS direccion_legal_ce, cp.idclientepersona, (cp.num_documento) AS num_documento_cp, cp.sexo, cp.nombres AS nombres_cp, 
			cp.apellidos AS apellidos_cp, cp.fecha_nacimiento, cp.telefono_fijo AS telefono_fijo_cp, cp.telefono_movil AS telefono_movil_cp, 
			mt.idmotivotraslado, mt.descripcion_mt, mov.idmovimiento, (mov.numero_serie) AS numero_serie_venta, 
			(mov.numero_correlativo) AS numero_correlativo_venta, (tdmov.idtipodocumentomov) AS idtipodocumentoventa, 
			(tdmov.descripcion_tdm) AS tipodocumentoventa', FALSE); 
		$this->db->from('guia_remision gr'); 
		$this->db->join('colaborador col','gr.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','gr.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','gr.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","gr.idcliente = ce.idclienteempresa AND gr.tipo_cliente = 'E'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_ce","ce.idtipodocumentocliente = tdc_ce.idtipodocumentocliente",'left'); 
		$this->db->join("cliente_persona cp","gr.idcliente = cp.idclientepersona AND gr.tipo_cliente = 'P'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_cp","cp.idtipodocumentocliente = tdc_cp.idtipodocumentocliente",'left'); 
		//$this->db->join('contacto ct','gr.idcontacto = ct.idcontacto','left'); 
		$this->db->join('motivo_traslado mt','gr.idmotivotraslado = mt.idmotivotraslado');
		$this->db->join('movimiento mov','gr.idmovimiento = mov.idmovimiento','left');
		$this->db->join('tipo_documento_mov tdmov','mov.idtipodocumentomov = tdmov.idtipodocumentomov','left');
		$this->db->where_in( 'gr.idguiaremision', array($idguiaremision) ); 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_esta_guia_remision_por_id_simple($idguiaremision)
	{ 
		$this->db->select('gr.idguiaremision, gr.fecha_emision, gr.estado_gr', FALSE); 
		$this->db->from('guia_remision gr'); 
		$this->db->join('colaborador col','gr.idcolaborador = col.idcolaborador'); 
		$this->db->where_in('gr.idguiaremision', array($idguiaremision)); 
		$this->db->limit(1); 
		$fData = $this->db->get()->row_array(); 
		return $fData; 
	}
	public function m_cargar_detalle_guia_remision_por_id($idguiaremision)
	{
		$this->db->select('dgr.idguiaremisiondetalle, gr.idguiaremision, gr.fecha_registro, gr.estado_gr, gr.idempresaadmin, 
			dgr.cantidad, dgr.num_paquetes, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento, c.idcaracteristica, c.orden_car, c.descripcion_car, dc.iddetallecaracteristica, dc.valor', FALSE); 
		$this->db->from('guia_remision gr'); 
		$this->db->join('guia_remision_detalle dgr','gr.idguiaremision = dgr.idguiaremision'); 
		$this->db->join('elemento ele','dgr.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','dgr.idunidadmedida = um.idunidadmedida','left'); 
		$this->db->join("detalle_caracteristica dc","dc.iddetalle = dgr.idguiaremisiondetalle AND dc.tipo_detalle = 'GR'",'left'); 
		$this->db->join('caracteristica c','dc.idcaracteristica = c.idcaracteristica','left'); 
		$this->db->where('gr.idguiaremision',$idguiaremision); 
		$this->db->where_in('dgr.estado_grd', array(1)); // habilitado 
		$this->db->order_by('dgr.idguiaremisiondetalle','ASC');
		$this->db->order_by('c.orden_car','ASC');
		$this->db->order_by('c.descripcion_car','ASC');
		return $this->db->get()->result_array();
	}
	public function m_validar_guia_por_correlativo($numSerie,$numCorrelativo)
	{
		$this->db->select('gr.idguiaremision, gr.numero_serie, gr.numero_correlativo', FALSE); 
		$this->db->from('guia_remision gr'); 
		$this->db->where('gr.numero_serie', $numSerie ); 
		$this->db->where('gr.numero_correlativo', $numCorrelativo ); 
		$this->db->where('gr.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_validar_caracteristicas_repetidas($idcaracteristica,$iddetalle)
	{
		$this->db->select('dc.iddetallecaracteristica');
		$this->db->from('detalle_caracteristica dc');
		$this->db->where('dc.idcaracteristica',$idcaracteristica);
		$this->db->where('dc.iddetalle',$iddetalle);
		$this->db->where('dc.tipo_detalle','GR');
		$this->db->where('dc.estado_dcar',1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_registrar($datos)
	{
		$data = array( 
			'tipo_cliente' => $datos['tipo_cliente'],
			'idcliente' => $datos['cliente']['id'],
			'idmovimiento' => empty($datos['idmovimiento']) ? NULL : $datos['idmovimiento'], 
			'numero_serie' => $datos['num_serie'],
			'numero_correlativo' => $datos['num_correlativo'],
			'tipo_guia'=> 1, // guia remitente 
			'idmotivotraslado'=> $datos['motivo_traslado']['id'],
			'marca_transporte'=> empty($datos['marca_unidad']) ? NULL : $datos['marca_unidad'],
			'placa_transporte'=> empty($datos['placa_unidad']) ? NULL : $datos['placa_unidad'],
			'num_constancia_inscripcion'=> empty($datos['cert_inscripcion']) ? NULL : $datos['cert_inscripcion'],
			'num_licencia_conducir'=> empty($datos['num_licencia_conducir']) ? NULL : $datos['num_licencia_conducir'],
			'punto_partida'=> empty($datos['punto_partida']) ? NULL : $datos['punto_partida'],
			'punto_llegada'=> empty($datos['punto_llegada']) ? NULL : $datos['punto_llegada'],
			'fecha_emision' => darFormatoYMD($datos['fecha_emision']),
			'fecha_inicio_traslado'=> empty($datos['fecha_inicio_traslado']) ? NULL : darFormatoYMD($datos['fecha_inicio_traslado']),
			'idusuarioregistro' => $this->sessionFactur['idusuario'],
			'costo_minimo'=> empty($datos['costo_minimo']) ? NULL : $datos['costo_minimo'], 
			'motivo_otros'=> empty($datos['motivo_otros']) ? NULL : $datos['motivo_otros'], // aún no se implementa, sera NULL por ahora 
			'numero_guia'=> empty($datos['numero_guia']) ? NULL : $datos['numero_guia'], 
			'peso_total'=> empty($datos['peso_total']) ? NULL : $datos['peso_total'], 
			'idcolaborador' => $datos['colaborador']['id'],
			'numero_orden_compra' => empty($datos['orden_compra']) ? NULL : $datos['orden_compra'],
			'idempresaadmin' => $this->sessionFactur['idempresaadmin'],
			'nombres_razon_social_trans'=> empty($datos['nombres_razon_social_trans']) ? NULL : $datos['nombres_razon_social_trans'],
			'domicilio_trans'=> empty($datos['domicilio_trans']) ? NULL : $datos['domicilio_trans'],
			'ruc_trans'=> empty($datos['ruc_dni_trans']) ? NULL : $datos['ruc_dni_trans']
		); 
		return $this->db->insert('guia_remision', $data); 
	}
	public function m_registrar_detalle($datos)
	{
		$data = array( 
			'idguiaremision' => $datos['idguiaremision'],
			'idelemento' => $datos['id'],
			'idunidadmedida' => is_array($datos['unidad_medida']) ? $datos['unidad_medida']['id'] : $datos['unidad_medida'], 
			'cantidad' => $datos['cantidad'],
			'num_paquetes'=> empty($datos['num_paquetes']) ? NULL : $datos['num_paquetes'] 
		); 
		return $this->db->insert('guia_remision_detalle', $data); 
	}
	public function m_registrar_detalle_caracteristica_gr($datos)
	{
		$data = array(
			'tipo_detalle' => 'GR', // GUIA REMISIÓN   
			'iddetalle' => $datos['idguiaremisiondetalle'],
			'idcaracteristica' => $datos['idcaracteristica'],
			'valor' => strtoupper($datos['valor'])
		);
		return $this->db->insert('detalle_caracteristica', $data); 
	}
	public function m_editar_detalle_caracteristica_gr($datos)
	{
		$data = array( 
			'valor' => strtoupper($datos['valor'])
		);
		$this->db->where('iddetallecaracteristica',$datos['iddetallecaracteristica']);
		return $this->db->update('detalle_caracteristica', $data); 
	}
	public function m_editar($datos)
	{
		$data = array( 
			'idmovimiento' => empty($datos['idmovimiento']) ? NULL : $datos['idmovimiento'], 
			'idmotivotraslado'=> $datos['motivo_traslado']['id'],
			'marca_transporte'=> empty($datos['marca_unidad']) ? NULL : $datos['marca_unidad'],
			'placa_transporte'=> empty($datos['placa_unidad']) ? NULL : $datos['placa_unidad'],
			'num_constancia_inscripcion'=> empty($datos['cert_inscripcion']) ? NULL : $datos['cert_inscripcion'],
			'num_licencia_conducir'=> empty($datos['num_licencia_conducir']) ? NULL : $datos['num_licencia_conducir'],
			'punto_partida'=> empty($datos['punto_partida']) ? NULL : $datos['punto_partida'],
			'punto_llegada'=> empty($datos['punto_llegada']) ? NULL : $datos['punto_llegada'],
			'fecha_emision' => darFormatoYMD($datos['fecha_emision']),
			'fecha_inicio_traslado'=> empty($datos['fecha_inicio_traslado']) ? NULL : darFormatoYMD($datos['fecha_inicio_traslado']),
			'costo_minimo'=> empty($datos['costo_minimo']) ? NULL : $datos['costo_minimo'], 
			'peso_total'=> empty($datos['peso_total']) ? NULL : $datos['peso_total'], 
			'idcolaborador' => $datos['colaborador']['id'],
			'numero_orden_compra' => empty($datos['orden_compra']) ? NULL : $datos['orden_compra'],
			'nombres_razon_social_trans'=> empty($datos['nombres_razon_social_trans']) ? NULL : $datos['nombres_razon_social_trans'],
			'domicilio_trans'=> empty($datos['domicilio_trans']) ? NULL : $datos['domicilio_trans'],
			'ruc_trans'=> empty($datos['ruc_dni_trans']) ? NULL : $datos['ruc_dni_trans']
		); 
		$this->db->where('idguiaremision',$datos['idguiaremision']);
		return $this->db->update('guia_remision', $data); 
	}
	public function m_editar_detalle($datos)
	{
		$data = array( 
			'idunidadmedida' => is_array($datos['unidad_medida']) ? $datos['unidad_medida']['id'] : $datos['unidad_medida'], 
			'cantidad' => $datos['cantidad'],
			'num_paquetes'=> empty($datos['num_paquetes']) ? NULL : $datos['num_paquetes'] 
		);
		$this->db->where('idguiaremisiondetalle',$datos['idguiaremisiondetalle']); 
		return $this->db->update('guia_remision_detalle', $data); 
	}
	public function m_anular($datos)
	{
		$data = array(
			'estado_gr' => 0 // anulado 
		);
		$this->db->where('idguiaremision',$datos['idguiaremision']); 
		return $this->db->update('guia_remision', $data); 
	}
	public function m_anular_guia_remision_detalle($datos)
	{
		$data = array(
			'estado_grd' => 0 // anulado 
		);
		$this->db->where('idguiaremisiondetalle',$datos['idguiaremisiondetalle']); 
		return $this->db->update('guia_remision_detalle', $data); 
	}
}
?>