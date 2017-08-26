<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	function obtener_parametros_configuracion(){
		$ci =& get_instance();
    	$arrConfig = array();
    	$lista = $ci->model_config->m_listar_configuraciones();
    	foreach ($lista as $key => $row) {
    		$arrConfig[$row['key_cf']] = $row['valor_cf'];
    	}
		return $arrConfig;
	}