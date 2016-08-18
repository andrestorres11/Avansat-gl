<?php

class en_letras
{
  function cientos($valor)
  {
        if(!$valor)return;
        if($valor == '100')
        $cien = "CIEN";
        else
        $cien = "CIENTO";
        $unidades=array('UN','DOS','TRES','CUATRO','CINCO','SEIS','SIETE','OCHO','NUEVE');
        $decenas=array('ONCE','DOCE','TRECE','CATORCE','QUINCE','DIECISEIS','DIECISIETE','DIECIOCHO','DIECINUEVE');
        $diez=array('DIEZ','VEINTE','TREINTA','CUARENTA','CINCUENTA','SESENTA','SETENTA','OCHENTA','NOVENTA');
        $cientos=array($cien,'DOSCIENTOS','TRESCIENTOS','CUATROCIENTOS','QUINIENTOS','SEISCIENTOS','SETECIENTOS','OCHOCIENTOS','NOVECIENTOS');

        $cadena='';
        $j=strlen($valor);
        $listo=false;
        for($i=0; $i<strlen($valor); $i++)
        {
         if($j==2)
         {
          if(strlen($valor)>2)
          {
           if($valor[0]!=0)
              $cadena = $cientos[$valor[0]-1];
           if(substr($valor,$i+1) != '')
              $cadena.= ' ';
          }//fin if
          if($valor[$i]==1)
          {
           if(($valor[$i+1]==0)&&($diez[$valor[$i]-1]))
              $cadena.=$diez[$valor[$i]-1].' Y ';
           else
           {
            $cadena.=$decenas[$valor[$i+1]-1];
            $listo=true;
           }//fin else
          }//fin if
          else if($diez[$valor[$i]-1])
          {
               $cadena.=$diez[$valor[$i]-1];
               if($valor[$i+1])
                  $cadena.= ' Y ';
          }
         }//fin if$j==2
         else if($valor[$i]!=0 && !$listo)
              $cadena.=$unidades[$valor[$i]-1];
         $j--;
        }//fin for
        return $cadena;
  }//fin function

  function a_letras($valor, $moneda = 0)
  {
      $valor = str_replace(',','',$valor);
      $valor = explode(".",$valor);
      $centavos = $valor[1];
      $valor = $valor[0];
      if($valor > 1000000)
          $miles=array('',' MIL ',' MILLONES ','MIL ',' BILLON ');
      else
          $miles=array('',' MIL ',' MILLON ',' MIL ',' BILLON ');
      $cadena='';
      if(strlen($valor)%3)
      {
        $cadena.=$this->cientos(substr($valor,0, strlen($valor)%3));
        if($valor >= 2000000000)
           $cadena.=$miles[floor(strlen($valor)/3)];
        else
           $cadena.=$miles[floor(strlen($valor)/3)];
      }//fin if
      for($i=0; $i<floor(strlen($valor)/3); $i++)
      {
       $cadena.=$this->cientos(substr($valor,strlen($valor)%3+($i*3),3));
       if(strlen($valor)%3+($i+1)*3)
          $cadena.=$miles[floor(strlen($valor)/3)-1-$i];
      }//fin for
      if(!$moneda)
          $cadena .= ' PESOS';
      if($centavos > 0)
          $cadena .= $this -> a_centavos($centavos);
      return $cadena;
      //return $cadena;
  }//fin function

  function a_centavos($valor)
  {
   $cadena = ' CON ';
   $cadena .= $this->a_letras($valor, 1);
   $cadena .= ' CENTAVOS';
   return $cadena;
  }//fin function
}//fin class

?>