<?php
class Model_cotizacion extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cotizaciones($paramPaginate,$paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE);
		$this->db->select("CONCAT(COALESCE(col_reg.nombres,''), ' ', COALESCE(col_reg.apellidos,'')) As colaborador_reg",FALSE);
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
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); // al que se le asigna la cotizacion
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); // registra cotizacion 
		$this->db->join('colaborador col_reg','us.idusuario = col_reg.idusuario'); // registra cotizacion
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'P'",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_cotizacion']) && $paramDatos['estado_cotizacion']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cotizacion']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where_in('cot.estado_cot', array(0,1,2,3)); // anulado, por enviar, enviado y nota de pedido 
		if( $this->sessionFactur['key_tu'] == 'key_vendedor' ){
			$this->db->where('col.idcolaborador', $this->sessionFactur['idcolaborador']);
		}
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		
		if( $paramPaginate['sortName'] ){ 
			$orderFlag = NULL;
			if( $paramPaginate['sortName'] == 'cot.num_cotizacion' ){
				$paramPaginate['sortName'] = 'RIGHT(cot.num_cotizacion,5)'; // 5 NUM CARACTERES
				$orderFlag = FALSE; 
			}
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort'],$orderFlag);
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
		if( !empty($paramDatos['cliente']) ){
			if( $paramDatos['cliente']['tipo_cliente'] == 'ce' ){
				$this->db->where('ce.idclienteempresa',$paramDatos['cliente']['id']);
			}
			if( $paramDatos['cliente']['tipo_cliente'] == 'cp' ){
				$this->db->where('cp.idclientepersona',$paramDatos['cliente']['id']);
			}
		}
		$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		if(!empty($paramDatos['estado_cotizacion']) && $paramDatos['estado_cotizacion']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cotizacion']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		$this->db->where('ea.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->where_in('cot.estado_cot', array(0,1,2,3)); // anulado, por enviar, enviado y nota de pedido 
		if( $this->sessionFactur['key_tu'] == 'key_vendedor' ){
			$this->db->where('col.idcolaborador', $this->sessionFactur['idcolaborador']);
		}
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
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) As colaborador",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,'')) As cliente_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) As cliente_persona",FALSE);
		$this->db->select('cot.idcotizacion, cot.fecha_registro, cot.fecha_emision, cot.tipo_cliente, cot.plazo_entrega, cot.validez_oferta, 
			cot.moneda, cot.modo_igv, cot.subtotal, cot.igv, cot.total, cot.estado_cot, cot.num_cotizacion, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, 
			dcot.iddetallecotizacion, dcot.cantidad, dcot.precio_unitario, dcot.importe_con_igv, dcot.importe_sin_igv, 
			dcot.excluye_igv, dcot.igv_detalle, dcot.agrupador_totalizado, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento,se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp,cael.idcategoriaelemento,cael.descripcion_cael', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'p'",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->join('detalle_cotizacion dcot','cot.idcotizacion = dcot.idcotizacion'); 
		$this->db->join('elemento ele','dcot.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','dcot.idunidadmedida = um.idunidadmedida','left'); 
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
			$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		}
		if(!empty($paramDatos['estado_cotizacion']) && $paramDatos['estado_cotizacion']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cotizacion']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		if(!empty($paramDatos['categoria_elemento']) && $paramDatos['categoria_elemento']['id'] !== 'ALL' ){ 
			$this->db->where('cael.idcategoriaelemento', $paramDatos['categoria_elemento']['id']);
		}
		$this->db->where_in('cot.estado_cot', array(0,1,2,3)); // anulado, por enviar, enviado y nota de pedido 
		$this->db->where_in('dcot.estado_dcot', array(1)); // habilitado 
		$this->db->where_in('ele.estado_ele', array(1)); // habilitado 
		if( $this->sessionFactur['key_tu'] == 'key_vendedor' ){
			$this->db->where('col.idcolaborador', $this->sessionFactur['idcolaborador']);
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
	public function m_count_cotizaciones_detalle($paramPaginate,$paramDatos)
	{ 
		$this->db->select('COUNT(*) AS contador', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'p'",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->join('detalle_cotizacion dcot','cot.idcotizacion = dcot.idcotizacion'); 
		$this->db->join('elemento ele','dcot.idelemento = ele.idelemento'); 
		$this->db->join('unidad_medida um','dcot.idunidadmedida = um.idunidadmedida','left'); 
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
			$this->db->where('cot.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));
		}
		if(!empty($paramDatos['estado_cotizacion']) && $paramDatos['estado_cotizacion']['id'] !== 'ALL' ){ 
			$this->db->where('cot.estado_cot', $paramDatos['estado_cotizacion']['id']);
		} 
		if(!empty($paramDatos['sede']) && $paramDatos['sede']['id'] !== 'ALL' ){ 
			$this->db->where('se.idsede', $paramDatos['sede']['id']);
		}
		if(!empty($paramDatos['categoria_elemento']) && $paramDatos['categoria_elemento']['id'] !== 'ALL' ){ 
			$this->db->where('cael.idcategoriaelemento', $paramDatos['categoria_elemento']['id']);
		}
		$this->db->where_in('cot.estado_cot', array(0,1,2,3)); // anulado, por enviar, enviado y nota de pedido 
		$this->db->where_in('dcot.estado_dcot', array(1)); // habilitado 
		$this->db->where_in('ele.estado_ele', array(1)); // habilitado 
		if( $this->sessionFactur['key_tu'] == 'key_vendedor' ){
			$this->db->where('col.idcolaborador', $this->sessionFactur['idcolaborador']);
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
	public function m_cargar_ultima_cotizacion_segun_config($datos)
	{
		$this->db->select('cot.idcotizacion, cot.num_cotizacion');
		$this->db->from('cotizacion cot');
		$this->db->join('sede se', 'cot.idsede = se.idsede');
		$this->db->where_in('cot.estado_cot', array(0,1,2,3)); // por enviar, enviado y nota de pedido 
		//$this->db->where('se.idsede',$datos['sede']['id']);
		if($datos['config']['incluye_mes_en_codigo_cot'] == 'no' && $datos['config']['incluye_dia_en_codigo_cot'] == 'no'){
			$this->db->where('YEAR(DATE(cot.fecha_registro))', (int)date('Y')); // año 
		}
		if($datos['config']['incluye_mes_en_codigo_cot'] == 'si' && $datos['config']['incluye_dia_en_codigo_cot'] == 'no'){
			$this->db->where('YEAR(DATE(cot.fecha_registro))', (int)date('Y')); // año 
			$this->db->where("DATE_FORMAT(DATE(cot.fecha_registro),'%m')",date('m')); // mes 
		}
		if($datos['config']['incluye_mes_en_codigo_cot'] == 'si' && $datos['config']['incluye_dia_en_codigo_cot'] == 'si'){
			$this->db->where('DATE(cot.fecha_registro)',date('Y-m-d')); // año, mes y dia
		}
		$this->db->where('cot.idempresaadmin', $this->sessionFactur['idempresaadmin']); // empresa session 
		$this->db->order_by('cot.fecha_registro','DESC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_numero_cotizacion_autocomplete($filtro,$datos) // telefono_fijo representante_legal sexo
	{ 
		$this->db->select("TRIM(CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))) AS cliente_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.email,''), ' ', COALESCE(ct.email,''))) AS email_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.abreviatura_tdc,''), ' ', COALESCE(tdc_cp.abreviatura_tdc,''))) AS tipo_documento_abv",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(ce.ruc,''), ' ', COALESCE(cp.num_documento,''))) AS num_documento_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('cot.idcotizacion, cot.num_cotizacion, cot.fecha_registro, cot.fecha_emision, cot.tipo_cliente, cot.plazo_entrega, cot.incluye_entrega_domicilio,
			cot.incluye_traslado_prov, cot.validez_oferta, cot.moneda, cot.modo_igv, cot.subtotal, cot.igv, cot.total, cot.estado_cot, 
			ea.idempresaadmin, ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, 
			(ce.representante_legal) AS representante_legal_ce, ce.dni_representante_legal, (ce.nombre_comercial) AS nombre_comercial_ce, 
			(ce.ruc) AS ruc_ce, (ce.telefono) AS telefono_ce, ce.idtipodocumentocliente AS ce_idtipodocumentocliente, 
			ce.direccion_guia, (ce.direccion_legal) AS direccion_legal_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, (cp.telefono_movil) AS telefono_movil_cp, (cp.telefono_fijo) AS telefono_fijo_cp, 
			cp.idtipodocumentocliente AS cp_idtipodocumentocliente, 
			se.idsede, se.descripcion_se, se.abreviatura_se, col.idcolaborador, col.nombres, col.apellidos, 
			fp.idformapago, fp.descripcion_fp, fp.modo_fp, ct.idcontacto, ct.telefono_fijo, ct.anexo, ct.area_encargada', FALSE); 
		$this->db->select("CONCAT(COALESCE(ct.nombres,''), ' ', COALESCE(ct.apellidos,'')) AS contacto",FALSE);
		$this->db->from('cotizacion cot');  
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_ce","ce.idtipodocumentocliente = tdc_ce.idtipodocumentocliente",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'P'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_cp","cp.idtipodocumentocliente = tdc_cp.idtipodocumentocliente",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->join('contacto ct','cot.idcontacto = ct.idcontacto','left'); 
		if( !empty($filtro['searchText']) ){
			$this->db->like('cot.num_cotizacion', $filtro['searchText']); 
		}
		
		if( !empty($filtro['idcotizacion']) ){
			$this->db->like('cot.idcotizacion', $filtro['idcotizacion']); 
		}
		if( isset($filtro['estado'])){
			$this->db->where_in('cot.estado_cot',array(0,1,2));// anulado, por enviar y enviado 
		}else{
			$this->db->where_in('cot.estado_cot',array(1,2)); // por enviar y enviado 
		}		
		if( !empty($datos['cliente']) ){ 
			if( !empty($datos['num_documento']) && !empty($datos['cliente']['idclienteempresa']) ){ 
				$this->db->where('ce.idclienteempresa',$datos['cliente']['idclienteempresa']);
			}
			if( !empty($datos['num_documento']) && ( !empty($datos['cliente']['idclientepersona']) ) ){ // limite 
				$this->db->where('cp.idclientepersona',$datos['cliente']['idclientepersona']);
			} 
		} 
		if( !empty($filtro['limit']) ){
			$this->db->limit($filtro['limit']);
		}
		return $this->db->get()->result_array();  
	}
	public function m_validar_caracteristicas_repetidas($idcaracteristica,$iddetalle)
	{
		$this->db->select('dc.iddetallecaracteristica');
		$this->db->from('detalle_caracteristica dc');
		$this->db->where('dc.idcaracteristica',$idcaracteristica);
		$this->db->where('dc.iddetalle',$iddetalle);
		$this->db->where('dc.tipo_detalle','C');
		$this->db->where('dc.estado_dcar',1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_esta_cotizacion_por_codigo($numCoti,$validate=FALSE,$numCaracteres=NULL)
	{
		$this->db->select('co.idcotizacion, co.num_cotizacion');
		$this->db->from('cotizacion co');
		$this->db->join('sede se', 'co.idsede = se.idsede');
		$this->db->where_in('co.estado_cot',array(0,1,2,3)); // todos 
		//var_dump($validate); exit();
		if($validate){
			$this->db->where('RIGHT(co.num_cotizacion,'.$numCaracteres.')',$numCoti); 
		}else{
			$this->db->where('co.num_cotizacion',$numCoti); 
		}
		$this->db->where('co.idempresaadmin',$this->sessionFactur['idempresaadmin']);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_esta_cotizacion_por_id_simple($idcotizacion)
	{
		$this->db->select('cot.idcotizacion, cot.num_cotizacion, cot.estado_cot'); 
		$this->db->from('cotizacion cot');
		$this->db->join('sede se', 'cot.idsede = se.idsede');
		$this->db->where_in('cot.estado_cot', array(0,1,2,3)); // anulado, por enviar, enviado y nota de pedido 
		$this->db->where('cot.idcotizacion',$idcotizacion);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_cotizacion_por_id($idcotizacion) // modo_fp nombre_corto
	{
		$this->db->select("CONCAT(COALESCE(ct.nombres,''), ' ', COALESCE(ct.apellidos,'')) AS contacto",FALSE);
		$this->db->select("CONCAT(COALESCE(col.nombres,''), ' ', COALESCE(col.apellidos,'')) AS colaborador",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.nombres,''), ' ', COALESCE(cp.apellidos,''), ' ', COALESCE(ce.razon_social,''))) AS cliente_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(cp.email,''), ' ', COALESCE(ct.email,''))) AS email_persona_empresa",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.idtipodocumentocliente,''), ' ', COALESCE(tdc_cp.idtipodocumentocliente,''))) AS idtipodocumentocliente",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(tdc_ce.abreviatura_tdc,''), ' ', COALESCE(tdc_cp.abreviatura_tdc,''))) AS tipo_documento_abv",FALSE);
		$this->db->select("TRIM(CONCAT(COALESCE(ce.ruc,''), ' ', COALESCE(cp.num_documento,''))) AS num_documento_persona_empresa",FALSE);
		$this->db->select("CONCAT(cp.nombres, ' ', cp.apellidos) AS cliente_persona",FALSE);
		$this->db->select('cot.idcotizacion, cot.num_cotizacion, cot.fecha_registro, cot.fecha_emision, cot.tipo_cliente, cot.plazo_entrega, 
			cot.incluye_traslado_prov, cot.incluye_entrega_domicilio, 
			cot.validez_oferta, cot.moneda, cot.modo_igv, cot.subtotal, cot.igv, cot.total, cot.estado_cot, 
			col.idcolaborador, (col.num_documento) AS num_documento_col, col.email, col.cargo, col.telefono ,us.idusuario, us.username, 
			ea.idempresaadmin, (ea.razon_social) AS razon_social_ea, (ea.nombre_comercial) AS nombre_comercial_ea, (ea.ruc) AS ruc_ea, 
			ea.nombre_logo, ea.direccion_legal, ea.pagina_web, (ea.telefono) AS telefono_ea, 
			ce.idclienteempresa, (ce.razon_social) AS razon_social_ce, (ce.nombre_comercial) AS nombre_comercial_ce, (ce.ruc) AS ruc_ce, (ce.telefono) AS telefono_ce, 
			ce.direccion_guia, (ce.direccion_legal) AS direccion_legal_ce, ce.nombre_corto, ce.representante_legal, ce.dni_representante_legal, ce.direccion_legal AS direccion_legal_ce, 
			cp.idclientepersona, (cp.num_documento) AS num_documento_cp, cp.sexo, cp.nombres AS nombres_cp, cp.apellidos AS apellidos_cp, cp.fecha_nacimiento, 
			cp.telefono_fijo AS telefono_fijo_cp, cp.telefono_movil AS telefono_movil_cp, 
			se.idsede, se.descripcion_se, se.abreviatura_se, 
			fp.idformapago, fp.descripcion_fp, fp.modo_fp, ct.idcontacto, ct.anexo, ct.telefono_fijo', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('colaborador col','cot.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','cot.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','cot.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join("cliente_empresa ce","cot.idcliente = ce.idclienteempresa AND cot.tipo_cliente = 'E'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_ce","ce.idtipodocumentocliente = tdc_ce.idtipodocumentocliente",'left'); 
		$this->db->join("cliente_persona cp","cot.idcliente = cp.idclientepersona AND cot.tipo_cliente = 'P'",'left'); 
		$this->db->join("tipo_documento_cliente tdc_cp","cp.idtipodocumentocliente = tdc_cp.idtipodocumentocliente",'left'); 
		$this->db->join('sede se','cot.idsede = se.idsede'); 
		$this->db->join('forma_pago fp','cot.idformapago = fp.idformapago'); 
		$this->db->join('contacto ct','cot.idcontacto = ct.idcontacto','left'); 
		$this->db->where_in( 'cot.idcotizacion', array($idcotizacion) ); 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData; 
	}
	public function m_cargar_detalle_cotizacion_por_id($idcotizacion=NULL,$iddetallecotizacion=NULL)
	{ 
		$this->db->select('dcot.iddetallecotizacion, cot.idcotizacion, cot.num_cotizacion, cot.fecha_registro, cot.subtotal, cot.igv, cot.total, cot.estado_cot, cot.idempresaadmin, 
			dcot.cantidad, dcot.precio_unitario, dcot.importe_con_igv, dcot.importe_sin_igv, 
			dcot.excluye_igv, dcot.igv_detalle, dcot.agrupador_totalizado, um.idunidadmedida, um.descripcion_um, um.abreviatura_um, 
			ele.idelemento, ele.descripcion_ele, ele.tipo_elemento, c.idcaracteristica, c.orden_car, c.descripcion_car, dc.iddetallecaracteristica,dc.valor', FALSE); 
		$this->db->from('cotizacion cot'); 
		$this->db->join('detalle_cotizacion dcot','cot.idcotizacion = dcot.idcotizacion'); 
		$this->db->join('elemento ele','dcot.idelemento = ele.idelemento'); 
		//$this->db->join('unidad_medida um','ele.idunidadmedida = um.idunidadmedida','left'); 
		$this->db->join('unidad_medida um','dcot.idunidadmedida = um.idunidadmedida','left'); 
		$this->db->join("detalle_caracteristica dc","dc.iddetalle = dcot.iddetallecotizacion AND dc.tipo_detalle = 'C'",'left'); 
		$this->db->join('caracteristica c','dc.idcaracteristica = c.idcaracteristica','left'); 
		if( $idcotizacion ){ 
			$this->db->where('cot.idcotizacion',$idcotizacion); 
		}
		// para cotizacion múltiple en N.P.
		if( $iddetallecotizacion ){
			$this->db->where('dcot.iddetallecotizacion',$iddetallecotizacion); 
		}
		$this->db->where_in('dcot.estado_dcot', array(1)); // habilitado 
		$this->db->order_by('dcot.iddetallecotizacion','ASC');
		$this->db->order_by('c.orden_car','ASC');
		$this->db->order_by('c.descripcion_car','ASC');
		return $this->db->get()->result_array();
	}
	public function m_registrar_cotizacion($datos)
	{
		$data = array( 
			'num_cotizacion' => $datos['num_cotizacion'], 
			'idcolaborador' => $datos['colaborador']['id'], 
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
			'estado_cot' => $datos['estado_cotizacion']['id'],
			'idcontacto' => empty($datos['contacto']['id']) ? NULL : $datos['contacto']['id']
		); 
		return $this->db->insert('cotizacion', $data); 
	}
	public function m_registrar_detalle_cotizacion($datos)
	{
		$data = array(
			'idcotizacion' => $datos['idcotizacion'],
			'idelemento' => $datos['id'],
			//'idunidadmedida' => $datos['unidad_medida']['id'],
			'idunidadmedida' => is_array($datos['unidad_medida']) ? $datos['unidad_medida']['id'] : $datos['unidad_medida'], 
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
			'idcaracteristica' => $datos['idcaracteristica'],
			'valor' => strtoupper($datos['valor'])
		);
		return $this->db->insert('detalle_caracteristica', $data); 
	} 
	// public function m_actualizar_estado_cotizaciones($arrCotizacion, $boolEstado)
	// {
	// 	$data = array(
	// 		'estado_cot' => $boolEstado,
	// 		'fecha_envio' => date('Y-m-d H:i:s')
	// 	);
	// 	$this->db->where_in('idcotizacion',$arrCotizacion); 
	// 	return $this->db->update('cotizacion', $data); 
	// }
	public function m_editar_cotizacion($datos)
	{
		$data = array(
			'fecha_emision'=> darFormatoYMD($datos['fecha_emision']),
			'idcolaborador' => $datos['colaborador']['id'], 
			'estado_cot' => $datos['estado_cotizacion']['id'], 
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
		$this->db->where('idcotizacion',$datos['idcotizacion']); 
		return $this->db->update('cotizacion', $data); 
	}
	public function m_editar_cotizacion_detalle($datos) 
	{
		$data = array( 
			'cantidad' => $datos['cantidad'],
			'precio_unitario' => $datos['precio_unitario'],
			'idunidadmedida' => is_array($datos['unidad_medida']) ? $datos['unidad_medida']['id'] : $datos['unidad_medida'], 
			'importe_con_igv' => $datos['importe_con_igv'],
			'importe_sin_igv' => $datos['importe_sin_igv'],
			'excluye_igv' => $datos['excluye_igv'],
			'igv_detalle' => $datos['igv'],
			'agrupador_totalizado' => $datos['agrupacion']
		);
		$this->db->where('iddetallecotizacion',$datos['iddetallecotizacion']); 
		return $this->db->update('detalle_cotizacion', $data); 
	}
	public function m_editar_detalle_caracteristica_cotizacion($datos)
	{
		$data = array( 
			'valor' => strtoupper($datos['valor']) 
		);
		$this->db->where('iddetallecaracteristica',$datos['iddetallecaracteristica']); 
		return $this->db->update('detalle_caracteristica', $data); 
	}
	public function m_eliminar_detalle_caracteristica_cotizacion($datos)
	{
		$this->db->where('iddetallecaracteristica',$datos['iddetallecaracteristica']);
		return $this->db->delete('detalle_caracteristica');
	}
	public function m_anular($datos)
	{
		$data = array(
			'estado_cot' => 0 // anulado 
		);
		$this->db->where('idcotizacion',$datos['idcotizacion']); 
		return $this->db->update('cotizacion', $data); 
	}
	public function m_anular_cotizacion_detalle($datos)
	{
		$data = array(
			'estado_dcot' => 0 // anulado 
		);
		$this->db->where('iddetallecotizacion',$datos['iddetallecotizacion']); 
		return $this->db->update('detalle_cotizacion', $data); 
	}
	public function m_cambiar_estado_enviado($datos)
	{
		$data = array(
			'estado_cot' => 2, // enviado 
			'fecha_envio' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idcotizacion',$datos['idcotizacion']); 
		return $this->db->update('cotizacion', $data); 
	}
	public function m_cambiar_fecha_enviado($datos)
	{
		$data = array( 
			'fecha_envio' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcotizacion',$datos['idcotizacion']); 
		return $this->db->update('cotizacion', $data); 
	}
	public function m_actualizar_estado_cotizaciones_a_pedido($arrCotizacion)
	{
		$data = array(
			'estado_cot' => 3, // PEDIDO  
			'fecha_pedido' => date('Y-m-d H:i:s')
		);
		$this->db->where_in('idcotizacion',$arrCotizacion); 
		return $this->db->update('cotizacion', $data); 
	}
} 
?>