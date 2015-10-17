function resetInput(numDist)
{
	SetStyle('edit','');
	document.getElementById('TdClasses').value='';
	for (var i=1;i<=numDist;++i)
		document.getElementById('Td' + i).value='';
}

function save(numDist)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var queryString
					= 'type=' +encodeURIComponent( document.getElementById('type').value) 
					+ '&cl=' + encodeURIComponent(document.getElementById('TdClasses').value)
					+ '&numDist=' + numDist;
					
				for (var i=1;i<=numDist;++i)
					queryString+='&td' + i + '=' + encodeURIComponent(document.getElementById('Td' + i).value);
				
				XMLHttp.open("POST","SaveDists.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="SaveDists.php?" + queryString;
				XMLHttp.onreadystatechange=save_StateChange;
				XMLHttp.send(queryString);
			}
		}
		catch(e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function save_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				save_Response();
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


function save_Response()
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
	var numDist = XMLRoot.getElementsByTagName('num_dist').item(0).firstChild.data;

	if (Error==0)
	{
		var tbody=document.getElementById('tbody');
		
		var td=XMLRoot.getElementsByTagName('td');
		
		var cl=XMLRoot.getElementsByTagName('cl').item(0).firstChild.data;
		var type=XMLRoot.getElementsByTagName('type').item(0).firstChild.data;
		
		var numRows=tbody.rows.length;
		var rows=tbody.rows;
		
		var row=rows[numRows-1].id.substr(4);
		++row;
		
		var TR=document.createElement('tr');
		TR.id='row_' +row;
		
		var TD_du=document.createElement('td');
		TD_du.className='Center';
		TD_du.width='20%';
		TD_du.innerHTML= '<div ></div>';
		
		TR.appendChild(TD_du);
		
		var TD_cl=document.createElement('td');
		TD_cl.className='Center';
		TD_cl.width='20%';
		TD_cl.innerHTML= '<div id="cl_' + row + '">' + cl + '</div>';
		
		TR.appendChild(TD_cl);
		
		for (var i=1;i<=td.length;++i)
		{
			var TD_dist=document.createElement('td');
			TD_dist.className='Center';
			TD_dist.innerHTML='<div id="td_' + row + '_' + i + '">' + td.item(i-1).firstChild.data + '</div>';
			TR.appendChild(TD_dist);
		}
		
		var TD_del=document.createElement('td');
		TD_del.className='Center';
		TD_del.innerHTML= '<img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow(' + row + ',\'' + cl + '\','  + type + ');">';
		
		TR.appendChild(TD_del);
		
		tbody.appendChild(TR);
		
		resetInput(numDist);
	}
	else
	{
		SetStyle('edit','warning');
	}
}

function deleteRow(row,cl,type)
{
	if (confirm(StrConfirm))
	{
		if (XMLHttp)
		{
			try
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var queryString
						= 'type=' +type
						+ '&cl=' + encodeURIComponent(cl)
						+ '&row=' + row;
					
					XMLHttp.open("POST","DeleteDists.php",true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="DeleteDists.php?" + queryString;
					XMLHttp.onreadystatechange=deleteRow_StateChange;
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

function deleteRow_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				deleteRow_Response();
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

function deleteRow_Response()
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
	var row = XMLRoot.getElementsByTagName('row').item(0).firstChild.data;
	
	if (Error==0)
	{
		var tbody=document.getElementById('tbody');

		var row2del=document.getElementById('row_' + row);
		
		if (row2del)
			tbody.removeChild(row2del);
	}
}

function ChangeInfo(obj) {
//	if(obj.value=='') return;
	var field=obj.name+'='+encodeURIComponent(obj.value);
	
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AjaxChangeInfo.php?"+field,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");

						XMLRoot = XMLResp.documentElement;

						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
						var Data = XMLRoot.getElementsByTagName('fld').item(0).firstChild.data;

						if (Error==0) {
							obj.style.color='green';
							obj.value=Data;
						} else {
							// SetStyle(Which,'error');
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}




