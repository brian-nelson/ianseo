/*
													- Fun_AJAX_ManagePays.js -
	Contiene le funzioni ajax che riguardano la pagina ManagePays.php 
*/ 		
var CacheField = new Array();	// cache per l'update generico dei campi
/*
	- UpdateField(Field)
	Invia la POST a UpdateField.php il campo Field da aggiornare
	Va agganciata all'evento onChange di tutti i campi 
*/
function UpdateField(Field)
{
	if (XMLHttp)
	{
		if (Field)
		{
			var FieldName = encodeURIComponent(Field);
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			CacheField.push(FieldName + "=" + FieldValue);
		}
		
		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && CacheField.length>0)
			{
				var FromCache = CacheField.shift();
				XMLHttp.open("POST","UpdatePay.php",true);
			//	document.getElementById('idOutput').innerHTML="UpdateField.php?" + FieldName + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateField_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch (e)
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateField_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateField_Response();
			}
			catch(e)
			{
				//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function UpdateField_Response()
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
	
	if (Error==1)
	{
		SetStyle(Which,'error');
	}
	else
	{
		SetStyle(Which,'');
	}
	
		
// per scaricare la cache degli update	
	setTimeout("UpdateField()",500);
}