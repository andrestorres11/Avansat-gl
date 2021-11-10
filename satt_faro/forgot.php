<?php

class forgotPass
{
  public function __construct()
  {
  	$_REQUEST['option'] = 'initialValidation';
  	$_REQUEST['ind_otherx'] = '1';
  	include_once('../satt_standa/forgot/ajax_forgot_forgot.php');
  }	
}

$_FORGOT = new forgotPass();

?>