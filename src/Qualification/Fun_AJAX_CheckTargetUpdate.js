/*
													- Fun_AJAX_CheckTargetUpdate.js -
	Contiene le funzioni ajax usate da CheckTargetUpdate.php
*/ 	

var ReloadTime = 5000;	// Intervallo di check in ms
/*
	- CheckTarget()
	Invia la get a CheckTargetUpdate_XML.php per avere lo status dell'inserimento dei punto
*/
function CheckTarget()
{
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var Session = document.getElementById('x_Session').value;
				var Hour = document.getElementById('x_Hour').value;
				var QueryString = 'Session=' + Session + '&Hour=' + Hour;
				
				XMLHttp.open("GET","TargetUpdate_XML.php?" + QueryString,true);
				//document.getElementById('idOutput').innerHTML="TargetUpdate_XML.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=CheckTarget_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function CheckTarget_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CheckTarget_Response();
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

function CheckTarget_Response()
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
	var tbody = document.getElementById('tbody');
	
// distruggo la tabella
	var Rows=tbody.getElementsByTagName('tr');
	document.getElementById('idOutput').innerHTML='';
	for (i=Rows.length-1;i>=0;--i)
	{
		if (Rows.item(i))
		{
			tbody.removeChild(Rows.item(i));
		}
	}
	
	if (Error==0)
	{
		var Arr_No = XMLRoot.getElementsByTagName('no');
		var Arr_Status = XMLRoot.getElementsByTagName('status');
		
		if (tbody)
		{
			var k=0;
			var NewRow = document.createElement('TR');
			NewRow.id = 'Row_' + k;
			
			for (i=0;i<Arr_No.length;++i)
			{
				if (k%10==0 && k!=0)
				{
					tbody.appendChild(NewRow);
					NewRow = document.createElement('TR');
					NewRow.id = 'Row_' + k;
				}
				var NewTd= document.createElement('TD');
				NewTd.innerHTML+=Arr_No.item(i).firstChild.data;
				var TdStyle='Center TargetOk';
				var x=Arr_Status.item(i).firstChild.data;
				switch (x)
				{
					case '0':
						TdStyle='Center TargetOk';
						break;
					case '1':
						TdStyle='Center TargetNoComplete';
						break;
					case '2':
						TdStyle='Center TargetKo';
						break;
				}
				NewTd.className=TdStyle;
				NewRow.appendChild(NewTd);
				
				++k;
			}
			tbody.appendChild(NewRow);
		}
		
	}
	
	setTimeout("CheckTarget()",ReloadTime);
}

/*
	- ReqServerTime(When)
	Invia la get a ReqServerTime.php
	When � un numero maggiore o uguale a zero che indica quanti minuti fa. Se vale 0 otteniamo l'ora attuale
*/
function ReqServerTime(When)
{
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				XMLHttp.open("GET","ReqServerTime.php?When=" + When,true);
				//document.getElementById('idOutput').innerHTML="TargetUpdate_XML.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=ReqServerTime_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function ReqServerTime_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				ReqServerTime_Response();
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

function ReqServerTime_Response()
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
		var Hour=XMLRoot.getElementsByTagName('hour').item(0).firstChild.data;
		document.getElementById('x_Hour').value=Hour;
	}
}
