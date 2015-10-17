function AddGroup(obj) {
	var Row=obj.parentNode.parentNode;
	var Gruppo=document.getElementById('GrName').value;
	if(Gruppo=='') return;
	
	var fields='';
	for(var n=2; n<Row.childElementCount; n++) {
		var radio=Row.childNodes[n].childNodes[0];
		if(radio.checked) fields=fields+'&'+radio.name+'='+Gruppo;
	}
	if(fields=='') return;
	fields='?new=1'+fields;
	
	ManageFields(fields);
}

function UpdateGroup(obj, remove) {
	var fields='';
	fields='?'+obj.name+'='+obj.value;
	if(remove) fields=fields+'&del=1';
	
	ManageFields(fields);
}

function ManageFields(fields) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxAddGroup.php"+fields, true);
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
						var Data = XMLRoot.getElementsByTagName('row').item(0).firstChild.data;

						if (Error==0) {
							if(Data) {
								var tabella=document.getElementById('Groups');
								var riga=tabella.insertRow(tabella.rows.length-1);
								riga.innerHTML=Data;
							}
							document.getElementById('GrName').value='';
						} else {
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

function DeleteGroup(obj) {
	if(!confirm(ConfirmDeleteRow)) return;
	
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxDeleteRow.php?Group="+obj.id,true);
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
							var Row=obj.parentNode.parentNode;
							Row.parentNode.deleteRow(Row.rowIndex);
						} else {
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

function getModuli(Cell, id, action) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","getModuli.php"+(id ? '?id='+id : ''),true);
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
						var Data = XMLRoot.getElementsByTagName('data');

						if (Error==0) {
							var CatSelect=document.getElementById(Cell);
							var option=document.createElement('option');
							option.text='==> Seleziona';
							option.value=0;
							try {
								// for IE earlier than version 8
								CatSelect.add(option,CatSelect.options[null]);
							} catch (e) {
								CatSelect.add(option,null);
							}
							for(n=0; n<Data.length; n++) {
								var option=document.createElement('option');
								option.text=Data.item(n).firstChild.data;
								option.value=Data.item(n).getAttribute('value');
								try {
									// for IE earlier than version 8
									CatSelect.add(option,CatSelect.options[null]);
								} catch (e) {
									CatSelect.add(option,null);
								}

							}
						} else {
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
