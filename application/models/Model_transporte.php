<?php
class Model_transporte extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_transporte($paramPaginate){ 
		$this->db->select("tr.idtransporte, tr.marca_transporte, tr.placa_transporte, tr.num_cert_inscripcion, tr.estado_transporte");
		$this->db->from('transporte tr');
		$this->db->where('estado_transporte', 1);
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

	public function m_count_transporte($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('transporte tr');
		$this->db->where('estado_transporte', 1);
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
			'marca_transporte' => strtoupper($datos['marca_transporte']),
			'placa_transporte' => strtoupper($datos['placa_transporte']),
			'num_cert_inscripcion'=> empty($datos['num_cert_inscripcion']) ? NULL : strtoupper($datos['num_cert_inscripcion']) 
		);
		return $this->db->insert('transporte', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'marca_transporte' => strtoupper($datos['marca_transporte']),
			'placa_transporte' => strtoupper($datos['placa_transporte']),
			'num_cert_inscripcion'=> empty($datos['num_cert_inscripcion']) ? NULL : strtoupper($datos['num_cert_inscripcion']) 
		);
		$this->db->where('idtransporte',$datos['id']);
		return $this->db->update('transporte', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_transporte' => 0
		);
		$this->db->where('idtransporte',$datos['id']); 
		return $this->db->update('transporte', $data); 
	}

}
?>