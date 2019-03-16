/**
 * @author jovo
 * @author chris
 */

$('body').ready(function(){
  
    $('#fec_inixxxID, #fec_finxxxID').datepicker({'dateFormat':'yy-mm-dd'}); // Jquery UI solo DATE


    /*$( "#tabs" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.jqXHR.fail(function() {
          ui.panel.html(
            "Couldn't load this tab. We'll try to fix this as soon as possible. " +
            "If this wouldn't be a demo." );
        });
      }
    });*/
    $( "#tabs" ).tabs();

    var months = new Array();
        months[0]  = "01";
        months[1]  = "02";
        months[2]  = "03";
        months[3]  = "04";
        months[4]  = "05";
        months[5]  = "06";
        months[6]  = "07";
        months[7]  = "08";
        months[8]  = "09";
        months[9]  = "10";
        months[10] = "11";
        months[11] = "12";



    // https://trentrichardson.com/examples/timepicker/
    $("#fec_sinnomID, #fec_newcitID").datetimepicker({
                                        'dateFormat':'yy-mm-dd', 
                                        // 'minDate': new Date(currentYear, currentMonth, currentDay, currentHour, currentMinute ),
                                      }); // Plugin JQUERY DATE + TIME


    // Carga las novedades segun el ind_cumple si el tab es el 3, osea cuando se pretende reprogramar o causar la cita
    if($("#tab_idID").val() == '3')
    {
        getNovedades(true);
    }

});


function getDataTab( id, tip_citdes )
{
    try
    {            

        var num_solici = $("#num_soliciID").val();
        var num_viajex = $("#num_viajexID").val();
        var est_solici = $("#est_soliciID").val();
        var cod_tipope = $("#cod_tipopeID").val();
        var cod_ciuori = $("#cod_ciuoriID").val();
        var cod_ciudes = $("#cod_ciudesID").val();
        var fec_inixxx = $("#fec_inixxxID").val();
        var fec_finxxx = $("#fec_finxxxID").val();

        var atributes  = 'Ajax=on&Option=loadTabInfo&tab_id='+id;
            atributes += num_solici != '' ? '&num_solici='+num_solici : '';
            atributes += num_viajex != '' ? '&num_viajex='+num_viajex : '';
            atributes += est_solici != '' ? '&est_solici='+est_solici : '';
            atributes += cod_tipope != '' ? '&cod_tipope='+cod_tipope : '';
            atributes += cod_ciuori != '' ? '&cod_ciuori='+cod_ciuori : '';
            atributes += cod_ciudes != '' ? '&cod_ciudes='+cod_ciudes : '';
            atributes += fec_inixxx != '' ? '&fec_inixxx='+fec_inixxx : '';
            atributes += fec_finxxx != '' ? '&fec_finxxx='+fec_finxxx : '';
            atributes += tip_citdes != '' ? '&tip_citdes='+tip_citdes : '';


        AjaxGetData('../'+ $("#standaID").val() +'/gescit/ges_citasx_entreg.php?', atributes, 'tabs-'+id, 'POST', 'repairTab("'+id+'")');
        // position: absolute; opacity: 0.4; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 1; visibility: hidden; border: 1px solid black; background: gray; 

    }
    catch(e)
    {
        alert("Error en getDataTab, mensaje" + e.message+"\nEn la linea: "+e.lineNumber);
    }
}

// repara los estilos de los tabs porque esa mierda se daña por si
function repairTab(id)
{
    $('li').each(function(i,o){
        if((i + 1 ) == id)
        {
            $(this).attr('class', 'ui-state-default ui-corner-top ui-tabs-selected ui-state-active');
        }
        else{
            $(this).attr('class', 'ui-state-default ui-corner-top');
        }
    });

    $('div[id^=tabs-]').each(function(i,o){
        if(( i + 1) == id)
        {
            $(this).attr('class', 'ui-tabs-panel ui-widget-content ui-corner-bottom');
        }
        else{
            $(this).attr('class', 'ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide');
        }
    });


    // ejecuta table de jquery
    // $('#tableCitasID').DataTable(); // no funciona por la maldita version de jquery


    // Ajusta tab para que no expanda el div
    $("#tabs-"+id).css('width', ( $( window ).width() - 60) );
    $("#tabs-"+id).css('overflow-x','scroll');


    $("#form2_").css('width', ( $("#tabla1ID").width() - 40 ) );

 
    // Coloca el icono en la primera columna dependiendo del tab ejecutado o seleccionado
    $('.DLTable tr').each(function(i, o){

        if(i > 0)
        {
            switch(id)
            {
                case '1':
                    var html = "<i class='fa fa-bell-o'      onclick='gestionar("+id+", "+(i - 1)+")' style='font-size:36px; cursor:pointer; background-color:yellow; border-radius:40px'></i>";
                break;    
                case '2':
                    var html = "<i class='fa fa-thumbs-o-up' style='font-size:36px; cursor:pointer; background-color:#ffffff; border-radius:40px'></i>";
                break;    
                case '3':
                    var html = "<i class='fa fa-bell-o'      onclick='gestionar("+id+", "+(i - 1)+")' style='font-size:36px; cursor:pointer; background-color:#f28900; border-radius:40px'></i>";
                break;    
                case '4':
                    var html = "Tab 4";
                break;
            }
            $(this).find('td').eq(0).html(html);
        }
    });


    // Carga las citas nuevas por defecto
    
   
}


function gestionar(tab_id, row_id)
{
    try
    { 
        if(confirm('Desea gestionar la solicitud: '+$("#DLCell"+row_id+"-1").text()+'?' ) )
        {
            var tip_citdes = $('input[id^=tip_citdes]:checked').val();
            location.href = 'index.php?window=central&cod_servic='+$("#cod_servicID").val()+'&tab_id='+tab_id+'&tip_citdes='+tip_citdes+'&num_consec='+$("#num_consec"+row_id+"ID").val()+'&Option=loadFormGestionar';
        }
    }
    catch(e)
    {
        alert("Error en gestionar, mensaje: " + e.message+"\nEn la linea: "+e.lineNumber);
    }  
}


function getNovedades( numConsec )
{
    var indCumple = $("#ind_cumpleID");
    var select = $("#cod_novedaID").html('');
    var option = '<option value="">--</option>';

    if(indCumple.val() == ''){
        select.html(option);
        return false;
    }
    
    $.ajax({
        url: '../'+ $("#standaID").val() +'/desnew/ajax_despachos.php',
        type: 'POST',
        data: 'option=GetNovedades&ind_cumpli='+indCumple.val(),
        success: function(result)
        {
            var response = jQuery.parseJSON( result );
            if(response.length > 0)
            {
                $.each(response, function(i, item){
                    option += '<option value="'+response[i].value+'">'+response[i].label+'</option>';
                });
                //console.log('option', option);
                select.html(option);
            }
        },
        complete: function(){
            if(numConsec == true)
            {
                getLastNovedad();
            }

        }
    });
}


function getLastNovedad()
{
    var num_consec = $("#num_consecID").val();
    var select = $("#cod_novedaID");
    $.ajax({
        url: '../'+ $("#standaID").val() +'/gescit/ges_citasx_entreg.php',
        type: 'POST',
        data: 'Ajax=on&Option=GetLastNovedad&num_consec='+num_consec,
        success: function(result)
        {
            var response = jQuery.parseJSON( result );
            if(response.status == true)
            {
                 
                select.val(response.cod_noveda);
            }
        }
    });
}

function getInfoCausas( codCausax )
{ 
    var cod_causax = $("#cod_causaxID").val();
    var nom_areaxx = $("#nom_areaxxID");
    var nom_clasif = $("#nom_clasifID");
    
    if(cod_causax == ''){
        nom_areaxx.val('');
        nom_clasif.val('');
        return false;
    }

    $.ajax({
        url: '../'+ $("#standaID").val() +'/gescit/ges_citasx_entreg.php',
        type: 'POST',
        data: 'Option=getInfoCausas&Ajax=on&cod_causax='+cod_causax,
        success: function(result)
        {
            var response = jQuery.parseJSON( result );
            nom_areaxx.val(response.nom_areasx);
            nom_clasif.val(response.nom_clasif);
        },
        complete: function(){

        }
    });
}


/*Registro de gestion de citas por parde de un pisco de faro, es que dice si si cumplio o no, el no debe reprogramar ni causar nada*/
function registrar()
{
    try 
    {
        var Standa     = $("#standaID").val();
        var Opcion     = $("#OpcionID").val();
        var fec_sinnom = $("#fec_sinnomID");
        var ind_cumple = $("#ind_cumpleID");
        var cod_noveda = $("#cod_novedaID");
        var obs_gestio = $("#obs_gestioID");
        var num_consec = $("#num_consecID").val();
        var tab_id     = $("#tab_idID").val();
             
        var msg = '';
        if(fec_sinnom.val() == ''){
            msg += "Debe seleccionar la fecha cita de descargue!\n";
        } 

        if(ind_cumple.val() == ''){
            msg += "Debe seleccionar el cumplido: SI/NO!\n";
        }

        if(cod_noveda.val() == ''){
            msg += "Debe seleccionar una novedad!\n";
        }


        if(msg != ''){
            return alert("Por favor validar: \n"+msg);
        }
        
    
        $.ajax({
          url: "../"+ Standa +"/gescit/ges_citasx_entreg.php",
          data : "Ajax=on&Option="+Opcion+"&Standa="+Standa+"&fec_sinnom="+fec_sinnom.val()+"&ind_cumple="+ind_cumple.val()+"&cod_noveda="+cod_noveda.val()+"&obs_gestio="+obs_gestio.val()+"&num_consec="+num_consec+"&tab_id="+tab_id,
          type : 'POST',
          success : function ( data ) 
          { 
            var response = jQuery.parseJSON( data );
            
            if(response.status == true)
            {
                location.href = 'index.php?window=central&cod_servic='+$('#cod_servicID').val()+"&Opcion=";
            }else{
                alert(response.message);
            }
          }

        });

    } 
    catch (e) 
    {
        alert("Error en registrar, mensaje: " + e.message+"\nEn la linea: "+e.lineNumber);
    } 
}


/*Registro de gestion de citas que no fueron cumplias y que le man de medellin las reprograma y causa el no cumpliminento*/
function registrar_no_cumplidas()
{
    try 
    {
        var Standa     = $("#standaID").val();
        var Opcion     = $("#OpcionID").val();
        var fec_sinnom = $("#fec_sinnomID");
        var ind_cumple = $("#ind_cumpleID");
        var cod_noveda = $("#cod_novedaID");
        var obs_gestio = $("#obs_gestioID");
        var num_consec = $("#num_consecID").val();

        var fec_sinnom = $("#fec_sinnomID");
        var fec_newcit = $("#fec_newcitID");
        var chk_reprog = $("#chk_reprogID").is(":checked") == true ? '1' : '0'; 
        var obs_reprog = $("#obs_reprogID");

        var cod_causax = $("#cod_causaxID");
        var obs_causal = $("#obs_causalID");
        var tab_id = $("#tab_idID");
             
        var msg = '';
        if(fec_sinnom.val() == ''){
            msg += "Debe seleccionar la fecha cita de descargue!\n";
        } 

        if(ind_cumple.val() == ''){
            msg += "Debe seleccionar el cumplido: SI/NO!\n";
        }

        if(cod_noveda.val() == ''){
            msg += "Debe seleccionar una novedad!\n";
        }


        if( $("#chk_reprogID").is(":checked") == true )
        {
            if(fec_newcit.val() == fec_sinnom.val() ){
                msg += "Debe cambiar la fecha de cita reprogramada!\n";
            }           

            if(obs_reprog.val() == '' ){
                msg += "Debe digitar una observación para la reprogramación!\n";
            }
                
        }

        if( cod_causax.val() == '' )
        {
            msg += "Debe seleccionar una causa!\n";
        }
        else if( obs_causal.val() == '' )
        { 
            msg += "Debe digitar una observación a la causa: "+$(cod_causax).find('option:selected').text()+" !\n";
        }
 

        
        if(msg != ''){
            return alert("Por favor validar: \n"+msg);
        }
            
        var params  = 'Ajax=on';
            params += '&Option='+Opcion;
            params += '&Standa='+Standa;
            params += '&ind_cumple='+ind_cumple.val();
            params += '&cod_noveda='+cod_noveda.val();
            params += '&obs_gestio='+obs_gestio.val();
            params += '&num_consec='+num_consec;
            params += '&tab_id='+tab_id.val();            
            params += '&fec_newcit='+fec_newcit.val();
            params += '&chk_reprog='+chk_reprog;
            params += '&cod_causax='+cod_causax.val();
            params += '&obs_causal='+obs_causal.val();
        
        $.ajax({
          url: "../"+ Standa +"/gescit/ges_citasx_entreg.php",
          data : params,
          type : 'POST',
          success : function ( data ) 
          { 
            var response = jQuery.parseJSON( data );
            
            if(response.status == true)
            {
                location.href = 'index.php?window=central&cod_servic='+$('#cod_servicID').val()+"&Opcion=";
            }else{
                alert(response.message);
            }
          }

        });
        
    } 
    catch (e) 
    {
        alert("Error en registrar, mensaje: " + e.message+"\nEn la linea: "+e.lineNumber);
    } 
}

 