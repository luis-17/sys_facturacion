<?php
class Model_categoria_elemento extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	
	public function m_cargar_categoria_elemento($paramPaginate){ 
		$this->db->select("ce.idcategoriaelemento, ce.descripcion_cael, ce.color_cael");
		$this->db->from('categoria_elemento ce');
		$this->db->where('estado_cael', 1);
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

	public function m_count_categoria_elemento($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('categoria_elemento ce');
		$this->db->where('estado_cael', 1);
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
			'descripcion_cael' => strtoupper($datos['descripcion_cael']),	
			'color_cael' => strtoupper($datos['color_cael'])

		);
		return $this->db->insert('categoria_elemento', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_cael' => strtoupper($datos['descripcion_cael']),	
			'color_cael' => strtoupper($datos['color_cael'])
		);
		$this->db->where('idcategoriaelemento',$datos['id']);
		return $this->db->update('categoria_elemento', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_cael' => 0
		);
		$this->db->where('idcategoriaelemento',$datos['id']); 
		return $this->db->update('categoria_elemento', $data); 
	}

	public function m_cargar_categoria_elemento_cbo(){ 
		$this->db->select('ce.idcategoriaelemento, ce.descripcion_cael, ce.estado_cael, ce.color_cael');
		$this->db->from('categoria_elemento ce');
		$this->db->where('ce.estado_cael', 1); // activo
		$this->db->order_by('ce.descripcion_cael','ASC');
		return $this->db->get()->result_array();
	}
}
?>