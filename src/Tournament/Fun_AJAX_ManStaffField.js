/*
													- Fun_AJAX_ManStaffField.js -
	Contiene le funzioni ajax che riguardano la pagina ManStaffField.php
*/ 		

var cache = new Array();	// cache per le richieste di scrittura al server

/*
	- CercaMatr(IdFrom,IdReturn)
	Invia la GET a ManStaffField_Find.php passando Matr=<Valore in IdFrom > & IdReturn=<Ret>
*/
function CercaMatr(From,Ret)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var Matr = encodeURIComponent(document.getElementById(From).value);
				var IdReturn =  encodeURIComponent(Ret);	
				XMLHttp.open("GET","ManStaffField_Find.php?Matr=" + Matr + "&IdReturn=" + Ret,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=CercaMatr_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
		}
	}
}

function CercaMatr_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CercaMatr_Response();
			}
			catch(e)
			{
				//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function CercaMatr_Response()
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
	var Unique = XMLRoot.getElementsByTagName('unique').item(0).firstChild.data;
	
	if (Error==0)
	{
		var IdRet=XMLRoot.getElementsByTagName('id_ret').item(0).firstChild.data;
		
		if (Unique==1)
		{
			var Name=XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
			document.getElementById(IdRet).value=Name;
		}
		else
		{
			document.getElementById(IdRet).value='';
		}
	}
	else
	{
	}
}

/*
	- EditMatr(Row)
	Invia la GET a ManStaffField_Edit.php
*/
function EditMatr(Row)
{
	if (XMLHttp)
	{
		if (Row)
		{
			var Matr = encodeURIComponent(document.getElementById('d_TiMatr_' + Row).value);
			var Name = encodeURIComponent(document.getElementById('d_TiName_' + Row).value);
			var Type = encodeURIComponent(document.getElementById('d_TiType_' + Row).value);
			cache.push("Row=" + Row +"&Matr=" + Matr + "&Name=" + Name + "&Type=" + Type);
		}
		
		try
		{
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && cache.length>0)
			{
				var FromCache = cache.shift();
				XMLHttp.open("POST","ManStaffField_Edit.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=EditMatr_StateChange;
				XMLHttp.send(FromCache);
			}
		}
		catch (e)
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
		}
	}
}

function EditMatr_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				EditMatr_Response();
			}
			catch(e)
			{
				//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function EditMatr_Response()
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
	
	if (Error==1)
	{
		var Id=XMLRoot.getElementsByTagName('row').item(0).firstChild.data;
		document.getElementById('d_TiMatr_' + Id).className='error';
		document.getElementById('d_TiName_' + Id).className='error';
		document.getElementById('d_TiType_' + Id).className='error';
	}
	
// per scaricare la cache degli update	
	setTimeout("EditMatr()",500);
}