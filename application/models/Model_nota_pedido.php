<?php
class Model_nota_pedido extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_nota_pedido($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE); 
		$this->db->select("CONCAT(COALESCE(col_cot.nombres,''), ' ', COALESCE(col_cot.apellidos,'')) As colaborador_cot",FALSE); 
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('np.idmovimiento, np.num_nota_pedido, np.fecha_registro, np.dir_movimiento, np.tipo_movimiento, np.fecha_emision, np.tipo_cliente, incluye_traslado_prov, incluye_entrega_domicilio, np.moneda, np.modo_igv, np.subtotal, np.igv, np.total, np.estado_movimiento, np.plazo_entrega, np.validez_oferta, np.observaciones_np, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, se.idsede, se.descripcion_se, se.abreviatura_se, fp.idformapago, fp.descripcion_fp', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('colaborador col_cot','np.idcolaboradorcotnp = col_cot.idcolaborador','left'); 
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
		$this->db->where_in('np.estado_movimiento', array(1,2,0)); // 1: registrado 2:facturado 0:anulado 
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		if( $paramPaginate['sortName'] ){ 
			$orderFlag = NULL;
			if( $paramPaginate['sortName'] == 'np.num_nota_pedido' ){
				$paramPaginate['sortName'] = 'RIGHT(np.num_nota_pedido,5)'; // 5 NUM CARACTERES
				$orderFlag = FALSE; 
			}
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort'],$orderFlag);
		}
		// if( $paramPaginate['sortName'] ){
		// 	$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		// }
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
		$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:facturado  
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
		$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:facturado  
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
		$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:facturado  
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
	public function m_cargar_detalle_nota_pedido_por_id($idnotapedido) 
	{
		$this->db->select('dm.iddetallemovimiento, np.idmovimiento, np.num_nota_pedido, np.fecha_registro, np.subtotal, np.igv, np.total, np.estado_movimiento, np.idempresaadmin, 
			dm.cantidad, dm.precio_unitario, dm.importe_con_igv, dm.importe_sin_igv, 
			dm.excluye_igv, dm.igv_detalle, dm.agrupador_totalizado, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento, c.idcaracteristica, c.orden_car, c.descripcion_car, dc.iddetallecaracteristica,dc.valor', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('detalle_movimiento dm','np.idmovimiento = dm.idmovimiento');
		$this->db->join('elemento ele','dm.idelemento = ele.idelemento');
		$this->db->join('unidad_medida um','dm.idunidadmedida = um.idunidadmedida','left'); 
		$this->db->join("detalle_caracteristica dc","dc.iddetalle = dm.iddetallemovimiento AND dc.tipo_detalle = 'NP'",'left'); 
		$this->db->join('caracteristica c','dc.idcaracteristica = c.idcaracteristica','left'); 
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col','us.idusuario = col.idusuario'); 
		$this->db->join('sede se','np.idsede = se.idsede'); 
		$this->db->where('np.idmovimiento', $idnotapedido); 
		// $this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session agrupador 
		$this->db->where('np.tipo_movimiento', 1); // nota de pedido 
		$this->db->where('dm.estado_dmov', 1); // nota de pedido 
		$this->db->order_by('dm.iddetallemovimiento','ASC');
		$this->db->order_by('c.orden_car','ASC');
		$this->db->order_by('c.descripcion_car','ASC');
		//$this->db->where_in('np.estado_movimiento', array(1,2)); // 1: registrado 2:facturado  
		return $this->db->get()->result_array();
	}
	public function m_cargar_numero_nota_pedido_autocomplete($filtro)
	{
		$this->db->select("TRIM(CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))) AS cliente_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.email,''), ' ', COALESCE(ct.email,''))) AS email_persona_empresa",FALSE);
		$this->db->select('np.idmovimiento, np.num_nota_pedido, np.fecha_registro, np.fecha_emision, np.moneda, np.modo_igv, np.subtotal, np.igv, np.total, np.estado_movimiento', FALSE); 
		$this->db->from('movimiento np');  
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('empresa_admin ea','np.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","np.idcliente = ce.idclienteempresa AND np.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","np.idcliente = cp.idclientepersona AND np.tipo_cliente = 'P'",'left'); 
		$this->db->join('contacto ct','np.idcontacto = ct.idcontacto','left'); 
		$this->db->where('ea.idempresaadmin',$this->sessionFactur['idempresaadmin']);
		$this->db->where('tipo_movimiento',1); // 1: nota de pedido 
		if( !empty($filtro['searchText']) ){
			$this->db->like('np.num_nota_pedido', $filtro['searchText']); 
		}
		$this->db->limit(20);
		return $this->db->get()->result_array(); 
	}

	public function m_verificar_existe_item_nota_pedido($iddetallemovimiento,$idnotapedido){
		$this->db->select('dm.iddetallemovimiento, np.idmovimiento', FALSE); 
		$this->db->from('movimiento np'); // nota de pedido 
		$this->db->join('detalle_movimiento dm','np.idmovimiento = dm.idmovimiento');
		$this->db->where('np.idmovimiento', $idnotapedido); 
		$this->db->where('dm.iddetallemovimiento', $iddetallemovimiento); 
		$this->db->where('np.tipo_movimiento', 1); // nota de pedido 
		$this->db->where('dm.estado_dmov', 1); // nota de pedido 
		return $this->db->get()->row_array();
	}

	public function m_cargar_nota_pedido_por_id($idnotapedido) // moneda idtipodocumentocliente ruc_ce idcolaboradorcotnp
	{
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select("CONCAT(COALESCE(ct.nombres,''), ' ', COALESCE(ct.apellidos,'')) AS contacto",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.idtipodocumentocliente,''), ' ', COALESCE(tdc_cp.idtipodocumentocliente,''))) AS idtipodocumentocliente",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.abreviatura_tdc,''), ' ', COALESCE(tdc_cp.abreviatura_tdc,''))) AS tipo_documento_abv",FALSE);

		$this->db->select("TRIM(CONCAT(COALESCE(ce.ruc,''), ' ', COALESCE(cp.num_documento,''))) AS num_documento_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.email,''), ' ', COALESCE(ct.email,''))) AS email_persona_empresa",FALSE);
		$this->db->select('np.idmovimiento, np.num_nota_pedido, np.fecha_registro, np.dir_movimiento, np.tipo_movimiento, np.fecha_emision, np.tipo_cliente, np.incluye_traslado_prov, np.incluye_entrega_domicilio, np.moneda, np.modo_igv, np.subtotal, np.igv, np.total, np.estado_movimiento, np.plazo_entrega, np.validez_oferta, np.idempresaadmin, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			ce.representante_legal AS representante_legal_ce, ce.dni_representante_legal AS dni_representante_legal_ce, ce.nombre_corto, 
			ce.idtipodocumentocliente AS ce_idtipodocumentocliente, ce.telefono AS telefono_ce,ce.direccion_guia, ce.direccion_legal AS direccion_legal_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, (cp.telefono_movil) AS telefono_movil_cp, (cp.telefono_fijo) AS telefono_fijo_cp, 
			cp.idtipodocumentocliente AS cp_idtipodocumentocliente, cp.sexo, cp.email, col.idcolaborador, col.nombres, col.apellidos, 
			(col_gen.nombres) AS nombres_gen, (col_gen.apellidos) AS apellidos_gen, 
			se.idsede, se.descripcion_se, se.abreviatura_se, fp.idformapago, fp.descripcion_fp, fp.modo_fp, 
			ct.idcontacto, ct.telefono_fijo, ct.anexo, ct.area_encargada,ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea,ea.nombre_logo, ea.direccion_legal, ea.pagina_web, (ea.telefono) AS telefono_ea, np.observaciones_np',FALSE); 
		$this->db->from('movimiento np');
		$this->db->join('empresa_admin ea','np.idempresaadmin = ea.idempresaadmin'); 		
		$this->db->join("cliente_empresa ce","np.idcliente = ce.idclienteempresa AND np.tipo_cliente = 'E'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_ce","ce.idtipodocumentocliente = tdc_ce.idtipodocumentocliente",'left'); 
		$this->db->join("cliente_persona cp","np.idcliente = cp.idclientepersona AND np.tipo_cliente = 'P'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_cp","cp.idtipodocumentocliente = tdc_cp.idtipodocumentocliente",'left'); 
		$this->db->join('sede se', 'np.idsede = se.idsede');
		$this->db->join('forma_pago fp','np.idformapago = fp.idformapago'); 
		$this->db->join('contacto ct','np.idcontacto = ct.idcontacto','left'); 
		$this->db->join('colaborador col','np.idcolaboradorcotnp = col.idcolaborador','left'); 
		$this->db->join('usuario us','np.idusuarionp = us.idusuario'); 
		$this->db->join('colaborador col_gen','us.idusuario = col_gen.idusuario'); 
		$this->db->where_in('np.estado_movimiento',array(0,1,2)); // solo "anulado", "registrado" y "facturado" 
		$this->db->where('np.idmovimiento',$idnotapedido);
		$this->db->where('np.tipo_movimiento',1); // 1 : nota de pedido 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_validar_nota_pedido($idnotapedido)
	{
		$this->db->select('np.idmovimiento, np.num_nota_pedido, np.estado_movimiento'); 
		$this->db->from('movimiento np');
		$this->db->where_in('np.estado_movimiento',array(0,1,2)); // solo "anulado", "registrado" y "facturado" 
		$this->db->where('np.idmovimiento',$idnotapedido);
		$this->db->where('np.tipo_movimiento',1); // 1 : nota de pedido 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_validar_caracteristicas_repetidas($idcaracteristica,$iddetalle)
	{
		$this->db->select('dc.iddetallecaracteristica');
		$this->db->from('detalle_caracteristica dc');
		$this->db->where('dc.idcaracteristica',$idcaracteristica);
		$this->db->where('dc.iddetalle',$iddetalle);
		$this->db->where('dc.tipo_detalle','NP');
		$this->db->where('dc.estado_dcar',1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_esta_nota_pedido_por_codigo($numNP) 
	{
		$this->db->select('np.idmovimiento, np.num_nota_pedido');
		$this->db->from('movimiento np');
		$this->db->join('sede se', 'np.idsede = se.idsede');
		$this->db->where_in('np.estado_movimiento',array(0,1,2)); // solo "anulado", "registrado" y "facturado" 
		$this->db->where('np.num_nota_pedido',$numNP);
		$this->db->where('np.idempresaadmin',$this->sessionFactur['idempresaadmin']);
		$this->db->where('np.tipo_movimiento',1); // 1 : nota de pedido 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_ultima_nota_pedido_segun_config($datos)
	{
		$this->db->select('np.idmovimiento, np.num_nota_pedido');
		$this->db->from('movimiento np');
		$this->db->join('sede se', 'np.idsede = se.idsede');
		$this->db->where_in('np.estado_movimiento',array(0,1,2)); // solo anulado, "registrado" y "facturado" 
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
	public function m_registrar_nota_pedido($datos)
	{
		$data = array( 
			'num_nota_pedido' => $datos['num_nota_pedido'], 
			'tipo_cliente' => $datos['tipo_cliente'],
			'idcliente' => $datos['cliente']['id'],
			'idusuarionp' => $this->sessionFactur['idusuario'],
			'idcolaboradorcotnp' => $datos['vendedor']['idvendedor'], 
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
			'validez_oferta' => $datos['validez_oferta'],
			'observaciones_np' => empty($datos['observaciones']) ? NULL : $datos['observaciones']
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
	public function m_actualizar_nota_pedido_a_venta($datos)
	{
		$data = array(
			'estado_movimiento' => 2, // facturado 
			'fecha_facturacion' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idmovimiento',$datos['idnotapedido']); 
		return $this->db->update('movimiento', $data); 
	}
	public function m_anular($datos)
	{
		$data = array(
			'estado_movimiento' => 0, // anulado  
			'fecha_anulacion' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idmovimiento',$datos['idnotapedido']); 
		return $this->db->update('movimiento', $data); 
	}

	public function m_actualizar_nota_pedido($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'estado_dmov' => 2, 
		);
		$this->db->where('iddetallemovimiento ',$datos['iddetallenotapedido']); 
		return $this->db->update('detalle_movimiento', $data); 
	}
	public function m_editar($datos)
	{
		$data = array(
			'observaciones_np' => empty($datos['observaciones']) ? NULL : strtoupper($datos['observaciones'])
		);
		$this->db->where('idmovimiento',$datos['idmovimiento']);
		return $this->db->update('detalle_movimiento ', $data); 
	}
} 
?>