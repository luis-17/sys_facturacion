<?php
class Model_caja extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_caja($paramPaginate){ 
		$this->db->select("c.idcaja,c.idserie,c.nombre_caja,c.maquina_reg,c.idempresaadmin,se.descripcion_ser");
		$this->db->from('caja c');
		$this->db->join('serie se', 'c.idserie = se.idserie');
		$this->db->where('estado_caja', 1);
		$this->db->where('c.idempresaadmin', $this->sessionFactur['idempresaadmin']);
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

	public function m_count_caja($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('caja c');
		$this->db->where('estado_caja', 1);
		$this->db->where('c.idempresaadmin', $this->sessionFactur['idempresaadmin']);
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
		// var_dump($datos);exit();
		$data = array(
			'idserie' => $datos['serie']['id'],
			'nombre_caja' => strtoupper($datos['nombre_caja']),	
			'maquina_reg' => strtoupper($datos['maquina_reg']),
			'idempresaadmin' => $this->sessionFactur['idempresaadmin']

		);
		return $this->db->insert('caja', $data); 
	}

	public function m_editar($datos)
	{	
		$data = array(
			'idserie' => $datos['serie']['id'],
			'nombre_caja' => strtoupper($datos['nombre_caja']),	
			'maquina_reg' => strtoupper($datos['maquina_reg'])
		);
		$this->db->where('idcaja',$datos['id']);
		return $this->db->update('caja', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_caja' => 0
		);
		$this->db->where('idcaja',$datos['id']); 
		return $this->db->update('caja', $data); 
	}

}
?>