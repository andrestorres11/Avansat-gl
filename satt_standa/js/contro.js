function setColor(color, nom) {
    var f = document.form_insert;
    var nom_cam = nom;


    if (color) {
        f.color.value = color;
        f.color.disabled;
    }
    //test.style.background = f.color.value;
    document.getElementById('test').style.background = '#' + f.color.value;
    //fix for mozilla: does this work with ie? opera ok.
    window.close()
}

function ShowRow() {
    try {
        var div = '';
        var urbano = document.getElementById('urbanoID').checked;

        if (urbano == true) {
            div = document.getElementById('colorID');
            div.disabled = false;
            div = document.getElementById('coloreal');
            div.style.display = '';
        } else {
            div = document.getElementById('colorID');
            div.value = '';
            div.disabled = true;
            div = document.getElementById('coloreal');
            div.style.display = 'none';
        }
    } catch (e) {
        alert("Error en ShowRow: " + e.message);
    }
}

function aceptar_insert(formulario) {
    virtua = document.getElementsByName("virtua");
    valor = "";

    var seleccionado = false;
    for (var i = 0; i < virtua.length; i++) {
        if (virtua[i].checked) {
            seleccionado = true;
            valor = virtua[i].value;
            break;
        }
    }

    validacion = true
    formulario = document.form_insert
    if (formulario.nom.value == "") {
        window.alert("El Nombre es Requerido")
        validacion = false
    } else if (formulario.ciudad.value == "0") {
        window.alert("La Ciudad es Requerida")
        validacion = false
    } else if (formulario.enc.value == "") {
        window.alert("El Encargado es Requerido")
        validacion = false
    } else if (formulario.dir.value == "") {
        window.alert("La Direccion es Requerida")
        validacion = false
    } else if (formulario.tel.value == "") {
        window.alert("El Telefono es Requerido")
        validacion = false
    } else if (!seleccionado) {
        window.alert("El Tipo de Puesto 'Fisico/Virtual' es Requerido")
        validacion = false
    } else {
        flag = false;
        if (valor == '0') {
            if (formulario.longi.value.length < 1 || formulario.latit.value.length < 1) {
                alert("Debe Llenar los campos Latitud y Longitud");
            } else {
                flag = true;
            }
        } else {
            flag = true;
        }

        if (flag == true) {

            if (confirm("Esta Seguro de Insertar el Puesto de Control?")) {
                formulario.opcion.value = 2;
                formulario.submit();
            }
        }
    }
}

function aceptar_lis(formulario) {
    validacion = true
    formulario = document.form_list
    formulario.opcion.value = 2;
    formulario.submit();
}

/*! \fn: reload_url
 *  \brief: actualiza los campos de las URLs
 *  \author: Ing. Miguel Romero
 *  \date: 28/04/2016 
 */
function reload_url() {
    longi = document.getElementsByName("longi");
    latit = document.getElementsByName("latit");

    document.getElementsByName("url_wazexx")[0].value = "http://waze.to/?ll=" + longi[0].value + "," + latit[0].value + "&navigate=yes";
    document.getElementsByName("url_google")[0].value = "http://maps.google.com/?q=" + longi[0].value + "," + latit[0].value + "";
}

function aceptar_act(formulario) {
    validacion = true
    formulario = document.form_act
    formulario.opcion.value = 1;
    formulario.submit();
}

function aceptar_act2(formulario) {
    virtua = document.getElementsByName("virtua");
    valor = "";

    for (var i = 0; i < virtua.length; i++) {
        if (virtua[i].checked) {
            valor = virtua[i].value;
            break;
        }
    }

    validacion = true
    formulario = document.form_insert
    if (formulario.nom.value == "") {
        window.alert("El Nombre es Requerido")
        validacion = false
    } else if (formulario.ciudad.value == "0") {
        window.alert("La Ciudad es Requerida")
        validacion = false
    } else if (formulario.enc.value == "") {
        window.alert("El Encargado es Requerido")
        validacion = false
    } else if (formulario.dir.value == "") {
        window.alert("La Direccion es Requerida")
        validacion = false
    } else if (formulario.tel.value == "") {
        window.alert("El Telefono es Requerido")
        validacion = false
    } else {
        flag = false;
        if (valor == '0') {
            if (formulario.longi.value.length < 1 || formulario.latit.value.length < 1) {
                alert("Debe Llenar los campos Latitud y Longitud");
            } else {
                flag = true;
            }
        } else {
            flag = true;
        }

        if (flag == true) {

            if (confirm("Esta Seguro de Actualizar el Puesto de Control?")) {
                formulario.opcion.value = 3;
                formulario.submit();
            }
        }
    }
}

function eli_aceptar(formulario) {
    if (confirm("Esta Seguro de Eliminar el Puesto de Control?")) {
        formulario.opcion.value = 3;
        formulario.submit();
    }
}
