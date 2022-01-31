/**
 * @author victor.lombo
 */

 function consulope(formulario){
   if(parseInt(formulario.transp.value)==0){
     alert('Por Favor Seleccione una Transportadora');
     return false;
   }
   document.getElementById('opcionID').value = 1;
   formulario.submit();
 }
 function borrar_buffer(){
   return false;
 }
 function openForm(){
   try{
     document.getElementById('formDispositivosID').style.visibility='visible';
     document.getElementById('addOpeID').style.visibility='hidden';
   }catch(e){
      alert( e.message + '\n' + e.stack );
   }
 }
 function closeForm(){
   try{
     document.getElementById('formDispositivosID').style.visibility='hidden';
     document.getElementById('addOpeID').style.visibility='visible';
   }catch(e){
      alert( e.message + '\n' + e.stack );
   } 
 } 
 
 
 function trim (str) { return str.replace(/^\s+|\s+$/, ''); };
 
 function addOpe(){
   try{
      var mess = new Array();
      var atributes = new Array();
      var numdis = document.getElementById('numdisID');
      var operardor = document.getElementById('operadorID');
      if(trim(numdis.value)==''){
        mess.push("Completar la Informacion del Campo Dispositivo");
      }
      if(trim(operardor.value)=='' || parseInt(trim(operardor.value))==0){
        mess.push("Completar la Informacion del Campo Opearador");
      }
      if(mess.length>0){
        alert(mess.join('\n'));
        return false;
      }
      atributes.push(numdis.name+'='+numdis.value);
      atributes.push(operardor.name+'='+operardor.value);
      atributes.push('transp='+document.getElementById('htranspID').value);
      atributes.push('opcion=addDisp');
      AjaxGetData("../" + document.getElementById('dir_aplica_centralID').value + "/menava/ins_emailx_alarma.php/" + "?", atributes.join('&'), 'formDispositivosID', "post");
      setTimeout(function(){
        document.form_item.submit();
      },1500);
      
   }catch(e){
      alert( e.message + '\n' + e.stack );
   }
 }
 function setOperador(){
   if(parseInt(document.getElementById("operadorID").value)==999){
     document.getElementById("dnsIDDIV").innerHTML = "<table width='100%' cellspacing='0' cellpadding='4' class='formulario'>"+document.getElementById('formOperadoresID').innerHTML+"</table>";
     document.getElementById("nombreID").focus();
   }else return false;
 }
 function Ope1(option){
   if(option == 'add'){
     var nombre = document.getElementById('nombreID');
     var dns    = document.getElementById('dnsID');
     var email  = document.getElementById('indamiID');
     var mess = new Array();
     if(trim(nombre.value)==''){
        mess.push("Completar la Informacion del Campo Nombre");
     }
     if(trim(dns.value)==''){
        mess.push("Completar la Informacion del Campo DNS");
     }
     if(mess.length>0){
        alert(mess.join('\n'));
        return false;
     }
     var atributes = new Array();
     atributes.push(nombre.name+'='+nombre.value);
     atributes.push(dns.name+'='+dns.value);
     atributes.push(email.name+'='+email.value);
     atributes.push('transp='+document.getElementById('htranspID').value);
     atributes.push('opcion=addOpe');
     AjaxGetData("../" + document.getElementById('dir_aplica_centralID').value + "/menava/ins_emailx_alarma.php/" + "?", atributes.join('&'), 'dnsIDDIV', "post");
   }else{
     var atributes = new Array();
     atributes.push('transp='+document.getElementById('htranspID').value);
     atributes.push('opcion=getLista');
     AjaxGetData("../" + document.getElementById('dir_aplica_centralID').value + "/menava/ins_emailx_alarma.php/" + "?", atributes.join('&'), 'dnsIDDIV', "post");
   }
   
 }
 function borrarFila(obj){
   if(confirm("Esta Realmente seguro de Eliminar este Dispositivo...?")==true){
       var tr = obj.parentNode.parentNode;
       var table = obj.parentNode.parentNode.parentNode;
       table.removeChild(tr);
   }else return false;
 }
 function activate(obj){
   var datos = obj.value;
   if(obj.checked==true){
     obj.value = datos.split('|',2).join('|')+"|1";
   }else{
     obj.value = datos.split('|',2).join('|')+"|0";
   }
 }

 