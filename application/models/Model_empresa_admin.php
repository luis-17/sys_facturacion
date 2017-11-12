<?php
class Model_empresa_admin extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_empresa_admin($paramPaginate){ 
		$this->db->select("ea.idempresaadmin,ea.razon_social,ea.nombre_comercial,ea.ruc,ea.direccion_legal,ea.representante_legal,ea.telefono,ea.pagina_web");
		$this->db->from('empresa_admin ea');
		$this->db->where('estado_ea', 1);
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

	public function m_count_empresa_admin($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('empresa_admin ea');
		$this->db->where('estado_ea', 1);
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
			'razon_social' => strtoupper($datos['razon_social']),	
			'nombre_comercial' => strtoupper($datos['nombre_comercial']),
			'ruc' => strtoupper($datos['ruc']),
			'direccion_legal' => strtoupper($datos['direccion_legal']),
			'representante_legal' => strtoupper($datos['representante_legal']),
			'telefono' => $datos['telefono'],
			'pagina_web' => $datos['pagina_web'],
		);
		return $this->db->insert('empresa_admin', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'razon_social' => strtoupper($datos['razon_social']),	
			'nombre_comercial' => strtoupper($datos['nombre_comercial']),
			'ruc' => strtoupper($datos['ruc']),
			'direccion_legal' => strtoupper($datos['direccion_legal']),
			'representante_legal' => strtoupper($datos['representante_legal']),
			'telefono' => $datos['telefono'],
			'pagina_web' => $datos['pagina_web'],
		);
		$this->db->where('idempresaadmin',$datos['id']);
		return $this->db->update('empresa_admin', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_ea' => 0
		);
		$this->db->where('idempresaadmin',$datos['id']); 
		return $this->db->update('empresa_admin', $data); 
	}

	public function m_cargar_empresa_cbo($datos = FALSE){ 
		$this->db->select("ea.idempresaadmin, ea.razon_social");
		$this->db->from('empresa_admin ea');
		$this->db->where('estado_ea', 1); //activo
		$this->db->order_by('ea.razon_social','ASC');
		return $this->db->get()->result_array();
	}


	public function m_cargar_empresa_admin_por_id($idempresaadmin)
	{
		$this->db->select("ea.idempresaadmin,ea.razon_social,ea.telefono,ea.nombre_logo,ea.pagina_web,ea.direccion_legal",FALSE);
		$this->db->from('empresa_admin ea'); 
		$this->db->where( 'ea.idempresaadmin', $idempresaadmin); 
		$this->db->limit(1);
		$fData = $this->db->get()->row_array();
		return $fData; 
	}


}
?>