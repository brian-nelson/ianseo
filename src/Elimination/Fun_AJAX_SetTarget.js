
function WriteSchedule(Field) {
    $.get('../Final/Individual/WriteDateTime.php?'+Field.id + "=" +encodeURIComponent(Field.value), function(data) {
        if($(data).find('error').text()=='0') {
            $('#d_FSScheduledDate_'+$(data).find('e').text()+'_'+$(data).find('m').text()).val($(data).find('date').text());
            $('#d_FSScheduledTime_'+$(data).find('e').text()+'_'+$(data).find('m').text()).val($(data).find('time').text());
            $('#d_FSScheduledLen_'+$(data).find('e').text()+'_'+$(data).find('m').text()).val($(data).find('len').text());
        }
    }, 'XML');
}

function CloneSchedule(obj) {
    $.getJSON('SetTarget-Schedule.php?dest='+$(obj).attr('event')+'&org='+$(obj).closest('div').find('select').val(), function(data) {
        if(data.error==0) {
            var first=true;
            $(data.items).each(function(item) {
                if(first) {
                    $('[id^="d_FSScheduledDate_'+this.FSEvent+'"]').val('');
                    $('[id^="d_FSScheduledTime_'+this.FSEvent+'"]').val('');
                    $('[id^="d_FSScheduledLen_'+this.FSEvent+'"]').val('');
                    first=false;
                }
                $('#d_FSScheduledDate_'+this.FSEvent+'_'+this.FSMatchNo).val(this.FSScheduledDate);
                $('#d_FSScheduledTime_'+this.FSEvent+'_'+this.FSMatchNo).val(this.FSScheduledTime);
                $('#d_FSScheduledLen_'+this.FSEvent+'_'+this.FSMatchNo).val(this.FSScheduledLen);
            });
        }
    });
}

function UpdateTargetNo(Field) {
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				var FieldName = encodeURIComponent(Field);
				var FieldValue= encodeURIComponent(document.getElementById(Field).value);
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("POST","UpdateTargetNo.php?" + FieldName + "=" + FieldValue,true);
					//document.getElementById('idOutput').innerHTML="UpdateTargetNo.php?FieldName + "=" + FieldValue;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=UpdateTargetNo_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateTargetNo_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateTargetNo_Response();
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

function UpdateTargetNo_Response()
{

	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);

// Intercetto gli errori }di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	//alert(Error);
	var Arr_Id = XMLRoot.getElementsByTagName('key');
	var Arr_Value = XMLRoot.getElementsByTagName('value');
	var Arr_Error = XMLRoot.getElementsByTagName('fieldError');
	var which= XMLRoot.getElementsByTagName('which').item(0).firstChild.data;

	//alert(Id);

	if (Error==1)
	{
		SetStyle('d_q_ElTargetNo_' + which ,'error');
	}
	else
	{
		SetStyle('d_q_ElTargetNo_' + which ,'');

	// e adesso giro per i campi
		for (var i=0;i<Arr_Id.length;++i)
		{
			var el=document.getElementById('d_q_ElTargetNo_' + Arr_Id.item(i).firstChild.data);

			if (el)
			{
				if (Arr_Error.item(i).firstChild.data==0)
				{
					el.value=Arr_Value.item(i).firstChild.data;

					SetStyle('d_q_ElTargetNo_' + Arr_Id.item(i).firstChild.data ,'');

					FindRedTarget();
				}
				else
				{
					SetStyle('d_q_ElTargetNo_' + Arr_Id.item(i).firstChild.data ,'warning');
				}
			}
		}
	}
}

function UpdateSession(Field)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				var FieldName = encodeURIComponent(Field);
				var FieldValue= encodeURIComponent(document.getElementById(Field).value);
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("POST","UpdateSession.php?" + FieldName + "=" + FieldValue,true);
					//document.getElementById('idOutput').innerHTML="UpdateTargetNo.php?FieldName + "=" + FieldValue;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=UpdateSession_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateSession_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateSession_Response();
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

function UpdateSession_Response()
{

	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);

// Intercetto gli errori }di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	//alert(Error);
	var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	var Ses = XMLRoot.getElementsByTagName('ses').item(0).firstChild.data;
	//alert(Id);

	if (Error==1)
	{
		SetStyle('d_q_ElSession_' + Id ,'error');
	}
	else
	{
		SetStyle('d_q_ElSession_' + Id ,'');

		document.getElementById('d_q_ElSession_' + Id ).value=Ses;

	}
	FindRedTarget();

}

/*
	- FindRedTarget()
	Esegue la POST a FindRedTarget.php
	Cerca i paglioni doppi nella sessione Ses.

*/
function FindRedTarget()
{
	XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				XMLHttp.open("POST","FindRedTarget.php",true);
				//document.getElementById('idOutput').innerHTML="FindRedTarget.php?Ses=" + Ses;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=FindRedTarget_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function FindRedTarget_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				FindRedTarget_Response();
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

function FindRedTarget_Response()
{

	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);

// Intercetto gli errori }di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	//alert(Error);

	if (Error==0)
	{
		var Arr_Id = XMLRoot.getElementsByTagName('id');
		var Arr_Good = XMLRoot.getElementsByTagName('good');

		for (i=0;i<Arr_Id.length;++i)
		{
			var el=document.getElementById('d_q_ElTargetNo_' + Arr_Id.item(i).firstChild.data);

			if(el)
			{
				if (Arr_Good.item(i).firstChild.data==0)
				{
					//console.debug(Arr_Id.item(i).firstChild.data);
					SetStyle('d_q_ElTargetNo_' + Arr_Id.item(i).firstChild.data,'red');
				}
				else
				{
					if (el.className!='warning')
						SetStyle('d_q_ElTargetNo_' + Arr_Id.item(i).firstChild.data,'');
				}
			}
		}
	}
}
