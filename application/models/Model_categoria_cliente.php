<?php
class Model_categoria_cliente extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_categoria_cliente($paramPaginate){ 
		$this->db->select("cc.idcategoriacliente, cc.descripcion_cc");
		$this->db->from('categoria_cliente cc');
		$this->db->where('estado_cc', 1);
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

	public function m_count_categoria_cliente($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('categoria_cliente cc');
		$this->db->where('estado_cc', 1);
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
			'descripcion_cc' => strtoupper($datos['descripcion_cc']),
		);
		return $this->db->insert('categoria_cliente', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_cc' => strtoupper($datos['descripcion_cc']),
		);
		$this->db->where('idcategoriacliente',$datos['idcategoriacliente']);
		return $this->db->update('categoria_cliente', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_cc' => 0
		);
		$this->db->where('idcategoriacliente',$datos['idcategoriacliente']); 
		return $this->db->update('categoria_cliente', $data); 
	}

	public function m_cargar_categoria_cliente_cbo($datos = FALSE){ 
		$this->db->select('cc.idcategoriacliente, cc.descripcion_cc, cc.estado_cc');
		$this->db->from('categoria_cliente cc');
		$this->db->where('cc.estado_cc', 1); // activo
		$this->db->order_by('cc.descripcion_cc','ASC');
		return $this->db->get()->result_array();
	}


}
?>