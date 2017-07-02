<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class pdf {
    
    function pdf()
    {
        $CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }
 
    function load($modo='C',$orientacion='A4-P',$sizeFont='10')
    {
        include_once APPPATH.'/third_party/mpdf60/mpdf.php';
        return new mPDF($modo,$orientacion,$sizeFont,$familyFont);
    }
}