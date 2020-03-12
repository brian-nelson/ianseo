var cache=new Array();

function updateScore2(field)
{
	if (XMLHttp)
	{
		if (field)
		{
			var which=encodeURIComponent(field);
			var value='';
			
			if (which.substr(0,1)=='S' || which.substr(0,1)=='T' || which.substr(0,1)=='P')
			{
				value=document.getElementById(which).value;
			}
			else if (which.substr(0,1)=='t') 	// qui devo costruire tutta la stringa dei tiebreak
			{
				//var name=which.substr(0,which.length-2);
				//console.debug(which);
				var name=which.split('_');
				name.splice(4,1); // tolgo l'ultimo elemento
			//console.debug(name);
				name=name.join('_');	// ricombino la stringa
				var ties=document.getElementsByName(name + '[]');
				for (i=0;i<ties.length;++i)
					value+=ties[i].value + '|';
				value=value.substr(0,value.length-1);
				
				//console.debug(value);return;
			}
			
			cache.push('which=' + which + '&value=' + value)
			
		}
		
		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && cache.length>0)
			{
				var fromCache=cache.shift();
				
				XMLHttp.open("POST","UpdateScore2.php",true);
				//document.getElementById('idOutput').innerHTML="WriteScore_Bra.php?" + Field + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=updateScore2_stateChange;
				XMLHttp.send(fromCache);
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
}

function updateScore2_stateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				updateScore2_response();
			}
			catch(e)
			{
				
			}
		}
		else
		{
			
		}
	}
}

function updateScore2_response()
{
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var xmlRoot;
	if ((xmlRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	xmlRoot = XMLResp.documentElement;	
	
	var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
	var which=xmlRoot.getElementsByTagName('which').item(0).firstChild.data;
	
	if (error==1)
	{
		SetStyle(which,'error');
	}
	else
	{
		SetStyle(which,'');
	}
	
	setTimeout("updateScore2()",500);
}

function updateTarget2(field)
{
	if (XMLHttp)
	{
		if (field)
		{
			var which=encodeURIComponent(field);
			var value='';
			
			value=document.getElementById(which).value;
			
			cache.push('which=' + which + '&value=' + value)
			
		}
		
		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && cache.length>0)
			{
				var fromCache=cache.shift();
				
				XMLHttp.open("POST","UpdateTarget2.php",true);
				//document.getElementById('idOutput').innerHTML="WriteScore_Bra.php?" + Field + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=updateTarget2_stateChange;
				XMLHttp.send(fromCache);
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
}

function updateTarget2_stateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				updateTarget2_response();
			}
			catch(e)
			{
				
			}
		}
		else
		{
			
		}
	}
}

function updateTarget2_response()
{
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var xmlRoot;
	if ((xmlRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	xmlRoot = XMLResp.documentElement;	
	
	var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
	var which=xmlRoot.getElementsByTagName('which').item(0).firstChild.data;
	var value=xmlRoot.getElementsByTagName('value').item(0).firstChild.data;
	
	if (error==1)
	{
		SetStyle(which,'error');
	}
	else
	{
		SetStyle(which,'');
		document.getElementById(which).value=value;
	}
	
	setTimeout("updateTarget2()",500);
}