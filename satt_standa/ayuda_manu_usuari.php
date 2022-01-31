<?php
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
class ManualHtml
{

  function __construct()
  {
    $this -> General();
  }
  
  
  function General()
  {
    $mUrl = ($_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://').$_SERVER["HTTP_HOST"]."/ap/satt_standa/manual/Manual de Usuario Avansat GL versión 1.4.pdf";
    echo "<tr>
              <td> 
                <script src='../satt_standa/js/jquery17.js' type='text/javascript'></script>
                <div id='ContentIframeID' style='width: 100%; height: 100;'>
                  <iframe frameborder='0' src='".$mUrl."' style='width: 100%; height: 100%;'> </iframe>
                </div>
                <script>
                  $(document).ready(function(){ $('#ContentIframeID').css({'height': ( $(window).outerHeight() - 5)+'px'}); });
                </script>
              </td>
          </tr>";
  }
  
}

$mManual = new ManualHtml();

?>