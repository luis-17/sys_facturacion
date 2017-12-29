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
		$this->db->where('estado_vcar', 1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_variable_car($paramPaginate){ 
		$this->db->select("vc.idvariablecar, vc.descripcion_vcar"); 
		$this->db->from('variable_car vc');
		$this->db->where('estado_vcar', 1);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}

		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_variable_car($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('variable_car vc');
		$this->db->where('estado_vcar', 1);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_cargar_variable_limite($datos)
	{
		$this->db->select('vc.idvariablecar, vc.descripcion_vcar');
		$this->db->from('variable_car vc');
		$this->db->where('vc.estado_vcar', 1);
		$this->db->like('vc.descripcion_vcar', $datos['searchText']);
		$this->db->order_by('vc.descripcion_vcar','ASC'); 
		$this->db->limit($datos['limite']);
		return $this->db->get()->result_array();
	}

	public function m_registrar($datos)
	{
		$data = array(
			'descripcion_vcar' => strtoupper($datos['descripcion_vcar'])
		);
		return $this->db->insert('variable_car', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_vcar' => strtoupper($datos['descripcion_vcar'])
		);
		$this->db->where('idvariablecar',$datos['id']);
		return $this->db->update('variable_car', $data); 
	}

	public function m_anular($datos)
	{
		$this->db->where('idvariablecar',$datos['id']);
		return $this->db->delete('variable_car');
	}
	public function m_cargar_esta_variable_car($datos)
	{	
	
		$this->db->select('vc.idvariablecar');
		$this->db->from('variable_car vc');
		$this->db->where('vc.descripcion_vcar',$datos['descripcion_vcar']);
		$this->db->where('vc.estado_vcar',1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}


}
?>