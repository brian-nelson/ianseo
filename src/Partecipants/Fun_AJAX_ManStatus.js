/*
													- Fun_AJAX_ManStatus.js -
	Contiene le funzioni ajax che riguardano la pagina ManStatus.php
*/ 		

/*
	- UpdateStatus(Field)
	Invia la post a UpdateStatus.php che ritorna l'id e il nuovo status del tizio.
*/
function UpdateStatus(Field)
{
	try
	{	
		if (!document.getElementById('chk_BlockAutoSave').checked)
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var FieldName=encodeURIComponent(Field);
				var FieldValue=encodeURIComponent(document.getElementById(Field).value);
				XMLHttp.open("POST","UpdateStatus.php?" + FieldName + "=" + FieldValue,true);
				//document.getElementById('idOutput').innerHTML="UpdateStatus.php?" + FieldName + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateStatus_StateChange;
				XMLHttp.send(null);
			}
		}
	}
	catch (e)
	{
		//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
	}
}

function UpdateStatus_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateStatus_Response();
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

function UpdateStatus_Response()
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
		var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
		var Status = XMLRoot.getElementsByTagName('new_status').item(0).firstChild.data;;
		
		switch(Status)
		{
			case '0':
				NewStyle = '';
				break;
			case '1':
				NewStyle = 'CanShoot';
				break;
			case '5':
				NewStyle = 'UnknownShoot';
				break;
			case '8':
				NewStyle = 'CouldShoot';
				break;
			case '9':
				NewStyle = 'NoShoot';
				break;
		}
		document.getElementById('Row_' + Id).className=NewStyle;
	}
}