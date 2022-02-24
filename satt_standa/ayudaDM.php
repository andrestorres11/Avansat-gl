<?php


$URL = "https://".$_SERVER["HTTP_HOST"]."/ap/satt_standa/manual/Desarrollos a la medida FARO - GL.pdf";
?>
    <head>
        <title> Manual Aplicación Móvil AVANSAT</title>
    </head>
    <body style="overflow:hidden">
        <?php
            echo "<iframe src='".$URL."' width='100%' height='90%' name='manual' id='manual'></iframe>";
            echo "<script>document.getElementById('manual').height=(screen.height)*0.85;</script>";
        ?>
    </body>
</html>
