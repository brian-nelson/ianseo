function DiUpdate(obj) {
//		if(obj.value=='') return;
	var field=encodeURIComponent(obj.name)+'='+encodeURIComponent(obj.value);
	
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxUpdate.php?"+field,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");

						XMLRoot = XMLResp.documentElement;

						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

						if (Error==0) {
							var Old=XMLRoot.getElementsByTagName('old');
							var New=XMLRoot.getElementsByTagName('new');
							var oldTimName = XMLRoot.getElementsByTagName('oldTimName');
							var oldDurName = XMLRoot.getElementsByTagName('oldDurName');
							var oldOptName = XMLRoot.getElementsByTagName('oldOptName');
							var newTimName = XMLRoot.getElementsByTagName('newTimName');
							var newDurName = XMLRoot.getElementsByTagName('newDurName');
							var newOptName = XMLRoot.getElementsByTagName('newOptName');
							if(oldTimName) {
								var Fields=obj.parentElement.parentElement.getElementsByTagName('input');
								for(var n=0; n<Fields.length; n++) {
									if(Fields.item(n).name==oldTimName.item(0).firstChild.data) {
										Fields.item(n).name=newTimName.item(0).firstChild.data;
										var Data=XMLRoot.getElementsByTagName('warmtime').item(0).firstChild.data;
										if(Data!=Fields.item(n).value) {
											Fields.item(n).value=Data;
											Fields.item(n).style.color='green';
										} else {
											Fields.item(n).style.color='blue';
										}
									}
									if(Fields.item(n).name==oldDurName.item(0).firstChild.data) {
										Fields.item(n).name=newDurName.item(0).firstChild.data;
										var Data=XMLRoot.getElementsByTagName('warmduration').item(0).firstChild.data;
										if(Data!=Fields.item(n).value) {
											Fields.item(n).value=Data;
											Fields.item(n).style.color='green';
										} else {
											Fields.item(n).style.color='blue';
										}
									}
									if(Fields.item(n).name==oldOptName.item(0).firstChild.data) {
										Fields.item(n).name=newOptName.item(0).firstChild.data;
										var Data=XMLRoot.getElementsByTagName('options').item(0).firstChild.data;
										if(Data!=Fields.item(n).value) {
											Fields.item(n).value=Data;
											Fields.item(n).style.color='green';
										} else {
											Fields.item(n).style.color='blue';
										}
									}
								}
								obj.style.color='green';
							} else {
								var Fields=obj.parentElement.parentElement.getElementsByTagName('input');
								for(var n=0; n<Fields.length; n++) {
									var FldName=Fields.item(n).name.substring(7, Fields.item(n).name.indexOf(']', 7)).toLowerCase();
									if(FldName=='') continue;
									var Data = XMLRoot.getElementsByTagName(FldName).item(0).firstChild.data;
									if(Data!=Fields.item(n).value) {
										Fields.item(n).value=Data;
										Fields.item(n).style.color='green';
									} else {
										Fields.item(n).style.color='blue';
									}
									if(Old.length>0 && New.length>0) {
										Fields.item(n).name=Fields.item(n).name.replace(Old.item(0).firstChild.data, New.item(0).firstChild.data);
									}
								}
								obj.style.color='green';
							}
							var Scheduler=XMLRoot.getElementsByTagName('sch');
							if(Scheduler) {
								document.getElementById('TrueScheduler').innerHTML=Scheduler.item(0).firstChild.data;
							}
							var Texts=XMLRoot.getElementsByTagName('txt');
							if(Texts) {
								document.getElementById('ScheduleTexts').innerHTML=Texts.item(0).firstChild.data;
							}
						} else {
							obj.style.backgroundColor='red';
							obj.value=obj.defaultValue;
							// SetStyle(Which,'error');
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function DiDelete(obj) {
	var field=encodeURIComponent(obj.id)+'=del';
	
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxDelete.php?"+field,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);
	
						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
	
						XMLRoot = XMLResp.documentElement;
	
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
						if (Error==0) {
							var Row=obj.parentElement.parentElement;
							Row.parentElement.deleteRow(Row.rowIndex);
							var Scheduler=XMLRoot.getElementsByTagName('sch');
							if(Scheduler) {
								document.getElementById('TrueScheduler').innerHTML=Scheduler.item(0).firstChild.data;
							}
							var Texts=XMLRoot.getElementsByTagName('txt');
							if(Texts) {
								document.getElementById('ScheduleTexts').innerHTML=Texts.item(0).firstChild.data;
							}
						} else {
							obj.style.bgcolor='red';
							// SetStyle(Which,'error');
						}
	
					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}
	
				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function DiInsert(obj) {
	var field='';
	var Fields=obj.parentElement.parentElement.getElementsByTagName('input');
	for(var n=0; n<Fields.length; n++) {
		var input=Fields.item(n);
		if(input.name.substr(0, 4)=='Fld[') {
			field+='&'+encodeURIComponent(input.name)+'='+encodeURIComponent(input.value);
		}
	}
	
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxInsert.php?"+field.substr(1),true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);
	
						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
	
						XMLRoot = XMLResp.documentElement;
	
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
						if (Error==0) {
							var Texts=XMLRoot.getElementsByTagName('txt');
							if(Texts) {
								document.getElementById('ScheduleTexts').innerHTML=Texts.item(0).firstChild.data;
							}
							var Scheduler=XMLRoot.getElementsByTagName('sch');
							if(Scheduler) {
								document.getElementById('TrueScheduler').innerHTML=Scheduler.item(0).firstChild.data;
							}
						} else {
							obj.style.bgcolor='red';
							// SetStyle(Which,'error');
						}
	
					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}
	
				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function DiAddSubRow(obj) {
	var row=obj.parentNode.parentNode;
	var cella=row.cells[6];
	// if no warmup already scheduled stops here
	if(cella.lastElementChild.value=='') return;
	
	cella.innerHTML+='<br/><input size="5"  type="text" name="'+cella.firstElementChild.name.replace(/\[[^\]]+\]$/, '[]')+'" value="" onchange="DiUpdate(this)">';
	var cella=row.cells[7];
	cella.innerHTML+='<br/><input size="3"  type="text" name="'+cella.firstElementChild.name.replace(/\[[^\]]+\]$/, '[]')+'" value="" onchange="DiUpdate(this)">';
	var cella=row.cells[8];
	cella.innerHTML+='<br/><input size="50"  type="text" name="'+cella.firstElementChild.name.replace(/\[[^\]]+\]$/, '[]')+'" value="" onchange="DiUpdate(this)">';
}

function DiDelSubRow(obj, warmTime) {
	var field='WarmDelete='+encodeURIComponent(warmTime);
	
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxDelete.php?"+field,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");

						XMLRoot = XMLResp.documentElement;

						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

						var row=obj.parentNode.parentNode;
						var cella6=row.cells[6];
						var cella7=row.cells[7];
						var cella8=row.cells[8];

						if (Error==0) {
							SearchIndex=0;
							for(var i=0; i<obj.parentNode.childElementCount; i++) {
								if(obj.parentNode.childNodes.item(i)==obj) SearchIndex=i;
							}
							
							if(SearchIndex>1) {
								SearchIndex--;
								cella6.removeChild(cella6.childNodes.item(SearchIndex));
								cella6.removeChild(cella6.childNodes.item(SearchIndex));
								cella7.removeChild(cella7.childNodes.item(SearchIndex));
								cella7.removeChild(cella7.childNodes.item(SearchIndex));
								cella8.removeChild(cella8.childNodes.item(SearchIndex));
								cella8.removeChild(cella8.childNodes.item(SearchIndex));
								obj.parentNode.removeChild(obj.parentNode.childNodes.item(SearchIndex));
								obj.parentNode.removeChild(obj.parentNode.childNodes.item(SearchIndex));
							} else {
								cella6.style.backgroundColor='red';
								cella7.style.backgroundColor='red';
								cella8.style.backgroundColor='red';
							}
							
							var Texts=XMLRoot.getElementsByTagName('txt');
							if(Texts.length>0) {
								document.getElementById('ScheduleTexts').innerHTML=Texts.item(0).firstChild.data;
							}
							var Scheduler=XMLRoot.getElementsByTagName('sch');
							if(Scheduler.length>0) {
								document.getElementById('TrueScheduler').innerHTML=Scheduler.item(0).firstChild.data;
							}
						} else {
							cella6.style.backgroundColor='red';
							cella7.style.backgroundColor='red';
							cella8.style.backgroundColor='red';
							// SetStyle(Which,'error');
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}