/*
													- Fun_AJAX_ListEvents.js -
	Contiene le funzioni ajax usate da ListEvents.php
*/ 	


/*
	Invia la get a UpdateFieldEventList.php
	per aggiornare il campo Field
*/
function UpdateField(Field) {
	if (XMLHttp) {	
		try {
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)	{
				var FieldValue = encodeURIComponent(document.getElementById(Field).value);
				
				XMLHttp.open("GET","UpdateFieldEventList.php?" + encodeURIComponent(Field) + "=" + FieldValue,true);
				//document.getElementById('idOutput').innerHTML="UpdateFieldEventList.php?" + Field + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateField_StateChange;
				XMLHttp.send(null);
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function UpdateField_StateChange() {
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE) {
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200) {
			try {
				UpdateField_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		} else {
			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}


function UpdateField_Response() {
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;	
	
	var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
	
	if (Error==1)
		SetStyle(Which,'error');
	else
		SetStyle(Which,'');
}

/*
	Invia la get a DeleteEvent.php
	Event è l'evento da eliminare.
	Msg è il messaggio di conferma
*/
function DeleteEvent(Event,Msg) {
	if (XMLHttp) {	
		try {
			if (confirm(Msg.replace(/\+/g," "))) {
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {				
					XMLHttp.open("GET","DeleteEvent.php?EvCode=" + Event,true);
					//document.getElementById('idOutput').innerHTML="DeleteEvent.php?Event=" + Event;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=DeleteEvent_StateChange;
					XMLHttp.send(null);
				}
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function DeleteEvent_StateChange() {
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE) {
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200) {
			try {
				DeleteEvent_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		} else {
			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function DeleteEvent_Response() {
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;	
	
	var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
	if (Error==0) {
		var Event=XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
		
		var tbody=document.getElementById('tbody');
		
		var Row=document.getElementById('Row_' + Event);
		if (Row)
			tbody.removeChild(Row);
	}
}

/*
	Invia la get a UpdatePhase.php
	Event è l'evento per cui si cambia la fase di inizio,
	OldValue è il vecchio valore da ripristinare in caso
	si voglia annullare l'operazione.
	Msg è il messaggio di conferma
*/
function UpdatePhase(Event,OldValue,Msg) {
	if (XMLHttp) {	
		try {
			if (confirm(Msg)) {
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {				
					NewPhase= document.getElementById('d_EvFinalFirstPhase_' + Event).value;
					XMLHttp.open("GET","UpdatePhase.php?EvCode=" + Event + "&NewPhase=" + NewPhase,true);
					//document.getElementById('idOutput').innerHTML="UpdatePhase.php?EvCode=" + Event + "&NewPhase=" + NewPhase;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=UpdatePhase_StateChange;
					XMLHttp.send(null);
				}
			} else {
					document.getElementById('d_EvFinalFirstPhase_' + Event).value=OldValue;
			}
		} catch (e)	{
			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function UpdatePhase_StateChange() {
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE) {
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200) {
			try {
				UpdatePhase_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		} else {
			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function UpdatePhase_Response() {
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;	
	
	var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	var Event=XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
	
	if (Error==1)
		SetStyle('d_EvFinalFirstPhase_','error');
	else
		SetStyle('d_EvFinalFirstPhase_','');
}

/*
	Invia la get a AddEvent.php
	per creare un nuovo evento
	ErrMsg è il messaggio di errore nel caso non si possa proseguire
*/
function AddEvent(ErrMsg) {
	if (XMLHttp) {	
		try {
			if (document.getElementById('New_EvCode').value!='' && document.getElementById('New_EvEventName').value!='' && document.getElementById('New_EvProgr').value!='') {
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) 	{
					var New_EvCode = encodeURIComponent(document.getElementById('New_EvCode').value);
					var New_EvEventName = encodeURIComponent(document.getElementById('New_EvEventName').value);
					var New_EvProgr = encodeURIComponent(document.getElementById('New_EvProgr').value);
					var New_EvMatchMode = encodeURIComponent(document.getElementById('New_EvMatchMode').value);
					var New_EvFinalFirstPhase = encodeURIComponent(document.getElementById('New_EvFinalFirstPhase').value);
					var New_EvFinalTargetType = encodeURIComponent(document.getElementById('New_EvFinalTargetType').value);
					var New_EvTargetSize = encodeURIComponent(document.getElementById('New_EvTargetSize').value);
					var New_EvDistance = encodeURIComponent(document.getElementById('New_EvDistance').value);
					
					var QueryString
						= 'New_EvCode=' + New_EvCode + '&'
						+ 'New_EvEventName=' + New_EvEventName + '&'
						+ 'New_EvProgr=' + New_EvProgr + '&'
						+ 'New_EvMatchMode=' + New_EvMatchMode + '&'
						+ 'New_EvFinalFirstPhase=' + New_EvFinalFirstPhase + '&'
						+ 'New_EvFinalTargetType=' + New_EvFinalTargetType + '&' 
						+ 'New_EvTargetSize=' + New_EvTargetSize + '&'
						+ 'New_EvDistance=' + New_EvDistance;

						
					XMLHttp.open("GET","AddEvent.php?" + QueryString,true);
					//document.getElementById('idOutput').innerHTML="AddEvent.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddEvent_StateChange;
					XMLHttp.send(null);
				}
			} else
				alert(ErrMsg.replace(/\+/g," "));
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function AddEvent_StateChange() {
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE) {
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200) {
			try {
				AddEvent_Response();
			} catch(e) {
				document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		} else {
			document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function AddEvent_Response() {
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;	
	
	var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
	if (Error==0) {
		var NewEvCode = XMLRoot.getElementsByTagName('new_evcode').item(0).firstChild.data;
		var NewEvEventName = XMLRoot.getElementsByTagName('new_eveventname').item(0).firstChild.data;
		var NewEvProgr = XMLRoot.getElementsByTagName('new_evprogr').item(0).firstChild.data;
		var NewEvMatchMode = XMLRoot.getElementsByTagName('new_evmatchmode').item(0).firstChild.data;
		var NewEvFinalFirstPhase = XMLRoot.getElementsByTagName('new_evfinalfirstphase').item(0).firstChild.data;
		var NewEvFinalTargetType = XMLRoot.getElementsByTagName('new_evfinaltargettype').item(0).firstChild.data;
		
		var Arr_PhaseId = XMLRoot.getElementsByTagName('phase_id');
		var Arr_PhaseName = XMLRoot.getElementsByTagName('phase_name');
		var Arr_TarId = XMLRoot.getElementsByTagName('tar_id');
		var Arr_TarDescr = XMLRoot.getElementsByTagName('tar_descr');
		var Arr_MatchModeId = XMLRoot.getElementsByTagName('matchmode_id');
		var Arr_MatchModeDescr = XMLRoot.getElementsByTagName('matchmode_descr');
		
		var NewEvTargetSize = XMLRoot.getElementsByTagName('new_evtargetsize').item(0).firstChild.data;
		var NewEvDistance = XMLRoot.getElementsByTagName('new_evdistance').item(0).firstChild.data;
		
		var ConfirmMsg = XMLRoot.getElementsByTagName('confirm_msg').item(0).firstChild.data;
		
		var tbody=document.getElementById('tbody');
		
		ComboPhase 
			= '<select name="d_EvFinalFirstPhase_' + NewEvCode + '" id="d_EvFinalFirstPhase_' + NewEvCode + '" onChange="javascript:UpdatePhase(\'' + NewEvCode + '\',' + NewEvFinalFirstPhase 	+ ',\'' + ConfirmMsg + '\');">';
		for (i=0;i<Arr_PhaseId.length;++i)
			ComboPhase+= '<option value="' + Arr_PhaseId.item(i).firstChild.data + '"' + (NewEvFinalFirstPhase==Arr_PhaseId.item(i).firstChild.data ? ' selected' : '') + '>' + Arr_PhaseName.item(i).firstChild.data + '</option>';
		ComboPhase+= '</select>';
		
		ComboMatchMode
			= '<select name="d_EvMatchMode_' + NewEvCode + '" id="d_EvMatchMode_' + NewEvCode + '" onChange="javascript:UpdateField(\'d_EvMatchMode_' + NewEvCode + '\');">';
		for (i=0;i<Arr_MatchModeId.length;++i)
			ComboMatchMode+= '<option value="' + Arr_MatchModeId.item(i).firstChild.data + '"' + (NewEvMatchMode==Arr_MatchModeId.item(i).firstChild.data ? ' selected' : '') + '>' + Arr_MatchModeDescr.item(i).firstChild.data + '</option>';
		ComboMatchMode+= '</select>';
		
		ComboTarget 
			= '<select name="d_EvFinalFirstPhase_' + NewEvCode + '" id="d_EvFinalFirstPhase_' + NewEvCode + '" onChange="javascript:UpdateField(\'d_EvFinalTargetType_' + NewEvCode + '\');">';
		for (i=0;i<Arr_TarId.length;++i)
			ComboTarget+= '<option value="' + Arr_TarId.item(i).firstChild.data + '"' + (NewEvFinalTargetType==Arr_TarId.item(i).firstChild.data ? ' selected' : '') + '>' + Arr_TarDescr.item(i).firstChild.data + '</option>';
		ComboTarget+= '</select>';
		
	// aggiungo la nuova riga prima dell'ultima
		
		var NewRow = document.createElement('TR');
		NewRow.id='Row_' + NewEvCode;
		
		var TD_EvCode = document.createElement('TD');
		TD_EvCode.className='Center';
		TD_EvCode.innerHTML
			= '<a class="Link" href="SetEventRules.php?EvCode=' + NewEvCode + '">' + NewEvCode + '</a>';
		
		var TD_EvEventName = document.createElement('TD');
		TD_EvEventName.className='Center';
		TD_EvEventName.innerHTML
			= '<input type="text" size="64" maxlength="64" name="d_EvEventName_' + NewEvCode + '" id="d_EvEventName_' + NewEvCode + '" value="' + NewEvEventName + '" onBlur="javascript:UpdateField(\'d_EvEventName_' + NewEvCode + '\');">';
		
		var TD_ComboMatchMode = document.createElement('TD');
		TD_ComboMatchMode.className='Center';
		TD_ComboMatchMode.innerHTML	= ComboMatchMode;
		
		var TD_EvProgr = document.createElement('TD');
		TD_EvProgr.className='Center';
		TD_EvProgr.innerHTML
			= '<input type="text" size="3" maxlength="3" name="d_EvProgr_' + NewEvCode + '" id="d_EvProgr_' + NewEvCode + '" value="' + NewEvProgr + '" onBlur="javascript:UpdateField(\'d_EvProgr_' + NewEvCode + '\');">';
			
		var TD_ComboPhase = document.createElement('TD');
		TD_ComboPhase.className='Center';
		TD_ComboPhase.innerHTML	= ComboPhase;
		
		var TD_ComboTarget = document.createElement('TD');
		TD_ComboTarget.className='Center';
		TD_ComboTarget.innerHTML= ComboTarget;

		var TD_EvTargetSize = document.createElement('TD');
		TD_EvTargetSize.className='Center';
		TD_EvTargetSize.innerHTML
			= '<input type="text" size="3" maxlength="3" name="d_EvTargetSize_' + NewEvCode + '" id="d_EvTargetSize_' + NewEvCode + '" value="' + NewEvTargetSize + '" onBlur="javascript:UpdateField(\'d_EvTargetSize_' + NewEvCode + '\');">';
		
		var TD_EvDistance = document.createElement('TD');
		TD_EvDistance.className='Center';
		TD_EvDistance.innerHTML
			= '<input type="text" size="12" maxlength="10" name="d_EvDistance_' + NewEvCode + '" id="d_EvDistance_' + NewEvCode + '" value="' + NewEvDistance + '" onBlur="javascript:UpdateField(\'d_EvDistance_' + NewEvCode + '\');">';
		
		var TD_Delete = document.createElement('TD');
		TD_Delete.className='Center';
		TD_Delete.innerHTML
			= '<a href="javascript:DeleteEvent(\'' + NewEvCode + '\',\'' + ConfirmMsg + '\');"><img src="../../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
		
		NewRow.appendChild(TD_EvCode);
		NewRow.appendChild(TD_EvEventName);
		NewRow.appendChild(TD_EvProgr);
		NewRow.appendChild(TD_ComboMatchMode);
		NewRow.appendChild(TD_ComboPhase);
		NewRow.appendChild(TD_ComboTarget);
		NewRow.appendChild(TD_EvTargetSize);
		NewRow.appendChild(TD_EvDistance);
		NewRow.appendChild(TD_Delete);
		
		tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
		
		document.getElementById('New_EvCode').value='';
		document.getElementById('New_EvEventName').value='';
		document.getElementById('New_EvProgr').value='';
		document.getElementById('New_EvFinalFirstPhase').selectedIndex=0;
		document.getElementById('New_EvFinalTargetType').selectedIndex=0;
		document.getElementById('New_EvTargetSize').value='';
		document.getElementById('New_EvDistance').value='';
	}
}
