<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
class InfEstudiSeguri 
{
    var $conexion,
        $cod_aplica,
        $usuario;
    var $cNull = array(array('', '- Todos -'));
    var $cNullt = array();

    function __construct($co, $us, $ca) {
        include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");

        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        $this->filtro();
    }

    function filtro()
    {
        
        if ($_REQUEST['fecini'] == NULL || $_REQUEST['fecini'] == '') {
            $fec_actual = strtotime('-7 day', strtotime(date('Y-m-d')));
            $_REQUEST['fecini'] = date('Y-m-d', $fec_actual);
        }

        if ($_REQUEST['fecfin'] == NULL || $_REQUEST['fecfin'] == ''){
            $_REQUEST['fecfin'] = date('Y-m-d');
        }
        
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.table2excel.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.filter.min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.min.js\"></script>\n";

        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<script src='../".DIR_APLICA_CENTRAL."/js/new_ajax.js' language='javascript'></script>";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_estudi_seguri.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

        
        echo "<script src='../".DIR_APLICA_CENTRAL ."/js/dinamic_list.js' language='javascript'></script>";
        echo "<link type='text/css' href='../". DIR_APLICA_CENTRAL ."/estilos/dinamic_list.css' rel='stylesheet'>";
        
        echo "<style> 
            .small, small {
                font-size: 114% !important;
            }
            #divBodyID {
                width: 95vw;
                margin-left: 18px;
                overflow-x: auto;
                overflow-y: auto;
            }
        </style>";
        $transpor = $this->getTransport();

        $cod_transp = array();
        foreach(explode(',',$_REQUEST['transp']) as $row)
        {
            $cod_transp[] = str_replace('"', '', $row);
        } 

        echo "
            <script>
                jQuery(function($) { 
                    $( '#feciniID,#fecfinID' ).datepicker();      
                    $.mask.definitions['A']='[12]';
                    $.mask.definitions['M']='[01]';
                    $.mask.definitions['D']='[0123]';
                    $.mask.definitions['n']='[0123456789]';
                    $( '#feciniID,#fecfinID' ).mask('Annn-Mn-Dn');

                })

                $( document ).ready(function() {
                  
                  $('#cod_transpID').multiselect().multiselectfilter();
                  var string = '". str_replace('"', '', $_REQUEST['transp'])."';
                  var arrayFromPHP  =  string.split(',');

                  $('#cod_transpID').multiselect('widget').find(':checkbox[value=`bing`]').each(function()          {
                    this.click();
                  });


                  
                  var box_checke = $('input[type=checkbox]');

                  box_checke.each(function(i, o) {
                    var elemento = $(this);
                    i = 0, size = arrayFromPHP.length;
                    for(i; i < size; i++){
                      if($(elemento).attr('name') == 'multiselect_cod_transpID'){
                        console.log($(elemento).attr('value')+' = '+arrayFromPHP[i]);
                        if($(elemento).attr('value') == arrayFromPHP[i] && $(elemento).attr('value')!=''){
                          $('#'+$(elemento).attr('id')).attr('checked',true);
                        }
                      }
                    }
                  });
                  size = arrayFromPHP.length + 1;
                  $('.ui-state-default span:last').empty();
                  $('.ui-state-default span:last').append(size+' Seleccionado(s)');
                  console.log('---------');
                });
            </script>";

        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe Estudio Seguridad", "formulario\" id=\"formularioID");
            $formulario -> nueva_tabla();
            echo "<tr>";
            echo $this->listaSelect('Transportadora:', 'cod_transp', $transpor, 'cellInfo1', 1 );
            echo '<input type="hidden" name="transp" id="transp">';
            echo '<input type="hidden" name="opcion" id="opcion" value="1">';
            
            $formulario -> texto ("No. solicitud","text","cod_solici\" id=\"cod_soliciID",1,15,15,"","");
            $formulario->texto("Fecha Inicial:", "text", "fecini\"  id=\"feciniID", 0, 10, 10, "", $_REQUEST['fecini']);
            $formulario->texto("Fecha Final:", "text", "fecfinID\"  id=\"fecfinID", 1, 10, 10, "", $_REQUEST['fecfin']);

            $formulario -> nueva_tabla();
            echo "<BR>";
            $formulario -> botoni("Buscar","listar()",0);
            $formulario -> nueva_tabla();
            $formulario -> oculto("window","central",0);
            $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
            $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
            $formulario -> oculto("option\" id=\"optionID",'',0);
            echo "<hr style=\"border: 1px solid black;\">";

            
            $formulario->OpenDiv("name:divBody");
            echo $this->getInform();        
            $formulario->CloseDiv();
            $formulario-> cerrar();

    }

    function getInform(){

        $mSqlP="SELECT a.cod_solici, a.fec_creaci, a.cor_solici, 
                a.tel_solici, a.cel_solici,
                CASE 
                    WHEN a.cod_estcon='1' THEN 'Primario'
                    WHEN a.cod_estcon='2' THEN 'Estandar'
                    WHEN a.cod_estcon='3' THEN 'Full'
                    ELSE 'No Especificado'
                END as nom_estcon,
                CASE 
                    WHEN a.cod_tipest='C' THEN 'Conductor'
                    WHEN a.cod_tipest='V' THEN 'Vehiculo'
                    WHEN a.cod_tipest='CV' THEN 'Combinado (Conductor/Vehiculo)'
                END as nom_tipest, 
                CASE
                    WHEN a.ind_estseg='A' THEN 'Aprobado'
                    WHEN a.ind_estseg='R' THEN 'Rechazado'
                    WHEN a.ind_estseg='P' THEN 'Pendiente'
                    WHEN a.ind_estseg='C' THEN 'Cancelado'
                END as nom_estseg, a.obs_estseg, 
                a.fec_finsol, a.fec_venest, b.cod_tercer as 'cod_conduc', 
                b.cod_tipdoc as 'tip_doccon', b.nom_apell1 as 'nom_ape1con', b.nom_apell2 as 'nom_ape2con', 
                b.nom_person 'nom_nomcon', b.num_licenc as 'num_liccon', c.nom_catlic as 'nom_catcon', 
                b.fec_venlic as 'fec_vliccon', b.nom_arlxxx as 'nom_arlcon', b.nom_epsxxx as 'nom_epscon', 
                b.num_telmov as 'num_movcon', b.num_telefo as 'num_telcon', d.nom_ciudad as 'nom_ciucon', 
                b.dir_domici as 'dir_rescon', b.dir_emailx as 'dir_emacon', 
                CASE
                    WHEN b.ind_precom='0' THEN 'No'
                    WHEN b.ind_precom='1' THEN 'Si'
                END
                as ind_precom, b.val_compar, CASE
                    WHEN b.ind_preres='0' THEN 'No'
                    WHEN b.ind_preres='1' THEN 'Si'
                END as ind_preres, 
                b.val_resolu, e.num_placax, e.num_remolq,
                f.nom_marcax, g.nom_lineax, e.ano_modelo, 
                h.nom_colorx, i.cod_carroc, e.num_chasis, 
                e.num_motorx, e.num_soatxx, e.fec_vigsoa, 
                e.num_lictra, j.nom_operad, e.usr_gpsxxx, 
                e.clv_gpsxxx, e.obs_opegps, e.fre_opegps,
                CASE
                    WHEN e.ind_precom='0' THEN 'No'
                    WHEN e.ind_precom='1' THEN 'Si'
                END as 'ind_vehcom', e.val_compar as 'val_comveh', 
                CASE
                    WHEN e.ind_preres='0' THEN 'No'
                    WHEN e.ind_preres='1' THEN 'Si'
                END as 'ind_preveh',
                e.val_resolu as 'val_resveh', k.cod_tercer as 'cod_propie', k.cod_tipdoc as 'tip_docpro', 
                k.nom_apell1 as 'nom_ape1pro', k.nom_apell2 as 'nom_ape2pro', k.nom_person 'nom_nompro',   
                k.num_telmov as 'num_movpro', k.num_telefo as 'num_telpro', 
                l.nom_ciudad as 'nom_ciupro', k.dir_domici as 'dir_respro', 
                k.dir_emailx as 'dir_emapro'
            FROM ".BASE_DATOS.".tab_estseg_solici a 
            LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer b ON a.cod_conduc = b.cod_tercer 
            LEFT JOIN ".BASE_DATOS.".tab_genera_catlic c ON b.cod_catlic = c.cod_catlic 
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d ON b.cod_ciudad = d.cod_ciudad
            LEFT JOIN ".BASE_DATOS.".tab_estseg_vehicu e ON a.cod_vehicu = e.num_placax
            LEFT JOIN ".BASE_DATOS.".tab_genera_marcas f ON e.cod_marcax = f.cod_marcax
            LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas g ON e.cod_marcax = g.cod_marcax AND e.cod_lineax = g.cod_lineax
            LEFT JOIN ".BASE_DATOS.".tab_vehige_colore h ON e.cod_colorx = h.cod_colorx
            LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc i ON e.cod_carroc = i.cod_carroc
            LEFT JOIN satt_standa.tab_genera_opegps j ON e.cod_opegps = j.cod_operad
            LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer k ON e.cod_propie = k.cod_tercer
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad l ON k.cod_ciudad = l.cod_ciudad
            WHERE 1=1
        ";


        $mSqlP .= $_REQUEST['transp']  ? " AND a.cod_emptra IN (".$_REQUEST['transp'].") " : "";
        $mSqlP .= $_REQUEST['cod_solici'] ? " AND a.cod_solici = '".$_REQUEST['cod_solici']."'" : "";
        $mSqlP .= $_REQUEST['fecini'] && $_REQUEST['fecfin'] ? " AND a.fec_creaci BETWEEN '".$_REQUEST['fecini']."' AND '".$_REQUEST['fecfin']."'" : ""; 

        $consulta  = new Consulta($mSqlP, $this -> conexion);
        $data = $consulta -> ret_matriz();

        if(count($data) == 0 ){

            $mSql="SELECT 
                a.cod_solici,
                b.nom_tipest,
                a.cod_emptra,
                c.nom_tercer,
                d.cod_tercer as 'num_docume', 
                UCASE(
                    CONCAT(
                        d.nom_apell1, ' ', d.nom_apell2, ' ', 
                        d.nom_person
                    )
                ) as nom_conduct,
                e.num_placax,  
                CASE
                    WHEN a.ind_estseg='A' THEN 'Aprobado'
                    WHEN a.ind_estseg='R' THEN 'Rechazado'
                    WHEN a.ind_estseg='P' THEN 'Pendiente'
                END as nom_estseg,
                a.obs_estseg,
                a.usr_estseg,
                a.fec_creaci,
                a.usr_modifi,
                a.fec_finsol
                FROM ".BASE_DATOS.".tab_estseg_solici a 
                INNER JOIN ".BASE_DATOS.".tab_estseg_tipoxx b ON a.cod_tipest = b.cod_tipest 
                INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON a.cod_emptra = c.cod_tercer 
                LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer d ON a.cod_conduc = d.cod_tercer 
                LEFT JOIN ".BASE_DATOS.".tab_estseg_vehicu e ON a.cod_vehicu = e.num_placax 

                WHERE 1=1 ";

                

                $mSql .= $_REQUEST['transp']  ? " AND a.cod_emptra IN (".$_REQUEST['transp'].") " : "";
                $mSql .= $_REQUEST['cod_solici'] ? " AND a.cod_solici = '".$_REQUEST['cod_solici']."'" : "";
                $mSql .= $_REQUEST['fecini'] && $_REQUEST['fecfin'] ? " AND a.fec_creaci BETWEEN '".$_REQUEST['fecini']."' AND '".$_REQUEST['fecfin']."'" : "";

                $this->tableExport(1,$mSql);

                $list = new DinamicList($this->conexion, $mSql, "1", "no", 'ASC');
                $list->SetClose('no');
                $list->SetExcel('excel','onclick:exportExcel()');
                $list->SetHeader("No. Codigo Solic", "field:cod_solici;");
                $list->SetHeader("Tipo Solicitud", "field:nom_tipest;");
                $list->SetHeader("Nit Empresa", "field:cod_emptra;");
                $list->SetHeader("Nombre Empresa", "field:nom_tercer;");
                $list->SetHeader("No. Documento Conductor", "field:num_docume;");
                $list->SetHeader("Nombre Conductor", "field:nom_conduct;");
                $list->SetHeader("Placa", "field:num_placax;");
                $list->SetHeader("Estado", "field:nom_estseg;");
                $list->SetHeader("Observaciones", "field:obs_estseg;");
                $list->SetHeader("Usuario Creación", "field:usr_estseg;");
                $list->SetHeader("Fecha Solicitud", "field:fec_creaci;");
                $list->SetHeader("Usuario Modificación", "field:usr_modifi;");
                $list->SetHeader("Fecha Respuesta", "field:fec_finsol;");
                $list->Display($this->conexion);
    

                $_SESSION["DINAMIC_LIST"] = $list;
                return $Html = $list->GetHtml();
        }else{

            $this->tableExport(2,$mSqlP);
            
            $list = new DinamicList($this->conexion, $mSqlP, "1", "no", 'ASC');
            $list->SetClose('no');
            $list->SetExcel('excel','onclick:exportExcel()');
            $list->SetHeader("Número de Solicitud", "field:cod_solici;");
            $list->SetHeader("Fecha de Solicitud", "field:fec_creaci;");
            $list->SetHeader("Correo", "field:cor_solici;");
            $list->SetHeader("Telefono", "field:tel_solici;");
            $list->SetHeader("Celular", "field:cel_solici;");
            $list->SetHeader("Estudio", "field:nom_estcon;");
            $list->SetHeader("Tipo de Estudio", "field:nom_tipest;");
            $list->SetHeader("Resultado", "field:nom_estseg;");
            $list->SetHeader("Observación", "field:obs_estseg;");
            $list->SetHeader("Fecha de finalización", "field:fec_finsol;");
            $list->SetHeader("Fecha de vencimiento", "field:fec_venest;");
            $list->SetHeader("Código del Conductor", "field:cod_conduc;");
            $list->SetHeader("Tipo de documento", "field:tip_doccon;");
            $list->SetHeader("Primer Apellido", "field:nom_ape1con;");
            $list->SetHeader("Segundo Apellido", "field:nom_ape2con;");
            $list->SetHeader("Nombres", "field:nom_nomcon;");
            $list->SetHeader("Número de Licencia", "field:num_liccon;");
            $list->SetHeader("Categoria", "field:nom_catcon;");
            $list->SetHeader("Vencimiento de Licencia", "field:fec_vliccon;");
            $list->SetHeader("ARL", "field:nom_arlcon;");
            $list->SetHeader("EPS", "field:nom_epscon;");
            $list->SetHeader("Celular", "field:num_movcon;");
            $list->SetHeader("Telefono", "field:num_telcon;");
            $list->SetHeader("Ciudad", "field:nom_ciucon;");
            $list->SetHeader("Dirección", "field:dir_rescon;");
            $list->SetHeader("Correo", "field:dir_emacon;");
            $list->SetHeader("Presenta Comparendos?", "field:ind_precom;");
            $list->SetHeader("Valor", "field:val_compar;");
            $list->SetHeader("Presenta Resoluciones?", "field:ind_preres;");
            $list->SetHeader("Valor", "field:val_resolu;");
            $list->SetHeader("Placa", "field:num_placax;");
            $list->SetHeader("Remolque", "field:num_remolq;");
            $list->SetHeader("Marca", "field:nom_marcax;");
            $list->SetHeader("Linea", "field:nom_lineax;");
            $list->SetHeader("Modelo", "field:ano_modelo;");
            $list->SetHeader("Color", "field:nom_colorx;");
            $list->SetHeader("Carrocería", "field:cod_carroc;");
            $list->SetHeader("Chasis", "field:num_chasis;");
            $list->SetHeader("Número Motor", "field:num_motorx;");
            $list->SetHeader("SOAT", "field:num_soatxx;");
            $list->SetHeader("Vigencia SOAT", "field:fec_vigsoa;");
            $list->SetHeader("Licencia de transito", "field:num_lictra;");
            $list->SetHeader("Operador GPS", "field:nom_operad;");
            $list->SetHeader("Usuario", "field:usr_gpsxxx;");
            $list->SetHeader("Contraseña", "field:clv_gpsxxx;");
            $list->SetHeader("Observacion", "field:obs_opegps;");
            $list->SetHeader("Frecuencia", "field:fre_opegps;");
            $list->SetHeader("Presenta Comparendos?", "field:ind_vehcom;");
            $list->SetHeader("Valor", "field:val_comveh;");
            $list->SetHeader("Presenta Resoluciones?", "field:ind_preveh;");
            $list->SetHeader("Valor", "field:val_resveh;");
            $list->SetHeader("Código Propietario", "field:cod_propie;");
            $list->SetHeader("Tipo de Documento", "field:tip_docpro;");
            $list->SetHeader("Primer Apellido", "field:nom_ape1pro;");
            $list->SetHeader("Segundo Apellido", "field:nom_ape2pro;");
            $list->SetHeader("Nombres", "field:nom_nompro;");
            $list->SetHeader("Telefono", "field:num_movpro;");
            $list->SetHeader("Ciudad", "field:nom_ciupro;");
            $list->SetHeader("Direccion", "field:dir_respro;");
            $list->SetHeader("Correo", "field:dir_emapro;");
            $list->Display($this->conexion);
            $_SESSION["DINAMIC_LIST"] = $list;

            return $Html = $list->GetHtml();
        }


        
    }

    public function tableExport($tipo,$mSql){

        $consulta  = new Consulta($mSql, $this -> conexion);
        $data = $consulta -> ret_matriz();
        if($tipo==1){

        
            $export = '<table id="tablaRegistros" class="table table-striped table-bordered table-sm" style="width: 90vw;font-size:10px;">
                <thead>
                <tr>
                    <th>No. Codigo Solic</th>
                    <th>Tipo Solicitud</th>
                    <th>Nit Empresa</th>
                    <th>Nombre Empresa</th>
                    <th>No. Documento Conductor</th>
                    <th>Nombre Conductor</th>
                    <th>Placa</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                    <th>Usuario Creación</th>
                    <th>Fecha Solicitud</th>
                    <th>Usuario Modificación</th>
                    <th>Fecha Respuesta</th>
                </tr>
                </thead>
                <tbody>';
                foreach($data as $value){
                   $export .="<tr>";
                   $export .="<td>".$value['cod_solici']."</td>";
                   $export .="<td>".$value['nom_tipest']."</td>";
                   $export .="<td>".$value['cod_emptra']."</td>";
                   $export .="<td>".$value['nom_tercer']."</td>";
                   $export .="<td>".$value['num_docume']."</td>";
                   $export .="<td>".$value['nom_conduct']."</td>";
                   $export .="<td>".$value['num_placax']."</td>";
                   $export .="<td>".($value['ind_estseg'] =='A' ? 'Aprobado':($value['ind_estudi']=='R' ? 'Rechazado':($value['ind_estudi']=='P' ? 'Pendiente':'N/a')))."</td>";
                   $export .="<td>".$value['obs_estseg']."</td>";
                   $export .="<td>".$value['usr_estseg']."</td>";
                   $export .="<td>".$value['fec_creaci']."</td>";
                   $export .="<td>".$value['usr_modifi']."</td>";
                   $export .="<td>".$value['fec_finsol']."</td>";
                   $export .="</tr>";
                }
           $export .='</tbody>
            </table>';

            $_SESSION["HTML"] = $export;
        }else{
            
            $export ='
                <table id="tablaRegistros" class="table table-striped table-bordered table-sm" style="width: 100%;font-size:10px;">
                <thead>
                    <tr>
                        <th>Nï¿½mero de Solicitud</th>
                        <th>Fecha de Solicitud</th>
                        <th>Correo</th>
                        <th>Telefono</th>
                        <th>Celular</th>
                        <th>Estudio</th>
                        <th>Tipo de Estudio</th>
                        <th>Resultado</th>
                        <th>Observaciï¿½n</th>
                        <th>Fecha de finalizaciï¿½n</th>
                        <th>Fecha de vencimiento</th>
                        <th>Cï¿½digo del Conductor</th>
                        <th>Tipo de documento</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Nombres</th>
                        <th>Nï¿½mero de Licencia</th>
                        <th>Categoria</th>
                        <th>Vencimiento de Licencia</th>
                        <th>ARL</th>
                        <th>EPS</th>
                        <th>Celular</th>
                        <th>Telefono</th>
                        <th>Ciudad</th>
                        <th>Direcciï¿½n</th>
                        <th>Correo</th>
                        <th>ï¿½Presenta Comparendos?</th>
                        <th>Valor</th>
                        <th>ï¿½Presenta Resoluciones?</th>
                        <th>Valor</th>
                        <th>Placa</th>
                        <th>Remolque</th>
                        <th>Marca</th>
                        <th>Linea</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Carrocerï¿½a</th>
                        <th>Chasis</th>
                        <th>Nï¿½mero Motor</th>
                        <th>SOAT</th>
                        <th>Vigencia SOAT</th>
                        <th>Licencia de transito</th>
                        <th>Operador GPS</th>
                        <th>Usuario</th>
                        <th>Contraseï¿½a</th>
                        <th>Observacion</th>
                        <th>Frecuencia</th>
                        <th>ï¿½Presenta Comparendos?</th>
                        <th>Valor</th>
                        <th>ï¿½Presenta Resoluciones?</th>
                        <th>Valor</th>
                        <th>Cï¿½digo Propietario</th>
                        <th>Tipo de Documento</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Nombres</th>
                        <th>Telefono</th>
                        <th>Telefono</th>
                        <th>Ciudad</th>
                        <th>Direccion</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>';
                foreach($data as $registro){
				
                    $export .="<tr>";
            
                   $export .="<td>".$registro['cod_solici']."</td>";
                   $export .="<td>".$registro['fec_creaci']."</td>";
                   $export .="<td>".$registro['cor_solici']."</td>";
                   $export .="<td>".$registro['tel_solici']."</td>";
                   $export .="<td>".$registro['cel_solici']."</td>";
                   $export .="<td>".$registro['cod_estcon']."</td>";
                   $export .="<td>".$registro['cod_tipest']."</td>";
                   $export .="<td>".$registro['ind_estseg']."</td>";
                   $export .="<td>".$registro['obs_estseg']."</td>";
                   $export .="<td>".$registro['fec_finsol']."</td>";
                   $export .="<td>".$registro['fec_venest']."</td>";
                   $export .="<td>".$registro['cod_conduc']."</td>";
                   $export .="<td>Cï¿½dula de Ciudadanï¿½a</td>";
                   $export .="<td>".$registro['nom_ape1con']."</td>";
                   $export .="<td>".$registro['nom_ape2con']."</td>";
                   $export .="<td>".$registro['nom_nomcon']."</td>";
                   $export .="<td>".$registro['num_liccon']."</td>";
                   $export .="<td>".$registro['nom_catcon']."</td>";
                   $export .="<td>".$registro['fec_vliccon']."</td>";
                   $export .="<td>".$registro['nom_arlcon']."</td>";
                   $export .="<td>".$registro['nom_epscon']."</td>";
                   $export .="<td>".$registro['num_movcon']."</td>";
                   $export .="<td>".$registro['num_telcon']."</td>";
                   $export .="<td>".$registro['nom_ciucon']."</td>";
                   $export .="<td>".$registro['dir_rescon']."</td>";
                   $export .="<td>".$registro['dir_emacon']."</td>";
                   $export .="<td>".$registro['ind_precom']."</td>";
                   $export .="<td>".$registro['val_compar']."</td>";
                   $export .="<td>".$registro['ind_preres']."</td>";
                   $export .="<td>".$registro['val_resolu']."</td>";
                   $export .="<td>".$registro['num_placax']."</td>";
                   $export .="<td>".$registro['num_remolq']."</td>";
                   $export .="<td>".$registro['nom_marcax']."</td>";
                   $export .="<td>".$registro['nom_lineax']."</td>";
                   $export .="<td>".$registro['ano_modelo']."</td>";
                   $export .="<td>".$registro['nom_colorx']."</td>";
                   $export .="<td>".$registro['cod_carroc']."</td>";
                   $export .="<td>".$registro['num_chasis']."</td>";
                   $export .="<td>".$registro['num_motorx']."</td>";
                   $export .="<td>".$registro['num_soatxx']."</td>";
                   $export .="<td>".$registro['fec_vigsoa']."</td>";
                   $export .="<td>".$registro['num_lictra']."</td>";
                   $export .="<td>".$registro['nom_operad']."</td>";
                   $export .="<td>".$registro['usr_gpsxxx']."</td>";
                   $export .="<td>".$registro['clv_gpsxxx']."</td>";
                   $export .="<td>".$registro['obs_opegps']."</td>";
                   $export .="<td>".$registro['fre_opegps']."</td>";
                   $export .="<td>".$registro['ind_vehcom']."</td>";
                   $export .="<td>".$registro['val_comveh']."</td>";
                   $export .="<td>".$registro['ind_preveh']."</td>";
                   $export .="<td>".$registro['val_resveh']."</td>";
                   $export .="<td>".$registro['cod_propie']."</td>";
                   $export .="<td>Cï¿½dula de Ciudadanï¿½a</td>";
                   $export .="<td>".$registro['nom_ape1pro']."</td>";
                   $export .="<td>".$registro['nom_ape2pro']."</td>";
                   $export .="<td>".$registro['nom_nompro']."</td>";
                   $export .="<td>".$registro['num_movpro']."</td>";
                   $export .="<td>".$registro['num_telpro']."</td>";
                   $export .="<td>".$registro['nom_ciupro']."</td>";
                   $export .="<td>".$registro['dir_respro']."</td>";
                   $export .="<td>".$registro['dir_emapro']."</td>";
                   $export .="</tr>";
                }
            $export .='</tbody>
            </table>';

            $_SESSION["HTML"] = $export;
        }
        

    }

    private function listaSelect( $mTitulo, $mNomSel, $mMatriz, $mClass, $mObliga = 0){
        $mHtml = '<td align="right" class="celda_titulo '.$mClass.'">'.( $mObliga ? '*' : '' ).$mTitulo.' &nbsp;</td>';
  
        $mHtml .= '<td class="celda_info '.$mClass.'">'; 
        $mHtml .= '<select name="'.$mNomSel.'" id="'.$mNomSel.'ID" onKeypress=buscar_op(this)>';
  
        $n = sizeof($mMatriz);
        for($i = 0; $i < $n; $i++){
          $mHtml .= '<option value="'.$mMatriz[$i][0].'">'.$mMatriz[$i][1].'</option>';
        }
  
        $mHtml .= '</select>';
        $mHtml .= '</td>';
        return $mHtml;
    }

    public function getTransport(){

        $inicio[0][0]= "";
        $inicio[0][1]= "-";

        $query = "SELECT a.cod_tercer, CONCAT(LTRIM(a.abr_tercer),' - ',a.cod_tercer)
                    FROM ".BASE_DATOS.".tab_tercer_tercer a
                    INNER JOIN ".BASE_DATOS.".tab_tercer_activi b ON a.cod_tercer = b.cod_tercer AND b.cod_activi = '1'
                    INNER JOIN ".BASE_DATOS.".tab_transp_tipser c ON a.cod_tercer = c.cod_transp AND c.num_consec = (
                      SELECT MAX(xx.num_consec) AS num_consec
                        FROM ".BASE_DATOS.".tab_transp_tipser xx
                       WHERE xx.cod_transp = a.cod_tercer
                     )
                   WHERE c.ind_estado = 1
                ORDER BY 2 ";
        $consulta = new Consulta($query, $this -> conexion);
        $transp = $consulta -> ret_matriz(); 
       
       return $transpor = array_merge($inicio,$transp);

    }

    
}
$service = new InfEstudiSeguri($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>