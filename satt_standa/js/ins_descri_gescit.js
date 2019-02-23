/**
 * @author jovo
 * @author chris
 */

$('body').ready(function(){
    $('#popID input[type=button][id="noID"]').click(function(){
        console.log('mmm');
        $('#cod_areasxID').val('nan');
        $('#nom_areasxID').val('nan');
        $('#row_dinamiID').val('nan');
    });
});

function registrar(accion, DLRow)
{
    try 
    {
 

        var Standa     = $("#standaID").val();
        var cod_causax = '';
        var des_causax = '';
        var cod_areasx = '';
        var cod_clasif = '';

        switch(accion)
        {
            case 'activar': 
            case 'inactivar': 
                cod_causax = $("input[id^=cod_causax"+DLRow+"ID]").val();
                des_causax = $("input[id^=des_causax"+DLRow+"ID]").val();

            break;
            case 'editar': 
                cod_causax = $("input[id=cod_causasID]").val();
                des_causax = $("input[id=des_causaxID]").val();   
                cod_areasx = $('#cod_areaxxID').val();
                cod_clasif = $('#cod_clasifID').val();
            break;            
            case 'registrar': 
                cod_causax = '0';
                des_causax = $('#des_causaxID').val();
                cod_areasx = $('#cod_areaxxID').val();
                cod_clasif = $('#cod_clasifID').val();
                if( des_causax == '' )
                {
                    return alert('Digite la descripción de la causa!');
                }                

                if( cod_areasx == '' )
                {
                    return alert('Seleccione una área de la lista!');
                }                

                if( cod_clasif == '' )
                {
                    return alert('Seleccione una clasificación de la lista!');
                }

            break;
        }
    
        $.ajax({
          url: "../"+ Standa +"/gescit/ins_descri_gescit.php",
          data : "Ajax=on&Option="+accion+"&Standa="+Standa+"&des_causax="+des_causax+"&cod_causax="+cod_causax+"&cod_areasx="+cod_areasx+"&cod_clasif="+cod_clasif,
          method : 'POST',
          success : function ( data ) 
          { 
            var response = jQuery.parseJSON( data );
            
            if(response.status == true)
            {
                location.href = 'index.php?window=central&cod_servic='+$('#cod_servicID').val()+"&menant="+$('#cod_servicID');
            }else{
                alert(response.message);
            }
          }

        });

    } 
    catch (e) 
    {
        alert("Error en registrar, mensaje" + e.message+"\nEn la linea: "+e.lineNumber);
    } 
}


//funcion de confirmacion para la edicion, eliminacion e inactivacion de transportadoras
function confirmar(operacion, data){

    LoadPopupJQNoButton( 'open', 'Confirmar Operación', 'auto', 'auto', false, false, true );
    var popup = $("#popID");
    console.log(data);
    
    var des_causax = data.des_causax;
    var onclick = "onclick='registrar(\""+operacion+"\", "+data.row+" )'";
    var msj = "<div style='text-align:center'>¿Está seguro de <b>"+operacion+"</b> la causa: <b>" +des_causax+ "?</b><br><br><br><br>";
    msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' "+onclick+" class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
    msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
    
    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
           
    popup.append(msj);// //lanza el popUp
}

function editarCausa(tipo, objeto)
{
    try 
    {
        var DLRow = $( objeto ).parent().parent();
        var cod_causax = DLRow.find("input[id^=cod_causax]").val(); // de los Hidden del dinamic list
        var des_causax = DLRow.find("input[id^=des_causax]").val(); // de los Hidden del dinamic list
        $("#cod_causasID").val(cod_causax); // De los Hidden principal
        $("#des_causasID").val(des_causax); // De los Hidden principal

        var row = DLRow.find("input[id^=cod_causax]").attr('name').substr(10, 11); 
        $("#row_dinamiID").val(row);
        //alert(cod_tercer+" - "+nom_transp);
 

        if(tipo == 1){
            confirmar('activar', {'cod_causax':cod_causax, 'des_causax': des_causax, 'row': row } );
        }else if(tipo == 2){
            confirmar('inactivar', {'cod_causax':cod_causax, 'des_causax': des_causax, 'row': row } );
        }else{
            LoadPopupJQNoButton( 'open', 'Confirmar Operación', 'auto', 'auto', false, false, true );
            var popup = $("#popID"); 
            var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> la causa: <b>" +des_causax+ "?</b><br><br><br><br>";
                msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='cargarCausa( "+cod_causax+", "+row+" )' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
                msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
                
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.append(msj);// //lanza el popUp
        }
    } 
    catch (e) 
    {
        alert("Error en editarCausa, mensaje" + e.message+"\nEn la linea: "+e.lineNumber);
    }
}

function cargarCausa(cod_causax, DLRow)
{
    try
    {
        var Standa     = $("#standaID").val();
        

        $.ajax({
          url: "../"+ Standa +"/gescit/ins_descri_gescit.php",
          data : "Ajax=on&Option=cargar&Standa="+Standa+"&cod_causax="+cod_causax,
          method : 'POST',
          success : 
            function ( data ) 
            { 
                var result = jQuery.parseJSON( data );
                if(result.status == true)
                {
                    $("#des_causaxID, #des_causasID").val(result.data.des_causax);
                    $("#cod_causasID").val(result.data.cod_causax);
                    $("input#registrarID").removeAttr('onclick');
                    $("input#registrarID").attr('onClick','registrar("editar", '+DLRow+')');

                    //$('#cod_areaxxID option[value="'+result.data.cod_areasx+'"]');
                    //$('#cod_clasifID option[value="'+result.data.cod_clasif+'"]');

                     $('#cod_areaxxID').val(result.data.cod_areasx); //$(' option[value="'+result.data.cod_areasx+'"]');
                     $('#cod_clasifID').val(result.data.cod_clasif); //$(' option[value="'+result.data.cod_clasif+'"]');
                }else{
                    $("#des_causaxID").val( "" );
                    $("#cod_causasID").val( "" );
                    $("input#registrarID").removeAttr('onclick');
                    $("input#registrarID").attr('onClick','registrar("registrar")');
                }
            },
            complete: function()
            {
                closePopUp();
            }
        });
    }
    catch( e )
    {
        alert("Error en cargarArea, mensaje" + e.message+"\nEn la linea: "+e.lineNumber);
    }
}

function Hide()
{
    var menuFrameSet = parent.document.getElementById("framesetID");
    
    if ( menuFrameSet.cols == '0,*' )
        menuFrameSet.cols = '190,*';
    else
        menuFrameSet.cols = '0,*';
}

function LoadComet()
{
    Comet();
    RefreshComet();
}


function LoadTurnos()
{
    Comet1();
    RefreshComet1();
}


function Comet1()
{
    try 
    {
        var petro = document.getElementById("petroID").value;
        var oplix = document.getElementById("oplixID").value;
        var sachu = document.getElementById("sachuID").value;
        var central = document.getElementById("centralID").value;
        var central2 = document.getElementById("central2ID").value;
        var estilo = document.getElementById("estiloID").value;
    var tipser = document.getElementById("tipserID").value;
        var us = document.getElementById("usID").value;
    var atributes = "opcion=Turnos";
            atributes+= '&petro=' + petro;
            atributes+= '&oplix=' + oplix;
            atributes+= '&sachu=' + sachu;
            atributes+= '&central=' + central;
            atributes+= '&estilo=' + estilo;
      atributes+= '&tipser=' + tipser;
      atributes+= '&us=' + us;
        AjaxGetData('../'+ central2 +'/inform/inf_despac_transi.php?', atributes, 'informeID', 'post', 'CalcularTotales1()');
    } 
    catch (e) 
    {
        alert("Error " + e.message);
    }
}

function RefreshComet1()
{
    try 
    {
        setInterval( "Comet1()" , 120000 );
    } 
    catch (e) 
    {
        alert("Error Load " + e.message);
    }
}


function Tipser(aux)
{
    try 
    {
      if (aux = 1) {
        document.getElementById("tipserID").value = document.getElementById("tipID").value;
        document.getElementById("usID").value = document.getElementById("us1ID").value;
        setTimeout(function(){Comet1();},200)
          
      }
    } 
    catch (e) 
    {
        alert("Error Load " + e.message);
    }
}



function Comet()
{
    try 
    {
        var petro = document.getElementById("petroID").value;
        var oplix = document.getElementById("oplixID").value;
        var sachu = document.getElementById("sachuID").value;
        var central = document.getElementById("centralID").value;
        var central2 = document.getElementById("central2ID").value;
        var estilo = document.getElementById("estiloID").value;
        
        var atributes = "opcion=informe";
            atributes+= '&petro=' + petro;
            atributes+= '&oplix=' + oplix;
            atributes+= '&sachu=' + sachu;
            atributes+= '&central=' + central;
            atributes+= '&estilo=' + estilo;
        AjaxGetData('../'+ central2 +'/inform/inf_despac_transi.php?', atributes, 'informeID', 'post', 'CalcularTotales()');
    } 
    catch (e) 
    {
        alert("Error " + e.message);
    }
}

function RefreshComet()
{
    try 
    {
        setInterval( "Comet()" , 120000 );
    } 
    catch (e) 
    {
        alert("Error Load " + e.message);
    }
}


function CalcularTotales()
{
  $('#formdespacID table:eq(2) tr:eq(0) td :gt(4)').each(function(index, thisTotal){
    var i = index + 6;
    var sumCol = 0;
    $('#formdespacID table:eq(2) tr > td:nth-child(' + i + ') :gt(1) a').each(function(index, domEle){
      sumCol = sumCol + Number($(domEle).html());
    });
    $(thisTotal).html('<b>' + sumCol + '</b>')
  });
}



function CalcularTotales1()
{
  $('#formdespacID table:eq(3) tr:eq(0) td :gt(4)').each(function(index, thisTotal){
    var i = index + 6;
    var sumCol = 0;
    $('#formdespacID table:eq(3) tr > td:nth-child(' + i + ') :gt(1) a').each(function(index, domEle){
      sumCol = sumCol + Number($(domEle).html());
    });
    $(thisTotal).html('<b>' + sumCol + '</b>')
  });
}
