/*! \fn: validarFiltrosInformeAcceso
 *  \brief: Validar la selecci�n de fechas y/o de usuario en los filtros
 *  \author: Ing. David Rinc�n
 *  \date: 14/02/2020
 *  \param: NINGUNO
 *  \return NADA, pero ayuda a validar antes de hacer submit
 */
function validarFiltrosInformeAcceso() {
    try
    {
        if ($("#fec_iniciaID").val() == "" && $("#fec_finalxID").val() == "" && $("#cod_usuariID").val() == "") {
            sweetAlert("Ingresos de Usuarios", "Al menos debe seleccionar uno de los filtros (Fechas o Usuario).", "warning");
        } else if($("#fec_iniciaID").val() != "" && $("#fec_finalxID").val() == "") {
            sweetAlert("Ingresos de Usuarios", "Debe seleccionar tambi�n una fecha final.", "warning");
        } else if($("#fec_iniciaID").val() == "" && $("#fec_finalxID").val() != "") {
            sweetAlert("Ingresos de Usuarios", "Debe seleccionar tambi�n una fecha inicial.", "warning");
        } else if($("#fec_iniciaID").val() != "" && $("#fec_finalxID").val() != "" && $("#fec_iniciaID").val() > $("#fec_finalxID").val()) {
            sweetAlert("Ingresos de Usuarios", "La fecha inicial debe ser menor o igual a la fecha final.", "warning");
        } else {
            swal({
                title: "Ingresos de Usuarios",
                text: "�Est� seguro de enviar esta informaci�n?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Si",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm)
            {
                if (isConfirm)
                {
                    var formulario = document.getElementById( "form_listaID" );
                    formulario.submit();
                }
                swal.close();
            });
        }
    }
    catch(e)
    {
        sweetAlert("Ingresos de Usuarios", "Error validarFiltrosInformeAcceso " + e.message, "error");
    }
}