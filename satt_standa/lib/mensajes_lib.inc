<?php

class mensajes{
        var $tit,        //titulo de la tabla
            $mens,       //mensaje
            $tipo;  //tipo de mensaje que se va a mostrar


        function mensajes()
        {

        }

        function advert($tit = "ADVERTENCIA", $mens)
        {
                $this -> tit = $tit;
                $this -> mens= $mens;

      echo '
      <table align="center" width="420" border="1" cellpadding="0" cellspacing="1" bgcolor="#257038">
      <tr>
      <td valign="top">
      <table align="center" width="420" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr>
      <td height="13" colspan="2" align="left" valign="top" bgcolor="#257038" class="Estilo1 Estilo2"><font color=#FFFFFF><b>'.$this ->tit.'</b></font></td>
      </tr>
      <tr>
      <td width="99" height="106" align="center" valign="middle"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/advertencia_grande.jpg"></td>
      <td width="306" align="center" valign="middle" class="Estilo6">'.$this -> mens.'
      </td>
      </tr>
      </table>
      </td>
      </tr>
      </table>';
         }

        function correcto($tit = "TRANSACCION EXITOSA", $mens)
        {
                $this -> tit = $tit;
                $this -> mens= $mens;

          echo '
      <table align="center" width="420" border="1" cellpadding="0" cellspacing="1" bgcolor="#257038">
      <tr>
      <td valign="top">
      <table align="center" width="420" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr>
      <td height="13" colspan="2" align="left" valign="top" bgcolor="#257038" class="Estilo1 Estilo2"><font color=#FFFFFF><b>'.$this ->tit.'</b></font></td>
      </tr>
      <tr>
      <td width="99" height="106" align="center" valign="middle"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/ok.gif"></td>
      <td width="306" align="center" valign="middle" class="Estilo6">'.$this -> mens.'
      </td>
      </tr>
      </table>
      </td>
      </tr>
      </table>';
         }

        function error($tit = "MENSAJE DE ERROR", $mens)
        {
                $this -> tit = $tit;
                $this -> mens= $mens;

      echo '
      <table align="center" width="420" border="1" cellpadding="0" cellspacing="1" bgcolor="#257038">
      <tr>
      <td valign="top">
      <table align="center" width="420" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr>
      <td height="13" colspan="2" align="left" valign="top" bgcolor="#257038" class="Estilo1 Estilo2"><font color=#FFFFFF><b>'.$this ->tit.'</b></font></td>
      </tr>
      <tr>
      <td width="99" height="106" align="center" valign="middle"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/alerta.gif"></td>
      <td width="306" align="center" valign="middle" class="Estilo6">'.$this -> mens.'
      </td>
      </tr>
      </table>
      </td>
      </tr>
      </table>';
         }

         function correcto2($tit = "TRANSACCION EXITOSA", $mens){
                $this -> tit = $tit;
                $this -> mens= $mens;
                $src = "../".DIR_APLICA_CENTRAL."/imagenes/ok.png";
          echo "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>
                  <div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>
                    <label>$this->tit</label>
                    <div style='background-color:#FFFFFF'><br><img src='$src'></div>
                    <div style='background-color:#FFFFFF; border-bottom-right-radius: 6px;border-bottom-left-radius: 6px;'>
                      <div style='width:97%'>
                        <font color='#000'>$this->mens</font>
                      </div>
                    </div>
                  </div>
                </div>";
              /*'<table align="center" width="420" border="1" bgcolor="#257038">
              <tr>
              <td valign="top">
              <table align="center" width="420" border="0" bgcolor="#FFFFFF">
              <tr>
              <td height="13" colspan="2" align="left" valign="top" bgcolor="#3A8104" class="Estilo1 Estilo2"><font color=#FFFFFF><b>'.$this ->tit.'</b></font></td>
              </tr>
              <tr>
              <td width="99" height="106" align="center" valign="middle"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/ok.png"></td>
              <td width="306" align="center" valign="middle" class="Estilo6">'.$this -> mens.'
              </td>
              </tr>
              </table>
              </td>
              </tr>
              </table>';*/
         }

        function error2($tit = "MENSAJE DE ERROR", $mens){
          $this -> tit = $tit;
                $this -> mens= $mens;
                $src = "../".DIR_APLICA_CENTRAL."/imagenes/bad.png";
          echo "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>
                  <div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>
                    <label>$this->tit</label>
                    <div style='background-color:#FFFFFF'><br><img src='$src'></div>
                    <div style='background-color:#FFFFFF; border-bottom-right-radius: 6px;border-bottom-left-radius: 6px;'>
                      <div style='width:97%'>
                        <font color='#000'>$this->mens</font>
                      </div>
                    </div>
                  </div>
                </div>";
         }
}
?>
