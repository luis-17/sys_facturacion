<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// if(empty($_SESSION['sess_vs_talario'])){
//    var_dump($_COOKIE);
//    // var_dump($_SESSION);
//    exit();
//   }

function GetLastId($campoId,$table){
    $ci2 =& get_instance();
    $ci2->db->select('MAX('.$campoId.') AS id',FALSE);
    $ci2->db->from($table);
    $fData = $ci2->db->get()->row_array();
    return $fData['id'];
}
function getIndexArrayByValue($arr,$arrFields,$arrValores)
{
	$arrKeys = array();
  foreach($arr as $key => $value){
  	$siCumple = TRUE;
		foreach ($arrValores as $keyV => $value2) {
			if ( $value[$arrFields[$keyV]] == $value2 ){
				$arrKeys[] = $key;
			}else{
				$siCumple = FALSE;
			}
		}
		if( $siCumple ){
			return $key;
		}
  }
  return false;
}
// para verificar si un string esta compuesto de solo numeros sin comas ni puntos
function soloNumeros($laCadena) {
    $carsValidos = "0123456789";
    for ($i=0; $i<strlen($laCadena); $i++) {
      if (strpos($carsValidos, substr($laCadena,$i,1))===false) {
         return false;
      }
    }
    return true;
}

function strtoupper_total($string){
  return strtr(strtoupper($string),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
}
function strtolower_total($string){
  return strtr(strtolower($string),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü");
}

function comprobar_email($email){
    $mail_correcto = FALSE;
    //compruebo unas cosas primeras
    if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
        if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
          //miro si tiene caracter .
          if (substr_count($email,".")>= 1){
              //obtengo la terminacion del dominio
              $term_dom = substr(strrchr ($email, '.'),1);
              //compruebo que la terminación del dominio sea correcta
              if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
                //compruebo que lo de antes del dominio sea correcto
                $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
                $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
                if ($caracter_ult != "@" && $caracter_ult != "."){
                    $mail_correcto = 1;
                }
              }
          }
        }
    }
    return $mail_correcto;
}

function GetConfiguracion(){
    $ci2 =& get_instance();
    $ci2->db->select('c.empresa, c.pagina_web, c.celular, c.logo_imagen, c.correo');
    $ci2->db->from('configuracion c');
    return $ci2->db->get()->row_array();
}