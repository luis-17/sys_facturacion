<?php
class Model_cotizacion extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_ultima_cotizacion_sede_dia($datos)
	{
		$this->db->select('co.idcotizacion, co.num_cotizacion');
		$this->db->from('cotizacion co');
		$this->db->join('sede se', 'co.idsede = se.idsede');
		$this->db->where_in('co.estado_cot',array(1,2)); // solo "por enviar" y "enviado" 
		$this->db->where('se.idsede',$datos['sede']['id']);
		$this->db->where('DATE(co.fecha_registro)',date('Y-m-d'));
		$this->db->order_by('co.fecha_registro','DESC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
}
?>