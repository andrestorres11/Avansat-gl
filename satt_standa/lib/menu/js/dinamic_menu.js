/*
@Autor: Ing. Christiam Barrera Arango.
@Descripción: Funciones Javascript que manipulan los eventos de las clases .inc de la aplicación.
@Nota: Todo parametro que necesite llegar desde php se obtiene por el ID de un input="hidden".
*/
/*
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
*/
/*
@Fin de las funciones de la Clase DinamicMenu.
*/