/*
													- Fun_AJAX_SetEventRules.js -
	Contiene le funzioni ajax usate da SetEventRules.php
*/ 	

var Cache = new Array();	// cache per l'update 

/*
	Invia la get a AddEventRule.php
	per aggiungere una coppia DivClass
	Event è l'evento a cui aggiungere la coppia
*/
function AddEventRule(Event)
{
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var OptDiv=document.getElementById('New_EcDivision').options;
				var OptCl=document.getElementById('New_EcClass').options;
				
				if (OptDiv.selectedIndex>=0 && OptCl.selectedIndex>=0)
				{
					var QueryString = 'EvCode=' + Event;
				
					for (i=0;i<OptDiv.length;++i)
						if (OptDiv[i].selected)
							QueryString+= '&New_EcDivision[]=' + OptDiv[i].value;
							
					for (i=0;i<OptCl.length;++i)
						if (OptCl[i].selected)
							QueryString+= '&New_EcClass[]=' + OptCl[i].value;
						
					XMLHttp.open("GET","AddEventRule.php?" + QueryString,true);
					//document.getElementById('idOutput').innerHTML="AddEventRule.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddEventRule_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function AddEventRule_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				AddEventRule_Response();
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

function AddEventRule_Response()
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
		var EvCode = XMLRoot.getElementsByTagName('evcode').item(0).firstChild.data;
		//var New_EcDivision = XMLRoot.getElementsByTagName('new_ecdivision').item(0).firstChild.data;
		//var New_EcClass = XMLRoot.getElementsByTagName('new_ecclass').item(0).firstChild.data;
		
		var Arr_Rules = XMLRoot.getElementsByTagName('new_rule');
		
		var ConfirmMsg = XMLRoot.getElementsByTagName('confirm_msg').item(0).firstChild.data;
		
		var tbody=document.getElementById('tbody');
		
	// Prima dell'ultima riga aggiungo le nuove regole
		
		for (i=0;i<Arr_Rules.length;++i)
		{
			var Div='';
			var Cl='';
			var DivCl=Arr_Rules.item(i).firstChild.data.split('|')
			
			NewDiv=DivCl[0];
			NewCl=DivCl[1];
			
			var NewRow = document.createElement('TR');
			NewRow.id='Row_' + EvCode + '_' + NewDiv + NewCl;
			
			var TD_Div = document.createElement('TD');
			TD_Div.className='Center';
			TD_Div.innerHTML=NewDiv;
			
			var TD_Cl = document.createElement('TD');
			TD_Cl.className='Center';
			TD_Cl.innerHTML=NewCl;
			
				
			var TD_Delete = document.createElement('TD');
			TD_Delete.className='Center';
			TD_Delete.innerHTML
				= '<a href="javascript:DeleteEventRule(\'' + EvCode + '\',\'' + NewDiv + '\',\'' + NewCl + '\');"><img src="../../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
			
			NewRow.appendChild(TD_Div);
			NewRow.appendChild(TD_Cl);
			NewRow.appendChild(TD_Delete);
			
			tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
		}
		
		document.getElementById('New_EcDivision').selectedIndex=-1;
		document.getElementById('New_EcClass').selectedIndex=-1;
		
	}
}

/*
	Invia la get a DeleteEventRule.php
	per eliminare una coppia DivClass
	Event è l'evento a cui eliminare la coppia
	DelDiv e DelClass servono per identificare la coppia
*/
function DeleteEventRule(Event,DelDiv,DelClass)
{	
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString
					= 'EvCode=' + Event + '&'
					+ 'DelDiv=' + DelDiv + '&'
					+ 'DelCl=' + DelClass;
					
				XMLHttp.open("GET","DeleteEventRule.php?" + QueryString,true);
			//	document.getElementById('idOutput').innerHTML="DeleteEventRule.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=DeleteEventRule_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function DeleteEventRule_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				DeleteEventRule_Response();
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

function DeleteEventRule_Response()
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
		var Event=XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
		var Div=XMLRoot.getElementsByTagName('div').item(0).firstChild.data;
		var Cl=XMLRoot.getElementsByTagName('cl').item(0).firstChild.data;
		
		var tbody=document.getElementById('tbody');
		
		var Row=document.getElementById('Row_' + Event + '_' + Div + Cl);
		if (Row)
			tbody.removeChild(Row);
	}
}

function UpdateParamsField(Field)
{
	if (XMLHttp)
	{	
		if (Field && document.getElementById(Field) != null)
		{
			var FieldName = encodeURIComponent(Field);
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			Cache.push(FieldName + "=" + FieldValue);
		}
		
		try
		{
			
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0)
			{
				//var FieldValue = encodeURIComponent(document.getElementById(Field).value);
				var FromCache = Cache.shift();
				
				//XMLHttp.open("GET","UpdateFieldEventList.php?" + encodeURIComponent(Field) + "=" + FieldValue,true);
				XMLHttp.open("POST","UpdateFieldEventList.php",true);
				//document.getElementById('idOutput').innerHTML="UpdateFieldEventList.php?" + Field + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateParamsField_StateChange;
				XMLHttp.send(FromCache);
			}
			
		}
		catch (e)
		{
		//	alert('Error: ' + e.toString());
		}
	}
}

function UpdateParamsField_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateParamsField_Response();
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

function UpdateParamsField_Response()
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
	setTimeout("UpdateParamsField()",500);
}