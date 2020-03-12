var rowId=null;
var activeCell=null;
var activeValue='';
var activeField='';
var activeWhat='';


function insertInput(cell, what){
	var url='';
	if(rowId)
		resetCell(activeWhat);

	rowId=cell.parentNode.id;

	activeCell=cell;
	activeValue=cell.innerHTML.replace(/<br>/ig,"");

	activeCell.onclick=null;

	activeWhat=what;

	switch(what) {
	case 'AwOrder':
	case 'FirstLanguageCode':
	case 'SecondLanguageCode':
	case 'AwEventTrans':
		activeField=document.createElement('input');
		activeField.maxlength='3';
		activeField.size='5';

		break;
	case 'AwPositions':
		activeField=document.createElement('select');
		arrValues = Array('1','1,2,3','1,2,3,4','1,2,4,3');
		for(var i=0; i<arrValues.length; i++) {
			var opt = document.createElement('option');
			opt.text = arrValues[i];
			if(arrValues[i]=='1,2,4,3') opt.text = '1,2,3-3';
			opt.value = arrValues[i];
			try {
				activeField.add(opt,null); // standard
			} catch(ex) {
				activeField.add(opt); // IE ....
			}
		}
		break;
	case 'AwDescription-1':
	case 'AwAwarders-1':
	case 'AwAwarders-2':
		activeField=document.createElement('select');
		var opt = document.createElement('option');
		opt.text = '==>';
		opt.value = '';
		try {
			activeField.add(opt,null); // standard
		} catch(ex) {
			activeField.add(opt); // IE ....
		}
		for(var i=0; i<AwKeys.length; i++) {
			var opt = document.createElement('option');
			opt.text = AwValues[i];
			opt.value = AwKeys[i];
			try {
				activeField.add(opt,null); // standard
			} catch(ex) {
				activeField.add(opt); // IE ....
			}
		}
		break;
	default:
		activeField=document.createElement('textarea');
		activeField.style.width='100%';
		activeField.rows = ((what=='AwAwarders-1' || what=='AwAwarders-2' ) ? 5 : 3);
		activeField.cols=80;
		break;
	}

	if (activeField.addEventListener)
		activeField.addEventListener("blur", function () {updateField (this);}, false);
	else if (activeField.attachEvent)
		activeField.attachEvent("onblur",  function () {updateField (this);});

	activeField.value=activeValue;
	activeField.id='f'+cell.parentNode.id;
	activeField.name=what;

	while(activeCell.childNodes.length > 0) activeCell.removeChild(activeCell.childNodes.item(0));
	activeCell.appendChild(activeField);
	activeField.focus();
}


function resetCell(activeCtrl) {
	if (activeCell.addEventListener)
		activeCell.addEventListener("click", function(){insertInput(this, activeCtrl);this.removeEventListener('click',arguments.callee,false);}, false);
	else if (activeCell.attachEvent)
		activeCell.attachEvent("onclick", function(){insertInput(this, activeCtrl);this.detachEventListener('onclick',arguments.callee);});
	activeCell.innerHTML=activeValue;

	rowId=null;
	activeCell=null;
	activeValue='';
	activeField='';
	activeWhat='';
}

function updateField(field) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","UpdateAwardings.php?id="+field.id+"&field="+field.name+"&value="+encodeURIComponent(field.value) ,true);
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
						activeValue=activeField.value;

						if(Error==0) {
							var Field = XMLRoot.getElementsByTagName('field').item(0).firstChild.data;
							var Value = XMLRoot.getElementsByTagName('value').item(0).firstChild.data;
							activeValue=Value;
							field.value=Value;
							field.parentNode.style.backgroundColor='';
							resetCell(Field);
						} else {
							field.parent.style.backgroundColor='yellow';
							resetCell(activeWhat);
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



function DeleteAwards(Event,FinEv,TeamEv,Message){
if (confirm(Message))
	window.location.href='ManAwards.php?Command=DELETE&EvDel=' + Event + '&FinEv=' + FinEv+ '&TeamEv=' + TeamEv;
}

function switchEnabled(Event,FinEv,TeamEv) {
	window.location.href='ManAwards.php?Command=SWITCH&EvSwitch=' + Event + '&FinEv=' + FinEv+ '&TeamEv=' + TeamEv;
}

function switchOption(Option) {
	window.location.href='ManAwards.php?Command=OPTION&OptSwitch=' + Option;
}

function Manage(obj) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET", "GetAwarders.php?id="+obj.parentNode.id, true);
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

						var Error = XMLRoot.getAttribute('error');

						if(Error==0) {
							var Html = XMLRoot.getElementsByTagName('html').item(0).firstChild.data;
							obj.innerHTML=Html;
							obj.onclick=null;
						} else {
							obj.parent.style.backgroundColor='yellow';
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

