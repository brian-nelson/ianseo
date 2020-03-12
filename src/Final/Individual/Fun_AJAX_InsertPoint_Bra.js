/*
													- Fun_AJAX_InsertPoint_Bra.js -
	Contiene le funzioni ajax usate da InsertPoint_Bra.php
*/ 	

var Cache = new Array();	// cache per l'update dei punti

/*
	Esegue una chiamata asincrona al server  chiamando WriteScore_Bra.php
	Field è l'id del campo da aggiornare
*/
function SendToServer(Field)
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
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0)
			{
				var FromCache = Cache.shift();
				
				XMLHttp.open("POST","WriteScore_Bra.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SendToServer_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch (e)
		{
			//alert('Error: ' + e.toString());
		}
	}
}

function SendToServer_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SendToServer_Response();
			}
			catch(e)
			{
				//alert('Error: ' + e.toString());
			}
		}
		else
		{
			//alert('Error: ' +XMLHttp.statusText);
		}
	}
}

function SendToServer_Response()
{
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;	
	
	var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
	
	if (Error==0)
	{
		var FieldError = XMLRoot.getElementsByTagName('field_error').item(0).firstChild.data;
		var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
		var Athletes = XMLRoot.getElementsByTagName('name');
		var Countries = XMLRoot.getElementsByTagName('cty');
		var Events = XMLRoot.getElementsByTagName('event');
		var MatchNo = XMLRoot.getElementsByTagName('matchno');
		var Tie = XMLRoot.getElementsByTagName('tie');
		
		var Ath = '';
		var Cty = '';
		
		for (i=0;i<Athletes.length;++i)
		{	
			Ath = (Athletes.item(i).firstChild.data!='#' ? Athletes.item(i).firstChild.data : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			Cty = (Countries.item(i).firstChild.data!='#' ? Countries.item(i).firstChild.data : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');	
			
			document.getElementById('idAth_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data).innerHTML=Ath;
			document.getElementById('idCty_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data).innerHTML=Cty
			
			if(document.getElementById('d_T_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data))
				document.getElementById('d_T_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data).value=Tie.item(i).firstChild.data
				
		}
		
		if (FieldError==1)
		{
			SetStyle(Which,'error');
		}
		else
		{
			SetStyle(Which,'');
		}
			
	}
	else
	{
		//document.getElementById('idOutput').innerHTML='Unexpected Error!';
	}
	
	// per scaricare la cache degli update	
	setTimeout("SendToServer()",500);
}

/*
	Esegue una chiamata asincrona al server  chiamando WriteScore_Bra.php passando i punti di tiebreak
	Field è l'id del campo da aggiornare,Num � il numero di freccie di tiebreak
*/
function SendTieBreak(Field,Num)
{
	if (XMLHttp)
	{	
		if (Field)
		{
			var Value='';
			for (i=0;i<Num;++i)
			{
				Value+=document.getElementById(Field + '_' + i).value + '|';
			}
			Value=Value.substr(0,Value.length-1);
			Cache.push(Field + "=" + Value);
		}
		
		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0 )
			{	
				var FromCache = Cache.shift();
				
				XMLHttp.open("POST","WriteScore_Bra.php",true);
				//document.getElementById('idOutput').innerHTML="WriteScore.php?" + Field + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SendTieBreak_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch (e)
		{
		//	document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function SendTieBreak_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SendTieBreak_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function SendTieBreak_Response()
{
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw("XML non valido:\n"+XMLResp.responseText);
	
// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;	
	
	var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	
	
	if (Error==0)
	{
		var FieldError = XMLRoot.getElementsByTagName('field_error').item(0).firstChild.data;
		var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
		var Athletes = XMLRoot.getElementsByTagName('name');
		var Countries = XMLRoot.getElementsByTagName('cty');
		var Events = XMLRoot.getElementsByTagName('event');
		var MatchNo = XMLRoot.getElementsByTagName('matchno');
		var Tie = XMLRoot.getElementsByTagName('tie');
		
		var Ath = '';
		var Cty = '';
		
		for (i=0;i<Athletes.length;++i)
		{	
			Ath = (Athletes.item(i).firstChild.data!='#' ? Athletes.item(i).firstChild.data : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			Cty = (Countries.item(i).firstChild.data!='#' ? Countries.item(i).firstChild.data : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');	
			
			document.getElementById('idAth_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data).innerHTML=Ath;
			document.getElementById('idCty_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data).innerHTML=Cty
			
			if(document.getElementById('d_T_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data))
				document.getElementById('d_T_' + Events.item(i).firstChild.data + '_' + MatchNo.item(i).firstChild.data).value=Tie.item(i).firstChild.data
				
		}

	}
	
	// per scaricare la cache degli update	
	setTimeout("SendTieBreak()",500);
}