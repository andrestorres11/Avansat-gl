var digitos=60 //cantidad de digitos buscados
var puntero=0
var buffer=new Array(digitos) //declaración del array Buffer
var cadena=""
var text2

function borrar_buffer()
{
   //inicializa la cadena buscada
    cadena='';
    puntero=0;
}
function buscar_op(obj){
   var letra = String.fromCharCode(event.keyCode)
   if(puntero >= digitos){
       cadena='';
       puntero=0;
    }
   //si se presiona la tecla ENTER, borro el array de teclas presionadas y salto a otro objeto...
   if (event.keyCode == 13)
	       borrar_buffer();
   //sino busco la cadena tipeada dentro del combo...
   else
    {
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       for (var opcombo=0;opcombo < obj.length;opcombo++)
           {
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase())
          {
          obj.selectedIndex=opcombo;
          }
       }
    }
   event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}