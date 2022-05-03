var jsonRecorrido={};
var map;

$("document").ready(function(){
    var altoVentana = $(window).outerHeight();
    
    $("#td_container, #main-container").css({height:(altoVentana-130)+"px"});

    $(".tabla_data").css({height:(altoVentana-320)+"px"});

    map = drawMap("container_mapaID", "100%", "100%","",coordenadaToCentrarMap, []);

    map.updateSize();

    $("#buscarID").click(function(){
        LockAplication('lock');
        map = drawMap("container_mapaID", "100%", "100%","",coordenadaToCentrarMap, []);//reset mapa
        obtenerDespachos();
        LockAplication('unlock');
    });

    $("input[name=vizuaizar]").click(function(){  
        if($('#tabla_despachosID tr').length>0){
            addOrRemoveCheckBox();
        }
    });

    $("#origenID").change(function () { 
        onchangeDestino();
    });
     
});

function collapseMenu() {
    var divMap = $("#main-container");
    
    if (divMap.hasClass('main-grid-container-collapse')) {
        // expand the panel
        divMap.toggleClass("main-grid-container-collapse main-grid-container");
        $("#flechas").toggleClass("fa-angle-double-right fa-angle-double-left");    
    } else {
        // collapse the panel
        divMap.toggleClass("main-grid-container main-grid-container-collapse");
        $("#flechas").toggleClass("fa-angle-double-left fa-angle-double-right");    
    }
    map.updateSize();
    
}

function onchangeDestino() {
    try {
        $("#tabla_despachosID tbody tr").remove();

        var standa = $("#standaID");
        var url = "";
        var pathURL = "/despac/banseg/inf_bandej_seguim.php?";
        var atributes = 'Ajax=on&opcion=get_ciudades&origen='+$('#origenID').val();
        
        url += '../'+standa.val()+pathURL;

        AjaxGetXmlLocked(url, atributes, 'post','setDefaultValueSelect()');
    } catch(e) {
        alert('Error function onchangeCodTransa: ' + e.message);
    }
}

function setDefaultValueSelect() {
    $("#destinoID option:first").text('Seleccione Destino');
}

function addOrRemoveCheckBox() {
    tipoVisualizacion = $("input[name=vizuaizar]:checked").val();

    if($('#select_all').length){
        $("#select_all").remove();
    }

    if(tipoVisualizacion == "novedad"){
        check = '<input type="checkbox" name="select_all" id="select_all_checkbox" onclick="checkOrUncheckAllCheckbox(this);" value="">';
        $('#tabla_despachosID').find('tbody').prepend("<tr id=\"select_all\"><td>" + check + "</td><td colspan=\"3\">Seleccionar todo</td> <td></td> <td></td> <td></td> </tr>");
    }
    
}

function checkOrUncheckAllCheckbox() {
    flagToCheckOrUnCheck = false;
    
    if($('#select_all_checkbox').prop('checked')){
        flagToCheckOrUnCheck = true;
    }

    cantRowsTable = $('#tabla_despachosID tr').length;
    for (let index = 0; index < cantRowsTable; index++) {
        idCheckbox = "despacho_checkbox_"+index;
        $('#'+idCheckbox).prop('checked', flagToCheckOrUnCheck);
    }
    buildDrawMap(null);
}

function obtenerDespachos() {
    
    var standa = $("#standaID");
    var url = "";
    var pathURL = "/despac/banseg/inf_bandej_seguim.php";
    var data = {
        "opcion":"get_despachos"
       };

    data = buildFilters(data);

    // en caso de no haber filtros resetea la tabla donde se ponen los despachos
    if (Object.keys(data).length==1) {
        $("#tabla_despachosID tbody tr").remove();
    }

    url += '../'+standa.val()+pathURL;
    
    var funcionAntes = function(){
        $('#tabla_despachosID').find('tbody').append("<tr><td>Cargando data...</td></tr>");
        $("#tabla_despachosID tbody tr").remove();
    }
    var funcionExito = function(respon){
        
        if($("input[name=vizuaizar]:checked").val() == "novedad"){
            check = '<input type="checkbox" name="select_all" id="select_all_checkbox" onclick="checkOrUncheckAllCheckbox(this);" value="">';
            $('#tabla_despachosID').find('tbody').append("<tr id=\"select_all\"><td>" + check + "</td><td colspan=\"3\">Seleccionar todo</td> <td></td> <td></td> <td></td> </tr>");
        }
        
        $('#tabla_despachosID').find('tbody').append(respon);
    }
    makeRequest(url, data, funcionAntes, funcionExito);
}    

function obtenerRecorrido(numDespac) {
    
    var standa = $("#standaID");
    var url = "";
    var pathURL = "/despac/banseg/inf_bandej_seguim.php";
    var data = {
        "opcion":"get_recorrido",
        "num_despac":numDespac
       };

    data = buildFilters(data);

    // en caso de no haber filtros resetea la tabla donde se ponen los despachos
    if (Object.keys(data).length==1) {
        $("#tabla_despachosID tbody tr").remove();
        return;
    }

    url += '../'+standa.val()+pathURL;
    
    var funcionAntes = function(){};
    var funcionExito = function(respon){
        jsonRecorrido = respon;
    };
    makeRequest(url, data, funcionAntes, funcionExito);
}    

function makeRequest(url, data, funcionAntes, funcionExito) {
    
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        async: false,// seteamos esta variable con el animo de poder obtener el respon a nivel global
        beforeSend: funcionAntes,
        success: funcionExito,
        complete: function(){
        }
    });
}

function buildFilters(data) {
    
    if($('#origenID').val()!=""){
        data["origen"] = $('#origenID').val();
    }

    if($('#destinoID').val()!=""){
        data["destino"] = $('#destinoID').val();
    }

    if($('#placaID').val()!=""){
        data["placa"] = $('#placaID').val();
    }

    if($('#manifiestoID').val()!=""){
        data["manifiesto"] = $('#manifiestoID').val();
    }

    return data;
}

function buildDrawMap(obj){
    let stackCoordenadas = [];
    let color="";
    var placax = [];


    if($("#vizuaizarID:checked").val()=="recorrido"){

        if($(obj).is(':checked')){

            var jsonTemp = JSON.parse($(obj).val());
            color = jsonTemp.color;
            obtenerRecorrido(jsonTemp.num_despac);
            var ban = 0;
            
            stackCoordenadas = JSON.parse(jsonRecorrido);
            
            $("input:checkbox:checked").each(function(index, objeto){
                if($(obj).prop('id') != $(objeto).prop('id')){
                    $(objeto).prop('checked', false);
                    if(ban == 1){
                        placax[index] = JSON.parse($(this).val()).placa;
                    }
                }else{
                    if(ban == 0){
                        placax[index] = JSON.parse($(this).val()).placa;
                        ban=1;
                    }
                }
                
            });
        }else{
            $("input:checkbox:checked").each(function(index, objeto){
                $(objeto).prop('checked', false);
                placax[index] = JSON.parse($(this).val()).placa;                
            });
        }

    }else{

        $("input[name=despacho]:checked").each(function(index,objeto){
            stackCoordenadas[index] = JSON.parse($(objeto).val());
            placax[index] = JSON.parse($(this).val()).placa;
        });
    }

    addOrRemoveCheckBox();
    console.log(placax);

    if (placax != '') {
        var standa = $("#standaID");
        var pathURL = "/despac/banseg/map_princi.php";
        var url = '../'+standa.val()+pathURL;
        var data = {
            "opcion":"getMapaPrincipal",
            "placax": placax,
           };
        
        var funcionAntes = function(){
            $('#tabla_despachosID').find('tbody').append("<tr><td>Cargando data...</td></tr>");
            
        }
        var funcionExito = function(respon){
            console.log(respon);
            respon = JSON.parse(respon);
            DynamicJson = respon.msg_resp.DynamicJson;
            respon = JSON.parse(DynamicJson);
            //$("#container_mapaID").html('<iframe id="myFrame" src="https://gps.shareservice.co/oet/site/OnLineMap.aspx?b2V0TWFw=Zm11SWQ,'+respon.Id+'" style="height:500px;width:100%"></iframe>');
            $("#container_mapaWD").html('<a href="https://gps.shareservice.co/oet/site/OnLineMap.aspx?b2V0TWFw=Zm11SWQ,'+respon.Id+'" target="_blank">[Ver mapa]</a>');
        }

        makeRequest(url, data, funcionAntes, funcionExito);

        drawMap("container_mapaID", "100%", "100%",color,coordenadaToCentrarMap, stackCoordenadas);
    }      

}
