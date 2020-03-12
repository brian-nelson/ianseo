function clearHiddenFields(form) {
	var CheckBox;
	for(n=0; n<form.elements.length; n++) {
		var el=form.elements[n];
		if(el.type=='checkbox' && el.parentNode.style.display=='none' && el.checked) {
			el.checked=false;
		}
	}
}

function updateRule(Rule) {
	var ClassIndHead='none';
	var ClassInd='none';
	var QualIndHead='none';
	var QualInd='none';
	var ClassTeamHead='none';
	var ClassTeam='none';
	var QualTeamHead='none';
	var QualTeam='none';
	var PhaseIndHead='none';
	var PhaseInd='none';
	var PhaseTeamHead='none';
	var PhaseTeam='none';

	switch(Rule) {
		case 'QUAL':
		case 'QUALS':
		//case 'LIST':
		//case 'LSPH':
		case 'RAND':
			ClassIndHead='table-cell';
			ClassInd='table-cell';
			break;
		case 'QUALT':
			ClassTeamHead='table-cell';
			ClassTeam='table-cell';
			break;
		case 'ELIM':
		case 'FIN':
			PhaseIndHead='table-cell';
			PhaseInd='table-cell';
		case 'ABS':
		case 'ABSS':
			QualIndHead='table-cell';
			QualInd='table-cell';
			break;
		case 'FINT':
			PhaseTeamHead='table-cell';
			PhaseTeam='table-cell';
		case 'ABST':
			QualTeamHead='table-cell';
			QualTeam='table-cell';
			break;
	}
	document.getElementById('ClassIndHead').style.display	= ClassIndHead;
	document.getElementById('ClassInd').style.display		= ClassInd;
	document.getElementById('QualIndHead').style.display	= QualIndHead;
	document.getElementById('QualInd').style.display		= QualInd;
	document.getElementById('ClassTeamHead').style.display	= ClassTeamHead;
	document.getElementById('ClassTeam').style.display		= ClassTeam;
	document.getElementById('QualTeamHead').style.display	= QualTeamHead;
	document.getElementById('QualTeam').style.display		= QualTeam;
	document.getElementById('PhaseIndHead').style.display	= PhaseIndHead;
	document.getElementById('PhaseInd').style.display		= PhaseInd;
	document.getElementById('PhaseTeamHead').style.display	= PhaseTeamHead;
	document.getElementById('PhaseTeam').style.display		= PhaseTeam;
}

function GetRuleSel(Id, RuleId) {
	try {
		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
			var MyId=encodeURIComponent(Id);
			XMLHttp.open("POST","GetRuleSel.php?Id=" + MyId + '&RuleId=' + RuleId, true);
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=function() {
				if (XMLHttp.readyState!=XHS_COMPLETE || XMLHttp.status!=200) return;

				// leggo l'xml
				var XMLResp=XMLHttp.responseXML;
				// intercetto gli errori di IE e Opera
				if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

				// Intercetto gli errori di Firefox
				var XMLRoot;
				if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw(XMLResp.responseText);

				XMLRoot = XMLResp.documentElement;

				document.getElementById('EventPhaseSel').innerHTML = XMLRoot.getElementsByTagName('result').item(0).firstChild.data;
				document.getElementById('PageSettings').innerHTML = XMLRoot.getElementsByTagName('settings').item(0).firstChild.data;

				XMLHttp = CreateXMLHttpRequestObject();
			};
			XMLHttp.send(null);
		}
	}
	catch (e) {
		//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
	}
}


function selectSchedule(Schedule) {
	try {
		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
			var MyId=encodeURIComponent(Schedule);
			XMLHttp.open("POST","GetSchedule.php?schedule=" + Schedule, true);
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=selectSchedule_StateChange;
			XMLHttp.send(null);
		}
	}
	catch (e) {
		alert(e);
		//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
	}
}

function selectSchedule_StateChange() {
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE) {
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200) {
			try {
				selectSchedule_Response();
			}
			catch(e) {
				//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		} else {
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function selectSchedule_Response() {
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw(XMLResp.responseText);

	XMLRoot = XMLResp.documentElement;

	var phases;
	phases=XMLRoot.getElementsByTagName('event');

	var oldPhases=document.getElementsByTagName('input');
	for(n=0; n<oldPhases.length; n++) {
		if(oldPhases.item(n).id.substr(0,3)=='id_') oldPhases.item(n).checked=false;
	}

	for(n=0; n < phases.length; n++) {
		if(inp=document.getElementById(phases.item(n).firstChild.data))
			inp.checked=true;
	}
	XMLHttp = CreateXMLHttpRequestObject();
}

function GetComboSchedule(teamEvent)
{
	if (XMLHttp)
	{
		try
		{

			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var useHHT = (document.getElementById('useHHT').checked==true ? 1:0);
				var onlyToday = (document.getElementById('onlyToday').checked==true ? 1:0);
				XMLHttp.open("GET","GetComboSchedule.php?useHHT="+useHHT+"&onlyToday="+onlyToday+"&teamEvent="+teamEvent,true);
				XMLHttp.onreadystatechange=GetComboSchedule_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e) { }
	}
}

function GetComboSchedule_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				GetComboSchedule_Response();
			}
			catch(e) { }
		}
	}
}

function GetComboSchedule_Response()
{
	var XMLResp=XMLHttp.responseXML;
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);

	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	if (Error==0)
	{
		var Combo = document.getElementById('d_Scheduler');

		if (Combo)
		{
			var Arr_Code = XMLRoot.getElementsByTagName('val');
			var Arr_Name = XMLRoot.getElementsByTagName('display');

			for (i = Combo.length - 1; i>=0; --i)
				Combo.remove(i);

			Combo.options[0] = new Option("--","");
			for (i=0;i<Arr_Code.length;++i)
				Combo.options[i+1] = new Option(Arr_Name.item(i).firstChild.data,Arr_Code.item(i).firstChild.data);
		}
		XMLHttp = CreateXMLHttpRequestObject();
		selectSchedule(Combo.value);
	}
}

