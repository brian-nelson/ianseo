/*
													- Fun_AJAX.js -
	Contiene le funzioni ajax che riguardano la speaker view 
*/
var t;
var OldTime=0;
var mRead = new Array();
var mUpdate = new Array();

function GetMatches() 
{
	if (XMLHttp)
	{
		try
		{

			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				clearTimeout(t);
				XMLHttp.open("GET","GetTVScore.php?TourCode="+TourCode+"&Time="+OldTime+"&Width"+getWinWidth(),true);
				XMLHttp.onreadystatechange=GetMatches_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e) { }
	}
}

function GetMatches_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				GetMatches_Response();
			}
			catch(e) 
			{ 
				t = setTimeout("GetMatches()",500);
			}
		}
	}
}

function GetMatches_Response()
{
	var XMLResp=XMLHttp.responseXML;
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;
	var tbody=document.getElementById('PopupContent');
	
	var Error = XMLRoot.getElementsByTagName('e').item(0).firstChild.data;
	if (Error==0)
	{
		var Chunk = XMLRoot.getElementsByTagName('c').item(0).firstChild.data;
		
		if(Chunk!='') {
			tbody.innerHTML=Chunk;
		}
		OldTime=XMLRoot.getElementsByTagName('t').item(0).firstChild.data;
	} else {
		var Chunk = XMLRoot.getElementsByTagName('c').item(0).firstChild.data;
		tbody.innerHTML='<table id="error"><tr><td>'+Chunk+'</td></tr></table>';
	}
	t = setTimeout("GetMatches()",500);
}

function getWinWidth() {
	// tutti ma non IE
	if( typeof( window.innerWidth ) == 'number' ) return window.innerWidth;
	// IE 6+ in 'standards compliant mode'
	if (document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) return document.documentElement.clientWidth;
	// IE 4 (per completezza)
	if (document.body && ( document.body.clientWidth || document.body.clientHeight )) return document.body.clientWidth;
	return 0;
}

