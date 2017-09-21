<?php
class Model_usuario_empresa_admin extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_usuario_empresa_admin($paramPaginate,$paramDatos){ 
		$this->db->select("uea.idusuarioempresaadmin,uea.idusuario,uea.idempresaadmin,uea.select_por_defecto,ea.razon_social,ea.nombre_comercial,ea.ruc,ea.direccion_legal,ea.representante_legal,ea.telefono,ea.pagina_web");
		$this->db->from('usuario_empresa_admin uea');
		$this->db->join('empresa_admin ea', 'uea.idempresaadmin = ea.idempresaadmin');
		$this->db->where('estado_uea', 1);
		$this->db->where('estado_ea', 1);
		$this->db->where('uea.idusuario', $paramDatos['idusuario']);
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

	public function m_count_usuario_empresa_admin($paramPaginate,$paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('usuario_empresa_admin uea');
		$this->db->join('empresa_admin ea', 'uea.idempresaadmin = ea.idempresaadmin');
		$this->db->where('estado_uea', 1);
		$this->db->where('estado_ea', 1);
		$this->db->where('uea.idusuario', $paramDatos['idusuario']);
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

	// CRUD 
	public function m_registrar($datos)
	{		
		$data = array(
			'idempresaadmin' => $datos['empresa']['id'],
			'idusuario'=> $datos['idusuario'],
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')			
		);
		return $this->db->insert('usuario_empresa_admin', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_uea' => 0
		); 
		$this->db->where('idusuarioempresaadmin',$datos['id']); 
		return $this->db->update('usuario_empresa_admin', $data); 
	} 

	public function m_editar_selectpordefecto($datos)
	{
		$data = array(
					'select_por_defecto'=> $datos[2]['newValue'],
					'createdat' => date('Y-m-d H:i:s'),
					'updatedat' => date('Y-m-d H:i:s')	
		);
		$this->db->where('idusuarioempresaadmin',$datos[0]['edited_row_id']);
		return $this->db->update('usuario_empresa_admin', $data); 
	}	

	public function m_cargar_selectpordefecto($datos)
	{
		$this->db->select("uea.idusuarioempresaadmin,uea.select_por_defecto");
		$this->db->from('usuario_empresa_admin uea');
		$this->db->where('estado_uea', 1);
		$this->db->where('uea.select_por_defecto', $datos[2]['newValue']);
		$this->db->where('uea.idusuario', $datos[5]['idusuario']);

		return $this->db->get()->row_array();
	}

	public function m_cargar_empresa($datos)
	{	
		// var_dump($idempresaadmin);exit();
		$this->db->select('ea.idempresaadmin');
		$this->db->from('empresa_admin ea');
		$this->db->join('usuario_empresa_admin uea', 'uea.idempresaadmin = ea.idempresaadmin');
		$this->db->where('ea.idempresaadmin',$datos['empresa']['id']);
		$this->db->where('uea.idusuario',$datos['idusuario']);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}

}
?>