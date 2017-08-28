<?php
class Model_tipo_documento_cliente extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_tipo_documento_cliente_cbo($datos = FALSE){ 
		$this->db->select('tdc.idtipodocumentocliente, tdc.descripcion_tdc, tdc.abreviatura_tdc, tdc.destino_tdc, tdc.estado_tdc');
		$this->db->from('tipo_documento_cliente tdc');
		$this->db->where('tdc.estado_tdc', 1); // activo
		$this->db->order_by('tdc.descripcion_tdc','DESC');
		return $this->db->get()->result_array();
	}
}
?>