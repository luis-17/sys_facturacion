<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function mostrar_plantilla_pdf($obj,$titulo,$subTitulo = false,$tituloAbr=false,$arrConfig = FALSE)
{
    $ci2 =& get_instance(); 
    if( empty($arrConfig) ){
        $fConfig = $ci2->model_config->m_cargar_empresa_usuario_activa(); 
        $fConfig['mode_report'] = FALSE;
    }else{ 
        $fConfig = $arrConfig;
    }
    $obj->setNombreEmpresa($fConfig['razon_social']);
    $obj->setDireccion($fConfig['domicilio_fiscal']); //var_dump($fConfig['domicilio_fiscal']); exit();
    $obj->setImagenCab('assets/img/dinamic/empresa/'.$fConfig['nombre_logo']);
    $obj->setTitulo($titulo);
    $obj->setTituloAbr($tituloAbr);
    $obj->setEstado($arrConfig['estado']);
    $obj->setModeReport($fConfig['mode_report']);
    return true;

}
function textIntoCols($strOriginal,$noCols,$pdf,$iMaxCharRow)
{
    $iAlturaRow = 4; //Altura entre renglones
    $iMaxCharRow = $iMaxCharRow;
    //$iMaxCharRow = 20; //Número máximo de caracteres por renglón 
    $iSizeMultiCell = $iMaxCharRow / $noCols; //Tamaño ancho para la columna
    $iTotalCharMax = 9957; //Número máximo de caracteres por página
    $iCharPerCol = $iTotalCharMax / $noCols; //Caracteres por Columna
    $iCharPerCol = $iCharPerCol - 290; //Ajustamos el tamaño aproximado real del número de caracteres por columna
    $iLenghtStrOriginal = strlen($strOriginal); //Tamaño de la cadena original
    $iPosStr = 0; // Variable de la posición para la extracción de la cadena.
    // get current X and Y
    $start_x = $pdf->GetX(); //Posición Actual eje X
    $start_y = $pdf->GetY(); //Posición Actual eje Y
    $cont = 0;
    while($iLenghtStrOriginal > $iPosStr) // Mientras la posición sea menor al tamaño total de la cadena entonces imprime
    {
    $strCur = substr($strOriginal,$iPosStr,$iCharPerCol);//Obtener la cadena actual a pintar
    if($cont != 0) //Evaluamos que no sea la primera columna
    {
    // seteamos a X y Y, siendo el nuevo valor para X
    // el largo de la multicelda por el número de la columna actual,
    // más 10 que sumamos de separación entre multiceldas
    $pdf->SetXY(($iSizeMultiCell*$cont)+10,$start_y); //Calculamos donde iniciará la siguiente columna
    }
    $pdf->MultiCell($iSizeMultiCell,$iAlturaRow,$strCur); //Pintamos la multicelda actual
    $iPosStr = $iPosStr + $iCharPerCol; //Posicion actual de inicio para extracción de la cadena
    $cont++; //Para el control de las columnas
    }   
    return $pdf;
}