<?php include_once("header_html_form_map.php"); ?>

<link href='../<?php echo DIR_APLICA_CENTRAL; ?>/css/ol.css' rel='stylesheet' />
<link href='../<?php echo DIR_APLICA_CENTRAL; ?>/css/banseg.css' rel='stylesheet' />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ"
    crossorigin="anonymous">


<div id="main-container" class="main-grid-container">

    <div class="left-panel-grid-container">
        <div class="formulario">
            <div>
                <select class="form_01" name="origen" id="origenID">

                    <?php foreach($ciudades['origen'] as $value): ?>

                        <option value="<?php echo $value[0]; ?>" <?php echo $value[0]=="" ? "selected=\"selected\"" : ""; ?> >
                                <?php echo $value[1]; ?>
                        </option>
                    
                    <?php endforeach; ?>

                </select>
            </div>

            <div>
                <select class="form_01" name="destino" id="destinoID">
                    
                    <?php foreach($ciudades['destino'] as $value): ?>

                    <option value="<?php echo $value[0]; ?>" <?php echo $value[0]=="" ? "selected=\"selected\"" : ""; ?> >
                            <?php echo $value[1]; ?>
                    </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <div>
                <input type="text" class="campo_texto " name="placa" id="placaID" placeholder="Placa" 
                onfocus="this.className='campo_texto_on'" onblur="this.className='campo_texto'">
            </div>

            <div>
                <input type="text" class="campo_texto " name="manifiesto" id="manifiestoID" placeholder="Manifiesto" 
                    onfocus="this.className='campo_texto_on'" onblur="this.className='campo_texto'">
            </div>

            <div class="container_radio_placa">
                <div>Visualizar ultima novedad:</div>
                <div>
                    <input type="radio" name="vizuaizar" id="vizuaizarID" value="novedad" checked="checked">
                </div>
            </div>
            <div class="container_radio_manifiesto">
                <div>Visualizar recorrido:</div>
                <div>
                    <input type="radio" name="vizuaizar" id="vizuaizarID" value="recorrido">
                </div>
            </div>
            <div class="btn_formulario" >    
                <input type="hidden" name="standa" id="standaID" value="<?php echo DIR_APLICA_CENTRAL;?>">
                <input type="button" style="cursor:pointer" name="buscar" id="buscarID" value="Buscar despachos">
            </div>
        </div>
        <div class="tabla_data">
            <table id='tabla_despachosID'>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="right-panel-grid-container">
        <div class="bar_collapse" onclick="collapseMenu( );">
            <i id="flechas" class="fas fa-angle-double-left"></i>
        </div>
        <div class="mapa">
             <div id='container_mapaWD'   >
            </div>
            <div id='container_mapaID'   >
            </div>
            <div id='popupContenedor' class='ol-popup' >
                <a href='#' id='popup-closer' class='ol-popup-closer'></a>
            </div>
        </div>
    </div>

</div>



<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script type='text/javascript' src='../<?php echo DIR_APLICA_CENTRAL; ?>/js/ol.js'></script>
<script type='text/javascript' src='../<?php echo DIR_APLICA_CENTRAL; ?>/js/maps/withOpenLayers.js'></script>
<script>
    let coordenadaToCentrarMap = <?php echo $coordenadaToCentrarMap; ?>;
</script>
<script type='text/javascript' src='../<?php echo DIR_APLICA_CENTRAL; ?>/js/inf_bandej_seguim.js?ran=<?php echo rand(200,15000); ?>'></script>
<script type='text/javascript' src='../<?php echo DIR_APLICA_CENTRAL; ?>/js/new_ajax.js'></script>
<script type='text/javascript' src='../<?php echo DIR_APLICA_CENTRAL; ?>/js/functions.js'></script>



<?php include_once("footer_html_form_map.php"); ?>