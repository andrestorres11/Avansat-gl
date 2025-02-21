/* !\file: ins_vehicu_colore.js
*  \brief: Modulo de funciones para validar en el módulo de colores
*  \author: Ing. Jesus Sanchez
*  \date: 29/04/2024
*/
/*! \fn: limpiarFormulario
 *  \brief: Limpia el formulario, dejándolo en modo de inserción
 *  \author: Ing. Jesus Sanchez
 *  \date: 29/04/2024
 *  \param: NINGUNO
 *  \return NADA
 */
function limpiarFormulario(){
    try 
    {
        $("#optionID").val("2");
        $("#btn_validaID").val("Insertar");
        $("#cod_coloreID").val("");
        $("#cod_coloreID").prop("readonly", false);
        $("#nom_colorxID").val("");
    } 
    catch ( e ) 
    {
        sweetAlert("Colores", "Error limpiarFormulario " + e.message, "error");
    }
}


/*! \fn: activarColores
 *  \brief: Activar/Desactivar Color a traves de una alerta
 *  \author: Ing. Jesus Sanchez
 *  \date: 25/04/2019
 *  \param: cod_colore (Codigo de ministerio del color)
 *  \param: ind_estado (Estado actual: 1 activo 0 inactivo)
 *  \return NADA
 */
function activarColores(cod_colore, mintra_cliente, nom_colorx, ind_estado){
    var mensaje = "";

    if(mintra_cliente != "" && ind_estado == "1"){
        mensaje = "desactivar";
    }else if(mintra_cliente != "" && ind_estado == "0"){
        mensaje = "activar";
    }else{
        mensaje = "insertar";
    }

    nom_colorx = nom_colorx.replaceAll('`','"');

    try 
    {
        swal({
            title: "Color",
            text: "¿Está seguro de "+mensaje+" el Color "+nom_colorx+" con código de ministerio "+cod_colore+"?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm)
        {
            if (isConfirm)
            {
                LockAplication('lock');
                AjaxLoader('block')
                // limpiarFormulario(); 
                $("#opcionID").val("5");
                $("#cod_coloreID").val(cod_colore);
                // $("input").prop("disabled", false);
                $("#form_coloresID").submit();
            }
            swal.close();
        });
    } 
    catch ( e ) 
    {
        sweetAlert("Colores", "Error activarColores " + e.message, "error");
    }
}

