function requireLibJs(){
	var req=false,jq=true,jqui=true;
	if(typeof jQuery == "undefined"){
		jq=false;
		jqui=false;
		req=true;
	}else{
		if(parseInt(jQuery.fn.jquery.split(".").join(""))>170 && parseInt(jQuery.fn.jquery.split(".").join(""))<1125){
		}else{
			jq=false;
			jqui=false;
			req=true;	
		}
	}
	//if($.fn.jquery<parseInt()){}
	if(typeof jQuery != "undefined"){
		if(typeof jQuery.ui == "undefined"){
			jqui=false;
			req=true;
		}
	}

	if(!jq){
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "https://code.jquery.com/jquery-1.12.4.js";
		document.getElementsByTagName('head')[0].appendChild(script);
	}
	if(jq && !jqui){
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "https://code.jquery.com/ui/1.12.1/jquery-ui.js";
		document.getElementsByTagName('head')[0].appendChild(script);
	}
	return req;
}

function prevRequireLibJs(){
	if(requireLibJs()==false){
		r();
	}else{
		setTimeout(prevRequireLibJs,1000);
	}
}


function r(){		
		var target=$(".inf_solici_solici");
		if(typeof ds == "undefined")
			ds = "";

		if(typeof dc == "undefined")
			dc = "";

		if(typeof cs == "undefined")
			cs = "";

		if(typeof wd == "undefined")
			wd = "";

		if(typeof ot == "undefined")
			ot = "";

		var server_req={"standa":ds,"central":dc,"cod_servic":cs,"window":wd,"option":ot};
		var loadCity=false;
		var loadLicensePlate=false;
		//console.log("server_req > ");
		//console.log(server_req);
		
		function load(){
			try{
				try{
					//se debe cargar usuario
					$.getJSON( server_req.standa+"inf_solici_solici.php?window="+server_req.window+"&option=99&cod_servic="+server_req.cod_servic+"&r="+Math.random(), function( data ) {
						updateUser(data);
						checkBasicLoad("tab1");
					});

					//se debe cargar transportadora
					resetSelect($("select[name=lis_transp]"));
					$.getJSON( server_req.standa+"inf_solici_solici.php?window="+server_req.window+"&option=93&cod_servic="+server_req.cod_servic+"&r="+Math.random(), function( data ) {
						updateSelect($("select[name=lis_transp]"),data,null);
						listFilter({"title":"Transportadora", "class":"form-control", "store": data,"el":$("select[name=lis_transp]"),"multiple":true});
						checkBasicLoad("tab1");
					});

					//se debe cargar fecha por defecto y se delega un calendario en caso de no soportarlo nativo
					$('input[type="date"]').each(function(k,v){
						forceDateLocal(this);
					});

				}catch(e){}

				$( "#tabs" ).tabs();
				//$("#tabs ul li a").unbind("click");
				$("#tabs ul li a").bind("click",tab_load_content);
				$("#tabs ul li a")[0].click();
				$("#ui-datepicker-div").css("display","none");
			}catch(e){}
		}
		function setWsData(tabid,objToSend){
			try{

				//console.log("setWsData > ");
				//console.log(tabid)
				//console.log(objToSend)

				var divActive=$("#tabs form").find('.alert.active');
				var currForm=$("#tabs "+tabid+" form");
				currForm.find('.alert').html("Enviando...");
				currForm.find('.alert.active.solici_emergente_alert').show("fade", 500);

				var ajax_url="";
				switch(tabid){
					case "#tabs-1":
					case "#tabs-2":
					case "#tabs-3":
					case "#tabs-4":
						ajax_url=server_req.standa+"inf_solici_solici.php?window="+server_req.window+"&option=50&cod_servic="+server_req.cod_servic+"&r="+Math.random();
					break;
				}
				$.ajax({
					method: "POST",
					type: "POST",
					url: ajax_url,
					dataType: "json",
					data: JSON.stringify(objToSend)
				})
				.done(processResult1);
			}catch(e){
				//console.log("setWsData > catch 1");
				//console.log(e);
			}
		}
		function processResult1(msg){
			try{
				//console.log("processResult1 > ");
				//console.log(msg);
				var divActive=$("#tabs form").find('.alert.active');
				var divActive2=$("#tabs form").find('.alert.active.solici_emergente_alert');
				if(divActive!=null){
					if(divActive2!=null){
						divActive2.show( "fade", 500 );
						setTimeout(function(){
							divActive2.hide( "fade", 500 );
						},5000);
					}
					divActive.parent().find(".btn[name=send]").attr("disabled","disabled");
					divActive.html("<h2>Respuesta</h2>");
					divActive.append("<ul>");
					var objResponse=typeof msg =="object" ? msg : JSON.parse(msg);
					divActive.append(objResponse.message);
					divActive.append("</ul>");
					setTimeout(function(){
						divActive.parent().find(".cfile").trigger("click");
					},50);
				}
			}catch(e){
				//console.log("processResult1 > catch 1");
				//console.log(e);
				var divActive=$("#tabs form").find('.alert.active');
				var divActive2=$("#tabs form").find('.alert.active.solici_emergente_alert');
				if(divActive!=null){
					if(divActive2!=null){
						divActive2.show( "fade", 500 );
						setTimeout(function(){
							divActive2.hide( "fade", 500 );
						},5000);
					}
					divActive.html("Ocurrio un error, intente nuevamente en unos minutos, si persiste el error, consulte con su administrador.");
					setTimeout(function(){
						divActive.parent().find(".cfile").trigger("click");
					},50);
				}
			}
		}
		function processWsResult(msg){
			try{
				var divActive=$("#tabs form").find('.alert.active');
				if(divActive!=null){
					divActive.parent().find(".btn[name=send]").attr("disabled","disabled");
					divActive.html("<h2>Respuesta</h2>");
					divActive.append("<ul>");
					var objResponse=JSON.parse(msg);
					for(a in objResponse){
						try{
							var currObjResp=objResponse[a];
							if(currObjResp.response==false){
								try{
									if(currObjResp.ws.get.error!=null){
										//alert(currObjResp.ws.get.error);
										divActive.append("<li>"+currObjResp.ws.get.error+"</li>");
									}
									if(currObjResp.ws.get.fault!=null){
										//alert(currObjResp.ws.get.fault);
										divActive.append("<li>"+currObjResp.ws.get.fault+"</li>");
									}
								}catch(e){
									//console.log("processResult1 >  > catch 3");
									//console.log(e);
									//alert("Error, The Web Service have any problem, contact your provider.");
									divActive.append("<li>Error, The Web Service have any problem, contact your provider.</li>");
								}
							}else{
								
								//alert(currObjResp.response);
								var y=currObjResp.response;
								if(typeof y == "object"){
									//divActive.append("<li>"+currObjResp.response+"</li>");
									for( a in y){
										divActive.append("<ul>"+a+"<li>"+y[a]+"</li><ul>");
									}
								}else{
									divActive.append("<li>"+currObjResp.response+"</li>");
								}
							}
						}catch(e){
							//console.log("processResult1 >  > catch 2");
							//console.log(e);
						}
					}
					divActive.append("</ul>");
					setTimeout(function(){
						divActive.parent().find(".cfile").trigger("click");
					},50);
				}
			}catch(e){
				//console.log("processResult1 > catch 1");
				//console.log(e);
				var divActive=$("#tabs form").find('.alert.active');
				if(divActive!=null)
					divActive.html(msg);
			}
		}
		function getErrorForm(form){
			var error=false;
			if(typeof form =="object"){
				form.find('select').each(function(e){
					if($(this).val()=="" && $(this).attr("required")=="required"){$(this).addClass("required");error=true;}
					else{$(this).removeClass("required");}
				})
				form.find('input[type=text]').each(function(e){
					if($(this).val()=="" && $(this).attr("required")=="required"){$(this).addClass("required");error=true;}
					else{$(this).removeClass("required");}
				})
				form.find('input[type=number]').each(function(e){
					if($(this).val()=="" && $(this).attr("required")=="required"){$(this).addClass("required");error=true;}
					else{$(this).removeClass("required");}
				})
				form.find('input[type=datetime-local]').each(function(e){
					if($(this).val()=="" && $(this).attr("required")=="required"){$(this).addClass("required");error=true;}
					else{$(this).removeClass("required");}
				})
				form.find('input[type=radio]').each(function(e){
					try{
						if(
							$(this).attr("required")=="required" && 
							$("input[type=radio][name="+this.name+"]:checked").length==0
						){
							$(this).addClass("required");$(this).parent().addClass("required");
							error=true;
						}else{
							$(this).removeClass("required");$(this).parent().removeClass("required");
						}
					}catch(e){
						//console.log(e);
					}
				})
				form.find('textarea').each(function(e){
					if($(this).val()=="" && $(this).attr("required")=="required"){$(this).addClass("required");error=true;}
					else{$(this).removeClass("required");}
				})
				form.find('file').each(function(e){
					if($(this).val()=="" && $(this).attr("required")=="required"){$(this).addClass("required");error=true;}
					else{$(this).removeClass("required");}
				})
			}else{
				error=true;
			}
			return error;
		}
		function getSoli(){
			var form=$("form.solici");
			if(!getErrorForm(form)){
				var data=form.serializeArray();
				var obj=[];
				var objs1={};
				for(var a in data){
					try{
						switch(data[a].name){
							case "nom_solici":
								objs1={};
								objs1.nom_solici=data[a].value;
							break;
							case "mai_solici":
								objs1.mai_solici=data[a].value;
							break;
							case "fij_solici":
								objs1.fij_solici=data[a].value;
								obj.push(objs1);
							break;
							case "cel_solici":
								objs1.cel_solici=data[a].value;
								obj.push(objs1);
							break;
						}
					}catch(e){}
				}
				return obj;
			}
			return [];
		}
		function getRuta(form){
			if(!getErrorForm(form)){
				var data=form.serializeArray();
				var obj=[];
				var objs1={};
				for(var a in data){
					try{
						switch(data[a].name){
							case "origen":
								objs1={};
								objs1.cod_ciuori=data[a].value;
							break;
							case "destino":
								objs1.cod_ciudes=data[a].value;
							break;
							case "via":
								objs1.nom_viaxxx=data[a].value;
								obj.push(objs1);
							break;
						}
					}catch(e){}
				}
				return obj;
			}
			return [];
		}

		function getSeguimEsp(form){
			if(!getErrorForm(form)){
				var data=form.serializeArray();
				var obj=[];
				var objs1={};
				for(var a in data){
					try{
						switch(data[a].name){
							case "ind_segesp":
								objs1={};
								objs1.ind_segesp=data[a].value;
							break;
							case "fec_iniseg":
								objs1.fec_iniseg=data[a].value;
							break;
							case "fec_finseg":
								objs1.fec_finseg=data[a].value;
							break;
							case "lis_placax":
								objs1.lis_placax=data[a].value;
							break;
							case "obs_solici":
								objs1.obs_solici=data[a].value;
								obj.push(objs1);
							break;
						}
					}catch(e){}
				}
				return obj;
			}
			return [];
		}
		
		function getPqr(form){
			//console.log("getPqr > ");
			//console.log(form);
			if(!getErrorForm(form)){
				var data=form.serializeArray();
				var obj=[];
				var objs1={};
				for(var a in data){
					try{
						switch(data[a].name){
							case "ind_pqrsxx":
								objs1={};
								objs1.ind_pqrsxx=data[a].value;
							break;
							case "nom_pqrsxx":
								objs1.nom_pqrsxx=data[a].value;
							break;
							case "obs_pqrsxx":
								objs1.obs_pqrsxx=data[a].value;
								obj.push(objs1);
							break;
							case "fil_archiv":
								obj[obj.length-1].fil_archiv=data[a].value;								
							break;
						}
					}catch(e){}
				}
				return obj;
			}
			return [];
		}

		function getOtrasSolici(form){
			if(!getErrorForm(form)){
				var data=form.serializeArray();
				var obj=[];
				var objs1={};
				for(var a in data){
					try{
						switch(data[a].name){
							case "nom_otroxx":
								objs1.nom_otroxx=data[a].value;
							break;
							case "obs_otroxx":
								objs1.obs_otroxx=data[a].value;
								obj.push(objs1);
							break;
							case "fil_archiv":
								obj[obj.length-1].fil_archiv=data[a].value;								
							break;
						}
					}catch(e){}
				}
				return obj;
			}
			return [];
		}

		function updateStatusTableDetail(currForm,currTab,id,filter1){
				currForm.find('.alert').html("Consultando detalle...");
				currForm.find('.alert').removeClass("hide");
				currForm.find('.alert').addClass("active");
				$.getJSON( filter1.url2, function( data ) {
					//console.log(id + " Detalle ");
					//console.log(data);
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
					currForm.find('.alert').removeClass("active");
					
					for(var i=0 in data){
						currTab.find("table.indicador-detallado")
						.append(
							"<tr>"+
							"<td>"+ data[i].fecha +"</td>"+
							"<td>"+ data[i].count +"</td>"+
							"<td>"+ ( data[i].estado3!=undefined && data[i].estado3!=null ? data[i].estado3 : 0 ) +"</td>"+
							"<td>"+ ( data[i].estado3!=undefined && data[i].estado3!=null && parseInt(data[i].estado3)>0 ? Math.round((parseInt(data[i].estado3)*100)/parseInt(data[i].count))+"%" : "0%" ) + "</td>"+
							"<td>"+ ( data[i].estado2!=undefined && data[i].estado2!=null ? data[i].estado2 : 0 ) +"</td>"+
							"<td>"+ ( data[i].estado2!=undefined && data[i].estado2!=null && parseInt(data[i].estado2)>0 ? Math.round((parseInt(data[i].estado2)*100)/parseInt(data[i].count))+"%" : "0%" ) + "</td>"+
							"<td>"+ ( data[i].estado1!=undefined && data[i].estado1!=null ? data[i].estado1 : 0 ) +"</td>"+
							"<td>"+ ( data[i].estado1!=undefined && data[i].estado1!=null && parseInt(data[i].estado1)>0 ? Math.round((parseInt(data[i].estado1)*100)/parseInt(data[i].count))+"%" : "0%" ) + "</td>"+
							"</tr>"
						);
					}
				})
				.fail(function( jqxhr, textStatus, error ) {
				    var err = textStatus + ", " + error;
				    //console.log(jqxhr);
				    //console.log(textStatus);
				    //console.log(error);
				    //console.log( "Request Failed: " + err );
				    currForm.find('.alert').html(jqxhr.responseText);
					currForm.find('.alert').removeClass("hide");
					currForm.find('.alert').addClass("active");
				})
				.always(function() {
					//console.log( "complete" );
				});
		}
		function updateStatusTable(currForm,currTab,id,filter1){
			try{
				currForm.show();
				currTab.find(".loading.help-block").hide();
				//limpair tablas
				currTab.find("table").parent().hide();
				currTab.find("table.indicador").parent().show();
				currTab.find("table.indicador").find("td").html("");
				currTab.find("table.indicador-detallado").find("td").parent().remove();
				currTab.find("table.indicador-detallado2").find("td").parent().remove();
				$.getJSON( filter1.url, function( data ) {
					//console.log(id);
					//console.log(data);
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
					currForm.find('.alert').removeClass("active");

					//si hay datos, entonces, se procede a buscar discriminado
					if(data!=undefined || data!=null || typeof data == "string"){
						//correr otra fn para url2
						updateStatusTableDetail(currForm,currTab,id,filter1);
						currTab.find("table.indicador-detallado").parent().show();
					}
					
					currTab.find("table.indicador").find("tr").each(function(i,v){
						if(i==0){
							var str="Indicador de solicitudes del periodo "+filter1.fec_inifil+" al "+filter1.fec_finfil+"";
							var td=$(this).find("th").html(str.toUpperCase());
						}
						if(data==null || typeof data == "string"){
							data={"count":0,"estado1":0,"estado2":0,"estado3":0};
						}
						if(i==2){
							var td=$(this).find("td");
							var rlt=data.count!=undefined && data.count!=null ? data.count : 0;
							td[0].innerHTML="<div data-ref=\"ui-id-1\" class=\"data link\">"+rlt+"</div>";
							var rlt=data.estado3!=undefined && data.estado3!=null ? data.estado3 : 0;
							td[1].innerHTML="<div data-ref=\"ui-id-2\" class=\"data link\">"+rlt+"</div>";
							var rlt=data.estado3!=undefined && data.estado3!=null && parseInt(data.estado3)>0 ? Math.round((parseInt(data.estado3)*100)/parseInt(data.count))+"%" : "0%";
							td[2].innerHTML="<div class=\"data\">"+rlt+"</div>";
							var rlt=data.estado2!=undefined && data.estado2!=null ? data.estado2 : 0;
							td[3].innerHTML="<div data-ref=\"ui-id-3\" class=\"data link\">"+rlt+"</div>";
							var rlt=data.estado2!=undefined && data.estado2!=null && parseInt(data.estado2)>0 ? Math.round((parseInt(data.estado2)*100)/parseInt(data.count))+"%" : "0%";
							td[4].innerHTML="<div class=\"data\">"+rlt+"</div>";
							var rlt=data.estado1!=undefined && data.estado1!=null ? data.estado1 : 0;
							td[5].innerHTML="<div data-ref=\"ui-id-4\" class=\"data link\">"+rlt+"</div>";
							var rlt=data.estado1!=undefined && data.estado1!=null && parseInt(data.estado1)>0 ? Math.round((parseInt(data.estado1)*100)/parseInt(data.count))+"%" : "0%";
							td[6].innerHTML="<div class=\"data\">"+rlt+"</div>";

							td.find("div.link").bind("click",function(e){
								try{
									var tabClick=$(e.currentTarget).attr("data-ref");
									$("a#"+tabClick).click();
								}catch(e){}
							});
						}
					});
				})
				.fail(function( jqxhr, textStatus, error ) {
				    var err = textStatus + ", " + error;
				    //console.log(jqxhr);
				    //console.log(textStatus);
				    //console.log(error);
				    //console.log( "Request Failed: " + err );
				    currForm.find('.alert').html(jqxhr.responseText);
					currForm.find('.alert').removeClass("hide");
					currForm.find('.alert').addClass("active");
				})
				.always(function() {
					//console.log( "complete" );
				});
			}catch(e){
				//console.log(e);
			}
		}
		function updateStatusTipoxTable(currForm,currTab,id,filter1){
			try{
				currForm.show();
				currTab.find(".loading.help-block").hide();
				//limpair tablas
				currTab.find("table").parent().hide();
				currTab.find("table.indicador").parent().show();
				currTab.find("table.indicador").find("td").html("");
				currTab.find("table.indicador-detallado").find("td").parent().remove();
				currTab.find("table.indicador-detallado2").find("td").parent().remove();

				$.getJSON( filter1.url, function( data ) {
					//console.log(id);
					//console.log(data);
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
					currForm.find('.alert').removeClass("active");

					//si hay datos, entonces, se procede a buscar discriminado
					if(data!=undefined || data!=null || typeof data == "string"){
						//correr otra fn para url2
						updateStatusTipoxTableDetail(currForm,currTab,id,filter1);
					}

					
					currTab.find("table.indicador").find("tr").each(function(i,v){
						if(i==0){
							//var str="Indicador de solicitudes del periodo "+data.set.fec_inifil+" al "+data.set.fec_finfil+"";
							var str="Indicador de solicitudes del periodo "+filter1.fec_inifil+" al "+filter1.fec_finfil+"";
							var td=$(this).find("th").html(str);
						}
						if(data==null || typeof data == "string"){
							data={"count":0,"tipsol1":0,"tipsol2":0,"tipsol3":0,"tipsol4":0};
						}
						if(i==2){
							var td=$(this).find("td");
							td[0].innerHTML=data.count!=undefined && data.count!=null ? '<div class="indicador-detallado-tercer-todos link">'+data.count+'</div>' : 0;
							td[1].innerHTML=data.tipsol1!=undefined && data.tipsol1!=null ? data.tipsol1 : 0;
							td[2].innerHTML=(data.tipsol1!=undefined && data.tipsol1!=null && parseInt(data.tipsol1)>0 ? Math.round((parseInt(data.tipsol1)*100)/parseInt(data.count)) : 0)+"%";
							td[3].innerHTML=data.tipsol2!=undefined && data.tipsol2!=null ? data.tipsol2 : 0;
							td[4].innerHTML=(data.tipsol2!=undefined && data.tipsol2!=null && parseInt(data.tipsol2)>0 ? Math.round((parseInt(data.tipsol2)*100)/parseInt(data.count)) : 0)+"%";
							td[5].innerHTML=data.tipsol3!=undefined && data.tipsol3!=null ? data.tipsol3 : 0;
							td[6].innerHTML=(data.tipsol3!=undefined && data.tipsol3!=null && parseInt(data.tipsol3)>0 ? Math.round((parseInt(data.tipsol3)*100)/parseInt(data.count)) : 0)+"%";
							td[7].innerHTML=data.tipsol4!=undefined && data.tipsol4!=null ? data.tipsol4 : 0;
							td[8].innerHTML=(data.tipsol4!=undefined && data.tipsol4!=null && parseInt(data.tipsol4)>0 ? Math.round((parseInt(data.tipsol4)*100)/parseInt(data.count)) : 0)+"%";
						}
					});

					currTab.find("div.indicador-detallado-tercer-todos").bind("click",function(){
						currTab.find("table").parent().hide();
						currTab.find("table.indicador-detallado2").find("td").parent().remove();
						currTab.find("table.indicador-detallado2").parent().show();
						updateStatusTipoxTableDetail2(currForm,currTab,id,filter1,filter1.lis_transp);
					});
				})
				.fail(function( jqxhr, textStatus, error ) {
				    var err = textStatus + ", " + error;
				    /*console.log(jqxhr.responseText);
				    console.log(textStatus);
				    console.log(error);*/
				    currForm.find('.alert').html(jqxhr.responseText);
					currForm.find('.alert').removeClass("hide");
					currForm.find('.alert').addClass("active");
				})
				.always(function() {
					//console.log( "complete" );
				});
			}catch(e){}  
		}
		function findTercer(v){
			try{
				console.log("findTercer > ");
				var tmp=$("select.control-lis_transp").find("option[value="+v+"]");
				if(tmp.length==0) 
					location.reload();
				else
					return tmp.length>0 ? tmp.text() : "";
			}catch(e){
				return v;
			}
		}
		function updateStatusTipoxTableDetail(currForm,currTab,id,filter1){
			try{
				currForm.find('.alert').html("Consultando detalle...");
				currForm.find('.alert').removeClass("hide");
				currForm.find('.alert').addClass("active");
				$.getJSON( filter1.url2, function( data ) {
					//console.log(id + " Detalle ");
					//console.log(data);
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
					currForm.find('.alert').removeClass("active");
					currTab.find("table").parent().show();
					currTab.find("table.indicador-detallado2").parent().hide();
					currTab.find("table.indicador-respuesta").parent().hide();					
					
					for(var i=0 in data){
						currTab.find("table.indicador-detallado")
						.append(
							"<tr data-tercer=\""+data[i].cod_transp+"\">"+
							"<td>"+ findTercer(data[i].cod_transp) +"</td>"+
							"<td><div class=\"indicador-detallado-tercer link\">"+ data[i].count +"</div></td>"+
							"<td>"+ ( data[i].tipsol1!=undefined && data[i].tipsol1!=null ? data[i].tipsol1 : 0 ) +"</td>"+
							"<td>"+ ( data[i].tipsol1!=undefined && data[i].tipsol1!=null && parseInt(data[i].tipsol1)>0 ? Math.round((parseInt(data[i].tipsol1)*100)/parseInt(data[i].count))+"%" : "0%" ) + "</td>"+
							"<td>"+ ( data[i].tipsol2!=undefined && data[i].tipsol2!=null ? data[i].tipsol2 : 0 ) +"</td>"+
							"<td>"+ ( data[i].tipsol2!=undefined && data[i].tipsol2!=null && parseInt(data[i].tipsol2)>0 ? Math.round((parseInt(data[i].tipsol2)*100)/parseInt(data[i].count))+"%" : "0%" ) + "</td>"+
							"<td>"+ ( data[i].tipsol3!=undefined && data[i].tipsol3!=null ? data[i].tipsol3 : 0 ) +"</td>"+
							"<td>"+ ( data[i].tipsol3!=undefined && data[i].tipsol3!=null && parseInt(data[i].tipsol3)>0 ? Math.round((parseInt(data[i].tipsol3)*100)/parseInt(data[i].count))+"%" : "0%" ) + "</td>"+
							"<td>"+ ( data[i].tipsol4!=undefined && data[i].tipsol4!=null ? data[i].tipsol4 : 0 ) +"</td>"+
							"<td>"+ ( data[i].tipsol4!=undefined && data[i].tipsol4!=null && parseInt(data[i].tipsol4)>0 ? Math.round((parseInt(data[i].tipsol4)*100)/parseInt(data[i].count))+"%" : "0%" ) + "</td>"+
							"</tr>"
						);
					}

					currTab.find("div.indicador-detallado-tercer").bind("click",function(){
						currTab.find("table").parent().hide();
						currTab.find("table.indicador-detallado2").find("td").parent().remove();
						currTab.find("table.indicador-detallado2").parent().show();
						updateStatusTipoxTableDetail2(currForm,currTab,id,filter1,$(this).parent().parent().attr("data-tercer"));
					});

				})
				.fail(function( jqxhr, textStatus, error ) {
				    var err = textStatus + ", " + error;
				    //console.log(jqxhr);
				    //console.log(textStatus);
				    //console.log(error);
				    //console.log( "Request Failed: " + err );
				    currForm.find('.alert').html(jqxhr.responseText);
					currForm.find('.alert').removeClass("hide");
					currForm.find('.alert').addClass("active");
				})
				.always(function() {
					//console.log( "complete" );
				});
			}catch(e){}  
		}
		function updateStatusTipoxTableDetail2(currForm,currTab,id,filter1,tercer){
			try{
				/*if(tercer==undefined || tercer==null || tercer==""){
					currForm.find('.alert').html("El dato transportadora es inv&aacute;lido");
					return;
				}*/
				//console.log("Filtro de tercer > ");
				//console.log(tercer);
				if(tercer==undefined || tercer==null){
					tercer="";
				}
				currForm.find('.alert').html("Consultando detalle 2...");
				currForm.find('.alert').removeClass("hide");
				currForm.find('.alert').addClass("active");

				$.getJSON( updateUrl(filter1.url3,"lis_transp",tercer), function( data ) {
					//console.log(id + " Detalle ");
					//console.log(data);
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
					currForm.find('.alert').removeClass("active");
					
					for(var i=0 in data){
						currTab.find("table.indicador-detallado2")
						.append(
							"<tr data-solici=\""+data[i].num_solici+"\">"+
							"<td><div class=\"indicador-detallado-solici link\">"+ data[i].num_solici +"</div></td>"+
							"<td>"+ data[i].nom_tipsol +"</td>"+
							"<td>"+ findTercer(data[i].cod_transp) +"</td>"+
							"<td>"+ data[i].det_solici +"</td>"+
							"<td>"+ data[i].fec_creaci +"</td>"+
							"<td>"+ data[i].fec_modifi +"</td>"+
							"<td>"+ data[i].fec_difere +"</td>"+
							"<td data-native-value=\""+data[i].cod_estado+"\">"+ data[i].nom_estado +"</td>"+
							"<td>"+ data[i].usr_creaci+"</td>"+
							"</tr>"
						);
					}

					currTab.find("div.indicador-detallado-solici").bind("click",function(){
						currTab.find("table").parent().hide();
						//currTab.find("table.indicador-respuesta").find("td").parent().remove();
						currTab.find("table.indicador-respuesta").parent().show();
						currTab.find("table.indicador-respuesta").parent().show();
						currTab.find("table.indicador-respuesta .bitacora").parent().show();
						currTab.find("table.indicador-respuesta .bitacora .new").remove();
						currTab.find("table.indicador-respuesta").parent().addClass("solici_emergente");
						currForm.find('.alert').html("");
						currForm.find('.alert').removeClass("hide");
						currForm.find('.alert').addClass("active");
						currForm.find('.alert.active').addClass("solici_emergente_alert");
						//currTab.find("table.indicador-respuesta").find("table").parent().show();
						
						//currTab.find("table.indicador-respuesta").find("table").show();
						updateStatusTipoxTableDetail3(currForm,currTab,id,filter1,$(this).parent().parent().attr("data-solici"));
					});
				})
				.fail(function( jqxhr, textStatus, error ) {
				    var err = textStatus + ", " + error;
				    //console.log(jqxhr);
				    //console.log(textStatus);
				    //console.log(error);
				    //console.log( "Request Failed: " + err );
				    currForm.find('.alert').html(jqxhr.responseText);
					currForm.find('.alert').removeClass("hide");
					currForm.find('.alert').addClass("active");
				})
				.always(function() {
					//console.log( "complete" );
				});
			}catch(e){}  
		}

		//en este punto, la idea es actualizar el registro, y permitir, volver a ejecutar updateStatusTipoxTableDetail2 para actualizar
		function updateStatusTipoxTableDetail3(currForm,currTab,id,filter1,num_solici){
			try{
				if(num_solici==undefined || num_solici==null || num_solici==""){
					currForm.find('.alert').html("El dato transportadora es inv&aacute;lido");
					return;
				}
				currForm.find('.alert').html("Consultando detalle 3...");
				currForm.find('.alert').removeClass("hide");
				currForm.find('.alert').addClass("active");

				$.getJSON( updateUrl(filter1.url4,"num_solici",num_solici), function( data ) {
					//console.log(id + " Detalle Solicitud ");
					//console.log(data);
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
					currForm.find('.alert').removeClass("active");


					var currTable = currTab.find("table.indicador-respuesta");

					//limpiar resgistro x si akas
					currTable.find('textarea.obs_seguim').val("");

					//ingresar info del tercero y su solicitud
					currTable.find('.data.num_solici').html(data.solici.num_solici);
					currTable.find('.data.nom_tipsol').html((data.solici.nom_tipsol).toUpperCase());
					currTable.find('.data.nom_transp').html(("<B>Cliente "+findTercer(data.solici.cod_transp)+"</B>").toUpperCase());
					currTable.find('.data.nom_usrsol').val(data.solici.nom_usrsol);
					currTable.find('.data.dir_usrmai').val(data.solici.dir_usrmai);
					currTable.find('.data.num_usrfij').val(data.solici.num_usrfij);
					currTable.find('.data.num_usrcel').val(data.solici.num_usrcel);
					currTable.find('.data.fec_creaci').val(data.solici.fec_creaci);
					currTable.find('.data.nom_estado').val(data.solici.nom_estado);
					
					currTable.find('.data.det_solici').val(data.solici.det_solici);
					
					if(data.solici.cod_estado==3){
						currTable.find('.data.cod_estado.cerrar').attr("checked","checked");
					}else{
						currTable.find('.data.cod_estado.cerrar').removeAttr("checked");
					}

					if(data.solici.dir_archiv.length>0){
						currTable.find('.data.dir_archiv').html('<a href="'+data.solici.dir_archiv+'" target="_blank">Ver archivo</a>');
						currTable.find('.data.dir_archiv').show();
					}else{
						currTable.find('.data.dir_archiv').html("");
						currTable.find('.data.dir_archiv').hide();
					}

					//si ya cambio de estado de abierto, debe traer los seguimientos
					if(data.seguim.length>0){
						for(var uid_seguim in data.seguim){
							currTable.find(".bitacora").append(
								"<tr class=\"new\"><td>"+data.seguim[uid_seguim].obs_seguim+
								"</td><td>"+data.seguim[uid_seguim].nom_estado+
								"</td><td>"+data.seguim[uid_seguim].fec_creaci+
								"</td><td>"+data.seguim[uid_seguim].nom_estado+
								"</td></tr>");
						}
					}
				})
				.fail(function( jqxhr, textStatus, error ) {
				    var err = textStatus + ", " + error;
				    //console.log(jqxhr);
				    //console.log(textStatus);
				    //console.log(error);
				    //console.log( "Request Failed: " + err );
				    currForm.find('.alert').html(jqxhr.responseText);
					currForm.find('.alert').removeClass("hide");
					currForm.find('.alert').addClass("active");
				})
				.always(function() {
					//console.log( "complete" );
				});
			}catch(e){}  
		}


		function updateUrl(url,search,value){
			try{
				if(url.indexOf(search)>-1){
					var p1=url.indexOf(search);
					var u1=url.substr(0,p1);
					var u2=url.substr(p1,url.length-p1);
				}
				if(u2.indexOf("&")>-1){
					var p2=u2.indexOf("&")+1;
					var u3=u2.substr(p2,u2.length-p2);
				}else{
					var u3=u2;
				}
				return u1+""+u3+"&"+search+"="+value;
			}catch(e){
				return url;
			}
		}
		function tab_load_content(e){
			try{
				var id=$(this).attr("id");
				var __id=id.split("-");
				var currTabStr="#tabs-"+__id[2];
				var currTab=$(currTabStr);
				var currForm=$("#tabs "+currTabStr+" form");
				var objReply = currForm.find(".form-group.reply");
				var objReplyParent = objReply.parent();

				$("#tabs form").hide();
				$("#tabs form").find('.alert').html("");
				$("#tabs form").find('.alert').addClass("hide");

				currForm.find(".btn[name=cancel]").unbind("click");
				currForm.find(".btn[name=cancel]").bind("click",function(e){onCancel(currForm,currTabStr);});

				currForm.find(".btn[name=send]").unbind("click");
				currForm.find(".btn[name=send]").bind("click",function(e){onSend(currForm,currTabStr);});

				var optionxtab=[[0,5,8,11,14],[0,6,9,12,15],[0,7,10,13,16],[0,17,17,17,17]];

				currForm.show();
				currTab.find(".loading.help-block").hide();
				
				var url=server_req.standa+"inf_solici_solici.php?window="+server_req.window+"&cod_servic="+server_req.cod_servic+ "&r="+Math.random();
				var lis_transp=$("form.solici").find("select[name=lis_transp]").val();
				var fec_inifil=$("form.solici").find("input[name=fec_inifil]").val();
				var fec_finfil=$("form.solici").find("input[name=fec_finfil]").val();
				var num_solici=$("form.solici").find("input[name=num_solici]").val();
				var filter1={"lis_transp":lis_transp,"fec_inifil":fec_inifil,"fec_finfil":fec_finfil,"num_solici":num_solici};
				
				//if(lis_transp.length>0){
				if(filter1.lis_transp!=null){
					url+="&lis_transp="+filter1.lis_transp;
				}
				if(filter1.fec_inifil.length>0){
					url+="&fec_inifil="+filter1.fec_inifil;
				}
				if(filter1.fec_finfil.length>0){
					url+="&fec_finfil="+filter1.fec_finfil;	
				}
				if(filter1.num_solici.length>0){
					url+="&num_solici="+filter1.num_solici;
				}
				if(optionxtab[3].length>0 && optionxtab[3][parseInt(id.split("-")[2])]!=undefined){
					url4=url+"&option="+optionxtab[3][parseInt(id.split("-")[2])];
					if(filter1.num_solici.length==0){
						url4+="&num_solici=0";
					}
				}
				if(optionxtab[2].length>0 && optionxtab[2][parseInt(id.split("-")[2])]!=undefined){
					url3=url+"&option="+optionxtab[2][parseInt(id.split("-")[2])];
				}
				if(optionxtab[1].length>0 && optionxtab[1][parseInt(id.split("-")[2])]!=undefined){
					url2=url+"&option="+optionxtab[1][parseInt(id.split("-")[2])];
				}
				if(optionxtab[0].length>0 && optionxtab[0][parseInt(id.split("-")[2])]!=undefined){
					url+="&option="+optionxtab[0][parseInt(id.split("-")[2])];
				}
				filter1.url=url;
				filter1.url2=url2;
				filter1.url3=url3;
				filter1.url4=url4;

				$("#tabs form").find('.alert').removeClass("active");//previo se elimina el acceso al div de forma general
				//var currForm=$("#tabs "+tabid+" form");
				currForm.find('.alert').addClass("active");//permite por la etiqueta clase acceder al div de la alerta activa
				currForm.find('.alert').removeClass("hide");
				currForm.find('.alert').html("Consultando....");

				//uselo para personalizar eventos
				switch(id){
					case "ui-id-1":
						updateStatusTable(currForm,currTab,id,filter1);
						currTab.find("input[type=file]").bind('change', prepareUpload);
					break;
					case "ui-id-2":
					case "ui-id-3":
					case "ui-id-4":
						updateStatusTipoxTable(currForm,currTab,id,filter1);
						currTab.find("input[type=file]").bind('change', prepareUpload);
					break;
				}
				
				try{
					$("#tabs").find(".excel").remove();
					var excel=$('<div class="excel"></div>');
					//excel.css("background","url("+xls+")");
					excel.bind("click",function(id,el){
						getHtmlTable(__id[2],currTab);
					});
					$("#tabs").children("ul").before(excel);					
				}catch(e){}
			}catch(e){
				//console.log(e);
			}
		}
		//datetime-local
		function forceDateLocal(obj){
			try{
				//if(navigator.userAgent.indexOf("WebKit")==-1){
					var randomId="data-source-"+parseInt(new Date().getMilliseconds()+parseInt(Math.random()*1000))+1;
					$(obj).attr("data-id",randomId);
					var setDate = $(obj).attr("data-value")!=undefined ? $(obj).attr("data-value") : null;
					try{
						$(obj).attr("type","hidden");
					}catch(e){
						obj.type="hidden";
					}
					$(obj).val(toShortDateString());
					$(obj).addClass("date-local");
					$(obj).parent().append('<input data-parent="'+randomId+'" required="required" type="text" class="form-control setdate" value="'+toShortDateString()+'" placeholder="yyyy-mm-dd">');
					
					$(obj).parent().find('.form-control.setdate')
					.datepicker(
						{ 
							monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noveembre", "Diciember" ],
							dateFormat: 'yy-mm-dd', 
							maxDate: new Date()
						}
					)
					.bind("change",function(v){
						//console.log("change date > ");
						var cv=tdtl(toShortDateString(),$(this).val(),null,null,"0000-00-00");
						$(this).val(cv);
						var parent=$(this).attr("data-parent");
						var prevDate=$("input[data-id="+parent+"]").val();
						$("input[data-id="+parent+"]").val(tdtl(prevDate,cv,null,null,"0000-00-00"));
						//console.log($("input[data-id="+parent+"]").val());
					});
					if(setDate!=null)
						$(obj).parent().find('.form-control.setdate').datepicker( "setDate", setDate);
					$(obj).parent().find('.form-control.setdate').trigger("change");
					
				//}
			}catch(e){}
		}
		function forceDateTimeLocal(obj){
			try{
				//if(navigator.userAgent.indexOf("WebKit")==-1){
					var randomId="data-source-"+parseInt(new Date().getMilliseconds()+parseInt(Math.random()*1000))+1;
					$(obj).attr("data-id",randomId);
					try{
						$(obj).attr("type","hidden");
					}catch(e){
						obj.type="hidden";
					}
					$(obj).val(toShortDateString()+" 00:00");
					$(obj).addClass("datetime-local");
					$(obj).parent().append('<input data-parent="'+randomId+'" required="required" type="text" class="form-control setdate" value="'+toShortDateString()+'" placeholder="yyyy-mm-dd">');
					$(obj).parent().append('<input data-parent="'+randomId+'" style="width:60px;" required="required" type="number" class="form-control settime hour" min="0" max="23" step="1" value="0" />');
					$(obj).parent().append(':<input data-parent="'+randomId+'" style="width:60px;" required="required" type="number" class="form-control settime minute" min="0" max="59" step="1" value="0" />');

					$(obj).parent().find('.form-control.setdate').datepicker({ dateFormat: 'yy-mm-dd' }).bind("change",function(v){
						//console.log("change date > ");
						var cv=tdtl(toShortDateString(),$(this).val(),null,null,"0000-00-00");
						$(this).val(cv);
						var parent=$(this).attr("data-parent");
						var prevDate=$("input[data-id="+parent+"]").val();
						$("input[data-id="+parent+"]").val(tdtl(prevDate,cv,null,null,"0000-00-00 00:00"));
						//console.log($("input[data-id="+parent+"]").val());
					});

					$(obj).parent().find('.form-control.settime.hour').bind("change",function(v){
						//console.log("change hour > ");
						var cv=$(this).val();
						if(cv>=0 && cv<=23){
							var parent=$(this).attr("data-parent");
							var prevDate=$("input[data-id="+parent+"]").val();
							$("input[data-id="+parent+"]").val(tdtl(prevDate,null,cv,null,"0000-00-00 00:00"));
							//console.log($("input[data-id="+parent+"]").val());
						}else{
							$(this).val(0);
						}
					});

					$(obj).parent().find('.form-control.settime.minute').bind("change",function(v){
						//console.log("change minute > ");
						var cv=$(this).val();
						if(cv>=0 && cv<=59){
							var parent=$(this).attr("data-parent");
							var prevDate=$("input[data-id="+parent+"]").val();
							$("input[data-id="+parent+"]").val(tdtl(prevDate,null,null,cv,"0000-00-00 00:00"));
							//console.log($("input[data-id="+parent+"]").val());
						}else{
							$(this).val(0);
						}
					});
				//}
			}catch(e){}
		}

		function toShortDateString(){
			var cds=new Date();
			return scds=cds.getFullYear() + "-" + ( (cds.getMonth()+1)<10 ? "0"+(cds.getMonth()+1) : (cds.getMonth()+1) ) + "-" + ( (cds.getDate()<10) ? "0"+cds.getDate() : cds.getDate() );
		}
		function tdtl(pd,d,h,m,f){var pd=pd==undefined||pd==null?"0000-00-00":pd,f=f==undefined||f==null?"yyyy-mm-dd hh:ii":f,i=0,c=[],df="1969-01-01 00:00",dfe=df.split("-");try{if(d.length>0){if(d.length==10){var x=d.split("-");if(x.length==3){if(parseInt(x[0])>999&&parseInt(x[0])<10000){}else{x[0]=dfe[0];}if(parseInt(x[1])>0&&parseInt(x[1])<13){}else{x[1]=dfe[1];}if(parseInt(x[2])>0&&parseInt(x[2])<32){}else{x[2]=dfe[2];}d=x.join("-");}else{d=df}}else{d=df;}}}catch(e){}while(i<f.length){switch(i){case 0:case 1:case 2:case 3:case 5:case 6:case 8:case 9:if(d!=undefined){if(d[i]!=undefined&& !isNaN(parseInt(d[i]))){c.push(parseInt(d[i]));}else{c.push(0);}}else{if(pd[i]!=undefined&&!isNaN(parseInt(pd[i]))){c.push(parseInt(pd[i]));}else{c.push(0);}}break;case 11:if(h!=undefined){if(h<10){c.push(0);}else{c.push(h);}}else{if(pd[i]!=undefined&&!isNaN(parseInt(pd[i]))){c.push(parseInt(pd[i]));}else{c.push(0);}}break;case 12:if(h!=undefined){if(h<10){c.push(h);}}else{if(pd[i]!=undefined&&!isNaN(parseInt(pd[i]))){c.push(parseInt(pd[i]));}else{c.push(0);}}break;case 14:if(m!=undefined){if(m<10){c.push(0);}else{c.push(m);}}else{if(pd[i]!=undefined&&!isNaN(parseInt(pd[i]))){c.push(parseInt(pd[i]));}else{c.push(0);}}break;case 15:if(m!=undefined){if(m<10){c.push(m);}}else{if(pd[i]!=undefined&&!isNaN(parseInt(pd[i]))){c.push(parseInt(pd[i]));}else{c.push(0);}}break;case 4:case 7:case 10:case 13:case 16:c.push(f[i]);break;default:if(pd[i]!=undefined&&!isNaN(parseInt(pd[i]))){c.push(parseInt(pd[i]));}else{c.push(0);}break;}i++;}return c.join("");}
		
		function onAddRuta(currForm,tabid,objReply,objReplyParent){
			try{
				//console.log("reply > ");
				//console.log("tabid > ");
				//console.log(tabid);
				//var currForm=$("#tabs "+tabid+" form");
				if(objReply.length>0){
					objReplyParent.append('<div class="form-group nested col-xs-12 col-md-12">'+objReply[0].innerHTML+'</div>');
				}
				currForm.find(".form-group.nested span.hide").removeClass("hide");
				currForm.find(".form-group.nested .btn.remove").bind("click",function(){$(this).parent().parent().parent().remove();})
			}catch(e){
				//console.log("onAdd > error > ");
				//console.log(e);
				//console.log("tabid > "+tabid);
			}
		}
		function onCancel(currForm,tabid){
			try{
					//console.log("onCancel > tabid > ");
					//console.log(tabid);
					//nuevo, para el formulario de respuesta
					$(".data").html("&nbsp;");
					//var currForm=$("#tabs "+tabid+" form");
					currForm.each(function(e){
						this.reset();
					});
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
					currForm.find('.alert').removeClass("active");
					currForm.find('select').each(function(e){
						$(this).removeClass("required")
					});
					currForm.find('input[type=text]').each(function(e){
						$(this).removeClass("required")
					});
					currForm.find('input[type=number]').each(function(e){
						$(this).removeClass("required")
					});
					currForm.find('input[type=hidden]').each(function(e){
						$(this).removeClass("required")
					});
					currForm.find('input[type=datetime-local]').each(function(e){
						$(this).removeClass("required")
					});
					currForm.find('input[type=radio]').each(function(e){
						$(this).removeClass("required");$(this).parent().removeClass("required");
					});
					currForm.find('textarea').each(function(e){
						$(this).removeClass("required")
					});
					currForm.find('file').each(function(e){
						$(this).removeClass("required")
					});
					currForm.find(".btn[name=send]").removeAttr("disabled");

					currForm.find(".loading.help-block").hide();
					//limpair tablas
					currForm.find("table").parent().hide();
					currForm.find("table.indicador").parent().show();
					currForm.find("table.indicador-detallado").parent().show();
					currForm.find("table.indicador-detallado2").parent().hide();
					currForm.find("table.indicador-respuesta").parent().hide();
					currForm.find("table.indicador-respuesta").parent().removeClass("solici_emergente");
					currForm.find('.alert.active').removeClass("solici_emergente_alert");
					try{
						currForm.find(".filterform li").click();
					}catch(e){}
			}catch(e){
				//console.log("onCancel > error > ");
				//console.log(e);
				//console.log("tabid > "+tabid);
			}
		}
		function onSend(currForm,tabid){
			try{
				//console.log("onSend > tabid > ");
				//console.log(tabid);
				$("#tabs form").find('.alert').removeClass("active");//previo se elimina el acceso al div de forma general
				//var currForm=$("#tabs "+tabid+" form");
				currForm.find('.alert').addClass("active");//permite por la etiqueta clase acceder al div de la alerta activa
				currForm.find('.alert').removeClass("hide");
				currForm.find('.alert').html("");
				var objSend={};
				objSend.num_solici=currForm.find('.data.num_solici').html();
				objSend.obs_seguim=currForm.find("textarea[name=obs_seguim]")!=undefined && currForm.find("textarea[name=obs_seguim]")!=null ? currForm.find("textarea[name=obs_seguim]").val() : "";
				objSend.dir_archiv=currForm.find("input[name=dir_archiv]")!=undefined && currForm.find("input[name=dir_archiv]")!=null ? currForm.find("input[name=dir_archiv]").val() : "";
				objSend.cod_estado=currForm.find("input[name=cod_estado]").attr("checked")!=undefined ? 3 : 2;

				var error=false;
				if(objSend.obs_seguim == "" && objSend.cod_estado==2){
					error=true;
				}

				if(!error){
					setWsData(tabid,objSend);
				}else{
					currForm.find('.alert').html("Informaci&oacute;n incompleta, verif&iacute;que e intente nuevamente")
					currForm.find('.alert.active.solici_emergente_alert').show("fade", 500);
					setTimeout(function(){
						currForm.find('.alert.active.solici_emergente_alert').hide( "fade", 500 );
					},2500);
				}
			}catch(e){
				//console.log("onSend > error > ");
				//console.log(e);
				//console.log("tabid > "+tabid);
			}
		}
		function onSendOld(currForm,tabid){
			try{
				//console.log("onSend > tabid > ");
				//console.log(tabid);
				$("#tabs form").find('.alert').removeClass("active");//previo se elimina el acceso al div de forma general
				//var currForm=$("#tabs "+tabid+" form");
				currForm.find('.alert').addClass("active");//permite por la etiqueta clase acceder al div de la alerta activa
				currForm.find('.alert').removeClass("hide");
				currForm.find('.alert').html("");
				var arraySoli=getSoli();
				var objSend={};
				objSend.solici=arraySoli.length>0 ? arraySoli[0] : null;
				
				switch(tabid){
					case "#tabs-1":
						var arrayInput=getRuta(currForm);
						if(arrayInput.length>0){
							objSend.rutaxx=arrayInput;
						}
					break;
					case "#tabs-2":
						var arrayInput=getSeguimEsp(currForm);
						if(arrayInput.length>0){
							objSend=arrayInput[0];
							objSend.solici=arraySoli[0];
							if(typeof objSend.lis_placax == "undefined" || parseInt(objSend.ind_segesp) == 1){
								objSend.lis_placax="";
							}
						}
					break;
					case "#tabs-3":
						var arrayInput=getPqr(currForm);
						//console.log("#tabs-3 > arrayInput > ");
						//console.log(arrayInput);
						if(arrayInput.length>0){
							objSend=arrayInput[0];
							objSend.solici=arraySoli[0];
							if(typeof objSend.fil_archiv == "undefined"){
								objSend.fil_archiv="";
							}
						}
					break;
					case "#tabs-4":
						var arrayInput=getOtrasSolici(currForm);
						if(arrayInput.length>0){
							objSend=arrayInput[0];
							objSend.solici=arraySoli[0];
							if(typeof objSend.fil_archiv == "undefined"){
								objSend.fil_archiv="";
							}
						}
					break;
				}

				var error=arrayInput.length==0 || arraySoli.length==0;
				if(!error){
					setWsData(tabid,objSend);
				}else{
					currForm.find('.alert').html("Informaci&oacute;n incompleta, verif&iacute;que e intente nuevamente")
					currForm.find('.alert.active.solici_emergente_alert').show("fade", 500);
					setTimeout(function(){
						currForm.find('.alert.active.solici_emergente_alert').hide( "fade", 500 );
					},2500);
				}
			}catch(e){
				//console.log("onSend > error > ");
				//console.log(e);
				//console.log("tabid > "+tabid);
			}
		}

		var check_load=[];
		function checkBasicLoad(ref){
			if(typeof check_load[changeStrToCodeAt(ref)] == "undefined"){
				check_load[changeStrToCodeAt(ref)]=0;
			}
			check_load[changeStrToCodeAt(ref)]++;
			switch(ref){
				case "tab1":
					if(check_load[changeStrToCodeAt(ref)]==2){
						$("#tabs #tabs-1 .loading.help-block").hide();
						check_load[changeStrToCodeAt(ref)]=0;
					}
				break;
				case "source":
					if(check_load[changeStrToCodeAt(ref)]==8){
						load();
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
				//console.log(e);
				return -1;
			}
		}

		function updateUser(data){
			try{
				//console.log("updateUser > ");
				//console.log(data);
				for(var i=0;i<data.length;i++){
					var key=data[i].key;
					var value=data[i].value;
					if(key=="nom_solici")
						$("#nom_solici").val(value);
					if(key=="mai_solici")
						$("#mai_solici").val(value);
					if(key=="fij_solici")
						$("#fij_solici").val(value);
					if(key=="cel_solici")
						$("#cel_solici").val(value);					
				}
			}catch(e){}
		}



		function updateSelect(el,data,id){
			try{
				//console.log("updateSelect > ");
				//console.log(data);
				//console.log(el);
				el.find("option").remove();
				el.append("<option value=''>Seleccione</option>");
				for(var i=0;i<data.length;i++){
					var selected=data[i].key==id && id!=null? "selected='selected'" : "";
					el.append("<option value='"+data[i].key+"' "+selected+">"+data[i].value+"</option>");
				}
			}catch(e){}
		}

		function resetSelect(el){
			try{
				el.find("option").remove();
				el.append('<option value="">Cargando...</option>');
			}catch(e){}
		}

		function renderJsonToHTML(obj,target,replace){
			//console.log("renderJsonToHTML");
			if(typeof replace == "undefined")
				replace=0;
			if(typeof obj == "object" && obj.tag!=undefined){
					var el = document.createElement(obj.tag);
					el=$(el);
					el.html(obj.html && obj.html != '&nbsp;'?obj.html.toUpperCase():obj.html);
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
						el.html(obj2.html && obj.html != '&nbsp;'?obj2.html.toUpperCase():obj2.html);
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
							$.getJSON( server_req.standa + objProperties[i]+"?_t="+Math.random(), function( data ) {
								//console.log("load "+objProperties[i]);
								renderJsonToHTML(data,el,1);
								checkBasicLoad("source");
							});
						}
					break;
					default:
						el.attr(i,objProperties[i]);
					break;
				}
			}
		}

		function uploadFiles(event,file,input,span)
		{
		  try{
		    //console.log("uploadFiles > ");
		    event.stopPropagation();
		    event.preventDefault();
		    
		    var data = new FormData();
		    $.each(files, function(key, value)
		    {
		        data.append(key, value);
		    });

		    $.ajax({
		    	url: server_req.standa+"inf_solici_solici.php?window="+server_req.window+"&option=96&cod_servic="+server_req.cod_servic+"&r="+Math.random(),
		        type: 'POST',
		        data: data,
		        cache: false,
		        dataType: 'json',
		        processData: false, // Don't process the files
		        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		        success: function(data, textStatus, jqXHR){uploadFilesSuccess(data,file,input,span);},
		        error: function(jqXHR, textStatus, errorThrown){uploadFilesError(file,input,span);}
		    });
		  }catch(e){
		    //console.log(e);
		  }
		}
		
		function uploadFilesError(file,input,span){
        	try{
        		file.show();
	        	file.val("");
	        	input.val("");
	        	//span.html('Error to upload file, please, try upload later.');
	        	span.html('Error al subir el archivo, intente m&aacute;s tarde.');
	        }catch(e){
        		//console.log("Error uploadFiles > error > ");
        		//console.log(e);
        	}
        }
		
		function uploadFilesSuccess(data,file,input,span){
        	try{
	        	if(data.url!=""){
	        		file.unbind('change');
		        	file.val("");
		        	file.hide();
		        	file.parent().find(".help-block").hide();
		        	file.bind('change', prepareUpload);
		        	input.val(data.url);
		        	span.html(data.message);
		        	//span.append('<br><br><a class="cfile" href="#">If you wants change uploaded file, press your mouse in the current link</a>');
		        	span.append('<br><br><a class="cfile" href="#">Si desea cambiar el archivo, presione este enlace</a>');
		        	span.find(".cfile").bind("click",function(){
		        		removeFile(input.val());
		        		file.show();
		        		file.parent().find(".help-block").show();
		        		input.val("");
		        		span.html("");
		        	});
	        	}else{
	        		input.val(data.url);
	        		span.html(data.message);
	        	}
        	}catch(e){
        		//console.log("Error uploadFiles > success > ");
        		//console.log(e);
        	}
        }

		function removeFile(hash){
			try{
				//console.log("removeFile > ");
				if(typeof hash!="undefined" && typeof hash!=null && typeof hash!=""){
					$.ajax({
				    	url: server_req.standa+"inf_solici_solici.php?window="+server_req.window+"&option=95&cod_servic="+server_req.cod_servic+"&r="+Math.random(),
				        type: 'POST',
				        data: '{"hash":"'+hash+'"}',
				        cache: false,
				        dataType: 'json',
				        success: function(data, textStatus, jqXHR){
				        	//console.log("removeFile > success > ");
				        	//console.log(data);
				        },
				        error: function(jqXHR, textStatus, errorThrown){
				        	//console.log("removeFile > error > ");
				        	//console.log(jqXHR);
				        }
	        		});
        		}
			}catch(e){}
		}

		function prepareUpload(event)
		{
		  try{
		    //console.log("prepareUpload > ");
		    files = event.target.files;
		    if(files!=null){
		    	//console.log("prepareUpload > uploadFiles > ");
		    	if($(this).parent().find('input[type=hidden]').length==0){
		    		$(this).parent().append('<input name="'+this.name+'" type="hidden">');
		    		$(this).parent().append('<span class="testing">Checking...</span>');
		    		this.name="";
		    	}else{
		    		$(this).parent().find('span.testing').html('Checking...');
		    	}
		    	uploadFiles(event,$(this),$(this).parent().find('input[type=hidden]'),$(this).parent().find('span.testing'));
		    }
		  }catch(e){
		    //console.log(e);
		  }
		}


		/******************************************************************/
		/******************************************************************/
		jQuery.expr[':'].Contains = function(a,i,m){
			return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
		};

	  	function search(store,value){
			try{
				var result=[];
				var ci=[];
					store.forEach(function(el,id){
					if(el.value!=undefined && el.value!=null){
						if(refSearch(el.value,value.indexOf(" ")>-1 ? value.split(" ") : [value])){
							result.push(el);
						}
					}
				})
				return result;
			}catch(e){
				console.log("error > fn > search > "+value);
				return [];
			}
		}

		function refSearch(string,match){
			try{
				var c=0;
				var nc=match.length;
				for(var i=0;i<nc;i++){
					if(match[i]!=undefined && match[i]!=null){
						if(match[i]==""){
							nc--;
						}else{
							if(string.toUpperCase().indexOf(match[i].toUpperCase())>-1){
								c++;
							}
						}
					}
				}
				if(nc==c){
					return true;
				}else{
					return false;
				}
			}catch(e){
				return false;
			}
		}
		function getDinId(){
			var alpha="id-";
			alpha+=""+Math.ceil(Math.random()*999)+"-";
			alpha+=""+Math.ceil(Math.random()*999)+"-";
			alpha+=""+Math.ceil(Math.random()*999);
		  	return alpha;
		}

		function listFilter(jsonFilter) {
			try{
				jsonFilter.el.parent().find(".wrapFilter").remove();
			}catch(e){}
			var id=getDinId(),
				multiple = jsonFilter.multiple ? "multiple" : "simple",
				required = jsonFilter.el.attr("required")!=undefined ? "req1" : "",
				cn = jsonFilter.class!=undefined && jsonFilter.class!=null ? "filterinput "+jsonFilter.class+" "+required : "filterinput",
			container = $("<div>").attr({"id":id, "class":"wrapFilter"}),
			div = $("<div>").attr({"class":"filterform"}),
		    newEl = $("<input>").attr({"name":"back_"+jsonFilter.el.attr("name"),"type":"hidden"}),
		    input = $("<input>").attr({"class":cn,"placeholder":jsonFilter.title,"required":required=="req1","type":"text"}),
		    list0 = $("<ul>").attr({"class":"list filter level0 "+multiple}),
		    list1 = $("<ul>").attr({"class":"list filter level1"});

		$(div).append(newEl);
		$(div).append(input);
		$(div).append(list1);
		$(div).append(list0);
		jsonFilter.el.attr("style","background:red;");
		$(container).append(div).appendTo(jsonFilter.el.parent());
		//jsonFilter.el.remove();
		jsonFilter.el.hide();

		$(input)
		.keyup( function () {
			try{
				//console.log("keyup >");
				//console.log(this);
				var filter = $(this).val();
				var r=null;
				r=search(jsonFilter.store,filter);
				var list0=$(this).parent().find(".list.level0");
				var list1=$(this).parent().find(".list.level1");
				list1.html("");
				list1.hide();
				if(r.length>0 && filter!=undefined && filter!=null && filter!=""){
					list1.show();
					for(var i=0;i<r.length;i++){
						list1.append('<li data-value="'+r[i].key+'">'+r[i].value+'</li>');
					}
				}
				addItemListFilter($(this),jsonFilter.el,newEl,jsonFilter.multiple,list0,list1);
				return false;
			}catch(e){
				return false;
			}
		});
		}

		function addItemListFilter(input,el,newEl,multiple,list0,list1){
			try{
				list0.find("li").unbind("click");
				list1.find("li").unbind("click");
			}catch(e){}	
			list1.find("li").bind("click",function(event){
				var value=$(this).attr("data-value");
				var exists=false;
				list0.find("li[data-value="+value+"]").each(function(id,el){
				exists=true;
				});
				if(!exists){
					if(!multiple){
						input.hide();
						if(list0.find("li").length>0){
							return false;
						}
					}
					if(list0.find("li").length>0){
						$(list0.find("li")[0]).before(event.currentTarget.outerHTML);
					}else{
						list0.append(event.currentTarget.outerHTML);
					}
					list0.show();
					$(this).remove();
					newEl.val("");
					list0.find("li").each(function(id,el){
						if(newEl.val().length>0){
							newEl.val($(el).attr("data-value")+","+newEl.val());
						}else{
							newEl.val($(el).attr("data-value"));
						}
					});
					el.val(newEl.val().split(","));
					if(el.val().length>0){
						if(input.hasClass("req1")){
							input.removeAttr("required");
						}
					}else{
						if(input.hasClass("req1")){
							input.attr("required","required");
						}
					}
					addItemListFilter(input,el,newEl,multiple,list0,list1);	
				}else{
					$(this).remove();
				}
				input.val("");
				list1.hide();
				list1.html("");
			});
			list0.find('li').bind("click",function(event){
				var value=$(this).attr("data-value");
					var exists=false;
					list1.find("li[data-value="+value+"]").each(function(id,el){
					exists=true;
					});
					if(!exists){
						if(!multiple){
							input.show();
						}
						list1.append(event.currentTarget.outerHTML);
						$(this).remove();
						newEl.val("");
						list0.find("li").each(function(id,el){
							if(newEl.val().length>0){
								newEl.val($(el).attr("data-value")+","+newEl.val());
							}else{
								newEl.val($(el).attr("data-value"));
							}
						});
						el.val(newEl.val().split(","));
						if(el.val().length>0){
							if(input.hasClass("req1")){
								input.removeAttr("required");
							}
						}else{
							if(input.hasClass("req1")){
								input.attr("required","required");
							}
						}
						addItemListFilter(input,el,newEl,multiple,list0,list1);
					}else{
						$(this).remove();
					}
					input.val("");
					list1.hide();
					list1.html("");
			});
		}
		/******************************************************************/
		/******************************************************************/

		/******************************************************************/
		/***************************** excel ******************************/
		/******************************************************************/
		function getHtmlTable(id,currTab){
			try{
				var html="";
				var id=parseInt(id);
				var listFileName=["Empty","general","gestionadas","enproceso","porgestionar"];
				var fileName=listFileName[id];
				/*indicador,indicador-detallado,indicador-detallado2*/
				currTab.find("table").parent().each(function(id,el){
					if(this.tagName==="DIV" && this.style.display!="none"){
						var cdiv=$(this);
						html+=$(this).html();
					}
				});
				exportExcel(fileName,html,true);
			}catch(e){
				console.log("fn > getHtmlTable > ");
				console.log(e);
			}
		}

		function exportExcel(nameFile,html,encode){
			if(html.length>0 && nameFile.length>0){
				html=encode ? encodeURI(html) : html;
				/*var form=$("<form>").attr({"style":"display:none;width:0;height:0px;",
					"target":"_blank",
					"method":"GET",
					"action":server_req.central+"lib/exportExcel.php"
				});
				var input1=$("<input>").attr({
					"name":"nameFile",
					"type":"hidden",
					"value":nameFile
				});
				var input2=$("<input>").attr({
					"name":"OptionExcel",
					"type":"hidden",
					"value":"_REQUEST"
				});
				var input3=$("<input>").attr({
					"name":"exportExcel",
					"type":"hidden",
					"value":html
				});
				input1.appendTo(form);
				input2.appendTo(form);
				input3.appendTo(form);
				form.submit();
				setTimeout(function(){
					delete form;
					delete input1;
					delete input2;
					delete input3;
				},30);*/
				location.href=server_req.central+"lib/exportExcel.php?nameFile="+nameFile+"&OptionExcel=_REQUEST&exportExcel="+html;
			}
		}
		/******************************************************************/
		/***************************** excel ******************************/
		/******************************************************************/



		$.getJSON( server_req.standa+ "data/template/inf_solici_solici.json?_t="+Math.random(), function( data ) {
			//console.log("load inf_solici_solici > ");
			renderJsonToHTML(data,target,1);
			checkBasicLoad("source");
		});

		try{
			//si en 30 segundo no se carga, intentar nuevamente
			setTimeout(function(){
				if($("#tabs #tabs-1 .loading.help-block").css("display")=="block"){
					if(location.href.indexOf("reload=true")>-1){
						if($(".alert.alert-warning.delay").length==0){
							$(".inf_solici_solici").before('<h4 class="alert alert-warning delay">Su conexion presenta demoras, intente cargar de nuevo el servicio.</h4>');
						}
					}else{
						location.href="?window="+server_req.window+"&cod_servic="+server_req.cod_servic+"&r="+Math.random()+"&reload=true";
					}
				}
			},7000);
		}catch(e){}
}
prevRequireLibJs();