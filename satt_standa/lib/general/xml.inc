<?php
/*! \file: dinamic_xml.inc
 *  \brief:  Archivo para formar el achivo XML
 *  \author: Ing. Christiam Barrera Arango,  Ing. Joan Voltaire Quintero C.
 *  \author email: christiam.barrera@intrared.net, joan.quintero@intrared.net
 *  \version: 2.0
 *  \Company : Intrared LTDA
 *  \date :   12-11-2008 Bogotá - Colombia
*/

/*CLASE DinamicXML*/
Class Xml
{
    var $cXml      = NULL;//variable para capturar lo q se va a imprimir
    var $cType     = NULL;//tipo  de componente
    var $cTagID    = NULL;//id del componente
    var $cValue    = NULL;//valor del componente
    var $cOptions  = array();//opciones para los select

/*! \fn DinamicXML( $matrix = "")
 *  \brief Constructor de Objeto
 *  \param $matrix = "" matriz con los datos del formularion a llenar
*  \return echo  $this -> cXml  : impresion del XML
 */
    function Xml( $matrix = "" )
    {
        header("Content-type: text/xml" );//cabcera para crear como archivo XML

        $this -> cXml   = '<?xml version="1.0" encoding="iso-8859-1"?>';//version y codificacion del XML
        $this -> cXml  .= "\n"; //fin de linea
        $this -> cXml  .= '<root>';//imprimo un tag root

        foreach( $matrix as $row )//lectura de  la matris por filas
        {
            $this -> cType    = $row[0];//capturo el tipo de la fila seleccionada
            $this -> cTagID   = $row[1];//capturo el id de la fila seleccionada
            $this -> cValue   = $row[2];//capturo el value de la fila seleccionada
            $this -> cOptions = $row[3];//capturo las opciones para usarlas en caso de que el componente sea un select

            switch( $this -> cType )//segun el tipo de componente
            {
                case "text" ://en caso de ser un componente texto
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<text id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'"></text>';//creo un tag de text con id y value
                break;
                case "radio" ://es caso de ser un componente radio
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<radio id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'"></radio>';//creo un tag de radio con id y value
                break;
                case "check" ://es caso de ser un componente check
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<check id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'"></check>';//creo un tag  de check con id y value
                break;
                case "layer" ://es caso de ser un componente layer
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<layer id="'.$this -> cTagID.'" innerHTML="'.rawurlencode( $this -> cValue ).'"></layer>';//creo un tag  de layer con id y value
                break;
                case "select" ://es caso de ser un componente select
                {
                    if($this -> cOptions != NULL )
                    {
                        $this -> cXml  .= "\n\t";//fin de linea mas tabular
                        $this -> cXml  .= '<select id="'.$this -> cTagID.'">';//creo un tag de select para incluirles las opciones a usar
                        $this -> cXml  .= "\n\t\t";//fin de linea mas tabular,  tabular
                        $this -> cXml  .= '<option value="">--</option>';//creo una opcion vacia

                        for ( $i=0, $size=sizeof( $this -> cOptions ); $i<$size; $i++ )//se recorre el array de las opciones
                        {
                            $value = $this -> cOptions[$i][0];//capturo rl valor de las opciones
                            $label = $this -> cOptions[$i][1];//capturo la etuiqueta de las opciones

                            $selected = $this -> cValue;//capturo el objeto a seleccionar

                            $this -> cXml  .= "\n\t\t";//fin de linea mas tabular,  tabular
                            $this -> cXml  .= '<option value="'.rawurlencode( $value ).'"';//creo la opcion con su valor respectivo

                            if ( $value == $selected )//comparo si el option actual es igual al q se debe seleccionar
                            {
                                $this -> cXml  .= ' selected="selected" ';// le inserto el atributo de seleccionado
                            }

                            $this -> cXml  .= '>';//cierro el tag de opcion
                            $this -> cXml  .= rawurlencode( $label );//inserto la etuiqueta de la opcion
                            $this -> cXml  .= '</option>';//cierro la opcion
                        }

                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '</select>';//cierro el select
                    }
                    else
                    {
                        $this -> cXml  .= "\n\t";//fin de linea mas tabular
                        $this -> cXml  .= '<selection id="'.$this -> cTagID.'" value="'.$this -> cValue.'"></selection>';//se crea un tag con el select y el value es la opcion a seleccionar.
                    }
                }
                break;
            
                case "multiple" ://es caso de ser un componente select
                {
                    if($this -> cOptions != NULL )
                    {
                        $this -> cXml  .= "\n\t";//fin de linea mas tabular
                        $this -> cXml  .= '<multiple id="'.$this -> cTagID.'">';//creo un tag de select para incluirles las opciones a usar
                        $this -> cXml  .= "\n\t\t";//fin de linea mas tabular,  tabular
                        $this -> cXml  .= '<option value="">--</option>';//creo una opcion vacia

                        for ( $i=0, $size=sizeof( $this -> cOptions ); $i<$size; $i++ )//se recorre el array de las opciones
                        {
                            $value = $this -> cOptions[$i][0];//capturo rl valor de las opciones
                            $label = $this -> cOptions[$i][1];//capturo la etuiqueta de las opciones

                            $selected = $this -> cValue;//capturo el objeto a seleccionar

                            $this -> cXml  .= "\n\t\t";//fin de linea mas tabular,  tabular
                            $this -> cXml  .= '<option value="'.rawurlencode( $value ).'"';//creo la opcion con su valor respectivo

                            for ( $k=0, $sizeValues=sizeof( $selected); $k<$sizeValues; $k++ )
                            {
                                if ( $value ==  $selected[$k] )//comparo si el option actual es igual al q se debe seleccionar
                                {
                                    $this -> cXml  .= ' selected="selected" ';// le inserto el atributo de seleccionado
                                }
                            }

                            $this -> cXml  .= '>';//cierro el tag de opcion
                            $this -> cXml  .= rawurlencode( $label );//inserto la etuiqueta de la opcion
                            $this -> cXml  .= '</option>';//cierro la opcion
                        }

                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '</multiple>';//cierro el select
                    }
                    else
                    {
                    
                        $selected = $this -> cValue;
                        $id = $this -> cTagID;
                        
                        for ( $k=0, $sizeValues=sizeof( $selected); $k<$sizeValues; $k++ )
                        {
                            $this -> cXml  .= "\n\t";//fin de linea mas tabular
                            $this -> cXml  .= '<selection id="'.$id.'" value="'.$selected[$k].'"></selection>';
                        }
                    }
                }
                break;
                
                case "form" :
                        $this -> cXml  .= "\n\t";//fin de linea mas tabular
                        $this -> cXml  .= '<form id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'" ids = "'.$this -> cOptions.'"></form>';
                break;
                
                case "focus" :
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<focus id="'.$this -> cTagID.'"></focus>';
                break;
                
                case "image" :
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<image id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'"></image>';
                break;
                
                case "read" :
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<read id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'"></read>';
                break;
                
                case "disable" :
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<disable id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'"></disable>';
                break;
                
                case "alert" :
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<alert value="'.rawurlencode( $this -> cValue ).'"></alert>';
                break;
                
                case "class" :
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<class id="'.$this -> cTagID.'" value="'.rawurlencode( $this -> cValue ).'"></class>';
                break;
                
                case "script" :
                    $this -> cXml  .= "\n\t";//fin de linea mas tabular
                    $this -> cXml  .= '<script value="'.rawurlencode( $this -> cValue ).'"></script>';
                break;
                
            }
        }
        $this -> cXml  .= "\n";//fin de linea
        $this -> cXml  .= '</root>';//cierro el root

        echo  $this -> cXml ;//imprimo el string creado

    }
}
?>