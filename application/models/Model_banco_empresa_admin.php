<?php
class Model_banco_empresa_admin extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_banco_empresa_admin($paramPaginate,$paramDatos){
		$this->db->select("bea.idbancoempresaadmin,bea.idbanco,, ba.descripcion_ba, ea.nombre_comercial,bea.num_cuenta,bea.num_cuenta_inter,bea.moneda");
		$this->db->from('banco_empresa_admin bea');
		$this->db->join('empresa_admin ea', 'bea.idempresaadmin = ea.idempresaadmin');
		$this->db->join('banco ba', 'bea.idbanco = ba.idbanco');
		$this->db->where('estado_bea', 1);
		$this->db->where('estado_ea', 1);
		$this->db->where('ea.idempresaadmin', $paramDatos['id']);
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

	public function m_count_banco_empresa_admin($paramPaginate,$paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('banco_empresa_admin bea');
		$this->db->join('empresa_admin ea', 'bea.idempresaadmin = ea.idempresaadmin');
		$this->db->join('banco ba', 'bea.idbanco = ba.idbanco');		
		$this->db->where('estado_bea', 1);
		$this->db->where('estado_ea', 1);
		$this->db->where('ea.idempresaadmin', $paramDatos['id']);
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
			'idbanco' => $datos['banco']['id'], 
			'idempresaadmin' => $datos['idempresaadmin'], 
			'num_cuenta' => strtoupper($datos['num_cuenta']),	
			'num_cuenta_inter' => strtoupper($datos['num_cuenta_inter']),
			'moneda' => $datos['moneda']['id']
		);
		return $this->db->insert('banco_empresa_admin', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'idbanco' => $datos['banco']['id'], 
			// 'idempresaadmin' => $datos['idempresaadmin'], 
			'num_cuenta' => strtoupper($datos['num_cuenta']),	
			'num_cuenta_inter' => strtoupper($datos['num_cuenta_inter']),
			'moneda' => $datos['moneda']['id']
		);
		$this->db->where('idbancoempresaadmin',$datos['id']);
		return $this->db->update('banco_empresa_admin', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_bea' => 0
		); 
		$this->db->where('idbancoempresaadmin',$datos['idbancoempresaadmin']); 
		return $this->db->update('banco_empresa_admin', $data); 
	} 
	
	public function m_cargar_cuentas_banco_por_filtros($idempresaadmin,$idmoneda)
	{ 
		$this->db->select('bea.num_cuenta, bea.num_cuenta_inter, b.abreviatura_ba, bea.moneda', FALSE); 
		$this->db->from('banco_empresa_admin bea'); 
		$this->db->join('banco b','bea.idbanco = b.idbanco'); 
		$this->db->where('bea.idempresaadmin',$idempresaadmin); 
		$this->db->where('bea.moneda',$idmoneda); 
		$this->db->where('estado_bea',1);
		return $this->db->get()->result_array();
	}


}
?>