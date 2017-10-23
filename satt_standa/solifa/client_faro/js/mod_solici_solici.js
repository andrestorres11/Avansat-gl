function r(){
	if(typeof jQuery == "function"){

		var target=$(".ins_solici_solici");

		function load(){
			try{
				$( "#tabs" ).tabs();
				$("#tabs ul li a").bind("click",tab_load_content);
				$("#tabs ul li a")[0].click();

				$("#tabs #tabs-1 form .btn[name=cancel]").bind("click",function(){
					$("#tabs #tabs-1 form")[0].reset();
				});
				$("#tabs #tabs-1 form .btn[name=send]").bind("click",function(){
					console.log("Enviar > ");
					console.log($("#tabs #tabs-1 form").serializeArray());
				});
				$("#tabs #tabs-1 form .btn[name=add]").bind("click",function(){
					console.log("reply > ");
					var objReply = $("#tabs #tabs-1 form .form-group.reply");
					var objTarget = objReply.parent();
					if(objReply.length>0){
						objTarget.append('<div class="form-group nested col-xs-12 col-md-12">'+objReply[0].innerHTML+'</div>');
					}
					$("#tabs #tabs-1 form .form-group.nested span.hide").removeClass("hide");
					$("#tabs #tabs-1 form .form-group.nested .btn.remove").bind("click",function(){$(this).parent().parent().parent().remove();})
				});
			}catch(e){}
		}

		function tab_load_content(e){
			try{
				var id=$(this).attr("id");
				$("#tabs div form").hide();
				$("#tabs div form").hide();
				$("#tabs div form").hide();
				$("#tabs div form").hide();
				switch(id){
					case "ui-id-1":
						$("#tabs #tabs-1 form").show();
						checkBasicLoad("tab1");

						prevUpdateSelect($("#tabs-1 form select.control-destino"));
						prevUpdateSelect($("#tabs-1 form select.control-origen"));
						prevUpdateSelect($("#tabs-1 form select.control-via"));

						$.getJSON( "data/origen.json?_t="+Math.random(), function( data ) {
							updateSelect($("#tabs-1 form select.control-origen"),data,null);
							checkBasicLoad("tab1");
						});
						$.getJSON( "data/destino.json?_t="+Math.random(), function( data ) {
							updateSelect($("#tabs-1 form select.control-destino"),data,null);
							checkBasicLoad("tab1");
						});
						$.getJSON( "data/via.json?_t="+Math.random(), function( data ) {
							updateSelect($("#tabs-1 form select.control-via"),data,null);
							checkBasicLoad("tab1");
						});
						$.getJSON( "data/usuario.json?_t="+Math.random(), function( data ) {
							updateUser(data);
							checkBasicLoad("tab1");
						});

					break;
					case "ui-id-2":
						$("#tabs #tabs-2 form").show();
						$("#tabs #tabs-2 .loading.help-block").hide();
					break;
					case "ui-id-3":
						$("#tabs #tabs-3 form").show();
						$("#tabs #tabs-3 .loading.help-block").hide();
					break;
					case "ui-id-4":
						$("#tabs #tabs-4 form").show();
						$("#tabs #tabs-4 .loading.help-block").hide();
					break;
				}
			}catch(e){}
		}

		var check_load=[];
		function checkBasicLoad(ref){
			if(typeof check_load[changeStrToCodeAt(ref)] == "undefined"){
				check_load[changeStrToCodeAt(ref)]=0;
			}
			check_load[changeStrToCodeAt(ref)]++;
			switch(ref){
				case "tab1":
					if(check_load[changeStrToCodeAt(ref)]==5){
						$("#tabs #tabs-1 .loading.help-block").hide();
						check_load[changeStrToCodeAt(ref)]=0;
					}
				break;
			}
		}
		function changeStrToCodeAt(s){
			try{
				var sf="";
				var s=s.toString();
				for(var i=0;i<s.length;i++){
					sf+="" + s.charCodeAt(i);
				}
				return parseInt(sf);
			}catch(e){
				console.log(e);
				return -1;
			}
		}

		function updateUser(data){
			try{
				console.log("updateUser > ");
				console.log(data);
				for(var i=0;i<data.length;i++){
					var key=data[i].key;
					var value=data[i].value;
					if(key=="nombre")
						$("#inputName").val(value);
					if(key=="email")
						$("#inputEmail").val(value);
					if(key=="telefono")
						$("#inputTel").val(value);
					if(key=="celular")
						$("#inputCel").val(value);
				}
			}catch(e){}
		}



		function updateSelect(el,data,id){
			try{
				console.log("updateSelect > ");
				console.log(data);
				console.log(el);
				el.find("option").remove();
				el.append("<option value=''>Seleccione</option>");
				for(var i=0;i<data.length;i++){
					var selected=data[i].key==id && id!=null? "selected='selected'" : "";
					el.append("<option value='"+data[i].key+"' "+selected+">"+data[i].value+"</option>");
				}
			}catch(e){}
		}

		function prevUpdateSelect(el){
			try{
				el.find("option").remove();
				el.append("<option>Cargando...</option>");
			}catch(e){}
		}

		function renderJsonToHTML(obj,target,replace){
			console.log("renderJsonToHTML");
			if(typeof replace == "undefined")
				replace=0;
			if(typeof obj == "object" && obj.tag!=undefined){
					var el = document.createElement(obj.tag);
					el=$(el);
					el.html(obj.html);
					addProperties(el,obj);
					if(obj.children!=undefined){
						for(var i=0; i<obj.children.length; i++){
							renderJsonToHTML(obj.children[i],el);
						}
					}
					if(replace==0)
						target.append(el);
					else
						target.html(el);
			}else{
				if(typeof obj == "object" && obj.tag==undefined){
					for(var i=0; i<obj.length; i++){
						var obj2=obj[i];
						var el = document.createElement(obj2.tag);
						el=$(el);
						el.html(obj2.html);
						addProperties(el,obj2);
						if(obj2.children!=undefined){
							for(var i=0; i<obj2.children.length; i++){
								renderJsonToHTML(obj2.children[i],el);
							}
						}
						if(replace==0)
							target.append(el);
						else
							target.html(el);
					}
				}
			}
		}

		function addProperties(el,objProperties){
			for(var i in objProperties){
				switch(i){
					case "tag":
					case "html":
					case "children":
					break;
					case "source":
						if(objProperties[i].length>0){
							$.getJSON( objProperties[i]+"?_t="+Math.random(), function( data ) {
								console.log("load "+objProperties[i]);
								renderJsonToHTML(data,el,1);
								load();
							});
						}
					break;
					default:
						el.attr(i,objProperties[i]);
					break;
				}
			}
		}
		$.getJSON( "data/template/ins_solici_solici.json?_t="+Math.random(), function( data ) {
			console.log("load ins_solici_solici > ");
			renderJsonToHTML(data,target,1);
			load();
		});

		try{
			setTimeout(function(){
				if($("#tabs #tabs-1 .loading.help-block").css("display")=="block"){
					alert("su conexion presenta demoras, intente cargar nuevamente servicio");
				}
			},5000);
		}catch(e){}

	}else{
		var jslib=["https://code.jquery.com/jquery-1.12.4.js","https://code.jquery.com/ui/1.12.1/jquery-ui.js"];
		for(var a=0;a<jslib.length;a++){
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = jslib[a];
			document.getElementsByTagName('head')[0].appendChild(script);
		}
		setTimeout(function(){r();},1001);
	}
}
r();