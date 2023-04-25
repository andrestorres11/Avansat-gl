/*! \fn: body ready
 *  \brief: cuando el modulo arranque se ejecuta la funcion para pintar el mapa si tiene novedades en 4999( GPS )
 *  \author: Ing. Miguel Romero
 *  \date: 04/03/2016
 *  \date modified: dia/mes/año
 *  \param: parametro 1
 *  \param: parametro 2
 *  \return valor que retorna
 */


$("body").ready(function() {


    var contenedorMapa = document.getElementById("form_mapaID");
    var div = $(contenedorMapa).parent();
    contenedorMapa.style.height = "340px";
    div.css("height", "370px");


    var embed = $('#map_embed').val();
    if (embed == 'yes') // si no es el mapa de OET no se ejecuta lo demas, de una vez el return
        return true;



    ruta = $("#dat_gpsxxx").val();

    ruta = window.atob(ruta);
    ruta = $.parseJSON(ruta);

    lat = parseFloat(ruta[0].val_latitu);
    lon = parseFloat(ruta[0].val_longit);

    let descargue = 1;

    var container = $("");

    var newCoord = ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857');

    var features = new Array();

    for (var i = 0; i < ruta.length; i++) {

        lat = parseFloat(ruta[i].val_latitu);
        lon = parseFloat(ruta[i].val_longit);

        if (ruta[i].tip_pointx == "N") {

            descripcion = "<b><label style='color:#000000'> Novedad " + (i + 1) + " </label></b><br>";
            descripcion += "<label style='color:#000000'> <b>Fecha:</b> " + ruta[i].fec_noveda + " </label><br>";
            descripcion += "<label style='color:#000000'> " + ruta[i].obs_noveda + " </label>";
            icono = '../satt_standa/imagenes/point.png'; //Verdes = N

        } else if (ruta[i].tip_pointx == "C") {

            descripcion = "<b><label style='color:#000000'> Cargue </label></b><br>";
            descripcion += "<label style='color:#000000'> <b>Fecha Cita de Cargue:</b> " + ruta[i].fec_noveda + " </label><br>";
            descripcion += "<label style='color:#000000'> " + ruta[i].obs_noveda + " </label>";
            icono = '../satt_standa/imagenes/inicio.png'; //Cargue = C

        } else if (ruta[i].tip_pointx == "O") {

            descripcion = "<b><label style='color:#000000'> Punto de Control </label></b><br>";
            descripcion += "<label style='color:#000000'> <b>Fecha y hora Planeada:</b> " + ruta[i].fec_noveda + " </label><br>";
            descripcion += "<label style='color:#000000'> " + ruta[i].obs_noveda + " </label>";
            icono = ruta[i].rut_iconox;
        } else if (ruta[i].tip_pointx == "P") {

            descripcion = "<b><label style='color:#000000'> Punto de Control </label></b><br>";
            descripcion += "<label style='color:#000000'> <b>Fecha y hora Planeada:</b> " + ruta[i].fec_noveda + " </label><br>";
            descripcion += "<label style='color:#000000'> " + ruta[i].obs_noveda + " </label>";
            icono = ruta[i].rut_iconox;
        } else if (ruta[i].tip_pointx == "D1") {

            descripcion = "<b><label style='color:#000000'> Descargue " + descargue + " </label></b><br>";
            descripcion += "<label style='color:#000000'> <b>Fecha Cita de Descargue:</b> " + ruta[i].fec_noveda + " </label><br>";
            descripcion += "<label style='color:#000000'> " + ruta[i].obs_noveda + " </label>";
            icono = '../satt_standa/imagenes/fin.png'; //Descargues = D
            descargue++;

        } else if (ruta[i].tip_pointx == "D2") {

            descripcion = "<b><label style='color:#000000'> Descargue " + descargue + " </label></b><br>";
            descripcion += "<label style='color:#000000'> <b>Fecha Cita de Descargue:</b> " + ruta[i].fec_noveda + " </label><br>";
            descripcion += "<label style='color:#000000'> " + ruta[i].obs_noveda + " </label>";
            icono = '../satt_standa/imagenes/fin2.png'; //Descargues = D
            descargue++;

        } else if (ruta[i].tip_pointx == "ORI") {

            descripcion = "<b><label style='color:#000000'> " + ruta[i].obs_noveda + " </label></b><br>";
            icono = '../satt_standa/imagenes/packages.png';

        } else if (ruta[i].tip_pointx == "DES") {

            descripcion = "<b><label style='color:#000000'> " + ruta[i].obs_noveda + " </label></b><br>";
            icono = '../satt_standa/imagenes/location.png';

        }

        features[i] = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat])),
            name: descripcion
        });

        features[i].setStyle(new ol.style.Style({
            image: new ol.style.Icon(({
                src: icono,
            }))
        }));


    }


    var vectorSource = new ol.source.Vector({
        features: features
    });

    var vectorLayer = new ol.layer.Vector({
        source: vectorSource
    });

    var container = document.getElementById('popupContenedor');
    var content = document.getElementById('popupContent');
    var closer = document.getElementById('popup-closer');

    var overlay = new ol.Overlay( /** @type {olx.OverlayOptions} */ ({
        element: container,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    }));

    var map = new ol.Map({
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            }),
            vectorLayer
        ],
        projection: 'EPSG:26915',
        target: contenedorMapa,
        overlays: [overlay],
        view: new ol.View({
            center: newCoord,
            zoom: 7
        })
    });


    closer.onclick = function() {
        overlay.setPosition(undefined);
        closer.blur();
        return false;
    };


    map.on('pointermove', function(evt) {
        var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
            return feature;
        });
        if (feature) {

            var coordinate = evt.coordinate;
            var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
                coordinate, 'EPSG:3857', 'EPSG:4326'));

            content.innerHTML = feature.T.name;
            overlay.setPosition(coordinate);
        } else {
            closer.click();
        }
    });

    // inactiva el zoom del mouse porque se cola cuando está bajando y lo toma en el mapa
    map.getInteractions().forEach(function(interaction) {
        if (interaction instanceof ol.interaction.MouseWheelZoom) {
            interaction.setActive(false);
        }
    }, this);


});