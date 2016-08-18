function Validar_F(Cadena)

{

        var Fecha= new String(Cadena)        // Crea un string

        var RealFecha= new Date()        // Para sacar la fecha de hoy

        // Cadena Año

        var Ano= new String(Fecha.substring(Fecha.lastIndexOf("-")+1,Fecha.length))

        // Cadena Mes

        var Mes= new String(Fecha.substring(Fecha.indexOf("-")+1,Fecha.lastIndexOf("-")))

        // Cadena Día

        var Dia= new String(Fecha.substring(0,Fecha.indexOf("-")))



        // Valido el tamaño del campo

        if (Fecha.length != 10){

                alert('Formato Inválido dd-mm-YYYY')

                return false

        }

        // Valido el año

        if (isNaN(Ano) || Ano.length<4 || parseFloat(Ano)<1900){

                alert('Año inválido')

                return false

        }

        // Valido el Mes

        if (isNaN(Mes) || parseFloat(Mes)<1 || parseFloat(Mes)>12){

                alert('Mes inválido')

                return false

        }

        // Valido el Dia

        if (isNaN(Dia) || parseFloat(Dia)<1 || parseFloat(Dia)>31){

                alert('Día inválido2')

                return false

        }

        if (Mes==4 || Mes==6 || Mes==9 || Mes==11 || Mes==2) {

                if (Mes==2 && Dia > 28 || Dia>30) {

                        alert('Día inválido2')

                        return false

                }

        }

}

function Validar_H(Cadena)

{

        var Hora= new String(Cadena)        // Crea un string

        var RealFecha= new Date()        // Para sacar la fecha de hoy

        // Cadena Hora

        var M= new String(Hora.substring(Hora.indexOf(":")+1,Hora.length))

        // Cadena Minutos

        var H= new String(Hora.substring(0,Hora.indexOf(":")))



        // Valido el tamaño del campo

        if (Hora.length != 5){

                alert('Formato Inválido HH:mm')

                return false

        }

        // Valido la Hora

        if (isNaN(H) || parseFloat(H)<0 || parseFloat(H)>24){

                alert('Hora Inválida')

                return false

        }

        // Valido los minutos

        if (isNaN(M) || parseFloat(M)<0 || parseFloat(M)>59){

                alert('Minutos Inválidos')

                return false

        }

}







function compar_fec(fec_ini, fec_fin)

{

      var dia_ini= new String(fec_ini.substring(fec_ini.lastIndexOf("-")+1,fec_ini.length))

      var mes_ini= new String(fec_ini.substring(fec_ini.indexOf("-")+1,fec_ini.lastIndexOf("-")))

      var ano_ini= new String(fec_ini.substring(0,fec_ini.indexOf("-")))



      var dia_fin= new String(fec_fin.substring(fec_fin.lastIndexOf("-")+1,fec_fin.length))

      var mes_fin= new String(fec_fin.substring(fec_fin.indexOf("-")+1,fec_fin.lastIndexOf("-")))

      var ano_fin= new String(fec_fin.substring(0,fec_fin.indexOf("-")))


       if(parseInt(ano_fin) < parseInt(ano_ini))
        return false
       else if (parseInt(ano_fin) == parseInt(ano_ini))
       {
        if(parseInt(mes_fin) < parseInt(mes_ini))
         return false
        else if (parseInt(mes_fin) == parseInt(mes_ini))
        {
         if (parseInt(dia_fin) < parseInt(dia_ini))
          return false
         else
          return true
		}
		else
		 return true
       }
       else
        return true
}





function cfec_actual(fec_form)

{

    fecha = new Date()

    var mes = new Array("01","02","03","04","05","06","07","08","09","10","11","12");

    var dia = new Array("0","01","02","03","04","05","06","07",

                        "08","09","10","11","12","13","14","15",

                        "16","17","18","19","20","21","22","23",

                        "24","25","26","27","28","29","30","31");

    fecha_a   = dia[fecha.getDate()]+"-"+mes[fecha.getMonth()]+"-"+fecha.getYear()

    dia_a  = dia[fecha.getDate()]

    mes_a  = mes[fecha.getMonth()]

    anno_a = fecha.getYear()





      var dia_form= new String(fec_form.substring(fec_form.lastIndexOf("-")+1,fec_form.length))

      var mes_form= new String(fec_form.substring(fec_form.indexOf("-")+1,fec_form.lastIndexOf("-")))

      var ano_form= new String(fec_form.substring(0,fec_form.indexOf("-")))



       if(parseInt(ano_form) < parseInt(anno_a))

       {

        return false

       }

       else if (parseInt(ano_form) == parseInt(anno_a))

            {

                if(parseInt(mes_form) < parseInt(mes_a)){

                 return false

                }

                else if (parseInt(mes_form) == parseInt(mes_a)){

                     if (parseInt(dia_form) < parseInt(dia_a)){

                     return false

                            }

                     else {

                        return true

                         }

                     }

                     else

                     {

                        return true

                     }

            }

            else

            {

                 return true

            }

}





//VALIDA EL FORMATO DE LA FECHA INCLUYENDO DIAS VICIESTOS

function val_fectexto(fecha)

{



    borrar = true

    if ((fecha.substr(4,1) == "-") && (fecha.substr(7,1) == "-"))

    {

        for (i=0; i<10; i++)

        {

             if ((i != 4)&&(i != 7)&&((fecha.substr(i,1)<"0")||(fecha.substr(i,1)>"9")))

             {

               borrar = false

               break

             }

         }

         if (borrar)

         {

            a = fecha.substr(0,4);

            m = fecha.substr(5,2);

            d = fecha.substr(8,2);

            if((a < 1995) || (a > 2050) || (m < 1) || (m > 12) || (d < 1) || (d > 31))

               borrar = false

            else

            {

               if((a%4 != 0) && (m == 2) && (d > 28))

                  borrar = false // Año no viciesto y es febrero y el dia es mayor a 28

               else

               {

                  if ((((m == 4) || (m == 6) || (m == 9) || (m==11)) && (d>30)) || ((m==2) && (d>29)))

                      borrar = false

               }  // else

            } // fin else

         } // if (error)

      } // if ((fecha.substr(2,1) == "/") && (fecha.substr(5,1) == "/"))

      else

         borrar = false

       return (borrar)

} // FUNCION
function fechas(este, mensaje)

{

    fecha = este.value

    formulario = document.form

    borrar = true

    if ((fecha.substr(4,1) == "-") && (fecha.substr(7,1) == "-"))

    {

        for (i=0; i<10; i++)

        {

             if ((i != 4)&&(i != 7)&&((fecha.substr(i,1)<"0")||(fecha.substr(i,1)>"9")))

             {

               borrar = false

               break

             }

         }

         if (borrar)

         {

            a = fecha.substr(0,4);

            m = fecha.substr(5,2);

            d = fecha.substr(8,2);

            if((a < 1995) || (a > 2050) || (m < 1) || (m > 12) || (d < 1) || (d > 31))

               borrar = false

            else

            {

               if((a%4 != 0) && (m == 2) && (d > 28))

                  borrar = false // Año no viciesto y es febrero y el dia es mayor a 28

               else

               {

                  if ((((m == 4) || (m == 6) || (m == 9) || (m==11)) && (d>30)) || ((m==2) && (d>29)))

                      borrar = false

               }  // else

            } // fin else

         } // if (error)

      } // if ((fecha.substr(4,1) == "-") && (fecha.substr(7,1) == "-"))

      else

         borrar = false

      if(!borrar)

      {

         window.alert(mensaje + " Fecha")

         este.focus()

      }

} // FUNCION