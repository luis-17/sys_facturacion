<?php
class Model_plazo_forma_pago extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_plazo_forma_pago($paramDatos){ 
		$this->db->select("pfp.idplazoformapago,pfp.dias_transcurridos,pfp.porcentaje_importe,pfp.idformapago");
		$this->db->from('plazo_forma_pago pfp');
		$this->db->where('estado_pfp', 1);
		$this->db->where('pfp.idformapago', $paramDatos['id']);
		$this->db->order_by('pfp.dias_transcurridos','ASC');
		return $this->db->get()->result_array();
	}

	public function m_count_plazo_forma_pago($paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('plazo_forma_pago pfp');
		$this->db->where('estado_pfp', 1);
		$this->db->where('pfp.idformapago', $paramDatos['id']);
		$fData = $this->db->get()->row_array();
		return $fData;
	}

	public function m_cargar_plazo_forma_pago_detalle($paramDatos){ 
		$this->db->select("pfp.idplazoformapago,pfp.dias_transcurridos,pfp.porcentaje_importe,pfp.idformapago");
		$this->db->from('plazo_forma_pago pfp');
		$this->db->where('estado_pfp', 1);
		$this->db->where('pfp.idformapago', $paramDatos);
		$this->db->order_by('pfp.dias_transcurridos','ASC');
		return $this->db->get()->result_array();
	}

	// VALIDACIONES 

	// CRUD 
	public function m_registrar($datos)
	{	
		// var_dump($datos);exit();
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