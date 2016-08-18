
function buscar_despac(formulario)
{
  try 
  {
    validacion = true;
    formulario = document.form_ins;
    var num_despac = document.getElementById('num_despacID');
    var num_placas = document.getElementById('num_placasID');
    var cod_manifi = document.getElementById('cod_manifiID');
    
    if(num_despac.value == '' && num_placas.value == '' && cod_manifi.value == '' )
    {
      alert("Debe digitar un dato de búsqueda.");
      validacion=false;
      return num_despac.focus();
    }
    else  
      formulario.submit();
  }
  catch(e)
  {
    alert("Error funcion aceptar_ins " + e.message + '\n' + e.stack);
  }
}

// Funcion para validación alfanumérica (Solo Números y Letras AZ - az: 09)
function TextInputNumeric( fEvent ){
var fKeyPressed = ( fEvent.which ) ? fEvent.which : fEvent.keyCode;
return !(( fKeyPressed < 48 || fKeyPressed > 57 )  && ( fKeyPressed != 8 )  && ( fKeyPressed != 9 ) && ( fKeyPressed != 32 ) );
}

function cambio ( id, ids )
{
  var uno = document.getElementById(id);
  uno.value = '';
  var dos = document.getElementById(ids);
  dos.value = '';
  
  var reset = document.getElementById('resetID');
  
  if(reset.value == 1)
  {
    var hidden = document.getElementById('cod_operadHiddenID');
    document.getElementById('cod_operadID').innerHTML = hidden.innerHTML ;
    
    
    document.getElementById('num_placaxID').value = "";
    document.getElementById('nom_usrgpsID').value = "";
    document.getElementById('clv_usrgpsID').value = "";
    document.getElementById('idx_gpsxxxID').value = "";
  }
}



function SaveDataGps ( id )
{
  try
  {
    var Message = '';
    var fStandar   = document.getElementById('fStandarID').value;
    var cod_servic = document.getElementById('cod_servicID').value;
    var opcion     = document.getElementById('opcionID').value;
    var Case       = document.getElementById('CaseID').value;
    var num_despac = document.getElementById('num_despacHID');
    var cod_operad = document.getElementById('cod_operadID');
    
    var cod_operadReq = document.getElementById('cod_operadHiddenReqID');
    
    var nom_usuari = document.getElementById('nom_usrgpsID');
    var clv_usrgps = document.getElementById('clv_usrgpsID');
    var idx_gpsxxx = document.getElementById('idx_gpsxxxID');
   
    if(cod_operad.value == '---')
    {
      alert("Debe seleccionar el operador GPS.");
      cod_operad.focus();
      return false;
    }
    if(nom_usuari.value == '')
    {
      alert("Debe digitar el Usuario.");
      nom_usuari.focus();
      return false;
    }
    if(clv_usrgps.value == '')
    {
      alert("Debe digitar la Clave.");
      clv_usrgps.focus();
      return false;
    }
    
    for (var i = 0; i < cod_operadReq.length; i++)
    { 
      if(cod_operadReq[i].value == cod_operad.value )
      {
        if(idx_gpsxxx.value == '')
        {
          alert("Debe digitar el ID para el operador GPS: "+cod_operad.options[cod_operad.selectedIndex].text);
          idx_gpsxxx.focus();
          return false;
        }
        
      }
    }
    
   
    var Status = '';
    if(opcion != '' && opcion == '3')    
      Message = "Desea insertar los datos GPS para el despacho: "+num_despac.value+" ?";     
    else
    {
      Message = "Dese actualizar los datos del operdor GPS para el despacho: "+num_despac.value+"?";
      Status = setTimeout("$('#"+id+"').hide('slow');", 6000);
    }
    

      if(confirm(Message))  
      {
        var parametros =  {
                            "Ajax" : 'on',
                            "Case" : Case,
                            "num_despac" : num_despac.value,
                            "cod_operad" : cod_operad.value,
                            "nom_usuari" : nom_usuari.value,
                            "clv_usrgps" : clv_usrgps.value,
                            "idx_gpsxxx" : idx_gpsxxx.value,
                            "cod_servic" : cod_servic
                          };
            $.ajax({
                    data:  parametros,
                    url:   '../'+fStandar+'/despac/ajax_despac_gpsxxx.php',
                    type:  'post',
                    beforeSend: function () {
                            $("#"+id+"").show();
                            $("#"+id+"").html("<table align='center'><tr><td align='center'><img src='../"+fStandar+"/imagenes/ajax-loader.gif'></td></tr></table>");
                    },
                    success:  function (response) {
                            $("#"+id+"").html(response);
                              Status
                              
                    }
            });
      }
    
    

  }
  catch (e)
  {
    alert( "Error en SaveDataGps()"+ e.message);
  }
}























