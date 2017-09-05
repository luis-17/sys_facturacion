<?php
class Model_contacto_empresa extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_contacto($paramPaginate){
		$this->db->select("co.idcontacto,co.apellidos,co.nombres, co.fecha_nacimiento,co.telefono_fijo,co.telefono_movil,co.email,cl.nombre_comercial,co.idclienteempresa");
		$this->db->from('contacto co');
		$this->db->join('cliente_empresa cl', 'co.idclienteempresa = cl.idclienteempresa');
		$this->db->where('estado_co', 1);
		$this->db->where('estado_ce', 1);
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

	public function m_count_contacto($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('contacto co');
		$this->db->join('cliente_empresa cl', 'co.idclienteempresa = cl.idclienteempresa');
		$this->db->where('estado_co', 1);
		$this->db->where('estado_ce', 1);
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

	public function m_cargar_contacto_esta_empresa($paramPaginate,$paramDatos){
		$this->db->select('co.idcontacto, co.nombres, co.apellidos, co.fecha_nacimiento, co.telefono_fijo, co.telefono_movil, co.email, 
			ce.idclienteempresa, ce.nombre_comercial, ce.nombre_corto');
		$this->db->from('cliente_empresa ce');
		$this->db->join('contacto co', 'ce.idclienteempresa = co.idclienteempresa');
		$this->db->where('ce.estado_ce', 1);
		$this->db->where('co.estado_co', 1);
		$this->db->where('ce.idclienteempresa', $paramDatos['id']);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key,strtoupper_total($value) ,FALSE);
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
	public function m_count_contacto_esta_empresa($paramPaginate,$paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cliente_empresa ce');
		$this->db->join('contacto co', 'ce.idclienteempresa = co.idclienteempresa');
		$this->db->where('ce.estado_ce', 1);
		$this->db->where('co.estado_co', 1);
		$this->db->where('ce.idclienteempresa', $paramDatos['id']);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	// VALIDACIONES 

	// CRUD 
	public function m_registrar($datos)
	{
		$data = array(
			'idclienteempresa' => $datos['nombre_comercial']['id'], 
			'nombres' => $datos['nombres'],
			'apellidos' => $datos['apellidos'],	
			'fecha_nacimiento' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),	
			'telefono_fijo' => $datos['telefono_fijo'],	
			'telefono_movil' => $datos['telefono_movil'],	
			'email' => empty($datos['email']) ? NULL : $datos['email'], 
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('contacto', $data);
	}	
	public function m_editar($datos){
		// var_dump($datos);exit();
		$data = array( 
			'idclienteempresa' => $datos['nombre_comercial']['id'],  
			'nombres' => $datos['nombres'],
			'apellidos' => $datos['apellidos'],	
			'fecha_nacimiento' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),	
			'telefono_fijo' => $datos['telefono_fijo'],	
			'telefono_movil' => $datos['telefono_movil'],	
			'email' => empty($datos['email']) ? NULL : $datos['email'], 
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcontacto',$datos['id']);
		return $this->db->update('contacto', $data);
	} 
	public function m_anular($datos)
	{
		$data = array( 
			'estado_co' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		); 
		$this->db->where('idcontacto',$datos['idcontacto']);
		return $this->db->update('contacto', $data);
	} 
}
?>