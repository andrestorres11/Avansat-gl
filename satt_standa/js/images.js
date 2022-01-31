function showImage( num_docume, nom_destin, num_despac )
{
  try
  {
    var dynamicDiv = '<img src="tmp/' + num_despac + '/' + num_docume + '.jpg" width="100%"><a id="Close" class="close" onclick="$.unblockUI(); $(\'#imgDetailDiv\').html(\'\');">&nbsp;</a>';
    $("#imgDetailDiv").html( dynamicDiv );
    
    $.blockUI({ 
      message: $("#imgDetailDiv"), 
      css: { 
        top:  ($(window).height() - 200) /2 + 'px', 
        left: ($(window).width() - 200) /2 + 'px', 
        width: '40%' 
      },
      overlayCSS:  
      { 
        backgroundColor: '#000000', 
        opacity:         0.9, 
        cursor:          'default' 
      }
    }); 
  }
  catch( e )
  {
    console.log( e.message );
  }
}