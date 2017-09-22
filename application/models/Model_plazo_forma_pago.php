<?php
class Model_plazo_forma_pago extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_plazo_forma_pago($paramPaginate,$paramDatos){ 
		$this->db->select("pfp.idplazoformapago,pfp.dias_transcurridos,pfp.porcentaje_importe");
		$this->db->from('plazo_forma_pago pfp');
		$this->db->where('estado_pfp', 1);
		$this->db->where('pfp.idformapago', $paramDatos['id']);
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

	public function m_count_plazo_forma_pago($paramPaginate,$paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('plazo_forma_pago pfp');
		$this->db->where('estado_pfp', 1);
		$this->db->where('pfp.idformapago', $paramDatos['id']);
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
			'idformapago' => $datos['idformapago'],
			'dias_transcurridos'=> $datos['dias_transcurridos'],
			'porcentaje_importe' => $datos['porcentaje_importe'],			
		);
		return $this->db->insert('plazo_forma_pago', $data); 
	}

	public function m_anular($datos)
	{
		$data = array( 
			'estado_pfp' => 0
		); 
		$this->db->where('idplazoformapago',$datos['id']); 
		return $this->db->update('plazo_forma_pago', $data); 
	} 

	public function m_editar($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'dias_transcurridos'=> $datos['dias_transcurridos'],
			'porcentaje_importe' => $datos['porcentaje_importe']
		);
		$this->db->where('idplazoformapago',$datos['id']);
		return $this->db->update('plazo_forma_pago', $data); 
	}




}
?>