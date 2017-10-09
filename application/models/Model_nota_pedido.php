<?php
class Model_nota_pedido extends CI_Model {
	public function __construct()
	{
		parent::__construct();
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
	public function m_registrar_nota_pedido($datos)
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
			'estado_cot' => $datos['estado_cotizacion']['id'],
			'idcontacto' => empty($datos['contacto']['id']) ? NULL : $datos['contacto']['id']
		); 
		return $this->db->insert('movimiento', $data); 
	}
	public function m_registrar_detalle_nota_pedido($datos)
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
		return $this->db->insert('detalle_movimiento', $data); 
	}
} 
?>