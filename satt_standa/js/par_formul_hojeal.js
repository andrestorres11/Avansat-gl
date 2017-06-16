/*! \file: ins_formul_formul
 *  \brief: JS para todas las acciones del modulo Hoja de Vida EAL 
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 28/09/2016
 *  \bug: 
 *  \warning: 
 */
 //variable global utilizada para validar los formularios
var validacion = true;
$(document).ready(function(){
    mostrar();
    /*//Autocompletables
    var Standa = $("#standaID").val();
    var attributes = '&Ajax=on&standa=' + Standa;
    var boton = "";
    $("#nom_transpID").autocomplete({
        source: "../" + Standa + "/transp/ajax_transp_transp.php?Option=buscarTransportadora" + attributes,
        minLength: 3,
        select: function(event, ui) {
            boton = "<input type='button' id='nuevo' value='Listado' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='mostrar();'>";
            $("#cod_tercerID").val(ui.item.id);
            $("#boton").empty();
            $("#boton").append(boton);
            $("body").removeAttr("class");
        }
    });*/
      
});
/*! \fn: registrarHojaDeVidaEAL
 *  \brief: Redirecciona al formulario para registrar una nueva hoja de vida
 *  \author: Ing. Fabian Salinas
 *  \date: 29/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function registrarHojaDeVidaEAL() {
    try 
    {
        $("#OptionID").val("formulario");
        $("#form_HojaVidaEalID").submit();
    } catch (e) {
        console.log("Error Function registrarHojaDeVidaEAL: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: cargarTablaEAL
 *  \brief: Carga el contenido del DIV de los datos basicos de la EAL
 *  \author: Ing. Fabian Salinas
 *  \date: 29/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object  objeto que activa la funcion
 *  \return: 
 */
function cargarTablaEAL(obj) {
    try {
        var cod_contro = $(obj).val();
        var div = $("#DivDatosEALID");

        if (cod_contro == '') {
            div.html("");
            return false;
        }

        var standa = $("#standaID").val();

        $.ajax({
            url: "../" + standa + "/formul/par_formul_hojeal.php",
            type: "POST",
            data: "Ajax=on&Option=tablaDatosBasicosEAL&cod_contro=" + cod_contro,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                div.html(datos);
                $("#secDivDatosEALID").css("height", "auto");
                cargarMapaEAL();
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Function cargarTablaEAL: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: cargarMapaEAL
 *  \brief: Carga el Mapa de la EAL
 *  \author: Ing. Fabian Salinas
 *  \date: 30/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function cargarMapaEAL() {
    try {
        var standa = $("#standaID").val();
        var div = $("#Sec2infBasicaEal");
        var features = new Array();

        div.css({
            height: "370px",
            width: "400px"
        });

        var pclon = parseFloat(div.attr('pclon'));
        var pclat = parseFloat(div.attr('pclat'));
        var pcIcono = '../' + standa + '/imagenes/point.png';
        var newCoord = ol.proj.transform([pclon, pclat], 'EPSG:4326', 'EPSG:3857');

        features[0] = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([pclon, pclat])),
            name: "<label style='color:#000000'>HOla</label>"
        });

        $.each(features, function(i, obj) {
            obj.setStyle(new ol.style.Style({
                image: new ol.style.Icon({
                    src: pcIcono
                })
            }));
        });

        var overlay = new ol.Overlay( /** @type {olx.OverlayOptions} */ ({
            element: $("#pintar"),
            autoPan: true,
            autoPanAnimation: {
                duration: 250
            }
        }));

        var vectorSource = new ol.source.Vector({
            features: features
        });

        var vectorLayer = new ol.layer.Vector({
            source: vectorSource
        });

        var map = new ol.Map({
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                }),
                vectorLayer
            ],
            projection: 'EPSG:26915',
            target: div,
            overlays: [overlay],
            view: new ol.View({
                center: newCoord,
                zoom: 7
            })
        });

    } catch (e) {
        console.log("Error Function cargarMapaEAL: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: mostrar
 *  \brief: Carga el Mapa de la EAL
 *  \author: Ing. Fabian Salinas
 *  \date: 30/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function mostrar() {
	try
	{
	    $("#form3").empty();
	    var standa = $("#standaID").val();
	    var parametros = "opcion=listaHojasVidaEAL&Ajax=on";
	    $.ajax({
	        url: "../" + standa + "/formul/par_formul_hojeal.php",
	        type: "POST",
	        data: parametros,
	        async: false,

	        success: function(data) {
	            $("#form3").append(data); // pinta los datos de la consulta      
	        },
	        complete: function(){
	            $("#sec2").css("height", "auto");
	        }
	    });
	    $("#datos").fadeIn(3000); // visualza los datos despues de pintarlos
    } catch (e) {
        console.log("Error Function mostrar: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: newHojaEAL
 *  \brief: Carga el Mapa de la EAL
 *  \author: Ing. Fabian Salinas
 *  \date: 30/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function newHojaEAL(accion, row = null) {
    try
    {
    	mData = "";
    	if(row != null)
    	{
    		var objeto = $(row).parent().parent();
    		var cod_contro = $(objeto).find("input[id^=cod_contro]").val();
    		mData = "&cod_contro=" + cod_contro; 
    	}
	    var standa = $("#standaID").val();
	    var transp = $("#cod_tercerID").val();
	    $("#popID").remove();
	    closePopUp('popID');
	    LoadPopupJQNoButton('open', 'Hoja de vida EAL ', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
	    var popup = $("#popID");
	    
	    var parametros = "opcion=FormNuevaHvEAL&Ajax=on&cod_transp=" + transp + "&accion=" + accion + mData;
	    $.ajax({
	        url: "../" + standa + "/formul/par_formul_hojeal.php",
	        type: "POST",
	        data: parametros,
	        async: false,

	        success: function(data) {
	            popup.html(data); // pinta los datos de la consulta      
	        }
	    });
	} catch (e) {
        console.log("Error Function newHojaEAL: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: cerrarAlert
 *  \brief: cierra los alert de los campos que no cumplen con las condiciones
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function cerrarAlert()
{
    try
    {
    	$(".error").fadeOut();
    } catch (e) {
        console.log("Error Function cerrarAlert: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: ValidarImg
 *  \brief: Carga el Mapa de la EAL
 *  \author: Ing. Fabian Salinas
 *  \date: 30/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function ValidarImg(obj) 
{
	try
	{
	    //Obtengo el objecto file
	    var file = $(obj).val();
	    var validate = true;
	    //Realizo la validacion de la extencion de la imagen
	    switch(file.substring(file.lastIndexOf('.') + 1).toLowerCase())
	    {
	        case 'jpg' : case 'jpeg': case 'bmp' : case 'tiff' : case 'png' :
	            validate = true;
	            var filesize = obj.files[0];
	            //Obtengo el tamano de la imagen
	            if(filesize.size>="2000000")
	            {
	                $(obj).focus().after('<span class="error" onclick="cerrarAlert()">tamaño del archivo no permitido</span>');
	                validate = false;
	            }
	        break;
	        default:
	            $(obj).focus().after('<span class="error" onclick="cerrarAlert()">Formato no permitido</span>');
	            validate = false;
	        break;
	    }

	    if(validate == true)
	    {
	        cerrarAlert();
	        //Recorro la imagen para pintarla en el div spaceImage
	        reader = new FileReader();
	        reader.onload = (e) => {
	            $(obj).parents().eq(5).find("DIV").each(function(){
	                if($(this).hasClass("spaceImage"))
	                {
	                    //pnto la imagen y el boton de limpiar
	                    $(this).html("<button onclick='RemoveImage(this,"+$(obj).parents().eq(4).attr('id')+")'>Limpiar</button><img src='"+e.target.result+"' height='180' width='250'>" );
	                }
	            });
	            
	        }
	        //Asigno la imagen
	        reader.readAsDataURL(obj.files[0]);
	    }
	    else
	    {
	        //Limpio la imagen y el input tipe file
	        $(obj).val("");
	        $(obj).parents().eq(5).find("DIV").each(function(){
	            if($(this).hasClass("spaceImage"))
	            {
	                $(this).html("");
	            }
	        });
	    }
	} catch (e) {
        console.log("Error Function ValidarImg: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: limpiarForm
 *  \brief: limpia el formulario y cierra el popap
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function limpiarForm()
{
	try
	{
	    closePopUp('popID');
	    $("#popID").remove();
	    $('.error').fadeOut();
	} catch (e) {
        console.log("Error Function limpiarForm: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: RemoveImage
 *  \brief: limpia las imagenes previsualizadas
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function RemoveImage(row,idDiv)
{
	try
	{
	    $(row).parent().html('');
	    $(idDiv).find("input[type=file]").each(function(i,v){
	        $(this).val('');
	    });
	} catch (e) {
        console.log("Error Function RemoveImage: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: addOtroInf
 *  \brief: Clona formulario informacion funcionario
 *  \author: Edward Serrano
 *  \date: 07/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function addOtroInf()
{
	try
	{
	    //Numero de formularios
	    var numForm = $('#Sec1infoFunciona > .formInfo .formcount').length + 1;
	    //Clono el formulario del prototipo
	    var CloneForm = $('#Sec1infoFunciona > .formInfo > .formPro').clone(true);
	    //recorro el objecto para eliminar la clase que identifica el prototipo
	    $.each(CloneForm,function(){
	        $(this).removeClass("formPro");
	    });
	    //Limpio la imagen
	    CloneForm.find(".spaceImage").each(function(i,v){
	        $(this).html("");
	    });
	    //Actulizo los ID del formulario clonado
	    CloneForm.find("DIV[id^=Sec3infoFunciona], DIV[id^=Sec2infoFunciona]").each(function(i,v){
	        $(this).attr('id', 'Sec3infoFunciona'+numForm);
	        $(this).attr('id', 'Sec2infoFunciona'+numForm);
	    });
	    //Limpio los input
	    CloneForm.find("input").each(function(i,v){
	        oldId = $(this).attr('id');
	        $(this).attr('id', oldId.replace('0',numForm));
	        $(this).val("");
	        if($(this).attr("type") == "file")
	        {
	        	oldName = $(this).attr('name');
	        	$(this).attr('name', oldName.replace('0',numForm));
	        }

	    });
	    //Agrego el formulario en el DOM
	    CloneForm.appendTo('.formInfo');
	} catch (e) {
        console.log("Error Function addOtroInf: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: getFormul
 *  \brief: Obtiene los formularios complementarios
 *  \author: Edward Serrano
 *  \date: 07/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function getFormul()
{
	try
	{
	    var standa = $("#standaID").val();
	    var tab_active = $("#tab_activeID").val();
	    $("#popForumlID").remove();
	    closePopUp('popForumlID');
	    LoadPopupJQNoButton('open', 'Formularios Complementarios ', '250', '500', false, false, true, 'popForumlID');
	    var popForuml = $("#popForumlID");

	    var parametros = "opcion=getOptionFormComp&Ajax=on&tab_active="+tab_active;
	    $.ajax({
	        url: "../" + standa + "/formul/par_formul_hojeal.php",
	        type: "POST",
	        data: parametros,
	        async: false,

	        success: function(data) {
	            popForuml.html(data); // pinta los datos de la consulta      
	        }
	    });
	} catch (e) {
        console.log("Error Function getFormul: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: addForumlComp
 *  \brief: Pinta los tab Seleccionados
 *  \author: Edward Serrano
 *  \date: 08/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function addForumlComp()
{
	try
	{
	    $("#tabs").tabs("destroy");
	    var standa = $("#standaID").val();
	    var formComp = [];
	    var formNomComp = [];
	    //Recorro las opciones seleccionadas y caturo el valor y el nombre el cuales
	    //buscado con un eq en un nivel superior
	    $("#OptionFormComp").find("input[type=checkbox]:checked").each(function(i,v){
	        formComp.push(v.value);
	        formNomComp["name"+v.value] = $(this).parents().eq(1).find("label").html();
	    });
	    if(formComp != "")
	    {
	        var parametros = "opcion=getDrawFormul&Ajax=on&cod_consec="+formComp;
	        $.ajax({
	            url: "../" + standa + "/formul/par_formul_hojeal.php",
	            type: "POST",
	            data: parametros,
	            async: false,
	            success: function(data) {
	            	//Actuliza los titulos de los tabs
	                $.each(formComp,function(ind,val){
	                    $("<li><a href='#tab"+val+"' >"+formNomComp["name"+val]+"<span class='ui-icon ui-icon-close' role='presentation' onclick='RemoveTabs(this);'>Remove Tab</span></a></li>").appendTo("#ulTab");
	                });
	                //Adiciona el contendio de los formularios a DOM
	                $(data).appendTo("#tabs");
	                //Contine los id de los tabs ya generados
	                var newActive = formComp.toString()+($("#tab_activeID").val() != ""?","+$("#tab_activeID").val():"");
	                $("#tab_activeID").val(newActive);
	                $("#tabs").tabs();
	            },
	            complete: function(){
	                closePopUp('popForumlID');
	                $('#popForumlID').remove();
	            }
	        }); 
	    }
	    else
	    {
	        alert("Debe seleccionar al menos un formulario.");
	    }
    } catch (e) {
        console.log("Error Function addForumlComp: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: RemoveTabs
 *  \brief: Elimina el tabs de formulario
 *  \author: Edward Serrano
 *  \date: 12/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function RemoveTabs(row)
{
	try
	{
		//Identificador de la etiqueta contenido estatico #tab
		var identificador = $(row).parents().attr("href");
		//Elimina la lista del tab
		var panelId = $( row ).closest( "li" ).remove().attr( "aria-controls" );
	    //Remueve el div de tab
	    $( identificador ).remove();
	    //Actulizo los tab
	    $("#tabs").tabs( "refresh" );
	    //Array que contine los tab activos
	    var mArray = $("#tab_activeID").val().split(",");
	    //Array con nuevos tab
	    var mNewArray = [];
	    //Recorro los tab actuales y excluyo el que ha sido eliminado
	    $.each(mArray,function(ind,val){
	    	if(val != identificador.substring(4))
	    	{
	    		mNewArray.push(val);
	    	}
	    });
	    //Asigno el nuevo valor de los tab
	    $("#tab_activeID").val(mNewArray.toString());
	} catch (e) {
        console.log("Error Function RemoveTabs: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: ConfirmAlmacerHvEal
 *  \brief: Genera popup para confirmar el amacenamiento de la informacion
 *  \author: Edward Serrano
 *  \date: 12/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function ConfirmAlmacerHvEal()
{
	try
	{
		var standa = $("#standaID").val();
	    var tab_active = $("#tab_activeID").val();
	    $("#popConfirmID").remove();
	    closePopUp('popConfirmID');
	    LoadPopupJQNoButton('open', 'Confirmacion ', '150', '300', false, false, true, 'popConfirmID');
	    var popForuml = $("#popConfirmID");
	    popForuml.html("</br><table width='100%'><tr><td colspan='2'><h3 align='center' style='color:white;'>Desea continuar?</h3></td></tr><tr><td style='text-align: end;'><button class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='AlmacerHvEal()'>Aceptar</button></td><td><button class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='closePopUp(\"popConfirmID\");$(\"#popConfirmID\").remove();'>Cancelar</button></td></tr></table>"); // pinta los datos de la consulta      
	} catch (e) {
        console.log("Error Function ConfirmAlmacerHvEal: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: AlmacerHvEal
 *  \brief: Almacena laa nuevas hojas de vida EAL
 *  \author: Edward Serrano
 *  \date: 12/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function AlmacerHvEal()
{
	try
	{
		//cierro el popup de confirmacion
		closePopUp("popConfirmID");
		$("#popConfirmID").remove();
		var standa = $("#standaID").val();
		var mdata = new FormData();
		mdata.append("opcion","insertar");
		mdata.append("Ajax","on");
		if(ValidateForm(mdata)!=false)
		{
			$.ajax({
		        url:"../" + standa + "/formul/par_formul_hojeal.php",
		        type:'POST',
		        dataType:'json',
		        contentType:false,
		        data: mdata,
		        //async: false,
		        processData:false,
		        cache:false,
		        success:function(data){
		          //alert(data);
		          console.log(data);
		        }
		    }); 
		}
	} catch (e) {
        console.log("Error Function AlmacerHvEal: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: ValidateForm
 *  \brief: Valida el formulario para almacenar la nueva hoja de vida EAL
 *  \author: Edward Serrano
 *  \date: 12/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function ValidateForm(mData)
{
	try
	{
		validacion = true;
		//valido la seccion secNewNotifi
		if($("#cod_controID").val() == "" || $("#cod_controID").val() == "-")
		{
			inc_alerta("cod_controID", "Campo requerido.");
			$("#cod_controID").focus();
			validacion = false;
		}
		else
		{
			mData.append($("#cod_controID").attr('name'),$("#cod_controID").val());
		}
		//valido la seccion Sec1RepreLegal
		var RepreLegal = {};
		$("#Sec1RepreLegal").find("input").each(function(){
			if(getInfForm(RepreLegal,this) == false)
			{
				validacion = false;
			}
		});
		mData.append("RepreLegal",JSON.stringify(RepreLegal));
		//Valido la seccion infoFunciona
		var infoFunciona = {};
		$("#Sec1infoFunciona").find(".formcount").each(function(index, value){
			var sec1 = {};
			$(this).find("input").each(function(key, infInput){
				if(getInfForm(sec1,infInput) == false)
				{
					validacion = false;
				}
			});
			//omito la etiqueta inc_alert
			if(!$(value).hasClass("inc_alert"))
			{
				infoFunciona[index] = sec1;
			}
		});
		mData.append("infoFunciona",JSON.stringify(infoFunciona));
		//Recorro los tab de los formularios adiccionados
		var infTab = {};
		$("#tabs").find("div").each(function(indTab, valTab){
			var sec1 = {};
			$(valTab).find("input,select").each(function(indSubTab, valSubTab){
				if(getInfForm(sec1,valSubTab,0) == false)
				{
					validacion = false;
				}
			});
			//omito la etiqueta inc_alert
			if(!$(valTab).hasClass("inc_alert"))
			{
				infTab[$(valTab).attr("id")] = sec1;
			}
		});
		mData.append("infTab",JSON.stringify(infTab));
		//Recorro todas las imagenes del fomulario
		$("#popID").find("input[type=file]").each(function(){
			if($(this).val() != "")
			{
				mData.append($(this).attr("name"),$(this)[0].files[0]);
			}
		});
		if(validacion == false)
		{
			return validacion;
		}
		else
		{
			return mData;
		}
	} catch (e) {
        console.log("Error Function ValidateForm: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: getInfForm
 *  \brief: procesa la imformacion del formulario
 *  \author: Edward Serrano
 *  \date: 13/06/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function getInfForm(data, objecto, nivel = null)
{
	try
	{
		var valorcampo = "";
		//parent
		if(nivel != null)
		{
			rowP = $(objecto).parents().eq(nivel);
		}
		else
		{
			rowP = objecto;
		}
		//valido los campos type text
		switch($(objecto).attr("type")) {
		    case "text":
		        		valorcampo = $(objecto).val();
		        break;
		    case "radio": case "checkbox": 
		        		if($(objecto).is(':checked'))
		        		{
			        		valorcampo = $(objecto).val();
		        		}
		        		else
		        		{
		        			return data;
		        		}
		        break;
		    case "file":
		        		data["file"] = $(objecto).attr("name");
		        		return data;
		        break;
		    default:
		    			if($(objecto).prop("tagName") == "SELECT")
		    			{
		    				valorcampo = $(objecto).val();
		    			}
		    	break;
		}
			
		if($(rowP).attr("obl")=="obl")
		{
			if($(objecto).val()=="")
			{
				inc_alerta($(objecto).attr("id"), "Campo requerido.");
				validacion = false;
			}
			else if($(objecto).attr("format") == "mail")
			{
				mailregexp = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i 
				if($(objecto).val() == '')
				{
					inc_alerta($(objecto).attr("id"), "Campo requerido.");
					validacion = false;
				}
				else if( !mailregexp.test( $(objecto).val() ) )
				{
					inc_alerta($(objecto).attr("id"), "Verificar el formato del correo.");
					validacion = false;
				}
				else
				{
					data[$(objecto).attr("name")] = valorcampo;
				}
			}
			else
			{
				data[$(objecto).attr("name")] = valorcampo;		
			}
		}
		else
		{
			data[$(objecto).attr("name")] = valorcampo;
		}
		return data;
	} catch (e) {
        console.log("Error Function getInfForm: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}