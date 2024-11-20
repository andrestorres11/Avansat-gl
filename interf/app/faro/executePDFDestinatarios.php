<?php
die();
#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

class executePDFDestinatarios {

    var $db4 = NULL;
    var $PDF = NULL;
    var $dir = "/var/www/html/ap/satt_mobile/tmp/";
    var $dir2 = "/var/www/html/ap/interf/app/faro/";
    var $tumb = "/var/www/html/ap/satt_mobile/tmp/tumb/";

    function __construct() {
        $noimport = true;
        @include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );
        @include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" );
        @include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
        @include_once( "/var/www/html/ap/interf/app/faro/fpdf/fpdf.php");
        @include_once( "/var/www/html/ap/satt_faro/constantes.inc");
        try {

            $fExcept = new Error(array("dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai));
            $fExcept->SetUser('CronPDFDestinatarios');
            $fExcept->SetParams("Faro", "Nueva funcionalidad PDF Destinatarios");
            $fLogs = array();
            $this->db4 = new Consult(array("server" => Hostxx, "user" => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS), $fExcept);
            $this->SendPDF();
        } catch (Exception $e) {
            $mTrace = $e->getTrace();
            $fExcept->CatchError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            return FALSE;
        }
    }

    function getImageFormat($dirFile) {
        $mType = exif_imagetype($dirFile);
        switch ($mType) {
            case '1': $mFormat = 'GIF';
                break;
            case '2': $mFormat = 'JPEG';
                break;
            case '3': $mFormat = 'PNG';
                break;
            case '4': $mFormat = 'SWF';
                break;
            case '5': $mFormat = 'PSD';
                break;
            case '6': $mFormat = 'BMP';
                break;
            case '7': $mFormat = 'TIFF_II';
                break;
            case '8': $mFormat = 'TIFF_MM';
                break;
            case '9': $mFormat = 'JPC';
                break;
            case '10': $mFormat = 'JP2';
                break;
            case '11': $mFormat = 'JPX';
                break;
            case '12': $mFormat = 'JB2';
                break;
            case '13': $mFormat = 'SWC';
                break;
            case '14': $mFormat = 'IFF';
                break;
            case '15': $mFormat = 'WBMP';
                break;
            case '16': $mFormat = 'XBM';
                break;
            case '17': $mFormat = 'ICO';
                break;
            default: $mFormat = "PNG";
                break;
        }
        return $mFormat;
    }

    function ImageFilter($mImage, $mFormat, $mNumDesp, $mNomFile) {
        $mNomFile = explode(".", $mNomFile);
        #echo "Imagen: ".$mImage." -------- Formato: ".$mFormat." \n";
        # Crea Copia de la Imagen
        switch ($mFormat) {
            case 'GIF' : $mImageNew = imagecreatefromgif($mImage);
                break;
            case 'JPEG' : $mImageNew = imagecreatefromjpeg($mImage);
                break;
            case 'PNG' : $mImageNew = imagecreatefrompng($mImage);
                break;
        }

        # Aplica filtro de Grises
        imagefilter($mImageNew, IMG_FILTER_GRAYSCALE);

        # Cambia tamaño imagen
        $porcentaje = 0.19;
        list($ancho, $alto) = getimagesize($mImage);
        $nuevo_ancho = $ancho * $porcentaje;
        $nuevo_alto = $alto * $porcentaje;


        $imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
        if (!imagecopyresampled($imagen_p, $mImageNew, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto))
            echo "NO se genero el resize\n";

        # Genera la imagen con el filtro de grices
        switch ($mFormat) {
            case 'GIF' : imagegif($imagen_p, $this->tumb . $mNumDesp . "_" . $mNomFile[0] . ".gif", 100);
                break;
            case 'JPEG' : imagejpeg($imagen_p, $this->tumb . $mNumDesp . "_" . $mNomFile[0] . ".jpg", 100);
                break;
            case 'PNG' : imagepng($imagen_p, $mImage, 100);
                break;
        }

        # Elimina la Imagen Creada
        imagedestroy($imagen_p);


        $imagen_p = NULL;
        $mNomFile = NULL;
        return TRUE;
    }

    function getDespacDestin($mNumDocumen = NULL) {
        /* $mSelect = "SELECT a.num_despac, a.num_docume, 
          a.num_docalt, b.num_placax,
          d.nom_tipdes, f.num_desext,
          a.bin_fotoxx
          FROM ".BASE_DATOS.".tab_despac_destin a,
          ".BASE_DATOS.".tab_despac_vehige b,
          ".BASE_DATOS.".tab_despac_despac c
          LEFT JOIN ".BASE_DATOS.".tab_despac_sisext f ON f.num_despac = c.num_despac,
          ".BASE_DATOS.".tab_genera_tipdes d
          WHERE a.num_despac = b.num_despac
          AND a.num_despac = c.num_despac
          AND d.cod_tipdes = c.cod_tipdes
          AND a.bin_fotoxx <> ''
          AND a.ind_sendxx = '0'"; */

        $mSelect = " SELECT num_despac, num_docume, url_imagex
                   FROM " . BASE_DATOS . ".tab_cumpli_despac   
                  WHERE  ind_sendxx = '0' /*AND num_docume = ' C1-5076719'*/ ";

        $mSelect .= ( $mNumDocumen != NULL ? " AND num_docume = '" . $mNumDocumen . "' " : " GROUP BY num_docume " );

        $this->db4->ExecuteCons($mSelect);
        return $this->db4->RetMatrix();
    }

    function SendPDF() {
        # Carga Facturas del Viaje
        $_DESPAC = $this->getDespacDestin();
       

        echo "<pre> Todas las facturas: <br>";
        print_r($_DESPAC);
        echo "</pre>";

        foreach ($_DESPAC AS $key => $row) {


            /*             * *************************************************** */
            $this->PDF = new FPDF('P', 'mm', 'letter');
            $this->PDF->AddPage();
            $this->PDF->SetLeftMargin(5);
            $this->PDF->SetFont('Arial', 'B', 11);
            $this->PDF->SetTextColor(0);
            $this->PDF->SetDrawColor(0);
            $this->PDF->SetFillColor(235);

            $this->PDF->Image($this->dir2 . 'pdfcorona/860068121.png', 5, 5, 65, 25);

            $this->PDF->SetX(80);
            $this->PDF->Cell(200, 5, "Cumplidos Del Viaje: " . ( $row['num_docume'] != '' ? $row['num_docume'] : 'DESCONOCIDO' ), 0, 1, 'L', 0);
            $this->PDF->SetX(80);
            $this->PDF->Cell(200, 5, "Tipo de Despacho: " . $row['nom_tipdes'], 0, 1, 'L', 0);
            $this->PDF->SetX(80);
            $this->PDF->Cell(200, 5, "Placa del Vehiculo: " . $row['num_placax'], 0, 1, 'L', 0);
            $this->PDF->Ln();

             $con_hojasx = 1;
            $_IN_X = 10;
            $_IN_Y = 30;

            $this->PDF->SetXY($_IN_X, $_IN_Y);

            # Carga las Imagenes asociadas a la factura -----------------------------------
            $_DESPAC2 = $this->getDespacDestin($row["num_docume"]);


            foreach ($_DESPAC2 as $key2 => $row2) {


                # GENERA LA IMAGEN LA CARPETA TEMPORAL ----------------------------------------------------------
                $tmp_file = $row2['url_imagex']; #$row['num_despac'].'.png';
                $filename = $this->dir . $row2["num_despac"] . "/" . $tmp_file;



                echo "<pre> Imagen: " . $this->tumb . $filename . " de la factura: " . $row['num_docume'] . "  -> Tratada: ";
                print_r($this->tumb . $row2["num_despac"] . "_" . $tmp_file);
                echo "</pre>";

                # Valida que el archivo de la Imagen Exista ------------------------------------------------------
                if (!file_exists($filename)) {
                    die("NO Existe la imagen: " . $filename);
                }

                # Valida Formato de la imagen de la imagen para que el &/%$% del PDF no se dañe------------------
                $mFormat = executePDFDestinatarios::getImageFormat($filename);


                # Filtra la imagen a escala de Grices (Blanco Y Negro)
                $mImageFilter = executePDFDestinatarios::ImageFilter($filename, $mFormat, $row2["num_despac"], $tmp_file);
                $this->PDF->Image($this->tumb . $row2["num_despac"] . "_" . $tmp_file, $_IN_X, $_IN_Y, 200, 110, $mFormat);
                

                echo '<pre>';
                echo "Y: " . $this->PDF->getY() . "<br>";
                echo "X: " . $this->PDF->getX();
                echo '</pre>';

                if ($con_hojasx == 2) {
                    $this->PDF->AddPage();
                    $_IN_Y += ($this->PDF->getY() - 145);
                    $con_hojasx--;
                } else {
                    $_IN_Y = ($this->PDF->getY() + 135);
                    $con_hojasx++;
                }
            }


            # Ruta Salida Archivo PDF
            $mNomFich = strpos($row['num_docume'], "RE-") != '' ? str_replace(",", "", trim($row['num_docume'])) : str_replace('-', ' ', str_replace(",", "", trim($row['num_docume'])));

            $file = '/jaula/ftpcorona/ftpcorona/' . $mNomFich . '.pdf';

            # Guarda PDF ------------------------------------------------------------------------------------------------------------    
            $this->PDF->Close();
            $this->PDF->Output($file, 'F');
            @chmod($file, 0777);

            if (file_exists($file)) {
                //echo $file."\n";
                #unlink( $this -> tumb.$tmp_file );
                $mUpdate = "UPDATE " . BASE_DATOS . ".tab_cumpli_despac 
                       SET ind_sendxx = '1'                            
                     WHERE num_despac = '" . $row['num_despac'] . "'
                       AND num_docume = '" . $row['num_docume'] . "' \n";
                 if( $this -> db4 -> ExecuteCons( $mUpdate ) )
                  {
                    echo "PDF CREADO: ".$file."<hr>\n";
                  } 
            }
        }
    }

}

$_CRON = new executePDFDestinatarios();
?>
