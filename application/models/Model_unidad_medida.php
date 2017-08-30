<?php
class Model_unidad_medida extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_unidad_medida($paramPaginate){ 
		$this->db->select("um.idunidadmedida, um.descripcion_um, um.abreviatura_um");
		$this->db->from('unidad_medida um');
		$this->db->where('estado_um', 1);
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

	public function m_count_unidad_medida($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('unidad_medida um');
		$this->db->where('estado_um', 1);
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
			'descripcion_um' => strtoupper($datos['descripcion_um']),	
			'abreviatura_um' => strtoupper($datos['abreviatura_um'])

		);
		return $this->db->insert('unidad_medida', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_um' => strtoupper($datos['descripcion_um']),	
			'abreviatura_um' => strtoupper($datos['abreviatura_um'])
		);
		$this->db->where('idunidadmedida',$datos['id']);
		return $this->db->update('unidad_medida', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_um' => 0
		);
		$this->db->where('idunidadmedida',$datos['id']); 
		return $this->db->update('unidad_medida', $data); 
	}

	public function m_cargar_unidad_medida_cbo(){
		$this->db->select("um.idunidadmedida, um.descripcion_um, um.abreviatura_um");
		$this->db->from('unidad_medida um');
		$this->db->where('estado_um', 1);
		return $this->db->get()->result_array();
	}
}
?>