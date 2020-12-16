$(function() {

    $('.datatable').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
        },
        "searching": false,
        "bInfo": false,
        "bLengthChange": false
    });




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

        descripcion = "<b><label style='color:#000000'>" + ruta[i].des_servic + "</label></b><br>";
        descripcion += "<b><label style='color:#000000'> <b>Fecha: </b>" + ruta[i].fec_ubicac + "<br> <b>Hora:</b> " + ruta[i].hor_ubicac + "</label></b><br>";
        icono = "../images/ubication_point.png"; //Descargues = D

        features[i] = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat])),
            name: descripcion
        });

        features[i].setStyle(new ol.style.Style({
            image: new ol.style.Icon(({
                src: icono,
                scale: 0.2,
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
        target: 'map',
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
});