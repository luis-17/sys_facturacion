<?php
class Model_tipo_documento_serie extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_registrar_tipo_documento_serie($datos)
	{
		$data = array(
			'idtipodocumentomov' => $datos['idtipodocumentomov'],	
			'correlativo_actual' => 0,
			'idserie' => $datos['idserie']
		);
		return $this->db->insert('tipo_documento_serie', $data); 
	}

}
?>