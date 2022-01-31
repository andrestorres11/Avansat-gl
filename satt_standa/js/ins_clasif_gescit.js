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
        var cod_clasif = '';
        var nom_clasif = '';

        switch(accion)
        {
            case 'activar': 
            case 'inactivar': 
                cod_clasif = $("input[id^=cod_clasif"+DLRow+"ID]").val();
                nom_clasif = $("input[id^=nom_clasif"+DLRow+"ID]").val();

            break;
            case 'editar': 
                cod_clasif = $("input[id=cod_clasifiID]").val();
                nom_clasif = $("input[id=nom_clasifiID]").val();   
            break;            
            case 'registrar': 
                cod_clasif = '0';
                nom_clasif = $('#nom_clasifiID').val();
                if( nom_clasif == '' )
                {
                    return alert('Digite el nombre de la nueva clasificación!');
                }

            break;
        }
    
        $.ajax({
          url: "../"+ Standa +"/gescit/ins_clasif_gescit.php",
          data : "Ajax=on&Option="+accion+"&Standa="+Standa+"&nom_clasif="+nom_clasif+"&cod_clasif="+cod_clasif,
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
    
    var nom_clasif = data.nom_clasif;
    var onclick = "onclick='registrar(\""+operacion+"\", "+data.row+" )'";
    var msj = "<div style='text-align:center'>¿Está seguro de <b>"+operacion+"</b> la clasificación: <b>" +nom_clasif+ "?</b><br><br><br><br>";
    msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' "+onclick+" class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
    msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
    
    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
           
    popup.append(msj);// //lanza el popUp
}

function editarClasificacion(tipo, objeto)
{
    try 
    {
        var DLRow = $( objeto ).parent().parent();
        var cod_clasif = DLRow.find("input[id^=cod_clasif]").val();
        var nom_clasif = DLRow.find("input[id^=nom_clasif]").val();

        $("#cod_clasifiID").val(cod_clasif);
        $("#nom_clasifiID").val(nom_clasif);

        var row = DLRow.find("input[id^=cod_clasif]").attr('name').substr(10, 11); 
        $("#row_dinamiID").val(row);
        //alert(cod_tercer+" - "+nom_transp);
 

        if(tipo == 1){
            confirmar('activar', {'cod_clasif':cod_clasif, 'nom_clasif': nom_clasif, 'row': row } );
        }else if(tipo == 2){
            confirmar('inactivar', {'cod_clasif':cod_clasif, 'nom_clasif': nom_clasif, 'row': row } );
        }else{
            LoadPopupJQNoButton( 'open', 'Confirmar Operación', 'auto', 'auto', false, false, true );
            var popup = $("#popID");
             
            var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> la clasificación: <b>" +nom_clasif+ "?</b><br><br><br><br>";
                msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='cargarClasificacion( "+cod_clasif+", "+row+" )' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
                msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
                
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.append(msj);// //lanza el popUp
        }
    } 
    catch (e) 
    {
        alert("Error en editarClasificacion, mensaje" + e.message+"\nEn la linea: "+e.lineNumber);
    }
}

function cargarClasificacion(cod_clasif, DLRow)
{
    try
    {
        var Standa     = $("#standaID").val();
        

        $.ajax({
          url: "../"+ Standa +"/gescit/ins_clasif_gescit.php",
          data : "Ajax=on&Option=cargar&Standa="+Standa+"&cod_clasif="+cod_clasif,
          method : 'POST',
          success : 
            function ( data ) 
            { 
                var result = jQuery.parseJSON( data );
                if(result.status == true)
                {
                    $("#nom_clasifiID, #nom_clasifiID").val(result.data.nom_clasif);
                    $("#cod_clasifiID").val(result.data.cod_clasif);
                    $("input#registrarID").removeAttr('onclick');
                    $("input#registrarID").attr('onClick','registrar("editar", '+DLRow+')');
                }else{
                    $("#nom_areasxID").val( "" );
                    $("#cod_clasifiID").val( "" );
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
