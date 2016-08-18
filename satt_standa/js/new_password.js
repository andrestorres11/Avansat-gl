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
    var msg = 'La Autenticaci�n de su Usuario "' + cod_usuari + '" ha sido realizada con �xito.'
    if ( num_credit >= 1 && num_credit <= 5 )
    {
      msg += '\nSin embargo INTRARED.NET le notifica que dentro de ' + String( num_credit ) + ' d�a(s) su contrase�a caducar�.'
      msg += '\n\n�Desea cambiar su contrase�a ahora?';
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
      msg += '\nNo obstante su contrase�a ha caducado. Por lo tanto deber� cambiarla a continuaci�n.';
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
      alert( 'Ingrese su Contrase�a Actual.' );
      bloker.style.visibility = 'hidden';
      clv_anteri.focus();
      return false;
    }
    if ( clv_anteri.value != clave.value )
    {
      alert( 'La Contrase�a Actual es Incorrecta.' );
      bloker.style.visibility = 'hidden';
      clv_anteri.value = '';
      clv_anteri.focus();
      return false;
    }
    if ( !clv_usuari.value )
    {
      alert( 'Ingrese su Nueva Contrase�a. Debe contener 8 o m�s d�gitos.' );
      bloker.style.visibility = 'hidden';
      clv_usuari.focus();
      return false;
    }
    if ( clv_usuari.value.length < 8 )
    {
      alert( 'Su Nueva Contrase�a debe contener 8 o m�s d�gitos.' );
      bloker.style.visibility = 'hidden';
      clv_usuari.value = '';
      clv_usuari.focus();
      return false;
    }
    if ( !clv_retype.value )
    {
      alert( 'Confirme su Nueva Contrase�a. Debe contener 8 o m�s d�gitos.' );
      bloker.style.visibility = 'hidden';
      clv_retype.focus();
      return false;
    }
    if ( clv_retype.value.length < 8 )
    {
      alert( 'Su Nueva Contrase�a debe contener 8 o m�s d�gitos.' );
      bloker.style.visibility = 'hidden';
      clv_retype.value = '';
      clv_retype.focus();
      return false;
    }
    if ( clv_retype.value != clv_usuari.value )
    {
      alert( 'La Confirmaci�n de su Nueva Contrase�a es Incorrecta.\nConfirme por favor su Nueva Contrase�a.' );
      bloker.style.visibility = 'hidden';
      clv_retype.value = '';
      clv_retype.focus();
      return false;
    }
    if ( confirm( 'Esta acci�n modificar� la contrase�a del usuario "' + usuario.value + '". �Est� seguro de continuar?' ) ) 
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
    alert( 'La Contrase�a del Usuario "' + cod_usuari + '" ha sido Actualizada con �xito.\nEsta Nueva Contrase�a Caducar� en ' + num_diasxx + ' d�as.' );
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
      alert('La Contrase�a no puede tener menos de 8 car�cteres.');
      input.value = '';
      input.focus();
      return false;
    }
    if ( IsNumeric( input.value ) ) {
      alert( 'La Contrase�a debe ser Alphan�merica. Debe contener car�cteres de aA-zZ y n�meros del 0-9.' );
      input.value = '';
      input.focus();
      return false;
    }
    if ( input.value.indexOf( '1' )==-1 && input.value.indexOf( '2' )==-1 && input.value.indexOf( '3' )==-1 && input.value.indexOf( '4' )==-1 && input.value.indexOf( '5' )==-1 && 
         input.value.indexOf( '6' )==-1 && input.value.indexOf( '7' )==-1 && input.value.indexOf( '8' )==-1 && input.value.indexOf( '9' )==-1 && input.value.indexOf( '0' )==-1 ) {
      alert( 'La Contrase�a debe ser Alphan�merica. Debe contener car�cteres de aA-zZ y n�meros del 0-9.' );
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
