<?php
    require_once "../constantes.inc";
    $randon = rand(1111,9999);
?>

<script src="../../<?php echo DIR_APLICA_CENTRAL; ?>/js/lib/plugins/jquery/jquery.min.js"></script>   
<script src="../../<?php echo DIR_APLICA_CENTRAL; ?>/js/lib/plugins/bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" language="JavaScript" src="https://adminlte.io/themes/AdminLTE/dist/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/adminlte.min.js" integrity="sha256-Utchz0cr9Hjt+G0gl1YbXb8P2mNugSxobc9AXUfreHc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/pages/dashboard3.min.js" integrity="sha256-bf6XNqDnwX4g6QZx934mr8BFaRNtjY2Vs88YsjZi9QY=" crossorigin="anonymous"></script>

<!------ Include the above in your HEAD tag ---------->

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../../<?php echo DIR_APLICA_CENTRAL; ?>/js/lib/plugins/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/adminlte.min.css" integrity="sha256-tDEOZyJ9BuKWB+BOSc6dE4cI0uNznodJMx11eWZ7jJ4=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/alt/adminlte.plugins.min.css" integrity="sha256-K/rXKcrvSBsdB8WjaU78Ga+3bqjOZ0oyKQ2hpOb2OgU=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/alt/adminlte.extra-components.min.css" integrity="sha256-NQaR4VO2vLNDjoagWSYPEuUeqU5U7X1bdqJJiQsrmn0=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
        <link href="assets/css/style.css?rand=<?=$randon;?>" type="text/css" rel="stylesheet" />
        <link rel="icon" href="../logos/favicon.ico"/>

        <title>Consulta Pedido</title>
    </head>
    <body class="bodyback">

        <main class="login-form" style="margin-top:10%">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-11">
                        <div class="card">
                            <div class="card-header color-principal text-white text-center"><h5 style="margin-bottom:0px">Pedidos Asociados</h5></div>
                                <div class="card-body overflow-auto" style="height:380px;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="box box-solid box-primary">
                                                <div class="box-header with-border text-center">
                                                    <h3 class="box-title ">Pedido No. </h3>
                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 row justify-content-center align-items-center">
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn color-principal text-white txtsmll">
                                                                Ver estado
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="box box-solid box-primary">
                                                <div class="box-header with-border text-center">
                                                    <h3 class="box-title ">Pedido No. </h3>
                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 row justify-content-center align-items-center">
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn color-principal text-white txtsmll">
                                                                Ver estado
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="box box-solid box-primary">
                                                <div class="box-header with-border text-center">
                                                    <h3 class="box-title ">Pedido No. </h3>
                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 row justify-content-center align-items-center">
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn color-principal text-white txtsmll">
                                                                Ver estado
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="box box-solid box-primary">
                                                <div class="box-header with-border text-center">
                                                    <h3 class="box-title ">Pedido No. </h3>
                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 row justify-content-center align-items-center">
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn color-principal text-white txtsmll">
                                                                Ver estado
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="box box-solid box-primary">
                                                <div class="box-header with-border text-center">
                                                    <h3 class="box-title ">Pedido No. </h3>
                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 row justify-content-center align-items-center">
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn color-principal text-white txtsmll">
                                                                Ver estado
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="box box-solid box-primary">
                                                <div class="box-header with-border text-center">
                                                    <h3 class="box-title ">Pedido No. </h3>
                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 row justify-content-center align-items-center">
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn color-principal text-white txtsmll">
                                                                Ver estado
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        </main>
    </body>
</html>