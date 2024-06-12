<?php
/*
-------------------------------------------------------------------------------------------------------------------------------------------------
@Class: DinamicList
@Author: Ing. Christiam Barrera Arango
@Company: Intrared LTDA. Bogotá - Colombia
@Date: 2008-08-31
-------------------------------------------------------------------------------------------------------------------------------------------------
*/


Class DinamicList    {
    var $base;
    var $sql;
    var $query;
    var $matrix;
    var $headers;
    var $options;
    var $create;
    var $offset;
    var $limit;
    var $length;
    var $fields;
    var $fieldsHaving;
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
    var $excel;
    var $search;

    function __construct( &$conecction, $base, $pkey=1, $check="no", $way= "ASC" )
    {
      $this -> base = $base;
      $this -> check = $check;
      $this -> query = new Consulta( $this->base, $conecction );
      $this -> length = $this -> query -> ret_num_rows();

      $this -> pkey = $pkey;
      $this -> way = $way;

       

      //------------------------------
      if( !$this -> offset || ( $this -> offset < 0 ) )
        $this -> offset = 0;
      //------------------------------

      //------------------------------
      if( !$this -> limit )
        $this -> limit = 10;
      //------------------------------

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

    function SetHeader( $label, $properties="", $options="", $having=false)
    {
      $this -> headers[] = Array( $label, $properties, $options, $having );
    }    

    function SetOption( $label, $properties="", $options="" )
    {
      $this -> options = Array( $label, $properties, $options );
    }
    
    function SetCreate( $label, $properties="", $options="" )
    {
      $this -> create = Array( $label, $properties, $options );
    }
    
    function SetExcel( $label, $properties="", $options="" )
    {
      $this -> excel = Array( $label, $properties, $options );
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

    function Ajax( &$conecction, $action, $sort_col="", $way="ASC", $filters="", $limit="", $page="", $selected="", $search="" )
    {

      $this->search = $search;

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
      $sqlHaving = "";
      if( FALSE !== strpos( $QueryGrou[0], "UNION" ) )
      {
         $QuerysDiv = explode( "UNION", $QueryGrou[0] );
         $QuerysDivHaving = array();

         for( $k = 0, $tot = count( $QuerysDiv ); $k < $tot; $k++ )
         {
           for( $i = 0; $i < $this -> cols; $i++ )
           {
              if( $this -> filters[$i] && !$this -> fieldsHaving[$i] && $search=="")
              {
                $QuerysDiv[$k] .= " AND ";
                $QuerysDiv[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
              }

              if($this -> filters[$i] && $this -> fieldsHaving[$i] && $search=="")
              {
                  $QuerysDivHaving[$k] .= " AND ";
                  $QuerysDivHaving[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
              }

              if(!$this -> fieldsHaving[$i] && $search!="")
              {
                $QuerysDiv[$k] .= $i ==0 ? "":" OR ";
                $QuerysDiv[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $search )."%'";
              }


           }
            $sql .=  $search!="" ? "AND (".$QuerysDiv[$k].")": $QuerysDiv[$k];
            $sqlHaving .= $QuerysDivHaving[$k];

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
            if( $this -> filters[$i] && !$this -> fieldsHaving[$i] && $search=="")
            {
              $QueryGrou[0] .= " AND ";
              $QueryGrou[0] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
            }

            if($this -> fieldsHaving[$i] && $search=="")
            {
              $QuerysDivHaving[0] .= " AND ";
              $QuerysDivHaving[0] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
            }

            if(!$this -> fieldsHaving[$i] && $search!="")
            {
              $QuerysDiv[0] .= $i ==0 ? "": " OR ";
              $QuerysDiv[0] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $search )."%'";
            }


         }
         $sql .=  $QueryGrou[0];
         $sql .= $search!="" ? "AND (".$QuerysDiv[0].")":'';
         $sqlHaving .= $QuerysDivHaving[0];
       }



       $sql .=" GROUP ".$QueryGrou[1];
    }
     elseif( FALSE !== strpos( $sql, "UNION" ) )
     {
       $QuerysDiv = explode( "UNION", $sql[0] );
       $QuerysDivHaving = array();
       $sql = "";
       $sqlHaving = "";

       for( $k = 0, $tot = count( $QuerysDiv ); $k < $tot; $k++ )
       {
         for( $i = 0; $i < $this -> cols; $i++ )
         {
           if( $this -> filters[$i] && !$this -> fieldsHaving[$i] && $search=="")
           {
             $QuerysDiv[$k] .= " AND ";
             $QuerysDiv[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
           }

           if($this -> fieldsHaving[$i] && $search=="")
           {
            $QuerysDivHaving[$k] .= " AND ";
            $QuerysDivHaving[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
           }

           if(!$this -> fieldsHaving[$i] && $search!="")
           {
             $QuerysDiv[$k] .= $i ==0 ? "":" OR ";
             $QuerysDiv[$k] .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $search )."%'";
           }

         }

         $sql .= $search!="" ? "AND (".$QuerysDiv[$k].")":$QuerysDiv[$k]; 
         $sqlHaving .= $QuerysDivHaving[$k];

         if( $k+1 < $tot )
         {
           $sql .= " UNION";
         }
       }
      }
      else
      {
        $slqWhere = '';

        for( $i = 0; $i < $this -> cols; $i++ )
        {
          if( $this -> filters[$i] && !$this -> fieldsHaving[$i] && $search=="")
          {
            $sql .= " AND ";
            $sql .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
          }

          if($this -> fieldsHaving[$i] && $search=="")
          {
            $sqlHaving  .= " AND ";
            $sqlHaving  .= "LOWER(".$this -> fields[$i].") LIKE '%".strtolower( $this -> filters[$i] )."%'";
          }

          if(!$this -> fieldsHaving[$i] && $search!="")
          {
            $slqWhere .= $i ==0 ? "":" OR ";
            $slqWhere .= "LOWER(".$this -> fields[$i].") LIKE '%".$search ."%'";
          }

        }

        $sql .=  $search!="" ? "AND (".$slqWhere.")": "";
      }
      
      $sql .= " HAVING 1=1 ".$sqlHaving;
      $sql .= " ORDER BY ".$this -> sort_col." ".$this -> way;
      
     //$sql = rawurlencode( $sql );

      //var_dump( $sql );
      
        #echo "<pre>"; print_r($sql); echo "</pre>";
     
      $this -> query = new Consulta( $sql, $conecction );

      $this -> length = $this -> query -> ret_num_rows();


      //------------------------------
      if( !$this -> offset || ( $this -> offset < 0 ) )
        $this -> offset = 0;
      //------------------------------

      //------------------------------
      if( !$this -> limit )
        $this -> limit = 10;
      //------------------------------

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
      if ( isset( $tag["value"] ) ) echo ' value="'.htmlentities( $tag["value"], ENT_COMPAT, 'ISO-8859-1', true ).'"';
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
      if ( isset( $tag["title"] ) ) echo ' title="'.htmlentities( $tag["title"], ENT_COMPAT, 'ISO-8859-1', true ).'"';
      if ( isset( $tag["readonly"] ) ) echo ' readonly="readonly"';
      if ( isset( $tag["disabled"] ) ) echo ' disabled="disabled"';
      if ( isset( $tag["selected"] ) ) echo ' selected="selected"';
      if ( isset( $tag["checked"] ) ) echo ' checked="checked"';
      if ( isset( $tag["onclikDisable"] ) ) echo ' onclick="javascript:'.$tag["onclikDisable"].'"';
      if ( isset( $tag["onclikEnable"] ) ) echo ' onclick="javascript:'.$tag["onclikEnable"].'"';
      if ( isset( $tag["onclikEdit"] ) )  echo ' onclick="javascript:'.$tag["onclikEdit"].'"';
      if ( isset( $tag["onclikPrint"] ) ) echo ' onclick="javascript:'.$tag["onclikPrint"].'"';
      if ( isset( $tag["onclickCopy"] ) ) echo ' onclick="javascript:'.$tag["onclickCopy"].'"';
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

    if( $this -> length <= 10 )
    {
      $mPages = 1;
    }
    else
    {
      $mPages = ceil( $this -> length / $this -> limit );
    }

    $mReturn .="<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/bootstrap.css' type='text/css'>";

    $mReturn .= "<div id='tableData' >";
      $mReturn .= "<div class='row'>";
        $mReturn .= "<div class='col-sm-3'>";
          $mReturn .= "<div class='form-group row'>";
              $mReturn .= "<label class='col-sm-4 col-form-label' style='margin-top: 10px;'>Buscar:</label>";
              $mReturn .= "<div class='col-sm-8'>";
                $mReturn .= "<input type='search' class='form-control input-sm' id='DLSeachID' value='".$this->search."' onchange='javascript:DLFilter();'  placeholder='....'>";
              $mReturn .= "</div>";
          $mReturn .= "</div>";
        $mReturn .= "</div>";
        $mReturn .= "<div class='col-sm-2'>";
        $mReturn .= "</div>";
        $mReturn .= "<div class='col-sm-2'>";
          $mReturn .= "<div class='form-group row'>";
              $mReturn .= "<label class='col-sm-4 col-form-label' style='margin-top: 10px;'>Mostrar:</label>";
              $mReturn .= "<div class='col-sm-8'>";
                $mReturn .= "<select class='form-control custom-select' name='DLLimiter' id='DLLimiterID' onchange='javascript:DLLimit( this );'>";
                    ( $this -> limit == 10 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
                    $mReturn .= '<option value="10"'.$mSelected.'>10</option>';

                    if( $this -> length >= 20 )
                    {
                      ( $this -> limit == 20 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
                      $mReturn .= '<option value="20"'.$mSelected.'>20</option>';
                    }
                    if( $this -> length >= 50 )
                    {
                      ( $this -> limit == 50 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
                      $mReturn .= '<option value="50"'.$mSelected.'>50</option>';
                    }
                    if( $this -> length >= 750 )
                    {
                      ( $this -> limit == 100 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
                      $mReturn .= '<option value="100"'.$mSelected.'>100</option>';
                    }
                    if( $this -> length >= 500 )
                    {
                      ( $this -> limit == 500 ) ? $mSelected = ' selected="selected"' : $mSelected = '';
                      $mReturn .= '<option value="500"'.$mSelected.'>500</option>';
                    }
                        
                $mReturn .="</select>";
              $mReturn .= "</div>";
          $mReturn .= "</div>";
        $mReturn .= "</div>";
        $mReturn .= "<div class='col-sm-1'>";
        $mReturn .= "</div>";
        $mReturn .= "<div class='col-sm-4'>";

          $mReturn .= "<div class='row'>";
            if( $this -> excel )
            {
                
                $mReturn .= "<div class='col-sm-4'>";
                  $mOptionse = $this -> GetProperties( $this -> excel[1] );
                  if( !isset( $mOptionse["align"] ) )
                    $mOptionse["align"] = "center";

                  if( !isset( $mOptionse["onclick"] ) )
                    $mOptionse["onclick"] = "";

                  $mReturn .= '<a href="#" onclick="javascript:'.$mOptionse["onclick"].'" class="btn btn-success" title="Exportar Excel">'. $this -> excel[0].'</a> ';
                $mReturn .= "</div>";
            }

            if( $this -> create )
            {
              $mReturn .= "<div class='col-sm-4'>";
                $mOptionsx = $this -> GetProperties( $this -> create[1] );
                if( !isset( $mOptionsx["align"] ) )
                  $mOptionsx["align"] = "center";

                if( !isset( $mOptionsx["onclick"] ) )
                  $mOptionsx["onclick"] = "";

                $mReturn .= '<a href="#" onclick="javascript:'.$mOptionsx["onclick"].'" class="btn btn-success" title="Crear Registro">'. $this -> create[0].'</a> ';
              $mReturn .= "</div>";
            }

            if ( strtolower( $this -> cClose ) != "no" )
            {
              $mReturn .= "<div class='col-sm-4'>";
                $mReturn .= '<a href="#" onclick="javascript:DLClose();" title="'.htmlentities( "Cerrar Listado" , ENT_COMPAT, 'ISO-8859-1', true).'">[ '.htmlentities( "Cerrar Listado", ENT_COMPAT, 'ISO-8859-1', true ).' ]</a>';
              $mReturn .= "</div>";
            }
          $mReturn .= "</div>";

        $mReturn .= "</div>";

      $mReturn .= "</div>";
    $mReturn .= "</div>";


    $mReturn .= "\n  ";
    $mReturn .= '<table cellpadding="1" cellspacing="1" align="center" width="100%">';
    $mReturn .= "<tr>";
    $mReturn .= "\n     ";
    $mReturn .= "<td>";

    $mReturn .= "\n       ";
    $mReturn .= "<table class='table table-striped table-bordered  table-sm'>";
      if( $this -> headers )
      {
        $mReturn .= "<thead>";
        if( $this -> check != "no" )
        {
          $mReturn .= '<th scope="col">';
            $mReturn .= '<input type="checkbox" id="DLCheckAll" checked="checked" disabled="disabled" />';
          $mReturn .= '</th>';
        }
        
        for( $h=0, $this -> cols = count( $this -> headers ); $h < $this -> cols; $h++ ) 
        {
          $mHeader = $this -> headers[$h];
          $mProperties = $this -> GetProperties( $mHeader[1] );
          $this -> fields[] = $mProperties["field"];
          $this -> fieldsHaving[] = isset($mProperties["having"]) ? true: false;

          isset( $mProperties["width"] ) ? $width = $mProperties["width"] : $mWidth = "";

          if( $this -> way == "ASC" && ( $this -> sort_col - 1 ) == $h )
            $htitle = "Ordenar por ".htmlentities( $mHeader[0] , ENT_COMPAT, 'ISO-8859-1', true)." Descendente.";
          else
            $htitle = "Ordenar por ".htmlentities( $mHeader[0] , ENT_COMPAT, 'ISO-8859-1', true)." Ascendente.";

          $mReturn .= '<th scope="col">';
            $mReturn .= '<a id="DLHeader" style="color: black;" class="" href="#" onclick="javascript:DLSort( '.( $h + 1 ).' );" title="'.$htitle.'">';
              
              $mReturn .= htmlentities( $mHeader[0] , ENT_COMPAT, 'ISO-8859-1', true);
              if( ( $this -> sort_col-1 ) == $h )
              {
                if( $this -> way == "ASC" )
                  $mReturn .= '<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/dinamic_list/row_up.gif" />';//
                else
                  $mReturn .= '<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/dinamic_list/row_down.gif" />';//
              }
            $mReturn .= '</a>';


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
              $mReturn .=  '<select id="DLFilter'.$h.'" class="form-control custom-select" onchange="javascript:DLFilter();">';

              for( $o = 0, $osize = count( $mHeader[2] ); $o < $osize; $o++ )
              {
                $mOption = $mHeader[2][$o];
                $mReturn .= "\n               ";
                $mReturn .= '<option value="'.$mOption[0].'"';
                if( $mOption[0] == $value )
                  $mReturn .= ' selected="selected"';
                $mReturn .= '>';
                $mReturn .= htmlentities( $mOption[1] , ENT_COMPAT, 'ISO-8859-1', true);
                $mReturn .= '</option>';
              }
              $mReturn .= "\n             ";
              $mReturn .= '</select>';
            }
            else
            {
              $mReturn .= '<input type="text" id="DLFilter'.$h.'" class="form-control input-sm" style="height: 20;" value="'.$value.'" onblur="javascript:DLFilter();" onkeypress="return DLFilterOnEnter( event );" />';
            }

          $mReturn .= '</th>';

        }

        if( $this -> options )
        { 
              $mReturn .= '<th scope="col">';
                $mReturn .= htmlentities( $this -> options[0] , ENT_COMPAT, 'ISO-8859-1', true);
              $mReturn .= '</th>';
        }
        $mReturn .= "</thead>";
          
      }

      $mReturn .= "<tbody>";
        if( count( $this -> matrix ) == 0 )  // Si no hay registros.
        {
          ( $this -> check != "no" ) ? $ck = $this -> cols + 1 : $ck = $this -> cols;
        
          $mReturn .= '<tr>';
            $mReturn .= '<td  colspan="'.$ck.'">';
            $mReturn .= htmlentities( "No se encontraron registros." , ENT_COMPAT, 'ISO-8859-1', true  );
            $mReturn .= '</td>';
          $mReturn .= '</tr>';
        }else{
          $mArraySelected = explode( "::", $this -> selected );

          for( $r=0, $size = count( $this -> matrix ); $r < $size; $r++ )
          {
            $r % 2 == 0 ? $style = "DLRow1" : $style = "DLRow2";

            $row = $this -> matrix[$r];
            $mReturn .= '<tr onclick="DLRowClick( \''.$r.'\', \''.( $this -> pkey - 1 ).'\', \''.$style.'\', \''.$this -> check.'\' );" >';

            if( $this -> check != "no" )
            {
              
              $mReturn .= '<td>';

              if( in_array( $this -> matrix[$r][$this -> pkey - 1], $mArraySelected ) )
                $mReturn .= '<input type="checkbox" id="DLCheck'.$r.'" checked="checked" />';
              else
                $mReturn .= '<input type="checkbox" id="DLCheck'.$r.'" />';

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
                $mProperties["align"] = "center";
    
              if( !isset( $mProperties["onclick"] ) )
                $mProperties["onclick"] = "";
    
              $mReturn .= '<td id="'.$cid.'" align="'.$mProperties["align"].'">';
    
              if( !isset( $mProperties["type"] ) )
                $mProperties["type"] = "";
    
              if( $mProperties["type"] == "link" )
                $mReturn .= '<a  id="'.$mItemLink.'" onclick="DLGet_Item( \''.$r.'\', \''.$c.'\'  ); '.$mProperties["onclick"].'">';
    
              $mReturn .= htmlentities( $mItem , ENT_COMPAT, 'ISO-8859-1', true );
    
              if( $mProperties["type"] == "link" )
                $mReturn .= '</a>';
              
              $mReturn .= '</td>';
            }

            # Trae la ultima columna de una fila para validar las imagenes de las opciones de editar, inactivar, activar
            if( $this -> options )
            { 
                $mOptionsx = $this -> GetProperties( $this -> options[1] );
                if( !isset( $mOptionsx["align"] ) )
                  $mOptionsx["align"] = "center";

                if( !isset( $mOptionsx["onclick"] ) )
                  $mOptionsx["onclick"] = "";

                if( !isset( $mOptionsx["onclikdisable"] ) )
                  $mOptionsx["onclikdisable"] = "";

                if( !isset( $mOptionsx["onclikenable"] ) )
                  $mOptionsx["onclikenable"] = "";

                if( !isset( $mOptionsx["onclikedit"] ) )
                  $mOptionsx["onclikedit"] = "";

                if( !isset( $mOptionsx["onclikprint"] ) )
                  $mOptionsx["onclikprint"] = "";

                if( !isset( $mOptionsx["onclickcopy"] ) )
                  $mOptionsx["onclickcopy"] = "";


                $mOpt = end($row);

                $mReturn .= '<td style="text-align:center">';
                # Si esta activo muestra inactivar
                  if($mOptionsx["onclikdisable"] != "" || $mOptionsx["onclikenable"] != ""){
                    if( $mOpt == '1' || $mOpt == 1) { 
                    $mReturn .='<img title="Inactivar"  class="ImagenDinamicList" src="../'.DIR_APLICA_CENTRAL.'/images/delete.png"  onclick="javascript:'. $mOptionsx["onclikdisable"].'"   />&nbsp;&nbsp;&nbsp;&nbsp;';
                    }elseif( $mOpt == '0' || $mOpt == 0){ 
                    $mReturn .='<img title="Activar"  class="ImagenDinamicList"   src="../'.DIR_APLICA_CENTRAL.'/images/active.png" onclick="javascript:'. $mOptionsx["onclikenable"].'"     />&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                  }
                if($mOptionsx["onclikedit"]){          
                      $mReturn .='<img title="Editar" class="ImagenDinamicList"     src="../'.DIR_APLICA_CENTRAL.'/images/edit.png"   onclick="javascript:'. $mOptionsx["onclikedit"].'"/>';
                }

                  if($mOptionsx["onclikprint"] != ""){
                    $mReturn .='&nbsp;&nbsp;&nbsp;&nbsp;<img title="Imprimir" class="ImagenDinamicList"     src="../'.DIR_APLICA_CENTRAL.'/images/print.png"   onclick="javascript:'.  $mOptionsx["onclikprint"].'"/>';
                  }

                  if($mOptionsx["onclickcopy"] != ""){
                    $mReturn .='&nbsp;&nbsp;&nbsp;&nbsp;<img title="Copiar" class="ImagenDinamicList"     src="../'.DIR_APLICA_CENTRAL.'/images/cp.png"   onclick="javascript:'.  $mOptionsx["onclickcopy"].'"/>';
                  }
                $mReturn .= '</td>';
            }

            if( is_array( $this -> cHiddens) )
            foreach( $this -> cHiddens as $mHiddens )
            {
              $ind = $mHiddens['value'];
              $mReturn .= '<input type="hidden" name="'.$mHiddens['name'].$r.'" id="'.$mHiddens['name'].$r.'ID" value="'.$this -> matrix[$r][$ind].'" />';
            }

            $mReturn .= '</tr>';


          }

        }

      $mReturn .= "</tbody>";
    $mReturn .= "</table>";

    $mReturn .= "<div class='row'>";
        $mReturn .= "<div class='col-sm-4'>";
          $mReturn .= "Mostrando registros del ".( $this -> offset + 1 )." al ".( $this -> rows + $this -> offset )." de un total de ".$this -> length." registros";
        $mReturn .= "</div>";
        $mReturn .= "<div class='col-sm-8'>";
          $mReturn .= "<nav>";
            $mReturn .= "<ul class='pagination justify-content-end'>";
              $disabledPrevius =$this -> page == 1 ? 'disabled': '';
              $mReturn .= "<li class='page-item ".$disabledPrevius."'>";
              $mReturn .= '<a class="page-link" href="#" tabindex="-1" aria-disabled="true" onclick="javascript:DLPage( '.( $this -> page - 1 ).' );" title="'.htmlentities( "Ir a la Anterior Pagina" , ENT_COMPAT, 'ISO-8859-1', true).' ('.( $this -> page-1 ).')">Anterior</a>';
              $mReturn .= "</li>";

              ( $this -> page > 7 ) ? $s = $this -> page - 7 : $s = 1;
              $p = $s + 14;

              if( $p > $mPages )
                $p = $mPages;

              for( $i=$s; $i<=$p; $i++ )
              {
                if( $this->page == $i )
                {
                  $mReturn .= '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
                }
                else
                {
                  $mReturn .= '<li class="page-item"><a class="page-link" href="#" onclick="javascript:DLPage( '.$i.' );" title="'.htmlentities( "Ir a la Pagina ".$i , ENT_COMPAT, 'ISO-8859-1', true).'">'.$i.'</a>';
                }
              }

              if( $this -> page == $mPages )
              {
                $mReturn .= '<li class="page-item disabled"><a class="page-link" href="#">Siguiente</a></li>';
              }
              else
              {
                $mReturn .= '<li class="page-item"><a class="page-link" href="#" onclick="javascript:DLPage( '.( $this -> page + 1 ).' );" title="'.htmlentities( "Ir a la Siguiente Pagina" , ENT_COMPAT, 'ISO-8859-1', true).' ('.( $this -> page+1 ).')">Siguiente</a></li>';
              }     

            $mReturn .= "</ul>";
          $mReturn .= "</nav>";
        $mReturn .= "</div>";
    $mReturn .= "</div>";

    


    $mReturn .= '<table class="DLTable" cellpadding="1" cellspacing="1" align="center" width="100%">';
    
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