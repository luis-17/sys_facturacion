<?php
class Model_categoria_elemento extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_categoria_elemento_cbo(){ 
		$this->db->select('ce.idcategoriaelemento, ce.descripcion_cael, ce.estado_cael, ce.color_cael');
		$this->db->from('categoria_elemento ce');
		$this->db->where('ce.estado_cael', 1); // activo
		$this->db->order_by('ce.descripcion_cael','ASC');
		return $this->db->get()->result_array();
	}
}
?>