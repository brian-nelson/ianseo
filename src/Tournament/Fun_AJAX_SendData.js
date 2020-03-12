var PATH=RootDir+'Tournament/TmpDownload/SendData/';

/**
 * Elenco dei files da elaborare.
 * La prima colonna indica il nome file, la seconda lo script da chiamare x generarlo
 */
var files=new Array();

var filesIndex=0;	// indice di files
var filesCount=0;	// lunghezza del vettore

// eventi da passare alla pagina che genera il manifest
var queryManifest='';

var oris=null;

/*
	- makeList()
	Inzializza files in base alle impostazioni della pagina e chiama la funzione per la creazione dei files
*/
function makeList()
{
	oris=Ext.get('oris').dom.checked;

	var idUpStatus=Ext.get('idUpStatus');
	var idStatus=Ext.get('idStatus');

	idUpStatus.dom.innerHTML='';

	var eventList = new Array();
	eventList.push('QualificationInd[]');
	eventList.push('QualificationTeam[]');
	eventList.push('EliminationInd[]');
	eventList.push('FinalInd[]');
	eventList.push('FinalTeam[]');
	eventList.push('BracketsInd[]');
	eventList.push('BracketsTeam[]');

	files=new Array();
	filesIndex=0;
	filesCount=0;

	queryManifest='ev=';

	var x=null;
	var el=null;

	idStatus.dom.innerHTML=StrInitProcess + '...';

	if (OnlineId==0)
	{
		idStatus.dom.innerHTML+=StrError + ': ' + StrNoCredential;
		return;
	}

// StartList - piazzole
	if (Ext.get('ENS').getValue()==1)
	{
		files[filesIndex]=new Array();
		files[filesIndex][0]='ENS';
		if (!oris)
		{
			files[filesIndex][1]=WebDir + 'Partecipants/PrnSession.php?Filled=1&ToFitarco=' + PATH + OnlineId + '_ENS.pdf&Dest=F';
		}
		else
		{
			files[filesIndex][1]=WebDir + 'Partecipants/OrisStartList.php?ToFitarco=' + PATH + OnlineId + '_ENS.pdf&Dest=F';
		}
		++filesIndex;

	// XML
		files[filesIndex]=new Array();
		files[filesIndex][0]='ENS_xml';
		files[filesIndex][1]=WebDir + 'Partecipants/XmlSession.php?ToFitarco=' + PATH + OnlineId + '_ENS.xml';
		++filesIndex;
	// END XML

		queryManifest+='ENS|';
	}

// StartList - societ�
	if (Ext.get('ENC').getValue()==1)
	{
		if (Ext.get('ENC').getValue()==1)
		{
			files[filesIndex]=new Array();
			files[filesIndex][0]='ENC';
			if (!oris)
			{
				files[filesIndex][1]=WebDir + 'Partecipants/PrnCountry.php?ToFitarco=' + PATH + OnlineId + '_ENC.pdf&Dest=F';
			}
			else
			{
				files[filesIndex][1]=WebDir + 'Partecipants/OrisCountry.php?ToFitarco=' + PATH + OnlineId + '_ENC.pdf&Dest=F';
			}
			++filesIndex;

		// XML
			files[filesIndex]=new Array();
			files[filesIndex][0]='ENC_xml';
			files[filesIndex][1]=WebDir + 'Partecipants/XmlCountry.php?ToFitarco=' + PATH + OnlineId + '_ENC.xml';
			++filesIndex;
		// END XML

			queryManifest+='ENC|';
		}
	}

// StartList - alfabetica
	if (Ext.get('ENA').getValue()==1)
	{
		files[filesIndex]=new Array();
		files[filesIndex][0]='ENA';
		if (!oris)
		{
			files[filesIndex][1]=WebDir + 'Partecipants/PrnAlphabetical.php?ToFitarco=' + PATH + OnlineId + '_ENA.pdf&Dest=F';
		}
		else
		{
			files[filesIndex][1]=WebDir + 'Partecipants/OrisAlphabetical.php?ToFitarco=' + PATH + OnlineId + '_ENA.pdf&Dest=F';
		}
		++filesIndex;

	// XML
		files[filesIndex]=new Array();
		files[filesIndex][0]='ENA_xml';
		files[filesIndex][1]=WebDir + 'Partecipants/XmlAlphabetical.php?ToFitarco=' + PATH + OnlineId + '_ENA.xml';
		++filesIndex;
	// END XML

		queryManifest+='ENA|';

	}

// Classifica di classe
	if (Ext.get('IC').getValue()==1)
	{
		files[filesIndex]=new Array();

		files[filesIndex][0]='IC';
		files[filesIndex][1]=WebDir + 'Qualification/PrnIndividual.php?ToFitarco=' + PATH + OnlineId + '_IC.pdf&Dest=F';
		++filesIndex;

	// XML
		files[filesIndex]=new Array();
		files[filesIndex][0]='IC_xml';
		files[filesIndex][1]=WebDir + 'Qualification/XmlIndividual.php?ToFitarco=' + PATH + OnlineId + '_IC.xml';
		++filesIndex;
	// END XML

		queryManifest+='IC|';
	}

	if (Ext.get('TC').getValue()==1)
	{
		files[filesIndex]=new Array();

		files[filesIndex][0]='TC';
		files[filesIndex][1]=WebDir + 'Qualification/PrnTeam.php?ToFitarco=' + PATH + OnlineId + '_TC.pdf&Dest=F';
		++filesIndex;

	// XML
		files[filesIndex]=new Array();
		files[filesIndex][0]='TC_xml';
		files[filesIndex][1]=WebDir + 'Qualification/XmlTeam.php?ToFitarco=' + PATH + OnlineId + '_TC.xml';
		++filesIndex;
	// END XML

		queryManifest+='TC|';
	}

// Medal stand
	if (Ext.get('MEDSTD').getValue()==1)
	{
		files[filesIndex]=new Array();
		files[filesIndex][0]='MEDSTD';
		if (!oris)
		{
			files[filesIndex][1]=WebDir + 'Final/PDFMedalStanding.php?ToFitarco=' + PATH + OnlineId + '_MEDSTD.pdf&Dest=F';
		}
		else
		{
			files[filesIndex][1]=WebDir + 'Final/OrisMedalStanding.php?ToFitarco=' + PATH + OnlineId + '_MEDSTD.pdf&Dest=F';
		}
		++filesIndex;

		queryManifest+='MEDSTD|';
	}

// Medal list
	if (Ext.get('MEDLST').getValue()==1)
	{
		files[filesIndex]=new Array();
		files[filesIndex][0]='MEDLST';
		if (!oris)
		{
			files[filesIndex][1]=WebDir + 'Final/PDFMedalList.php?ToFitarco=' + PATH + OnlineId + '_MEDLST.pdf&Dest=F';
		}
		else
		{
			files[filesIndex][1]=WebDir + 'Final/OrisMedalList.php?ToFitarco=' + PATH + OnlineId + '_MEDLST.pdf&Dest=F';
		}
		++filesIndex;

		queryManifest+='MEDLST|';
	}

	var fileURL='';
	var FileXML='';

	for (var j=0; j<eventList.length;++j)
	{
	// devo accontentarmi di tirare fuori i nodi con il nome che inizia per eventList[j] perch� le quadre nel campo rompono
		var x=Ext.query('*[name^=' + eventList[j].replace('[','').replace(']','') + ']');

		if (eventList[j]=='QualificationInd[]')
			if (!oris)
			{
				fileURL=WebDir + 'Qualification/PrnIndividualAbs.php';
				fileXML=WebDir + 'Qualification/XmlAbsIndividual.php';
			}
			else
			{
				fileURL=WebDir + 'Qualification/OrisIndividual.php';
				fileXML=WebDir + 'Qualification/XmlAbsIndividual.php';
			}
		else if (eventList[j]=='QualificationTeam[]')
			if (!oris)
			{
				fileURL=WebDir + 'Qualification/PrnTeamAbs.php';
				fileXML=WebDir + 'Qualification/XmlAbsTeam.php';
			}
			else
			{
				fileURL=WebDir + 'Qualification/OrisTeam.php';
				fileXML=WebDir + 'Qualification/XmlAbsTeam.php';
			}
		else if (eventList[j]=='EliminationInd[]')
			if (!oris)
			{
				fileURL=WebDir + 'Elimination/PrnIndividual.php';
				fileXML=WebDir + 'Elimination/XmlElimination.php';
			}
			else
			{
				fileURL=WebDir + 'Elimination/OrisIndividual.php';
				fileXML=WebDir + 'Elimination/XmlElimination.php';
			}
		else if (eventList[j]=='FinalInd[]')
			if (!oris)
			{
				fileURL=WebDir + 'Final/Individual/PrnRanking.php';
				fileXML=WebDir + 'Final/Individual/XmlRanking.php';
			}
			else
			{
				fileURL=WebDir + 'Final/Individual/OrisRanking.php';
				fileXML=WebDir + 'Final/Individual/XmlRanking.php';
			}
		else if (eventList[j]=='FinalTeam[]')
			if (!oris)
			{
				fileURL=WebDir + 'Final/Team/PrnRanking.php';
				fileXML=WebDir + 'Final/Team/XmlRanking.php';
			}
			else
			{
				fileURL=WebDir + 'Final/Team/OrisRanking.php';
				fileXML=WebDir + 'Final/Team/XmlRanking.php';
			}
		else if (eventList[j]=='BracketsInd[]')
			if (!oris)
			{
				fileURL=WebDir + 'Final/Individual/PrnBracket.php';
				fileXML=WebDir + 'Final/Individual/XmlBracket.php';
			}
			else
			{
				fileURL=WebDir + 'Final/Individual/OrisBracket.php';
				fileXML=WebDir + 'Final/Individual/XmlBracket.php';
			}
		else if (eventList[j]=='BracketsTeam[]')
			if (!oris)
			{
				fileURL=WebDir + 'Final/Team/PrnBracket.php';
				fileXML=WebDir + 'Final/Team/XmlBracket.php';
			}
			else
			{
				fileURL=WebDir + 'Final/Team/OrisBracket.php';
				fileXML=WebDir + 'Final/Team/XmlBracket.php';
			}

		for (var i=0;i<x.length;++i)
		{
			el=Ext.get(x[i].id);

			if (el.dom.checked)
			{
				var event=el.id.substr(2,el.id.length-1);

				files[filesIndex]=new Array();

				files[filesIndex][0]=el.id;
				files[filesIndex][1]=fileURL + '?Event=' + event +  '&ToFitarco=' + PATH + OnlineId + '_' + files[filesIndex][0] + '.pdf' + '&Dest=F' + (oris ? "&ShowTargetNo=1&ShowSchedule=1" : "");
				++filesIndex;

			// XML
				files[filesIndex]=new Array();
				files[filesIndex][0]=el.id + '_xml';
				files[filesIndex][1]=fileXML + '?Event=' + event +  '&ToFitarco=' + PATH + OnlineId + '_' + files[filesIndex][0].replace('_xml','') + '.xml';
				++filesIndex;
			// END XML

				queryManifest+=el.id + '|';
			}
		}
	}

	filesCount=files.length;

	filesIndex=0;	// riporto a zero l'indice

	queryManifest=queryManifest.substr(0,queryManifest.length-1);
	//console.debug(files);return;
	idStatus.dom.innerHTML+=StrOk;
	//console.debug(filesCount);return;

/*
	Faccio tutte le chiamate async verso il generatore di pdf.
	Quando tutti files sono stati generati creo il manifest.

	Sta roba sostituisce la vecchia callURL()
*/
	idStatus.dom.innerHTML+= '<br>' + StrCreateFiles + '...';

	for (var i=0;i<files.length;++i)
	{
		//idStatus.dom.innerHTML+= '<br>' + StrMakingFile + ' ' + files[i][0] + '.pdf ...');

		var o=
		{
			url: files[i][1],
			method: 'POST',
			success: function(response)
			{
			// se non sono finite tutte le request non faccio nulla
				if (--filesCount>0)
				{
					return;
				}
			// qui vuol dire che tutte le chiamate sono terminate quindi vai con il manifest!
				else
				{
					idStatus.dom.innerHTML+= StrOk;
					makeManifest();
				}
			}
		}

		Ext.Ajax.request(o);
	}
}

function makeManifest()
{
	var idStatus=Ext.get('idStatus');

	idStatus.dom.innerHTML+= '<br>' + StrMakingManifest + '...';

	var o=
	{
		url: WebDir + 'Tournament/TmpDownload/SendData/MakeManifest.php?' + queryManifest,
		method: 'POST',
		success: function(response)
		{
			var xmlResp={};

			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);

				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;

				switch (error)
				{
					case '0':
						idStatus.dom.innerHTML+=StrOk;
						callZip();
						break;

					case '1':
						idStatus.dom.innerHTML+=StrError;
						break;
				}
			}
			catch (e)
			{
				alert(e.toString());
			}
		}
	}

	Ext.Ajax.request(o);
}

function callZip()
{
//console.debug('zip');
	var idStatus=Ext.get('idStatus');

	idStatus.dom.innerHTML+= '<br>' + StrMakingZip + '...';

	var o=
	{
		url: WebDir + 'Tournament/TmpDownload/SendData/MakeZip2Send.php',
		method: 'POST',
		success: function(response)
		{
			var xmlResp={};

			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);

				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;

				switch (error)
				{
					case '0':
						idStatus.dom.innerHTML+=StrOk;
						//return;
						sendOnline();
						break;

					case '1':
						idStatus.dom.innerHTML+=StrError;
						break;
				}
			}
			catch (e)
			{
				alert(e.toString());
			}
		}
	}

	Ext.Ajax.request(o);
}

function sendOnline()
{
	var idStatus=Ext.get('idStatus');

	idStatus.dom.innerHTML+= '<br>' + StrSendData + '...';

	var o=
	{
		url: WebDir + 'Tournament/TmpDownload/SendData/Send.php',
		method: 'POST',
		timeout: 36000000,		// ms per il timeout della request
		success: function(response)
		{
			var xmlResp={};

			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);

				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;

				var ErrorCode = xmlRoot.getElementsByTagName('error_code').item(0).firstChild.data;
				var ErrorMsg = xmlRoot.getElementsByTagName('error_msg').item(0).firstChild.data;
				var CurlErrorCode = xmlRoot.getElementsByTagName('curl_error_code').item(0).firstChild.data;
				var CurlErrorMsg = xmlRoot.getElementsByTagName('curl_error_msg').item(0).firstChild.data;

				if (error!=0)
				{
					idStatus.dom.innerHTML+=StrError;
				}
				else if (CurlErrorCode!=0)
				{
					idStatus.dom.innerHTML+=StrErrorCode + ': ' + CurlErrorCode + ' --> ' + CurlErrorMsg;
					return;
				}
				else if (ErrorCode!=0)
				{
					idStatus.dom.innerHTML+=StrErrorCode + ': ' + ErrorCode + ' --> ' + ErrorMsg;
					return;
				}

				idStatus.dom.innerHTML+=StrOk;
			}
			catch (e)
			{
				alert(e.toString());
			}
		}
	}

	Ext.Ajax.request(o);
}

function deleteOnline()
{
	var idUpStatus=Ext.get('idUpStatus');

	idUpStatus.dom.innerHTML='';

	if (OnlineId==0)
	{
		idUpStatus.dom.innerHTML+=StrError + ': ' + StrNoCredential;
		return;
	}

	if (confirm(StrMsgAreYouSure))
	{
		idUpStatus.dom.innerHTML=StrDeleting + '...';
		var o=
		{
			url: WebDir + 'Tournament/TmpDownload/SendData/DeleteOnline.php',
			method: 'POST',
			success: function(response)
			{
				var xmlResp={};

				try
				{
					xmlResp=response.responseXML;
					xmlRoot=Ext.util.xmlResponse(xmlResp);

					var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;

					var ErrorCode = xmlRoot.getElementsByTagName('error_code').item(0).firstChild.data;
					var ErrorMsg = xmlRoot.getElementsByTagName('error_msg').item(0).firstChild.data;
					var CurlErrorCode = xmlRoot.getElementsByTagName('curl_error_code').item(0).firstChild.data;
					var CurlErrorMsg = xmlRoot.getElementsByTagName('curl_error_msg').item(0).firstChild.data;

					if (error!=0)
					{
						idUpStatus.dom.innerHTML+=StrError;
					}
					else if (CurlErrorCode!=0)
					{
						idUpStatus.dom.innerHTML+=StrErrorCode + ': ' + CurlErrorCode + ' --> ' + CurlErrorMsg;
						return;
					}
					else if (ErrorCode!=0)
					{
						idUpStatus.dom.innerHTML+=StrErrorCode + ': ' + ErrorCode + ' --> ' + ErrorMsg;
						return;
					}

					idUpStatus.dom.innerHTML+=StrOk;
				}
				catch (e)
				{
					alert(e.toString());
				}
			}
		}

		Ext.Ajax.request(o);
	}
}
