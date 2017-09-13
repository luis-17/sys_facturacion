<?php
class Model_variable_car extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_buscar_variable($datos){
		$this->db->select("vc.idvariablecar, vc.descripcion_vcar");
		$this->db->from('variable_car vc');
		$this->db->where('vc.descripcion_vcar',$datos['valor']);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'descripcion_vcar' => strtoupper($datos['valor']) 
		);
		return $this->db->insert('sede', $data); 
	}
}
?>