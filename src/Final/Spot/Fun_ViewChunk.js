/*
	- CheckArrowString(Live,Event,MatchNo)
	Invia la GET a CheckArrowString.php
*/
var TimeCheck=800;
var XMLHttpCheckUpdate=CreateXMLHttpRequestObject();
var LastUpdate=''; 

function CheckUpdate() {
	if (XMLHttpCheckUpdate)
	{
		try
		{
			if (XMLHttpCheckUpdate.readyState==XHS_COMPLETE || XMLHttpCheckUpdate.readyState==XHS_UNINIT)
			{
				var QueryString 
					= '?LastUpdate=' + LastUpdate;
					
				XMLHttpCheckUpdate.open("GET","CheckLastUpdate.php" + QueryString, true);
//console.debug("CheckArrowString.php" + QueryString );
				XMLHttpCheckUpdate.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttpCheckUpdate.onreadystatechange=XMLHttpCheckUpdate_StateChange;
				XMLHttpCheckUpdate.send(null);
			}
		}
		catch (e)
		{
//console.debug('Errore: ' + e.toString());
		}
	}
}

function XMLHttpCheckUpdate_StateChange() {
	// se lo stato è Complete vado avanti
	if (XMLHttpCheckUpdate.readyState==XHS_COMPLETE) {
	// se lo status di HTTP è ok vado avanti
		if (XMLHttpCheckUpdate.status==200) {
			try {
				CheckUpdate_Response();
			} catch(e) {
//console.debug('Errore: ' + e.toString());
			}
		} else {
//console.debug('Errore: ' +XMLHttp.statusText);
		}
	}
}

function CheckUpdate_Response() {

// leggo l'xml
	var XMLResp=XMLHttpCheckUpdate.responseXML;
	var XMLRoot;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("");

	XMLRoot = XMLResp.documentElement;
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
	if (Error==0) {
		var tmp = XMLRoot.getElementsByTagName('lu').item(0).firstChild.data;
		
		if(tmp!=0) {
			CheckArrowString();
			LastUpdate=tmp;
		}
	} else {
//console.debug('Rilevato errore da XML di ritorno');
	}
	setTimeout("CheckUpdate()",TimeCheck);
}

function CheckArrowString() {
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString 
					= '?Event=' + Event
					+ '&MatchNo=' + MatchNo
					+ '&Team=' + Team
					+ '&Lock=' + Lock;
					
				XMLHttp.open("GET","ViewChunk.php" + QueryString, true);
//console.debug("CheckArrowString.php" + QueryString );
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=CheckArrowString_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
//console.debug('Errore: ' + e.toString());
		}
	}
}

function CheckArrowString_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CheckArrowString_Response();
			}
			catch(e)
			{
//console.debug('Errore: ' + e.toString());
			}
		}
		else
		{
//console.debug('Errore: ' +XMLHttp.statusText);
		}
	}
}

function CheckArrowString_Response()
{

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
	
	if (Error==0)
	{
		var NewEvent = XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
		var NewMatchNo = XMLRoot.getElementsByTagName('matchno').item(0).firstChild.data;
		var NewTeam = XMLRoot.getElementsByTagName('team').item(0).firstChild.data;
		var Reload=0;
		
		var Table = XMLRoot.getElementsByTagName('table').item(0).firstChild.data;
		
		document.getElementById('Content').innerHTML=Table;
		
		if(NewEvent>'' && NewMatchNo>=0 && NewTeam>=0) {
			Event=NewEvent;
			MatchNo=NewMatchNo;
			Team=NewTeam;
		}
		
		var Display = null;
		var Display2 = null;
		var FinalDisplay = null;
		if(document.getElementById('Display'))
			Display= document.getElementById('Display').value;
		if(document.getElementById('Display2'))
			Display2= document.getElementById('Display2').value;
		if(document.getElementById('FinalDisplay'))
			FinalDisplay= document.getElementById('FinalDisplay').value;	
	}
	else
	{
//console.debug('Rilevato errore da XML di ritorno');
	}
}

/*
- Fun_AJAX.js -
Contiene le funzioni ajax usate in tutta la sezione
*/ 		
var need2Change = 'd';
var callChangeEventPhase=false;
var ChangeXMLHttp=CreateXMLHttpRequestObject();
/*
- ChangeEvent(TeamEvent);
Invia la post a ChangeEvent.php.
Se TeamEvent=1 l'evento è di squadra
*/
function ChangeEvent(TeamEvent,whichForm,call) {
	if (call != null) callChangeEventPhase=call;

	if(whichForm != null) need2Change = whichForm;

	try {	
		if (ChangeXMLHttp.readyState==XHS_COMPLETE || ChangeXMLHttp.readyState==XHS_UNINIT) {
			var tmp=document.getElementById(need2Change + '_Event').value.split('-');
			Ev=tmp[1];
			TeamEvent=tmp[0];
			

			ChangeXMLHttp.open("POST",WebDir+"Final/ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent,true);
			//document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent;
			ChangeXMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			ChangeXMLHttp.onreadystatechange=ChangeEvent_StateChange;
			ChangeXMLHttp.send(null);
		}
	} catch (e) {	
		console.debug('Errore: ' + e.toString());
	}
}

function ChangeEvent_StateChange() {
	// se lo stato ? Complete vado avanti
	if (ChangeXMLHttp.readyState==XHS_COMPLETE) {
		// se lo status di HTTP ? ok vado avanti
		if (ChangeXMLHttp.status==200) {
			try {
				ChangeEvent_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		} else {
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function ChangeEvent_Response() {
	// leggo l'xml
	var XMLResp=ChangeXMLHttp.responseXML;
	//intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);
	
	//Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("");
	
	XMLRoot = XMLResp.documentElement;
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	//alert(Error);
	if (Error==0) {
		var StartPhase = XMLRoot.getElementsByTagName('start_phase').item(0).firstChild.data;
		var SetPoints = XMLRoot.getElementsByTagName('set_points').item(0).firstChild.data;
		
		var Combo = document.getElementById(need2Change + '_Phase');
		var CmbSP = document.getElementById(need2Change + '_SetPoint');
		
		if (Combo) {
			var Arr_Code = XMLRoot.getElementsByTagName('code');
			var Arr_Name = XMLRoot.getElementsByTagName('name');
		
			// Pulisco la select (tenendo conto del solito problema di IE e Konqueror con innerHTML)
			for (i = Combo.length - 1; i>=0; --i) Combo.remove(i);
		
			// aggiungo gli elementi
			for (i=0;i<Arr_Code.length;++i) Combo.options[i] = new Option(Arr_Name.item(i).firstChild.data,Arr_Code.item(i).firstChild.data);
		}
		
		if(CmbSP) {
			if(SetPoints==0) {
				CmbSP.selectedIndex=1;
				CmbSP.disabled=true;
			} else {
				CmbSP.disabled=false;
			}
		}
		
		if (callChangeEventPhase) {
			ChangeXMLHttp = CreateXMLHttpRequestObject();
			ChangeEventPhase();
		}
	}
}

function ChangeEventPhase(TeamEvent,whichForm) {
	if(whichForm != null) need2Change = whichForm;
	try {	
		if (ChangeXMLHttp.readyState==XHS_COMPLETE || ChangeXMLHttp.readyState==XHS_UNINIT) {
			var tmp=document.getElementById(need2Change + '_Event').value.split('-');
			var Ph=document.getElementById(need2Change + '_Phase').value;
			Ev=tmp[1];
			TeamEvent=tmp[0];
			
			ChangeXMLHttp.open("POST",WebDir+"Final/ChangeEventPhase.php?Ev=" + Ev + "&Ph=" + Ph + "&TeamEvent=" + TeamEvent,true);
			ChangeXMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			ChangeXMLHttp.onreadystatechange=ChangeEventPhase_StateChange;
			
			ChangeXMLHttp.send(null);
		}
	} catch (e) {	
	//console.debug('Errore: ' + e.toString());
	}
}

function ChangeEventPhase_StateChange() {
	// se lo stato è Complete vado avanti
	if (ChangeXMLHttp.readyState==XHS_COMPLETE) {
		// se lo status di HTTP è ok vado avanti
		if (ChangeXMLHttp.status==200) {
			try {
				ChangeEventPhase_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		} else {
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function ChangeEventPhase_Response() {
	// leggo l'xml
	var XMLResp=ChangeXMLHttp.responseXML;
	//intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);
	
	//Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("");
	
	XMLRoot = XMLResp.documentElement;
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	//alert(Error);
	if (Error==0) {
		var Combo = document.getElementById(need2Change + '_Match');
		var i;
		
		if (Combo) {
			// Pulisco la select (tenendo conto del solito problema di IE e Konqueror con innerHTML)
			for (i = Combo.length - 1; i>=0; --i) Combo.remove(i);
			
			var matchs=XMLRoot.getElementsByTagName('match');
			var matchNo1s=XMLRoot.getElementsByTagName('matchno1');
			var names1=XMLRoot.getElementsByTagName('name1');
			var names2=XMLRoot.getElementsByTagName('name2');
			
			for (i=0;i<matchs.length;++i) Combo.options[i] = new Option(names1.item(i).firstChild.data + ' - ' + names2.item(i).firstChild.data,matchNo1s.item(i).firstChild.data);
		}
	}
}
