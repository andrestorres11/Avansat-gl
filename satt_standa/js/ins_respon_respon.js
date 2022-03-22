$("body").ready(function(){
	$(".accordion").accordion({
		collapsible : true, 
		heightStyle : "content",
		icons: { "header" : "ui-icon-circle-arrow-e", "activeHeader": "ui-icon-circle-arrow-s" }
	}).click(function(){
		$("body").removeAttr("class");
	});
});

function verify()
{
	try
	{
		var ind_activo = '0';

		if( $("#nom_responID").val() == '' ){
			alert( 'Por favor digite un responsable.' );
			return false;
		}else{
			var r = confirm( 'Esta Seguro de Guardar el Registro?' );
			if( r == true ){
				if( $("#ind_activoID").attr('checked') )
					ind_activo = '1';

				saveRespon( $("#standaID").val(), $("#cod_responID").val(), $("#nom_responID").val(), ind_activo );
			}
			else
				return false;
		}

	}
	catch(e)
	{
		console.log( "Error Fuction verify: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function saveRespon( standar, cod_respon, nom_respon, ind_activo )
{
	try
	{
		var attributes  = 'Ajax=on&Option=saveRespon&standa='+standar;
			attributes += '&cod_respon='+ cod_respon;
			attributes += '&nom_respon='+ nom_respon;
			attributes += '&ind_activo='+ ind_activo;
			attributes += getDataFormulario();

		//Load PopUp
		LoadPopupJQNoButton('close');
		LoadPopupJQNoButton( 'open', 'Resultado de la Operación', 'auto', 'auto', false, false, true );
		var popup = $("#popID");
		$.ajax({
			url: "../"+ standar + "/seguridad/class_respon_respon.php",
			type: "POST", 
			data: attributes,
			async: false,
			beforeSend: function(){
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
			},
			success: function(data){
				popup.append(data);
			}
		});
	}
	catch(e)
	{
		console.log( "Error Fuction saveRespon: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function editRespon( opt, obj )
{
	try
	{
		if( opt != '2' )
		{
			var DLRow = $( obj ).parent().parent();
			var cod_respon = DLRow.find("input[id^=cod_respon]").val();
			var nom_respon = DLRow.find("input[id^=nom_respon]").val();
		}else{
			cod_respon='';
			nom_respon='';
		}

		if( opt == '0' || opt == '1' )
			saveRespon( $("#standaID").val(), cod_respon, nom_respon, opt );
		else
		{
			var fStandar   = $("#standaID");
			var attributes  = 'Ajax=on&Option=edicionRespon&standa='+fStandar.val();
				attributes += '&cod_servic='+ $("#cod_servicID").val();
				attributes += '&cod_respon='+ cod_respon;
				attributes += '&nom_respon='+ nom_respon;

			$.ajax({
				url: "../"+ fStandar.val() + "/seguridad/class_respon_respon.php",
				type: "POST", 
				data: attributes,
				async: false,
				beforeSend: function(){
					$("#sub_responID").html("<center>Cargando Formulario...</center>");
				},
				success: function(data){
					$("#sub_responID").html(data);
				},
				complete: function(){
					$("#secID").css({'height':'100%'});
				}
			});
		}
	}
	catch(e)
	{
		console.log( "Error Fuction editRespon: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function getDataFormulario()
{
	try{
		var box_checke = $("input[type=checkbox]:checked");
		var attributes = '';

		box_checke.each(function(i,o){
			if( $(this).attr("name") != 'ind_activo' ){
				attributes += '&'+ $(this).attr("name");
				attributes += '='+ $(this).val();
			}
		});

		if( attributes != '' )
			attributes += '&ind_editor=1';

		return attributes;
	}
	catch(e)
	{
		console.log( "Error Function getDataFormulario: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}