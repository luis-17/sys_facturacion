<?php
class Model_categoria_cliente extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_categoria_cliente_cbo($datos = FALSE){ 
		$this->db->select('cc.idcategoriacliente, cc.descripcion_cc, cc.estado_cc');
		$this->db->from('categoria_cliente cc');
		$this->db->where('cc.estado_cc', 1); // activo
		$this->db->order_by('cc.descripcion_cc','ASC');
		return $this->db->get()->result_array();
	}
}
?>