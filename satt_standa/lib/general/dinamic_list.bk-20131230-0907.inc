<?php
/*
-------------------------------------------------------------------------------------------------------------------------------------------------
@Class: DinamicList
@Author: Ing. Christiam Barrera Arango
@Company: Intrared LTDA. Bogotï¿½ - Colombia
@Date: 2008-08-31
-------------------------------------------------------------------------------------------------------------------------------------------------
*/


Class DinamicList    {
    var $base;
    var $sql;
    var $query;
    var $matrix;
    var $headers;
    var $offset;
    var $limit;
    var $length;
    var $fields;
    var $rows;
    var $cols;
    var $labels;
    var $page;
    var $check;
    var $way;
    var $filters;
    var $pkey;
    var $sort_col;
    var $selected;
    var $cHtml;
    var $cClose    = NULL;
    var $cHiddens    = NULL;

    function __construct( &$conecction, $base, $pkey=1, $check="no" )
    {
      $this -> base = $base;
      $this -> check = $check;
      $this -> query = new Consulta( $this->base, $conecction );
      $this -> length = $this -> query -> ret_num_rows();

      $this -> pkey = $pkey;

      if( !$this -> way )
      {
        $this -> way = "ASC";
      }
      if( !$this -> offset )
      {
        $this -> offset = 0;
      }
      if( !$this -> limit )
      {
        $this -> limit = 100;
      }
      if( !$this -> sort_col )
      {
        $this -> sort_col = $this -> pkey;
      }

      //$this->sql = $this->base." ORDER BY ".$this->sort_col." ".$this->way." LIMIT ".$this->limit." OFFSET ".$this->offset;
      $this -> sql = $this -> base." ORDER BY ".$this -> sort_col." ".$this -> way." LIMIT ".$this -> offset.", ".$this -> limit;
    }
    
    
    function SetClose( $mClose="yes" )
    {
        $this->cClose = $mClose;
    }
    

    function GetHtml()
    {
      return $this -> cHtml;
    }
    
    function SetBase( $base )
    {
      $this -> base = $base;
    }

    function SetHeader( $label, $properties="", $options="" )
    {
      $this -> headers[] = Array( $label, $properties, $options );
    }
    
    
    function SetHeaders( $headers )
    {
        $this -> headers = $headers;
    }

    function SetHidden( $mName, $mValue )
    {
    	$this -> cHiddens[] = array( 'name' => $mName, 'value' => $mValue );
    }

    function Display( &$mConecction )
    {
      $mHtml = "\n";
      $mHtml .= '<div id="DinamicListDIV">';

      $mHtml .= $this -> Draw( $mConecction );

      $mHtml .= "\n";
      $mHtml .= '</div>';

      $this -> cHtml = $mHtml; 
    }
/* *
    function Display( &$conecction )    {
        echo "\n";
        echo '<div id="DinamicListDIV">';

        $this->Draw( $conecction );

        echo "\n";
        echo '</div>';
    }
/* */

    function SetPrimaryKey( $pkey=0 )
    {
      $this -> pkey = $pkey;
    }
    
    
    function SetWay( $way="ASC" )
    {
      $this -> way = $way;
    }
    
    function SetLimit( $limit )
    {
      $this -> limit = $limit;
    }

    function SetSelected( $selected )
    {
        $this -> selected = $selected;
    }

    function Ajax( &$conecction, $action, $sort_col="", $way="ASC", $filters="", $limit="", $page="", $selected="" )
    {

    if( $sort_col )
        $this -> sort_col = $sort_col;

      if( $way )
        $this -> way = $way;

      if( $filters )
        $this -> filters = explode( "::", $filters );

      if( $limit )
        $this -> limit = $limit;

      if( $page )
      {
        $pages = ceil( $this -> length / $this -> limit ) ;
        if( !is_numeric( $page ) )
          $this -> page = 1;
        elseif( $page > $pages )
          $this -> page = $pages;
        else
          $this -> page = $page;
      }

      $this -> selected = $selected;
      $this -> offset = ( $this -> page * $this -> limit ) - $this -> limit;

      $sql  = "";
      //$sql .= "SELECT * FROM (";
      $sql .= $this -> base;
      //$sql .= " LIMIT ".$this -> offset.", ".$this -> limit.") AS query ";
      //$sql .= " LIMIT ".$this -> offset.", ".$this -> limit." ";
      //$sql .= "WHERE 1 = 1";
    if( FALSE !== strpos( $sql, "GROUP" ) )
    {
      $QueryGrou = explode( "GROUP", $sql );
      $sql = "";
      if( FALSE !== strpos( $QueryGrou[0], "UNION" ) )
      {
         $QuerysDiv = explode( "UNION", $QueryGrou[0] );

         for( $k = 0, $tot = count( $QuerysDiv ); $k < $tot; $k++ )
         {
           for( $i = 0; $i < $this -> cols; $i++ )
           {
             if( $this -> filters[$i] )
             {
               $QuerysDiv[$k] .= " AND ";
               $QuerysDiv[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
             }
           }
           $sql .= $QuerysDiv[$k]; 

           if( $k+1 < $tot )
           {
             $sql .= " UNION";
           }
         }
       }
       else
       {
         for( $i = 0; $i < $this -> cols; $i++ )
         {
           if( $this -> filters[$i] )
           {
             $QueryGrou[0] .= " AND ";
             $QueryGrou[0] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
           }
         }
         $sql .= $QueryGrou[0];
       }

       $sql .=" GROUP ".$QueryGrou[1];
    }
     elseif( FALSE !== strpos( $sql, "UNION" ) )
     {
       $QuerysDiv = explode( "UNION", $sql[0] );
       $sql = "";

       for( $k = 0, $tot = count( $QuerysDiv ); $k < $tot; $k++ )
       {
         for( $i = 0; $i < $this -> cols; $i++ )
         {
           if( $this -> filters[$i] )
           {
             $QuerysDiv[$k] .= " AND ";
             $QuerysDiv[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
           }
         }

         $sql .= $QuerysDiv[$k]; 

         if( $k+1 < $tot )
         {
           $sql .= " UNION";
         }
       }
      }
      else
      {
        for( $i = 0; $i < $this -> cols; $i++ )
        {
          if( $this -> filters[$i] )
          {
            $sql .= " AND ";
            $sql .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
          }
        }
      }

      $sql .= " ORDER BY ".$this -> sort_col." ".$this -> way;
      //$sql = rawurlencode( $sql );

      //var_dump( $sql );
      
      $this -> query = new Consulta( $sql, $conecction );
//echo "no sale";
      $this -> length = $this -> query -> ret_num_rows();

      $sql .= " LIMIT ".$this -> offset.", ".$this -> limit." ";

      $this -> sql = $sql;

      $this -> cHtml = $this -> Draw( $conecction );
    }

    function GetProperties( $properties )
    {
      if( !$properties )
        return Array();

      $properties = explode( ";", $properties );
      $temp = Array();

      foreach( $properties as $propertie )
      {
        $propertie = $propertie;
        $propertie = explode( ":", $propertie );
        $value = trim( $propertie[1] );
        $propertie = strtolower( trim( $propertie[0] ) );
        $temp[$propertie] = $value;
      }
      return $temp;
    }

    function Properties( $tag )
    {
      if ( isset( $tag["value"] ) ) echo ' value="'.htmlentities( $tag["value"] ).'"';
      if ( isset( $tag["align"] ) ) echo ' align="'.$tag["align"].'"';
      if ( isset( $tag["width"] ) ) echo ' width="'.$tag["width"].'"';
      if ( isset( $tag["maxlength"] ) ) echo ' maxlength="'.$tag["maxlength"].'"';
      if ( isset( $tag["size"] ) ) echo ' size="'.$tag["size"].'"';
      if ( isset( $tag["onclick"] ) ) echo ' onclick="javascript:'.$tag["onclick"].'"';
      if ( isset( $tag["ondblclick"] ) ) echo ' ondblclick="javascript:'.$tag["ondblclick"].'"';
      if ( isset( $tag["onmousedown"] ) ) echo ' onmousedown="javascript:'.$tag["onmousedown"].'"';
      if ( isset( $tag["onmouseup"] ) ) echo ' onmouseup="javascript:'.$tag["onmouseup"].'"';
      if ( isset( $tag["onmouseover"] ) ) echo ' onmouseover="javascript:'.$tag["onmouseover"].'"';
      if ( isset( $tag["onmousemove"] ) ) echo ' onmousemove="javascript:'.$tag["onmousemove"].'"';
      if ( isset( $tag["onmouseout"] ) ) echo ' onmouseout="javascript:'.$tag["onmouseout"].'"';
      if ( isset( $tag["onfocus"] ) ) echo ' onfocus="javascript:'.$tag["onfocus"].'"';
      if ( isset( $tag["onblur"] ) ) echo ' onblur="javascript:'.$tag["onblur"].'"';
      if ( isset( $tag["onkeypress"] ) ) echo ' onkeypress="javascript:'.$tag["onkeypress"].'"';
      if ( isset( $tag["onkeydown"] ) ) echo ' onkeydown="javascript:'.$tag["onkeydown"].'"';
      if ( isset( $tag["onkeyup"] ) ) echo ' onkeyup="javascript:'.$tag["onkeyup"].'"';
      if ( isset( $tag["onreset"] ) ) echo ' onreset="javascript:'.$tag["onreset"].'"';
      if ( isset( $tag["onselect"] ) ) echo ' onselect="javascript:'.$tag["onselect"].'"';
      if ( isset( $tag["onchange"] ) ) echo ' onchange="javascript:'.$tag["onchange"].'"';
      if ( isset( $tag["title"] ) ) echo ' title="'.htmlentities( $tag["title"] ).'"';
      if ( isset( $tag["readonly"] ) ) echo ' readonly="readonly"';
      if ( isset( $tag["disabled"] ) ) echo ' disabled="disabled"';
      if ( isset( $tag["selected"] ) ) echo ' selected="selected"';
      if ( isset( $tag["checked"] ) ) echo ' checked="checked"';
    }

  function Draw( &$mConecction )
  {

    /* *
    echo "<hr />";
    echo $this -> sql;
    echo "<hr />";
    /* */

    $mConecction = $mConecction;
        
    //----------------------------------------------------------------------------------------------------
    /* *
    $this -> cQuery = new Consulta( $this -> cSql, $mConecction );
    $this -> CRows = $this -> cQuery -> ret_num_rows();
    $this -> cMatrix = $this -> cQuery -> ret_matriz( "i" );
    /* */

    $this -> query = new Consulta( $this -> sql, $mConecction );
    $this -> rows = $this -> query -> ret_num_rows();
    $this -> matrix = $this -> query -> ret_matriz( "i" );
    
    /*
    echo "<pre>";
    print_r( $this -> matrix );
    echo "</pre>";
    */
/*
    echo "<hr />";
    echo $this -> sql;
    echo "<hr />";
  */  
 
 /* *
    $this -> cQuery = mysql_query( $this -> cSql, $mConecction );
    $this -> CRows = mysql_num_rows( $this -> cQuery );
    $mReturno = array();
    while( $mRow = mysql_fetch_array( $this -> cQuery, MYSQL_NUM ) )
    {
      array_push( $mReturno, $mRow );
    }
    $this -> cMatrix = $mReturno;
 /* */
    //----------------------------------------------------------------------------------------------------

    if( !$this -> page )
      $this -> page = 1;

    if( $this -> length <= 100 )
    {
      $mPages = 1;
    }
    else
    {
      $mPages = ceil( $this -> length / $this -> limit );
    }

    $mReturn .= "\n  ";
    $mReturn .= '<table cellpadding="1" cellspacing="1" align="center" width="100%">';
    $mReturn .= "\n    ";
    $mReturn .= "<tr>";
    $mReturn .= "\n     ";
    $mReturn .= "<td>";
    $mReturn .= "\n  ";
    $mReturn .= "\n       ";

    $mReturn .= '<table class="DLInfoTable" cellpadding="1" cellspacing="1" align="center" width="100%">';
    $mReturn .= "\n         ";
    $mReturn .= "<tr>";
    $mReturn .= "\n           ";
    $mReturn .= "<td>";
    $mReturn .= "\n             ";
    $mReturn .= htmlentities( "Paginar Listado cada " );

    if( $this -> length < 100 )
    {
      $mReturn .= "<b>".$this -> limit."</b> ";
      $mReturn .= "\n             ";
      $mReturn .= '<input type="hidden" name="DLLimiter" id="DLLimiterID" value="100" />';
    }
    else
    {
      $mReturn .= "\n             ";
      $mReturn .= '<select name="DLLimiter" id="DLLimiterID" onchange="javascript:DLLimit( this );">';
      ( $this -> limit == 100 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
      $mReturn .= "\n               ";
      $mReturn .= '<option value="100"'.$mSelected.'>100</option>';
      if( $this -> length >= 250 )
      {
        ( $this -> limit == 250 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
        $mReturn .= "\n               ";
        $mReturn .= '<option value="250"'.$mSelected.'>250</option>';
      }
      if( $this -> length >= 500 )
      {
        ( $this -> limit == 500 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
        $mReturn .= "\n               ";
        $mReturn .= '<option value="500"'.$mSelected.'>500</option>';
      }
      if( $this -> length >= 750 )
      {
        ( $this -> limit == 750 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
        $mReturn .= "\n               ";
        $mReturn .= '<option value="750"'.$mSelected.'>750</option>';
      }
      if( $this -> length >= 1000 )
      {
        ( $this -> limit == 1000 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
        $mReturn .= "\n               ";
        $mReturn .= '<option value="1000"'.$mSelected.'>1000</option>';
      }
      $mReturn .= "\n             ";
      $mReturn .= '</select>';
      $mReturn .= "\n           ";
    }

    $mReturn .= "registros.";
    $mReturn .= "\n         ";
    $mReturn .= "</td>";
    $mReturn .= "\n       ";
    $mReturn .= "</tr>";
    $mReturn .= "\n       ";
    $mReturn .= "<tr>";
    $mReturn .= "\n         ";
    $mReturn .= "<td>";
    $mReturn .= "\n           ";
    $mReturn .= "Total Registros: <b>".$this -> length."</b>.  Listados <b>".$this -> rows."</b>";
    $mReturn .= " del <b>".( $this -> offset + 1 )."</b> al <b>".( $this -> rows + $this -> offset )."</b>.";
    $mReturn .= "\n         ";
    $mReturn .= "</td>";
    $mReturn .= "\n       ";
    $mReturn .= "</tr>";
    $mReturn .= "\n       ";
    $mReturn .= "<tr>";
    $mReturn .= "\n         ";
    $mReturn .= "<td>";

    $mReturn .= htmlentities( "Pagina" )." Nro. ";

    if( $this -> length < 100 )
    {
      $mReturn .= "<b>".$this -> page."</b> ";
      $mReturn .= "\n           ";
      $mReturn .= '<input type="hidden" id="DLNroPageID" value="'.$this -> page.'" />';
    }
    else
    {
      $mReturn .= "\n           ";
      $mReturn .= '<input type="text" id="DLNroPageID" value="'.$this -> page.'" maxlength="'.strlen( $mPages ).'" size="1" ';
      $mReturn .= 'onblur="javascript:DLPage( this.value );" ';
      $mReturn .= 'onkeypress="return NumericInput( event );" ';
      //$mReturn .= 'onkeypress="return DLPageOnEnter( event, this.value );" ';
      $mReturn .= 'onkeyup="javascript:DLPageFormat( this, '.$mPages.' )" ';
      $mReturn .= '/>';
      $mReturn .= "\n           ";
    }
    $mReturn .= "de <b>".$mPages."</b> ".htmlentities( "páginas." );
    $mReturn .= " Orden por <b>\"".( htmlentities( $this -> headers[$this -> sort_col-1][0] ) )."\"</b> ";

    if( $this -> way == "ASC" ) 
      $mReturn .= "Ascendente.";
    else
      $mReturn .= "Descendente.";

    $mReturn .= "</td>";
    $mReturn .= "\n         ";
    $mReturn .= "</tr>";
    $mReturn .= "\n         ";
    $mReturn .= "<tr>";
    $mReturn .= "\n           ";
    $mReturn .= "<td>";
    $mReturn .= "\n             ";

    if( $this -> page == 1 )
    {
      $mReturn .= '<span class="DLNormal">'.htmlentities( "[ Primera ] [ Anterior ] " ).'</span>';
    }
    else
    {
      $mReturn .= '<a href="#" onclick="javascript:DLPage( 1 );" title="'.htmlentities( "Ir a la Primera Pagina (1)" ).'">[ Primera ]</a> ';
      $mReturn .= '<a href="#" onclick="javascript:DLPage( '.( $this -> page - 1 ).' );" title="'.htmlentities( "Ir a la Anterior Pagina" ).' ('.( $this -> page-1 ).')">[ Anterior ]</a> ';
    }
    ( $this -> page > 7 ) ? $s = $this -> page - 7 : $s = 1;
    $p = $s + 14;

    if( $p > $mPages )
      $p = $mPages;

    for( $i=$s; $i<=$p; $i++ )
    {
      if( $this->page == $i )
      {
        $mReturn .= '<span class="DLNormal">'.$i.'</span>';
      }
      else
      {
        $mReturn .= '<a href="#" onclick="javascript:DLPage( '.$i.' );" title="'.htmlentities( "Ir a la Pagina ".$i ).'">'.$i.'</a>';
      }
      $mReturn .= " ";
    }
    if( $this -> page == $mPages )
    {
      $mReturn .= '<span class="DLNormal">'.htmlentities( "[ Siguiente ] [ Última ] " ).'</span>';
    }
    else
    {
      $mReturn .= '<a href="#" onclick="javascript:DLPage( '.( $this -> page + 1 ).' );" title="'.htmlentities( "Ir a la Siguiente Pagina" ).' ('.( $this -> page+1 ).')">[ Siguiente ]</a> ';
      $mReturn .= '<a href="#" onclick="javascript:DLPage( '.$mPages.' );" title="'.htmlentities( "Ir a la ultima Pagina" ).' ('.( $mPages ).')">[ '.htmlentities( "Última" ).' ]</a> ';
    }
    
    
    if ( strtolower( $this -> cClose ) != "no" )
    {
        $mReturn .= '<a href="#" onclick="javascript:DLClose();" title="'.htmlentities( "Cerrar Listado" ).'">[ '.htmlentities( "Cerrar Listado" ).' ]</a>';
    }

    $mReturn .= "\n           ";
    $mReturn .= "</td>";
    $mReturn .= "\n         ";
    $mReturn .= "</tr>";
    $mReturn .= "\n       ";
    $mReturn .= "</table>";

    $mReturn .= "\n  ";
    $mReturn .= "\n     ";
    $mReturn .= "</td>";
    $mReturn .= "\n   ";
    $mReturn .= "</tr>";
    $mReturn .= "\n   ";
    $mReturn .= "<tr>";
    $mReturn .= "\n     ";
    $mReturn .= "<td>";

    $mReturn .= "\n       ";
    $mReturn .= '<table class="DLTable" cellpadding="1" cellspacing="1" align="center" width="100%">';
    if( $this -> headers )
    {
      $mReturn .= "\n         ";
      $mReturn .= '<tr id="DLRowHeader" class="DLRowHeader">';
      if( $this -> check != "no" )
      {
        $mReturn .= "\n           ";
        $mReturn .= '<th>';
        $mReturn .= "\n             ";
        $mReturn .= '<input type="checkbox" id="DLCheckAll" checked="checked" disabled="disabled" />';
        $mReturn .= "\n           ";
        $mReturn .= '</th>';
      }

      for( $h=0, $this -> cols = count( $this -> headers ); $h < $this -> cols; $h++ ) 
      {
        $mHeader = $this -> headers[$h];
        $mProperties = $this -> GetProperties( $mHeader[1] );
        $this -> fields[] = $mProperties["field"];
        isset( $mProperties["width"] ) ? $width = $mProperties["width"] : $mWidth = "";

        if( $this -> way == "ASC" && ( $this -> sort_col - 1 ) == $h )
          $htitle = "Ordenar por ".htmlentities( $mHeader[0] )." Descendente.";
        else
          $htitle = "Ordenar por ".htmlentities( $mHeader[0] )." Ascendente.";

        $mReturn .= "\n           ";
        $mReturn .= '<th align="center" width="'.$width.'">';
        $mReturn .= "\n             ";
        $mReturn .= '<a id="DLHeader" class="DLHeader" href="#" onclick="javascript:DLSort( '.( $h + 1 ).' );" title="'.$htitle.'">';
        $mReturn .= htmlentities( $mHeader[0] );
        $mReturn .= '</a>';

        if( ( $this -> sort_col-1 ) == $h )
        {
          $mReturn .= '&nbsp;';
          if( $this -> way == "ASC" )
            $mReturn .= '<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/dinamic_list/row_up.gif" />';//
          else
            $mReturn .= '<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/dinamic_list/row_down.gif" />';//
        }
        $mReturn .= "\n             ";
        $mReturn .= '<br />';
        $mReturn .= "\n             ";

        if( $this -> filters[$h] )
        {
          $value = $this -> filters[$h];
        }
        else     
        {
          if( !isset( $mProperties["value"] ) )
            $value = "";
          else
            $value  = $mProperties["value"];
        }

        if( $mHeader[2] )
        {
          $mReturn .=  '<select id="DLFilter'.$h.'" class="DLSelect" onchange="javascript:DLFilter();">';

          for( $o = 0, $osize = count( $mHeader[2] ); $o < $osize; $o++ )
          {
            $mOption = $mHeader[2][$o];
            $mReturn .= "\n               ";
            $mReturn .= '<option value="'.$mOption[0].'"';
            if( $mOption[0] == $value )
              $mReturn .= ' selected="selected"';
            $mReturn .= '>';
            $mReturn .= htmlentities( $mOption[1] );
            $mReturn .= '</option>';
          }
          $mReturn .= "\n             ";
          $mReturn .= '</select>';
        }
        else
        {
          $mReturn .= '<input type="text" id="DLFilter'.$h.'" class="DLInput" value="'.$value.'" onblur="javascript:DLFilter();" onkeypress="return DLFilterOnEnter( event );" />';
        }
        $mReturn .= "\n           ";
        $mReturn .= '</th>';

      } // fin del for

      $mReturn .= "\n         ";
      $mReturn .= '</tr>';
    }

    if( count( $this -> matrix ) == 0 )  // Si no hay registros.
    {
      ( $this -> check != "no" ) ? $ck = $this -> cols + 1 : $ck = $this -> cols;
      $mReturn .= "\n         ";
      $mReturn .= '<tr id="DLNullRow" class="DLNULLRow">';
      $mReturn .= "\n           ";
      $mReturn .= '<td id="DLNullCell" colspan="'.$ck.'" style="color:white">';
      $mReturn .= htmlentities( "No se encontraron registros." );
      $mReturn .= '</td>';
      $mReturn .= "\n         ";
      $mReturn .= '</tr>';
    }
    else   // Si hay registros.
    {
      $mArraySelected = explode( "::", $this -> selected );

      for( $r=0, $size = count( $this -> matrix ); $r < $size; $r++ )
      {

        $r % 2 == 0 ? $style = "DLRow1" : $style = "DLRow2";

/*
        if( in_array( $this -> matrix[$r][$this -> pkey - 1], $mArraySelected ) )
          $mRowClass = "DLRowClick";
        else
*/
          $mRowClass = $style;


        $rid = "DLRow".$r;
        $row = $this -> matrix[$r];
        $mReturn .= "\n         ";
        $mReturn .= '<tr id="'.$rid.'" class="'.$mRowClass.'" ';
        $mReturn .= 'onclick="DLRowClick( \''.$r.'\', \''.( $this -> pkey - 1 ).'\', \''.$style.'\', \''.$this -> check.'\' );" ';
        $mReturn .= 'title="Fila '.( $r+1 ).', Registro '.( $this -> offset + $r+1 ).'." ';
        $mReturn .= '>';

        if( $this -> check != "no" )
        {
          $mReturn .= "\n           ";
          $mReturn .= '<td>';
          $mReturn .= "\n             ";

          if( in_array( $this -> matrix[$r][$this -> pkey - 1], $mArraySelected ) )
            $mReturn .= '<input type="checkbox" id="DLCheck'.$r.'" checked="checked" />';
          else
            $mReturn .= '<input type="checkbox" id="DLCheck'.$r.'" />';

          $mReturn .= "\n           ";
          $mReturn .= '</td>';
        }

        for( $c=0; $c < $this -> cols; $c++ )
        {

          $mHeader = $this -> headers[$c];
          $mProperties = $this -> GetProperties( $mHeader[1] );
          $cid = "DLCell".$r."-".$c;
          $mItemLink = "DLLink".$r."-".$c;
          $mItem = $row[$c];

          if( !isset( $mProperties["align"] ) )
            $mProperties["align"] = "left";

          if( !isset( $mProperties["onclick"] ) )
            $mProperties["onclick"] = "";

          $mReturn .= "\n           ";
          $mReturn .= '<td id="'.$cid.'" align="'.$mProperties["align"].'">';

          if( !isset( $mProperties["type"] ) )
            $mProperties["type"] = "";

          if( $mProperties["type"] == "link" )
            $mReturn .= '<a class="DLLink" id="'.$mItemLink.'" onclick="DLGet_Item( \''.$r.'\', \''.$c.'\'  ); '.$mProperties["onclick"].'">';

          $mReturn .= htmlentities( $mItem );

          if( $mProperties["type"] == "link" )
            $mReturn .= '</a>';
            $mReturn .= '</td>';
        }

        if( is_array( $this -> cHiddens) )
          foreach( $this -> cHiddens as $mHiddens )
          {
            $ind = $mHiddens['value'];
            $mReturn .= "\n           ";
            $mReturn .= '<input type="hidden" name="'.$mHiddens['name'].$r.'" id="'.$mHiddens['name'].$r.'ID" value="'.$this -> matrix[$r][$ind].'" />';
          }

        $mReturn .= "\n          ";
        $mReturn .= '</tr>';
      }
    }
    $mReturn .= "\n        ";
    $mReturn .= '</table>';

    $mReturn .= "\n      ";
    $mReturn .= "</td>";
    $mReturn .= "\n    ";
    $mReturn .= "</tr>";
    $mReturn .= "\n    ";
    $mReturn .= "<tr>";
    $mReturn .= "\n      ";
    $mReturn .= "<td>";       

    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" id="Sort_ColID" value="'.$this -> sort_col.'" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" id="WayID" value="'.$this -> way.'" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" id="HeadersID" value="'.$this -> cols.'" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" id="LabelsID" value="'.$this -> labels.'" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" id="ActualRowID" value="" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" id="ActualCellID" value="" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" id="ActualItemID" value="" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" name="DLSelectedRows" id="DLSelectedRowsID" value="'.$this -> selected.'" />';
    $mReturn .= "\n        ";
    $mReturn .= '<input type="hidden" name="DLTotal" id="DLTotalID" value="'.sizeof( $this -> matrix ).'" />';
    

    $mReturn .= "\n      ";
    $mReturn .= "</td>";
    $mReturn .= "\n    ";
    $mReturn .= "</tr>";

    $mReturn .= "\n  ";
    $mReturn .= "</table>";

    return $mReturn;
  }

}
?>