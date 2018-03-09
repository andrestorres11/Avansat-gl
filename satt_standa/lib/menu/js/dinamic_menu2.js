/*
@Autor: Ing. Christiam Barrera Arango.
@Descripción: Funciones Javascript que manipulan los eventos de las clases .inc de la aplicación.
@Nota: Todo parametro que necesite llegar desde php se obtiene por el ID de un input="hidden".
*/

function GetClientResolution()	{
	document.getElementById( "ClientWidthID" ).value = screen.width;
	document.getElementById( "ClientHeightID" ).value = screen.height;
	alert( "Width: "+document.getElementById( "ClientWidthID" ).value+", Heigth: "+document.getElementById( "ClientHeightID" ).value+" - Resolution: "+document.getElementById( "ClientWidthID" ).value+" x "+document.getElementById( "ClientHeightID" ).value );
}

function DinamicDisplayer( id, cod )		{
  try { 
	var nodes = document.getElementById( "NodesID" ).value.split( "," );
	var level = Number( id.split( "-" )[0] );
	var cell =  Number( id.split( "-" )[1] );
	var next = String( level+1 )+"-"+String( cell+1 );
    
	if ( !document.getElementById( next ) )	 {
		var PostBody = "window=central&cod_servic="+cod;
		var fr = parent.document.getElementById( "centralFrameID" );
		fr.src = "?window=central&cod_servic="+cod;
        
        
    	return false;
	}
	else	{
		var size = nodes.length;
		for ( var i = cell; i < size; i++ )	{ 
          var actual = nodes[i];
          var nlevel = Number( actual.split( "-" )[0] );
          var ncell = Number( actual.split( "-" )[1] );
          if ( nlevel <= level )
            return false;
          else	{
            if (  document.getElementById( actual ).style.display == "block" )	{
              document.getElementById( actual ).style.display = "none";
            }
            else	{
              if ( nlevel == level+1 )	{
                document.getElementById( actual ).style.display = "block";
              }
            }
          }
		}
		return false;
	}
  }
  catch( e ) {
    alert( 'DinamicDisplayer: ' + e.message );
  }
}


function DinamicClose(e, cod){

  $("#menu ul li > a[class^="+$(e).attr('class')+"]").each(function(){
      var cod_servic = Number($(this).attr('id').replace(/[^\d]/g, '').replace(/^\s+|\s+$/g,""));
      if( cod_servic != Number(cod) ){
        $(this).parent().removeClass('mm-opened');
        $(this).parent().find("li > a[class^=servi]").each(function(){
            $(this).parent().removeClass('mm-opened');
        });
      }
  });

}
 
function DinamicDisplayer2(e, cod )		{
  try { 

    //---------------------//
      DinamicClose(e, cod);
    //---------------------//

    if($(e).parent().hasClass('mm-opened')){
      $(e).parent().removeClass('mm-opened');
    }else{
      $(e).parent().addClass('mm-opened');
    }

    if($(e).parent().find('ul.mm-submenu').length == 0){
      var fr = parent.document.getElementById( "centralFrameID" );
          fr.src = "?window=central&cod_servic="+cod;
    }else{
      return false;
    } 
  }
  catch( e ) {
    alert( 'DinamicDisplayer: ' + e.message );
  }
}




/*
@Fin de las funciones de la Clase DinamicMenu.
*/