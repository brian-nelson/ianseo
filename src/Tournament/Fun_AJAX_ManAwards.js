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
		activeField=document.createElement('input');
		activeField.maxlength='3';
		activeField.size='5';
		
		break;	
	case 'AwAwarders':
	case 'AwDescription':
		activeField=document.createElement('textarea');
		activeField.style.width='100%';
		activeField.rows = (what=='AwAwarders' ? 5 : 3);
		activeField.cols=80;
		break;
	case 'AwPositions':
		activeField=document.createElement('select');
		arrValues = Array('1','1,2,3','1,2,3,4'); 
		for(var i=0; i<arrValues.length; i++) {
			var opt = document.createElement('option');
			opt.text = arrValues[i];
			opt.value = arrValues[i];
			try {
				activeField.add(opt,null); // standard
			} catch(ex) {
				activeField.add(opt); // IE ....
			}
		}
		break;
	}

	if (activeField.addEventListener)
		activeField.addEventListener("blur", updateField, false);
	else if (activeField.attachEvent)
		activeField.attachEvent("onblur", updateField);

	activeField.value=activeValue;

	while(activeCell.childNodes.length > 0) activeCell.removeChild(activeCell.childNodes.item(0));
	activeCell.appendChild(activeField);
	activeField.focus();
}


function resetCell(activeCtrl) {
	if (activeCell.addEventListener)
		activeCell.addEventListener("click", function(){insertInput(this, activeCtrl);this.removeEventListener('click',arguments.callee,false);}, false);
	else if (activeCell.attachEvent)
		activeCell.attachEvent("onclick", function(){insertInput(this, activeCtrl);this.detachEventListener('onclick',arguments.callee);});
	activeCell.innerHTML=activeField.value;
	
	rowId=null;
	activeCell=null;
	activeValue='';
	activeField='';
	activeWhat='';
}

function updateField() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","UpdateAwardings.php?id="+rowId+"&field="+activeWhat+"&value="+encodeURIComponent(activeField.value) ,true);
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
							activeField.value=Value;
							activeCell.style.backgroundColor='';
							resetCell(Field);
						} else {
							activeCell.style.backgroundColor='yellow';
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