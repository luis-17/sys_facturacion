<?php
class Model_usuario extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_usuario($paramPaginate){ 
		$this->db->select("u.idusuario, u.idtipousuario, u.username, u.password_view, u.password, u.ultimo_inicio_sesion, tu.descripcion_tu");
		$this->db->from('usuario u');
		$this->db->join('tipo_usuario tu', 'u.idtipousuario = tu.idtipousuario');
		$this->db->where('estado_us', 1);
		$this->db->where('estado_tu', 1); 
		if( $this->sessionFactur['key_tu'] != 'key_root' ){ 
			$this->db->where_not_in('tu.key_tu', array('key_root'));
		}
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

	public function m_count_usuario($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('usuario u');
		$this->db->join('tipo_usuario tu', 'u.idtipousuario = tu.idtipousuario');
		$this->db->where('u.estado_us', 1);
		$this->db->where('tu.estado_tu', 1);
		if( $this->sessionFactur['key_tu'] != 'key_root' ){ 
			$this->db->where_not_in('tu.key_tu', array('key_root'));
		}
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

	public function m_cargar_tipo_usuario_cbo(){
		$this->db->select("tu.idtipousuario, tu.descripcion_tu",FALSE);
		$this->db->from('tipo_usuario tu');
		$this->db->where('estado_tu', 1);
		if( $this->sessionFactur['key_tu'] != 'key_root' ){ 
			$this->db->where_not_in('tu.key_tu', array('key_root'));
		}
		return $this->db->get()->result_array();
	}
	// VALIDACIONES 
	public function m_validar_usuario_username($username,$excepcion = FALSE,$idusuario=NULL) 
	{
		$this->db->select('u.idusuario');
		$this->db->from('usuario u');
		$this->db->where('u.estado_us',1);
		$this->db->where('u.username',$username);
		if( $excepcion ){
			$this->db->where_not_in('u.idusuario',$idusuario);
		}
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}	
	public function m_registrar($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'idtipousuario' => $datos['tipo_usuario']['id'],
			'username' => $datos['username'],
			'password'=> md5($datos['password_view']),			
			'password_view'=>strtoupper_total($datos['password_view']),						
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('usuario', $data);
	}	
	public function m_editar_foto($datos){
		$data = array(
			'nombre_foto' => $datos['nombre_foto'],
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcolaborador',$datos['idcolaborador']);
		return $this->db->update('colaborador', $data);
	}	
	public function m_editar($datos){
		// var_dump($datos);exit();
		$data = array(
			'idtipousuario' => $datos['tipo_usuario']['id'], 
			'username' => $datos['username'], 
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idusuario',$datos['idusuario']);
		return $this->db->update('usuario', $data);
	} 
	public function m_anular($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'estado_us' => 0,
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idusuario',$datos['idusuario']);
		return $this->db->update('usuario', $data);
	}	

}
?>