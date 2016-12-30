$(document).ready(function() 
{
  $("#fec_iniID, #fec_finID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
    });
  


});
function btnGeneral()
  {
      var fec_iniID=$("#fec_iniID").val();
      var fec_finID=$("#fec_finID").val();
      var standa = $("#standaID").val();
      var formData = "option=getFormGeneral";
      //alert(fec_iniID+" ;"+fec_finID);
      if(fec_iniID!="")
      {
          if(fec_finID!="")
          {
              $.ajax({
                  url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                  type: "POST",
                  data: formData,
                  async: true,
                  beforeSend: function() {
              
                  },
                  success: function(data) {
                      alert(data);
                  }
              });
          }else
          {
            alert("Seleciones una fecha");
          }
      }else
      {
        alert("Seleciones una fecha");
      }
  }