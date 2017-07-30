<?php
class Model_cliente_empresa extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cliente_empresa($paramPaginate){
		$this->db->select("co.idcolaborador, CONCAT(co.nombres, ' ', co.apellidos) As colaborador",FALSE);
		$this->db->select('ce.idclienteempresa, ce.nombre_comercial, ce.nombre_corto, ce.razon_social, ce.ruc, ce.representante_legal, ce.dni_representante_legal, 
			ce.direccion_legal, ce.direccion_guia, ce.telefono, 
			cc.idcategoriacliente, cc.descripcion_cc');
		$this->db->from('cliente_empresa ce');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente');
		$this->db->join('colaborador co', 'ce.idcolaborador = co.idcolaborador','left');
		$this->db->where('estado_ce', 1);
		$this->db->where('ce.idempresaadmin', $this->sessionFactur['idempresaadmin']);
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
	public function m_count_cliente_empresa($paramPaginate=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cliente_empresa ce');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente');
		$this->db->where('estado_ce', 1);
		$this->db->where('ce.idempresaadmin', $this->sessionFactur['idempresaadmin']);
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
	// VALIDACIONES 
	public function m_validar_cliente_empresa_num_documento($ruc,$excepcion = FALSE,$idclienteempresa=NULL) 
	{
		$this->db->select('ce.idclienteempresa');
		$this->db->from('cliente_empresa ce');
		$this->db->where('ce.estado_ce',1);
		$this->db->where('ce.ruc',$ruc);
		$this->db->where('ce.idempresaadmin', $this->sessionFactur['idempresaadmin']);
		if( $excepcion ){
			$this->db->where_not_in('ce.idclienteempresa',$idclienteempresa);
		}
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_comercial' => strtoupper($datos['nombre_comercial']), 
			'nombre_corto' => strtoupper($datos['nombre_corto']),
			'razon_social' => strtoupper($datos['razon_social']),	
			'ruc' => $datos['ruc'],	
			'representante_legal' => empty($datos['representante_legal']) ? NULL : strtoupper($datos['representante_legal']),	
			'dni_representante_legal' => empty($datos['dni_representante_legal']) ? NULL : $datos['dni_representante_legal'],	
			'direccion_legal' => $datos['direccion_legal'],	
			'direccion_guia' => empty($datos['direccion_guia']) ? NULL : $datos['direccion_guia'],
			'telefono' => empty($datos['telefono']) ? NULL : $datos['telefono'],
			'idcategoriacliente' => $datos['categoria_cliente']['id'],
			'idempresaadmin' => $this->sessionFactur['idempresaadmin'],
			'idcolaborador' => empty($datos['colaborador']['id']) ? NULL : $datos['colaborador']['id'], 
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('cliente_empresa', $data);
	}	
	public function m_editar($datos){
		$data = array(
			'nombre_comercial' => strtoupper($datos['nombre_comercial']), 
			'nombre_corto' => strtoupper($datos['nombre_corto']),
			'razon_social' => strtoupper($datos['razon_social']),	
			'ruc' => $datos['ruc'],	
			'representante_legal' => empty($datos['representante_legal']) ? NULL : strtoupper($datos['representante_legal']),	
			'dni_representante_legal' => empty($datos['dni_representante_legal']) ? NULL : $datos['dni_representante_legal'],	
			'direccion_legal' => $datos['direccion_legal'],	
			'direccion_guia' => empty($datos['direccion_guia']) ? NULL : $datos['direccion_guia'],
			'telefono' => empty($datos['telefono']) ? NULL : $datos['telefono'],
			'idcategoriacliente' => $datos['categoria_cliente']['id'],
			'idcolaborador' => empty($datos['colaborador']['id']) ? NULL : $datos['colaborador']['id'], 
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idclienteempresa',$datos['id']);
		return $this->db->update('cliente_empresa', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado_ce' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idclienteempresa',$datos['id']);
		return $this->db->update('cliente_empresa', $data);
	}	
}
?>