<?php
require('fpdf.php');

class PDF_MC_Table extends FPDF
{
var $widths;
var $aligns;

/* ! \fn: SetWidths
 *  \brief:Obtiene el ancho de los campos
 *  \author: Alejandro Arango
 *  \date: 29/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: $w array ancho de las columnas de la tabla
 */

function SetWidths($w)
{
    $this->widths=$w;
}
/* ! \fn: SetAligns
 *  \brief: Obtiene las alineaciones de las columnas
 *  \author: Alejandro Arango       
 *  \date: 29/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: $a text Alinea el texto dependiendo de como se indique
 */
function SetAligns($a)
{
    $this->aligns=$a;
}

/* ! \fn: Row
 *  \brief: calcula las tablas generadas
 *  \author: Alejandro Arango
 *  \date: 29/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: $data array calcula la cantidad de tablas generadas
 */

function Row($data)
{
    
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
   
    $this->CheckPageBreak($h);

    
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
       
        $x=$this->GetX();
        $y=$this->GetY();
        
        $this->Rect($x,$y,$w,$h);
        
        $this->MultiCell($w,5,$data[$i],0,$a);
        
        $this->SetXY($x+$w,$y);
    }
    
    $this->Ln($h);
}

/* ! \fn: CheckPageBreak
 *  \brief: Si la h de altura causa un desbordamiento pasa a la siguiente pagina
 *  \author: Alejandro Arango
 *  \date: 29/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: $h int Si la pagina se desborda pasa a la siguiente
 */
function CheckPageBreak($h)
{
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

/* ! \fn: NbLines
 *  \brief: Calcula el numero de lineas MultiCell que con el ancho de la w
 *  \author: Andres Torres Vega
 *  \date: dd/mm/2016
 *  \date modified: dd/mm/aaaa
 *  \param: $w int calcula las margenes de la celda 
 *  \param: $txt texto calcula el tamaño del texto ingresado
 */
function NbLines($w,$txt)
{
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
}
?>