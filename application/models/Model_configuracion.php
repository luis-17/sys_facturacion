<?php
class Model_configuracion extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_configuracion(){
		$this->db->select("cf.idconfiguracion, cf.descripcion_cf, cf.param_key, cf.param_value, cf.comentario"); 
		$this->db->from('configuracion cf');
		$this->db->where('cf.estado_cf', 1);
		return $this->db->get()->result_array();
	}
	public function m_editar_configuracion($datos)
	{
		$data = array( 
			'param_value' => $datos['param_value'] 
		);
		$this->db->where('param_key',$datos['param_key']); 
		return $this->db->update('configuracion', $data); 
	}
}
?>