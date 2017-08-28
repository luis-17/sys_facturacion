<?php
class Model_unidad_medida extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_unidad_medida_cbo(){
		$this->db->select("um.idunidadmedida, um.descripcion_um, um.abreviatura_um");
		$this->db->from('unidad_medida um');
		$this->db->where('estado_um', 1);
		return $this->db->get()->result_array();
	}
}
?>