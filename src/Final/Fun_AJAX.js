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

function ChangeEvent(TeamEvent, whichForm, call, addAll) {
	if (call != null) {
		callChangeEventPhase=call;
	}

	if(whichForm != null) {
		need2Change = whichForm;
	}

	if(addAll != null) {
		addAllEvents = true;
	}

	$.getJSON(WebDir+"Final/ChangeEvent.php?Ev=" + $('#'+need2Change + '_Event').val() + "&TeamEvent=" + TeamEvent+((typeof ElimPool =='undefined' ) ? '' : '&ElimPool='+ElimPool), function(data) {
		if(data.error==0) {
			$('#'+need2Change + '_Phase').empty();
			$(data.good_phase).each(function() {
				$('#'+need2Change + '_Phase').append('<option value="'+this.code+'">'+this.name+'</option>');
			});

			if(data.set_points==0) {
				$('#'+need2Change + '_SetPoint').prop('disabled', true).val(0);
			} else {
				$('#'+need2Change + '_SetPoint').prop('disabled', false).val(1);
			}

			if (callChangeEventPhase) {
				ChangeEventPhase(TeamEvent);
			}
		}
	});
}


function ChangeEventPhase(TeamEvent, whichForm) {
	if(whichForm != null) {
		need2Change = whichForm;
	}

	var Ev=$('#'+need2Change + '_Event').val();
	var Ph=$('#'+need2Change + '_Phase').val();

	$.getJSON(WebDir+"Final/ChangeEventPhase.php?Ev=" + Ev + "&Ph=" + Ph + "&TeamEvent=" + TeamEvent, function(data) {
		if(data.error==0) {
			var selectedItem = $('#'+need2Change + '_Match').val();
			$('#'+need2Change + '_Match').empty();
			$(data.match).each(function() {
				$('#'+need2Change + '_Match').append('<option value="'+this.matchno1+'">'+this.name1+' - '+this.name2+'</option>');
			});

			if (Reload) {
				$('#'+need2Change + '_Match').val(selectedItem);
			}
		}
	});
}

function move2nextPhase(ev, match, team, value)
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
