function rewrite_body( obj ) {
  try {
    if ( typeof( obj ) == 'string' ) {
      obj = document.getElementById( obj );
    }
    while ( obj.tagName.toLowerCase() != 'body' ) {
      obj = obj.parentNode;
    }
    obj.innerHTML = '<div id="informDIV"></div>';
    //obj.innerHTML = '';
    return true;
  }
  catch( e ) {
    alert( 'Error función "burlar_index": ' + e.message );
  }
}


function LoadReport() { 
  try { 
    var atributes = 'ajax=1';
    atributes    += '&window=central';
    atributes    += '&cod_servic=' + $( '#cod_servicID' ).val();
    if ( $( '#cod_tipserID' ).val() )
      atributes    += '&cod_tipser=' + $( '#cod_tipserID' ).val();
    if ( $( '#usr_asignaID' ).val() )
      atributes    += '&usr_asigna=' + $( '#usr_asignaID' ).val();
    if ( $( '#tip_alarmaID' ).val() )
      atributes    += '&tip_alarma=' + $( '#tip_alarmaID' ).val();
    if ( $( '#cod_transpID' ).val() )
      atributes    += '&cod_transp=' + $( '#cod_transpID' ).val();
	if ( $( '#cod_tipserID' ).val() )
      atributes    += '&cod_tipser=' + $( '#cod_tipserID' ).val();
    $.ajax({ 
      type: 'post', 
      url:  'index.php', 
      data: atributes, 
      timeout: 0, 
      async: true, 
      success: function( result ) { 
        rewrite_body( 'informDIV' );
        $( '#informDIV' ).html( result ); 
        return true;
      }, 
      cache: false
    }); 
  }
  catch( e ) {
    alert( 'Error función "LoadReport": ' + e.message );
  }
} 


function ReloadReport() { 
  try { 
    setTimeout( "LoadReport()", 120000 );
    //setTimeout( "LoadReport()", 15000 );
  }
  catch( e ) {
    alert( 'Error función "ReloadReport": ' + e.message );
  }
} 


function Hide() { 
  var menuFrameSet = parent.document.getElementById( "framesetID" );
  if ( menuFrameSet.cols == '0,*' )
  menuFrameSet.cols = '190,*';
  else
  menuFrameSet.cols = '0,*';
} 


$(document).ready(function() 
{     
  $('#cod_tipserID, #usr_asignaID, #servic_0, #servic_1, #servic_2, #ind_cargueID, #ind_transiID, #ind_descarID, #ind_desurbID, #ind_desnacID, #ind_desimpID, #ind_desexpID, #ind_desxd1ID, #ind_desxd2ID').change(function() 
  {         
    $.blockUI({css: { border: 'none',   
                      padding: '15px',      
                      backgroundColor: '#001100',             
                      '-webkit-border-radius': '10px',             
                      '-moz-border-radius': '10px',             
                      opacity: .5,             
                      color: '#fff'         
                     },
                overlayCSS: { backgroundColor: '#001100', width: '100%', top: 0, left: 0 }
               });  
      if ( document.getElementById( 'informDIV' ) ) { 
        document.getElementById( 'informDIV' ).style.display = 'none';
      }
      if ( document.getElementById( 'frm_reportID' ) ) { 
        document.getElementById( 'frm_reportID' ).submit();
      }
    }); 
 }); 
 
 
 $(document).ready(function() {     
  $('.send_back').click(function() {         
    $.blockUI({css: { border: 'none',   
                      padding: '15px',      
                      backgroundColor: '#001100',             
                      '-webkit-border-radius': '10px',             
                      '-moz-border-radius': '10px',             
                      opacity: .5,             
                      color: '#fff'         
                     },
                overlayCSS: { backgroundColor: '#001100', width: '100%', top: 0, left: 0 }
               });  
    }); 
 }); 
 
 
 
function SendDespac( tip_alarma, cod_transp, all_despac ) {
  try {
    document.getElementById( 'tip_alarma' ).value = tip_alarma;
    document.getElementById( 'cod_transp' ).value = cod_transp;
    document.getElementById( 'all_despac' ).value = all_despac;
    document.getElementById( 'frm_reportID' ).submit();
  }
  catch( e ) {
    alert( 'Error función "SendDespac": ' + e.message );
  }
}



$(function() {
  $( "#dialog:ui-dialog" ).dialog( "destroy" );
  $( "#pc" ).dialog({
    height: ($.browser.msie ? 800: 400),
    width: '70%',
    draggable: false,
    resizable: false,
    autoOpen: false,
    modal: true
  });
});



function poppc(num_despac){
  
  var central = document.getElementById('dir_centraID').value;
  var atributes = "Ajax=on";
      atributes+= '&opcion=getPC';
      atributes+= '&window=central';
      atributes+= '&num_despac=' + num_despac;
  
  $.ajax({
    type: "POST",
    url: '../'+ central +'/inform/inf_despac_seguim.php',
    data: atributes,
    success: function(data) {
      $('#pc').html(data);
      $("#pc").dialog('open');
    }
  });
}