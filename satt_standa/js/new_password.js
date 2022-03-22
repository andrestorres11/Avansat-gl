/******************************************************************************************************
 * Archivo de Funciones Javascript new_password.js                                                    *
 * @version 0.1                                                                                       *
 * @ultima_modificacion 27 de Abril de 2010                                                           *
 * @author Christiam Barrera Arango( The Messias )                                                    *
 ******************************************************************************************************/

function ConfirmCredit( cod_usuari, num_credit )
{
  try
  {
    var msg = 'La Autenticación de su Usuario "' + cod_usuari + '" ha sido realizada con éxito.'
    if ( num_credit >= 1 && num_credit <= 5 )
    {
      msg += '\nSin embargo INTRARED.NET le notifica que dentro de ' + String( num_credit ) + ' día(s) su contraseña caducará.'
      msg += '\n\n¿Desea cambiar su contraseña ahora?';
      if ( confirm( msg ) )
      {
        LoadNewPasswordForm();
      }
      else
      {
        document.getElementById( 'actionID' ).value = 'mainSATE';
        document.getElementById( 'frm_changeID' ).submit();
        return false;
      }
    }
    else if ( Number( num_credit ) <= 0 )
    {
      msg += '\nNo obstante su contraseña ha caducado. Por lo tanto deberá cambiarla a continuación.';
      alert( msg );
      LoadNewPasswordForm();
    }
  }
  catch( e )
  {
    alert( 'Error Function ConfirmCredit: ' + e.message );
  }
}


function LoadNewPasswordForm()
{
  try
  {
    var frm_change  =  document.getElementById( 'frm_changeID' );
    var action      =  document.getElementById( 'actionID' );
    action.value    =  'form';
    frm_change.submit();
  }
  catch( e )
  {
    alert( 'Error Function LoadNewPasswordForm: ' + e.message );
  }
}


function UpdatePassword()
{
  try
  {
    var bloker      =  document.getElementById( 'central_transparencyDIV' );
    var frm_change  =  document.getElementById( 'frm_changeID' );
    var clv_anteri  =  document.getElementById( 'clv_anteriID' );
    var clv_usuari  =  document.getElementById( 'clv_usuariID' );
    var clv_retype  =  document.getElementById( 'clv_retypeID' );
    
    var usuario     =  document.getElementById( 'usuarioID' );
    var clave       =  document.getElementById( 'claveID' );
    var action      =  document.getElementById( 'actionID' );
    
    bloker.style.visibility = 'visible';
    
    if ( !clv_anteri.value )
    {
      alert( 'Ingrese su Contraseña Actual.' );
      bloker.style.visibility = 'hidden';
      clv_anteri.focus();
      return false;
    }
    if ( clv_anteri.value != clave.value )
    {
      alert( 'La Contraseña Actual es Incorrecta.' );
      bloker.style.visibility = 'hidden';
      clv_anteri.value = '';
      clv_anteri.focus();
      return false;
    }
    if ( !clv_usuari.value )
    {
      alert( 'Ingrese su Nueva Contraseña. Debe contener 8 o más dígitos.' );
      bloker.style.visibility = 'hidden';
      clv_usuari.focus();
      return false;
    }
    if ( clv_usuari.value.length < 8 )
    {
      alert( 'Su Nueva Contraseña debe contener 8 o más dígitos.' );
      bloker.style.visibility = 'hidden';
      clv_usuari.value = '';
      clv_usuari.focus();
      return false;
    }
    if ( !clv_retype.value )
    {
      alert( 'Confirme su Nueva Contraseña. Debe contener 8 o más dígitos.' );
      bloker.style.visibility = 'hidden';
      clv_retype.focus();
      return false;
    }
    if ( clv_retype.value.length < 8 )
    {
      alert( 'Su Nueva Contraseña debe contener 8 o más dígitos.' );
      bloker.style.visibility = 'hidden';
      clv_retype.value = '';
      clv_retype.focus();
      return false;
    }
    if ( clv_retype.value != clv_usuari.value )
    {
      alert( 'La Confirmación de su Nueva Contraseña es Incorrecta.\nConfirme por favor su Nueva Contraseña.' );
      bloker.style.visibility = 'hidden';
      clv_retype.value = '';
      clv_retype.focus();
      return false;
    }
    if ( confirm( 'Esta acción modificará la contraseña del usuario "' + usuario.value + '". ¿Está seguro de continuar?' ) ) 
    {
      action.value = 'update_password';
      frm_change.submit();
    }
    bloker.style.visibility = 'hidden';
    return true;
  }
  catch( e )
  {
    alert( 'Error Function LoadNewPasswordForm: ' + e.message );
  }
}


function ConfirmUpdatePassword( cod_usuari, num_diasxx )
{
  try
  {
    alert( 'La Contraseña del Usuario "' + cod_usuari + '" ha sido Actualizada con éxito.\nEsta Nueva Contraseña Caducará en ' + num_diasxx + ' días.' );
    GoToSATLogin();
  }
  catch( e )
  {
    alert( 'Error Function ConfirmUpdatePassword: ' + e.message );
  }
}


function GoToSATLogin()
{
  try
  {
    var frm_change  =  document.getElementById( 'frm_changeID' );
    var usuario     =  document.getElementById( 'usuarioID' );
    var clave       =  document.getElementById( 'claveID' );
    var action      =  document.getElementById( 'actionID' );
    var Submit      =  document.getElementById( 'SubmitID' );
    
    usuario.value    = '';
    clave.value      = '';
    action.value     = '';
    Submit.value     = '';
    
    frm_change.submit();
  }
  catch( e )
  {
    alert( 'Error Function GoToSATLogin: ' + e.message );
  }
}


function ValidatePassword( input ) 
{
  try {
    if ( !input.value )
      return true;
    if ( input.value.length < 8 ) {
      alert('La Contraseña no puede tener menos de 8 carácteres.');
      input.value = '';
      input.focus();
      return false;
    }
    if ( IsNumeric( input.value ) ) {
      alert( 'La Contraseña debe ser Alphanúmerica. Debe contener carácteres de aA-zZ y números del 0-9.' );
      input.value = '';
      input.focus();
      return false;
    }
    if ( input.value.indexOf( '1' )==-1 && input.value.indexOf( '2' )==-1 && input.value.indexOf( '3' )==-1 && input.value.indexOf( '4' )==-1 && input.value.indexOf( '5' )==-1 && 
         input.value.indexOf( '6' )==-1 && input.value.indexOf( '7' )==-1 && input.value.indexOf( '8' )==-1 && input.value.indexOf( '9' )==-1 && input.value.indexOf( '0' )==-1 ) {
      alert( 'La Contraseña debe ser Alphanúmerica. Debe contener carácteres de aA-zZ y números del 0-9.' );
      input.value = '';
      input.focus();
      return false;
    }
    return true;
  }
  catch( e )
  {
    alert( 'Error Function ValidatePassword: ' + e.message );
  }
}
