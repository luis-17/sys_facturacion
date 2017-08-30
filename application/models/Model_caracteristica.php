<?php
class Model_caracteristica extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_caracteristica($paramPaginate){ 
		$this->db->select("ca.idcaracteristica, ca.descripcion_car");
		$this->db->from('caracteristica ca');
		$this->db->where('estado_car', 1);
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

	public function m_count_caracteristica($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('caracteristica ca');
		$this->db->where('estado_car', 1);
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

	public function m_registrar($datos)
	{
		$data = array(
			'descripcion_car' => strtoupper($datos['descripcion_car'])
		);
		return $this->db->insert('caracteristica', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_car' => strtoupper($datos['descripcion_car'])
		);
		$this->db->where('idcaracteristica',$datos['id']);
		return $this->db->update('caracteristica', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_car' => 0
		);
		$this->db->where('idcaracteristica',$datos['id']); 
		return $this->db->update('caracteristica', $data); 
	}

}
?>