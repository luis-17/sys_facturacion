<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Acceso extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('security'));
		$this->load->model(array('model_acceso','model_profesional'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");		
	}

	public function index(){
		//$this->load->library('encrypt');
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$arrData['flag'] = 0;
    	$arrData['message'] = 'No se encontraron datos.';
		if(!empty($allInputs['usuario']) && !empty($allInputs['clave']) ){ 
			$loggedUser = $this->model_acceso->m_logging_user($allInputs);
			if( isset($loggedUser['logged']) && $loggedUser['logged'] > 0 ){				
    			if($loggedUser['estado_us'] == 1){
					$arrData['flag'] = 1;
					$arrPerfilUsuario = array();
					// $arrPerfilUsuario['idusuario'] = $loggedUser['idusuario'];
					// $arrPerfilUsuario['idgrupo'] = $loggedUser['idgrupo'];
					// $arrPerfilUsuario['username'] = strtoupper($loggedUser['username']);
					$arrPerfilUsuario = $this->model_profesional->m_cargar_perfil($loggedUser['idusuario']);
					$arrPerfilUsuario['nombre_foto'] = empty($arrPerfilUsuario['nombre_foto']) ? 'sin-imagen.png' : $arrPerfilUsuario['nombre_foto'];
					// $arrPerfilUsuario['idprofesional'] = $perfil['idprofesional'];
					// $arrPerfilUsuario['idusuario'] = $perfil['idusuario'];
					// $arrPerfilUsuario['idespecialidad'] = $perfil['idespecialidad'];
					// $arrPerfilUsuario['especialidad'] = $perfil['especialidad'];
					// $arrPerfilUsuario['nombre'] = $perfil['nombre'];
					// $arrPerfilUsuario['apellidos'] = $perfil['apellidos'];
					// $arrPerfilUsuario['correo'] = $perfil['correo'];
					// $arrPerfilUsuario['fecha_nacimiento'] = $perfil['fecha_nacimiento'];
					// $arrPerfilUsuario['num_colegiatura'] = $perfil['num_colegiatura'];
					
				    					
					// GUARDAMOS EN EL LOG DE LOGEO LA SESION INICIADA. 
					//$this->model_acceso->m_registrar_log_sesion($arrPerfilUsuario);
					// ACTUALIZAMOS EL ULTIMO LOGEO DEL USUARIO. 
					//$this->model_acceso->m_actualizar_fecha_ultima_sesion($arrPerfilUsuario);
					$arrData['message'] = 'Usuario inició sesión correctamente';
					if( isset($arrPerfilUsuario['idusuario']) ){ 
						$this->session->set_userdata('sess_vp_'.substr(base_url(),-8,7),$arrPerfilUsuario);
						
					}else{
						$arrData['flag'] = 0;
	    				$arrData['message'] = 'No se encontró los datos del usuario.';
					}
				}/*elseif($loggedUser['estado_us'] == 2){
					$arrData['flag'] = 2;
					$arrData['message'] = 'Su cuenta se encuentra deshabilitada. Debe verificarla mediante el email enviado.';
				}*/ 				
			}else{ 
    			$arrData['flag'] = 0;
    			$arrData['message'] = 'Usuario o contraseña invalida. Inténtelo nuevamente.';
    		}		
		} 

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function getSessionCI(){
		$arrData['flag'] = 0;
		$arrData['datos'] = array();
			// var_dump($_SESSION); exit();
		
		if( $this->session->has_userdata( 'sess_vp_'.substr(base_url(),-8,7) ) && 
			!empty($_SESSION['sess_vp_'.substr(base_url(),-8,7) ]['idusuario']) ){
			$arrData['flag'] = 1;
			$arrData['datos'] = $_SESSION['sess_vp_'.substr(base_url(),-8,7) ];
		}

		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function logoutSessionCI(){
		$this->session->unset_userdata('sess_vp_'.substr(base_url(),-8,7));
        //$this->cache->clean();
        $arrData['flag'] = 1;
		$arrData['datos'] = 'Salida OK';
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

}