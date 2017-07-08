<?php
class Model_categoria_cliente extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cliente_empresa($paramPaginate){
		$this->db->select('ce.idclienteempresa, ce.nombre_comercial, ce.nombre_corto, ce.razon_social, ce.ruc, ce.representante_legal, ce.direccion_legal, ce.direccion_guia, ce.telefono, 
			cc.idcategoriacliente, cc.descripcion_cc');
		$this->db->from('cliente_empresa ce');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente');
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
	public function m_count_cliente_empresa($paramPaginate=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cliente_empresa ce');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente');
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
}
?>