var Files2Exp = new Array(); // Files da esportare
var FileIndex=0;	// Indice di Files2Exp
var EventCode = '';	// codice gara

/*
	- ExportAndSend()
	Se i controlli sui campi sono ok e i file vengono generati allora spedisce il tutto sul sito
	Code è il codice gara
	RootDir è la document root
	Ind>0 significa che occorre esportare anche le finali individuali
	Team>0 significa che occorre esportare anche le finali a squadra
	Ritorna true se ok; false altrimenti
*/
function ExportAndSend(Code,RootDir,Ind,Team)
{
	EventCode=Code;

	document.getElementById('Report').innerHTML='';

	var Ret=false;
// Verifico la mail
	if(ValidateEMail())
	{
		Ret=true;

		Files2Exp = new Array(); // Files da esportare
		FileIndex=0;

	// ASCII

	// Qualificazioni
		Files2Exp.push(WebDir+'Tournament/ExportASC.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '.asc');
		Files2Exp.push(WebDir+'Qualification/LST_Individual.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '.lst');
		Files2Exp.push(WebDir+'Qualification/LST_Team.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_team.lst');

	// Finali
		if (Ind>0)
			Files2Exp.push(WebDir+'Final/Individual/LST_Individual.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_rank.lst');
		if (Team>0)
			Files2Exp.push(WebDir+'Final/Team/LST_Team.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_rank_team.lst');

	// PDF

	// Qualificazioni
		Files2Exp.push(WebDir+'Qualification/PrnIndividual.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '.pdf&Dest=F');
		Files2Exp.push(WebDir+'Qualification/PrnTeam.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_team.pdf&Dest=F');

	// Finali
		if (Ind>0)
		{
			Files2Exp.push(WebDir+'Final/Individual/PrnRanking.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_rank.pdf&Dest=F');
			Files2Exp.push(WebDir+'Final/Individual/PrnBracket.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_grid.pdf&Dest=F');
		}
		if (Team>0)
		{
			Files2Exp.push(WebDir+'Final/Team/PrnRanking.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_rank_team.pdf&Dest=F');
			Files2Exp.push(WebDir+'Final/Team/PrnBracket.php?ToFitarco=' + RootDir + 'Tournament/TmpDownload/' + Code + '_grid_team.pdf&Dest=F');
		}

	// Provo ad esportare in automatico i files
		if (Files2Exp.length>0)
			CallURL();
	}


	return Ret;
}

/*
	- CallURL()
	Prova ad esportare il file indicizzato da FileIndex
*/
function CallURL()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var TheURL=Files2Exp[FileIndex];
				var Report=document.getElementById('Report');
				Report.innerHTML+= '<br>' + Msg_MakingFile + ' ' + TheURL + ' ...';

				XMLHttp.open("GET",TheURL,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=CallURL_StateChange;
				XMLHttp.send(null);
			}

		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}

}

function CallURL_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CallURL_Response();
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

function CallURL_Response()
{
	// leggo l'xml
	var Resp=XMLHttp.responseText;
	var Report = document.getElementById('Report');

	if (Resp!='')
		Report.innerHTML+=Msg_Error;
	else
		Report.innerHTML+=Msg_Ok;

	if (FileIndex<(Files2Exp.length-1))
	{
		++FileIndex;
		CallURL();
	}

	SendPOST();
}

function SendPOST()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString = '?';

				QueryString
					+= 'Code=' + EventCode
					+  '&From=' + document.getElementById('d_RefEmail').value
					+  '&Message=' + document.getElementById('d_Notes').value;

				XMLHttp.open("GET",WebDir+'Tournament/Send2Fitarco.php' + QueryString,true);
			//	document.getElementById('idOutput').innerHTML=WebDir+'Tournament/Send2Fitarco.php' + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SendPOST_StateChange;
				XMLHttp.send(null);
			}

		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}

}

function SendPOST_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SendPOST_Response();
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

function SendPOST_Response()
{
	// leggo l'xml
	var Resp=XMLHttp.responseText;
	var Report = document.getElementById('Report');

	Report.innerHTML+= '<br>' + Resp;
}

/*
	- ValidateEMail()
	Se l'email inserito è valido scrive ok nel report altrimenti segnala l'errore
	Ritorna true se è ok; false altrimenti
*/
function ValidateEMail()
{
	var mm = document.getElementById('d_RefEmail').value;
	var Report= document.getElementById('Report');

	Report.innerHTML+='<br>' + Msg_CheckRefMail;

	if (CheckMail(mm))
	{
		Report.innerHTML+=Msg_Ok;
		return true;
	}
	else
	{
		Report.innerHTML+=Msg_Error;
		return false;
	}
}
