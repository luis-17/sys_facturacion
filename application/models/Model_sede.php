<?php
class Model_sede extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_sede_cbo(){
		$this->db->select("se.idsede, se.descripcion_se, se.direccion_se, se.abreviatura_se");
		$this->db->from('sede se');
		$this->db->where('estado_se', 1);
		return $this->db->get()->result_array();
	}
}
?>