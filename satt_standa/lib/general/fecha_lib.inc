<?php

class Fecha

{

    //Atributos

    var $ano,

       $mes,

       $dia,

       $hora,

       $minutos,

       $segundos,

       $aplsbm;





    //Métodos

    function __construct ($an = 0, $me = 0, $di = 0, $ho = 0, $mi = 0, $se = 0)

    {

        $this -> ano = $an;

        $this -> mes = $me;

        $this -> dia = $di;

        $this -> hora = $ho;

        $this -> minutos = $mi;

        $this -> segundos = $se;

        $this -> aplsbm = "";

    }

    function aplicaSubmit($aplsbm)
    {
        $this -> aplsbm = $aplsbm;
    }





    function fec_comp_mysql($fecha_mysql)

    {

        $primero = explode("-", $fecha);

        $segundo = explode(" ", $primero[2]);

        $tercero = explode(":",$segundo[1]);

        $this -> ano = $primero[0];

        $this -> mes = $primero[1];

       $this -> dia = $segundo[0];

       $this -> hora = $tercero[0];

        $this -> minutos = $tercero[1];

        $this -> segundos = $tercero[2];

    }





    function fec_mysql($fecha)

    {

       $primero = explode("-", $fecha);

        $segundo = explode(" ", $primero[2]);

        $this -> ano = $primero[0];

        $this -> mes = $primero[1];

        $this -> dia = $segundo[0];

    }





    function hora_mysql($fecha)

    {

        $primero = explode("-", $fecha);

        $segundo = explode(" ", $primero[2]);

        $tercero = explode(":",$segundo[1]);

        $this -> hora = $tercero[0];

        $this -> minutos = $tercero[1];

        $this -> segundos = $tercero[2];

    }





    function formato_mysql()

    {

        $fecha_mysql = $this -> ano."-".$this -> mes."-".$this -> dia." ".$this -> hora.":".$this -> minutos.":".$this -> segundos;

        return $fecha_mysql;

    }



    function formato_espanol()

    {

        $fecha_espanol = strftime("%d de %B de %Y %I:%M %p",mktime($this -> hora, $this -> minutos, $this -> segundos, $this -> mes, $this -> dia, $this -> ano));

        return $fecha_espanol;

    }



    function formato_espanol_fecha()

    {

        $fecha_espanol = strftime("%d de %B de %Y",mktime($this -> hora, $this -> minutos, $this -> segundos, $this -> mes, $this -> dia, $this -> ano));

        return $fecha_espanol;

    }

    function formato_fecha_corta()

    {

        $fecha_espanol = strftime("%d - %b - %y",mktime($this -> hora, $this -> minutos, $this -> segundos, $this -> mes, $this -> dia, $this -> ano));

        return $fecha_espanol;

    }

    function pedir_fecha_ant ($ano,$mes,$dia,$acomienzo,$fechabd)

    {

         // $fecha =date("Y-m-d H:i:s");

         // $fecha=$fechabd;

           $primero = explode("-", $fechabd);

           $segundo = explode("-", $primero[2]);

           $tercero = explode("-",$segundo[1]);



           echo $primero[0];



          $ano_a = $primero[0];

          $mes_a = $segundo[0];

          $dia_a = $tercero[0];







          echo "<td align=\"right\" class=\"etiqueta\"><b>Dia:</b></td><td class=\"celda\"><select name=\"$dia\">\n";

                  echo "<option value=\"$dia_a\">$dia_a</option>\n";

          for ($i = 1; $i <= 31; $i++)

            {

               if($i < 10)

                  echo "<option value=\"0$i\">$i</option>\n";

               else

                  echo "<option value=\"$i\">$i</option>\n";

             }

           echo "</select></td>\n";



           echo "<td align=\"right\" class=\"etiqueta\"><b>Mes:</b></td><td class=\"celda\"><select name=\"$mes\">\n";

               $mes_actual = strftime("%B", mktime(0, 0, 0, $mes_a+1, 0, 0));

               echo "<option value=\"$mes_a\">$mes_actual</option>\n";

           for ($i = 1; $i <= 12; $i++)

             {

                echo "<option value=\"";

                if($i < 10)

                  {

                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                      echo "0$i";

                   }

                else

                   {

                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                      echo "$i";

                   }

                echo "\">$mes</option>\n";

                   }

                echo "</select></td>\n";



                echo "<td align=\"right\" class=\"etiqueta\"><b>Año:</b></td><td class=\"celda\"><select name=\"$ano\">\n";

                echo "<option value=\"$ano_a\">$ano_a</option>\n";

           for ($i = 0; $i <= 105; $i++)

               {

                 $year=$acomienzo+$i;

                 echo "<option value=\"$year\">$year</option>\n";

               }

               echo "</select></td>\n";

     }

   function pedir_fecha1 ($ano,$mes,$dia)

    {

          $fecha =date("Y-m-d H:i:s");

          $primero = explode("-", $fecha);

          $segundo = explode(" ", $primero[2]);

          $tercero = explode(":",$segundo[1]);

          $ano_a = $primero[0];

          $mes_a = $primero[1];

          $dia_a = $segundo[0];





          echo "<td align=\"right\" class=\"etiqueta\">Dia&nbsp;<select name=\"$dia\">";

                  echo "<option value=\"-\">-</option>\n";

          for ($i = 1; $i <= 31; $i++)

            {



               if($i < 10)

                  echo "<option value=\"0$i\">$i</option>\n";

               else

                  echo "<option value=\"$i\">$i</option>\n";

             }

           echo "</select>";



           echo "&nbsp;Mes&nbsp;<select name=\"$mes\">";

               $mes_actual = strftime("%B", mktime(0, 0, 0, $mes_a+1, 0, 0));

                echo "<option value=\"-\">-</option>\n";

           for ($i = 1; $i <= 12; $i++)

             {

                echo "<option value=\"";

                if($i < 10)

                  {

                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                      echo "0$i";

                   }

                else

                   {

                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                      echo "$i";

                   }

                echo "\">$mes</option>\n";

                   }

                echo "</select>";



                echo "&nbsp;Año&nbsp;<select name=\"$ano\">";

                echo "<option value=\"-\">-</option>\n";

           for ($i = 2; $i <= 9; $i++)

               {

                 echo "<option value=\"200$i\">200$i</option>\n";

               }

               echo "</select>";

               echo "</td>";

     }



             function pedir_ano_mes($ano,$mes)

    {

            $fecha =date("Y-m-d H:i:s");

        $primero = explode("-", $fecha);

        $segundo = explode(" ", $primero[2]);

        $tercero = explode(":",$segundo[1]);

        $ano_a = $primero[0];

        $mes_a = $primero[1];

        $dia_a = $segundo[0];



           echo "<td align=\"right\" class=\"etiqueta\">Mes:</td><td class=\"celda\"><select name=\"$mes\">\n";

               $mes_actual = strftime("%B", mktime(0, 0, 0, $mes_a+1, 0, 0));

               echo "<option value=\"$mes_a\">$mes_actual</option>\n";

           for ($i = 1; $i <= 12; $i++)

             {

                echo "<option value=\"";

                if($i < 10)

                  {

                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                      echo "0$i";

                   }

                else

                   {

                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                      echo "$i";

                   }

                echo "\">$mes</option>\n";

                   }

                echo "</select></td>\n";



                echo "<td align=\"right\" class=\"etiqueta\">Año:</td><td class=\"celda\"><select name=\"$ano\">\n";

                echo "<option value=\"$ano_a\">$ano_a</option>\n";

           for ($i = 2; $i <= 9; $i++)

               {

                 echo "<option value=\"200$i\">200$i</option>\n";

               }

               echo "</select></td>\n";

     }

    function pedir_fecha_adi ($ano,$mes,$dia,$fecha)

    {

      $primero = explode("-", $fecha);

      $segundo = explode(" ", $primero[2]);

      $tercero = explode(":",$segundo[1]);

      $ano_a = $primero[0];

      $mes_a = $primero[1];

      $dia_a = $segundo[0];



      echo "<td align=\"right\" class=\"etiqueta\">Dia:</td><td class=\"celda\"><select name=\"$dia\">\n";

              echo "<option value=\"$dia_a\">$dia_a</option>\n";

      for ($i = 1; $i <= 31; $i++)

        {

           if($i < 10)

              echo "<option value=\"0$i\">$i</option>\n";

           else

              echo "<option value=\"$i\">$i</option>\n";

         }

       echo "</select></td>\n";



       echo "<td align=\"right\" class=\"etiqueta\">Mes:</td><td class=\"celda\"><select name=\"$mes\">\n";

           $mes_actual = strftime("%B", mktime(0, 0, 0, $mes_a+1, 0, 0));

           echo "<option value=\"$mes_a\">$mes_actual</option>\n";

       for ($i = 1; $i <= 12; $i++)

         {

            echo "<option value=\"";

            if($i < 10)

              {

                  $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                  echo "0$i";

               }

            else

               {

                  $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));

                  echo "$i";

               }

            echo "\">$mes</option>\n";

               }

            echo "</select></td>\n";



            echo "<td align=\"right\" class=\"etiqueta\">Año:</td><td class=\"celda\"><select name=\"$ano\">\n";

            echo "<option value=\"$ano_a\">$ano_a</option>\n";

       for ($i = 2; $i <= 9; $i++)

           {

             echo "<option value=\"200$i\">200$i</option>\n";

           }

           echo "</select></td>\n";

     }

    function pedir_fecha ($ano,$mes,$dia,$clase="etiqueta")
    {
          $fecha =date("Y-m-d H:i:s");
          $primero = explode("-", $fecha);
          $segundo = explode(" ", $primero[2]);
          $tercero = explode(":",$segundo[1]);

          if($this -> ano == NULL)
           $this -> ano = $primero[0];
          if($this -> mes == NULL)
           $this -> mes = $primero[1];
          if($this -> dia == NULL)
           $this -> dia = $segundo[0];

          $textsbt = "";
          if($this -> aplsbm != "")
           $textsbt = "onChange=\"".$this -> aplsbm.".submit()\"";

          //incluye la libreria del query by example
          echo "<td align=\"right\" class=\"$clase\">Dia:</td><td class=\"$clase\"><select name=\"$dia\" ".$textsbt.">\n";
                  echo "<option value=\"".$this -> dia."\">".$this -> dia."</option>\n";
          for ($i = 1; $i <= 31; $i++)
            {
               if($i < 10)
                  echo "<option value=\"0$i\">0$i</option>\n";
               else
                  echo "<option value=\"$i\">$i</option>\n";
             }
           echo "</select></td>\n";

           echo "<td align=\"right\" class=\"$clase\">Mes:</td><td class=\"$clase\"><select name=\"$mes\" ".$textsbt.">\n";
               $mes_actual = strftime("%B", mktime(0, 0, 0, $this -> mes +1, 0, 0));
               echo "<option value=\"".$this -> mes."\">$mes_actual</option>\n";
           for ($i = 1; $i <= 12; $i++)
             {
                echo "<option value=\"";
                if($i < 10)
                  {
                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));
                      echo "0$i";
                   }
                else
                   {
                      $mes = strftime("%B", mktime(0, 0, 0, $i+1, 0, 0));
                      echo "$i";
                   }
                echo "\">$mes</option>\n";
                   }
                echo "</select></td>\n";

                echo "<td align=\"right\" class=\"$clase\">Año:</td><td class=\"$clase\"><select name=\"$ano\" ".$textsbt.">\n";
                echo "<option value=\"".$this -> ano."\">".$this -> ano."</option>\n";
           for ($i = 2; $i <= 9; $i++)
               {
                 echo "<option value=\"200$i\">200$i</option>\n";
               }
               echo "</select></td>\n";
     }


    function pedir_hora ($hora,$minuto,$clase="etiqueta")
    {
          $fecha =date("Y-m-d H:i:s");
          $primero = explode("-", $fecha);
          $segundo = explode(" ", $primero[2]);
          $tercero = explode(":",$segundo[1]);

          if($this -> hora == NULL)
           $this -> hora = $tercero[0];
          if($this -> minutos == NULL)
           $this -> minutos = $tercero[1];

          $textsbt = "";
          if($this -> aplsbm != "")
           $textsbt = "onChange=\"".$this -> aplsbm.".submit()\"";

          echo "<td align=\"right\" class=\"$clase\">Hora:</td><td class=\"$clase\"><select name=\"$hora\" ".$textsbt.">\n";
          echo "<option value=\"".$this -> hora."\">".$this -> hora."</option>\n";
          for ($i = 0; $i < 24; $i++)
          {
               if($i < 10)
                  echo "<option value=\"0$i\">0$i</option>\n";
               else
                  echo "<option value=\"$i\">$i</option>\n";
          }
          echo "</select></td>\n";

          echo "<td align=\"right\" class=\"$clase\">Minutos:</td><td class=\"$clase\"><select name=\"$minuto\" ".$textsbt.">\n";
          echo "<option value=\"".$this -> minutos."\">".$this -> minutos."</option>\n";
          for ($i = 0; $i <= 59; $i++)
          {
               if($i < 10)
                  echo "<option value=\"0$i\">0$i</option>\n";
               else
                  echo "<option value=\"$i\">$i</option>\n";
          }

               echo "</select></td>\n";
     }

}

?>