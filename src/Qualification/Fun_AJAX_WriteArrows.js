/*
													- Fun_AJAX_WriteArrows.js.php -
	Contiene le funzioni ajax che riguardano la pagina WriteArrows.php
*/ 		

var Cache = new Array();	// cache per l'update 

function ManagePostUpdateArrow(chk)
{
	if (!chk) 
	{
		UpdateArrow();
	}
	else 
	{
		PostUpdate=true;
		PostUpdateCnt=0;
	}
}

/*
	- UpdateArrow(Field)
	Invia la POST a UpdateArrow.php
*/
function UpdateArrow(Field)
{
	if (XMLHttp)
	{
		if (Field)
		{
		/*
			Splitted contiene i dati da passare alla pagina nell'ordine:
			Valore da scartare (arr),Distanza,Indice,Id e il valore è quello del campo Field
		*/
			var Splitted = Field.split('_');
			
			var QueryString 
				= 'Dist=' + Splitted[1]
				+ '&Index=' + Splitted[2]
				+ '&Id=' + Splitted[3]
				+ '&Point=' + encodeURIComponent(document.getElementById(Field).value);
			
			Cache.push(QueryString);
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
						XMLHttp.open("POST",RootDir+"UpdateArrow.php",true);
						//document.getElementById('idOutput').innerHTML="UpdateArrow.php?" + QueryString;
						XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
						XMLHttp.onreadystatechange=UpdateArrow_StateChange;
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

function UpdateArrow_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateArrow_Response();
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

function UpdateArrow_Response()
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
	
	var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	var Dist = XMLRoot.getElementsByTagName('dist').item(0).firstChild.data;
	var Index = XMLRoot.getElementsByTagName('index').item(0).firstChild.data;
	var CurScore = XMLRoot.getElementsByTagName('curscore').item(0).firstChild.data;
	var CurGold = XMLRoot.getElementsByTagName('curgold').item(0).firstChild.data;
	var CurXNine = XMLRoot.getElementsByTagName('curxnine').item(0).firstChild.data;
	var Score = XMLRoot.getElementsByTagName('score').item(0).firstChild.data;
	var Gold = XMLRoot.getElementsByTagName('gold').item(0).firstChild.data;
	var Xnine = XMLRoot.getElementsByTagName('xnine').item(0).firstChild.data;
	var Xvalue = XMLRoot.getElementsByTagName('xvalue').item(0).firstChild.data;

	var Which = 'arr_' + Dist + '_' + Index + '_' + Id;
	
	var idCurScore = 'idScore_' + Dist + '_' + Id;
	var idCurGold = 'idGold_' + Dist + '_' + Id;
	var idCurXNine = 'idXNine_' + Dist + '_' + Id;
		
	if (Error==0)
	{
		SetStyle(Which,'');
		document.getElementById(idCurScore).innerHTML=CurScore;
		document.getElementById(idCurGold).innerHTML=CurGold;
		document.getElementById(idCurXNine).innerHTML=CurXNine;
		document.getElementById('idScore_' + Id).innerHTML=Score;
		if(document.getElementById('idGold_' + Id))
			document.getElementById('idGold_' + Id).innerHTML=Gold;
		if(document.getElementById('idXNine_' + Id))
			document.getElementById('idXNine_' + Id).innerHTML=Xnine;
		
		if(document.getElementById("ScoreCard"))
			recalcScoreCard(Id, Dist, Xvalue);
	}
	else
	{
		SetStyle(Which,'error');
	}
	
	// per scaricare la cache degli update	
	setTimeout("UpdateArrow()",250);
}

function recalcScoreCard (AthleteId, Distance, Xvalue)
{
	var NumEnds = document.getElementById("NumEnds").value;
	var MaxArrows = document.getElementById("MaxArrows").value;
	var Arr4End = (MaxArrows/NumEnds);
	
	var totEnd = 0;
	var totEndRun = 0;
	var totDist = 0;
	
	for(i=0; i<MaxArrows; i++)
	{
		tmpValue = document.getElementById('arr_' + Distance + '_' + i + '_' + AthleteId).value;
		if(tmpValue=='X' || tmpValue=='x')
			tmpValue = Xvalue;
		if(tmpValue=='M' || tmpValue=='m')
			tmpValue = 0;
		totEnd += (tmpValue*1.0);
		totEndRun += (tmpValue*1.0);
		totDist += (tmpValue*1.0);
		if(i%Arr4End == (Arr4End-1))
		{
			document.getElementById('idEnd_' + Distance + '_' + i + '_' + AthleteId).innerHTML=totEnd;
			totEnd=0;

			tmp = document.getElementById('idEndRun_' + Distance + '_' + i + '_' + AthleteId);
			if(tmp) {
				tmp.innerHTML=totEndRun;
				totEndRun=0;
			}
			
			tmp = document.getElementById('idScore_' + Distance + '_' + i + '_' + AthleteId);
			if(tmp)
				tmp.innerHTML=totDist;		
			
		}
	}
	document.getElementById('idTotScore_' + Distance + '_' + AthleteId).innerHTML=totDist;
		
}