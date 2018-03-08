<?php
class Model_tipo_documento_mov extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_tipo_documento_mov($paramPaginate){ 
		$this->db->select("tdm.idtipodocumentomov, tdm.descripcion_tdm, tdm.porcentaje_imp,tdm.abreviatura_tdm,se.numero_serie, se.descripcion_ser,se.idempresaadmin");
		$this->db->from('tipo_documento_mov tdm');
		$this->db->join("tipo_documento_serie tds","tds.idtipodocumentomov = tdm.idtipodocumentomov",'left'); 
		$this->db->join("serie se","tds.idserie = se.idserie",'left'); 
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
	public function m_count_tipo_documento_mov($paramPaginate){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('tipo_documento_mov tdm');
		$this->db->join("tipo_documento_serie tds","tds.idtipodocumentomov = tdm.idtipodocumentomov",'left'); 
		$this->db->join("serie se","tds.idserie = se.idserie",'left'); 
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
	public function m_cargar_tipo_documento_grilla()
	{
		$this->db->select("tdm.idtipodocumentomov, tdm.descripcion_tdm, tdm.porcentaje_imp,tdm.abreviatura_tdm, 
			se.idserie ,se.numero_serie, se.descripcion_ser, se.idempresaadmin, 
			tds.idtipodocumentoserie, tds.correlativo_actual"); 
		$this->db->from('tipo_documento_mov tdm'); 
		$this->db->join("tipo_documento_serie tds","tds.idtipodocumentomov = tdm.idtipodocumentomov",'left'); 
		$this->db->join("serie se","tds.idserie = se.idserie AND se.idempresaadmin = ".$this->sessionFactur['idempresaadmin'],'left'); 
		$this->db->where('tdm.estado_tdm', 1); 
		// $this->db->where('se.idempresaadmin', ); // empresa session 
		$this->db->order_by('tdm.idtipodocumentomov','ASC'); 
		return $this->db->get()->result_array(); 
	}
	public function m_cargar_tipo_documento_serie(){ 
		$this->db->select("se.idserie,se.numero_serie, se.descripcion_ser,se.idempresaadmin");
		$this->db->from('serie se');	
		$this->db->where('idempresaadmin', 1);
		return $this->db->get()->result_array();
	}
	public function m_cargar_tipo_documento_mov_para_venta_cbo()
	{
		$this->db->select("tdm.idtipodocumentomov, tdm.descripcion_tdm, tdm.porcentaje_imp, tdm.abreviatura_tdm, tdm.key_tdm");
		$this->db->from('tipo_documento_mov tdm');
		$this->db->where('tdm.estado_tdm', 1); // activo 
		$this->db->where('tdm.para_venta', 1); // form venta  
		return $this->db->get()->result_array();
	}
	public function m_cargar_configuracion_td($datos)
	{
		$this->db->select("tdm.idtipodocumentomov, tdm.descripcion_tdm, tdm.key_tdm, tdc.idtipodocumentoconfig, tdc.tamanio_fuente, tdc.tipo_fuente, tdc.unidad_medida");
		$this->db->from('tipo_documento_config tdc');
		$this->db->join('tipo_documento_mov tdm','tdc.idtipodocumentomov = tdm.idtipodocumentomov'); 
		$this->db->where('tdc.idtipodocumentomov', $datos['idtipodocumentomov']); 
		$this->db->limit(1); 
		return $this->db->get()->row_array();
	}
	public function m_cargar_configuracion_detalle_td($datos)
	{
		$this->db->select("tdc.idtipodocumentoconfig, tdc.tamanio_fuente, tdc.tipo_fuente, tdc.unidad_medida, 
			tcd.idtdconfigdetalle, tcd.descripcion_elemento, tcd.key_config_detalle, tcd.valor_x, tcd.valor_y, tcd.valor_w, tcd.visible"); 
		$this->db->from('tipo_documento_config tdc');
		$this->db->join('td_config_detalle tcd','tdc.idtipodocumentoconfig = tcd.idtipodocumentoconfig'); 
		$this->db->where('tdc.idtipodocumentoconfig', $datos['idtipodocumentoconfig']); 
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'descripcion_tdm' => strtoupper($datos['tipo_documento']),	
			'abreviatura_tdm' => strtoupper($datos['abreviatura']),
			'porcentaje_imp' => $datos['porcentaje'] 
		);
		return $this->db->insert('tipo_documento_mov', $data); 
	}
	public function m_editar_formato_impresion($datos)
	{
		$data = array(
			$datos['campo'] => $datos['nuevo_valor']
		);
		$this->db->where('idtdconfigdetalle',$datos['idtdconfigdetalle']);
		return $this->db->update('td_config_detalle', $data); 
	}
	public function m_editar($datos)
	{
		$data = array(
			'descripcion_tdm' => strtoupper($datos['tipo_documento']),	
			'abreviatura_tdm' => strtoupper($datos['abreviatura']),
			'porcentaje_imp' => $datos['porcentaje']
		);
		$this->db->where('idtipodocumentomov',$datos['idtipodocumentomov']);
		return $this->db->update('tipo_documento_mov', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_tdm' => 0
		);
		$this->db->where('idtipodocumentomov',$datos['idtipodocumentomov']); 
		return $this->db->update('tipo_documento_mov', $data); 
	}

}
?>