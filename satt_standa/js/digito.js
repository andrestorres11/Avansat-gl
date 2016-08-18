function genera_digito(nit, dig) 
{
        ceros = "000000";
        li_peso= new Array();
        li_peso[0] = 71;
        li_peso[1] = 67;
        li_peso[2] = 59;
        li_peso[3] = 53;
        li_peso[4] = 47;
        li_peso[5] = 43;
        li_peso[6] = 41;
        li_peso[7] = 37;
        li_peso[8] = 29;
        li_peso[9] = 23;
        li_peso[10] = 19;
        li_peso[11] = 17;
        li_peso[12] = 13;
        li_peso[13] = 7;
        li_peso[14] = 3;

        ls_str_nit = ceros + nit.value;
        li_suma = 0;
        for(i = 0; i < 15; i++)
	{
            	li_suma += ls_str_nit.substring(i,i+1) * li_peso[i];
        }
        digito_chequeo = li_suma%11;
        if (digito_chequeo >= 2)
                digito_chequeo = 11 - digito_chequeo;
        dig.value = digito_chequeo
}