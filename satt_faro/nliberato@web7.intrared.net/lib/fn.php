<?php

function JS( $name )
{
	$name = str_replace( ".js" , "" , $name );
	echo "<script src='js/$name.js'></script>\n";
}

?>