<?php
class Model_cotizacion extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_ultima_cotizacion_sede_dia($datos)
	{
		$this->db->select('co.idcotizacion, co.num_cotizacion');
		$this->db->from('cotizacion co');
		$this->db->join('sede se', 'co.idsede = se.idsede');
		$this->db->where_in('co.estado_cot',array(1,2)); // solo "por enviar" y "enviado" 
		$this->db->where('se.idsede',$datos['sede']['id']);
		$this->db->where('DATE(co.fecha_registro)',date('Y-m-d'));
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
	public function m_registrar_cotizacion($datos)
	{
		$data = array( 
			'num_cotizacion' => $datos['num_cotizacion'], 
			'idcolaborador' => $this->sessionFactur['idcolaborador'], 
			'fecha_registro' => date('Y-m-d H:i:s'),
			'fecha_emision' => $datos['fecha_emision'],
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
			'moneda' => $datos['moneda']['id'],
			'modo_igv' => $datos['abreviatura_ba'],
			'subtotal' => $datos['subtotal'],
			'igv' => $datos['igv'],
			'total' => $datos['total']

		); 
		return $this->db->insert('cotizacion', $data); 
	}
	public function m_registrar_detalle_cotizacion($datos)
	{
		$data = array(
			'idcotizacion' => $datos['descripcion_ba'],	
			'idelemento' => $datos['abreviatura_ba'],
			'descripcion_elemento' => $datos['descripcion_elemento'],
			'idunidadmedida' => $datos['idunidadmedida'],
			'cantidad' => $datos['cantidad'],
			'precio_unitario' => $datos['precio_unitario'],
			'importe_con_igv' => $datos['importe_con_igv'],
			'importe_sin_igv' => $datos['importe_sin_igv'],
			'importe' => $datos['importe'],
			'si_inafecto' => $datos['importe'],
			'igv_detalle' => $datos['importe'],
			'agrupador_totalizado' => $datos['agrupador_totalizado']
		);
		return $this->db->insert('detalle_cotizacion', $data); 
	}
}
?>