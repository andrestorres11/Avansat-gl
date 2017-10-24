function requireLibJs(){
	var req=false,jq=true,jqui=true;
	if(typeof jQuery == "undefined"){
		jq=false;
		jqui=false;
		req=true;
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
		var target=$(".ins_solici_solici");
		if(typeof ds == "undefined")
			ds = "";

		if(typeof cs == "undefined")
			cs = "";

		if(typeof wd == "undefined")
			wd = "";

		if(typeof ot == "undefined")
			ot = "";

		var server_req={"standa":ds,"cod_servic":cs,"window":wd,"option":ot};
		var loadCity=false;
		var loadLicensePlate=false;
		var store={};
		//console.log("server_req > ");
		//console.log(server_req);
		
		function load(){
			try{
				try{
					$.getJSON( server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=99&cod_servic="+server_req.cod_servic+"&r="+Math.random(), function( data ) {
						updateUser(data);
						checkBasicLoad("tab1");
					});
				}catch(e){}

				$( "#tabs" ).tabs();
				//$("#tabs ul li a").unbind("click");
				$("#tabs ul li a").bind("click",tab_load_content);
				$("#tabs ul li a")[0].click();

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
				var ajax_url="";
				switch(tabid){
					case "#tabs-1":
						ajax_url=server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=1&cod_servic="+server_req.cod_servic+"&r="+Math.random();
					break;
					case "#tabs-2":
						ajax_url=server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=2&cod_servic="+server_req.cod_servic+"&r="+Math.random();
					break;
					case "#tabs-3":
						ajax_url=server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=3&cod_servic="+server_req.cod_servic+"&r="+Math.random();
					break;
					case "#tabs-4":
						ajax_url=server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=4&cod_servic="+server_req.cod_servic+"&r="+Math.random();
					break;
				}
				$.ajax({
					method: "POST",
					type: 'POST',
					url: ajax_url,
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
				var divActive=$("#tabs form").find('.alert.active');
				//divActive.parent().find(".btn[name=send]").attr("disabled","disabled");
				//divActive.html(("<h2>Respuesta</h2>").toUpperCase());
				divActive.html((""));
				divActive.append("<ul>");
				var objResponse=JSON.parse(msg);
				for(a in objResponse){
					try{
						var currObjResp=objResponse[a];
						if(currObjResp.response==false){
							try{
								if(currObjResp.ws.get.error!=null){
									//alert(currObjResp.ws.get.error);
									divActive.append("<li>"+currObjResp.ws.get.error.toUpperCase()+"</li>");
								}
								if(currObjResp.ws.get.fault!=null){
									//alert(currObjResp.ws.get.fault);
									divActive.append("<li>"+currObjResp.ws.get.fault.toUpperCase()+"</li>");
								}
							}catch(e){
								//console.log("processResult1 >  > catch 3");
								//console.log(e);
								//alert("Error, The Web Service have any problem, contact your provider.");
								divActive.append(("<li>Error, The Web Service have any problem, contact your provider.</li>").toUpperCase());
							}
						}else{
							
							//alert(currObjResp.response);
							var y=currObjResp.response;
							if(typeof y == "object"){
								//divActive.append("<li>"+currObjResp.response+"</li>");
								for( a in y){
									divActive.append("<ul>"+a.toUpperCase()+"<li>"+y[a].toUpperCase()+"</li><ul>");
								}
							}else{
								var srtResponse = currObjResp.response.split(";")[1].split(":")[1];
								divActive.append("<li>"+srtResponse.toUpperCase()+"</li>");
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
			}catch(e){
				//console.log("processResult1 > catch 1");
				//console.log(e);
				var divActive=$("#tabs form").find('.alert.active');
				if(divActive!=null)
					divActive.html(msg.toUpperCase());
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

				//uselo para personalizar eventos
				switch(id){
					case "ui-id-1":
						try{
							var origen=currForm.find("select.control-origen");
							var destino=currForm.find("select.control-destino");
							currForm.show();

							if(loadCity==false){
								resetSelect(destino);
								resetSelect(origen);
								$.getJSON( server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=98&cod_servic="+server_req.cod_servic+"&r="+Math.random(), function( data ) {
									store.city=data;
									updateSelect(destino,data,null);
									updateSelect(origen,data,null);
									listFilter({"title":"Origen", "class":"form-control", "store": store.city,"el":origen,"multiple":false});
									listFilter({"title":"Destino", "class":"form-control", "store": store.city,"el":destino,"multiple":false});
									checkBasicLoad("tab1");
									loadCity=true;
								});
								
								currForm.find(".btn[name=add]").unbind("click");
								currForm.find(".btn[name=add]").bind("click",function(e){
									onAddRuta(currForm,currTabStr,objReply,objReplyParent);
								});
							}else{
								checkBasicLoad("tab1");
							}
						}catch(e){
							//console.log(e);
						}
					break;
					case "ui-id-2":
						currForm.show();
						currTab.find(".loading.help-block").hide();

						$("input[type=radio][name=ind_segesp][value=1]").bind("click",function(){
							 $(".form-group.lis_placax").addClass("hide");
							 $(".form-group.lis_placax select").removeClass("required");
							 $(".form-group.lis_placax select").removeAttr("required");
						});
						$("input[type=radio][name=ind_segesp][value=2]").bind("click",function(){
							 $(".form-group.lis_placax").removeClass("hide");
							 $(".form-group.lis_placax select").addClass("required");
							 $(".form-group.lis_placax select").attr("required","required");

							if(loadLicensePlate==false){
								//resetSelect(updateSelect($(".form-group.row.lis_placax").find("select"));
								$.getJSON( server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=97&cod_servic="+server_req.cod_servic+"&r="+Math.random(), function( data ) {
									updateSelect(updateSelect($(".form-group.row.lis_placax").find("select"),data,null));
									checkBasicLoad("tab2");
									loadLicensePlate=true;
								});
							}
						});
						//forzar a usar una alternativa para navegadores que no usen webkit
						$('input[type="datetime-local"]').each(function(k,v){
							forceDateTimeLocal(this);
						});
  
					break;
					case "ui-id-3":
						currForm.show();
						currTab.find(".loading.help-block").hide();
						currTab.find("input[type=file]").bind('change', prepareUpload);
					break;
					case "ui-id-4":
						currForm.show();
						currTab.find(".loading.help-block").hide();
						currTab.find("input[type=file]").bind('change', prepareUpload);
					break;
				}
			}catch(e){}
		}
		//datetime-local
		function forceDateTimeLocal(obj){
			//if(navigator.userAgent.indexOf("WebKit")==-1){

				var randomId="data-source-"+parseInt(new Date().getMilliseconds()+parseInt(Math.random()*1000))+1;
				$(obj).attr("data-id",randomId);
				$(obj).attr("type","hidden");
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
					var tmpHtml=$('<div class="form-group nested col-xs-12 col-md-12">'+objReply[0].innerHTML+'</div>');
					tmpHtml.appendTo(objReplyParent);
					var origen=tmpHtml.find("select.control-origen");
					var destino=tmpHtml.find("select.control-destino");
					listFilter({"title":"Origen", "class":"form-control", "store": store.city,"el":origen,"multiple":false});
					listFilter({"title":"Destino", "class":"form-control", "store": store.city,"el":destino,"multiple":false});
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
					//var currForm=$("#tabs "+tabid+" form");
					currForm.each(function(e){
						this.reset();
					});
					currForm.find('.alert').html("");
					currForm.find('.alert').addClass("hide");
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
					currForm.find('.alert').html(("Informacion incompleta, verifique e intente nuevamente").toUpperCase());
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
					if(check_load[changeStrToCodeAt(ref)]==1){
						$("#tabs #tabs-1 .loading.help-block").hide();
						check_load[changeStrToCodeAt(ref)]=0;
					}
				break;
				case "source":
					if(check_load[changeStrToCodeAt(ref)]==6){
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
				el.append("<option>Cargando...</option>");
			}catch(e){}
		}

		function renderJsonToHTML(obj,target,replace){
			//console.log("renderJsonToHTML");
			if(typeof replace == "undefined")
				replace=0;
			if(typeof obj == "object" && obj.tag!=undefined){
					var el = document.createElement(obj.tag);
					el=$(el);
					el.html(obj.html?obj.html.toUpperCase():obj.html);
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
						el.html(obj2.html?obj2.html.toUpperCase():obj2.html);
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
		    	url: server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=96&cod_servic="+server_req.cod_servic+"&r="+Math.random(),
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
				    	url: server_req.standa+"ins_solici_solici.php?window="+server_req.window+"&option=95&cod_servic="+server_req.cod_servic+"&r="+Math.random(),
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
				//console.log("error > fn > search > "+value);
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

		$.getJSON( server_req.standa+ "data/template/ins_solici_solici.json?_t="+Math.random(), function( data ) {
			//console.log("load ins_solici_solici > ");
			renderJsonToHTML(data,target,1);
			checkBasicLoad("source");
		});

		try{
			//si en 30 segundo no se carga, intentar nuevamente
			setTimeout(function(){
				if($("#tabs #tabs-1 .loading.help-block").css("display")=="block"){
					if(location.href.indexOf("reload=true")>-1){
						if($(".alert.alert-warning.delay").length==0){
							$(".ins_solici_solici").before('<h4 class="alert alert-warning delay">'+('Su conexion presenta demoras, intente cargar de nuevo el servicio.').toUpperCase()+'</h4>');
						}
					}else{
						location.href="?window="+server_req.window+"&cod_servic="+server_req.cod_servic+"&r="+Math.random()+"&reload=true";
					}
				}
			},7000);
		}catch(e){}
}
prevRequireLibJs();