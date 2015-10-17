var RowNodes = {
		enStatus:0, 
		enSession:1, 
		enTargetno:2,
		enCode:3,
		enFirstname:4,
		enName:5,
		enDob:6,
		enSex:7,
		enCountry_code:8,
		enCountry_name:9,
		enDivision:10,
		enAgeclass:11,
		enClass:12,
		enSubclass:13,
		enTargetface_name:14
	};

var rowId=null;
var enId=0;
var activeCell=null;
var activeValue='';
var activeField='';
var activeWhat='';


function insertInput(cell, what){
	var url='';
	
	if(rowId) resetCell();
	
	if(!XMLHttp) return;
	
	rowId=cell.parentNode.id;
	var tmp=rowId.split('_');
	enId=tmp[2];
	
	activeCell=cell;
	activeValue=cell.innerHTML;
	
	activeCell.onclick=null;
	
	activeWhat=what;
	
	switch(what) {
	case 'subclass':
		url='Get-Subclasses.php';
		break;
	case 'firstname':
		break;
	case 'name':
		break;
	}
	
	if(url>'') { // only combos have to get the correct data!
		createComboField(url);
	} else {
		createTextField();
	}
}

function createTextField() {
	activeField=document.createElement('input');
	if (activeField.addEventListener)
		activeField.addEventListener("blur", updateField, false);
	else if (activeField.attachEvent)
		activeField.attachEvent("onblur", updateField);

	activeField.value=activeValue;

	while(activeCell.childNodes.length > 0) activeCell.removeChild(activeCell.childNodes.item(0));
	activeCell.appendChild(activeField);
	activeField.focus();
}

function createComboField(url) {
	try {	
		if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) ) {
			XMLHttp.open("GET", url, true);
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=createComboField_StateChange;
			XMLHttp.send(null);
		}
	} catch (e) {
	}
}

function createComboField_StateChange() {
	// if status not ready or error returns
	if (XMLHttp.readyState!=XHS_COMPLETE) return;
	if(XMLHttp.status!=200) {
		setError();
		return;
	}

	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");
	
	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
	//alert(Error);
	if (Error==1) {
		activeCell.style='error';
	} else {
		activeCell.style='';
				
		activeField=document.createElement('select');
		if (activeField.addEventListener)
			activeField.addEventListener("blur", updateField, false);
		else if (activeField.attachEvent)
			activeField.attachEvent("onblur", updateField);

		var opt = document.createElement('option');
		opt.text='--';
		opt.value='';
		try {
			activeField.add(opt,null); // standard
		} catch(ex) {
			activeField.add(opt); // IE ....
		}
		
		var Specs = XMLRoot.getElementsByTagName('items').item(0).firstChild.data;
		
		if (Specs!='')  {
			var Fields = Specs.split('---');
			
			for (var i=0;i<Fields.length;++i) {
				var opt = document.createElement('option');
				var KeyVal=Fields[i].split(':::');
				opt.value=KeyVal[0];
				opt.text=KeyVal[1];
				if(activeValue==KeyVal[0] || Fields.length == 1) opt.selected=true
				try {
					activeField.add(opt,null); // standard
				} catch(ex) {
					activeField.add(opt); // IE ....
				}				
			}
		}
		while(activeCell.childNodes.length > 0) activeCell.removeChild(activeCell.childNodes.item(0));
		activeCell.appendChild(activeField);
		activeField.focus();
	}
}

function updateField() {
	if(!XMLHttp) return;
	
	if(activeField.value == activeValue) {
		resetCell();
		return;
	}

	try {	
		if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) ) {
			var session=(activeWhat=='session' ? activeValue : activeCell.parentNode.childNodes.item(RowNodes.enSession).innerHTML);
			var targetno=(activeWhat=='targetno' ? activeValue : activeCell.parentNode.childNodes.item(RowNodes.enTargetno).innerHTML);
			XMLHttp.open("GET", 'Set-UpdateField.php?id=' + enId 
					+ '&session=' + session
					+ '&targetno=' + targetno
					+ '&field=' + activeWhat 
					+ '&value=' + activeField.value, true);
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=updateField_StateChange;
			XMLHttp.send(null);
		}
	} catch (e) {
	}
}

function updateField_StateChange() {
	// if status not ready or error returns
	if (XMLHttp.readyState!=XHS_COMPLETE) return;
	if(XMLHttp.status!=200) {
		setError();
		return;
	}

	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");
	
	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
	//alert(Error);
	activeValue=activeField.value;
	
	var Update = XMLRoot.getElementsByTagName('update').item(0).firstChild.data;
	var Value = XMLRoot.getElementsByTagName('value').item(0).firstChild.data;
	if(Update==0 && Error==0) {
		var tmp=rowId.split('_');
		tmp[2]=XMLRoot.getElementsByTagName('id').item(0).firstChild.data
		activeCell.parentNode.id = tmp.join('_') ;
		enId=tmp[2];
	}
	
	activeField.value=Value;
	
	if(Error==1) activeCell.style.backgroundColor='yellow';
	else activeCell.style.backgroundColor='';
	resetCell();
}

function setError() {
	activeCell.style='error';
	resetCell();
}

function resetCell() {
	switch(activeWhat) {
	case 'subclass':
		if (activeCell.addEventListener)
			activeCell.addEventListener("click", function(){insertInput(this, 'subclass');this.removeEventListener('click',arguments.callee,false);}, false);
		else if (activeCell.attachEvent)
			activeCell.attachEvent("onclick", function(){insertInput(this, 'subclass');this.detachEventListener('onclick',arguments.callee);});
		break;
	case 'firstname':
		if (activeCell.addEventListener)
			activeCell.addEventListener("click", function(){insertInput(this, 'firstname');this.removeEventListener('click',arguments.callee,false);}, false);
		else if (activeCell.attachEvent)
			activeCell.attachEvent("onclick", function(){insertInput(this, 'firstname');this.detachEventListener('onclick',arguments.callee);});
		break;
	case 'name':
		if (activeCell.addEventListener)
			activeCell.addEventListener("click", function(){insertInput(this, 'name');this.removeEventListener('click',arguments.callee,false);}, false);
		else if (activeCell.attachEvent)
			activeCell.attachEvent("onclick", function(){insertInput(this, 'name');this.detachEventListener('onclick',arguments.callee);});
		break;
	}
	
	activeCell.innerHTML=activeField.value;
	
	rowId=null;
	enId=0;
	activeCell=null;
	activeValue='';
	activeField='';
	activeWhat='';
}

