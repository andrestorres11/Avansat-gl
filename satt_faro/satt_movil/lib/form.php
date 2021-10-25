<?php

class Form
{
	function Form(  )
	{
		echo "<form action='index.php?servic=$_REQUEST[servic]' method='post' name='formulario' id='formulario'  >\n";
	}
	
	function Label( $params = "" )
	{
		$for = ( $params["for"]  ? " for='$params[for]' ": "" );
		$label = ( $params["label"]  ? "$params[label]": "" );		
		
		echo "<label$for>$label</label>\n";
	}
	
	function Text( $params = "" )
	{
		echo "<input type='text' name='$params[name]' maxlength='$params[maxlength]' id='$params[name]' value='$params[value]'  />\n";
	}
	
	function Pass( $params = "" )
	{
		echo "<input type='password' name='$params[name]' maxlength='$params[maxlength]' id='$params[name]' value='$params[value]'  />\n";
	}
	
	function Boton( $params = "" )
	{
		echo "<a data-theme='b' href='#' data-icon='$params[icon]' onclick='$params[onclick]'  data-role='button' >$params[label]</a>";
	}
	
	function Back()
	{
		echo "<a href='#' data-role='button' onclick='history.back()' >Volver</a>";
	}
	
	function Buscar( $params = "" )
	{
		echo "<input type='search' name='$params[name]' maxlength='$params[maxlength]' id='$params[name]' value='$params[value]'  />\n";
	}
	
	function Hidden( $params = "" )
	{
		echo "<input type='hidden' value='$params[value]' name='$params[name]' id='$params[name]' >\n";	
	}
	
	function closeDiv()
	{
		echo "</div>";
	}
	
	function closeForm()
	{
		echo "</form>\n";
	}
	
	function Head( $title )
	{
		echo "<li role='heading' data-role='list-divider' >$title</li>";
	}
	
	function Dato( $label, $dato )
	{
		echo "<li>";
		echo "<b>$label</b> <span style='font-weight:normal;' >$dato</span>\n";
		echo "</li>";
	}
}
?>