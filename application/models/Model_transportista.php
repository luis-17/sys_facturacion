<?php
class Model_transportista extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_transportista($paramPaginate){ 
		$this->db->select("tr.idtransportista, tr.nombres_trans, tr.domicilio_trans, tr.ruc_trans, tr.num_lic_conducir, tr.estado_trans");
		$this->db->from('transportista tr');
		$this->db->where('estado_trans', 1);
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

	public function m_count_transportista($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('transportista tr');
		$this->db->where('estado_trans', 1);
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
			'nombres_trans' => strtoupper($datos['nombre_razon_social']),
			'domicilio_trans' => strtoupper($datos['domicilio']),
			'ruc_trans'=> empty($datos['ruc_transport']) ? NULL : $datos['ruc_transport'],
			'num_lic_conducir'=> empty($datos['num_lic_conducir']) ? NULL : strtoupper($datos['num_lic_conducir']) 
		);
		return $this->db->insert('transportista', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'nombres_trans' => strtoupper($datos['nombre_razon_social']),
			'domicilio_trans' => strtoupper($datos['domicilio']),
			'ruc_trans'=> empty($datos['ruc_transport']) ? NULL : $datos['ruc_transport'],
			'num_lic_conducir'=> empty($datos['num_lic_conducir']) ? NULL : strtoupper($datos['num_lic_conducir']) 
		);
		$this->db->where('idtransportista',$datos['id']);
		return $this->db->update('transportista', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_trans' => 0
		);
		$this->db->where('idtransportista',$datos['id']); 
		return $this->db->update('transportista', $data); 
	}

}
?>