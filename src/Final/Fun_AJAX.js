/*
													- Fun_AJAX.js -
	Contiene le funzioni ajax usate in tutta la sezione
*/
var need2Change = 'd';
var callChangeEventPhase=false;
var addAllEvents=false;
var Reload=false;
/*
	- ChangeEvent(TeamEvent);
	Invia la post a ChangeEvent.php.
	Se TeamEvent=1 l'evento è di squadra
*/
function ChangeEvent(TeamEvent,whichForm,call,addAll)
{
	if (call != null)
		callChangeEventPhase=call;

	if(whichForm != null)
		need2Change = whichForm;

	if(addAll != null)
		addAllEvents = true;

	try
	{
		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
		{
			var Ev=document.getElementById(need2Change + '_Event').value;
			//alert(Ev);

			XMLHttp.open("POST",WebDir+"Final/ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent+((typeof ElimPool =='undefined' ) ? '' : '&ElimPool='+ElimPool),true);
// 			document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=ChangeEvent_StateChange;
			XMLHttp.send(null);
		}
	}
	catch (e)
	{
//		console.debug('Errore: ' + e.toString());
	}
}

function ChangeEvent_StateChange()
{
	// se lo stato ? Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP ? ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				ChangeEvent_Response();
			}
			catch(e)
			{
				document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function ChangeEvent_Response()
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
	//alert(Error);
	if (Error==0)
	{
		var StartPhase = XMLRoot.getElementsByTagName('start_phase').item(0).firstChild.data;
		var SetPoints = XMLRoot.getElementsByTagName('set_points').item(0).firstChild.data;

		var Combo = document.getElementById(need2Change + '_Phase');
		var CmbSP = document.getElementById(need2Change + '_SetPoint');

		if (Combo)
		{
			var Arr_Code = XMLRoot.getElementsByTagName('code');
			var Arr_Name = XMLRoot.getElementsByTagName('name');

			// Pulisco la select (tenendo conto del solito problema di IE e Konqueror con innerHTML)
			for (i = Combo.length - 1; i>=0; --i)
				Combo.remove(i);

			// aggiungo gli elementi
			j=0;
			if(addAllEvents)
				Combo.options[j++] = new Option(AllEvents,"");
			for (i=0+j;i<Arr_Code.length+j;++i)
				Combo.options[i] = new Option(Arr_Name.item(i-j).firstChild.data,Arr_Code.item(i-j).firstChild.data);
		}

		if(CmbSP)
		{
			if(SetPoints==0)
			{
				CmbSP.selectedIndex=1;
				CmbSP.disabled=true;
			}
			else
				CmbSP.disabled=false;
		}

		if (callChangeEventPhase)
		{
			var team = XMLRoot.getElementsByTagName('team').item(0).firstChild.data;

			XMLHttp = CreateXMLHttpRequestObject();
			ChangeEventPhase(team);
		}
	}
}

function ChangeEventPhase(TeamEvent,whichForm) {
	if(whichForm != null)
		need2Change = whichForm;

	try {
		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
		{
			var Ev=document.getElementById(need2Change + '_Event').value;
			var Ph=document.getElementById(need2Change + '_Phase').value;

			XMLHttp.open("POST",WebDir+"Final/ChangeEventPhase.php?Ev=" + Ev + "&Ph=" + Ph + "&TeamEvent=" + TeamEvent,true);
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=function() {
                // se lo stato è Complete vado avanti
                if (XMLHttp.readyState==XHS_COMPLETE) {
                    // se lo status di HTTP è ok vado avanti
                    if (XMLHttp.status==200) {
                        try {
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
                            if (Error==0)
                            {
                                var Combo = document.getElementById(need2Change + '_Match');
                                var i;

                                if (Combo) {
                                    var selectedItem=Combo.value;
                                    // Pulisco la select (tenendo conto del solito problema di IE e Konqueror con innerHTML)
                                    for (i = Combo.length - 1; i>=0; --i)
                                        Combo.remove(i);

                                    var matchs=XMLRoot.getElementsByTagName('match');
                                    var matchNo1s=XMLRoot.getElementsByTagName('matchno1');
                                    var names1=XMLRoot.getElementsByTagName('name1');
                                    var names2=XMLRoot.getElementsByTagName('name2');

                                    for (i=0;i<matchs.length;++i) {
                                        Combo.options[i] = new Option(names1.item(i).firstChild.data + ' - ' + names2.item(i).firstChild.data,matchNo1s.item(i).firstChild.data);
                                    }

                                    if(Reload) {
                                        Combo.value=selectedItem;
                                    }
                                }
                            }
                        } catch (e) {
                            document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
                        }
                    } else {
                        document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
                    }
                }
            };

			XMLHttp.send(null);
		}
	}
	catch (e)
	{
//		console.debug('Errore: ' + e.toString());
	}
}

function move2nextPhase(ev,match,team, value)
{
	try
	{
		if (ev=='') return;

		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
		{
			XMLHttp.open("GET",WebDir+"Final/Move2NextPhase.php?event=" + ev + "&match="  +  match + "&team=" + team + "&pool="+value+"&ts=" + ts4qs(),true);
// 			document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=move2nextPhase_StateChange;
			XMLHttp.send(null);
		}
	}
	catch (e)
	{
		console.debug('Errore: ' + e.toString());
	}
}

function move2nextPhase_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				move2nextPhase_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function move2nextPhase_Response()
{
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
	var msg=XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	if(XMLRoot.getAttribute('action')=='reload') {
        Reload=true;
        ChangeEventPhase(TeamEvent);
    }

	alert(msg);
}