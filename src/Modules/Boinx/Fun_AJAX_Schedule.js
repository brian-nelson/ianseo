/*
														- Fun_AJAX_ManTraining.js -
	Contiene le funzioni ajax che riguardano la pagina ManTraining.php
	NOTA BENE: deve essere invlusa la stringa
	<?php
	print 'var StrConfirm="' . get_text('MsgAreYouSure') . '";';
	?>
*/



function toggle(but) {
	var todo='';
	var idToDo=but.id;
	var DivSwitch=but.id.substr(0,3);
	if(but.className=='button_on') {
		switch(DivSwitch) {
		case 'Rss':
			switch(idToDo.substr(idToDo.length-4)) {
			case '$03$':
			case '$10$':
			case '$20$':
				idToDo=idToDo.substr(0,idToDo.length-5);
				break;
			}
			break;
		case 'Qua':
			idToDo=idToDo.substr(0,idToDo.length-2);
			break;
		}
		todo = 'type[' + idToDo + ']=0';
	} else {
		var extra=1;
		switch(DivSwitch) {
		case 'Awa':
			// eliminates all "on" buttons of the div
			var bb=document.getElementsByName(DivSwitch);
			for(var b=0; b<bb.length; b++) {
				if(bb[b].className=='button_on') {
					// turns off the button
					todo = todo + 'type[' + bb[b].id + ']=0&';
				}
			}
			break;
		case 'Rss':
			extra=3;
			switch(idToDo.substr(idToDo.length-4)) {
			case '$03$':
			case '$10$':
			case '$20$':
			case '$al$':
				extra=idToDo.substr(idToDo.length-3, 2);
				idToDo=idToDo.substr(0,idToDo.length-5);
				break;
			}
			break;
		case 'Qua':
			var Bib='0000'+document.getElementById('Qua_Ind_Bib').value.toUpperCase();
			if(idToDo.substr(idToDo.length-3, 3)=='Bib') {
				idToDo=idToDo.substr(0,idToDo.length-4);
			} else {
				QuaSes=parseInt(idToDo.substr(idToDo.length-1));
				idToDo=idToDo.substr(0,idToDo.length-2);
			}
			extra=QuaSes+Bib.substr(Bib.length-4);
			break;
		}
		todo = todo + 'type[' + idToDo + ']='+extra;
	}
	toggle_do(todo);
}

function toggle_do(queryString) {
	if (!XMLHttp) return;

	try {
		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {

			XMLHttp.open("POST","SaveSchedule.php",true);
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=toggle_do_StateChange;
			XMLHttp.send(queryString);

		}
	} catch(e) {
		//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
	}
}

function toggle_do_StateChange() {
	if (XMLHttp.readyState==XHS_COMPLETE) {
		if (XMLHttp.status==200) {
			try {
				toggle_do_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		} else {
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function toggle_do_Response() {
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;

	// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

	// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

	if (Error==0) {
		var button = XMLRoot.getElementsByTagName('button');
		var on = XMLRoot.getElementsByTagName('onoff');
		var RaiseButton=document.getElementById('RaiseFlag');

		for(var n=0; n< button.length; n++) {
			var but=document.getElementById(button[n].textContent);
			but.className='button_' + (on[n].textContent>0 ? 'on' : 'off');
			if(RaiseButton) RaiseButton.style.backgroundColor='white';
		}
	}
}

function SetBackColor() {
	var obj=document.getElementById('Page_BGColor');
	var field=encodeURIComponent(obj.name)+'='+encodeURIComponent(obj.value);

	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxUpdateBackColor.php?"+field,true);
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

						if (Error!=0) {
							alert('Could not change color');
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
		}
	}
}

function DoRaiseFlag() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxRaiseFlags.php",true);
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

						if (Error!=0) {
							alert('Could not raise flag');
						} else {
							var Status=XMLRoot.getElementsByTagName('status').item(0).firstChild.data;
							switch(Status) {
								case '1':
									document.getElementById('RaiseFlag').style.backgroundColor='white';
									break;
								case '2':
									document.getElementById('RaiseFlag').style.backgroundColor='yellow';
									break;
							}
						}
					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
		}
	}

}