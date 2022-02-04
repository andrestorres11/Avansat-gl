<?php

class AES_functions
{
  function Encrypt( $M, $k )
  { 	
    for( $i = 0; $i < strlen( $M ); $i++ )
      $C .= chr( ( ord( $M[$i] ) + $k ) % 255 );
    return $C;
  }
  
  function Decrypt( $C, $k )
  {
    for( $i = 0; $i < strlen( $C ); $i++ )
      $M .= chr( ( ord( $C[$i] ) - $k + 255 ) % 255 );
    return $M;
  }
}

?> 