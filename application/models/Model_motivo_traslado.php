<?php
class Model_motivo_traslado extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_motivo_traslado_cbo(){ 
		$this->db->select("mt.idmotivotraslado, mt.descripcion_mt, mt.orden_en_guia, mt.key_motivo_traslado, mt.posicion_guia_x, mt.posicion_guia_y, mt.estado_mt"); 
		$this->db->from('motivo_traslado mt');
		$this->db->where('mt.estado_mt', 1); // habilitado 
		return $this->db->get()->result_array();
	}
}
?>