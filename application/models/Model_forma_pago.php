<?php
class Model_forma_pago extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_forma_pago($paramPaginate){ 
		$this->db->select("fp.idformapago, fp.descripcion_fp, fp.modo_fp");
		$this->db->from('forma_pago fp');
		$this->db->where('estado_fp', 1);
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

	public function m_count_forma_pago($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('forma_pago fp');
		$this->db->where('estado_fp', 1);
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
			'descripcion_fp' => strtoupper($datos['descripcion_fp']),
			'modo_fp' => $datos['modo_fp']	

		);
		return $this->db->insert('forma_pago', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_fp' => strtoupper($datos['descripcion_fp']),
			'modo_fp' => $datos['modo_fp']	
		);
		$this->db->where('idformapago',$datos['id']);
		return $this->db->update('forma_pago', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_fp' => 0
		);
		$this->db->where('idformapago',$datos['id']); 
		return $this->db->update('forma_pago', $data); 
	}

	public function m_cargar_formas_pago_cbo(){ 
		$this->db->select("fp.idformapago, fp.descripcion_fp, fp.modo_fp");
		$this->db->from('forma_pago fp');
		$this->db->where('estado_fp', 1);
		return $this->db->get()->result_array();
	}
}
?>