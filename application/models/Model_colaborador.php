<?php
class Model_colaborador extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	//CARGAR PERFIL
	public function m_cargar_perfil($idusuario, $idempresa = FALSE){ 
		$this->db->select('co.idcolaborador, co.nombres, co.apellidos, co.email, 
			co.fecha_nacimiento, co.nombre_foto, 
			us.idusuario ,us.username, us.ultimo_inicio_sesion, 
			tu.idtipousuario, tu.descripcion_tu, tu.key_tu, tu.categoria, 
			ea.idempresaadmin, ea.razon_social, ea.nombre_comercial, ea.ruc, ea.nombre_logo, 
			uea.idusuarioempresaadmin, uea.select_por_defecto',FALSE);
		$this->db->from('colaborador co');
		$this->db->join('usuario us', 'co.idusuario = us.idusuario');
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario');
		$this->db->join('usuario_empresa_admin uea', 'us.idusuario = uea.idusuario');
		$this->db->join('empresa_admin ea', 'uea.idempresaadmin = ea.idempresaadmin');
		$this->db->where('us.idusuario', $idusuario);
		$this->db->where('uea.select_por_defecto', 1);
		$this->db->where('uea.estado_uea', 1);
		$this->db->where('ea.estado_ea', 1);
		$this->db->where('co.estado_col', 1);
		if( $idempresa ){ 
			$this->db->where('ea.idempresaadmin', $idempresa);
		}
		$this->db->order_by('ea.razon_social', 'ASC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}	
	public function m_cargar_colaborador($paramPaginate=FALSE){ 
		$this->db->select('co.idcolaborador, co.nombres, co.apellidos, co.email, co.fecha_nacimiento, co.nombre_foto,co.telefono,co.num_documento,us.idusuario, us.username,us.password_view,us.password, us.ultimo_inicio_sesion, tu.idtipousuario, tu.descripcion_tu, tu.key_tu');
		$this->db->from('colaborador co');
		$this->db->join('usuario us', 'co.idusuario = us.idusuario AND us.estado_us = 1','left'); 
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario');
		$this->db->where('co.estado_col', 1);
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
	public function m_count_colaborador($paramPaginate=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('colaborador co');
		$this->db->join('usuario us', 'co.idusuario = us.idusuario AND us.estado_us = 1','left'); 
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario','left');
		$this->db->where('co.estado_col', 1);
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
	public function m_cargar_colaborador_cbo(){
		$this->db->select("co.idcolaborador, CONCAT(COALESCE(co.nombres,''), ' ', COALESCE(co.apellidos,'')) As colaborador",FALSE);
		$this->db->from('colaborador co');
		$this->db->join('usuario us', 'co.idusuario = us.idusuario AND us.estado_us = 1','left'); 
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario','left');
		$this->db->where('estado_col', 1);
		if( $this->sessionFactur['key_tu'] != 'key_root' ){ 
			$this->db->where_not_in('tu.key_tu', array('key_root'));
		}
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'idusuario' => $datos['idusuario'],
			'nombres' => strtoupper_total($datos['nombres']),
			'apellidos' => empty($datos['apellidos']) ? NULL : strtoupper_total($datos['apellidos']),
			'num_documento'=> empty($datos['num_documento']) ? NULL : $datos['num_documento'],
			'telefono'=> empty($datos['telefono']) ? NULL : $datos['telefono'],
			'email' => empty($datos['email'])? NULL : strtoupper_total($datos['email']), 
			'fecha_nacimiento' => empty($datos['fecha_nacimiento'])? NULL : darFormatoYMD($datos['fecha_nacimiento']), 
			// 'nombre_foto' => empty($datos['nombre_foto']) ? 'sin-imagen.png' : $datos['nombre_foto'],							
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s'),
			'idusuario'=> $datos['idusuario'],
		);
		return $this->db->insert('colaborador', $data);
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
			'apellidos' => empty($datos['apellidos']) ? NULL : strtoupper_total($datos['apellidos']),
			'num_documento'=> empty($datos['num_documento']) ? NULL : $datos['num_documento'],
			'telefono'=> empty($datos['telefono']) ? NULL : $datos['telefono'],
			'email' => empty($datos['email'])? NULL : strtoupper_total($datos['email']), 
			'fecha_nacimiento' => empty($datos['fecha_nacimiento'])? NULL : darFormatoYMD($datos['fecha_nacimiento']), 	
			'updatedat' => date('Y-m-d H:i:s') 
		);
		// var_dump($datos['fecha_nacimiento'],darFormatoYMD($datos['fecha_nacimiento'])); exit();
		$this->db->where('idcolaborador',$datos['id']);
		return $this->db->update('colaborador', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado_col' => 0,
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcolaborador',$datos['id']);
		return $this->db->update('colaborador', $data);
	}	

	public function m_cargar_cotizacion_colaborador($colaborador)
	{
		$this->db->select('co.idcotizacion');
		$this->db->from('cotizacion co');
		$this->db->where('co.idcolaborador',$colaborador['id']); 
		$this->db->where_in('co.estado_cot',array(1,2)); 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}

}
?>