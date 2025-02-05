<?php

// ini_set('display_errors', true);
// error_reporting(E_ALL);

/*! \Cron: CronLinMarcColRUNT.php
*  \brief: Este cron esta especificado para que se estructure de manera que se suministre el CSV con la informacion requerida, basada en la estructura de los 
*          archivos planos de las lineas, marcas y colores del RUNT.
*          Tener en cuenta que al ser de GL este cron, no hace interaccion con el RNDC, pero si se tiene en cuenta la informacion de los vehiculos en el RUNT
*  Para que funcione se debe tener la siguiente estructura de CSV al momento de asignarlo al cron:
*  Fila 1 (Lineas - Marcas - Colores): Esta fila esta reservada para el encabezado, esto con el objetivo de que se sepa en que columna va cada informacion
*  CSV - Colores: Columna A como codigo del color y Columna B como descripcion del color en el CSV
*  CSV - Marcas: Columna A como codigo de la marca y Columna B como descripcion de la marca en el CSV
*  CSV - Lineas: Columna A como codigo de la marca, Columna B como codigo de la linea y Columna C como descripcion de la linea en el CSV
*  \author: Ing. Jesus Sanchez
*  \date: 2025-02-05
*/

ini_set('display_errors', true);
error_reporting(E_ERROR);

if (!isset($_POST['usuario'])) {
    $html = '
        <html>
            <head>
                <title>Cron Empresarial - Insercion de Lineas, Marcas y Colores a tablas maestras del sate_standa </title>
            </head>
            <body>
                <form method="POST" name="form" id="form" enctype="multipart/form-data" action="?">
                    <label for="Action">Servidor:</label>
                    <select name="servidor">
                        <option value="oet-devbd.intrared.net">OET-DETV</option>
                        <option value="oet-qabd.intrared.net">OET-QA</option>
                        <option value="oet-avansatglbd.intrared.net">OET-AVANSATGL</option>
                    </select>
                    <label for="Action">Archivo CSV de la tabla maestra a cargar (Debe tener el encabezado):</label>
                    <input type="file" name="archivo" id="archivo" accept=".csv" title="Archivo Excel"><br/><br/>
                    <label for="Action">Tabla que se requiere insertar registros:</label>
                    <select name="action" id="action">
                        <option value="lineas">Tabla de Lineas de Vehiculos</option>
                        <option value="marcas">Tabla de Marcas de Vehiculos</option>
                        <option value="colores">Tabla de Colores de Vehiculos</option>
                    </select><br/>
                    <label for="usuarioBD">Usuario que realiza el proceso:</label><br>
                    <input type="text" name="usuario_bd" id="usuario_bd"><br>
                    <label for="usuario">Usuario de conexion BD:</label><br>
                    <input type="text" name="usuario" id="usuario"><br>
                    <label for="contrasena">Contrasena de conexion BD:</label><br>
                    <input type="password" name="contrasena" id="contrasena"><br>
                    <input type="submit" value="ejecutar">
                </form>
                <script>
                    function Validar(){
                        const form = document.querySelector("#form");
                        const usuario = document.querySelector("#usuario");
                        const password = document.querySelector("#contrasena");
                        const archivo = document.querySelector("#archivo");
                        const action = document.querySelector("#Action");
                        if(usuario.value === ""){
                            alert("El usuario es requerido");
                            return;
                        }else if(password.value === ""){
                            alert("La contraseña es requerida");
                            return;
                        }
                        form.submit();
                    }
                </script>
            </body>
        </html>
    ';
    echo $html;
}else{
    //conexion
    $conexion = mysqli_connect($_POST['servidor'], $_POST['usuario'], $_POST['contrasena']);
    if(mysqli_connect_errno()){
        die("Error de conexion: " . mysqli_connect_error());
    }
    //bases de datos
    $basesDatos = mysqli_query($conexion, "SHOW DATABASES");
    //recorrido de las bases de datos
    $consultasTemporal = array();
    //mysqli_query($conexion, 'START TRANSACTION;');
    $bd = "satt_standa";

    // echo "Este die() es para indicar que este cron se ejecutara para una base de datos en especifico (".$bd.") y para prevenir una ejecucion inesperada, si quiere cambiar por otra BD se debe ajustar en el codigo <br>";
    // die('Fin');
    
    while ($baseDatos = mysqli_fetch_object($basesDatos)) { // A pesar de tener un while, el almacenamiento de la informacion CSV lo hace UNICAMANTE con la base de datos declarada para $bd
        if((substr($baseDatos->Database,0,5) == "sate_" || substr($baseDatos->Database,0,5) == "sadc_" || substr($baseDatos->Database,0,6) == "spyme_" || substr($baseDatos->Database,0,5) == "satb_" || substr($baseDatos->Database,0,5) == "satt_" ) && substr($baseDatos->Database,0,14) == $bd) {
            if (isset($_FILES['archivo'])) {
                // Mover el archivo a una carpeta temporal
                $encabezado = 1; //Se tomara en cuenta si esta en el encabezado, siendo 1 que lo esta y 0 donde no lo esta
                $i = 2; // Se referencia la fila del CSV, en este caso no sera uno porque esta el encabezado
                $nombre = $_FILES['archivo']['tmp_name'];

                $seleccionBaseDatos = mysqli_select_db($conexion, $baseDatos->Database);
                echo 'Base de datos: ';
                print_r($baseDatos->Database);
                echo '<br>';

                $usuarioBD = $_POST['usuario_bd']; // Se toma el usuario que realizara la insercion

                if (($handle = fopen($nombre, 'r')) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) { // Recorre el CSV seleccionado
                        if($encabezado == 1){
                            $encabezado = 0;
                        }else if($encabezado == 0){
                            $data = explode(';',$data[0]);
                            if($_POST['action'] == "lineas"){ // Si se selecciono la opcion para las lineas del vehiculo

                                // No se toma en cuenta la primera columna, ya que es un codigo para WS
                                $datos[$i][] = $data[0]; // Codigo de la marca de la linea ante el RNDC (Para insumo de la BD Estandar de GL)
                                $datos[$i][] = $data[1]; // Codigo de la linea ante el RNDC (Para insumo de la BD Estandar de GL)
                                $datos[$i][] = $data[2]; // Descripcion de la linea ante el RNDC (Para insumo de la BD Estandar de GL)

                                if(!empty($usuarioBD)){ // Se valida que se haya digitado el usuario de BD
                                    $anti_comilla = strpos(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2]),"'"); 
                                    $nc_data = "";
                                    if($anti_comilla !== false){ // Si encuentra comilla simple, se reemplaza por ` para evitar novedades en SQL
                                        $nc_data = str_replace("'","`",iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2]));
                                    }
                                    if($nc_data != ""){ // Valida si se encuentra dentro del nombre de la linea, una comilla ` ajustada para evitar errores en la busqueda SQL
                                        $sql = "SELECT a.* FROM ".$baseDatos->Database.".tab_genera_lineas a WHERE a.cod_lineax = '".$data[1]."' 
                                                                                                    AND a.cod_marcax = '".$data[0]."'
                                                                                                    AND a.nom_lineax LIKE '%".iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2])."%'"; // Se valida si la linea existe
                                    }else{
                                        $sql = "SELECT a.* FROM ".$baseDatos->Database.".tab_genera_lineas a WHERE a.cod_lineax = '".$data[1]."' 
                                                                                                    AND a.cod_marcax = '".$data[0]."'
                                                                                                    AND a.nom_lineax LIKE '%".$nc_data."%'"; // Se valida si la linea existe
                                    }

                                    $existe_lineas = mysqli_query($conexion, $sql);
    
                                    if(mysqli_num_rows($existe_lineas) > 0){ // Si ya existe, se actualiza
                                        echo "La linea ".iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2])." de la fila ".$i." del CSV ya fue insertada previamente o ya esta presente, en la BD: ".$baseDatos->Database." <br>";
                                        
                                    }else{ // Si no existe, se inserta
                                        if($nc_data != ""){ // Si la linea no existe y tiene comilla simple se reemplaza el caracter, para evitar novedades
                                            $data[2] = $nc_data; 
                                        }
 
                                        // $query_consec = "SELECT IF(a.cod_lineax IS NULL, 0, MAX(CAST(a.cod_lineax AS UNSIGNED)) + 1 ) AS 'consec' FROM sate_standa.tab_genera_lineas a";
                                        // $consulta_consec = mysqli_query($conexion, $query_consec);
                                        // $matriz_consec = mysqli_fetch_array($consulta_consec);
                                        // $consec = $matriz_consec['consec'];
                                        

                                        $insert_sql = "INSERT INTO ".$baseDatos->Database.".tab_genera_lineas(cod_lineax, cod_marcax, nom_lineax,           
                                                                                                ind_estado, usr_creaci, fec_creaci, usr_modifi, fec_modifi) 
                                                                                            VALUES ('".$data[1]."','".$data[0]."','".iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2])."',
                                                                                            '1','".$usuarioBD."',NOW(),NULL,NULL)
                                                                                            ON DUPLICATE KEY UPDATE nom_lineax = '".iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2])."', usr_modifi = '".$usuarioBD."', fec_modifi = NOW()";
                                             
                                        // die("No hay resultados");
                                        $insert_lineas = mysqli_query($conexion, $insert_sql);

                                        if(!mysqli_errno($conexion)){
                                            echo "La linea '".iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2])."' de la fila ".$i." del CSV fue insertada correctamente, en la BD: ".$baseDatos->Database." <br>";
                                        }else{
                                            echo "La linea '".iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[2])."' de la fila ".$i." del CSV no fue insertada correctamente, en la BD: ".$baseDatos->Database." debido al siguiente error: <br>";
                                            echo mysqli_error($conexion) ."<br>";
                                        }
                                    }
                                }else{
                                    die("No esta el usuario quien desea insertar los registros, por favor digitarlo");
                                }

                            }else if($_POST['action'] == "marcas"){ // Si se selecciono la opcion para marcas del vehiculo

                                $datos[$i][] = $data[0]; // Codigo de la marca ante el RNDC (Para insumo de la BD Estandar de GL)
                                $datos[$i][] = $data[1]; // Nombre o descripcion de la marca ante el RNDC (Para insumo de la BD Estandar de GL)

                                if(!empty($usuarioBD)){ // Se valida que se haya digitado el usuario que inserta los registros
                                    $anti_comilla = strpos(utf8_decode($data[1]),"'");
                                    $nc_data = "";
                                    if($anti_comilla !== false){ // Si encuentra comilla simple, se reemplaza por ` para evitar novedades en SQL
                                        $nc_data = str_replace("'","`",utf8_decode($data[1]));
                                    }
                                    if($nc_data != ""){ // Valida si se encuentra dentro del nombre de la marca, una comilla ` ajustada para evitar errores en la busqueda SQL
                                        $sql = "SELECT a.* FROM ".$baseDatos->Database.".tab_genera_marcas a WHERE a.cod_marcax = '".$data[0]."' 
                                                                                            AND a.nom_marcax LIKE '%".$nc_data."%'"; // Se valida si la marca ya existe en el sate_standa
                                    }else{
                                        $sql = "SELECT a.* FROM ".$baseDatos->Database.".tab_genera_marcas a WHERE a.cod_marcax = '".$data[0]."' 
                                                                                            AND a.nom_marcax LIKE '%".utf8_decode($data[1])."%'"; // Se valida si la marca ya existe en el sate_standa
                                    }
    
                                    $existe_marca = mysqli_query($conexion, $sql);

    
                                    if(mysqli_num_rows($existe_marca) > 0){ // Si ya existe, solo muestra mensaje
                                        echo "La marca ".utf8_decode($data[1])." de la fila ".$i." del CSV ya fue insertada previamente o ya esta presente, en la BD: ".$baseDatos->Database." <br>";
                                    }else{ // Si no existe, se inserta 
                                        if($nc_data != ""){ // Si la marca no existe y tiene comilla simple se reemplaza el caracter, para evitar novedades
                                            $data[1] = $nc_data;
                                        }
                                        
                                        $insert_sql = "INSERT INTO ".$baseDatos->Database.".tab_genera_marcas(cod_marcax, nom_marcax, abr_marcax, ind_estado, 
                                                                                                    usr_creaci, fec_creaci, usr_modifi, fec_modifi) 
                                                                                            VALUES ('".$data[0]."','".utf8_decode($data[1])."','".utf8_decode($data[1])."','1',
                                                                                            '".$usuarioBD."', NOW(),NULL,NULL)
                                                                                            ON DUPLICATE KEY UPDATE nom_marcax = '".utf8_decode($data[1])."', usr_modifi = '".$usuarioBD."', fec_modifi = NOW()";

                                        // die("No hay resultados");
                                        $insert = mysqli_query($conexion, $insert_sql);

                                        if(!mysqli_errno($conexion)){
                                            echo "La marca ".utf8_decode($data[1])." de la fila ".$i." del CSV fue insertada correctamente, en la BD: ".$baseDatos->Database." <br>";
                                        }else{
                                            echo "La marca ".utf8_decode($data[1])." de la fila ".$i." del CSV no fue insertada correctamente, en la BD: ".$baseDatos->Database." debido al siguiente error: <br>";
                                            echo mysqli_error($conexion) ."<br>";
                                        }
                                    }
                                }else{
                                    die("No esta el usuario quien desea insertar los registros, por favor digitarlo");
                                }
                            }else if($_POST['action'] == "colores"){ // Si se selecciono la opcion para los colores del vehiculo

                                $datos[$i][] = $data[0]; // Codigo del color
                                $datos[$i][] = $data[1]; // Nombre o descripcion del color

                                if(!empty($usuarioBD)){ // Se valida que se haya digitado el usuario de la BD

                                    $color_codificado = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[1]); 
                                    if($color_codificado === false ){
                                        $color_codificado = utf8_decode($data[1]);
                                    }
                                    
                                    $anti_comilla = strpos($color_codificado,"'");

                                    $nc_data = "";
                                    if($anti_comilla !== false){ // Si encuentra comilla simple, se reemplaza por ` para evitar novedades en SQL
                                        $nc_data = str_replace("'","`",$color_codificado); 
                                    } 
                                    if($nc_data != ""){ // Valida si se encuentra dentro del nombre del color, una comilla ` ajustada para evitar errores en la busqueda SQL
                                        $sql = "SELECT a.* FROM ".$baseDatos->Database.".tab_vehige_colore a WHERE a.cod_colorx = '$data[0]' 
                                                                                        AND a.nom_colorx LIKE '%".$nc_data."%'"; // Se valida si el color ya existe en el sate_standa
    
                                    }else{
                                        $sql = "SELECT a.* FROM ".$baseDatos->Database.".tab_vehige_colore a WHERE a.cod_colorx = '$data[0]' 
                                                                                        AND a.nom_colorx LIKE '%".$color_codificado."%'"; // Se valida si el color ya existe en el sate_standa 
                                    }
                            
                                    $existe_colorx = mysqli_query($conexion, $sql);
    
                                    if(mysqli_num_rows($existe_colorx) > 0){ // Si ya existe, se actualiza
                                        echo "El color ".$color_codificado." de la fila ".$i." del CSV ya fue insertada previamente o ya esta presente, en la BD: ".$baseDatos->Database." <br>";  
                                    }else{ // Si no existe, se inserta
                                        if($nc_data != ""){ // Si el color no existe y tiene comilla simple se reemplaza el caracter, para evitar novedades
                                            $color_codificado = $nc_data; 
                                        }
                                        $insert_sql = "INSERT INTO ".$baseDatos->Database.".tab_vehige_colore(cod_colorx, nom_colorx, ind_estado,
                                                                                                                usr_creaci, fec_creaci, usr_modifi, fec_modifi) 
                                                                                            VALUES ('".$data[0]."','".$color_codificado."','1',
                                                                                                    '".$usuarioBD."',NOW(),NULL,NULL)
                                                                                            ON DUPLICATE KEY UPDATE nom_colorx = '".$color_codificado."', usr_modifi = '".$usuarioBD."', fec_modifi = NOW()"; 
                                        // Realiza la insercion teniendo en cuenta que no esta presente el color, si encuentra coincidencia por el codigo de color, actualizara su nombre (Dado que verificando antes de ejecutar el cron, hay colores con nombres)
                                        
                                        // die("No hay resultados");
                                        $insert = mysqli_query($conexion, $insert_sql);

                                        if(!mysqli_errno($conexion)){
                                            echo "El color ".$color_codificado." de la fila ".$i." del CSV fue insertado correctamente, en la BD: ".$baseDatos->Database." <br>";
                                        }else{
                                            echo "El color ".$color_codificado." de la fila ".$i." del CSV no fue insertado correctamente, en la BD: ".$baseDatos->Database." debido al siguiente error: <br>";
                                            echo mysqli_error($conexion) ."<br>";
                                        }
                                    }
                                }else{
                                    die("No esta el usuario quien desea insertar los registros, por favor digitarlo");
                                }
                            }
                            $i++;
                        }
                    }
                    fclose($handle);
                } else {
                    return "Error al abrir el archivo CSV.";
                }
            }else{
                return "Cargue un archivo Excel csv, xls ...";
            }
            
        }
    }
}