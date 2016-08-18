/**
 * @author Jorge.Preciado
 */
function MostrarResul()
{
    try 
    {
        var fec_ini = document.getElementById('fec_inicialID');
        var fec_final = document.getElementById('fec_finalID');
        var busq_transp = document.getElementById('busq_transpID');
        
        if (fec_ini.value == '') 
        {
            alert('La Fecha Inicial es Obligatoria');
            return fec_ini.focus();
        }
        if (fec_final.value == '') 
        {
            alert('La Fecha Final es Obligatoria');
            return fec_final.focus();
        }
        /*if (busq_transp.value == '') 
        {
            alert('La Transportadora es Obligatoria');
            return busq_transp.focus();
        }*/
        document.getElementById('opcionID').value = 1;
        document.getElementById('formularioID').submit();
    }
    catch (e)
    {
        alert( "Error MostrarResul " + e.message);
    }
}

function exportarXls(  )
{
    var dir_aplica = document.getElementById( 'dir_aplicaID' );
    top.window.open("../"+dir_aplica.value+"/inform/inf_conduc_huella.php?opcion=2");
}