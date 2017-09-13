<?php
class Model_sede extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_sede($paramPaginate){ 
		$this->db->select("se.idsede, se.descripcion_se, se.direccion_se, se.abreviatura_se");
		$this->db->from('sede se');
		$this->db->where('estado_se', 1);
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

	public function m_count_sede($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('sede se');
		$this->db->where('estado_se', 1);
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
	public function m_cargar_sede_cbo(){
		$this->db->select("se.idsede, se.descripcion_se, se.direccion_se, se.abreviatura_se");
		$this->db->from('sede se');
		$this->db->where('estado_se', 1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'descripcion_se' => strtoupper($datos['descripcion_se']),	
			'direccion_se' => strtoupper($datos['direccion_se']),
			'abreviatura_se' => strtoupper($datos['abreviatura_se'])

		);
		return $this->db->insert('sede', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_se' => strtoupper($datos['descripcion_se']),	
			'direccion_se' => strtoupper($datos['direccion_se']),
			'abreviatura_se' => strtoupper($datos['abreviatura_se'])
		);
		$this->db->where('idsede',$datos['id']);
		return $this->db->update('sede', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_se' => 0
		);
		$this->db->where('idsede',$datos['id']); 
		return $this->db->update('sede', $data); 
	}


}
?>