<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function convertImageToBase64($urlImage)
{
    $type = pathinfo($urlImage, PATHINFO_EXTENSION);
    $data = file_get_contents($urlImage);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    return $base64;
}
function subir_imagen_Base64($Base64Img, $ruta, $nombre_foto_con_extension)
{
    list(, $Base64Img) = explode(';', $Base64Img);
    list(, $Base64Img) = explode(',', $Base64Img);
    //Decodificamos $Base64Img codificada en base64.
    $Base64Img = base64_decode($Base64Img);
    $file = $ruta . $nombre_foto_con_extension;
    file_put_contents($file, $Base64Img);
}
function convertBase64ToImage($base64_string, $output_file)
{
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' );

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp );

    return $output_file;
}

function subir_fichero($directorio_destino, $nombre_fichero, $nombreArchivo = FALSE)
{
    //if($nombre_fichero){
        $tmp_name = @$_FILES[$nombre_fichero]['tmp_name']; //print_r($tmp_name);
        //var_dump($_FILES['archivo']['error']); exit();
        //var_dump($_FILES[$nombre_fichero]); exit();
        //si hemos enviado un directorio que existe realmente y hemos subido el archivo
        // $path = $_FILES['image']['name'];
        $extension = pathinfo($_FILES[$nombre_fichero]['name'], PATHINFO_EXTENSION);
        if (is_dir($directorio_destino) && is_uploaded_file($tmp_name))
        {
            $img_file = $_FILES[$nombre_fichero]['name'];
            if( !empty($nombreArchivo) ){
                $img_file = $nombreArchivo/*.'.'.$extension*/;
            }
            $img_type = $_FILES[$nombre_fichero]['type'];
            //echo 1;
            // Si se trata de una imagen
            //if (((strpos($img_type, "gif") || strpos($img_type, "jpeg") || strpos($img_type, "jpg")) || strpos($img_type, "png")))
            //{
                //¿Tenemos permisos para subir la imágen?
                //echo 2;
                if (move_uploaded_file($tmp_name, $directorio_destino . '/' . $img_file))
                {
                    return true;
                }
            //}
        }
    //}
    //Si llegamos hasta aquí es que algo ha fallado
    return false;
}
function subir_fichero_solo_PDF($directorio_destino, $nombre_fichero, $fechaUnique)
{

    //if($nombre_fichero){
        $tmp_name = @$_FILES[$nombre_fichero]['tmp_name'];
        //var_dump($tmp_name);
        //si hemos enviado un directorio que existe realmente y hemos subido el archivo
        if (is_dir($directorio_destino) && is_uploaded_file($tmp_name))
        {
            $img_file = $_FILES[$nombre_fichero]['name'];
            $img_type = $_FILES[$nombre_fichero]['type'];
            //echo 1;
            // Si se trata de una imagen
            if ( strpos($img_type, "pdf") )
            {
                //¿Tenemos permisos para subir la imágen?
                //echo 2;
                if (move_uploaded_file($tmp_name, $directorio_destino . '/' . $img_file.$fechaUnique))
                {
                    return true;
                }
            }
        }
    //}
    //Si llegamos hasta aquí es que algo ha fallado
    return false;
}
function crearVistasPrevias150($img,$dir, $width = '150', $height = '150')
    {
                //unset al arreglo config por si existe en memoria
        unset($config);
        $config['image_library']  = 'GD2';
        $config['source_image']   = './'.$dir.'/'.$img;
                //se debe de crear la carpeta thumb dentro de nuestro directorio $dir
        $config['new_image']      = './'.$dir.'/thumbs_150/'.$img;
        $config['create_thumb']   = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']          = $width;
        $config['height']         = $height;

                //verificamos que no este bacio nuestro archivo a subir
        if(!empty($config['source_image']))
        {
                        //cargamos desde CI  a nuestra libreria image_lib
            $ci =& get_instance();
            $ci->load->library('image_lib', $config);
                        // iniciamos image_lib con el contenido de $config
            $ci->image_lib->initialize($config);

                        //le hacemos resize a nuestra imagen
            if (!$ci->image_lib->resize())
            {
                $error = array('error'=>$ci->image_lib->display_errors());
                return $error;
            }
            else
            {
                return TRUE;
            }
                        //limpeamos el contenido de image_lib esto para crear varias thumbs
            $ci->image_lib->clear();
        }
    }
function crearVistasPreviasCompletas($img,$dir, $width, $height)
    {
                //unset al arreglo config por si existe en memoria
        unset($config);
        $config['image_library']  = 'GD2';
        $config['source_image']   = './'.$dir.'/'.$img;
                //se debe de crear la carpeta thumb dentro de nuestro directorio $dir
        $config['new_image']      = './'.$dir.'/thumbs_completos/'.$img;
        $config['create_thumb']   = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']          = $width;
        $config['height']         = $height;

                //verificamos que no este bacio nuestro archivo a subir
        if(!empty($config['source_image']))
        {
                        //cargamos desde CI  a nuestra libreria image_lib
            $ci =& get_instance();
            $ci->load->library('image_lib', $config);
                        // iniciamos image_lib con el contenido de $config
            $ci->image_lib->initialize($config);

                        //le hacemos resize a nuestra imagen
            if (!$ci->image_lib->resize())
            {
                $error = array('error'=>$ci->image_lib->display_errors());
                return $error;
            }
            else
            {
                return TRUE;
            }
                        //limpeamos el contenido de image_lib esto para crear varias thumbs
            $ci->image_lib->clear();
        }
    }


