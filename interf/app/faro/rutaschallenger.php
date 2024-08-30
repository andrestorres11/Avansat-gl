<?php
ini_set('display_errors', false);
error_reporting(E_ALL & ~E_NOTICE);

  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.  
  include_once( "/var/www/html/ap/interf/app/faro/protoc.class.inc" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/tracki.class.php");


try 
{
		$fNomAplica = 'satt_challe';
		$fNomUsuari = 'admin-cron';


        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "rutasChalle" );



        

		if(!include('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
		{
			throw new Exception( "Aplicacion ".$fNomAplica." en sat trafico no encontrada ".'/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc', "1999" );
			break;
        }


        $fReturn = NULL;
        $fSalidAut = TRUE;
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        $fQueryRutas = 'SELECT  cod_rutasx, nom_rutasx, cod_ciuori, cod_ciudes FROM '.BASE_DATOS.'.tab_genera_rutasx 
        				 WHERE ind_estado = "1" -- AND cod_ciuori IN (11001000) AND cod_ciudes IN (5001000, 66001000) ';
		$fConsult -> ExecuteCons( $fQueryRutas );
        if( 0 != $fConsult -> RetNumRows() )
        {
			$fRutas = $fConsult -> RetMatrix( "a" );
        }else{
        	throw new Exception( "Error en SELECT RUTASX.".$fQueryRutas, "3001" ); 
        }

        $fConsult -> StartTrans();

        // Se busca pestos de control, segun la ciudad de origen y destino
        foreach ($fRutas as $mIndex => $fRutaData) 
        {
        	// consulta PC de origen
        	$fQueryPC = '
						(SELECT 
								a.cod_rutasx, a.nom_rutasx,
								a.cod_contro, a.nom_contro, a.tiem_duraci 

							FROM 
							(
								SELECT a.cod_contro, a.nom_contro , '.$fRutaData['cod_rutasx'].' AS cod_rutasx, "'.$fRutaData['nom_rutasx'].'" AS nom_rutasx, "15" AS tiem_duraci
								  FROM '.BASE_DATOS.'.tab_genera_contro a
								 WHERE a.ind_estado = "1"  
								   AND a.cod_contro = "10000"
								   LIMIT 1
							) a
					    )
						UNION 
						(SELECT 
 								b.cod_rutasx, b.nom_rutasx,
 								b.cod_contro, b.nom_contro, b.tiem_duraci 
 						    FROM
 							(
 								SELECT a.cod_contro, a.nom_contro , '.$fRutaData['cod_rutasx'].' AS cod_rutasx, "'.$fRutaData['nom_rutasx'].'" AS nom_rutasx, "540" AS tiem_duraci
 								  FROM '.BASE_DATOS.'.tab_genera_contro a
 								 WHERE a.ind_estado = "1"  
 								   AND a.cod_contro = "9999"
 								   LIMIT 1
 							) b  
 						) ';

			//echo "<pre>"; print_r($fQueryPC); echo "</pre>";  die();
			$fConsult -> ExecuteCons( $fQueryPC );
			$dPC = $fConsult -> RetMatrix( "a" );
			//echo "<pre>"; print_r($dPC); echo "</pre>";  



			foreach ($dPC as $mCount => $mDataPC) 
			{
				// inserta Puestos de control a las rutas
				$mPCRuta = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon 
							(cod_rutasx, cod_contro, val_duraci, val_distan, ind_estado, usr_creaci, fec_creaci) 
							VALUES
				            ('".$fRutaData['cod_rutasx']."' , '".$mDataPC['cod_contro']."', '".$mDataPC['tiem_duraci']."', '1' , '1', 'cron', NOW())  ";

				if( $fConsult -> ExecuteCons( $mPCRuta, "R" ) === FALSE )
            		throw new Exception( "Error tab_genera_rutcon.", "3001" );  

				// asigna la ruta a una transportadora
				$mRutaTransp = "INSERT INTO ".BASE_DATOS.".tab_genera_ruttra 
								(cod_rutasx, cod_contro, cod_transp, usr_creaci, fec_creaci )
								VALUES
								('".$fRutaData['cod_rutasx']."' , '".$mDataPC['cod_contro']."', '900714469', 'cron', NOW() )   "; 
				if( $fConsult -> ExecuteCons( $mRutaTransp, "R" ) === FALSE )
            		throw new Exception( "Error tab_genera_ruttra.", "3001" ); 
			}
			 

        echo "<hr>";
        }



		$fConsult -> Commit();


} 
catch (Exception $e) 
{
	echo "<pre>"; print_r($e); echo "</pre>";  
}
 

?>