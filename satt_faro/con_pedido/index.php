<?php
    require_once "../constantes.inc";
    $randon = rand(1111,9999);
?>

<script src="assets/lib/jquery/src/jquery.min.js"></script>   
<script src="assets/lib/bootstrap4/js/bootstrap.js"></script>

<!------ Include the above in your HEAD tag ---------->

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/lib/bootstrap4/css/bootstrap.css">
        <link href="assets/css/style.css?rand=<?=$randon;?>" type="text/css" rel="stylesheet" />
        <link rel="icon" href="../logos/favicon.ico"/>

        <title>Consulta Pedido</title>
    </head>
    <body class="bodyback">
        <main class="login-form" style="margin-top:10%;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header color-principal text-white text-center"><h5 style="margin-bottom:0px">Estatus de mi pedido</h5></div>
                            <div class="card-body">
                                <form name="frmContact" autocomplete="off" method="post" action="view/index.php">
                                    <div class="form-group row" style="margin-bottom:0px;">
                                         <div class="col-md-6">
                                            <label class="radio-inline mr-3">
                                                <input type="radio" name="opttip" value="1" checked> Pedido
                                            </label>
                                            <label class="radio-inline mr-3">
                                                <input type="radio" name="opttip" value="2"> Remesa
                                            </label>
                                            <label class="radio-inline mr-2">
                                                <input type="radio" name="opttip" value="3"> No Interno
                                            </label>
                                            <div id="error-opcion" class="span demo-error">
                                            <?php if(isset($_REQUEST['message_par'])) { echo $_REQUEST['message_par']; } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="pedido" class="form-control" name="pedido" required autofocus>
                                            <div id="error-busqueda" class="span demo-error">
                                                <?php if(isset($_REQUEST['message_bus'])) { echo $_REQUEST['message_bus']; } ?>
                                            </div>
                                        </div>

                                        
                                       <!--  <div class="col-md-2"></div>
                                        <div class="input-group col-md-5">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">No. Pedido</span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="No. Pedido" id="pedido" name="pedido">
                                        </div>
                                        <div class="input-group col-md-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">Línea</span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Línea" id="linea" name="linea">
                                        </div> -->
                                    </div>

                                    <div class="form-group row">
                                        <label for="captcha_code" class="col-md-6 col-form-label text-md-right">Captcha Code</label>
                                        <div class="col-md-6">
                                            <input name="captcha_code" type="text" class="demo-input captcha-input">
                                            <div id="error-captcha" class="span demo-error">
                                                <?php if(isset($_REQUEST['message'])) { echo $_REQUEST['message']; } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-5">
                                        <button type="submit" class="btn color-principal text-white">
                                            Consultar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        </main>
    </body>
</html>