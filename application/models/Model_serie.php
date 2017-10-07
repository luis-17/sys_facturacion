<?php
class Model_serie extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_serie_cbo(){
		$this->db->select("se.idserie, se.descripcion_ser");
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin', $this->sessionFactur['idempresaadmin']); 	


		return $this->db->get()->result_array();
	}
}
?>