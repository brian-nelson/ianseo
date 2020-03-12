/*
														- Fun_AJAX_ManTraining.js -
	Contiene le funzioni ajax che riguardano la pagina ManTraining.php
	NOTA BENE: deve essere invlusa la stringa
	<?php 
	print 'var StrConfirm="' . get_text('MsgAreYouSure') . '";';
	?> 				
*/
function DefaultTarget(obj) {
	var cell=obj.parentNode;
	var getCell=cell.nextSibling.innerText;
	var putCell=cell.previousSibling.childNodes.item(0);
	putCell.value=getCell;
	UpdateTargets(putCell);
}

function UpdateTargets(obj) {
	//	if(obj.value=='') return;
	var field=encodeURIComponent(obj.name)+'='+encodeURIComponent(obj.value);
	
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxManTraining.php?"+field,true);
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
							var val=XMLRoot.getElementsByTagName('val').item(0).firstChild.data;
							obj.value=val;
							obj.style.color='green';
						} else {
							obj.style.backgroundColor='red';
							obj.value=obj.defaultValue;
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

