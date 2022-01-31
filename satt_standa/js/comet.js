/**
 * @author jovo
 * @author chris
 */

function Hide()
{
	var menuFrameSet = parent.document.getElementById("framesetID");
	
	if ( menuFrameSet.cols == '0,*' )
		menuFrameSet.cols = '190,*';
	else
		menuFrameSet.cols = '0,*';
}

function LoadComet()
{
    Comet();
    RefreshComet();
}


function LoadTurnos()
{
    Comet1();
    RefreshComet1();
}


function Comet1()
{
    try 
	{
		var petro = document.getElementById("petroID").value;
		var oplix = document.getElementById("oplixID").value;
		var sachu = document.getElementById("sachuID").value;
		var central = document.getElementById("centralID").value;
		var central2 = document.getElementById("central2ID").value;
		var estilo = document.getElementById("estiloID").value;
    var tipser = document.getElementById("tipserID").value;
		var us = document.getElementById("usID").value;
    var atributes = "opcion=Turnos";
			atributes+= '&petro=' + petro;
			atributes+= '&oplix=' + oplix;
			atributes+= '&sachu=' + sachu;
			atributes+= '&central=' + central;
			atributes+= '&estilo=' + estilo;
      atributes+= '&tipser=' + tipser;
      atributes+= '&us=' + us;
        AjaxGetData('../'+ central2 +'/inform/inf_despac_transi.php?', atributes, 'informeID', 'post', 'CalcularTotales1()');
    } 
    catch (e) 
    {
        alert("Error " + e.message);
    }
}

function RefreshComet1()
{
    try 
    {
        setInterval( "Comet1()" , 120000 );
    } 
    catch (e) 
    {
        alert("Error Load " + e.message);
    }
}


function Tipser(aux)
{
    try 
    {
      if (aux = 1) {
        document.getElementById("tipserID").value = document.getElementById("tipID").value;
        document.getElementById("usID").value = document.getElementById("us1ID").value;
        setTimeout(function(){Comet1();},200)
          
      }
    } 
    catch (e) 
    {
        alert("Error Load " + e.message);
    }
}



function Comet()
{
    try 
	{
		var petro = document.getElementById("petroID").value;
		var oplix = document.getElementById("oplixID").value;
		var sachu = document.getElementById("sachuID").value;
		var central = document.getElementById("centralID").value;
		var central2 = document.getElementById("central2ID").value;
		var estilo = document.getElementById("estiloID").value;
		
		var atributes = "opcion=informe";
			atributes+= '&petro=' + petro;
			atributes+= '&oplix=' + oplix;
			atributes+= '&sachu=' + sachu;
			atributes+= '&central=' + central;
			atributes+= '&estilo=' + estilo;
        AjaxGetData('../'+ central2 +'/inform/inf_despac_transi.php?', atributes, 'informeID', 'post', 'CalcularTotales()');
    } 
    catch (e) 
    {
        alert("Error " + e.message);
    }
}

function RefreshComet()
{
    try 
    {
        setInterval( "Comet()" , 120000 );
    } 
    catch (e) 
    {
        alert("Error Load " + e.message);
    }
}


function CalcularTotales()
{
  $('#formdespacID table:eq(2) tr:eq(0) td :gt(4)').each(function(index, thisTotal){
    var i = index + 6;
    var sumCol = 0;
    $('#formdespacID table:eq(2) tr > td:nth-child(' + i + ') :gt(1) a').each(function(index, domEle){
      sumCol = sumCol + Number($(domEle).html());
    });
    $(thisTotal).html('<b>' + sumCol + '</b>')
  });
}



function CalcularTotales1()
{
  $('#formdespacID table:eq(3) tr:eq(0) td :gt(4)').each(function(index, thisTotal){
    var i = index + 6;
    var sumCol = 0;
    $('#formdespacID table:eq(3) tr > td:nth-child(' + i + ') :gt(1) a').each(function(index, domEle){
      sumCol = sumCol + Number($(domEle).html());
    });
    $(thisTotal).html('<b>' + sumCol + '</b>')
  });
}
