<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    // Incluimos el archivo fpdf
    require_once APPPATH."/third_party/fpdf/fpdf.php";

    //Extendemos la clase Pdf de la clase fpdf para que herede todas sus variables y funciones. . 
    class Fpdfext extends FPDF { 
      public function __construct($orientation='P', $unit='mm', $size='A4') {
        parent::__construct($orientation, $unit, $size);
      }
      var $widths;
      var $aligns;
      var $angle=0;
      var $tituloAbr;
      var $imagenCab;

      public function setModeReport($modeReport){
        $this->modeReport = $modeReport;
      }
      public function getModeReport()
      {
        return $this->modeReport;
      }
      public function setEstado($estado){
        $this->estado = $estado;
      }
      public function getEstado()
      {
        return $this->estado;
      }
      public function setTituloAbr($tituloAbr){
        $this->tituloAbr = $tituloAbr;
      }
      public function getTituloAbr()
      {
        return $this->tituloAbr;
      }
      public function setTitulo($title){
        $this->title = $title;
      }
      public function getTitulo()
      {
        return $this->title;
      }
      public function setImagenCab($param){
        $this->imagenCab = $param;
      }
      public function getImagenCab()
      {
        return $this->imagenCab;
      }
      public function setNombreEmpresa($param){
        $this->nombreEmpresa = $param;
      }
      public function getNombreEmpresa()
      {
        return $this->nombreEmpresa;
      }
      public function setDireccion($param){
        $this->direccion = $param;
      }
      public function getDireccion()
      {
        return $this->direccion;
      }

      public function SetWidths($w)
      {
          //Set the array of column widths
          $this->widths=$w;
      }
      public function GetWidths()
      {
          //Set the array of column widths
          return $this->widths;
      }
      public function SetAligns($a)
      {
          //Set the array of column alignments
          $this->aligns=$a;
      }
      public function GetAligns()
      {
          //Set the array of column widths
          return $this->aligns;
      }
      public function setNombreEmpresaFarm($param){
        $this->nombreEmpresaFarm = $param;
      }
      public function setRucEmpresaFarm($param){
        $this->rucEmpresaFarm = $param;
      }
      public function setIdEmpresaFarm($param){
        $this->idEmpresaFarm = $param;
      }
      public function Row($data,$fill=FALSE,$border=0,$arrBolds=FALSE,$heigthCell=FALSE,$arrTextColor=FALSE,$arrBGColor=FALSE,$arrImage=FALSE,$bug=FALSE,$arrFontSize=FALSE)
      {
          //Calculate the height of the row
          //var_dump($heigthCell); exit();
          // if(empty($fontSize)){
          //   $fontSize = 7;
          // }
          if( empty($heigthCell) ){
            $heigthCell = 5;
          }
          $nb=0;
          for($i=0;$i<count($data);$i++)
              $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
          $h=($heigthCell)*$nb;
          //Issue a page break first if needed
          $this->CheckPageBreak($h);
          //Draw the cells of the row
          for($i=0;$i<count($data);$i++)
          {
              $w=$this->widths[$i];
              $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
              //Save the current position
              $x=$this->GetX();
              $y=$this->GetY();
              //Draw the border
              // $this->Rect($x,$y,$w,$h);
              //Print the text
              // if( $arrBolds ){
              //   if( $arrBolds[$i] == 'B'){
              //     $this->SetFont('Arial','B',$fontSize+1);
              //   }else{
              //     $this->SetFont('Arial','',$fontSize);
              //   }
              // }
              if( $arrTextColor ){
                if( $arrTextColor[$i] == 'red'){
                  $this->SetTextColor(225,22,22);
                }elseif( $arrTextColor[$i] == 'green'){
                  $this->SetTextColor(12,162,10);
                }else{
                  $this->SetTextColor($arrTextColor[$i]['r'],$arrTextColor[$i]['g'],$arrTextColor[$i]['b']);
                }
              }
              if( $arrFontSize ){
                $this->SetFont($arrFontSize[$i]['family'],$arrFontSize[$i]['weight'],$arrFontSize[$i]['size']);
              }
              if( $arrBGColor ){
                $fill=TRUE;
                if( $arrBGColor[$i] == 'p1'){
                  $this->SetFillColor(130);
                }elseif( $arrBGColor[$i] == 'p2'){
                  $this->SetFillColor(160);
                }elseif( $arrBGColor[$i] == 'p3'){
                  $this->SetFillColor(190);
                }elseif( $arrBGColor[$i] == 'p4'){
                  $this->SetFillColor(220);
                }elseif( $arrBGColor[$i] == 'p5'){
                  $this->SetFillColor(240);
                }else{
                  // $fill=FALSE;
                  $this->SetFillColor($arrBGColor[$i]['r'],$arrBGColor[$i]['g'],$arrBGColor[$i]['b']);
                }
              }
              $textoCell = $data[$i];
              if( !empty($arrImage[$i]) ){
                // var_dump($textoCell); exit();
                // $textoCell = $this->Image('assets/img/dinamic/empresa/'.$textoCell,2,2,50);
                //$
                if( empty($textoCell) ){
                  $textoCell = 'noimage.jpg';
                }
                $textoCell = $this->Image('assets/img/dinamic/empleado/'.$textoCell,($x + 6),($y + 1),10,10);
                //$fill= FALSE;
              }
              // if( empty($heigthCell) ){
              //   $heigthCell = 5;
              // }
              
              $this->MultiCell($w,$heigthCell,$textoCell,$border,$a,$fill);
              //$this->SetFont('Arial','',$fontSize);
              //Put the position to the right of the cell
              $this->SetXY($x+$w,($y));
              // var_dump($i);
          }// exit();
          //Go to the next line
          if($bug){
            $h = $heigthCell;
          }
          $this->Ln($h);
      }
      public function RowSmall($data,$fill=FALSE,$border=0,$arrBolds=FALSE,$heigthCell=5,$arrTextColor=FALSE,$arrBGColor=FALSE)
      {
          //Calculate the height of the row
          $nb=0;
          for($i=0;$i<count($data);$i++)
              $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
          $h=5*$nb;
          //Issue a page break first if needed
          $this->CheckPageBreak($h);
          //Draw the cells of the row
          for($i=0;$i<count($data);$i++)
          {
              $w=$this->widths[$i];
              $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
              //Save the current position
              $x=$this->GetX();
              $y=$this->GetY();
              //Draw the border
              // $this->Rect($x,$y,$w,$h);
              //Print the text
              if( $arrBolds ){
                if( $arrBolds[$i] == 'B'){
                  $this->SetFont('Arial','B',7);
                }else{
                  $this->SetFont('Arial','',6);
                }
              }
              if( $arrTextColor ){
                if( $arrTextColor[$i] == 'red'){
                  $this->SetTextColor(225,22,22);
                }elseif( $arrTextColor[$i] == 'green'){
                  $this->SetTextColor(12,162,10);
                }else{
                  $this->SetTextColor(0);
                }
              }
              if( $arrBGColor ){
                if( $arrBGColor[$i] == 'p1'){
                  $this->SetFillColor(130);
                }elseif( $arrBGColor[$i] == 'p2'){
                  $this->SetFillColor(160);
                }elseif( $arrBGColor[$i] == 'p3'){
                  $this->SetFillColor(190);
                }elseif( $arrBGColor[$i] == 'p4'){
                  $this->SetFillColor(220);
                }elseif( $arrBGColor[$i] == 'p5'){
                  $this->SetFillColor(240);
                }else{
                  $this->SetFillColor(255);
                }
              }
              $this->MultiCell($w,$heigthCell,utf8_decode($data[$i]),$border,$a,$fill);


              $this->SetFont('Arial','',6);
              //Put the position to the right of the cell
              $this->SetXY($x+$w,$y);
          }
          //Go to the next line
          $this->Ln($h);
      }
      public function TextArea($data,$fill=FALSE,$border=0,$arrBolds=FALSE,$heigthCell=5, $heightTextArea=20) //para una sola celda
      {
          //Calculate the height of the row
          $nb=0;
          for($i=0;$i<count($data);$i++)
              $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
          $h=5*$nb;
          // calcular el alto del textarea
          if($h < $heightTextArea){
            $h = $heightTextArea;
          }

          //Issue a page break first if needed
          $this->CheckPageBreak($h);
          //Draw the cells of the row
          for($i=0;$i<count($data);$i++)
          {
              $w=$this->widths[$i];
              $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
              //Save the current position
              $x=$this->GetX();
              $y=$this->GetY();
              //Draw the border
              $this->Rect($x,$y,$w,$h);
              //Print the text
              if( $arrBolds ){
                if( $arrBolds[$i] == 'B'){
                  $this->SetFont('Arial','B',8);
                }else{
                  $this->SetFont('Arial','',7);
                }
              }
              $this->MultiCell($w,$heigthCell,$data[$i],$border,$a,$fill);
              $this->SetFont('Arial','',7);
              //Put the position to the right of the cell
              $this->SetXY($x+$w,$y);
          }
          //Go to the next line
          //$this->Ln($h);
      }

      public function CheckPageBreak($h)
      {
          //If the height h would cause an overflow, add a new page immediately
          if($this->GetY()+$h>$this->PageBreakTrigger){
            $this->AddPage($this->CurOrientation);
          }
              
      }

      public function NbLines($w,$txt)
      {
          //Computes the number of lines a MultiCell of width w will take
          $cw=&$this->CurrentFont['cw'];
          if($w==0)
              $w=$this->w-$this->rMargin-$this->x;
          $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
          $s=str_replace("\r",'',$txt);
          $nb=strlen($s);
          if($nb>0 and $s[$nb-1]=="\n")
              $nb--;
          $sep=-1;
          $i=0;
          $j=0;
          $l=0;
          $nl=1;
          while($i<$nb)
          {
              $c=$s[$i];
              if($c=="\n")
              {
                  $i++;
                  $sep=-1;
                  $j=$i;
                  $l=0;
                  $nl++;
                  continue;
              }
              if($c==' ')
                  $sep=$i;
              $l+=$cw[$c];
              if($l>$wmax)
              {
                  if($sep==-1)
                  {
                      if($i==$j)
                          $i++;
                  }
                  else
                      $i=$sep+1;
                  $sep=-1;
                  $j=$i;
                  $l=0;
                  $nl++;
              }
              else
                  $i++;
          }
          return $nl;
      }
      public function Header(){
        // var_dump( $this->tituloAbr ); exit(); //SetMargins 
        if( $this->tituloAbr == 'VEN-COMPR' ){
          //$this->SetAutoPageBreak(TRUE,25); 
          // $this->SetFont('Arial','',6);
          // $this->MultiCell(120,6,'USUARIO: ');
          return; 
        }

        //$this->SetAutoPageBreak(TRUE,25);
        $ci2 =& get_instance(); 
        $this->SetFont('Arial','',6);
        $this->SetXY(-70,0);
        $this->MultiCell(120,6,'USUARIO: '.strtoupper($ci2->sessionFactur['username']).utf8_decode('    /   FECHA DE IMPRESIÓN: ').date('Y-m-d H:i:s')); 
        $this->Image($this->getImagenCab(),4,4,50); 
        if( $this->PageNo() > 1 ){
          $this->SetY(26);
        }
        
      }
       // El pie del pdf
      public function Footer(){
        $ci2 =& get_instance();
        if( $this->PageNo() > 1 ){
          // $this->SetY(0);
        }
      }
      function RotatedText($x, $y, $txt, $angle){
          //Text rotated around its origin
          $this->Rotate($angle,$x,$y);
          $this->Text($x,$y,$txt);
          $this->Rotate(0);
      }
      function Rotate($angle,$x=-1,$y=-1)
      {
          if($x==-1)
              $x=$this->x;
          if($y==-1)
              $y=$this->y;
          if($this->angle!=0)
              $this->_out('Q');
          $this->angle=$angle;
          if($angle!=0)
          {
              $angle*=M_PI/180;
              $c=cos($angle);
              $s=sin($angle);
              $cx=$x*$this->k;
              $cy=($this->h-$y)*$this->k;
              $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
          }
      }

      function _endpage()
      {
          if($this->angle!=0)
          {
              $this->angle=0;
              $this->_out('Q');
          }
          parent::_endpage();
      }

    }
?>