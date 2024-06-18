<?php

// ruta del archivo CSV
$csvFile = 'control.csv';

//Conectar a la base de datos
$servername = "oet-avansatglbd";
$username = "atorres";
$password = "oPi;itas";
$dbname = "satt_faro";

$db = new mysqli($servername, $username, $password, $dbname);

//Verificar conexi�n
if ($db->connect_error) {
    die("Conexi�n fallida: " . $db->connect_error);
}

// leer el archivo CSV
$file = fopen($csvFile, 'r');

// verificar si se pudo leer el archivo
if (!$file) {
    die("No se puedo abrir el Excel" . $csvFile);
}

// crear un bucle para leer todas las filas del archivo CSV
while (($row = fgetcsv($file, 1500, ',')) !== FALSE) {
    // escapar las comillas y los caracteres especiales
    $cod_tercer = $db->real_escape_string($row[0]);  
    $cod_tipsex = $db->real_escape_string($row[1]);  
    $cod_grupsa = $db->real_escape_string($row[2]);  
    $num_licenc = $db->real_escape_string($row[3]);  
    $num_catlic = $db->real_escape_string($row[4]);  
    $fec_venlic = $db->real_escape_string($row[5]);  
    $cod_califi = $db->real_escape_string($row[6]);  
    $num_libtri = $db->real_escape_string($row[7]);  
    $fec_ventri = $db->real_escape_string($row[8]);  
    $obs_habili = $db->real_escape_string($row[9]);  
    $nom_epsxxx = $db->real_escape_string($row[10]);  
    $nom_arpxxx = $db->real_escape_string($row[11]);  
    $fec_venarl = $db->real_escape_string($row[12]);  
    $nom_pensio = $db->real_escape_string($row[13]);  
    $num_pasado = $db->real_escape_string($row[14]);  
    $fec_venpas = $db->real_escape_string($row[15]);  
    $nom_refper = $db->real_escape_string($row[16]);  
    $tel_refper = $db->real_escape_string($row[17]);  
    $obs_conduc = $db->real_escape_string($row[18]);  
    $cod_operad = $db->real_escape_string($row[19]);  
    $usr_creaci = $db->real_escape_string($row[20]);  
    $fec_creaci = $db->real_escape_string($row[21]);  
    $usr_modifi = $db->real_escape_string($row[22]);  
    $fec_modifi = $db->real_escape_string($row[23]); 

    // 
    //OJO sexo va 1 hombre 2 mujer gen_sexoxx
    //  SE PUEDE PONER REPLACE INTO PAR CUANDO SE ACTUALICE INFO EN EL INSERT
    // crear la consulta SQL para insertar la fila en la tabla
    $sql = array(
        "INSERT INTO ".$dbname.".`tab_tercer_conduc` (`cod_tercer`, `cod_tipsex`, `cod_grupsa`, `num_licenc`, `num_catlic`, `fec_venlic`, `cod_califi`, `num_libtri`, `fec_ventri`, `obs_habili`, `nom_epsxxx`, `nom_arpxxx`, `fec_venarl`, `nom_pensio`, `num_pasado`, `fec_venpas`, `nom_refper`, `tel_refper`, `obs_conduc`, `cod_operad`, `usr_creaci`, `fec_creaci`, `usr_modifi`, `fec_modifi`) VALUES
        ('$cod_tercer', 
        '$cod_tipsex', 
        '$cod_grupsa', 
        '$num_licenc', 
        '$num_catlic', 
        '$fec_venlic', 
        '$cod_califi', 
        '$num_libtri', 
        '$fec_ventri', 
        '$obs_habili', 
        '$nom_epsxxx', 
        '$nom_arpxxx', 
        '$fec_venarl', 
        '$nom_pensio', 
        '$num_pasado', 
        '$fec_venpas', 
        '$nom_refper', 
        '$tel_refper', 
        '$obs_conduc', 
        '$cod_operad', 
        '$usr_creaci', 
        '$fec_creaci', 
        '$usr_modifi', 
        '$fec_modifi');"
      //  "UPDATE ".$dbname.".`tab_tercer_tercer` (`cod_tercer`)  VALUES ('$cod_tercer')
    );


    // ejecutar la consulta SQL
    /*if (!$db->query($sql)) {
        die("Insert failed: " . $db->error);
    }*/
    for($i= 0; $i < count($sql); $i++){
        echo '<br>';
        echo $sql[$i];
        if(mysqli_query($db, $sql[$i])) {
            echo '<br><strong style="color: green;">ejecutada correctamente</strong>';
        } else {
            echo '<br><strong style="color: red;">Error: ' . mysqli_error($db) . '</strong>';
        }
        echo "<br>";
        
    }  
}

// cerrar el archivo CSV
fclose($file);

// cerrar la conexi�n con la base de datos
$db->close();

?>