<?php

/*! \file encript.class.php
 *  \brief Clase encriptar 
 *  \author Carlos A. Mock-kow M:
 *  \author carlos.mock@intrared.net
 *  \version 1.0
 *  \date    08-2008.
*/
class Encript
{
  /*! \class encriptar encript.class.php "lib/encript.class.php"
   *  \brief clase para manejo de encriptacion de la informacion del framework.
   */

  /*! \var private llave.
   *  \brief string variable donde se guarda la clave con la que se encripta y desencripta la nformacion.
  */
  private $llave = NULL;

  /*! \fn construct( $llave = NULL )
   *  \brief Funcion para inicalizar i construir los parametros basicos de la clase.
   *  \param $llave: string variable donde se guarda la clave con la que se encripta y desencripta la nformacion, valor por defecto NULL.
   *  \return no retorna.
   */
  public function __construct( $llave = NULL )
  {
    if( $llave != NULL )
    {
      $this -> llave = $llave;
    }
    else
    {
     $this -> llave = NULL;
    }
  }

  /*! \fn public getLLave()
   *  \brief Funcion para obtener el valor de la llave.
   *  \return el valor de la llave.
   */
  public function getLLave()
  {
  	return $this -> llave;
  }

  /*! \fn public setLLave()
   *  \brief Funcion para asignar el valor de la llave.
   */
  public function setLLave( $valor = NULL )
  {
  	if( $valor !== NULL )
    {
    	$this -> llave = $valor;
    }
  }

  /*! \fn public encPasswd( $entrada = NULL, $llave = NULL )
   *  \brief Funcion para encriptar la clave de usuario con un metodo sin reversion.
   *  \param $entrada : string valor de la clave de usuario, valor por defecto NULL.
   *  \param $llave : string llave con la que se va a encriptar(si no se asigna nada usa la del constructor), valor por defecto NULL.
   *  \return la clave encriptado, o false si genero error.
   */
  public function encPasswd( $entrada = NULL, $llave = NULL )
  {
    $var = $this -> encript( $entrada, $llave ); 
    if( $var )
    {
      return sha1( $var );
    }
    else
      return FALSE;
  }

  /*! \fn public encript( $entrada = NULL, $llave = NULL, $metodo = 'ecb' )
   *  \brief Funcion para encriptar la clave de usuario con un metodo sin reversion.
   *  \param $entrada : string palabra a encriptar, valor por defecto NULL.
   *  \param $llave   : string llave con la que se va a encriptar(si no se asigna nada usa la del constructor), valor por defecto NULL.
   *  \param $metodo  : string algoritmo a aplicar para encripcion, valor por defecto ecb.
   *  \return la palabra encriptado, o false si genero error.
   */
  public function encript( $entrada = NULL, $llave = NULL, $metodo = 'ecb' )
  {
    if( $llave !== NULL )
    {
    	$key = $llave;
    }
    elseif( $this -> llave !== NULL )
    {
    	$key = $this -> llave;
    }
    else
    {
    	return FALSE;
    } 

    $td = mcrypt_module_open( 'tripledes', '', $metodo, '' );
    $iv = mcrypt_create_iv ( mcrypt_enc_get_iv_size( $td ), MCRYPT_RAND );

    mcrypt_generic_init( $td, $key, $iv );

    if( mcrypt_generic_init( $td, $key, $iv ) != -1 )
    {
      mcrypt_generic_init( $td, $key, $iv );
      $datosCifrados = mcrypt_generic( $td, $entrada );
    }
    mcrypt_generic_deinit( $td );
    mcrypt_module_close( $td );

    return $datosCifrados;
  }

  /*! \fn public decript( $entrada = NULL, $llave = NULL, $metodo = 'ecb' )
   *  \brief Funcion para encriptar la clave de usuario con un metodo sin reversion.
   *  \param $entrada : string palabra a desencriptar, valor por defecto NULL.
   *  \param $llave   : string llave con la que se va a encriptar(si no se asigna nada usa la del constructor), valor por defecto NULL.
   *  \param $metodo  : string algoritmo a aplicar para encripcion, valor por defecto ecb.
   *  \return la palabra desencriptado, o false si genero error.
   */
  public function decript( $entrada = NULL, $llave = NULL, $metodo = 'ecb' )
  {
    if( $llave !== NULL )
    {
        $key = $llave;
    }
    elseif( $this -> llave !== NULL )
    {
        $key = $this -> llave;
    }
    else
    {
        return FALSE;
    }

    $td = mcrypt_module_open( 'tripledes', '', $metodo, '' );

    $iv = mcrypt_create_iv ( mcrypt_enc_get_iv_size( $td ), MCRYPT_RAND );

    if( mcrypt_generic_init( $td, $key, $iv ) != -1 )
    {
      mcrypt_generic_init( $td, $key, $iv );
      $datosDecifrados = mdecrypt_generic( $td, $entrada );
    }

    mcrypt_generic_deinit( $td );
    mcrypt_module_close( $td );

    return trim( $datosDecifrados );
  }
  
  public function __destruct()
  {
    unset( $this -> llave );
  }
}
?>