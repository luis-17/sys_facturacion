<?php
class Model_tipo_documento extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_tipo_documento($paramPaginate){ 
		$this->db->select("tds.idtipodocumentoserie,tdm.descripcion_tdm, se.descripcion_ser");
		$this->db->from('tipo_documento_serie tds');
		$this->db->join('serie se', 'tds.idserie = se.idserie');
		$this->db->join('tipo_documento_mov tdm', 'tds.idtipodocumentomov = tdm.idtipodocumentomov');
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
		$this->db->from('tipo_documento_serie tds');
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
			'idtipodocumentomov' => strtoupper($datos['idtipodocumentomov']),	
			'idserie' => strtoupper($datos['idserie']),
			'correlativo_actual' => strtoupper($datos['correlativo_actual'])

		);
		return $this->db->insert('tipo_documento_serie', $data); 
	}

	public function m_editar($datos)
	{
		$data = array(
			'idtipodocumentomov' => strtoupper($datos['descripcion_um']),	
			'idserie' => strtoupper($datos['idserie']),
			'correlativo_actual' => strtoupper($datos['correlativo_actual'])
		);
		$this->db->where('idtipodocumentoserie',$datos['id']);
		return $this->db->update('tipo_documento_serie', $data); 
	}

	// public function m_anular($datos)
	// {
	// 	$data = array( 
	// 		'estado_um' => 0
	// 	);
	// 	$this->db->where('idunidadmedida',$datos['id']); 
	// 	return $this->db->update('unidad_medida', $data); 
	// }

	public function listar_serie_cbo($dato){
		$this->db->select("se.idserie, se.descripcion_ser");
		$this->db->from('serie se');
		$this->db->where('idempresaadmin', $dato);
		return $this->db->get()->result_array();
	}
}
?>