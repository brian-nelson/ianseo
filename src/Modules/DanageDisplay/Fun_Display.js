var dispCache = new Array();
var displayCallBack = CreateXMLHttpRequestObject();

function resetCmbSession() {}

function refreshDisplay()
{
	if(document.getElementById('dispDanage').checked)
		updateDanageDisplay();	
}

function updateDanageDisplay(cacheCheck)
{

//	console.
	if(displayCallBack)
	{
		if(!cacheCheck)
		{
			var hhtLine = document.getElementById('x_Hht').value;
			var dispType = document.getElementById('x_DispType').value;
			var contrast=document.getElementById('x_Contrast').value;
			
			var match = document.getElementById('d_Match').value;
			var event=document.getElementById('event').value;
			var team=document.getElementById('team').value;
			
			
	
			
			var qs = "match=" + match
				+ "&event=" + event
				+ "&team=" + team
				+ "&line=" + hhtLine
				+ "&type=" + dispType
				+ "&contrast=" + contrast;
			dispCache.push(qs);
		}
		try
		{
			if ((displayCallBack.readyState==XHS_COMPLETE || displayCallBack.readyState==XHS_UNINIT) && dispCache.length>0)
			{
				var FromCache = dispCache.shift();
				displayCallBack.open("POST",WebDir+"Modules/DanageDisplay/UpdateDisplay.php",true);
				displayCallBack.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				displayCallBack.onreadystatechange=updateDanageDisplay_StateChange;
				displayCallBack.send(FromCache);
			}
			
		}
		catch (e)
		{	
			//console.debug('Errore: ' + e.toString());
		}
	}
}

function updateDanageDisplay_StateChange()
{
	// se lo stato è Complete vado avanti
	if (displayCallBack.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (displayCallBack.status==200)
		{
			try
			{
				updateDanageDisplay_Response();
			}
			catch(e)
			{
				//console.debug('Errore: ' + e.toString());
			}
		}
		else
		{
			//console.debug('Errore: ' + e.toString());
		}
	}
}

function updateDanageDisplay_Response()
{
	// leggo l'xml
	var XMLResp=displayCallBack.responseXML;
	console.debug(XMLResp);
/*	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;
	*/	
	if(document.getElementById('x_autoRefresh').checked)
		setTimeout("updateDanageDisplay()",100);
}