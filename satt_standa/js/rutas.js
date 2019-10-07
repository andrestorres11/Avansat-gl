$("document").ready(function() {

	var standa = $("#standaID").val();
	var atributes = '&Ajax=on&standa=' + standa;

	$("#origenID").autocomplete({
		source: "../" + standa + "/rutas/class_rutasx_rutasx.php?Option=getCiudades" + atributes,
		minLength: 3,
		select: function(event, ui) {
			$("#cod_ciuoriID").val(ui.item.id);
		}
	});

	$("#destinoID").autocomplete({
		source: "../" + standa + "/rutas/class_rutasx_rutasx.php?Option=getCiudades" + atributes,
		minLength: 3,
		select: function(event, ui) {
			$("#cod_ciudesID").val(ui.item.id);
		}
	});

	$("#transpID").autocomplete({
		source: "../" + standa + "/rutas/class_rutasx_rutasx.php?Option=getTransp" + atributes,
		minLength: 3,
		select: function(event, ui) {
			$("#cod_transpID").val(ui.item.id);
		}
	});

	$("input[name^='contr']").each(function() {
		$(this).autocomplete({
			source: "../" + standa + "/rutas/class_rutasx_rutasx.php?Option=getPC" + atributes,
			minLength: 3,
			select: function(event, ui) {
				var nameID = $(this).attr("nameID");
				$("#" + nameID).val(ui.item.id);
			}
		});
	});
});

function aceptar_insert(formulario) {
	validacion = true
	formulario = document.form_ins
	if (formulario.nom.value == '') {
		window.alert("El Nombre la Rutas es Requerido")
		formulario.nom.focus()
		validacion = false
	} else if (formulario.origen.value == "0") {
		window.alert("Seleccione la ciudad de Origen")
		formulario.origen.focus()
		validacion = false
	} else if (formulario.destino.value == "0") {
		window.alert("Seleccione la cuidad de Destino")
		formulario.destino.focus()
		validacion = false
	} else if (confirm("Desea Crear La ruta " + formulario.nom.value + " ?")) {
		formulario.opcion.value = 1;
		formulario.submit();
	}
}

function aceptar_lis(formulario) {
	validacion = true
	formulario = document.form_list
	if (formulario.ruta.value == "") {
		window.alert("La Ruta es Requerida")
		validacion = false
	} else {
		formulario.opcion.value = 2;
		formulario.submit();
	}
}

function aceptar_act(formulario) {
	validacion = true
	formulario = document.form_act
	if (formulario.ruta.value == "") {
		window.alert("La Ruta es Requerida")
		formulario.ruta.focus()
		validacion = false
	} else if (formulario.origen.value == "0") {
		window.alert("Seleccione la ciudad de Origen")
		formulario.origen.focus()
		validacion = false
	} else if (formulario.destino.value == "0") {
		window.alert("Seleccione la ciudad de Destino")
		formulario.destino.focus()
		validacion = false
	} else if (confirm("Desea actualizar la ruta?")) {
		formulario.opcion.value = 1;
		formulario.submit();
	}
}

function aceptar_eli(formulario) {
	validacion = true
	formulario = document.form_eli
	if (formulario.ruta.value == "") {
		window.alert("La Ruta es Requerida")
		validacion = false
	} else {
		formulario.opcion.value = 1;
		formulario.submit();
	}
}

function aceptar_act2(formulario) {
	validacion = true
	formulario = document.form_item
	if (formulario.nombre.value == '') {
		window.alert("El Nombre la Rutas es Requerida")
		validacion = false
	} else {
		formulario.opcion.value = 3;
		formulario.submit();
	}
}

function aceptar_act3(formulario) {
	validacion = true
	formulario = document.form_item
	if (formulario.ruta.value == '') {
		window.alert("El Nombre la Rutas es Requerida")
		validacion = false
	} else {
		formulario.opcion.value = 1;
		formulario.submit();
	}
}

function aceptar_actuali(formulario) {
	validacion = true
	formulario = document.form_item
	valorMayor = $("#timepcultID");

	var numerico = /^[0-9]+\.?[0-9]*$/

	if ( document.getElementById('origenID') ){
		if (formulario.origen.value == "") {
			window.alert("La Ciudad de Origen es Requerida.")
			formulario.origen.focus();
			return false;
		}
	}

	if ( document.getElementById('destinoID') ){
		if (formulario.destino.value == "") {
			window.alert("La Ciudad de Destino es Requerida.")
			formulario.destino.focus();
			return false;
		}
	}

	if (formulario.nom.value == "") {
		window.alert("La Descripcion de la Via es Requerida")
		validacion = false
		formulario.nom.focus();
	} else if (formulario.timepcult.value == "") {
		window.alert("Debe Asignar los Minutos Desde el Origen Para Ultimo Puesto de Control")
		validacion = false
		formulario.timepcult.focus();
	} else if (!numerico.test(formulario.timepcult.value)) {
		window.alert("Los Minutos Desde el Origen Deben Contener Solo Valores Numericos.")
		validacion = false
		formulario.timepcult.focus();
	} else if (parseInt(formulario.timepcult.value) <= valorMayor.value) {
		window.alert("Los Minutos Desde el Origen Para Ultimo Puesto de Control Debe ser Mayor que los Anteriores.")
		validacion = false
		formulario.timepcult.focus();
	} else {
		if (confirm("Desea Modificar la Ruta.?")) {
			formulario.opcion.value = 3;
			formulario.submit();
		}
	}
}

function exporExcel( idBoton, name )
{	
	//Se captura como objeto la tabla que contiene los registros
	var tablaExport = $("#"+idBoton).parents("table").siblings()[3];
  	//var tablaExport = document.getElementById(idBoton).parentNode.parentNode.parentNode.parentNode.parentNode.nextSibling.nextSibling;
  	//Se Asigna un ID a la tabla
	tablaExport.setAttribute('id', 'tableListRut');

    var wb = XLSX.utils.table_to_book(document.getElementById('tableListRut'), {sheet:"Sheet JS"});
    var wbout = XLSX.write(wb, {bookType:'xls', bookSST:true, type: 'binary'});
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }
    //Asigna el evento
    saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), name+'.xls');
  
}