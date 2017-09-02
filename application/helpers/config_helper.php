<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	function obtener_parametros_configuracion(){
		$ci =& get_instance();
    	$arrConfig = array();
    	$lista = $ci->model_configuracion->m_cargar_configuracion();
    	foreach ($lista as $key => $row) {
    		$arrConfig[$row['param_key']] = $row['param_value'];
    	}
		return $arrConfig;
	}