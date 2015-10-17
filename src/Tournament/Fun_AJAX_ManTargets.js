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
				var TdRegExp='';
				if(document.getElementById('TdRegExp')) TdRegExp=document.getElementById('TdRegExp').value;
				var queryString
					= 'cl=' + encodeURIComponent(document.getElementById('TdClasses').value)
					+ '&RegExp=' + encodeURIComponent(TdRegExp)
					+ '&TfName=' + encodeURIComponent(document.getElementById('TdName').value)
					+ '&isDefault=' + (document.getElementById('TdDefault').checked?'1':'0');
					
				for (var i=1;i<=numDist;++i) {
					queryString+='&tdface[' + i + ']=' + encodeURIComponent(document.getElementById('TdFace' + i).value);
					queryString+='&tddiam[' + i + ']=' + encodeURIComponent(document.getElementById('TdDiam' + i).value);
				}
				
				XMLHttp.open("POST","SaveTargets.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
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

	if (Error==0)
	{
		var tbody=document.getElementById('tbody');
		
		var face=XMLRoot.getElementsByTagName('face');
		var diam=XMLRoot.getElementsByTagName('diam');
		
		var cl=XMLRoot.getElementsByTagName('cl').item(0).firstChild.data;
		var reg=XMLRoot.getElementsByTagName('reg').item(0).firstChild.data;
		var tfid=XMLRoot.getElementsByTagName('tfid').item(0).firstChild.data;
		var tfname=XMLRoot.getElementsByTagName('tfname').item(0).firstChild.data;
		var def=XMLRoot.getElementsByTagName('default').item(0).firstChild.data;
		
		var numRows=tbody.rows.length;
		var rows=tbody.rows;
		
		var row=rows[numRows-1].id.substr(4);
		++row;
		
		var TR=document.createElement('tr');
		TR.id='row_' +row;
		
		var TD_del=document.createElement('td');
		TD_del.className='Center';
		TD_del.innerHTML= '&nbsp;';
		TR.appendChild(TD_del);

		var TD_nam=document.createElement('td');
		TD_nam.className='Center';
		TD_nam.innerHTML= tfname;
		TR.appendChild(TD_nam);

		var TD_cl=document.createElement('td');
		TD_cl.className='Center';
		TD_cl.width='20%';
		TD_cl.innerHTML= '<div id="cl_' + row + '">' + cl + '</div>';
		TR.appendChild(TD_cl);
		
		var TD_reg=document.createElement('td');
		TD_reg.className='Center';
		TD_reg.width='20%';
		TD_reg.innerHTML= '<div id="reg_' + row + '">' + reg + '</div>';
		TR.appendChild(TD_reg);
		
		for (var i=1;i<=face.length;++i)
		{
			var TD_dist=document.createElement('td');
			TD_dist.className='Center';
			TD_dist.innerHTML='<div id="tf_' + row + '_' + i + '">' + face.item(i-1).firstChild.data + '</div>' 
				+ '<div id="td_' + row + '_' + i + '">' + diam.item(i-1).firstChild.data + '</div>';
			TR.appendChild(TD_dist);
		}
		
		var TD_del=document.createElement('td');
		TD_del.className='Center';
		TD_del.innerHTML= def;
		
		TR.appendChild(TD_del);
		
		var TD_del=document.createElement('td');
		TD_del.className='Center';
		TD_del.innerHTML= '<img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow(' + row + ',' + tfid + ');">';
		
		TR.appendChild(TD_del);
		
		tbody.appendChild(TR);
	}
	else
	{
		SetStyle('edit','warning');
	}
}

function deleteRow(row, tfid)
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
						= 'row=' + row
						+ '&tfid=' + tfid;
					
					XMLHttp.open("POST","DeleteTargets.php",true);
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
	} else if(Error==2) {
		alert(CannotDelete);
	}
}





