<?php
class Model_banco extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_banco($paramPaginate){ 
		$this->db->select("ba.idbanco, ba.descripcion_ba, ba.abreviatura_ba");
		$this->db->from('banco ba');
		$this->db->where('estado_ba', 1);
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

	public function m_count_banco($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('banco ba');
		$this->db->where('estado_ba', 1);
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
			'descripcion_ba' => strtoupper($datos['descripcion_ba']),	
			'abreviatura_ba' => strtoupper($datos['abreviatura_ba'])

		);
		return $this->db->insert('banco', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_ba' => strtoupper($datos['descripcion_ba']),	
			'abreviatura_ba' => strtoupper($datos['abreviatura_ba'])
		);
		$this->db->where('idbanco',$datos['id']);
		return $this->db->update('banco', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_ba' => 0
		);
		$this->db->where('idbanco',$datos['id']); 
		return $this->db->update('banco', $data); 
	}

	public function m_cargar_banco_cbo($datos = FALSE){ 
		$this->db->select("ba.idbanco, ba.descripcion_ba");
		$this->db->from('banco ba');
		$this->db->where('estado_ba', 1); //activo
		$this->db->order_by('ba.descripcion_ba','ASC');
		return $this->db->get()->result_array();
	}

}
?>