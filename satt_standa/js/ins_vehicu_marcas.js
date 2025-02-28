/* !\file: ins_vehicu_marcas.js
 *  \brief: Modulo de funciones para validar en el modulo de Marcas
 *  \author: Ing. Jesus Sanchez
 *  \date: 29/04/2024
 */


/*! \fn: limpiarFormulario
 *  \brief: Limpia el formulario, dejandolo en modo de insercion
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
        $("#cod_marcasID").val("");
        $("#cod_marcasID").prop("readonly", false);
        $("#nom_marcaxID").val("");
    } 
    catch ( e ) 
    {
        sweetAlert("Marcas", "Error limpiarFormulario " + e.message, "error");
    }
}


/*! \fn: activarMarcas
 *  \brief: Activar/Desactivar Marca a traves de una alerta
 *  \author: Ing. Jesus Sanchez
 *  \date: 25/04/2019
 *  \param: cod_marcas (Codigo de ministerio del Marca)
 *  \param: ind_estado (Estado actual: 1 activo 0 inactivo)
 *  \return NADA
 */
function activarMarcas(cod_marcas, mintra_cliente, nom_marcax, ind_estado){
    var mensaje = "";

    if(mintra_cliente != "" && ind_estado == "1"){
        mensaje = "desactivar";
    }else if(mintra_cliente != "" && ind_estado == "0"){
        mensaje = "activar";
    }else{
        mensaje = "insertar";
    }

    nom_marcax = nom_marcax.replaceAll('`','"');
    
    try 
    {
        Swal.fire({
            title: "Marcas",
            text: " Esta seguro de "+mensaje+" la marca "+nom_marcax+" con codigo de ministerio "+cod_marcas+"?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then((result) => {
            if (result.value) {
                // LockAplication('lock');
                // AjaxLoader('block')
                // limpiarFormulario();
                $("#opcionID").val("5");
                $("#cod_marcasID").val(cod_marcas);
                // $("input").prop("disabled", false);
                $("#form_marcasID").submit();
            }
            swal.close();
        });
    } 
    catch ( e ) 
    {
        // sweetAlert("Marcas", "Error activarMarcas " + e.message, "error");
        Swal.fire({
            title: 'Error!',
            text: "Error activarMarcas " + e.message,
            type: 'error',
            confirmButtonColor: '#336600'
        });
    }
}
