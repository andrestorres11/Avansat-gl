<?php

/*
class Foo
{
    private $var = 	'3.1415962654';
    private $var2 =	'9.81';
    private $var3 =	'Grupo OET es LO MEJOR';
    private $var4 =	'Grupo OET es PASION';
}

$a = array(1, 2, array("a", "b", "c"));
var_dump ($a);

echo "<br /><br />---------------------------------------------<br /><br />";

//  //gc_enable();
//	gc_disable();
//  echo "<br /><br />var dump gc_enable :: ";
//  //var_dump( gc_enable() );
//  var_dump( gc_disable() );
//  echo "<br />Luego de gc_enable:<br />";
//  echo sprintf( '%8d: ', $i ), memory_get_usage(), "\n";


var_dump(gc_enabled()); 
ini_set('zend.enable_gc', 1); 
var_dump(gc_enabled()); 


echo "<br /><br />---------------------------------------------<br /><br />";

$baseMemory = memory_get_usage();

for ( $i = 0; $i <= 30500; $i++ )
{
  $a = new Foo;
  $a->self = $a;
  if ( $i % 1 === 0 )
  {
  	echo "<br />";
    //echo sprintf( '%8d: ', $i ), memory_get_usage() - $baseMemory, "\n";
    echo sprintf( '%8d: ', $i ), memory_get_usage(), "\n";
  }
}

echo "<br /><br />---------------------------------------------<br /><br />";

gc_disable();
echo "<br /><br />var dump gc_disable :: ";
var_dump( gc_disable() );
echo "<br />Luego de gc_disable:<br />";
echo sprintf( '%8d: ', $i ), memory_get_usage(), "\n";
*/

// phpinfo();

/*
echo "hola";

echo "<br /><br />";
$a = NULL;
$a[0][0] = "hola00";
$a[0][1] = "hola01";
$a[1][0] = "hola10";
$a[1][1] = "hola11";
$a[1][2] = "hola12";
$a[2][0] = "hola20";
$a[2]["miguel"] = "hola2miguel";

echo "<pre>", print_r($a); echo "</pre>";
echo "<br /><br />";

echo "Sizeof :: " . sizeof($a) . "<br />";
for( $i=0; $i<sizeof($a); $i++ )
{
	for( $j=0; $j<sizeof($a[$i]); $j++ )
	{
		if( isset( $a[$i][$j] ) )
			echo "<br />".$a[$i][$j];
		else
			echo "<br />".$a[$i]["miguel"];
	}
}
echo "<br /><br />";
echo "--------------------------------------<br /><br />";
echo "Count :: " . count($a) . "<br />";
for( $i=0; $i<count($a); $i++ )
{
	for( $j=0; $j<count($a[$i]); $j++ )
	{
		if( isset( $a[$i][$j] ) )
			echo "<br />".$a[$i][$j];
		else
			echo "<br />".$a[$i]["miguel"];
	}
}



echo "<br /><br />";
echo "--------------------------------------<br /><br />";

class cliente{ 
   	var $nombre; 
   	var $numero; 
   	var $peliculas_alquiladas; 

   	function __construct($nombre,$numero){ 
      	 $this->nombre=$nombre; 
      	 $this->numero=$numero; 
      	 $this->peliculas_alquiladas=array(); 
   	} 

   	function __destruct(){ 
      	 echo "<br>destruido: " . $this->nombre; 
   	} 

   	function dame_numero(){ 
      	 return $this->numero; 
   	} 
} 

//instanciamos un par de objetos cliente 
$cliente1 = new cliente("Pepe", 1); 
$cliente2 = new cliente("Roberto", 564); 

//mostramos el numero de cada cliente creado 
echo "El identificador del cliente 1 es: " . $cliente1->dame_numero(); 
echo "<br />Colombia es Pasión";
echo "<br />El identificador del cliente 2 es: " . $cliente2->dame_numero(); 
*/


$value = "8300752199  1'?|°_;}{à{[â^}´`40909584650  20110106  1969800=)0  76001000  SPN615  C& 10137096    202000  2000  0 0   20110114      CONDUCTOR: LUIS ALFREDO RODRIGUEZCELULAR: 3175423884  N 900168948 20110114  G R   ";


      echo "<br /><br /><br /><br />";
      echo "<pre>"; print_r($value); echo "</pre><br /><br />";


      // ---------------------------------
      echo "wof---------------------------------------------<br />";
      echo $value . "<br /><br />";

      $salida[0] = '';
      $len = strlen($value);

      for( $i=0; $i < $len; $i++ )
      {
        if( ctype_alpha( $value[$i] ) || ctype_digit( $value[$i] ) || $value[$i]==" " || $value[$i]=="." || $value[$i]==":" || $value[$i]=="-" )
          $salida[0] .= $value[$i];
      }
      // ---------------------------------
      $variab = "";
      //$value = implode( $variab, $salida[0] );

      echo "f---------------------------------------------<br />";
      //echo $value;
      echo $salida[0];
      // ---------------------------------


      echo "<br /><br /><br /><br />";
      echo "<pre>"; print_r($salida[0]); echo "</pre>";

?>