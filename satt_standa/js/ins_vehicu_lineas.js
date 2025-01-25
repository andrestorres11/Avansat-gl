/* !\file: ins_vehicu_lineas.js
 *  \brief: Modulo de funciones para validar en el módulo de Lineas
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
        $("#cod_lineasID").val("");
        $("#cod_lineasID").prop("readonly", false);
        $("#nom_lineaxID").val("");
    } 
    catch ( e ) 
    {
        sweetAlert("Lineas", "Error limpiarFormulario " + e.message, "error");
    }
}

/*! \fn: ActivarLineas
 *  \brief: Activar/Desactivar Linea a traves de una alerta
 *  \author: Ing. Jesus Sanchez
 *  \date: 25/04/2019
 *  \param: cod_lineas (Codigo de ministerio del Linea)
 *  \param: ind_estado (Estado actual: 1 activo 0 inactivo)
 *  \return NADA
 */
function ActivarLineas(cod_lineas, mintra_cliente, ind_estado, nom_lineax, cod_marcax){
    var mensaje = "";

    if(mintra_cliente != "" && ind_estado == "1"){
        mensaje = "desactivar";
    }else if(mintra_cliente != "" && ind_estado == "0"){
        mensaje = "activar";
    }else{
        mensaje = "insertar";
    }

    nom_lineax = nom_lineax.replaceAll('`','"');
    
    try 
    {
        swal({
            title: "Lineas",
            text: "¿Está seguro de "+mensaje+" la linea "+nom_lineax+" con código de ministerio "+cod_lineas+"?",
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
                $("#optionID").val("5");
                $("#cod_lineasID").val(cod_lineas);
                $("#cod_marcasID").val(cod_marcax); 
                $("input").prop("disabled", false);
                $("#form_lineasID").submit();
            }
            swal.close();
        });
    } 
    catch ( e ) 
    {
        sweetAlert("Lineas", "Error ActivarLineas " + e.message, "error");
    }
}
