function DiUpdate(obj) {
//		if(obj.value=='') return;
	var field=encodeURIComponent(obj.name)+'='+encodeURIComponent(obj.value);

	$.getJSON("AjaxUpdate.php?"+field, function(data) {
		if (data.error==0) {
			var Old=data.old;
			var New=data.new;
			var oldTimName = data.oldTimName;
			var oldDurName = data.oldDurName;
			var oldOptName = data.oldOptName;
			var newTimName = data.newTimName;
			var newDurName = data.newDurName;
			var newOptName = data.newOptName;
			if(oldTimName && oldTimName.length>0) {
				$(obj).closest('tr').find('input').each(function() {
					if(this.name==oldTimName) {
						this.name=newTimName;
						if(data.warmtime!=this.value) {
							this.value=data.warmtime;
							this.style.color='green';
						} else {
							this.style.color='blue';
						}
					}
					if(this.name==oldDurName) {
						this.name=newDurName;
						if(data.warmduration!=this.value) {
							this.value=data.warmduration;
							this.style.color='green';
						} else {
							this.style.color='blue';
						}
					}
					if(this.name==oldOptName) {
						this.name=newOptName;
						if(data.options!=this.value) {
							this.value=data.options;
							this.style.color='green';
						} else {
							this.style.color='blue';
						}
					}
				});
				obj.style.color='green';
			} else {
				$(obj).closest('tr').find('input').each(function() {
					var FldName=this.name.substring(7, this.name.indexOf(']', 7)).toLowerCase();
					if(FldName=='') return;
					var Data = data[FldName];
					if(Data && Data!=this.value) {
						this.value=Data;
						this.style.color='green';
					} else {
						this.style.color='blue';
					}
					if(Old && Old.length>0 && New && New.length>0) {
						this.name=this.name.replace(Old, New);
					}

				});
				obj.style.color='green';
			}
			if(data.sch) {
				$('#TrueScheduler').html(data.sch);
			}
			if(data.txt) {
				$('#ScheduleTexts').html(data.txt);
			}
		} else {
			obj.style.backgroundColor='red';
			obj.value=obj.defaultValue;
			// SetStyle(Which,'error');
		}
	});
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
