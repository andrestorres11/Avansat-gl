<?php

/*

  -------------- ----------------------------------------------------

  Clase         : Interfaz........................................

  Metodos       : Constructor:Interfaz_cis;

                  Ins_tercer();



                  Ins_vehige();

                  Ins_pc($datos);

                  Validar_interfaz();

                  Insertar;

                  Insertar_cis

  Nombre Archivo: maestra_tercer_ins.php............................

  Objetivo      : Insertar terceros .................................

                : ...................................................

  Sis.Operativo : Linux/Unix.........................................

  Fecha Creacion: 09 de Marzo de 2006................................

  Fecha Modifica: 09 de Marzo de 2006................................

                : ...................................................

  Programadores : .Leonardo Romero Castro,  lromero@intrared.net ....

                  .Elkin Javier Beleï¿½ Correa, ebeleno@intrared.net .

                : .Johan Manuel Gutierrez, jgutierrez@intrared.net

  Notas         : ...................................................

                : ...................................................

  -------------------------------------------------------------------

*/



class Interfaz



{



        var $conexion,

            $conexion_sat,

            $bd,//Nombre de base de datos SAD TRAFICO

            $cod_interf,//Array que contiene los codigos de la Interfaz

            $nom_interf,//Array que contiene los nombres de la Interaz

            $nom_basedx,//Array que contiene los nombres de las bases de datos de la interfaz

            $cant_interf,//Contiene la cantidad de interfazces que estan activas

            $usuario;//una conexion ya establecida a la base de datos



           //Metodos



        function Interfaz($bd,$co, $us)

        {

         $this -> conexion    = $co;

         $this -> usuario     = $us;

         $this -> bd          = $bd;



         $query = "SELECT cod_interf, nom_interf, nom_basedx, ind_interf

                   FROM ".$this -> bd.".tab_interf_parame

                   WHERE ind_interf = '1'

                   ";

         $consulta = new Consulta($query, $this -> conexion,"R");

         $interf   = $consulta  -> ret_matriz();



         $this -> cant_interf = sizeof($interf);

         for($i=0 ; $i < $this -> cant_interf; $i++)

         {

             $this -> cod_interf[$i][0] = $interf[$i][0];

             $this -> nom_interf[$i][0] = $interf[$i][1];

             $this -> nom_basedx[$i][0] = $interf[$i][2];

         }



        }



        //Cambia el Objeto de la Conexion

        function act_conexion($conexion_act)

        {

                $this -> conexion_sat = $this -> conexion;

                $this -> conexion = $conexion_act;

        }



        //Funciones para manipulacion de los puestos de control

        function ins_pc($bd_interf,$cod_tercer,$contro_sad,$contro_interf){



           if($contro_interf == 0)

           {return false;}



           $query = "INSERT INTO ".$bd_interf.".tab_contro_sad (cod_tercer, cod_contro_cis, cod_contro_sad)

                     VALUES ('$cod_tercer','$contro_interf','$contro_sad')";



           if($consulta = new Consulta($query, $this -> conexion,"R"))

           {return true;}

           else

           {return false;}

        }



        function is_pc($bd_interf,$cod_tercer,$contro_sad){



           $query = "SELECT cod_contro_cis

                     FROM ".$bd_interf.".tab_contro_sad

                     WHERE cod_contro_sad = '$contro_sad' AND

                           cod_tercer     = '$cod_tercer'";



         $consulta = new Consulta($query, $this -> conexion,"R");

         $existe = $consulta -> ret_arreglo();



           if($existe)

           {return true;}

           else

           {return false;}



        }



        function act_pc($bd_interf,$cod_tercer,$contro_sad,$contro_interf){



           if($contro_interf == 0)

           {

                $query = "DELETE FROM ".$bd_interf.".tab_contro_sad

                                WHERE cod_contro_sad = '".$contro_sad."' AND

                                      cod_tercer = '".$cod_tercer."' ";



                $consulta = new Consulta($query, $this -> conexion,"R");

                return false;

           }



           if($this -> is_pc($bd_interf,$cod_tercer,$contro_sad))

           {

                      $query = "UPDATE ".$bd_interf.".tab_contro_sad

                                SET cod_contro_cis   = '$contro_interf'

                                WHERE cod_contro_sad = '$contro_sad' AND

                                      cod_tercer     = '$cod_tercer' ";



                      if($consulta = new Consulta($query, $this -> conexion,"R"))

                      {return true;}

                      else

                      {return false;}

           }

           else

           {

               if($this -> ins_pc($bd_interf,$cod_tercer,$contro_sad,$contro_interf))

               return true;

               else

               return false;



           }

        }





        function get_pc($bd_interf,$cod_tercer,$contro_sad){



          $query = "SELECT cod_contro_cis

                     FROM ".$bd_interf.".tab_contro_sad

                     WHERE cod_contro_sad = '$contro_sad' AND

                           cod_tercer     = '$cod_tercer'";



         $consulta = new Consulta($query, $this -> conexion);

         $pc       = $consulta -> ret_arreglo();

         return $pc[0];



        }





        function eli_pc($bd_interf,$cod_tercer,$contro_sad){



           if($this -> is_pc($bd_interf,$cod_tercer,$contro_sad))

           {

              $query = "DELETE FROM ".$bd_interf.".tab_contro_sad

                        WHERE cod_contro_sad = '$contro_sad' AND

                              cod_tercer     = '$cod_tercer' ";



                      if($consulta = new Consulta($query, $this -> conexion,"R"))

                      {return true;}

                      else

                      {return false;}

           }

        }



        //Funciones para manipulacion de las rutas

        function ins_ruta($bd_interf, $cod_tercer, $ruta_sad, $ruta_interf)

        {

                if($ruta_interf == 0)

                {return false;}



           $query = "INSERT INTO ".$bd_interf.".tab_rutasx_sad ( cod_tercer , cod_rutasx_cis , cod_rutasx_sad)

                     VALUES ('$cod_tercer', '$ruta_interf', '$ruta_sad')";



           if($consulta = new Consulta($query, $this -> conexion,"R"))

           {return true;}

           else

           {return false;}

        }





        function get_ruta($bd_interf,$cod_tercer,$ruta_sad){



                $query = "SELECT cod_rutasx_cis

                     FROM ".$bd_interf.".tab_rutasx_sad

                     WHERE cod_rutasx_sad = '$ruta_sad' AND

                           cod_tercer     = '$cod_tercer'";



         $consulta = new Consulta($query, $this -> conexion);

         $ruta = $consulta -> ret_vector();



         if($ruta[0] != "")

         {

                         return $ruta[0];

         }

         else

         {

                         return false;

         }

        }



        function get_ruta_despac($bd_interf,$cod_tercer,$despac_sad){



        $query = "SELECT a.cod_rutasx

                    FROM ".$bd_interf.".tab_despac_vehige a,

                         ".$bd_interf.".tab_despac_sad b

                   WHERE b.cod_despac_sad = '".$despac_sad."' AND

                         b.cod_tercer = '".$cod_tercer."' AND

                         a.num_despac = b.cod_despac_cis

                ";



         $consulta = new Consulta($query, $this -> conexion);

         $ruta = $consulta -> ret_vector();



         if($ruta[0] != "")

         {

                         return $ruta[0];

         }

         else

         {

                         return false;

         }

        }





        function is_ruta($bd_interf,$cod_tercer,$ruta_sad){



           $query = "SELECT cod_rutasx_cis

                     FROM ".$bd_interf.".tab_rutasx_sad

                     WHERE cod_rutasx_sad = '$ruta_sad' AND

                           cod_tercer     = '$cod_tercer'";



         $consulta = new Consulta($query, $this -> conexion);

         $existe = $consulta -> ret_arreglo();



           if($existe)

           {return true;}

           else

           {return false;}



        }



        function act_ruta($bd_interf,$cod_tercer,$ruta_sad,$ruta_interf){



           if($ruta_interf == 0)

           {

                $query = "DELETE FROM ".$bd_interf.".tab_rutasx_sad

                                WHERE cod_rutasx_sad = '".$ruta_sad."' AND

                                      cod_tercer = '".$cod_tercer."' ";



                $consulta = new Consulta($query, $this -> conexion,"R");

                return false;

           }



           if($this -> is_ruta($bd_interf,$cod_tercer,$ruta_sad))

           {



                      $query = "UPDATE ".$bd_interf.".tab_rutasx_sad

                                SET cod_rutasx_cis   = '$ruta_interf'

                                WHERE cod_rutasx_sad = '$ruta_sad' AND

                                      cod_tercer     = '$cod_tercer' ";



                      if($consulta = new Consulta($query, $this -> conexion,"R"))

                      {return true;}

                      else

                      {return false;}

           }

           else

           {

               if($this -> ins_ruta($bd_interf,$cod_tercer,$ruta_sad,$ruta_interf))

               return true;

               else

               return false;



           }

        }



        function eli_ruta($bd_interf,$cod_tercer,$ruta_sad){



           if($this -> is_ruta($bd_interf,$cod_tercer,$ruta_sad))

           {

              $query = "DELETE FROM ".$bd_interf.".tab_rutasx_sad

                        WHERE cod_rutasx_sad = '$ruta_sad' AND

                              cod_tercer     = '$cod_tercer' ";



                      if($consulta = new Consulta($query, $this -> conexion,"R"))

                      {return true;}

                      else

                      {return false;}

           }

        }

        //Funciones para manipulacion de las Agencias

        function get_agenci_sad($cod_agenci, $cod_tercer)

        {

                 $query = "SELECT cod_agenci, nom_agenci, cod_ciudad,

                                  dir_agenci, tel_agenci,

                                  con_agenci, dir_emailx, num_faxxxx

                           FROM ".$this -> bd.".tab_genera_agenci

                           WHERE cod_agenci = '$cod_agenci'";



                 if($this -> conexion_sat)

                        $consulta = new Consulta($query, $this -> conexion_sat);

                      else

                             $consulta = new Consulta($query, $this -> conexion);



                 $agenci   = $consulta -> ret_arreglo();

                 return $agenci;

        }



        function ins_agenci($bd_interf,$cod_tercer,$cod_agenci_sad)
        {
          $fec_actual = date("Y-m-d H:i:s");

            $query = "SELECT cod_agenci_sad
                        FROM ".$bd_interf.".tab_agenci_sad
                       WHERE cod_tercer = '".$cod_tercer."' AND
                             cod_agenci_sad = '".$cod_agenci_sad."'";

             $consec = new Consulta($query, $this -> conexion,"R");
             $existe = $consec -> ret_arreglo();

         if($existe)
                 return false;
        else
        {
            //trae el consecutivo de la tabla
              $query = "SELECT Max(cod_agenci) AS maximo
                        FROM ".$bd_interf.".tab_genera_agenci
                       WHERE cod_tercer = '".$cod_tercer."'  ";

            $consec = new Consulta($query, $this -> conexion,"R");
            $ultimo = $consec -> ret_matriz();

            $ultimo_consec = $ultimo[0][0];
            $nuevo_consec = $ultimo_consec+1;

            //retorna los datos de la agencia
            $datos = $this ->  get_agenci_sad($cod_agenci_sad, $cod_tercer);

            $query = "INSERT INTO ".$bd_interf.".tab_genera_agenci (cod_tercer,cod_agenci,nom_agenci, nom_contac,
                                    cod_ciudad, dir_agenci, tel_agenci, mai_contac,usr_creaci, fec_creaci)
                           VALUES ('".$cod_tercer."','$nuevo_consec','$datos[nom_agenci]',
                                   '$datos[con_agenci]','$datos[cod_ciudad]','$datos[dir_agenci]','$datos[tel_agenci]','$datos[dir_emailx]','$datos[usr_creaci]','$fec_actual') ";

           $consulta = new Consulta($query, $this -> conexion,"R");

            $query = "INSERT INTO ".$bd_interf.".tab_agenci_sad(cod_tercer, cod_agenci_cis , cod_agenci_sad)
                           VALUES ('".NIT_TRANSPOR."', '$nuevo_consec','$cod_agenci_sad')";

            $consulta = new Consulta($query , $this -> conexion,"R");

            if(!mysql_error())
               return true;
            else
               return false;
        }

        }//fin function insertar_agencia cis



        function is_agenci($bd_interf,$cod_tercer,$agenci_sad)

        {

           $query = "SELECT cod_agenci_cis

                     FROM ".$bd_interf.".tab_agenci_sad

                     WHERE cod_agenci_sad = '$agenci_sad' AND

                           cod_tercer     = '$cod_tercer'";



         $consulta = new Consulta($query, $this -> conexion);

         $existe = $consulta -> ret_arreglo();



           if($existe)

           {return $existe[0];}

           else

           {return false;}



        }





        function act_agenci($bd_interf,$cod_tercer,$cod_agenci_sad)

        {

           $agenci_sad =  $this -> is_agenci($bd_interf,$cod_tercer, $cod_agenci_sad);

           if($agenci_sad)

           {

                      $query = "UPDATE ".$bd_interf.".tab_agenci_sad

                                SET cod_agenci_cis   = '$cod_agenci_cis'

                                WHERE cod_agenci_sad = '$agenci_sad' AND

                                      cod_tercer     = '$cod_tercer' ";

                      $consulta = new Consulta($query, $this -> conexion,"R");



                      //retorna los datos de la agencia en sad trafico

                      $datos = get_agenci_sad($cod_agenci_sad, $cod_tercer);



                      $query = "UPDATE ".$bd_interf.".tab_genera_agenci

                                SET nom_agenci = '$datos[nom_agenci]',

                                    cod_ciudad = '$datos[cod_ciudad]',

                                    dir_agenci = '$datos[dir_agenci]',

                                    tel_agenci = '$datos[tel_agenci]',

                                    con_agenci = '$datos[con_agenci]',

                                    dir_emailx = '$datos[dir_emailx]',

                                    num_faxxxx = '$datos[num_faxxxx]',

                                    usr_modifi = 'interfaz',

                                    fec_modifi = NOW()

                                    WHERE cod_tercer = '$cod_tercer' AND

                                          cod_agenci = '$cod_agenci_cis'";



                      if($consulta = new Consulta($query, $this -> conexion,"R"))

                      {return true;}

                      else

                      {return false;}

           }



        }



        function eli_agenci($bd_interf,$cod_tercer,$agenci_sad)

        {



           $agenci_sad =  $this -> is_agenci($bd_interf,$cod_tercer, $cod_agenci_sad);

           if($agenci_sad)

           {

              $query = "DELETE FROM ".$bd_interf.".tab_agenci_sad

                        WHERE cod_agenci_sad = '$agenci_sad' AND

                              cod_tercer     = '$cod_tercer' ";

              $consulta = new Consulta($query, $this -> conexion,"R");





             $query = "DELETE FROM ".$bd_interf.".tab_genera_agenci

                       WHERE cod_tercer = '$cod_tercer' AND

                             cod_agenci = '$agenci_sad'  ";



                      if($consulta = new Consulta($query, $this -> conexion,"R"))

                      {return true;}

                      else

                      {return false;}

           }

        }





    function get_tercer($cod_tercer_sad)

    {

             $query = "SELECT a.cod_tercer, a.num_verifi, a.cod_tipdoc, a.cod_terreg,

                              a.nom_apell1, a.nom_apell2, a.nom_tercer, a.abr_tercer,

                              a.dir_domici, a.num_telef1, a.num_telef2, a.num_telmov,

                              a.num_faxxxx, a.cod_paisxx, a.cod_depart, a.cod_ciudad,

                              a.dir_emailx, a.dir_urlweb, a.cod_estado, a.dir_ultfot,

                              a.obs_tercer, a.obs_aproba, a.usr_modifi, a.fec_modifi

                       FROM ".$this -> bd.".tab_tercer_tercer a

                       WHERE cod_tercer = '$cod_tercer_sad'";



             if($this -> conexion_sat)

                $consulta = new Consulta($query, $this -> conexion_sat);

             else

                     $consulta = new Consulta($query, $this -> conexion);

             return $consulta -> ret_arreglo();

    }



    function is_tercer($bd_interf,$cod_tercer)

    {

             //verifica que el tercero exista

                          $query = "SELECT a.cod_tercer

                       FROM ".$bd_interf.".tab_genera_tercer a

                       WHERE a.cod_tercer = '".$cod_tercer."'";

             $consulta = new Consulta($query, $this -> conexion);

              $tercer   = $consulta -> ret_arreglo();



             if($tercer[0])

                 return true;

             else

                return false;



    }



    function is_hab_tercer($bd_interf,$cod_tercer,$cod_emptra)

    {

            $query = "SELECT cod_tercer

                      FROM ".$bd_interf.".tab_tercer_emptra

                      WHERE cod_tercer = '$cod_tercer' AND

                            cod_emptra = '$cod_emptra' ";

            $consulta = new Consulta($query, $this -> conexion);

            $habil    = $consulta -> ret_arreglo();



            if($habil[0])

                return true;

            else

                return false;

    }



    function ins_tercer($bd_interf,$cod_transp,$cod_tercer)

    {

           if(!$this -> is_tercer($bd_interf,$cod_tercer))

           {

             $datos = $this -> get_tercer($cod_tercer);

             $query = "INSERT INTO ".$bd_interf.".tab_genera_tercer (cod_tercer,

                       cod_tipdoc, nom_tercer, abr_tercer, dir_tercer, tel_tercer, num_cel,

                       mai_tercer, cod_ciudad, obs_tercer,

                       ind_estado, usr_creaci, fec_creaci)

                      VALUES ('$datos[cod_tercer]','$datos[cod_tipdoc]','$datos[nom_tercer]',

                               '$datos[abr_tercer]','$datos[dir_domici]',

                               '$datos[num_telef1]','$datos[num_telmov]',

                               '$datos[dir_emailx]','$datos[cod_ciudad]','$datos[obs_tercer]',

                               '1', '$GLOBALS[usuario]', NOW())";

            $insert = new Consulta($query, $this -> conexion,"R");

            //trae las actividades del tercero en sad
            $activi_sad = $this -> get_teract($cod_tercer);

            for($i = 0 ; $i < sizeof($activi_sad); $i++)
            {
                //homologa las actividades
                $activi_interf = $this -> get_activi_interf($bd_interf,$activi_sad[$i][0]);
                if($activi_interf)
                {
                //inserta las actividades
                $this -> act_activi($bd_interf,$cod_tercer,$activi_interf);
                }
            }

            $query = "INSERT INTO ".$bd_interf.".tab_tercer_emptra (cod_tercer, cod_emptra, usr_creaci, fec_creaci)
                      VALUES ('$cod_tercer','$cod_transp','$GLOBALS[usuario]', NOW())";

            $insert = new Consulta($query, $this -> conexion,"R");
            return true;
           }
           else
           {
             $datos = $this -> get_tercer($cod_tercer);

             $query = "UPDATE ".$bd_interf.".tab_genera_tercer
                                   SET cod_tipdoc = '$datos[cod_tipdoc]',
                                nom_tercer = '$datos[nom_tercer]',
                                abr_tercer = '$datos[abr_tercer]',
                                dir_tercer = '$datos[dir_domici]',
                                tel_tercer = '$datos[num_telef1]',
                                num_cel = '$datos[num_telmov]',
                                mai_tercer = '$datos[dir_emailx]',
                                cod_ciudad = '$datos[cod_ciudad]',
                                obs_tercer = '$datos[obs_tercer]',
                                usr_modifi = 'U.I.SAT',
                                fec_modifi = NOW()
                          WHERE cod_tercer = '$datos[cod_tercer]'
                        ";

            $insert = new Consulta($query, $this -> conexion,"R");

            //trae las actividades del tercero en sad
            $activi_sad = $this -> get_teract($cod_tercer);

            for($i = 0 ; $i < sizeof($activi_sad); $i++)
            {
                //homologa las actividades
                $activi_interf = $this -> get_activi_interf($bd_interf,$activi_sad[$i][0]);
                //valida que el tercero no tenga la actividad

                if(!$this -> is_activi($bd_interf,$cod_tercer,$activi_interf))
                {
                     if($activi_interf && (!$this ->  is_hab_tercer($bd_interf,$cod_tercer,$cod_transp)))
                     {
                     //inserta las actividades
                     $this -> act_activi($bd_interf,$cod_tercer,$activi_interf);
                     }
                }
            }
           }
    }

    function eli_tercer($bd_interf,$cod_transp,$cod_tercer)

    {

           if($this -> is_tercer($bd_interf,$cod_tercer))

           {

            $query = "DELETE FROM ".$bd_interf.".tab_emptra_tercer

                      WHERE cod_tercer = '$cod_tercer' AND cod_emptra = '$cod_transp'";



            $eliminar = new Consulta($query, $this -> conexion,"R");

            return true;

           }



    }





    function get_activi_interf($bd_interf,$cod_activi)

    {

            $query = "SELECT cod_activi_cis

                      FROM ".$bd_interf.".tab_activi_sad

                      WHERE cod_activi_sad = '$cod_activi'";

            $consulta = new Consulta($query, $this -> conexion);

            $activi   = $consulta -> ret_arreglo();

            return $activi[0];

    }



    function get_teract($cod_tercer)

    {



      $query = "SELECT cod_activi FROM ".$this -> bd.".tab_tercer_activi WHERE cod_tercer = '$cod_tercer' ";



      if($this -> conexion_sat)

        $consulta = new Consulta($query, $this -> conexion_sat);

      else

              $consulta = new Consulta($query, $this -> conexion);



      return $consulta -> ret_matriz();

    }





    function is_activi($bd_interf,$cod_tercer,$cod_activi)

    {

                 //verifica que el tercero tenga la actividad requerida

                 $query = "SELECT a.cod_tercer

                       FROM ".$bd_interf.".tab_genera_teract a

                       WHERE a.cod_tercer = '".$cod_tercer."' AND

                             a.cod_activi = '".$cod_activi."'";



             $consulta = new Consulta($query, $this -> conexion);

             $activi   = $consulta -> ret_arreglo();



             if($activi[0])

                return true;

             else

                return false;

    }



    function act_activi($bd_interf,$nit,$activi)

    {

            $query = "INSERT INTO ".$bd_interf.".tab_genera_teract (cod_tercer,cod_activi,usr_creaci, fec_creaci)

                      VALUE ('$nit','$activi','$GLOBALS[usuario]',NOW()) ";

            $insert = new Consulta($query, $this -> conexion,"R");

    }



    //si los datos no cuadran debe ingresar datos no registrados

             //ins_vehicu($registro[5],$registro[6], $registro[7],$registro[8],$registro[9],$registro[10],$registro[11],$registro[12],$registro[18],$registro[24]);

    function ins_vehicu($bd_interf,$cod_transp,$num_placa)

    {



           if(!$this -> is_vehicu($bd_interf,$num_placa))

           {

             $datos = $this -> get_vehicu($num_placa);



            $query = "INSERT INTO ".$bd_interf.".tab_vehige_placas (num_placax, cod_marcax,

                             cod_lineax, cod_clasex,ano_modelo, cod_colorx, cod_carroc, num_motorx, num_seriex,

                             num_chasis, val_pesove, cod_propie,

                             cod_poseed, cod_conduc, usr_creaci, fec_creaci)

                      VALUES ('$datos[num_placax]','$datos[cod_marcax]','$datos[cod_lineax]','1',

                               '$datos[ano_modelo]','$datos[cod_colorx]','$datos[cod_carroc]',

                               '$datos[num_motorx]','$datos[num_seriex]','$datos[num_chasis]',

                               '$datos[val_pesove]','$datos[cod_propie]','$datos[cod_tenedo]',

                               '$datos[cod_conduc]','$GLOBALS[usuario]', NOW())";

            $insert = new Consulta($query, $this -> conexion,"R");



            $query = "INSERT INTO ".$bd_interf.".tab_vehige_emptra (num_placax, cod_emptra, usr_creaci, fec_creaci)

                      VALUES ('$num_placa','$cod_transp','$GLOBALS[usuario]', NOW())";

            $insert = new Consulta($query, $this -> conexion,"R");

            return true;



           }

           else

           {

                $datos = $this -> get_vehicu($num_placa);

                $query = "UPDATE ".$bd_interf.".tab_vehige_placas
                             SET cod_marcax = '$datos[cod_marcax]',
                                 cod_lineax = '$datos[cod_lineax]',
                                 ano_modelo = '$datos[ano_modelo]',
                                 cod_colorx = '$datos[cod_colorx]',
                                 cod_carroc = '$datos[cod_carroc]',
                                 num_motorx = '$datos[num_motorx]',
                                 num_seriex = '$datos[num_seriex]',
                                      num_chasis = '$datos[num_chasis]',
                                 val_pesove = '$datos[val_pesove]',
                                 cod_propie = '$datos[cod_propie]',
                                      cod_poseed = '$datos[cod_tenedo]',
                                 cod_conduc = '$datos[cod_conduc]',
                                 usr_modifi = 'U.I.SAT',
                                 fec_modifi = NOW()
                           WHERE num_placax = '$datos[num_placax]'
                        ";

                    $insert = new Consulta($query, $this -> conexion,"R");

               if(!$this ->  is_hab_vehicu($bd_interf,$num_placa,$cod_transp))

               {

                        $query = "INSERT INTO ".$bd_interf.".tab_vehige_emptra (num_placax, cod_emptra, usr_creaci, fec_creaci)

                                  VALUES ('$num_placa','$cod_transp','$GLOBALS[usuario]', NOW())";

                        $insert = new Consulta($query, $this -> conexion,"R");

                        return true;

               }



           }



    }//fin ins_vehicu





    function get_vehicu($cod_placa_sadt)

    {

             $query = "SELECT num_placax, cod_marcax, cod_lineax,

                              ano_modelo, cod_colorx, cod_carroc,

                              num_motorx, num_seriex, num_chasis,

                              val_pesove, val_capaci, reg_nalcar,

                              num_poliza, cod_asesoa, cod_ciusoa,

                              fec_vigfin, ano_repote, num_config,

                              cod_propie, cod_tenedo, cod_conduc,

                              nom_vincul, num_tarpro, num_tarope,

                              cod_califi, fec_vigvin, num_polirc,

                              fec_venprc, cod_aseprc, ind_aproba,

                              obs_aproba, dir_fotfre, dir_fotizq,

                              dir_fotder, dir_fotpos, cod_tipveh,

                              ind_chelis, fec_revmec, num_agases,

                              fec_vengas, obs_vehicu

                       FROM ".$this -> bd.".tab_vehicu_vehicu

                       WHERE num_placax = '$cod_placa_sadt'";



             if($this -> conexion_sat)

                $consulta = new Consulta($query, $this -> conexion_sat);

             else

                     $consulta = new Consulta($query, $this -> conexion);

             return $consulta -> ret_arreglo();

    }



    function is_vehicu($bd_interf,$num_placa)

    {

             //verifica que el vehiculo exista

             $query = "SELECT a.num_placax

                       FROM ".$bd_interf.".tab_vehige_placas a

                       WHERE a.num_placax = '".$num_placa."'";

             $consulta = new Consulta($query, $this -> conexion);

              $placa_interf   = $consulta -> ret_arreglo();



             if($placa_interf[0])

                 return true;

             else

                return false;



    }



    function is_hab_vehicu($bd_interf,$num_placa,$cod_emptra)

    {

            $query = "SELECT num_placax

                      FROM ".$bd_interf.".tab_vehige_emptra

                      WHERE num_placax = '$num_placa' AND

                            cod_emptra = '$cod_emptra' ";

            $consulta = new Consulta($query, $this -> conexion);

            $habil    = $consulta -> ret_arreglo();



            if($habil[0])

                return true;

            else

                return false;

    }



    function ins_despac($bd_interf,$cod_transp,$num_despac,$despac_sad,$agenci_sad,$rut_sad,$conduc,$ciuori,$ciudes,$generador = "",$obsgen,$medcom,$proesp)
    {
             if(!$this -> is_agenci($bd_interf,$cod_transp,$agenci_sad))
             {
                if($this -> ins_agenci($bd_interf,$cod_transp,$agenci_sad));
             }

             $agenci = $this -> is_agenci($bd_interf,$cod_transp,$agenci_sad);
             $ruta   = $this -> get_ruta($bd_interf,$cod_transp,$rut_sad);



             //trae el consecutivo de despachos en cis

             $query = "SELECT MAX(num_despac) FROM ".$bd_interf.".tab_despac_despac";

             $consulta = new Consulta($query, $this -> conexion);

             $consec   = $consulta -> ret_arreglo();

             $nuevo_consec1 = $consec[0]+1;

             if($generador != "")
             {
              if(!$this -> is_client($bd_interf,$generador))
              {
                if($this -> ins_tercer($bd_interf,$cod_transp,$generador));
              }
              else if(!$this -> is_activi_tercer($bd_interf,$generador,"5"))
                if($this -> ins_activi_tercer($bd_interf,$generador,"5"));
             }
             else $generador = "NULL";

             $query = "INSERT INTO ".$bd_interf.".tab_despac_despac

                      (num_despac, num_manifi, cod_genera, fec_despac, cod_ciuori,

                       cod_ciudes, obs_despac,

                       usr_creaci, fec_creaci)

                       VALUES('$nuevo_consec1','$despac_sad[manifi]', $generador, NOW(),

                              '$ciuori','$ciudes','$obsgen',

                              '$GLOBALS[usuario]', NOW() )";

            $consulta = new Consulta($query, $this -> conexion,"R");



            if(!$ruta)

                    exit;



            $query = "INSERT INTO ".$bd_interf.".tab_despac_vehige (num_despac, cod_transp,
                             cod_agenci, cod_rutasx, cod_conduc, num_placax,
                             obs_medcom, obs_proesp, ind_activo, usr_creaci,
                             fec_creaci,ind_planru)
                      VALUES ('$nuevo_consec1','$cod_transp','$agenci','$ruta',
                              '$conduc','$despac_sad[placa]','$medcom','$proesp',
                              'N','$despac_sad[usuario]',NOW(),'1')";

            $consulta = new Consulta($query, $this -> conexion,"R");


            $query = "INSERT INTO ".$bd_interf.".tab_despac_sad
                      (cod_tercer, cod_despac_cis, cod_despac_sad)
                      VALUES ('$cod_transp','$nuevo_consec1','$num_despac')";

            $consulta = new Consulta($query, $this -> conexion,"R");



            if(mysql_error())

               return false;

            else

                return true;

    }





function ins_fec_salpl($bd_interf,$cod_transp,$despac_sad,$fecha_sal,$nov_antici)

{



         //trae el despacho homologado

         $despac_interf = $this -> get_despac_interf($bd_interf,$cod_transp,$despac_sad);



         $nov_antici = array_values($nov_antici);



         if(sizeof($nov_antici))

         {

                  for($k=0 ; $k < sizeof($nov_antici); $k++)

                  {

                       $nov    = $this -> get_noveda_interf($bd_interf,$cod_transp,$nov_antici[$k][1]);

                       $puesto = $this -> get_pc($bd_interf,$cod_transp,$nov_antici[$k][0]);



                       if($puesto)

                       {

                       $query = "INSERT INTO ".$bd_interf.".tab_despac_pernoc

                                 VALUES ('$despac_interf', '".$puesto."','".$nov."',

                                         '".$nov_antici[$k][2]."','$GLOBALS[usuario]',NOW(),

                                          '$GLOBALS[usuario]',NOW())";

                      $consulta = new Consulta($query, $this -> conexion);

                      }



                   }

          }

         $query = "UPDATE ".$bd_interf.".tab_despac_vehige

                   SET  fec_salipl =  '$fecha_sal',

                        fec_llegpl =  '$fec_lleg'

                   WHERE num_despac = '$despac_interf'";

         $update = new Consulta($query, $this -> conexion, "R");



         if(!mysql_error())

         return true;

         else

         return false;

}





    function act_despac($bd_interf,$cod_transp,$num_despac,$despac_sad)

    {

            //trae el despacho homologado

            $despac_cis = $this -> get_despac_interf($bd_interf,$cod_transp,$despac_sad);

            //trae la agecia homolgada

            $agenci = $this -> is_agenci($bd_interf,$cod_transp,$despac_sad[agencia]);

            //trae la ruta homologada

            $ruta   = $this -> get_ruta($bd_interf,$cod_transp,$despac_sad[ruta]);



            $query = "UPDATE ".$bd_interf.".tab_despac_despac

                      SET num_manifi = '$despac_sad[manifi]',

                          fec_despac = NOW(),

                          cod_ciuori = '$despac_sad[ciuori]',

                          cod_ciudes = '$despac_sad[ciudes]',

                          obs_despac = '$despac_sad[obsgrl]',

                          usr_modifi = '$GLOBALS[usuario]',

                          fec_modifi = NOW()

                      WHERE num_despac = '$despac_cis'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            $query = "UPDATE ".$bd_interf.".tab_despac_vehige

                      SET cod_transp = '$cod_transp',

                          cod_agenci = '$agenci',

                          cod_rutasx = '$ruta',

                          cod_conduc = '$despac_sad[conduc]',

                          num_placax = '$despac_sad[placa]',

                          obs_medcom = '$despac_sad[medcom]',

                          ind_activo = 'N',

                          usr_modifi = '$GLOBALS[usuario]'

                          fec_modifi = NOW()

                      WHERE num_despac = '$despac_cis'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            if(mysql_error())

               return false;

            else

                return true;

    }





    function eli_despac($bd_interf,$cod_transp,$num_despac,$placa)

    {



           //valida que el vehiculo no este en ruta

           if(!$this -> is_enruta($bd_interf,$placa))

           {

            //trae el despacho homologado

            $despac_cis = $this -> get_despac_interf($bd_interf,$cod_transp,$despac_sad);



            $query = "DELETE FROM ".$bd_interf.".tab_despac_contro WHERE num_despac = '$despac_cis'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            $query = "DELETE FROM ".$bd_interf.".tab_despac_pernoc WHERE num_despac = '$despac_cis'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            $query = "DELETE FROM ".$bd_interf.".tab_despac_seguim WHERE num_despac = '$despac_cis'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            $query = "DELETE FROM ".$bd_interf.".tab_despac_vehige WHERE num_despac = '$despac_cis'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            $query = "DELETE FROM ".$bd_interf.".tab_despac_despac WHERE num_despac = '$despac_cis'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            if(mysql_error())

               return false;

            else

                return true;

            }

            else

            { return false;}



    }



    function act_salida($bd_interf,$cod_trans,$despac_sad,$rut_sad,$fec_sal,$puestos_sad)
    {
      $fec_actual = date("Y-m-d H:i:s");
      $fecha=$fec_sal;

      //trae el despacho homologado
      $despac_cis = $this -> get_despac_interf($bd_interf,$cod_trans,$despac_sad);

      //trae la ruta homologada
      $rut_interf  = $this -> get_ruta($bd_interf,$cod_trans,$rut_sad);

      for($i = 0; $i < sizeof($puestos_sad); $i++)
      {
       $pcontro[$i] = $puestos_sad[$i][0];
       $pcnove[$i]  = $puestos_sad[$i][2];

       if($pcnove[$i] != "0")
       {
        $query = "SELECT a.cod_noveda_cis
		    FROM ".$bd_interf.".tab_noveda_sad a
		   WHERE a.cod_noveda_sad = ".$pcnove[$i]." AND
			 a.cod_tercer = '".$cod_trans."'
		 ";

        $consulta = new Consulta($query, $this -> conexion);
        $novhom = $consulta -> ret_matriz();

        if($novhom)
        {
	 $pctime[$i]  = $puestos_sad[$i][1];
         $pcnove[$i]  = $novhom[0][0];
        }
        else
        {
	 echo "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">La Novedad C&oacute;digo ".$pcnove[$i]." no se Encuentra Homologada en la Interfaz con <b>".$this -> nom_interf[$this -> cant_interf - 1][0]."</b> Para Realizar Proceso de Pre Novedades del Despacho";

	 exit;
        }
       }
      }

      $j = 0;

      for($i = 0; $i < sizeof($puestos_sad); $i++)
      {
       $query = "SELECT a.cod_contro
                   FROM ".$bd_interf.".tab_genera_rutcon a,
                        ".$bd_interf.".tab_contro_sad b
                  WHERE a.cod_rutasx = '$rut_interf' AND
                        b.cod_contro_sad = '".$pcontro[$i]."' AND
                        a.cod_contro = b.cod_contro_cis AND
                        b.cod_tercer = '".$cod_trans."'
                ";

       $consulta = new Consulta($query, $this -> conexion);
       $matriz1 = $consulta -> ret_matriz();

       if($matriz1)
       {
	$pcontro[$i] = $matriz1[0][0];
        $j++;
       }
       else
       {
	$pcontro[$i] = NULL;
        $pctime[$i]  = NULL;
        $pcnove[$i]  = NULL;
       }
      }

      if(!$j)
      {
       echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Ninguno de Los Puestos de Control Seleccionados Se Encuentra Homologado en Interfaz ".$this -> nom_interf[$this -> cant_interf - 1][0].", o Se Encuentra Homologado Con Un Puesto De Control en Ruta Diferente. Se Interrumpe la Salida del Vehiculo Placa <b>$GLOBALS[placa]</b>.<br>";

       exit;
      }

      $query = "DELETE FROM ".$bd_interf.".tab_despac_seguim
		   WHERE num_despac = ".$despac_cis." AND
			 cod_rutasx = ".$rut_interf."
	    ";

      $insercion = new Consulta($query, $this -> conexion,"R");

    //La Eliminacióin de los registros de pernoctación relacionados a este plan de ruta
    //se eliminan en cascada.

    $tieacu = 0;

    for($i = 0; $i < sizeof($pcontro); $i++)
    {
     if($pcontro[$i] != NULL)
     {
	$query = "SELECT a.val_duraci
		    FROM ".$bd_interf.".tab_genera_rutcon a
		   WHERE a.cod_rutasx = ".$rut_interf." AND
			 a.cod_contro = ".$pcontro[$i]."
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$tieori = $consulta -> ret_matriz();

	$ultimo = $tieori[0][0];
	$tiepla = $tieacu + $tieori[0][0];

	 $query = "INSERT INTO ".$bd_interf.".tab_despac_seguim
			       (num_despac,cod_rutasx,cod_contro,fec_planea,
				fec_alarma,usr_creaci,fec_creaci,usr_modifi,
				fec_modifi)
                   	VALUES (".$despac_cis.",'".$rut_interf."','".$pcontro[$i]."',
                     		DATE_ADD('$fec_sal', INTERVAL ".$tiepla." MINUTE),
                     		DATE_ADD('$fec_sal', INTERVAL ".$tiepla." MINUTE),
                     		'".$this -> usuario."','$fec_actual',NULL,NULL)";

    	 $insercion = new Consulta($query, $this -> conexion,"R");

	 if($pcnove[$i] != "0" && $pcontro[$i] != CONS_CODIGO_PCLLEG)
	 {
	  $query = "INSERT INTO ".$bd_interf.".tab_despac_pernoc
			        (num_despac,cod_rutasx,cod_contro,cod_noveda,
				 val_pernoc,usr_creaci,fec_creaci,usr_modifi,
				 fec_modifi)
			 VALUES (".$despac_cis.",".$rut_interf.",".$pcontro[$i].",
				 ".$pcnove[$i].",".$pctime[$i].",'".$this -> usuario."',
				 '".$fec_actual."',NULL,NULL)
		   ";

	  $insercion = new Consulta($query, $this -> conexion,"R");
	 }

	 if($pcnove[$i] != "0")
	  $tieacu += $pctime[$i];
     }
    }

    $query = "SELECT DATE_ADD('$fec_sal', INTERVAL ".$ultimo." MINUTE)
	    ";

    $consulta = new Consulta($query, $this -> conexion);
    $timlle = $consulta -> ret_matriz();

    $query = "UPDATE ".$bd_interf.".tab_despac_vehige
		SET fec_salipl = '".$fec_sal."',
		    ind_activo = 'S',
		    fec_llegpl = '".$timlle[0][0]."',
		    usr_modifi = '".$this -> usuario."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = '".$despac_cis."'
	    ";

    $insercion = new Consulta($query, $this -> conexion,"R");

    $query = "UPDATE ".$bd_interf.".tab_despac_despac
		SET fec_salida= '".$fec_sal."',
		    usr_modifi = '".$this -> usuario."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = '".$despac_cis."'
	    ";

    $insercion = new Consulta($query, $this -> conexion,"R");



  return true;

    }



    function revsal_despac($bd_interf,$cod_transp,$despac_sad)

    {

            //trae el despacho homologado

            $despac_cis = $this -> get_despac_interf($bd_interf,$cod_transp,$despac_sad);



            $query = "UPDATE ".$bd_interf.".tab_despac_despac

                         SET fec_salida = NULL

                       WHERE num_despac = '".$despac_cis."'

                        ";



            $consulta = new Consulta($query, $this -> conexion,"R");



            if(mysql_error())

               return false;

            else

                return true;

    }



    function revlle_despac($bd_interf,$cod_transp,$despac_sad)
    {
     $query = "SELECT a.num_placax
               FROM ".$bd_interf.".tab_despac_vehige a
              WHERE a.num_despac = ".$despac_sad."
                 ";

     $consulta = new Consulta($query, $this -> conexion);
     $placax = $consulta -> ret_matriz();

     $query = "SELECT a.num_despac
               FROM ".$bd_interf.".tab_despac_vehige a,
                    ".$bd_interf.".tab_despac_despac b
              WHERE a.num_placax = '".$placax[0][0]."' AND
                    a.num_despac = b.num_despac AND
                    a.ind_activo = 'S' AND
                    b.fec_salida IS NOT NULL AND
                    b.fec_llegad IS NULL AND
                    b.num_despac != ".$despac_sad."
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $enruta = $consulta -> ret_matriz();

   if(!$enruta)
   {
            //trae el despacho homologado
            $despac_cis = $this -> get_despac_interf($bd_interf,$cod_transp,$despac_sad);

            $query = "UPDATE ".$bd_interf.".tab_despac_despac
                         SET fec_llegad = NULL,
                             obs_llegad = NULL
                       WHERE num_despac = '".$despac_cis."'
                ";

            $consulta = new Consulta($query, $this -> conexion,"R");

            if(mysql_error())
               return false;
            else
                return true;
     }
     else
     {
      return false;
     }
    }



    function anu_despac($bd_interf,$cod_transp,$num_despac,$obs_anula)

    {

            //trae el despacho homologado

            $despac_cis = $this -> get_despac_interf($bd_interf,$cod_transp,$num_despac);



            $query = "UPDATE ".$bd_interf.".tab_despac_vehige

                               SET ind_activo = 'A'

                       WHERE num_despac = '".$despac_cis."'";

            $consulta = new Consulta($query, $this -> conexion,"R");



            if(mysql_error())

               return false;

            else

                return true;

    }



    function get_despac_interf($bd_interf,$cod_trans,$despac_sad)

    {

              $query = "SELECT cod_despac_cis

                        FROM ".$bd_interf.".tab_despac_sad

                        WHERE cod_despac_sad = '$despac_sad' AND

                              cod_tercer     = '$cod_trans' ";

              $consulta = new Consulta($query, $this -> conexion);

              $despac_cis = $consulta -> ret_arreglo();

              return $despac_cis[0];

    }

    function ins_planru($bd_interf,$cod_trans,$despac_sad,$rut_sad,$fec_sal,$puestos_sad)
    {
      $fec_actual = date("Y-m-d H:i:s");
      $fecha=$fec_sal;

      $despac_cis = $this -> get_despac_interf($bd_interf,$cod_trans,$despac_sad);
      $rut_interf  = $this -> get_ruta($bd_interf,$cod_trans,$rut_sad);
      $j = 0;

      for($i = 0; $i < sizeof($puestos_sad); $i++)
      {
       $pcontro[$i] = $puestos_sad[$i][0];
       $pcnove[$i]  = $puestos_sad[$i][2];

       if($pcnove[$i] != "0")
       {
        $query = "SELECT a.cod_noveda_cis
		    FROM ".$bd_interf.".tab_noveda_sad a
		   WHERE a.cod_noveda_sad = ".$pcnove[$i]." AND
			 a.cod_tercer = '".$cod_trans."'
		 ";

        $consulta = new Consulta($query, $this -> conexion);
        $novhom = $consulta -> ret_matriz();

        if($novhom)
        {
	 $pctime[$i]  = $puestos_sad[$i][1];
         $pcnove[$i]  = $novhom[0][0];
        }
        else
        {
	 echo "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">La Novedad C&oacute;digo ".$pcnove[$i]." no se Encuentra Homologada en la Interfaz con <b>".$this -> nom_interf[$this -> cant_interf - 1][0]."</b> Para Realizar Proceso de Pre Novedades del Despacho";

	 exit;
        }
       }
      }

      for($i = 0; $i < sizeof($puestos_sad); $i++)
      {
       $query = "SELECT a.cod_contro
                   FROM ".$bd_interf.".tab_genera_rutcon a,
                        ".$bd_interf.".tab_contro_sad b
                  WHERE a.cod_rutasx = '$rut_interf' AND
                        b.cod_contro_sad = '".$pcontro[$i]."' AND
                        a.cod_contro = b.cod_contro_cis AND
                        b.cod_tercer = '".$cod_trans."'
                ";

       $consulta = new Consulta($query, $this -> conexion);
       $matriz1 = $consulta -> ret_matriz();

       if($matriz1)
       {
	$pcontro[$i] = $matriz1[0][0];
        $j++;
       }
       else
       {
	$pcontro[$i] = NULL;
        $pctime[$i]  = NULL;
        $pcnove[$i]  = NULL;
       }
      }

      if(!$j)
      {
       echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Ninguno de Los Puestos de Control Seleccionados Se Encuentra Homologado en Interfaz ".$this -> nom_interf[$this -> cant_interf - 1][0].", o Se Encuentra Homologado Con Un Puesto De Control en Ruta Diferente. Se Interrumpe la Salida del Vehiculo Placa <b>$GLOBALS[placa]</b>.<br>";

       exit;
      }

      $query = "DELETE FROM ".$bd_interf.".tab_despac_seguim
		   WHERE num_despac = ".$despac_cis." AND
			 cod_rutasx = ".$rut_interf."
	    ";

      $insercion = new Consulta($query, $this -> conexion,"R");

      $tieacu = 0;

      for($i = 0; $i < sizeof($pcontro); $i++)
      {
       if($pcontro[$i] != NULL)
       {
	$query = "SELECT a.val_duraci
		    FROM ".$bd_interf.".tab_genera_rutcon a
		   WHERE a.cod_rutasx = ".$rut_interf." AND
			 a.cod_contro = ".$pcontro[$i]."
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$tieori = $consulta -> ret_matriz();

	$ultimo = $tieori[0][0];
	$tiepla = $tieacu + $tieori[0][0];

	$query = "SELECT a.cod_rutasx
		    FROM ".$bd_interf.".tab_genera_ruttra a
		   WHERE a.cod_rutasx = ".$rut_interf." AND
			 a.cod_transp = '".$cod_trans."' AND
			 a.cod_contro = ".$pcontro[$i]."
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$asigtran = $consulta -> ret_matriz();

	if(!$asigtran)
	{
	 $query = "INSERT INTO ".$bd_interf.".tab_genera_ruttra
			  (cod_rutasx,cod_contro,cod_transp,usr_creaci,
			   fec_creaci)
		   VALUES (".$rut_interf.",".$pcontro[$i].",'".$cod_trans."','".$this -> usuario."',
			   '".$fec_actual."')
		  ";

	 $insercion = new Consulta($query, $this -> conexion,"R");
	}

	$query = "INSERT INTO ".$bd_interf.".tab_despac_seguim
			      (num_despac,cod_rutasx,cod_contro,fec_planea,
			       fec_alarma,usr_creaci,fec_creaci,usr_modifi,
			       fec_modifi)
                       VALUES (".$despac_cis.",'".$rut_interf."','".$pcontro[$i]."',
                     	      DATE_ADD('$fec_sal', INTERVAL ".$tiepla." MINUTE),
                     	      DATE_ADD('$fec_sal', INTERVAL ".$tiepla." MINUTE),
                     	      '".$this -> usuario."','$fec_actual',NULL,NULL)";

    	$insercion = new Consulta($query, $this -> conexion,"R");

	if($pcnove[$i] && $pcontro[$i] != CONS_CODIGO_PCLLEG)
	{
	 $query = "INSERT INTO ".$bd_interf.".tab_despac_pernoc
			       (num_despac,cod_rutasx,cod_contro,cod_noveda,
				val_pernoc,usr_creaci,fec_creaci,usr_modifi,
				fec_modifi)
			VALUES (".$despac_cis.",".$rut_interf.",".$pcontro[$i].",
				".$pcnove[$i].",".$pctime[$i].",'".$this -> usuario."',
				'".$fec_actual."',NULL,NULL)
		   ";

	 $insercion = new Consulta($query, $this -> conexion,"R");
	}

	if($pcnove[$i] != "0")
	 $tieacu += $pctime[$i];
       }
      }

       $query = "SELECT DATE_ADD('$fec_sal', INTERVAL ".$ultimo." MINUTE)
	    ";

       $consulta = new Consulta($query, $this -> conexion);
       $timlle = $consulta -> ret_matriz();

       $query = "UPDATE ".$bd_interf.".tab_despac_vehige
		    SET fec_salipl = '".$fec_sal."',
		        ind_activo = 'S',
		        fec_llegpl = '".$timlle[0][0]."',
		        usr_modifi = '".$this -> usuario."',
		        fec_modifi = '".$fec_actual."'
	          WHERE num_despac = '".$despac_cis."'
	        ";

    $insercion = new Consulta($query, $this -> conexion,"R");

    $query = "UPDATE ".$bd_interf.".tab_despac_despac
		SET fec_salida= '".$fec_sal."',
		    usr_modifi = '".$this -> usuario."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = '".$despac_cis."'
	    ";

    $insercion = new Consulta($query, $this -> conexion,"R");

  return true;

  }    

  function ins_noveda_interf($bd_interf,$cod_transp,$despac_sad,$pc_noveda, $noveda, $fec_noveda,$tiempo_adicis,$observ= "")
  {
   $fec_actual = date("Y-m-d H:i:s");

   //trae el despacho
   $despac_interf = $this -> get_despac_interf($bd_interf,$cod_transp,$despac_sad);

   //trae la novedad
   $cod_noveda_interf = $this -> get_noveda_interf($bd_interf,$cod_transp,$noveda);

   //trae el puesto de control
   $pc_interf = $this -> get_pc($bd_interf,$cod_transp,$pc_noveda);

   $query = "SELECT a.cod_rutasx,a.num_placax
               FROM ".$bd_interf.".tab_despac_vehige a
              WHERE a.num_despac = ".$despac_interf."
            ";

   $consulta = new Consulta($query,  $this -> conexion);
   $rut_interf = $consulta -> ret_matriz();

   if($this -> is_enruta($bd_interf,$rut_interf[0][1]))
   {
    $query = "SELECT a.cod_contro
                FROM ".$bd_interf.".tab_despac_seguim a
               WHERE a.num_despac = ".$despac_interf." AND
                     a.cod_rutasx = ".$rut_interf[0][0]." AND
                     a.cod_contro = ".$pc_interf."
             ";

    $consec = new Consulta($query, $this -> conexion,"R");
    $enplanru = $consec -> ret_matriz();

    if($enplanru)
    {
    //verificar el tipo de novedad
    $query = "SELECT cod_noveda, num_despac
                FROM ".$bd_interf.".tab_despac_noveda
               WHERE cod_noveda = '".$cod_noveda_interf."' AND
                     cod_rutasx = ".$rut_interf[0][0]." AND
                     num_despac = '".$despac_interf."' AND
                     cod_contro = '".$pc_interf."'";

    $consulta = new Consulta($query,  $this -> conexion);
    $existe = $consulta -> ret_matriz();

    //trae el ultimo reporte
    $report = $this -> ult_report($bd_interf,$despac_interf);

    if($fec_noveda <= $report )
    {
     echo "Fecha novedad menor a ultimo reporte";
     return false;
    }

    if(sizeof($existe) > 0)
    {
     echo "ya existe la novedad";
     return false;
    }
    else
    {
     //Verifica que el puesto de control se encuentre en ruta
     $query = "SELECT cod_contro
                 FROM ".$bd_interf.".tab_despac_seguim
                WHERE num_despac = '".$despac_interf."' AND
                      cod_contro = '".$pc_interf."' AND
                      cod_rutasx = ".$rut_interf[0][0]."
              ";

     $consulta = new Consulta($query,  $this -> conexion);
     $en_rutac = $consulta -> ret_vector();

     //valida que el puesto de control este homologado
     //si $pc_interf es nulo el sistema inserta el reporte de la novedad
     //como una nota de controlador
     if(!$tiempo_adicis)
      $tiempo_adicis = 0;

     if($pc_interf && $en_rutac)
     {
      $query = "INSERT INTO ".$bd_interf.".tab_despac_noveda
                            (num_despac,cod_rutasx,cod_contro,cod_noveda,tiem_duraci,fec_noveda,des_noveda,
			     usr_creaci,fec_creaci,usr_modifi,fec_modifi)
                     VALUES ('".$despac_interf."',".$rut_interf[0][0].", '".$pc_interf."',
                             '".$cod_noveda_interf."',".$tiempo_adicis.",'".$fec_noveda."','".$observ."',
                             'U.I.SAT', NOW(),
                             'U.I.SAT', NOW())";

      $insercion = new Consulta($query, $this -> conexion,"R");

      $query = "SELECT a.fec_planea
                  FROM ".$bd_interf.".tab_despac_seguim a,
                       ".$bd_interf.".tab_despac_vehige b
                 WHERE b.num_despac = a.num_despac AND
                       b.cod_rutasx = a.cod_rutasx AND
                       b.num_despac = '".$despac_interf."' AND
                       a.cod_contro = '".$pc_interf."'
               ";

      $consulta = new Consulta($query, $this -> conexion);
      $fecpla = $consulta -> ret_matriz();

      //trae los puestos de control que no han sido reportados
      $query = "SELECT a.cod_contro,c.val_duraci,c.val_duraci
                  FROM ".$bd_interf.".tab_despac_seguim a,
                       ".$bd_interf.".tab_despac_vehige b,
                       ".$bd_interf.".tab_genera_rutcon c LEFT JOIN
                       ".$bd_interf.".tab_despac_noveda d ON
                       a.num_despac = d.num_despac AND
                       a.cod_rutasx = d.cod_rutasx AND
                       a.cod_contro = d.cod_contro
                 WHERE a.num_despac = b.num_despac AND
                       a.cod_rutasx = b.cod_rutasx AND
                       c.cod_rutasx = a.cod_rutasx AND
                       c.cod_contro = a.cod_contro AND
                       a.fec_planea > '".$fecpla[0][0]."' AND
                       d.num_despac IS NULL AND
                       d.cod_contro IS NULL AND
                       d.cod_rutasx IS NULL AND
                       d.cod_noveda IS NULL AND
                       b.num_despac = '".$despac_interf."'
                       GROUP BY 1 ORDER BY 2
                ";

      $consulta = new Consulta($query, $this -> conexion);
      $contro = $consulta -> ret_matriz();

      //actualiza el estado del despacho
      //actualiza la hora de generacion de la alarma con base a la fecha ingresada por el usuario
      $query="UPDATE ".$bd_interf.".tab_despac_seguim
                 SET fec_alarma = '".$fec_noveda."',
                     usr_modifi = '".$this -> usuario."',
                     fec_modifi = '".$fec_actual."'
               WHERE num_despac = ".$despac_interf." AND
                     cod_rutasx = '".$rut_interf[0][0]."' AND
                     cod_contro = ".$pc_interf."
               ";

      $consulta = new Consulta($query, $this -> conexion,"R");

      //trae los sitios de pernoctacion del despacho
      $query = "SELECT cod_contro,val_pernoc
                  FROM ".$bd_interf.".tab_despac_pernoc
                 WHERE num_despac = '".$despac_interf."' AND
                       cod_rutasx = '".$rut_interf[0][0]."'
           ";

      $consulta = new Consulta($query, $this -> conexion);
      $pernoc = $consulta -> ret_matriz();

      for($i = 0; $i < sizeof($contro); $i++)
      {
       $pcontro[$i][0] = $contro[$i][0];
       $pcontro[$i][1] = $contro[$i][1];

       $indper = 0;

       for($j= 0; $j < sizeof($pernoc); $j++)
       {
        if($contro[$i][0] == $pernoc[$j][0])
        {
         $pcontro[$i][2] = 1;
         $pcontro[$i][3] = $pernoc[$j][1];
         $indper = 1;
         $j = sizeof($pernoc);
        }
       }

       if($indper == 0)
       {
        $pcontro[$i][2] = 0;
        $pcontro[$i][3] = 0;
       }
      }

      $tieacu = $GLOBALS[tiemp_adicis];

      for($i = 0; $i < sizeof($pcontro); $i++)
      {
       $tiepla = $tieacu + $pcontro[$i][1];

       $query = "UPDATE ".$bd_interf.".tab_despac_seguim
                    SET fec_alarma = DATE_ADD('".$fec_noveda."', INTERVAL ".$tiepla." MINUTE),
                        usr_modifi = '".$this -> usuario."',
                        fec_modifi = '".$fec_actual."'
                 WHERE num_despac = '".$despac_interf."' AND
                       cod_rutasx = '".$rut_interf[0][0]."' AND
                       cod_contro = '".$pcontro[$i][0]."'
                 ";

       $consulta = new Consulta($query, $this -> conexion,"R");

       if($pcontro[$i][3])
        $tieacu += $pcontro[$i][3];
      }

      $ultimo = $tieacu + $pcontro[sizeof($pcontro) - 1][1];

      $query = "UPDATE ".$bd_interf.".tab_despac_vehige
                   SET fec_llegpl = DATE_ADD('".$fec_noveda."', INTERVAL ".$ultimo." MINUTE),
                       usr_modifi = '".$this -> usuario."',
                       fec_modifi = '".$fec_actual."'
                 WHERE num_despac = '$despac_interf'
               ";

      $insercion = new Consulta($query, $this -> conexion,"R");
     }

     return true;
    }
   }
  }
  }//fin funcion

  function ins_salida($bd_interf,$cod_trans,$despac_sad,$rut_sad,$placa,$fec_salida,$puestos_sad)
  {
   //valida que el vehiculo no este en ruta
   if(!$this -> is_enruta($bd_interf,$placa))
   {
    //"inserta el plan de ruta en el sistema<br>";
    $this -> ins_planru($bd_interf,$cod_trans,$despac_sad,$rut_sad,$fec_salida,$puestos_sad);

    //"trae el despacho homologado<br>";
    $despac_cis = $this -> get_despac_interf($bd_interf,$cod_trans,$despac_sad);

    //actualiza el estado del despacho<br>";
    $this -> act_estado_despac($bd_interf,$despac_cis,"S");

    $query="UPDATE ".$bd_interf.".tab_despac_despac
                      SET fec_salida = '$fec_salida'
                      WHERE num_despac = '$despac_cis' ";

    $consulta = new Consulta($query, $this -> conexion,"R");
              return true;
   }
   else
   {
     return false;
   }
  }

  function ins_llegada($bd_interf,$cod_trans,$despac_sad,$fec_llegad,$obs,$ind_interf=1)

  {

      if($ind_interf == 1)

      {

      //trae el numero de despacho

      $despac_interf = $this -> get_despac_interf($bd_interf,$cod_trans,$despac_sad);

      }

      else

      {

      //trae el despacho

      $despac_interf = $despac;

      }

              $query = "SELECT num_placax

                       FROM ".$bd_interf.".tab_despac_vehige

                       WHERE num_despac = '$despac_interf'";

             $placa = new Consulta($query, $this -> conexion);

             $placa = $placa -> ret_arreglo();

           if($this -> is_enruta($bd_interf,$placa[0]))

           {

                   if($ultrep = $this -> ult_report($bd_interf,$despac_interf))

                   {

                      if ($fec_llegad <= $ultrep[0])

                      {



                      echo $mens_fec =  "La fecha de llegada debe ser mayor a la fecha de la ultima novedad";

                      return false;

                      }

                      else

                      {

                         //actualiza la hora de salida del despacho

                            $query="UPDATE ".$bd_interf.".tab_despac_despac

                                    SET fec_llegad = '$fec_llegad',

                                        obs_llegad = '$obs'

                                    WHERE num_despac = '".$despac_interf."' ";

                            $consulta = new Consulta($query, $this -> conexion,"R");

                            $this -> act_estado_despac($bd_interf,$despac_cis,"N");



                            return true;

                      }

                   }

                   else

                   {

                         //actualiza la hora de llegada del despacho

                            $query="UPDATE ".$bd_interf.".tab_despac_despac

                                    SET fec_llegad = '$fec_llegad',

                                        obs_llegad = '$obs'

                                    WHERE num_despac = '".$despac_interf."' ";

                            $consulta = new Consulta($query, $this -> conexion,"R");

                            $this -> act_estado_despac($bd_interf,$despac_cis,"N");

                            return true;



                   }

           }

           else

           {

           return false;

           }

  }



  function act_estado_despac($bd_interf,$desp,$estado)

  {



          $query = "UPDATE ".$bd_interf.".tab_despac_vehige

                    SET ind_activo = '$estado'

                    WHERE num_despac = '$desp'";

          $update = new Consulta($query, $this -> conexion,"R");

          return true;



  }



  function is_enruta($bd_interf,$placa)

  {

          $query = "SELECT a.num_placax

                     FROM ".$bd_interf.".tab_despac_vehige a,

                          ".$bd_interf.".tab_despac_despac b

                     WHERE a.num_despac = b.num_despac AND

                           b.fec_salida IS NOT NULL AND

                           b.fec_llegad IS NULL AND

                           a.num_placax = '$placa' AND

                           a.ind_activo = 'S'";

           $consulta = new Consulta($query, $this -> conexion);

           $enrut    = $consulta -> ret_arreglo();



           if($enrut)

           {

            return true;

           }

          else

          {

            return false;

          }

  }





  function ult_report($bd_interf,$despac)
  {
       $query="SELECT  MAX(e.fec_noveda)
               FROM ".$bd_interf.".tab_despac_vehige c,
                    ".$bd_interd.".tab_despac_seguim d,
                    ".$bd_interf.".tab_despac_noveda e
               WHERE c.num_despac = d.num_despac AND
                     c.cod_rutasx = d.cod_rutasx AND
                     c.num_despac = e.num_despac AND
                     c.cod_rutasx = e.cod_rutasx AND
                     c.num_despac = '".$despac."' ";

   $consulta = new Consulta($query, $this -> conexion);

   //fecha del ultimo reporte

    $ultrep = $consulta -> ret_arreglo();

    return $ultrep[0];

   }

   function get_client($cod_tercer)
   {
        $query = "SELECT a.cod_tercer,a.cod_tipdoc,CONCAT (a.nom_apell1,' ',a.nom_apell2,' ',a.nom_tercer),
                         a.abr_tercer,a.dir_domici,a.num_telef1,a.num_telmov,a.dir_emailx,a.cod_ciudad,NULL,
                         NULL,NULL,a.obs_tercer,if(a.cod_estado = '2','0',a.cod_estado)
                    FROM ".$this -> bd.".tab_genera_tercer a
                   WHERE a.cod_tercer = '".$cod_tercer."'
                 ";

        $consulta = new Consulta($query, $this -> conexion);
            $cliente = $consulta -> ret_arreglo();

        if($cliente)
                    return $cliente;
        else        return false;
   }

   function is_client($bd_interf,$cod_tercer)
   {
        $query = "SELECT a.cod_tercer
                    FROM ".$bd_interf.".tab_genera_tercer a
                   WHERE a.cod_tercer = '".$cod_tercer."'
                 ";

        $consulta = new Consulta($query, $this -> conexion);
            $cliente = $consulta -> ret_arreglo();

        if($cliente)
                return true;
        else        return false;
   }

   function is_activi_tercer($bd_interf,$cod_tercer,$cod_activi)
   {
        $query = "SELECT a.cod_tercer
                    FROM ".$bd_interf.".tab_genera_teract a
                 ";

        $consulta = new Consulta($query,$this -> conexion);
        $activi = $consulta -> ret_arreglo();

        if($activi)
                return true;
        else        return false;
   }

   function ins_activi_tercer($bd_interf,$cod_tercer,$cod_activi)
   {
        $query = "INSERT INTO ".$bd_interf.".tab_genera_teract
                              (cod_tercer,cod_activi,usr_creaci,fec_creaci)
                       VALUES ('".$cod_tercer."',".$cod_activi.",'Interfaz SAT',NOW())
                 ";

        $consulta = new Consulta($query,$this -> conexion,"R");
        if(!$consulta)
                return false;
        else        return true;
   }

   function get_noveda_interf($bd_interf,$cod_tercer,$cod_noveda){



         $query = "SELECT cod_noveda_cis

                  FROM ".$bd_interf.".tab_noveda_sad

                  WHERE cod_noveda_sad = '$cod_noveda' AND

                        cod_tercer     = '$cod_tercer'";

         $consulta = new Consulta($query, $this -> conexion);

         $noveda   = $consulta -> ret_arreglo();



         if($noveda[0])

         {

                  return $noveda[0];

         }

         else

         {

            $query = "SELECT nom_noveda

                      FROM ".$this -> bd.".tab_genera_noveda

                      WHERE cod_noveda = '$cod_noveda' ";



            if($this -> conexion_sat)

                $consulta = new Consulta($query, $this -> conexion_sat);

             else

                     $consulta = new Consulta($query, $this -> conexion);

            $noveda   = $consulta -> ret_arreglo();

            $mensaje =  "Debe Homologar la Novedad ".$noveda[0]." codigo $cod_noveda ";

             echo "<img src=\"../satb_standa/imagenes/ok.gif\">$mensaje <hr>";

            exit;

         }

        }

   function actualizar_tiempos($bd_interf,$cod_trans,$despac,$pc_interfaz,$fech1,$tiempo_noveda=0,$is_notcontro=0)
   {
    $fec_actual = date("Y-m-d H:i:s");

    $query = "SELECT a.cod_rutasx
                FROM ".$bd_interf.".tab_despac_vehige a
               WHERE a.num_despac = ".$despac."
             ";

    $consulta = new Consulta($query,  $this -> conexion);
    $rut_interf = $consulta -> ret_matriz();

    //trae el numero de despacho
    $despac_interf = $despac;

    //trae el puesto de control
    $pc_interf = $pc_interfaz;

    if(!$is_notcontro)
    {
           $query="UPDATE ".$bd_interf.".tab_despac_seguim
                      SET fec_alarma = '$fech1'
                    WHERE num_despac = '".$despac_interf."' AND
                          cod_contro = '".$pc_interf."' AND
                          cod_rutasx = ".$rut_interf[0][0]."
                        ";



         $consulta = new Consulta($query, $this -> conexion, "R");



         }







        $query = "SELECT a.cod_contro,c.val_duraci

                     FROM   ".$bd_interf.".tab_despac_seguim a,

                            ".$bd_interf.".tab_despac_vehige b,

                            ".$bd_interf.".tab_genera_rutcon c

                    WHERE a.num_despac = b.num_despac AND

                          b.cod_rutasx = c.cod_rutasx AND

                          a.cod_contro = c.cod_contro AND

                          a.num_despac = '".$despac_interf."' AND

                          a.cod_contro = '".$pc_interf."' ";

        $consulta = new Consulta($query, $this -> conexion);

        $actual = $consulta -> ret_matriz();







        //echo "//trae los puestos de pernoctacion del despacho<hr>";

         $query = "SELECT cod_contro,val_pernoc

                     FROM ".$bd_interf.".tab_despac_pernoc

                    WHERE num_despac = '".$despac_interf."'";

         $consulta = new Consulta($query, $this -> conexion);

         $pernoc = $consulta -> ret_matriz();





          $query = "SELECT a.cod_contro,c.val_duraci,c.val_duraci

                   FROM ".$bd_interf.".tab_despac_seguim a,

                        ".$bd_interf.".tab_despac_vehige b,

                        ".$bd_interf.".tab_genera_rutcon c LEFT JOIN

                        ".$bd_interf.".tab_despac_noveda AS d

                            ON a.num_despac = d.num_despac AND

                               a.cod_contro = d.cod_contro

                    WHERE a.num_despac = b.num_despac AND

                          b.cod_rutasx = c.cod_rutasx AND

                          a.cod_contro = c.cod_contro AND

                          a.num_despac = '".$despac_interf."' AND

                          d.num_despac Is Null AND

                          d.cod_contro Is Null

                     ORDER BY 2";



          $consulta = new Consulta($query, $this -> conexion);

          $contro = $consulta -> ret_matriz();



          //trae el tiempo al puesto de control

          $tiempo=$actual[0][1];



          if($tiempo_noveda)

             for($i=0; $i < sizeof($contro); $i++)

             {

                   $contro[$i][1] = $contro[$i][1]+$tiempo_noveda;

             }





          //los tiempos y puestos de control ha actualizar

          for($i=0;$i<sizeof($contro);$i++)

          {

           for($j=0;$j<sizeof($pernoc);$j++)

           {

            if($pernoc[$j][0] == $contro[$i][0])

            {

             $pernocta[$i]=$pernoc[$j][0];

             $tiempos[$i]=$pernoc[$j][1];

             $j=sizeof($pernoc)+1;

            }//fin if

            else

            {

             $pernocta[$i]=0;

             $tiempos[$i]=0;

            }//fin else

           }//fin for $j

          }//fin for $i



          //Recalcular los tiempos

          for($i=0;$i<sizeof($contro);$i++)

          {

           $contro[$i][1]=$contro[$i][1]-$tiempo;

           if($pernocta[$i] == $contro[$i][0])

           {

            $contro[$i][1]=$contro[$i][1]+$carry[$i];

            $carry[$i+1]  =$carry[$i]+$tiempos[$i];

           }//fin if

           else

           {

            $contro[$i][1]=$contro[$i][1]+$carry[$i];

            $carry[$i+1]  =$carry[$i];

           }//fin else

          }//fin for





          //ACTUALIZA TODO EL PLAN DE RUTA



          for($i=0; $i < sizeof($contro); $i++)

          {

             if($contro[$i][2] > $actual[0][1])

             {

                      $query = "UPDATE  ".$bd_interf.".tab_despac_seguim

                                SET  fec_alarma = DATE_ADD('$fech1', INTERVAL ".$contro[$i][1]." MINUTE),

                                     usr_modifi = '$GLOBALS[usuario]',

                                     fec_modifi = '$fec_actual'

                                WHERE num_despac = '".$despac_interf."' AND

                                      cod_contro = '".$contro[$i][0]."'";

                        $insercion = new Consulta($query, $this -> conexion, "R");

             }

          }//fin for $i



          //trae el ultimo registro de la matriz s/n su tamaï¿½



          $ult = sizeof($contro)-1;



          //trae el ultimo campo del resultado



          //garantiza que hay valor

          if(sizeof($contro) > 0)

             $ultimo = $contro[$ult][1];

          else

             $ultimo = 0;



           //echo "//actualiza la hora y fecha de llegada planeada s/n el ultimo puesto de control<hr>";

           $query = "UPDATE ".$bd_interf.".tab_despac_vehige

                    SET fec_llegpl = DATE_ADD('$fech1', INTERVAL ".abs($ultimo)." MINUTE)

                    WHERE num_despac = '".$despac_interf."' ";

          //echo "<hr>";

          $insercion = new Consulta($query, $this -> conexion, "R");

          return true;

   }//fin funcion

   function ins_genera_noveda($bd_interf,$cod_tercer,$noveda_sat,$noveda_int)
   {
        if($noveda_int == 0)
         return false;

        $query = "INSERT INTO ".$bd_interf.".tab_noveda_sad
                              (cod_tercer,cod_noveda_cis,cod_noveda_sad)
                       VALUES ('".$cod_tercer."',".$noveda_int.",".$noveda_sat.")
                 ";

        if(!$consulta = new Consulta($query, $this -> conexion,"R"))
         return false;
        else
         return true;
   }

   function act_genera_noveda($bd_interf,$cod_tercer,$novedad_sat,$noveda_int)
   {
        if($noveda_int == 0)
        {
          if($this -> eli_genera_noveda($bd_interf,$cod_tercer,$novedad_sat))
            return false;
        }

        if($this -> is_genera_noveda($bd_interf,$cod_tercer,$novedad_sat))
        {
          $query = "UPDATE ".$bd_interf.".tab_noveda_sad
                       SET cod_noveda_cis = ".$noveda_int."
                     WHERE cod_noveda_sad = '$novedad_sat' AND
                           cod_tercer     = '$cod_tercer' ";

          if(!$consulta = new Consulta($query, $this -> conexion,"R"))
            return false;
          else
            return true;
        }
        else
        {
          if($this -> ins_genera_noveda($bd_interf,$cod_tercer,$novedad_sat,$noveda_int))
            return true;
          else
            return false;
        }
   }

   function eli_genera_noveda($bd_interf,$cod_tercer,$novedad_sat)
   {
        $query = "DELETE FROM ".$bd_interf.".tab_noveda_sad
                        WHERE cod_noveda_sad = '".$novedad_sat."' AND
                              cod_tercer = '".$cod_tercer."' ";

        if(!$consulta = new Consulta($query, $this -> conexion,"R"))
          return false;
        else
          return true;
   }

   function is_genera_noveda($bd_interf,$cod_tercer,$novedad_sat)
   {
        $query = "SELECT cod_noveda_cis
                    FROM ".$bd_interf.".tab_noveda_sad
                   WHERE cod_noveda_sad = '$novedad_sat' AND
                         cod_tercer     = '$cod_tercer'";

        $consulta = new Consulta($query, $this -> conexion,"R");
        $existe = $consulta -> ret_arreglo();

        if($existe)
          return true;
        else
          return false;
   }

   function ins_notas($bd_interf,$cod_transp,$despac_sad,$pc_noveda,$pc_recalc,$noveda, $fec_noveda,$tiempo_adicis,$observ)
   {

      //trae el despacho
      $despac_interf = $this -> get_despac_interf($bd_interf,$cod_transp,$despac_sad);

      //trae la novedad
      $cod_noveda_interf = $this -> get_noveda_interf($bd_interf,$cod_transp,$noveda);

      $query = "SELECT a.cod_rutasx,a.num_placax
		  FROM ".$bd_interf.".tab_despac_vehige a
		 WHERE a.num_despac = ".$despac_interf."
	       ";

      $consulta = new Consulta($query,  $this -> conexion);
      $rut_interf = $consulta -> ret_matriz();

      //trae el puesto de control
      if($pc_interf = $this -> get_pc($bd_interf,$cod_transp,$pc_noveda))
         $pc_interf = $pc_interf;
      else
         $pc_interf = null;

     if($pc_interf && $this -> is_enruta($bd_interf,$rut_interf[0][1]))
     {

      $query = "SELECT a.cod_contro
		  FROM ".$bd_interf.".tab_despac_seguim a
		 WHERE a.num_despac = ".$despac_interf." AND
		       a.cod_rutasx = ".$rut_interf[0][0]." AND
		       a.cod_contro = ".$pc_interf."
		";

      $consec = new Consulta($query, $this -> conexion,"R");
      $enplanru = $consec -> ret_matriz();

      if($enplanru)
      {

      //trae el consecutivo de la tabla
      $query = "SELECT Max(cod_consec) AS maximo
               FROM ".$bd_interf.".tab_despac_contro
              WHERE num_despac = '".$despac_interf."' AND
		    cod_rutasx = ".$rut_interf[0][0]."
		";

   $consec = new Consulta($query, $this -> conexion,"R");
   $ultimo = $consec -> ret_matriz();

   $ultimo_consec = $ultimo[0][0];
   $nuevo_consec = $ultimo_consec+1;

   if(!$tiempo_adicis)
        $tiempo_adicis = 0;

   if($cod_noveda_interf == CONS_NOVEDA_PCLLEG)
   {
    $query = "SELECT (TIME_TO_SEC(TIMEDIFF('".$fec_noveda."', a.fec_alarma))/60) + ".$tiempo_adicis."
                FROM ".$bd_interf.".tab_despac_seguim a,
                     ".$bd_interf.".tab_despac_vehige b
               WHERE a.num_despac = ".$despac_interf." AND 
		     a.cod_contro = ".$pc_interf." AND 
		     b.num_despac = a.num_despac AND 
		     a.cod_rutasx = b.cod_rutasx";

    $consulta = new Consulta($query, $this -> conexion);
    $tiemprec = $consulta -> ret_matriz();

    $tiempo_adicis = $tiemprec[0][0];
   }

   //inserta la novedad
   $query = "INSERT INTO ".$bd_interf.".tab_despac_contro 
			 (num_despac, cod_rutasx,cod_consec,obs_contro,
			  cod_contro,cod_noveda,tiem_duraci,fec_contro,
			  usr_creaci, fec_creaci, usr_modifi, fec_modifi)
                 VALUES ('".$despac_interf."', ".$rut_interf[0][0].",'$nuevo_consec','$observ',$pc_interf,
                         '$cod_noveda_interf','$tiempo_adicis','$fec_noveda',
                         'U.I.SAT', NOW(), 'U.I.SAT', NOW())";

  $insercion = new Consulta($query, $this -> conexion,"R");

  $query = "SELECT b.cod_contro,DATE_ADD(b.fec_alarma, INTERVAL ".$tiempo_adicis." MINUTE)
              FROM ".$bd_interf.".tab_despac_seguim b,
                   ".$bd_interf.".tab_despac_vehige c
             WHERE b.cod_rutasx = c.cod_rutasx AND
                   b.num_despac = c.num_despac AND
                   c.num_despac = ".$despac_interf."
                   GROUP BY 1 ORDER BY 2
           ";

  $consulta = new Consulta($query, $this -> conexion);
  $planrut = $consulta -> ret_matriz();

  $tiemacu = $tiempo_adicis;
  $actact = 0;

  for($i = 0; $i < sizeof($planrut); $i++)
  {
   if($planrut[$i][0] == $pc_interf)
    $actact = 1;

   if($actact)
   {
    $query = "UPDATE ".$bd_interf.".tab_despac_seguim
                 SET fec_alarma = '".$planrut[$i][1]."',
                     usr_modifi = 'U.I.SAT',
                     fec_modifi = NOW()
               WHERE num_despac = ".$despac_interf." AND
                     cod_rutasx = '".$rut_interf[0][0]."' AND
                     cod_contro = '".$planrut[$i][0]."'
             ";

    $consulta = new Consulta($query, $this -> conexion,"R");
   }
  }

  $query = "UPDATE ".$bd_interf.".tab_despac_vehige
               SET fec_llegpl = DATE_ADD('".$planrut[sizeof($planrut) - 1][1]."', INTERVAL ".$tiemacu." MINUTE),
                   usr_modifi = 'U.I.SAT',
                   fec_modifi = NOW()
             WHERE num_despac = ".$despac_interf."
           ";

  $insercion = new Consulta($query, $this -> conexion,"R");

   return true;
   }
  }
  return false;

  }

}//fin clase





?>