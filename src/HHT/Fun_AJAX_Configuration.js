var Cache = new Array();	// cache per l'update 

function resetInputHht()
{
	SetStyle('edit','');
	document.getElementById('HhtName').value='';
	document.getElementById('HhtIpAddress').value='';
	document.getElementById('HhtIpPort').value='9001';
}

function selectAllEvents()
{
	for (i = 0; i < document.myform.eventList.length; i++)
	{
		if(document.myform.eventList[i].checked == false)
		{
			document.myform.eventList[i].checked = true;
			saveHhtEvent(document.myform.eventList[i].value);
		}
	}
}

function saveHhtEvent(EventCode)
{
	if (XMLHttp)
	{
		try
		{
			if (EventCode)
			{
				var queryString
					= 'Id=' +encodeURIComponent( document.getElementById('HhtId').value) 
					+ '&Event=' + encodeURIComponent(EventCode)
					+ '&Value=' + encodeURIComponent(document.getElementById('chk_'+EventCode).checked);
				Cache.push(queryString);
			}
			
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0)
			{
				var FromCache = Cache.shift();
				XMLHttp.open("POST","SaveHHTEvent.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="SaveDists.php?" + queryString;
				XMLHttp.onreadystatechange=saveHhtEvent_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch(e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function saveHhtEvent_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				saveHhtEvent_Response();
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

function saveHhtEvent_Response()
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
	var chkId = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	var chkEvent = XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
	var chkEnabled = XMLRoot.getElementsByTagName('enabled').item(0).firstChild.data;

	if (Error==0)
	{
//		document.getElementById('chk_'+chkEvent).checked=chkEnabled;
		SetStyle('row_'+chkEvent,'');
	}
	else
	{
		document.getElementById('chk_'+chkEvent).checked=!chkEnabled;
		SetStyle('row_'+chkEvent,'warning');
	}
	// per scaricare la cache degli update	
	setTimeout("saveHhtEvent()",10);
}



function saveHht()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var queryString
					= 'IpAddress=' +encodeURIComponent( document.getElementById('HhtIpAddress').value) 
					+ '&Port=' + encodeURIComponent(document.getElementById('HhtIpPort').value)
					+ '&Name=' + encodeURIComponent(document.getElementById('HhtName').value);
					
				XMLHttp.open("POST","SaveHHT.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="SaveDists.php?" + queryString;
				XMLHttp.onreadystatechange=saveHht_StateChange;
				XMLHttp.send(queryString);
			}
		}
		catch(e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function saveHht_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				saveHht_Response();
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

function saveHht_Response()
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
	var IdHht = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	var NameHht = XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
	var IpHht = XMLRoot.getElementsByTagName('ip').item(0).firstChild.data;
	var PortHht = XMLRoot.getElementsByTagName('port').item(0).firstChild.data;

	if (Error==0)
	{
		var tbody=document.getElementById('tbody');
		
		var TR=document.createElement('tr');
		TR.id='row_' + IdHht;

		var TD_name=document.createElement('td');
		TD_name.className='Center';
		TD_name.innerHTML= '<div id="name_' + IdHht + '"><a href="ConfDetails.php?Id=' + IdHht + '">' + NameHht + '</a></div>';
		TR.appendChild(TD_name);
		
		var TD_ip=document.createElement('td');
		TD_ip.className='Center';
		TD_ip.innerHTML= '<div id="ip_' + IdHht + '">' + IpHht + '</div>';
		TR.appendChild(TD_ip);

		var TD_port=document.createElement('td');
		TD_port.className='Center';
		TD_port.innerHTML= '<div id="port_' + IdHht + '">' + PortHht + '</div>';
		TR.appendChild(TD_port);
		
		var TD_del=document.createElement('td');
		TD_del.className='Center';
		TD_del.innerHTML= '<img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteHht(' + IdHht + ');">';
		TR.appendChild(TD_del);
		
		tbody.appendChild(TR);
		resetInputHht();
	}
	else
	{
		SetStyle('edit','warning');
	}
}

function deleteHht(row)
{
	if (confirm(StrConfirm))
	{
		if (XMLHttp)
		{
			try
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var queryString = 'Id='+row;
					
					XMLHttp.open("POST","DeleteHHT.php",true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="DeleteDists.php?" + queryString;
					XMLHttp.onreadystatechange=deleteHht_StateChange;
					XMLHttp.send(queryString);
				}
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		}
	}
}

function deleteHht_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				deleteHht_Response();
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

function deleteHht_Response()
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
	var IdHHt = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	
	if (Error==0)
	{
		var tbody=document.getElementById('tbody');

		var row2del=document.getElementById('row_' + IdHHt);
		
		if (row2del)
			tbody.removeChild(row2del);
	}
}

