/*
														- Fun_AJAX_ManTraining.js -
	Contiene le funzioni ajax che riguardano la pagina ManTraining.php
	NOTA BENE: deve essere invlusa la stringa
	<?php
	print 'var StrConfirm="' . get_text('MsgAreYouSure') . '";';
	?>
*/
function AddLocation() {
	//	if(obj.value=='') return;
	var Location=document.getElementById('LocLocation');
	var Target_1=document.getElementById('LocStart');
	var Target_2=document.getElementById('LocEnd');

	if(!Location.value || !Target_1.value || !Target_2.value) return;

	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxFopSetup.php?Location="+Location.value+"&Target_1="+Target_1.value+"&Target_2="+Target_2.value,true);
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
							var loc=XMLRoot.getElementsByTagName('loc').item(0).firstChild.data;
							var tg1=XMLRoot.getElementsByTagName('tg1').item(0).firstChild.data;
							var tg2=XMLRoot.getElementsByTagName('tg2').item(0).firstChild.data;
							var num=XMLRoot.getElementsByTagName('num').item(0).firstChild.data;

							var Table=document.getElementById('FopTable');
							var Row=Table.insertRow(Table.rows.length-3);
							Row.id='Row'+num;
							// insert checkbox
							var Cell = Row.insertCell(-1);
							Cell.innerHTML='<input type="checkbox" name="Locations['+num+']" checked="checked">';

							// insert location name
							var Cell = Row.insertCell(-1);
							Cell.innerHTML='<input type="text" value="'+loc+'" id="Location['+num+']" onchange="UpdateField(this)">';

							// inserts first target
							var Cell = Row.insertCell(-1);
							Cell.innerHTML='<input type="text" value="'+tg1+'" id="Start['+num+']" onchange="UpdateField(this)">';

							// inserts last target
							var Cell = Row.insertCell(-1);
							Cell.innerHTML='<input type="text" value="'+tg2+'" id="Start['+num+']" onchange="UpdateField(this)">';

							// inserts delete image
							var Cell = Row.insertCell(-1);
							Cell.innerHTML='<img src="../Common/Images/drop.png" onclick="DeleteLocation(\'Row'+num+'\')">';


							Location.style.backgroundColor='';
							Target_1.style.backgroundColor='';
							Target_2.style.backgroundColor='';
							Location.value='';
							Target_1.value='';
							Target_2.value='';
						} else {
							Location.style.backgroundColor='red';
							Target_1.style.backgroundColor='red';
							Target_2.style.backgroundColor='red';
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

function UpdateField(obj) {
	if(obj.value=='') {
		obj.value=obj.defaultValue;
		return;
	}

	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxFopSetupUpdate.php?"+obj.id+"="+obj.value,true);
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
							obj.style.backgroundColor='green';
						} else {
							obj.style.backgroundColor='red';
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

function DeleteLocation(obj) {
	if(!confirm(StrConfirm)) {
		return;
	}

	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxFopSetupDelete.php?row="+obj,true);
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

						var Row=document.getElementById(obj);
						if (Error==0) {
							document.getElementById('FopTable').deleteRow(Row.rowIndex);
						} else {
							obj.style.backgroundColor='red';
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