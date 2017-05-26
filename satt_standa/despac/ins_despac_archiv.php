<?php

class Proc_ins_despac
{
    var $usuario,
        $conexion,
        $cod_aplica;

    function __construct($us, $co, $ca)
    {
        $this -> usuario = $us;
        $this -> conexion = $co;
        $this -> cod_aplica = $ca;
        $this -> principal();
    }

    function principal()
    {
     $this -> formulario();
     $is_scv = substr($_REQUEST["archivo"],0,-3);
     $validacion = true;
     #valido informacion del acrhivo adjunto
     if($_FILES["archivo"])
     {
        if($_FILES["archivo"]["error"] != "0")
        {
          $validacion = false;
          echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"> El archivo Contiene error.</img>";
          die();
        }
        if($_FILES["archivo"]["size"] >= $_REQUEST['MAX_FILE_SIZE'])
        {
          $validacion = false;
          echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"> El tamaño del archivo supera el maximo permitido. tamaño maximo(".$_REQUEST['MAX_FILE_SIZE'].").</img>";
          die();
        }

        if($_FILES["archivo"]["type"] != "text/csv" && $_FILES["archivo"]["type"] != "application/csv" && $_FILES["archivo"]["type"] != "application/vnd.ms-excel")
        {
          $validacion = false;
          echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"> El formato del archivo no es permitido. formato requerido CSV.</img>";
          die();
        }
     }
     else
     {
        $validacion = false;      
     }

     if( $validacion == true  )
     {
        $this -> insertar();
     }

    }

    function formulario()
    {
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];

        $formulario = new Formulario ("index.php","post\" enctype=\"multipart/form-data","IMPORTAR DESPACHOS","form_insert");
        $formulario -> linea("Cargar Despachos al Sistema por Medio de un Archivo Plano",1,"t2");
        $formulario -> nueva_tabla();
        $formulario -> archivo("Archivo", "archivo", 15, 255, 0, 1);
        $formulario -> caja ("Cargar Datos Nuevos","subir",1,1,1);
        $formulario -> linea("<a href='../".DIR_APLICA_CENTRAL."/despac/archiv_plan.xls'>Descargue la plantilla del archivo</a>",1);

        $formulario -> nueva_tabla();
        $formulario -> oculto("cod_servic", $_REQUEST["cod_servic"], 0, 0);
        $formulario -> oculto("usuario","$usuario",0);
        $formulario -> oculto("MAX_FILE_SIZE", "2000000", 0, 0);
        $formulario -> oculto("window", $_REQUEST["window"], 0, 0);
        $formulario -> boton("Aceptar","submit",0);
        $formulario -> cerrar();
    }

    //Funcion para la insercion del registro
    function insertar()
    {
        $datos_usuario = $this -> usuario -> retornar();
        $fec_creaci = new Fecha();
        $mensaje_reg = 0;
        $mensaje_cod = 0;
        $i = 1;

        if(!$archivo = fopen($_FILES['archivo']['tmp_name'], "r"))
        {
        	echo "<br>No se Pudo Abrir el Archivo";
        }
        else
        {
            $e = 0;
            while ($datos = fgetcsv ($archivo, 1000, ","))
            {
               $num_campos = count($datos);
                $nitcle = $datos[3];
                $indnit = 0;

                $permitidos = "0123456789";
                for ($i=0; $i<strlen($nitcle); $i++)
                {
                        if (strpos($permitidos, substr($nitcle,$i,1))===false)
                        {
                                $indnit = 1;
                        }
                }
                
                if($indnit)
                {
                   if($datos[0]!="Manifiesto")
                   {
                    echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"> El NIT ".$nitcle." Relacionado al manifiesto # ".$datos[0]." debe ir sin Puntos,Comas, Guiones u otro tipo de Caracteres.</img>";
                   }
                }
                else if(strlen($nitcle) > 10)
                {
                     echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"> El NIT ".$nitcle." Relacionado al manifiesto # ".$datos[0]." no debe Contener mas de 10 digitos.</img>";
                }
                else
                {
                        $consulta = new Consulta("START TRANSACTION", $this -> conexion);
                        $query = "SELECT a.num_despac, a.cod_manifi, a.ind_anulad
                                    FROM ".BASE_DATOS.".tab_despac_despac a
                                      WHERE a.cod_manifi = '".$datos[0]."' ";
                        $consulta = new Consulta($query, $this -> conexion);
                        $mDespacho = $consulta -> ret_arreglo();
                        if($mDespacho[0]['ind_anulad']=='A' || $mDespacho==FALSE)
                        {
                          if(!$mensaje_reg = $this -> validar_registro($datos))
                          {
                                  $this -> Subir($datos);
                          }
                          else
                          {
                                  if($e == 0)
                                  {
                                          $formulario = new Formulario ("index.php\" ","post","<b>RESULTADOS</b>","f");
                                          $formulario -> linea("INFORMACION DE LA CARGA DEL ARCHIVO",1);
                                          $formulario -> linea("Los Siguientes Datos No subieron al Sistema",0);
                                          $formulario -> nueva_tabla();
                                  }

                                  echo "<br>Manifiesto ".$datos[0]." | ".$mensaje_reg;
                                  echo "<hr>";
                                  $e++;
                          }
                        }
                        else
                        {
                            echo "<br>Manifiesto ".$datos[0]." ya se encuenta registrado con el despacho: ".$mDespacho[0];
                            echo "<hr>";
                        }
                }
            }

            if($formulario)
            $formulario -> cerrar();
        }
        fclose($archivo);
    }

    function validar_registro($registro)
    {
        $query = "SELECT a.nom_ciudad, b.nom_ciudad
                  FROM ".BASE_DATOS.".tab_genera_ciudad a,
                         ".BASE_DATOS.".tab_genera_ciudad b
                  WHERE a.cod_ciudad = '".$registro[31]."' AND
                        b.cod_ciudad = '".$registro[32]."'";
        $consulta = new Consulta($query, $this -> conexion);
        $ciudades = $consulta -> ret_arreglo();

      //valida la ruta
      $query = "SELECT a.cod_rutasx
                  FROM ".BASE_DATOS.".tab_genera_rutasx a
                 WHERE a.cod_ciuori = '".$registro[31]."' AND
                       a.cod_ciudes = '".$registro[32]."' AND
		       a.ind_estado = '1'
                       GROUP BY a.cod_rutasx  ";
       $consulta = new Consulta($query, $this -> conexion);

       if(!$existe = $consulta -> ret_arreglo())
       {
            $mensaje = "La ruta Origen " .$ciudades[0]." Destino ".$ciudades[1]." No esta creada o no esta Asignada a la transportadora " ;
    	 }


             //Valida la agencia
             $query    = "SELECT cod_agenci FROM ".BASE_DATOS.".tab_genera_agenci WHERE cod_agenci = '".$registro[1]."'";
             $consulta = new Consulta($query, $this -> conexion);
             $agenci   = $consulta -> ret_arreglo();

             if(!$agenci[0])
             {
                  if($_REQUEST[subir])
                  {
                  $this -> ins_agenci($registro[1],$registro[2]);
                  echo "crea la agencia ";
                  }
                  else
                  {
                     $mensaje = "Agencia " . $registro[1];
                  }
             }

             //valida que el NIT del Generador no sea mayor a 10
             //digitos.

             if(COUNT($registro[3]) > 10)
             {
                     $mensaje .= "|Generador ".$registro[3]." Nit con mas de 10 caracteres.";
             }
             else
             {
                     //valida que el generador exista en la BD
                     //codigo del generador 10
                     $generador =    $this -> is_tercer($registro[3]);

                     if($generador == 0)
                     {
                             if($_REQUEST[subir])
                             {
                                     $this -> ins_tercer($registro[3],$registro[4],"","N");
                                     $this -> act_activi($registro[3],3);//cambio el generado a 3 estaba en 10
                                     echo "<br>Crea el Generador";
                             }
                             else
                             {
                                     $mensaje .= "|".$registro[4];
                             }
                     }

                     //si el generador existe valida que tenga la actividad Generador
                     if($generador == 1)
                     {
                             //Codigo del Generador = 10
                             $activi = $this -> is_activi($registro[3],3);//cambio el generado a 3 estaba en 10
                             if(!$activi)
                             {
                                     if($_REQUEST[subir])
                                     {
                                             $this -> act_activi($registro[3],3);//cambio el generado a 3 estaba en 10
                                             echo "<br>Actualiza la actividad del generador<br>";
                                     }
                                     else
                                     {
                                             $mensaje .= "|Generador ".$registro[3]." sin Actividad";
                                     }
                             }
                     }//fin activi
              }

             //valida que el conductor exista en la BD
             //codigo conductor 16
             $conduc =    $this -> is_tercer($registro[12]);
             if($conduc == 0)
             {
                  if($_REQUEST[subir])
                  {
                    $nombre = $registro[13]." ".$registro[14]." ".$registro[15];
                    $this -> ins_conpos($registro[12],$nombre,$registro[14],$registro[15],$registro[16],"C",$registro[17]);
                    echo "<br>Crea el Conductor<br>";
                    $conduc = 1;
                  }
                  else
                  {
                     $mensaje .= "|Conductor ".$registro[12]." ".$registro[13];
                     $this -> act_celular($registro[12],$registro[16]);
                  }
             }

             //si el Conductor existe valida que tenga la actividad
             if($conduc == 1)
             {
                $this -> act_celular($registro[12],$registro[16]);
                //Codigo del Conductor = 16
                $activi = $this -> is_activi($registro[12],4);//cambio la actividad a 4 estaba en 16

                  if(!$activi)
                  {
                      if($_REQUEST[subir])
                      {
                         $this -> act_activi($registro[12],4);//cambio la actividad a 4 estaba en 16
                         echo "<br>Actualiza la actividad del Conductor<br>";
                      }
                      else
                      {
                         $mensaje .= "|Conductor ".$registro[12]." sin Actividad";
                      }
                  }
             }//fin activi

             //valida que el Poseedor exista en la BD
             //codigo poseedor = 18
             $poseed =  $this -> is_tercer($registro[18]);
             if($poseed == 0)
             {
                  if($_REQUEST[subir])
                  {
                    $nombre = $registro[19]." ".$registro[20]." ".$registro[21];
                    $this -> ins_conpos($registro[18],$nombre,$registro[14],$registro[15],$registro[22],"C",$registro[23]);
                    $activi = $this -> is_activi($registro[18],6);//cambio la actividad a 4 estaba en 18
                     echo "<br>Crea el Poseedor";
                     $poseed=1;
                  }
                  else
                  {
                     $mensaje .= "|Poseedor ".$registro[18]." ".$registro[19];
                  }
             }

             //si el Poseedor existe valida que tenga la actividad
             if($poseed == 1)
             {
                //Codigo del Poseedor = 18
                $activi = $this -> is_activi($registro[18],6);//cambio la actividad a 4 estaba en 18

                  if(!$activi)
                  {
                      if($_REQUEST[subir])
                     {
                         $this -> act_activi($registro[18],6);//cambio la actividad a 4 estaba en 18
                         echo "<br>Actualiza la actividad del Poseedor<br>";
                      }
                      else
                      {
                         $mensaje .= "|Poseedor ".$registro[18]." sin Actividad";
                      }
                  }

             }//fin activi

             //valida que el Propietario exista en la BD
             //codigo propietario = 15
             $propie =    $this -> is_tercer($registro[24]);
             if($propie == 0)
             {
                  if($_REQUEST[subir])
                  {
                    $nombre = $registro[25]." ".$registro[26]." ".$registro[27];
                    $this -> ins_tercer($registro[24],$nombre, $registro[28], "C", $registro[29]);
                    $this -> act_activi($registro[24],5);//cambio la actividad a 5 estaba en 15
                     echo "<br>Crea el Propietario";
                     $propie =1;
                  }
                  else
                  {
                     $mensaje .= "|Propietario ".$registro[24]." ".$registro[25];
                  }
             }

             //si el Propietario existe valida que tenga la actividad
             if($propie == 1)
             {
                //Codigo del Propietario = 15
                $activi = $this -> is_activi($registro[24],5);//cambio la actividad a 5 estaba en 15
                  if(!$activi)
                  {
                      if($_REQUEST[subir])
                      {
                         $this -> act_activi($registro[24],5);//cambio la actividad a 5 estaba en 15
                         echo "<br>Actualiza la actividad del Propietario<br>";
                      }
                      else
                      {
                         $mensaje .= "|Propietario ".$registro[24]." sin Actividad";
                      }
                  }

             }//fin activi

             $query = "SELECT num_placax
                       FROM tab_vehicu_vehicu
                       WHERE num_placax = '".$registro[5]."'";
             $consulta = new Consulta($query, $this -> conexion);
             $placa      = $consulta -> ret_arreglo();

              if(!$placa[0])
              {
                /*se inabilita opcion ya que los codigos enviados son diferentes
                if($_REQUEST[subir])
                {
                     $this ->  ins_vehicu($registro[5],$registro[6], $registro[7],$registro[8],$registro[9],
                                          $registro[10],$registro[11],$registro[12],$registro[18],$registro[24]);
                      echo "<br>Actualiza el vehiculo<br>";

                }
                else
                {
                 $mensaje .= "|Vehiculo ".$registro[5];
                }*/
                $mensaje .= "|Vehiculo ".$registro[5]." no se encuenta registrado, registre el vehiculo en la plataforma e intente nuevamente.";
             }
/*
             if(!$this -> is_linea($registro[6], $registro[7]))
             {
                 $mensaje .= "| La Linea ".$registro[6].'-'.$registro[7]." No es Valida";
             }
             if(!$this -> is_color($registro[9]))
             {
                 $mensaje .= "| El Codigo de la Color ".$registro[9]." No es Valido";
             }
             if(!$this -> is_carroc($registro[10]))
             {
                 $mensaje .= "| El Codigo de la Carroceria ".$registro[10]." No es Valida";
             }
             if(!$this -> is_config($registro[11]))
             {
                 $mensaje .= "| La Configuracion ".$registro[11]." No es Valida Segun el Estandar del Ministerio de                                    Transporte";
             }*/
             /*No existe tabla en gl por eso la omito es
             $query = "SELECT cod_mercan
                       FROM ".BASE_DATOS.".tab_genera_mercan
                       WHERE cod_mercan = '".$registro[30]."'";
             $consulta = new Consulta($query, $this -> conexion);
             $mercan   = $consulta -> ret_arreglo();

             if(!$mercan[0])
             {
                 if($_REQUEST[subir])
                 {
                    $this ->  ins_mercan($registro[30]);
                    echo "Actualiza la mercancia<br>";
                 }
                 else
                 {
                    $mensaje .= "|Mercancia ".$registro[30];
                 }
             }
              */
             //query ciuori
             $query = "SELECT cod_ciudad
                       FROM  ".BASE_DATOS.".tab_genera_ciudad
                       WHERE cod_ciudad = '".$registro[31]."'";
             $consulta = new Consulta($query, $this -> conexion);
             $origen   = $consulta -> ret_arreglo();

             if(!$origen[0])
             {
                 if($_REQUEST[subir])
                 {
                   echo "Actualiza la Ciudad<br>";
                 }
                 else
                 {
                   $mensaje .= "|Origen ".$registro[31];
                 }

             }

             //query ciuori

             $query = "SELECT cod_ciudad
                       FROM  ".BASE_DATOS.".tab_genera_ciudad
                       WHERE cod_ciudad = '".$registro[32]."'";
             $consulta = new Consulta($query, $this -> conexion);
             $origen   = $consulta -> ret_arreglo();

             if(!$origen[0])
             {
                 if($_REQUEST[subir])
                 {
                   echo "Actualiza la Ciudad";
                  }
                 else
                  {
                   $mensaje .= "|Destino".$registro[32];
                  }
             }

             //Valida tipo de despacho
             $query = "SELECT cod_tipdes
                       FROM ".BASE_DATOS.".tab_genera_tipdes
                       WHERE cod_tipdes = '".$registro[33]."'";
             $consulta = new Consulta($query, $this -> conexion);
             $tip_des  = $consulta -> ret_arreglo();

             if(!$tip_des[0])
             {
                     if($registro[33] != "0")
                           $mensaje .= "|Tipo Despacho".$registro[33];
             }
              return $mensaje;
    }

    function Subir($registro)
    {
       $fec_actual = date("Y-m-d H:i:s");
       //trae la aseguradora del despacho
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];

       $query = "SELECT a.cod_tercer
                 FROM ".BASE_DATOS.".tab_tercer_tercer a,
                      ".BASE_DATOS.".tab_tercer_activi b
                 WHERE a.cod_tercer = b.cod_tercer AND
                       b.cod_activi = '5' ";
       $consulta = new Consulta($query, $this -> conexion, "R");
       $asegra   = $consulta -> ret_arreglo();
       $asegra   = $asegra[0];


       //Trae la ruta del despacho
      $query = "SELECT a.cod_rutasx
                 FROM ".BASE_DATOS.".tab_genera_rutasx a
                 WHERE a.cod_ciuori = '".$registro[31]."' AND
                       a.cod_ciudes = '".$registro[32]."' AND
		       a.ind_estado = '1'
                       GROUP BY a.cod_rutasx  ";
       $consulta = new Consulta($query, $this -> conexion);
       if($ruta     = $consulta -> ret_arreglo())
               $ruta     = $ruta[0];

       //trae el consecutivo de la tabla
       $query = "SELECT Max(num_despac) AS maximo
                 FROM ".BASE_DATOS.".tab_despac_despac ";
       $consec = new Consulta($query, $this -> conexion);
       $ultimo = $consec -> ret_matriz();

       $nuevo_consec =  $ultimo[0][0]+1;

       if($registro[33] == "0")
      {
       $registro[33] = "2";
      }

      if($registro[36] == "0")
      {
       $retefu = $registro[36]*0.01;
      }
      else
      {
       $retefu = $registro[36];
      }

      $query = "SELECT num_despac
                FROM ".BASE_DATOS.".tab_despac_despac
                WHERE cod_manifi = '".$registro[0]."'";
      $consulta = new Consulta($query, $this -> conexion);
      $is_manifi= $consulta -> ret_arreglo();


      /*pais de origen para gl es*/
      $query = "SELECT a.cod_paisxx,a.cod_depart
            FROM ".BASE_DATOS.".tab_genera_ciudad a
          WHERE a.cod_ciudad = ".$registro[31]." ";
    
      $consulta = new Consulta($query, $this -> conexion);
      $paidepori = $consulta -> ret_matriz();
      
      $query = "SELECT a.cod_paisxx,a.cod_depart
            FROM ".BASE_DATOS.".tab_genera_ciudad a
            WHERE a.cod_ciudad = ".$registro[32]." ";

      $consulta = new Consulta($query, $this -> conexion);
      $paidepdes = $consulta -> ret_matriz();

      //Se obtiene la informacion del conductor
      $query = "SELECT a.num_telef1, a.num_telmov, a.dir_domici
            FROM ".BASE_DATOS.".tab_tercer_tercer a
            WHERE a.cod_tercer = ".$registro[12]." ";

      $consulta = new Consulta($query, $this -> conexion);
      $conduc = $consulta -> ret_matriz();

      //Se trae la aseguradora y numero de poliza actual
      /*esta tabla no existe en gl es$query = "SELECT a.cod_asegra,a.num_poliza
                  FROM ".BASE_DATOS.".tab_poliza_tercer a
                 WHERE a.cod_tercer = '".NIT_TRANSPOR."' AND
                       a.fec_modifi = (select Max(b.fec_modifi) from tab_poliza_tercer b where b.cod_tercer = a.cod_tercer)
            ";

      $consulta = new Consulta($query, $this -> conexion);
      $datospoli = $consulta -> ret_arreglo();*/

      if(!$is_manifi)
      {
       if(!$registro[41])
        $registro[41] = "1";
       if(!$registro[42])
        $registro[42] = "1";

       //query de insercion de despachos
       /*$query = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
                 (`num_despac`, `cod_manifi`, `fec_despac`, `cod_tipdes`,
                  `cod_ciuori`, `cod_ciudes`, `val_flecon`, `val_despac`,
                  `val_antici`, `val_retefu`, `nom_carpag`, `nom_despag`,
                  `cod_agedes`, `fec_pagoxx`, `cod_unimed`, `cod_natemp`,
                  `obs_despac`, `num_carava`, `ind_planru`, `ind_anulad`,
                   cod_asegra ,  num_poliza,
                  `usr_creaci`, `fec_creaci`, `usr_modifi`,  `fec_modifi`)

                 VALUES ('$nuevo_consec','".$registro[0]."','$fec_actual','".$registro[33]."',
                 '".$registro[31]."','".$registro[32]."','".$registro[35]."','".$registro[36]."',
                 '".$registro[37]."','$retefu','".$registro[38]."','".$registro[39]."','".$registro[1]."',
                 '".$registro[40]."','".$registro[41]."','".$registro[42]."','Insertado por Interfaz Web',
                 '".$registro[34]."','N','R','".$datospoli[0]."','".$datospoli[1]."','$_REQUEST[usuario]','$fec_actual','$_REQUEST[usuario]','$fec_actual') ";*/
      
        //val_declara y val_pesoxx lo dejo en nulo porq no encuentro campo
        //gps_operad, gps_usuari, gps_paswor, cod_asegur, num_poliza, num_carava los quito porque no estan en la tabla
        $query = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
          (
            num_despac, cod_manifi, fec_despac,

            cod_client, cod_paiori, cod_depori,

            cod_ciuori, cod_paides, cod_depdes,

            cod_ciudes, val_flecon, val_despac,

            val_antici, val_retefu, nom_carpag,

            nom_despag, cod_agedes, fec_pagoxx,

            obs_despac, val_declara, usr_creaci,

            fec_creaci, val_pesoxx, con_telef1,

            con_telmov, con_domici, cod_tipdes

            
          )
          VALUES 
          (
            ".$nuevo_consec.", '".$registro[0]."', '$fec_actual', 

            ".$registro[3].", ".$paidepori[0][0].", ".$paidepori[0][1].",

            ".$registro[31].", ".$paidepdes[0][0].", ".$paidepdes[0][1].",

            ".$registro[32].", NULL, NULL,

            NULL, NULL, NULL,

            NULL, '".$registro[1]."', NULL,

            '".$registro[47]."', NULL, '".$_REQUEST[usuario]."',

            '$fec_actual', NULL, '".$conduc[0][0]."',

            '".$conduc[0][1]."', '".$conduc[0][2]."', '".$registro[33]."'
          )";

       $consulta = new Consulta($query, $this -> conexion, "R");

       $query = "SELECT a.clv_filtro
            FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a
            WHERE a.cod_perfil = ".$datos_usuario['cod_perfil']." ";

       $consulta = new Consulta($query, $this -> conexion);
       $nit_trans = $consulta -> ret_matriz();      
       $nit_trans = $nit_trans[0][clv_filtro];
       //query de insercion de despachos vehiculos
        $query = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
                 (num_despac, cod_transp, cod_agenci, cod_rutasx,
                  cod_conduc, num_placax, obs_medcom, fec_salipl,
                  fec_llegpl, ind_activo, usr_creaci,
                  fec_creaci, usr_modifi, fec_modifi)
                 VALUES ('$nuevo_consec','".$nit_trans."','".$registro[1]."','$ruta',
                 '".$registro[12]."','".$registro[5]."','".$registro[16]."',
                 '$fec_actual','$fec_actual','R','$_REQUEST[usuario]','$fec_actual','$_REQUEST[usuario]','$fec_actual') ";

       //$consulta = new Consulta($query, $this -> conexion, "R");
       $consulta = new Consulta($query, $this -> conexion, "RC");
       //$this -> ins_remesa($nuevo_consec,$registro[0],$registro[46],$registro[43], $registro[44],$registro[45],$registro[30],$registro[3],$registro[38],$registro[39]);

          echo "<br><b>Se ha subido el Despacho $nuevo_consec con el Manifiesto ".$registro[0]." correctamente al Sistema al sistema </b><br>";
      }//fin manifi
      else
      {
                 //ins_remesa($despac,$manifi,$remesa,$peso, $volumen,$empaque,$mercan,$client,$remite,$destin)
          //$this -> ins_remesa($is_manifi[0],$registro[0],$registro[46],$registro[43], $registro[44],$registro[45],$registro[30],$registro[3],$registro[38],$registro[39]);
      }
    }//fin funcion


    function is_tercer($cod_tercer)
    {
             //verifica que el generador exista
             $query = "SELECT a.cod_tercer
                       FROM ".BASE_DATOS.".tab_tercer_tercer a
                       WHERE a.cod_tercer = '".$cod_tercer."'";
             $consulta = new Consulta($query, $this -> conexion);

             if($tercer   = $consulta -> ret_arreglo())
                {
                 return 1;
                }
             else
                {
                return 0;
                }

    }

    function is_activi($cod_tercer,$cod_activi)
    {
             //verifica que el tercero tenga la actividad requerida
             $query = "SELECT a.cod_tercer
                       FROM ".BASE_DATOS.".tab_tercer_activi a
                       WHERE a.cod_tercer = '".$cod_tercer."' AND
                             a.cod_activi = '".$cod_activi."'";

             $consulta = new Consulta($query, $this -> conexion);


             if($activi   = $consulta -> ret_arreglo())
                return true;
             else
                return false;
    }

    function act_celular($cod_tercer,$celular)
    {
             //actualiza el numero del celular del tercero o conductor
             $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer set num_telmov = '".$celular."'
                            WHERE cod_tercer = '".$cod_tercer."' ";
             $consulta = new Consulta($query, $this -> conexion,"R");

    }

   //crear la agencia si no existe
   function ins_agenci($cod,$nombre)
   {
           $query = "INSERT INTO ".BASE_DATOS.".tab_genera_agenci (cod_agenci,
                    nom_agenci,cod_ciudad,usr_creaci, fec_creaci)
                     VALUES ('$cod','$nombre', '1','$_REQUEST[usuario]',NOW())";
           $insert = new Consulta($query, $this -> conexion, "R");
   }
    // inserta el conductor o poseedor
    function ins_conpos($nit,$nombre,$apell1,$apell2,$tel = "",$tipdoc=1,$direcc="")
    {

            $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer (cod_tercer,nom_apell1,nom_apell2,cod_tipdoc,nom_tercer,
                      abr_tercer, num_telmov, dir_domici,cod_paisxx,cod_depart,cod_ciudad, usr_creaci, fec_creaci)
                      VALUES ('$nit','$apell1','$apell2','$tipdoc','$nombre','$nombre','$tel', '$direcc' ,'3','1',
                      '1', '$_REQUEST[usuario]', NOW())";
            $insert = new Consulta($query, $this -> conexion,"R");

           //conduc = 16, propiet = 15, genera = 10

    }
    function ins_tercer($nit,$nombre,$tel = "",$tipdoc=1,$direcc="")
    {

            $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer (cod_tercer,cod_tipdoc,nom_tercer,
                      abr_tercer, num_telmov, dir_domici,cod_paisxx,cod_depart,cod_ciudad, usr_creaci, fec_creaci)
                      VALUES ('$nit','$tipdoc','$nombre','$nombre','$tel', '$direcc' ,'3','1',
                      '1', '$_REQUEST[usuario]', NOW())";
            $insert = new Consulta($query, $this -> conexion,"R");

           //conduc = 16, propiet = 15, genera = 10

    }

    function act_activi($nit,$activi)
    {
           $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi (cod_tercer,cod_activi)
                      VALUE ('$nit','$activi') ";
            $insert = new Consulta($query, $this -> conexion, "R");

            if($activi == 4)
            {
                     $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_conduc (cod_tercer, cod_tipsex, cod_grupsa,
                                          cod_califi, usr_creaci, fec_creaci,num_catlic)
                             VALUES ('$nit','1','O (+)','1','$_REQUEST[usuario]', NOW(),1)";
                    $insert = new Consulta($query, $this -> conexion, "R");

            }
            /*se comenta porque esa tabla no existe en gl
            if($activi == 3)
            {

                    $query = "INSERT INTO tab_tercer_client (cod_tercer, cod_estper, cod_terreg,
                                          usr_creaci, fec_creaci)
                             VALUES ('$nit','1','1','$_REQUEST[usuario]', NOW())";
                    $insert = new Consulta($query, $this -> conexion, "R");

            }*/
    }

    //si los datos no cuadran debe ingresar datos no registrados
             //ins_vehicu($registro[5],$registro[6], $registro[7],$registro[8],$registro[9],$registro[10],$registro[11],$registro[12],$registro[18],$registro[24]);
    function ins_vehicu($placa,$marca, $linea,$modelo,$color,$carroc,$config,$cod_conduc,$cod_poseed,$cod_propie)

    {

               /*$query = "SELECT a.cod_asegra
                 		   FROM ".BASE_DATOS.".tab_poliza_tercer a
                		  WHERE a.cod_tercer = '".NIT_TRANSPOR."' AND
                      			a.fec_modifi = (select Max(b.fec_modifi) from tab_poliza_tercer b where b.cod_tercer = a.cod_tercer)
                         ";

               $cod_asesoa = new Consulta($query, $this -> conexion);
               $cod_asesoa = $cod_asesoa -> ret_arreglo();
               $cod_asesoa = $cod_asesoa[0];


              $query = "INSERT INTO ".BASE_DATOS.".tab_vehicu_vehicu
                         (num_placax,cod_marcax,cod_lineax,ano_modelo,
                          cod_asesoa,
                          cod_colorx,cod_carroc,num_config,cod_propie,
                          cod_tenedo,cod_conduc,
                          usr_creaci,fec_creaci,cod_tipveh,
                          cod_califi)
                         VALUE ('$placa','$marca','$linea','$modelo',
                                 '$cod_asesoa',
                                 '$color','$carroc','$config',
                                 '$cod_propie','$cod_poseed','$cod_conduc',
                                 '$_REQUEST[usuario]', NOW(),1,1) ";
               $insert = new Consulta($query, $this -> conexion, "R");*/

              $query = "INSERT INTO ".BASE_DATOS.".tab_vehicu_vehicu
                         (num_placax,cod_marcax,cod_lineax,ano_modelo,
                          cod_colorx,cod_carroc,num_config,cod_propie,
                          cod_tenedo,cod_conduc,
                          usr_creaci,fec_creaci,cod_tipveh,
                          cod_califi)
                         VALUE ('$placa','$marca','$linea','$modelo',
                                 '$color','$carroc','$config',
                                 '$cod_propie','$cod_poseed','$cod_conduc',
                                 '$_REQUEST[usuario]', NOW(),1,1) ";
               $insert = new Consulta($query, $this -> conexion, "R");

    }//fin ins_vehicu

    //funcion que verifica si existe la linea del vehiculo
    function is_linea($marca,$linea)
    {
           $query = "SELECT cod_lineax
                     FROM ".BASE_DATOS.".tab_vehige_lineas
                     WHERE  cod_marcax = '".$marca."' AND
                            cod_lineax = '".$linea."' ";
           $consulta = new Consulta($query, $this -> conexion);
           if($consulta -> ret_arreglo())
               return true;
           else
                return false;
    }
    //funcion que verifica si existe la configuracion del vehiculo
    function is_config($config)
    {

                $query = "SELECT num_config
                       FROM tab_vehige_config
                       WHERE num_config = '".$config."'";
                $consulta = new Consulta($query, $this -> conexion);
                if($consulta -> ret_arreglo())
                   return true;
                else
                   return false;


    }
    //funcion que verifica si existe el Color del vehiculo
    function is_color($color)
    {

                $query = "SELECT cod_colorx
                       FROM tab_vehige_colore
                       WHERE cod_colorx = '".$color."'";
                $consulta = new Consulta($query, $this -> conexion);
                if($consulta -> ret_arreglo())
                   return true;
                else
                   return false;


    }
    //funcion que verifica si existe la carroceria del vehiculo
    function is_carroc($carroc)
    {

                $query = "SELECT cod_carroc
                       FROM tab_vehige_carroc
                       WHERE cod_carroc = '".$carroc."'";
                $consulta = new Consulta($query, $this -> conexion);
                if($consulta -> ret_arreglo())
                   return true;
                else
                   return false;


    }
    function ins_mercan($cod)
    {
           $query = "SELECT cod_minmer
                     FROM ".BASE_DATOS.".tab_minist_mercan
                     WHERE  cod_minmer = '".$cod."'";
           $is_merca = new Consulta($query, $this -> conexion);
           if($is_merca->ret_num_rows())
           {
                      $query = "INSERT INTO ".BASE_DATOS.".tab_genera_mercan (cod_mercan,abr_mercan,
                               nom_mercan,cod_minmer,ind_activa, usr_creaci, fec_creaci) SELECT cod_minmer, nom_minmer, nom_minmer, cod_minmer, '1', '$_REQUEST[usuario]', NOW()
                                FROM ".BASE_DATOS.".tab_minist_mercan
                                WHERE cod_minmer = '$cod'";
                      $insert = new Consulta($query, $this -> conexion, "R");
           }
           else
           {
               echo "<br><font=\"RED\"><b>La Mercancia Codigo $cod no se encuentra registrada como mercancia del ministerio</b></font><br>";
               exit;
           }
    }

    function ins_remesa($despac,$manifi,$remesa,$peso, $volumen,$empaque,$mercan,$client,$remite,$destin)
    {

           echo $query = "SELECT cod_remesa
                     FROM ".BASE_DATOS.".tab_despac_remesa
                     WHERE  cod_remesa = '".$remesa."' AND
                            cod_manifi = '".$manifi."'";
           $is_reme = new Consulta($query, $this -> conexion, "R");

           if($is_reme -> ret_arreglo())
           {
              echo $algo = "<br>La remesa $remesa ya esta Registrada con el manifiesto $manifi <br>";
              $reme = new Consulta("ROLLBACK", $this -> conexion);
           }
           else
           {
           $query = "INSERT INTO ".BASE_DATOS.".tab_despac_remesa
                     (cod_remesa, num_despac,cod_manifi,
                      val_pesoxx, val_volume,cod_empaqu,
                      cod_mercan, cod_client,nom_remite,
                      nom_destin, usr_creaci,fec_creaci)
                     VALUES ('$remesa','$despac','$manifi',
                             '$peso','$volumen','$empaque',
                             '$mercan','$client','$remite',
                             '$destin','$_REQUEST[usuario]', NOW())";
           $ins_reme = new Consulta($query, $this -> conexion, "RC");
           }

    }

}//fin clase


$proceso = new Proc_ins_despac($this -> usuario_aplicacion, $this -> conexion, $this -> codigo);



?>
