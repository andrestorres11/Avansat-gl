<?php
error_reporting("E_ERROR");

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include ("../lib/general/pdf_lib.inc");
include("../../".$GLOBALS['url']."/constantes.inc");

class Proc_Factur_Faro
{
 var $conexion;
 	 

 function Proc_Factur_Faro()
 {
  $this -> conexion = new Conexion("localhost", USUARIO, CLAVE, $GLOBALS["db"]);
  $query  = "SELECT a.num_despac AS 'DESPACHO',a.fec_salida AS 'FECHA DE SALIDA',  d.nom_ciudad AS 'CIUDAD ORIGEN',
				   e.nom_ciudad AS 'CIUDAD DESTINO',a.cod_manifi AS 'MANIFIESTO',c.abr_tercer AS EMPRESA,
				   a.fec_llegad AS 'FECHA DE LLEGADA' ,b.num_placax AS 'PLACA',f.abr_tercer AS 'CONDUCTOR',
				   f.cod_tercer AS 'CEDULA',f.num_telef1 AS 'TELEFONO',if(j.num_despac IS NOT NULL ,'SI','NO') AS 'FACTURADO' 	   
			FROM ".BASE_DATOS.".tab_despac_despac a, ".BASE_DATOS.".tab_despac_vehige b,".BASE_DATOS.".tab_tercer_tercer c,
				 ".BASE_DATOS.".tab_genera_ciudad d, ".BASE_DATOS.".tab_genera_ciudad e, ".BASE_DATOS.".tab_tercer_tercer f,
				 ".BASE_DATOS.".tab_genera_rutasx g LEFT JOIN ".BASE_DATOS.".tab_despac_noveda h ON h.num_despac = a.num_despac 
				 LEFT JOIN ".BASE_DATOS.".tab_despac_contro i ON a.num_despac = i.num_despac
				 LEFT JOIN ".CENTRAL.".tab_factur_factur j ON j.num_despac = a.num_despac AND j.cod_tercer = c.cod_tercer
			WHERE a.num_despac = b.num_despac AND
				  a.fec_salida IS NOT NULL AND
				  b.cod_transp = c.cod_tercer AND
				  b.cod_rutasx = g.cod_rutasx AND
				  g.cod_ciuori = d.cod_ciudad AND
				  g.cod_ciudes = e.cod_ciudad AND
				  f.cod_tercer = b.cod_conduc AND 
				  a.fec_salida >= '".$GLOBALS["fecini"]."' AND 
				  a.fec_salida <= '".$GLOBALS["fecfin"]."' AND 
				 (a.cod_conult ='9999' OR a.fec_llegad IS NOT NULL) AND
				  g.cod_ciuori != g.cod_ciudes
			GROUP BY 1";
	$consulta = new Consulta($query, $this -> conexion);
    $facturas = $consulta -> ret_matriz(); 
	$factur = array();
	$j=0;
	$tot=array();
	$x=0;
	for($i = 0; $i < sizeof($facturas); $i++)
	{
		$factur[$i][0]=$facturas[$i][0];
		$factur[$i][1]=$facturas[$i][1];
		$factur[$i][2]=$facturas[$i][2];
		$factur[$i][3]=$facturas[$i][3];
		$factur[$i][4]=$facturas[$i][4];
		$factur[$i][5]=$facturas[$i][5];
		$factur[$i][6]=$facturas[$i][6];
		$factur[$i][7]=$facturas[$i][7];
		$factur[$i][8]=$facturas[$i][8];
		$factur[$i][9]=$facturas[$i][9];
		$factur[$i][10]='SI';
		$factur[$i][11]=$facturas[$i][5];
		$factur[$i][12]=$facturas[$i][10];
		$factur[$i][13]=$facturas[$i][11];
		$j=$i;
		$x++;
	}
	$c=0;
	$tot[0][0]=$x;
	$tot[0][1]='FARO';
	$tot[0][2]=$x;
	$tot[0][3]=0;
	//consulta de todas las bases de datos parametrizadas para el servidor
	$query = "SELECT nom_basexx,nom_empres
			FROM ".CENTRAL.".tab_factur_faroxx 
			WHERE ind_estado ='1'";
			
	$consulta = new Consulta($query, $this -> conexion);
    $bases = $consulta -> ret_matriz();
	$query = "SHOW DATABASES";
	$consulta = new Consulta($query, $this -> conexion);
    $BD = $consulta -> ret_matriz(); 	
	for($i = 0; $i < sizeof($bases); $i++)
	{echo 2;
		$x=0;
		for($y = 0; $y < sizeof($BD); $y++)
		{
			if($bases[$i][0]==$BD[$y][0])
				$x=1;
		}
	
	
		if($x==1){
			$aux="";
			$aux1="";
			if(ereg("satb",$bases[$i][0])){
				$aux =$bases[$i][0].'.tab_config_parame k';
				$aux1= 'k.cod_tercer';
			}
			if(ereg("sate",$bases[$i][0])){
				$aux =$bases[$i][0].'.tab_emptra_config k';
				$aux1= 'k.cod_emptra';
			}
			//busqueda de los despachos por cada base de datos
			$query  = "SELECT a.num_despac AS 'DESPACHO',a.fec_salida AS 'FECHA DE SALIDA',  d.nom_ciudad AS 'CIUDAD ORIGEN',
					   e.nom_ciudad AS 'CIUDAD DESTINO',a.cod_manifi AS 'MANIFIESTO',c.abr_tercer AS EMPRESA,
					   a.fec_llegad AS 'FECHA DE LLEGADA' ,b.num_placax AS 'PLACA',f.abr_tercer AS 'CONDUCTOR',
					   f.cod_tercer AS 'CEDULA',if(a.ind_segfar='1','SI','NO') AS 'SEGUIMIENTO FARO',f.num_telef1 AS 'TELEFONO',
					   if(j.num_despac IS NOT NULL ,'SI','NO') AS 'FACTURADO'	   
				FROM ".$bases[$i][0].".tab_despac_despac a, ".$bases[$i][0].".tab_despac_vehige b,".$bases[$i][0].".tab_tercer_tercer c,
					 ".$bases[$i][0].".tab_genera_ciudad d,".$aux.", ".$bases[$i][0].".tab_genera_ciudad e, ".$bases[$i][0].".tab_tercer_tercer f,
					 ".$bases[$i][0].".tab_genera_rutasx g LEFT JOIN ".$bases[$i][0].".tab_despac_noveda h ON h.num_despac = a.num_despac 
					 LEFT JOIN ".$bases[$i][0].".tab_despac_contro i ON a.num_despac = i.num_despac
					 LEFT JOIN ".CENTRAL.".tab_factur_factur j ON j.num_despac = a.num_despac AND j.cod_tercer = ".$aux1."  
				WHERE a.num_despac = b.num_despac AND
					  a.fec_salida IS NOT NULL AND
					  b.cod_transp = c.cod_tercer AND
					  b.cod_rutasx = g.cod_rutasx AND
					  g.cod_ciuori = d.cod_ciudad AND
					  g.cod_ciudes = e.cod_ciudad AND
					  f.cod_tercer = b.cod_conduc AND 
					  a.fec_salida >= '".$GLOBALS["fecini"]."' AND 
					  a.fec_salida <= '".$GLOBALS["fecfin"]."' AND
					 (h.usr_creaci LIKE '%faro%' OR i.usr_creaci LIKE '%faro%' OR h.usr_creaci LIKE '%american-Admin%' OR i.usr_creaci LIKE '%american-Admin%')  AND 
					 (a.cod_conult ='9999' OR a.fec_llegad IS NOT NULL)  AND
					 g.cod_ciuori != g.cod_ciudes
				GROUP BY 1
				HAVING (COUNT(h.usr_creaci)+COUNT(i.usr_creaci))>=2";
			$consulta = new Consulta($query, $this -> conexion);
			$facturas = $consulta -> ret_matriz(); 
			$x=0;
			$si=0;
			$no=0;
			$c++;
			for($z = 0; $z < sizeof($facturas); $z++)
			{
				$j++;
				$factur[$j][0]=$facturas[$z][0];
				$factur[$j][1]=$facturas[$z][1];
				$factur[$j][2]=$facturas[$z][2];
				$factur[$j][3]=$facturas[$z][3];
				$factur[$j][4]=$facturas[$z][4];
				$factur[$j][5]=$facturas[$z][5];
				$factur[$j][6]=$facturas[$z][6];
				$factur[$j][7]=$facturas[$z][7];
				$factur[$j][8]=$facturas[$z][8];
				$factur[$j][9]=$facturas[$z][9];
				$factur[$j][10]=$facturas[$z][10];
				$factur[$j][11]=$bases[$i][1];
				$factur[$j][12]=$facturas[$z][11];
				$factur[$j][13]=$facturas[$z][12];
				$x++;
				if($factur[$j][10]=='SI')
					$si++;
				else
					$no++;
			}
			$tot[$c][0]=$x;
			$tot[$c][1]=$bases[$i][1];
			$tot[$c][2]=$si;
			$tot[$c][3]=$no;		
			
		}
		
	}
	
    $fechoract = date("Y-m-d h:i a");
    $archivo = "Facturacion_Faro_".$fechoract;
    $this -> expDetalleExcel($archivo,$factur,$tot);
 
 }

 

 function expDetalleExcel($archivo,$factur,$tot)
 {
  header('Content-Type: application/octetstream');
  header('Expires: 0');
  header('Content-Disposition: attachment; filename="'.$archivo.'.xls"');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');

  $formulario = new Formulario ("index.php","post",$archivo,"form_item", "","");

  $formulario -> nueva_tabla();
	$formulario -> linea("Despacho",0,"t2");
	$formulario -> linea("Manifiesto",0,"t2");
	$formulario -> linea("Ciudad Origen",0,"t2");
	$formulario -> linea("Ciudad Destino",0,"t2");
	$formulario -> linea("Placa",0,"t2");
	$formulario -> linea("Conductor",0,"t2");
	$formulario -> linea("Cedula",0,"t2");
	$formulario -> linea("Celular",0,"t2");
	$formulario -> linea("Seguimiento Faro",0,"t2");
	$formulario -> linea("Fecha de Salida",0,"t2");
	$formulario -> linea("Fecha de Llegada",0,"t2");
	$formulario -> linea("Empresa Transportadora",0,"t2");
	$formulario -> linea("Generador",0,"t2");
	$formulario -> linea("Facturado",1,"t2");
	for($i = 0; $i < sizeof($factur); $i++)
	{
		
		$formulario -> linea($factur[$i][0],0,"t1");
		$formulario -> linea($factur[$i][4],0,"t1");
		$formulario -> linea($factur[$i][2],0,"t1");
		$formulario -> linea($factur[$i][3],0,"t1");
		$formulario -> linea($factur[$i][7],0,"t1");
		$formulario -> linea($factur[$i][8],0,"t1");
		$formulario -> linea($factur[$i][9],0,"t1");
		$formulario -> linea($factur[$i][12],0,"t1");
		$formulario -> linea($factur[$i][10],0,"t1");
		$formulario -> linea($factur[$i][6],0,"t1");
		$formulario -> linea($factur[$i][1],0,"t1");
		$formulario -> linea($factur[$i][11],0,"t1");
		$formulario -> linea($factur[$i][5],0,"t1");
		$formulario -> linea($factur[$i][13],1,"t1");
	
	}
	
	

  $formulario -> cerrar();
 }

 }
$proceso = new Proc_Factur_Faro();
?>