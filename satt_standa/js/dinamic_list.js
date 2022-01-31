var gPager = "block";

function DLFilter() 
{
  var selected = document.getElementById( "DLSelectedRowsID" ).value;
  var params = "Action=filter";
  params += "&Filters="+DLFilterValues();
  params += "&Selected="+selected;
  //alert( params );
  //getData( "../satb_sta155/dinamic_list.php?"+params, "DinamicListDIV", "POST" );
  AjaxGetData( '../satt_standa/dinamic_list.php?', params, 'DinamicListDIV', 'post' );
}

function DLFilterOnEnter( e )   
{
  var keycode;
  if( window.event )
    keycode = window.event.keyCode;
  else if(e)
    keycode = e.which;
  else
    return true;

  if( keycode == 13 )
    DLFilter();
  else    
    return true;
}

function DLFilterValues()   
{
  var headers = Number( document.getElementById( "HeadersID" ).value );
  if( headers == 0 )
    return false;

  var values = "";

  for( var h=0; h<headers; h++ )
  {
    var filter = document.getElementById( "DLFilter"+String( h ) ).value;
    values += String( filter );
    if( h < headers-1 )
      values += "::";
  }
  return values;
}


function DLLimit( select )  
{
  var selected = document.getElementById( "DLSelectedRowsID" ).value;
  var limit = select.value;

  var sort_col = Number( document.getElementById( "Sort_ColID" ).value );
  var way = document.getElementById( "WayID" ).value;

  //var module = document.getElementById( "module" ).value;

  var params = "Action=limit";
  params += "&Limit="+limit;
  params += "&Page=1";
  params += "&Selected="+selected;
  params += "&Sort_Col="+String( sort_col );
  params += "&Way="+way;
  params += "&Filters="+DLFilterValues();

  //alert( params );

  //getData( "../satb_sta155/dinamic_list.php?"+params, "DinamicListDIV", "POST" );
  AjaxGetData( '../satt_standa/dinamic_list.php?', params, 'DinamicListDIV', 'post' );
  //bar.showBar();
}


function DLPage( page )
{
  var selected = document.getElementById( "DLSelectedRowsID" ).value;
  var limit = document.getElementById( "DLLimiterID" ).value;

  var sort_col = Number( document.getElementById( "Sort_ColID" ).value );
  var way = document.getElementById( "WayID" ).value;

  //var module = document.getElementById( "module" ).value;

  var params = "Action=page";
  params += "&Page="+String( page );
  params += "&Selected="+selected;
  params += "&Limit="+limit;
  params += "&Sort_Col="+String( sort_col );
  params += "&Way="+way;
  params += "&Filters="+DLFilterValues();
  //alert( "../satb_sta155/dinamic_list.php?"+params );
  //getData( "../satb_sta155/dinamic_list.php?"+params, "DinamicListDIV", "POST" );
  
  AjaxGetData( '../satt_standa/dinamic_list.php?', params, 'DinamicListDIV', 'post' );
  
  //bar.showBar();
}


function DLPageOnEnter( e, page )   
{
  var keycode;
  if( window.event )
    keycode = window.event.keyCode;
  else if(e)
    keycode = e.which;
  else
    return true;

  if( keycode == 13 )
    return DLPage( page );
  else    
    return false;
}

function DLSort( col )  
{
  var sort_col = Number( document.getElementById( "Sort_ColID" ).value );
  var way = document.getElementById( "WayID" ).value;
  var selected = document.getElementById( "DLSelectedRowsID" ).value;
  var limit = document.getElementById( "DLLimiterID" ).value;
  var page = document.getElementById( "DLNroPageID" ).value;

  //var module = document.getElementById( "module" ).value;

  if( col == sort_col )
  {
    if( way == "ASC" )
      way = "DESC";
    else
      way = "ASC";
  }
  else
    way = "ASC";

  var params = "Action=sort";
  params += "&Sort_Col="+String( col );
  params += "&Way="+way;
  params += "&Selected="+selected;
  params += "&Limit="+String( limit );
  params += "&Page="+String( page );
  params += "&Filters="+DLFilterValues();
  
  //alert( params );

  //alert( "usuarios/dinamic_list.php?"+params );//../usuarios/
  //getData( "../satb_sta155/dinamic_list.php?"+params, "DinamicListDIV", "POST" );
  
  AjaxGetData( '../satt_standa/dinamic_list.php?', params, 'DinamicListDIV', 'post' );
  //bar.showBar();
}


function DLPageFormat( input, pages )
{
  var c = Number( input.value.slice( input.value.length-1, input.value.length )  );
  //alert( c );
  if( ( c!=0&&c!=1&&c!=2&&c!=3&&c!=4&&c!=5&&c!=6&&c!=7&&c!=8&&c!=9 ) ||  input.value > pages )
  {
    input.value = "";
    return false;
  }
}

function DLGet_Item( row, cell )
{
  var objRow = document.getElementById( "ActualRowID" );
  var objCell = document.getElementById( "ActualCellID" );
  var objItem = document.getElementById( "ActualItemID" );

  objRow.value = row;
  objCell.value = cell;
  objItem.value = "DLItem"+row+"-"+cell;

  //alert( objItem.value );
}

function DLRowClick( row, col, style )
{
  //alert( "row = "+row+", col = "+col+", style = "+style );

  var objRow = document.getElementById( "DLRow"+row );
  var objSelected = document.getElementById( "DLSelectedRowsID" );
  var pkey = String( document.getElementById( "DLCell"+row+"-"+col ).innerHTML );

  if( DLBelong( pkey ) ) 
  {
    var str = "";
    var items = objSelected.value.split( "::" );
    for( var i=0; i<items.length; i++ )
    {
      if( String( items[i] ) != pkey )   
      {
        if( str )
          str += "::";

        str += items[i];
      }
    }
    objSelected.value = str;
    return objRow.className = style;
  }
  else
  {
    if( objSelected.value )
      objSelected.value += "::";
    objSelected.value += pkey;
    return objRow.className = "DLRowClick";
  }
}

function DLBelong( pkey )
{
  var objSelected = document.getElementById( "DLSelectedRowsID" );
  var items = objSelected.value.split( "::" );
  for( var i=0; i<items.length; i++ )
  {
    if( items[i] == pkey )
      return true;
  }
  return false;
}

function DLPagerbarOnmouseover()
{
  var objBar = document.getElementById( "DLPagerBarID" );
  objBar.className = "DLPagerBarFocus";
}

function DLPagerbarOnmouseout()
{
  var objBar = document.getElementById( "DLPagerBarID" );
  objBar.className = "DLPagerBar";
}

function DLPagebarOnclick()
{
  var fDiv = document.getElementById( "DLPagerDIV" );
  var fBar = document.getElementById( "DLPagerBarID" );
  if( fDiv.style.display == "block" )
  {
    fDiv.style.display = "none";
    fBar.title = "Mostrar Paginador";
  }
  else
  {
    fDiv.style.display = "block";
    fBar.title = "Ocultar Paginador";
  }
}

function DLClose()
{
    ClosePopup();
}