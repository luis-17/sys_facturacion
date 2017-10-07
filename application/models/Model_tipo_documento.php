<?php
class Model_tipo_documento extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_tipo_documento($paramPaginate){ 
		$this->db->select("tdm.idtipodocumentomov, tdm.descripcion_tdm, tdm.porcentaje_imp,tdm.abreviatura_tdm");
		$this->db->from('tipo_documento_mov tdm');
		$this->db->where('estado_tdm', 1);
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

	public function m_count_tipo_documento($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('tipo_documento_mov tdm');
		$this->db->where('estado_tdm', 1);
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
			'descripcion_tdm' => strtoupper($datos['descripcion_tdm']),	
			'abreviatura_tdm' => strtoupper($datos['abreviatura_tdm']),
			'porcentaje_imp' => strtoupper($datos['porcentaje_imp'])

		);
		return $this->db->insert('tipo_documento_mov', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'descripcion_tdm' => strtoupper($datos['descripcion_tdm']),	
			'abreviatura_tdm' => strtoupper($datos['abreviatura_tdm']),
			'porcentaje_imp' => strtoupper($datos['porcentaje_imp'])
		);
		$this->db->where('idtipodocumentomov',$datos['id']);
		return $this->db->update('tipo_documento_mov', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_tdm' => 0
		);
		$this->db->where('idtipodocumentomov',$datos['id']); 
		return $this->db->update('tipo_documento_mov', $data); 
	}

}
?>