<?php
class Model_Empresa extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_empresas($paramPaginate=FALSE){
		$this->db->select('idempresa, nombre_comercial, razon_social, ruc, celular, personal_contacto');
		$this->db->from('empresa');
		$this->db->where('estado_emp', 1);
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
	public function m_count_empresas($paramPaginate=FALSE){
		$this->db->select('count(*) AS contador');
		$this->db->from('empresa');
		$this->db->where('estado_emp', 1);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}	
	public function m_cargar_empresa_cbo(){
		$this->db->select('emp.idempresa, emp.nombre_comercial');
		$this->db->from('empresa emp');
		$this->db->where('emp.estado_emp', 1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_comercial' => strtoupper_total($datos['nombre_comercial']),
			'razon_social' => strtoupper_total($datos['razon_social']),
			'ruc' => $datos['ruc'],
			'celular' => empty($datos['celular'])? NULL : $datos['celular'],
			'personal_contacto' => empty($datos['personal_contacto'])? NULL : $datos['personal_contacto'],			
			'createdAt' => date('Y-m-d H:i:s'),
			'updatedAt' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('empresa', $data);
	}	
	public function m_editar($datos){
		$data = array(
			'nombre_comercial' => strtoupper_total($datos['nombre_comercial']),
			'razon_social' => strtoupper_total($datos['razon_social']),
			'ruc' => $datos['ruc'],
			'celular' => empty($datos['celular'])? NULL : $datos['celular'],
			'personal_contacto' => empty($datos['personal_contacto'])? NULL : $datos['personal_contacto'],	
			'updatedAt' => date('Y-m-d H:i:s')
		);
		$this->db->where('idempresa',$datos['idempresa']);
		return $this->db->update('empresa', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado_emp' => 0,
			'updatedAt' => date('Y-m-d H:i:s')
		);
		$this->db->where('idempresa',$datos['idempresa']);
		return $this->db->update('empresa', $data);
	}	
}
?>