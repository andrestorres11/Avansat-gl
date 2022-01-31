
//--------------------------

$(function() {
  $( "#dialog:ui-dialog" ).dialog( "destroy" );
  $( "#pc" ).dialog({
    height: ($.browser.msie ? 800: 400),
    width: '70%',
    draggable: false,
    resizable: false,
    autoOpen: false,
    modal: true
  });
});



function poppc(num_despac){
  
  var central = document.getElementById('central').value;
  var atributes = "Ajax=on";
      atributes+= '&opcion=getPC';
      atributes+= '&window=central';
      atributes+= '&num_despac=' + num_despac;
  
  $.ajax({
    type: "POST",
    url: '../'+ central +'/inform/inf_despac_transi.php',
    data: atributes,
    success: function(data) {
      $('#pc').html(data);
      $("#pc").dialog('open');
    }
  });
}