<?php
class Model_serie extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_registrar($datos)
	{
		$data = array(
			'numero_serie' => strtoupper($datos['numero_serie']),	
			'descripcion_ser' => strtoupper($datos['descripcion_ser']),
			'idempresaadmin' => $this->sessionFactur['idempresaadmin']

		);
		return $this->db->insert('serie', $data); 
	}	
	public function m_validar_num_serie($datos) 
	{
		$this->db->select("se.idserie");
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin', $this->sessionFactur['idempresaadmin']);
		$this->db->where('se.numero_serie', $datos);
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}

	public function m_cargar_serie_cbo(){
		$this->db->select("se.idserie, se.descripcion_ser");
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin', $this->sessionFactur['idempresaadmin']); 	


		return $this->db->get()->result_array();
	}
}
?>