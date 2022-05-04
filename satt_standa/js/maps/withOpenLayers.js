/**
 * Conjunto de funciones que nos permitiran crear un mapa con la libreria http://openlayers.org (ol.js)
 * 
 */



function drawMap(idMapContainer, heightContenedorMapa, heightParentContainerMap, color, coordenadaToCentrarMap, coordenadasToDraw) {

  var contenedorMapa = $("#"+idMapContainer);
  var map;

  if(contenedorMapa.length > 0){
    //set ready containers
    var container = document.getElementById('popupContenedor');
    var content = document.getElementById('popupContent');
    var closer = document.getElementById('popup-closer');      

    // PENDIENTE APLICAR ESTE CODIGO A CAMBIO DEL PARPADEO DE BORRAR EL MAPA 
    //   var features = geojsonFormat.readFeatures(data
    //   {featureProjection:"EPSG:3857"});
    //   geojsonSource.clear();
    //   geojsonSource.addFeatures(features);

    contenedorMapa.html("");


    var contenedorMapa = document.getElementById(idMapContainer);
    var div = $("#"+idMapContainer).parent();
    contenedorMapa.style.height = heightContenedorMapa;
    div.css("height",heightParentContainerMap) ;
      
      

    var newCoord = ol.proj.fromLonLat([coordenadaToCentrarMap.lon, coordenadaToCentrarMap.lat]);
    var features = new Array();
  
    
    //recoremos ruta la cual tiene 1 o varios puntos(lat y lon)
    for (var i = 0 ; i < coordenadasToDraw.length; i++) {
      
      lat = parseFloat(coordenadasToDraw[i].lat);
      lon = parseFloat(coordenadasToDraw[i].lon);
      
      if('color' in coordenadasToDraw[i]){
        color = coordenadasToDraw[i].color;
      }
      //INI agregar etiqueta al punto marcado
      descripcion = "<b><label style='color:#000000'> Novedad " + decodeURIComponent(coordenadasToDraw[i].novedad) + " </label></b><br>";
      descripcion += "<label style='color:#000000'> Fecha " + decodeURIComponent(coordenadasToDraw[i].fecha) + " </label><br>";
      descripcion += "<label style='color:#000000'> " + decodeURIComponent(coordenadasToDraw[i].observacion) + " </label>";
      
      features[i] = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat])),
        name: descripcion
      });
      //FIN agregar etiqueta al punto marcado

      //INI agregar imagen a la ubicacion
      var svg = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 30 30" enable-background="new 0 0 30 30" xml:space="preserve">'+    
      '<path fill="#' + color + '" d="M22.906,10.438c0,4.367-6.281,14.312-7.906,17.031c-1.719-2.75-7.906-12.665-7.906-17.031S10.634,2.531,15,2.531S22.906,6.071,22.906,10.438z"/>'+
      '<circle fill="#FFFFFF" cx="15" cy="10.677" r="3.291"/></svg>';
     
      var mysvg = new Image();
      mysvg.src = 'data:image/svg+xml,' + escape(svg);

      features[i].setStyle(new ol.style.Style({
        image: new ol.style.Icon(({
          img: mysvg,
          imgSize:[30,30]
        }))
      }));
      //FIN agregar imagen a la ubicacion


    }//fin for rutas

    
    var vectorSource = new ol.source.Vector({
      features: features
    });

    var vectorLayer = new ol.layer.Vector({
      source: vectorSource
    });

    var overlay = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
      element: container,
      autoPan: true,
      autoPanAnimation: {
        duration: 250
      }
    }));
    
    // crea el mapa
    /*var map = new ol.Map({
        target: contenedorMapa,
        interactions: ol.interaction.defaults({mouseWheelZoom:false}),
        projection:'EPSG:26915', 
        overlays: [overlay],

        view: new ol.View({
          center: newCoord, 
          zoom: 6
        }),

        layers: [
          new ol.layer.Tile({
            source: new ol.source.OSM()
          }),
          vectorLayer
        ]
        
        
    }); */

    var map = new ol.Map({
      target: contenedorMapa,
      interactions: ol.interaction.defaults({mouseWheelZoom:false}),
      projection:'EPSG:26915', 
      overlays: [overlay],
      
      layers: [
        new ol.layer.Tile({
            source: new ol.source.OSM({
                url: 'http://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
               //url: 'http://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}&s=Ga',
                attributions: [
                    new ol.Attribution({ html: '© Google' }),
                    new ol.Attribution({ html: '<a href="https://developers.google.com/maps/terms">Terms of Use.</a>' })
                ]
            })
        }),
        vectorLayer
      ],
      view: new ol.View({
        center: newCoord, 
        zoom: 6
      })
    });

    //cierra el popup de la descripcion del punto en el mapa
    closer.onclick = function() {
      overlay.setPosition(undefined);
      closer.blur();
      return false;
    };
  
    //configura acciones cuando mueve el puntero en el mapa
    map.on('pointermove', function(evt){
      var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) { 
          return feature; 
      });
      if (feature) {
        var coordinate = evt.coordinate;
        var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
            coordinate, 'EPSG:3857', 'EPSG:4326'));

        content.innerHTML = feature.T.name ;
        overlay.setPosition(coordinate);
      }
      else{
        closer.click();
      }
    });
    
    // inactiva el zoom del mouse porque se cola cuando está bajando y lo toma en el mapa
    map.getInteractions().forEach(function(interaction) {
      if (interaction instanceof ol.interaction.MouseWheelZoom) {
        interaction.setActive(false);
      }
    }, this);
  }

  return map;

}





