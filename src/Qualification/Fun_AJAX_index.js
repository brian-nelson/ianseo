/*
													- Fun_AJAX_index.js -
	Contiene le funzioni ajax che riguardano le pagine:
	 	index.php
	 	index_all.php
	 	WriteArrows.php
	 	PrintBackNo.php
	 	PrintScore.php
*/


var Cache = new Array();	// cache per l'update
var PostUpdate=false;		// true se è partito il postupdate. verrà rimesso a false dopo che la coda si è svuotata
var PostUpdateCnt=0;		// Contatore degli aggiornamenti in postupdate, per decidere se ricalcolare o no

function ManagePostUpdate(chk)
{
	if (!chk)
	{
		UpdateQuals();
	}
	else
	{
		PostUpdate=true;
		PostUpdateCnt=0;
	}
}

function PostUpdateMessage()
{
	//document.getElementById('idPostUpdateMessage').innerHTML=PostUpdating;
	document.getElementById('PostUpdateMask').style.visibility="visible";
}

function ResetPostUpdate()
{
	PostUpdate=false;
	//document.getElementById('idPostUpdateMessage').innerHTML='';
	document.getElementById('PostUpdateMask').style.visibility="hidden";
	alert(PostUpdateEnd);
}

/*
	- UpdateQuals(Field)
	Invia la GET a UpdateQuals.php
*/
function UpdateQuals(Field)
{
	if (XMLHttp)
	{
		if (Field)
		{
			var FieldName = encodeURIComponent(Field);
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			Cache.push(FieldName + "=" + FieldValue);
			PostUpdateCnt++;
		}


		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT))
				{
					if (Cache.length>0)
					{
						var FromCache = Cache.shift();
						XMLHttp.open("POST",RootDir+"UpdateQuals.php",true);
						//document.getElementById('idOutput').innerHTML="UpdateQuals.php?" + FieldName + "=" + FieldValue;
						XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
						XMLHttp.onreadystatechange=UpdateQuals_StateChange;
						if (PostUpdate)
							FromCache += "&NoRecalc=1";
						XMLHttp.send(FromCache);
					}
					else
					{
						if (!document.getElementById('chk_PostUpdate').checked)
						{
							if (PostUpdate)
							{
								PostUpdateMessage();
								if(PostUpdateCnt != 0)
								{
									CalcRank(true);
									XMLHttp = CreateXMLHttpRequestObject();
									CalcRank(false);
									XMLHttp = CreateXMLHttpRequestObject();
									MakeTeams();
								}
								ResetPostUpdate();
							}
						}
					}
				}
			}
			else
				Cache.shift();
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}

	}
}

function UpdateQuals_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateQuals_Response();
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

function UpdateQuals_Response()
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
	var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;

	if (Error==0)
	{
		var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
		var Score = XMLRoot.getElementsByTagName('score').item(0).firstChild.data;
		var Gold = XMLRoot.getElementsByTagName('gold').item(0).firstChild.data;
		var XNine = XMLRoot.getElementsByTagName('xnine').item(0).firstChild.data;

		document.getElementById('idScore_' + Id).innerHTML=Score;
		document.getElementById('idGold_' + Id).innerHTML=Gold;
		document.getElementById('idXNine_' + Id).innerHTML=XNine;

		SetStyle(Which,'');
	}
	else
	{
		SetStyle(Which,'error');
	}

	// per scaricare la cache degli update
	setTimeout("UpdateQuals()",250);
}

/*
	- MakeTeams()
	Invia la GET a MakeTeams.php
*/
function MakeTeams()
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("GET",RootDir+"MakeTeams.php",true);
					//document.getElementById('idOutput').innerHTML="MakeTeams.php";
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=MakeTeams_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function MakeTeams_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				MakeTeams_Response();
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

function MakeTeams_Response()
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
	var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	alert(Msg);

	XMLHttp = CreateXMLHttpRequestObject();
	MakeTeamsAbs();
}

/*
	- MakeTeamsAbs()
	Invia la GET a MakeTeamsAbss.php
*/
function MakeTeamsAbs()
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("GET","MakeTeamsAbs.php",true);
					//document.getElementById('idOutput').innerHTML="MakeTeamsAbs.php";
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=MakeTeamsAbs_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function MakeTeamsAbs_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				MakeTeamsAbs_Response();
			}
			catch(e)
			{
				document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function MakeTeamsAbs_Response()
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
	var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	alert(Msg);
}




/*
	- CalcRank(Dist=false)
	Invia la GET a CalcRank.php
	Se Dist è false, chiama senza Dist.
	Se Dist è true, occorre che la distanza sia stata selezionata
*/

function CalcRank(Dist)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if (!Dist || (Dist && document.getElementById('x_Dist').value!=-1))
				{
					if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
					{
						XMLHttp.open("GET","CalcRank.php" + (Dist ? "?Dist=" + document.getElementById('x_Dist').value : ""),true);
					//	document.getElementById('idOutput').innerHTML="CalcRank.php" + (Dist ? "Dist=" + document.getElementById('x_Dist').value : "");
						XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
						XMLHttp.onreadystatechange=CalcRank_StateChange;
						XMLHttp.send(null);
					}
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function CalcRank_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CalcRank_Response();
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

function CalcRank_Response()
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
	var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	alert(Msg);
}

function SelectSession()
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				var Ses = encodeURIComponent(document.getElementById('x_Session').value);
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("GET","SelectSession.php?Ses=" + Ses,true);
					//document.getElementById('idOutput').innerHTML="SelectSession.phpSes=" + Ses;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=SelectSession_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function SelectSession_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectSession_Response();
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

function SelectSession_Response()
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
		var Minimo = XMLRoot.getElementsByTagName('minimo').item(0).firstChild.data;
		var Massimo = XMLRoot.getElementsByTagName('massimo').item(0).firstChild.data;

		document.getElementById('x_From').value=(Minimo!='#' ? Minimo : '');
		document.getElementById('x_To').value=(Massimo!='#' ? Massimo : '');
	}
}

/*
	- Went2Home(Id)
	Invia la get a Went2Home.php per ritirare una persona.
	Id � l'id del tizio
*/
function Went2Home(Id) {
	if($('#chk_BlockAutoSave:checked').length==0 && confirm(MsgAreYouSure)) {
		// create loader icon
		var loader=$('<div style="position:absolute;left:50%;top:150px"><img src="../Common/Images/ajax-loader.gif"></div>');
		$('#Content').append(loader);

		$.getJSON('Went2Home.php?Id=' + Id, function(data) {
			if (data.error==0) {
				// var Id = XMLRoot.getElementsByTagName('ath').item(0).firstChild.data;
				// var Retired = XMLRoot.getElementsByTagName('retired').item(0).firstChild.data;
				// var NewStatus = XMLRoot.getElementsByTagName('newstatus').item(0).firstChild.data;
				// var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

				if(data.NewStatus==1) {
					$('#Row_'+data.Id).toggleClass('NoShoot', false);
					$('#Went2Home_' + data.Id).html(MsgWent2Home);
				} else {
					$('#Row_'+data.Id).toggleClass('NoShoot', true);
					$('#Went2Home_' + data.Id).html(MsgBackFromHome);
				}

				$.each([1,2,3,4,5,6,7,8], function() {
					$('#d_QuD' + this + 'Score_' + data.Id).val('0').prop('disabled', data.Retired==1);
					$('#d_QuD' + this + 'Gold_' + data.Id).val('0').prop('disabled', data.Retired==1);
					$('#d_QuD' + this + 'Xnine_' + data.Id).val('0').prop('disabled', data.Retired==1);
				});

				$('#idScore_'  + data.Id).html('0');
				$('#idGold_'  + data.Id).html('0');
				$('#idXNine_'  + data.Id).html('0');
			}
			loader.remove();
			alert(data.Msg);
		});
	}
}

function saveSnapshotImage()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var ses=document.getElementById('x_Session').value;
				var from=document.getElementById('x_From').value;
				var to=document.getElementById('x_To').value;
				var dist=document.getElementById('x_Dist').value;
				var qs
					= '?Session=' + ses
					+ '&Distance=' + dist
					+ '&fromTarget=' + from
					+ '&toTarget=' + to;
				XMLHttp.open("GET",RootDir+"MakeSnapshot.php" + qs,true);
				//document.getElementById('idOutput').innerHTML="MakeSnapshot.php";
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=saveSnapshotImage_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function saveSnapshotImage_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				saveSnapshotImage_Response();
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

function saveSnapshotImage_Response()
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
	var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	alert(Msg);
}

function Disqualify(Id) {
	if($('#chk_BlockAutoSave:checked').length==0 && confirm(MsgAreYouSure)) {
		// create loader icon
		var loader=$('<div style="position:absolute;left:50%;top:150px"><img src="../Common/Images/ajax-loader.gif"></div>');
		$('#Content').append(loader);

		$.getJSON("Disqualify.php?Id=" + Id,function(data) {
			if (data.Error==0) {
				if(data.NewStatus==1) {
					$('#Row_'+data.Id).toggleClass('Dsq', false);
					$('#Disqualify_' + data.Id).html(MsgSetDSQ);
				} else {
					$('#Row_'+data.Id).toggleClass('Dsq', true);
					$('#Disqualify_' + data.Id).html(MsgUnsetDSQ);
				}
			}

			loader.remove();
			alert(data.Msg);
		});
	}
}

