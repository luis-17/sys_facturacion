<?php
class Model_usuario extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_usuario($paramPaginate){ 
		$this->db->select("u.idusuario,u.idtipousuario,u.username,u.password_view,tu.descripcion_tu,u.password");
		$this->db->from('usuario u');
		$this->db->join('tipo_usuario tu', 'u.idtipousuario = tu.idtipousuario');
		$this->db->where('estado_us', 1);
		$this->db->where('estado_tu', 1);
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
		$this->db->where('estado_us', 1);
		$this->db->where('estado_tu', 1);
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

	public function m_cargar_usuario_cbo(){
		$this->db->select("tu.idtipousuario, tu.descripcion_tu",FALSE);
		$this->db->from('tipo_usuario tu');
		$this->db->where('estado_tu', 1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'idtipousuario' => $datos['tipo_usuario']['id'],
			'username' => strtoupper_total($datos['username']),
			'password'=> md5($datos['password_view']),			
			'password_view'=>strtoupper_total($datos['password_view']),		
			'ultimo_inicio_sesion' => date('Y-m-d H:i:s'),	
			// 'ip_address'=>  $_SERVER['REMOTE_ADDR'],						
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
		$data = array(
			// 'idusuario' => $datos['idusuario'],
			'nombres' => strtoupper_total($datos['nombres']),
			'apellidos' => strtoupper_total($datos['apellidos']),
			'num_documento'=> $datos['num_documento'],
			'telefono'=> empty($datos['telefono']) ? NULL : $datos['telefono'],
			'email' => empty($datos['email'])? NULL : strtoupper_total($datos['email']), 
			'fecha_nacimiento' => empty($datos['fecha_nacimiento'])? NULL : darFormatoYMD($datos['fecha_nacimiento']), 	
			'updatedat' => date('Y-m-d H:i:s') 
		);
		// var_dump($datos['fecha_nacimiento'],darFormatoYMD($datos['fecha_nacimiento'])); exit();
		$this->db->where('idcolaborador',$datos['id']);
		return $this->db->update('colaborador', $data);
	}	

}
?>