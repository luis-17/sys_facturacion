<?php
class Model_nota_pedido extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_nota_pedido($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE); 
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('np.idmovimiento, np.num_nota_pedido, np.fecha_registro, np.dir_movimiento, np.tipo_movimiento, np.fecha_emision, np.tipo_cliente, , incluye_traslado_prov, incluye_entrega_domicilio, np.moneda, np.modo_igv, np.subtotal, np.igv, np.total, np.estado_movimiento, np.plazo_entrega, np.validez_oferta, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, fp.idformapago, fp.descripcion_fp', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','np.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","np.idcliente = ce.idclienteempresa AND np.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","np.idcliente = cp.idclientepersona AND np.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','np.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','np.idformapago = fp.idformapago'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('np.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_np']) && $paramDatos['estado_np']['id'] !== 'ALL' ){ 
			$this->db->where('np.estado_movimiento', $paramDatos['estado_np']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where('np.tipo_movimiento', 1); // nota de pedido 
		$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:enviado 
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
	public function m_count_nota_pedido($paramPaginate,$paramDatos)
	{
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','np.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","np.idcliente = ce.idclienteempresa AND np.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","np.idcliente = cp.idclientepersona AND np.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','np.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','np.idformapago = fp.idformapago'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('np.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_np']) && $paramDatos['estado_np']['id'] !== 'ALL' ){ 
			$this->db->where('np.estado_movimiento', $paramDatos['estado_np']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where('np.tipo_movimiento', 1); // nota de pedido 
		$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:enviado 
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
	public function m_cargar_nota_pedido_detalle($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('npd.iddetallemovimiento,np.idmovimiento, np.num_nota_pedido, np.fecha_registro, np.dir_movimiento, np.tipo_movimiento, np.fecha_emision, np.tipo_cliente, , incluye_traslado_prov, incluye_entrega_domicilio, np.moneda, np.modo_igv, np.subtotal, np.igv, np.total, np.estado_movimiento, np.plazo_entrega, np.validez_oferta, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, fp.idformapago, fp.descripcion_fp,ele.idelemento, ele.descripcion_ele, ele.tipo_elemento,cael.idcategoriaelemento,cael.descripcion_cael,npd.cantidad,npd.importe_con_igv,npd.precio_unitario', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','np.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","np.idcliente = ce.idclienteempresa AND np.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","np.idcliente = cp.idclientepersona AND np.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','np.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','np.idformapago = fp.idformapago'); 
		$this->db->join('detalle_movimiento npd','npd.idmovimiento = np.idmovimiento'); 
		$this->db->join('elemento ele','npd.idelemento = ele.idelemento'); 
		$this->db->join('categoria_elemento cael','ele.idcategoriaelemento = cael.idcategoriaelemento'); 

		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('np.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_np']) && $paramDatos['estado_np']['id'] !== 'ALL' ){ 
			$this->db->where('np.estado_movimiento', $paramDatos['estado_np']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		if(!empty($paramDatos['categoria_elemento']) && $paramDatos['categoria_elemento']['id'] !== 'ALL' ){ 
			$this->db->where('cael.idcategoriaelemento', $paramDatos['categoria_elemento']['id']);
		}

		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where('np.tipo_movimiento', 1); // nota de pedido 
		$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:enviado 
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
	public function m_count_nota_pedido_detalle($paramPaginate,$paramDatos)
	{
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('empresa_admin ea','np.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","np.idcliente = ce.idclienteempresa AND np.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","np.idcliente = cp.idclientepersona AND np.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','np.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','np.idformapago = fp.idformapago');
		$this->db->join('detalle_movimiento npd','npd.idmovimiento = np.idmovimiento'); 
		$this->db->join('elemento ele','npd.idelemento = ele.idelemento'); 
		$this->db->join('categoria_elemento cael','ele.idcategoriaelemento = cael.idcategoriaelemento'); 		 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('np.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_np']) && $paramDatos['estado_np']['id'] !== 'ALL' ){ 
			$this->db->where('np.estado_movimiento', $paramDatos['estado_np']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		if(!empty($paramDatos['categoria_elemento']) && $paramDatos['categoria_elemento']['id'] !== 'ALL' ){ 
			$this->db->where('cael.idcategoriaelemento', $paramDatos['categoria_elemento']['id']);
		}
		
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where('np.tipo_movimiento', 1); // nota de pedido 
		$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:enviado 
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
	public function m_cargar_detalle_esta_nota_pedido($datos)
	{
		$this->db->select('np.idmovimiento, np.num_nota_pedido, np.fecha_registro, dm.iddetallemovimiento, dm.cantidad, dm.precio_unitario, dm.importe_con_igv, 
			dm.importe_sin_igv, dm.excluye_igv, dm.igv_detalle, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento, 
			um.idunidadmedida, um.descripcion_um, um.abreviatura_um', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('detalle_movimiento dm','np.idmovimiento = dm.idmovimiento');
		$this->db->join('elemento ele','dm.idelemento = ele.idelemento');
		$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida'); 

		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('sede se','np.idsede = se.idsede'); 
		$this->db->where('np.idmovimiento', $datos['idmovimiento']); 
		// $this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where('np.tipo_movimiento', 1); // nota de pedido 
		$this->db->order_by('dm.iddetallemovimiento','ASC');
		//$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:enviado 
		return $this->db->get()->result_array();
	}
	public function m_cargar_ultima_nota_pedido_segun_config($datos)
	{
		$this->db->select('np.idmovimiento, np.num_nota_pedido');
		$this->db->from('movimiento np');
		$this->db->join('sede se', 'np.idsede = se.idsede');
		$this->db->where_in('np.estado_movimiento',array(1,2)); // solo "registrado" y "facturado" 
		$this->db->where('np.tipo_movimiento',1); // solo nota de pedido 
		//$this->db->where('se.idsede',$datos['sede']['id']);
		if($datos['config']['incluye_mes_en_codigo_np'] == 'no' && $datos['config']['incluye_dia_en_codigo_np'] == 'no'){
			$this->db->where('YEAR(DATE(np.fecha_registro))', (int)date('Y')); // año 
		}
		if($datos['config']['incluye_mes_en_codigo_np'] == 'si' && $datos['config']['incluye_dia_en_codigo_np'] == 'no'){
			$this->db->where('YEAR(DATE(np.fecha_registro))', (int)date('Y')); // año 
			$this->db->where("DATE_FORMAT(DATE(np.fecha_registro),'%m')",date('m')); // mes 
		}
		if($datos['config']['incluye_mes_en_codigo_np'] == 'si' && $datos['config']['incluye_dia_en_codigo_np'] == 'si'){
			$this->db->where('DATE(np.fecha_registro)',date('Y-m-d')); // año, mes y dia
		}
		$this->db->where('np.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->order_by('np.fecha_registro','DESC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_esta_nota_pedido_por_codigo($numNP) 
	{
		$this->db->select('np.idmovimiento, np.num_nota_pedido');
		$this->db->from('movimiento np');
		$this->db->join('sede se', 'np.idsede = se.idsede');
		$this->db->where_in('np.estado_movimiento',array(1,2)); // solo "registrado" y "facturado" 
		$this->db->where('np.num_nota_pedido',$numNP);
		$this->db->where('np.tipo_movimiento',1); // 1 : nota de pedido 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_registrar_nota_pedido($datos)
	{
		$data = array( 
			'num_nota_pedido' => $datos['num_nota_pedido'], 
			'tipo_cliente' => $datos['tipo_cliente'],
			'idcliente' => $datos['cliente']['id'],
			'idusuarionp' => $this->sessionFactur['idusuario'],
			'dir_movimiento' => 'E',
			'tipo_movimiento' => 1, // 1: nota de pedido 
			'idempresaadmin' => $this->sessionFactur['idempresaadmin'],
			'idsede' => $datos['sede']['id'],
			'idcontacto' => empty($datos['idcontacto']) ? NULL : $datos['idcontacto'], 
			'fecha_registro' => date('Y-m-d H:i:s'),
			'fecha_emision' => darFormatoYMD($datos['fecha_emision']),
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
	public function m_registrar_detalle_nota_pedido($datos)
	{
		$data = array(
			'idmovimiento' => $datos['idnotapedido'],	
			'idelemento' => $datos['idelemento'],
			'idunidadmedida' => $datos['unidad_medida']['id'],
			'cantidad' => $datos['cantidad'],
			'precio_unitario' => $datos['precio_unitario'],
			'importe_con_igv' => $datos['importe_con_igv'],
			'importe_sin_igv' => $datos['importe_sin_igv'],
			'excluye_igv' => $datos['excluye_igv'],
			'igv_detalle' => $datos['igv'],
			'idcotizacion' => $datos['idcotizacion'],
			'iddetallecotizacion' => $datos['iddetallecotizacion'] 
		);
		return $this->db->insert('detalle_movimiento', $data); 
	} 
	public function m_registrar_detalle_caracteristica_nota_pedido($datos)
	{
		$data = array(
			'tipo_detalle' => 'NP', // NOTA DE PEDIDO  
			'iddetalle' => $datos['iddetallenotapedido'],
			'idcaracteristica' => $datos['idcaracteristica'],
			'valor' => strtoupper($datos['valor'])
		);
		return $this->db->insert('detalle_caracteristica', $data); 
	}
} 
?>