/*
													- Fun_AJAX_index.js -
	Contiene le funzioni ajax che riguardano la pagina index.php
*/ 		

var Cache = new Array();	// cache per l'update 

/*
	- UpdateElim(Field)
	Invia la GET a UpdateElim.php
*/
function UpdateElim(Field)
{
	if (XMLHttp)
	{
		if (Field)
		{
			var FieldName = encodeURIComponent(Field);
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			Cache.push(FieldName + "=" + FieldValue);
		}
		
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0)
				{
					var FromCache = Cache.shift();
					XMLHttp.open("POST","UpdateElim.php",true);
				//	document.getElementById('idOutput').innerHTML="UpdateElim.php?" + FieldName + "=" + FieldValue;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=UpdateElim_StateChange;
					XMLHttp.send(FromCache);
				}
			}
			else
				Cache.shift();
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateElim_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateElim_Response();
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

function UpdateElim_Response()
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
	var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
	
	if (Error==0)
	{
		var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
		var Phase = XMLRoot.getElementsByTagName('phase').item(0).firstChild.data;
		SetStyle(Which,'');
	}
	else
	{
		SetStyle(Which,'error');
	}
	
	// per scaricare la cache degli update	
	setTimeout("UpdateElim()",500);
}

function SelectSession()
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				var Ses = encodeURIComponent(document.getElementById('x_Session').value);
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("GET","SelectSession.php?Ses=" + Ses,true);
					//document.getElementById('idOutput').innerHTML="SelectSession.php?Ses=" + Ses;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=SelectSession_StateChange;
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

function SelectSession_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectSession_Response();
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

function SelectSession_Response()
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
		var Minimo = XMLRoot.getElementsByTagName('minimo').item(0).firstChild.data;
		var Massimo = XMLRoot.getElementsByTagName('massimo').item(0).firstChild.data;
		
		document.getElementById('x_From').value=(Minimo!='#' ? Minimo : '');
		document.getElementById('x_To').value=(Massimo!='#' ? Massimo : '');
	}
}
