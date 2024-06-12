<?php

// ruta del archivo CSV
$csvFile = 'destin.csv';

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
    $cod_sitiox = $db->real_escape_string($row[0]);
    $nom_sitiox = $db->real_escape_string($row[1]); 

    // 
    //OJO sexo va 1 hombre 2 mujer gen_sexoxx
    //  SE PUEDE PONER REPLACE INTO PAR CUANDO SE ACTUALICE INFO EN EL INSERT
    // crear la consulta SQL para insertar la fila en la tabla
    $sql = array(
        "INSERT INTO ".$dbname.".`tab_despac_sitio` (`cod_sitiox`, `nom_sitiox`) VALUES
        ('$cod_sitiox','$nom_sitiox');",
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