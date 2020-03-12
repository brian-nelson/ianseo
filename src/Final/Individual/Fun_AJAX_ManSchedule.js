/*
													- Fun_AJAX_ManSchedule.js -
	Contiene le funzioni ajax usate da ManSchedule.php
*/
var CacheWrite = new Array();	// Cache per le scritture
/*
	Esegue una get a WriteDateTime.php
	Field è l'id del campo da aggiornare
*/
function WriteSchedule(Field)
{
	if (XMLHttp)
	{
		if (Field)
		{
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			CacheWrite.push(Field + "=" + FieldValue);
		}

		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && CacheWrite.length>0)
			{
				//var FieldValue = document.getElementById(Field).value;
				var FromCache=CacheWrite.shift();

				XMLHttp.open("POST","WriteDateTime.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=WriteSchedule_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function WriteSchedule_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				WriteSchedule_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function WriteSchedule_Response()
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
	var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;

	if (Error==0)
	{
		SetStyle(Which,'');
		document.getElementById(Which).value=XMLRoot.getElementsByTagName('value').item(0).firstChild.data;

	}
	else
	{
		SetStyle(Which,'error');
	}
	// per scaricare la cache degli update
	setTimeout("WriteSchedule()",600);
}

/*
	Esegue una get a WriteDateTimeAll.php
	Event,Phase indentificano la fase dell'evento;
*/
function WriteScheduleAll(Event,Phase)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString = '';
				var TimeSchedule=encodeURIComponent(document.getElementById('d_FSScheduledTimeAll_' + Event + '_' + Phase).value);
				QueryString
					= '?d_Event=' + Event
					+ '&d_Phase=' + Phase
					+ '&d_FSScheduledDateAll=' + document.getElementById('d_FSScheduledDateAll_' + Event + '_' + Phase).value
					+ '&d_FSScheduledTimeAll=' + TimeSchedule;
				if(document.getElementById('d_FSScheduledLenAll_' + Event + '_' + Phase))
					QueryString += '&d_FSScheduledLenAll=' + document.getElementById('d_FSScheduledLenAll_' + Event + '_' + Phase).value
				XMLHttp.open("GET","WriteDateTimeAll.php" + QueryString,true);
				//document.getElementById('idOutput').innerHTML="WriteDateTimeAll.php" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=WriteScheduleAll_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function WriteScheduleAll_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				WriteScheduleAll_Response();
			}
			catch(e)
			{
			//	document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function WriteScheduleAll_Response()
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
	var Event =XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
	var Phase =XMLRoot.getElementsByTagName('phase').item(0).firstChild.data;
	var DtId = document.getElementById('d_FSScheduledDateAll_' + Event + '_' + Phase).id;
	var HrId = document.getElementById('d_FSScheduledTimeAll_' + Event + '_' + Phase).id;
	var LnId = (document.getElementById('d_FSScheduledLenAll_' + Event + '_' + Phase) ? document.getElementById('d_FSScheduledLenAll_' + Event + '_' + Phase).id : 0);

	if (Error==0)
	{
		SetStyle(DtId,'');
		SetStyle(HrId,'');
		if(document.getElementById('d_FSScheduledLenAll_' + Event + '_' + Phase))
			SetStyle(LnId,'');

		var Dt =XMLRoot.getElementsByTagName('date').item(0).firstChild.data;
		var Hr =XMLRoot.getElementsByTagName('time').item(0).firstChild.data;
		var Ln =XMLRoot.getElementsByTagName('len').item(0).firstChild.data;
		var Arr_MatchNo =XMLRoot.getElementsByTagName('matchno');

		for (i=0;i<Arr_MatchNo.length;++i)
		{
		    var elem=document.getElementById('d_FSScheduledDate_' + Event + '_' + Arr_MatchNo.item(i).firstChild.data);
		    if(elem) {
                document.getElementById('d_FSScheduledDate_' + Event + '_' + Arr_MatchNo.item(i).firstChild.data).value=Dt;
                document.getElementById('d_FSScheduledTime_' + Event + '_' + Arr_MatchNo.item(i).firstChild.data).value=Hr;
                if(document.getElementById('d_FSScheduledLenAll_' + Event + '_' + Phase)) {
                    document.getElementById('d_FSScheduledLen_' + Event + '_' + Arr_MatchNo.item(i).firstChild.data).value=Ln;
                }
            }
		}
	}
	else
	{
		SetStyle(DtId,'error');
		SetStyle(HrId,'error');
		if(document.getElementById('d_FSScheduledLenAll_' + Event + '_' + Phase))
			SetStyle(LnId,'error');
	}
}