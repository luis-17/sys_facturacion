<?php
class Model_forma_pago extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_formas_pago_cbo(){ 
		$this->db->select("fp.idformapago, fp.descripcion_fp, fp.modo_fp");
		$this->db->from('forma_pago fp');
		$this->db->where('estado_fp', 1);
		return $this->db->get()->result_array();
	}
}
?>