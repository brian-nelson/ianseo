/*
													- Fun_AJAX_ManTarget.js -
	Contiene le funzioni ajax usate da ManTarget.php
*/ 	

var Cache = new Array();	// Cache per le chiamate a FindRedTarget
var CacheWrite = new Array();	// Cache per le scritture

/*
	Esegue una get a WriteTarget.php
	Field è l'id del campo da aggiornare
*/
function WriteTarget(Field)
{
	if (XMLHttp)
	{	
		if (Field)
		{
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			CacheWrite.push(Field + "=" + FieldValue);
		}
		
		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && CacheWrite.length>0)
			{
				//var FieldValue = document.getElementById(Field).value;
				var FromCache=CacheWrite.shift();
				
				XMLHttp.open("POST","WriteTarget.php",true);
				//document.getElementById('idOutput').innerHTML="WriteTarget.php?" + Field + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=WriteTarget_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch (e)
		{
			//console.debug('Error: ' + e.toString());
		}
	}
}

function WriteTarget_StateChange()
{
	//console.debug('Write Target state changed: ' + XMLHttp.readyState);
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				WriteTarget_Response();
			}
			catch(e)
			{
				//console.debug('Error: ' + e.toString());
			}
		}
		else
		{
			//console.debug('Error: ' +XMLHttp.statusText);
		}
	}
}

function WriteTarget_Response()
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
	
	var Error=XMLRoot.getElementsByTagName('error');
	var Which = XMLRoot.getElementsByTagName('which');
	var Target = XMLRoot.getElementsByTagName('target');
	var Phase =  XMLRoot.getElementsByTagName('phase');
	var Event =  XMLRoot.getElementsByTagName('event');
	
	for(n=0; n<Error.length; n++) {
		if (Error.item(n).firstChild.data==0)
		{
			SetStyle(Which.item(n).firstChild.data,'');
			document.getElementById(Which.item(n).firstChild.data).value=Target.item(n).firstChild.data;
			
			var Sup=new Array();
			Sup=Which.item(n).firstChild.data.split('_');
			
		// controllo i doppioni
			//console.debug('Chiamo FindRedTarget');
			FindRedTarget(Event.item(n).firstChild.data,Phase.item(n).firstChild.data,'');
		}
		else
		{
			SetStyle(Which.item(n).firstChild.data,'error');
		}
	}
	
	// per scaricare la cache degli update	
	setTimeout("WriteTarget()",600);
}

/*
	Esegue una post a FindRedTarget.php
	Event è l'evento
	Phase è la fase
	Tar!='' implica il filtro nella pagina
*/
function FindRedTarget(Event,Phase,Tar)
{
	if (XMLHttp)
	{	
		if (Event && Phase)
		{
			var QueryString = '';
			QueryString
				+='d_Event=' + Event
				+ '&d_Phase=' + Phase
				+ (Tar!='' ? '&Target=' + Tar : '');
			
			Cache.push(QueryString);
		}	
		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0)
			{
				var FromCache = Cache.shift();	
				XMLHttp.open("POST","FindRedTarget.php",true);
				//document.getElementById('idOutput').innerHTML+="<br>FindRedTarget.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=FindRedTarget_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch (e)
		{
			//condole.debug('Error: ' + e.toString());
		}	
	}
	
}

function FindRedTarget_StateChange()
{
	//console.debug('state changed: ' + XMLHttp.readyState);
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				FindRedTarget_Response();
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

function FindRedTarget_Response()
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
		var Bersagli = XMLRoot.getElementsByTagName('bersagli').item(0).firstChild.data;
		if (Bersagli==1)
		{
			var Event  = XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
			var Phase  = XMLRoot.getElementsByTagName('phase').item(0).firstChild.data;
			
			var Arr_MatchNo = XMLRoot.getElementsByTagName('matchno');
			var Arr_TargetNo = XMLRoot.getElementsByTagName('targetno');
			var Arr_Quanti = XMLRoot.getElementsByTagName('quanti');
			
			var ath = XMLRoot.getElementsByTagName('athfortar').item(0).firstChild.data;
			
			for (i=0;i<Arr_MatchNo.length;++i)
			{
			// In base a quanti target ho e al flag athfortar decido il colore del bersaglio
				var mm = Arr_MatchNo.item(i).firstChild.data;
				var tt = Arr_TargetNo.item(i).firstChild.data;
				var qq = Arr_Quanti.item(i).firstChild.data;
			/*
				Se ath vale 0 vuol dire che devo avere al pi� una persona per paglione e quindi
				non posso avere numeri doppi; se vale 1 significa che posso avere due persone per paglione
				e al pi� quindi un doppione che deve essere nel matchno accoppiato
			*/
				var ObjId = (document.getElementById('d_FSTarget_' + Event + '_' + mm + '_' + ath) ? document.getElementById('d_FSTarget_' + Event + '_' + mm + '_' + ath).id : null);
				
				var Rosso = 0;

				if (ObjId)
				{
					if (ath==0)
					{
						if (qq>1)
							Rosso=1;
					}
					else if (ath==1)
					{
					/*
						Se qq>2 sicuramente ho almeno un doppio dove non deve esserci (matchno non accoppiato con mm)
						Se qq=2 devo verificare che il doppio sia nel matchno accoppiato
						qq<=1 sempre ok
					*/
						if (qq>2)
							Rosso=1;
						else if (qq==2)
						{
							if (i<Arr_MatchNo.length-1)
							{
								var mm2_index = ((mm % 2) == 0 ? i+1 : i-1);
			
								if (Arr_TargetNo.item(mm2_index).firstChild.data!=tt) 
								{
									Rosso=1;
								}
							}
						}
					}
				}
				//document.getElementById('idOutput').innerHTML+=ObjId + '<br>';
				
				if (ObjId)
				{
					if (Rosso==1)
						SetStyle(ObjId,'red');
					else
						SetStyle(ObjId,'');
				}
			}
		}
	}
	
	
// per scaricare la cache
	setTimeout("FindRedTarget()",500);

}