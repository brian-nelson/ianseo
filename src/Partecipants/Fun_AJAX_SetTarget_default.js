/*
													- Fun_AJAX_SetTarget_default.js -
	Contiene le funzioni ajax che riguardano la pagina SetTarget_default.php
*/ 		

/*
	- UpdateSession(Field)
	Invia la POST a UpdateSession.php
*/
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
					//document.getElementById('idOutput').innerHTML="UpdateSession.php?" + FieldName + "=" + FieldValue;
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
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	//alert(Error);
	if (Error==0)
	{
		var Troppi = XMLRoot.getElementsByTagName('troppi').item(0).firstChild.data;
		var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
		
		if (Troppi==1)
		{
			document.getElementById('d_q_QuSession_' + Id).value=0;
			var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;
			alert(Msg);
		}
		
		if (document.getElementById('d_q_QuSession_' + Id).value==0)
		{
			document.getElementById('d_q_QuTargetNo_' + Id).value='';
			document.getElementById('d_q_QuTargetNo_' + Id).readOnly=true;
		}
		else
		{
			document.getElementById('d_q_QuTargetNo_' + Id).readOnly=false;
		}
		
		var NewTargetNo = XMLRoot.getElementsByTagName('new_targetno').item(0).firstChild.data;
		if (NewTargetNo=='#')
			document.getElementById('d_q_QuTargetNo_' + Id).value='';
	}
}

/*
	- UpdateTargetNo(Field,Ses)
	Invia la POST a UpdateTargetNo.php
	Ses contiene la sessione che può valere '*' e serve per chiamare FindRedTarget
*/
function UpdateTargetNo(Field,Ses)
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
					XMLHttp.open("POST","UpdateTargetNo.php?Ses=" + Ses + "&" + FieldName + "=" + FieldValue,true);
					//document.getElementById('idOutput').innerHTML="UpdateTargetNo.php?Ses=" + Ses + "&" + FieldName + "=" + FieldValue;
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
	var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	//alert(Id);
	
	if (Error==1)
	{
		SetStyle('d_q_QuTargetNo_' + Id,'error');		
	}
	else
	{
		SetStyle('d_q_QuTargetNo_' + Id,'');		
		var PadValue = XMLRoot.getElementsByTagName('pad_value').item(0).firstChild.data;
		document.getElementById('d_q_QuTargetNo_' + Id).value=(PadValue!='#' ? PadValue : '');
		
		var Ses = XMLRoot.getElementsByTagName('ses').item(0).firstChild.data;
		XMLHttp = CreateXMLHttpRequestObject();
		FindRedTarget(Ses);
	}
}

/*
	- FindRedTarget(Ses)
	Esegue la POST a FindRedTarget.php
	Cerca i paglioni doppi nella sessione Ses.
	Ses se vale '*' significa 'tutte le sessioni'
*/
function FindRedTarget(Ses)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				XMLHttp.open("POST","FindRedTarget.php?Ses=" + Ses,true);
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
		var Arr_Num = XMLRoot.getElementsByTagName('num');
		for (i=0;i<Arr_Id.length;++i)
		{
			if(document.getElementById('d_q_QuTargetNo_' + Arr_Id.item(i).firstChild.data))
			{
				if (Arr_Num.item(i).firstChild.data!=1)
					SetStyle('d_q_QuTargetNo_' + Arr_Id.item(i).firstChild.data,'red');
				else
					SetStyle('d_q_QuTargetNo_' + Arr_Id.item(i).firstChild.data,'');
			}
		}
	}
}
