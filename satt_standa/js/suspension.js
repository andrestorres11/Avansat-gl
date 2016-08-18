/**
 * @author victor.lombo
 * @Actualizado por:			MIGUEL ANGEL GARCIA RIVERA
 * @Fecha Actualización:	2012/10/16
 */
 
 function aceptar_ins1(){
   
   var size_suspend = document.getElementById('size_suspenID');
   var mssgx = new Array();
   var focus = new Array();
   var flagx = false;
   for(var i=0; i<size_suspend.value; i++){ 
     if(document.getElementById('ind_suspen'+ i +'ID').checked == true){
       flagx = true;
       
       if(document.getElementById('fec_suspen'+ i +'ID').value.replace(/^\s*|\s*$/g,"") == ''){
         mssgx.push('*- La Fecha de Suspensión de la Fila No. '+(i+1)+' es requerido ');
         focus.push(document.getElementById('fec_suspen'+ i +'ID'));
       }
       if(document.getElementById('hor_suspen'+ i +'ID').value.replace(/^\s*|\s*$/g,"") == ''){
         mssgx.push('*- La Hora  de Suspensión de la Fila No. '+(i+1)+' es requerido ');
         focus.push(document.getElementById('hor_suspen'+ i +'ID'));
       }
     }
   }
   if(flagx == true ){
     if(mssgx.length > 0){
       alert('Por Favor verificar los Siguientes Campos: \n\n'+mssgx.join('\n')); 
       focus[0].focus();
       return false;
     }
/*
		 else{ 
       if(confirm('Realmente desea guardar esta información...?') == true){
         document.getElementById('form_insID').submit();
       }
     }
*/
   }
	 //else return false;
	 
	if(confirm('Realmente desea guardar esta información...?') == true)
		document.getElementById('form_insID').submit();


 }
