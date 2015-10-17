var Cache = new Array();
var ShootFirst='';

// calcola il timestamp da accodare alla querystring per bypassare la cache del browser
function ts4qs()
{
	var date=new Date();

	var day=''+date.getDay();
	var month=''+date.getMonth();
	var year=''+date.getFullYear();
	var hours=''+date.getHours();
	var min=''+date.getMinutes();
	var sec=''+date.getSeconds();
	var milli=''+date.getMilliseconds();

	if (day.length<2)
		day='0'+day;
	if (month.length<2)
		month='0'+month;
	if (hours.length<2)
		hours='0'+hours;
	if (min.length<2)
		min='0'+min;
	if (sec.length<2)
		sec='0'+sec;
	if (milli.length<3)
		milli='0'+milli;

	return (year+month+day+hours+min+sec+milli);
}


function makeScore(TeamEvent)
{
	try
	{
		var chunk=document.getElementById('outputChunk');

		chunk.innerHTML='';

		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
		{
			var ev=document.getElementById('d_Event').value;
			var mm=document.getElementById('d_Match').value;
			var mode=document.getElementById('d_Modes').value;


			XMLHttp.open("GET",WebDir+"Final/ScoreCardChunk.php?d_Event=" + ev + "&d_Match="  +  mm + "&d_Team=" + TeamEvent + "&d_Mode=" + mode + "&ts=" + ts4qs(),true);
// 			document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=makeScore_StateChange;
			XMLHttp.send(null);
		}
	}
	catch (e)
	{
		//console.debug('Errore: ' + e.toString());
	}
}

function makeScore_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				makeScore_Response();
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

function makeScore_Response()
{
	var resp=XMLHttp.responseText;
	var chunk=document.getElementById('outputChunk');
	chunk.innerHTML=resp;

	var inputs=document.getElementsByTagName("input");

	for (i=0;i<inputs.length;++i)
	{
		//console.debug(inputs[i]);
		if (inputs[i].type=='text' && (inputs[i].id.substr(0,2)=='s_' || inputs[i].id.substr(0,2)=='t_') )
		{
			//console.debug(inputs[i].name);
			inputs[i].setAttribute('onblur','updateScore(this.id);');
		}
	}
	document.getElementById('buttonMove2Next').disabled = (document.getElementById('d_Modes').value == 2)
}


function updateScore(which)
{
/*
 * which è nella forma:
 * [st]_match_row
 *
 * con row la riga dello score
 *
 * which è il nome e non l'id e indica un gruppo di textbox
 */
	if (XMLHttp)
	{
		var qs='';

		if (which)
		{
			var index=0;
			var match=-1;
			var what='s';
			var event=document.getElementById('event').value;
			var team=document.getElementById('team').value;
			var alternate='';
			if(document.getElementById('alternate') && document.getElementById('alternate').checked) alternate='1';

			var split=which.split('_');

			what = split[0];
			match = split[1];
			index = split[2];

			// prendo tutte le frecce della volee o del tie
			var obj=document.getElementById(which);

			if(obj !== null)
			{
				qs
					= "what=" + what
					+ "&alternate=" + alternate
					+ "&matchfirst=" + ShootFirst
					+ "&match=" + match
					+ "&index=" + index
					+ "&arrow=" + obj.value
					+ "&event=" + event
					+ "&team=" + team
					+ "&ts=" + ts4qs();
				Cache.push(qs);
			}
		}

		try
		{


			//console.debug(arrows + ' - ' + from);

			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0)
			{
				//console.debug('dd');
				var FromCache = Cache.shift();

				//console.debug(FromCache);

				XMLHttp.open("POST",WebDir+"Final/UpdateScoreCard.php",true);
	// 			document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=updateScore_StateChange;
				XMLHttp.send(FromCache);
			}

		}
		catch (e)
		{
			//console.debug('Errore: ' + e.toString());
		}
	}
}

function updateScore_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				updateScore_Response();
			}
			catch(e)
			{
				//console.debug('Errore: ' + e.toString());
			}
		}
		else
		{
			//console.debug('Errore: ' + e.toString());
		}
	}
}

function updateScore_Response()
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
	var msg=XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	var spotEnable=XMLRoot.getElementsByTagName('spot_enable').item(0).firstChild.data;
	var spotStart=XMLRoot.getElementsByTagName('spot_start').item(0).firstChild.data;
	var spotEnd=XMLRoot.getElementsByTagName('spot_end').item(0).firstChild.data;
	var nextArrow=XMLRoot.getElementsByTagName('alternate').item(0).firstChild.data;

	document.getElementById('spotEnable').value = spotEnable;
	document.getElementById('spotStart').value = spotStart;
	document.getElementById('spotEnd').value = spotEnd;

	if (Error==0)
	{
		document.getElementById('msg').innerHTML='';

	// tiro fuori tutti i nodi figli sotto arrows
		var arr=XMLRoot.getElementsByTagName('arrows').item(0);

		for (i=0;i<arr.childNodes.length;++i)
		{
			var node=arr.childNodes[i];

		// se è un nodo valido (no spazi bianchi)
			if (node.nodeType==1)
			{
				if (document.getElementById(node.nodeName)!=null)
				{
					SetStyle(node.nodeName,'');
					if(node.firstChild)
						document.getElementById(node.nodeName).value = node.firstChild.data;
					else if(document.getElementById(node.nodeName).value)
						SetStyle(node.nodeName,'error');

					var arrField=node.nodeName.split("_");
					if(((arrField[0]=='s' && spotEnable==0) || (arrField[0]=='t' && spotEnable==1)) && (parseInt(arrField[2])>=spotStart && parseInt(arrField[2])<=spotEnd) && document.getElementById("spot_" + arrField[1] + "_" + (parseInt(arrField[2])-spotStart)))
					{
						document.getElementById("spot_" + arrField[1] + "_" + (arrField[2]-spotStart)).innerHTML = document.getElementById(node.nodeName).value;
						if(spotEnable==1)
						{
							for($i=parseInt(spotEnd)+1; $i<document.getElementById('cols').value;$i++)
								document.getElementById("spot_" + arrField[1] + "_" + $i).innerHTML = '&nbsp;';
						}
					}

				}
			}
		}


	// tiro fuori tutti i nodi figli sotto results
		var res=XMLRoot.getElementsByTagName('results').item(0);

		//console.debug('res');
		for (i=0;i<res.childNodes.length;++i)
		{
			var node=res.childNodes[i];

		// se è un nodo valido (no spazi bianchi)
			if (node.nodeType==1)
			{
				//console.debug(node.nodeName + ': ' + node.firstChild.data);
				if (document.getElementById(node.nodeName)!=null)
					document.getElementById(node.nodeName).innerHTML=node.firstChild.data;
			}
		}
	}
	else
	{
		document.getElementById('msg').innerHTML=msg;
	}

	if(document.getElementById('dispDanage'))
	{
		if(document.getElementById('dispDanage').checked)
			updateDanageDisplay();
	}

	if(nextArrow) document.getElementById(nextArrow).focus();

	setTimeout("updateScore()",500);
}

function setLive(TeamEvent)
{
	try
	{
		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
		{
			var ev=document.getElementById('d_Event').value;
			var mm=document.getElementById('d_Match').value;


			XMLHttp.open("GET",WebDir+"Final/UpdateLive.php?d_Event=" + ev + "&d_Match="  +  mm + "&d_Team=" + TeamEvent,true);
// 			document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=setLive_StateChange;
			XMLHttp.send(null);
		}
	}
	catch (e)
	{
		//console.debug('Errore: ' + e.toString());
	}
}

function setLive_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				setLive_Response();
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

function setLive_Response()
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
	var msg=XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;


	if (Error==0)
	{
		var live=XMLRoot.getElementsByTagName('live').item(0).firstChild.data;
		var livemsg=XMLRoot.getElementsByTagName('livemsg').item(0).firstChild.data;
		document.getElementById('liveButton').value=livemsg;
		if(live==1)
			SetStyle('liveButton','error');
		else
			SetStyle('liveButton','');
	}
	else
	{
		document.getElementById('msg').innerHTML=msg;
	}
}

function saveCommentary(TeamEvent)
{
	try
	{
		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
		{
			var params = "";

			params += "d_Event=" + document.getElementById('d_Event').value;
			params += "&d_Match="  +  document.getElementById('d_Match').value;
			params += "&d_Team=" + TeamEvent;
			params += "&Review1=" + document.getElementById('Lang1').value;
			params += "&Review2=" + document.getElementById('Lang2').value;

			XMLHttp.open("POST",WebDir+"Final/UpdateCommentary.php",true);
// 			document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev=" + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.setRequestHeader("Content-length", params.length);
			XMLHttp.setRequestHeader("Connection", "close");
			XMLHttp.onreadystatechange=saveCommentary_StateChange;
			XMLHttp.send(params);
		}
	}
	catch (e)
	{
		//console.debug('Errore: ' + e.toString());
	}
}

function saveCommentary_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				saveCommentary_Response();
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

function saveCommentary_Response()
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
	var msg=XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;


	if (Error==0)
	{
		SetStyle('Lang1','');
		SetStyle('Lang2','');
	}
	else
	{
		SetStyle('Lang1','error');
		SetStyle('Lang2','error');
	}
}

function targetClick(side, size, event){

	if (!event)
		var event = document.event;

	var xpos = (event.offsetX ? event.offsetX : (event.layerX ? event.layerX : 0));
	var ypos = (event.offsetY ? event.offsetY : (event.layerY ? event.layerY : 0));
	//alert("Mouse X: " + xpos + "\nMouse Y: " + ypos);

	var match=side;
	var event=document.getElementById('event').value;
	var team=document.getElementById('team').value;

	if(size!=0 && xpos!=0 && ypos!=0)
	{
		qs
			= "match=" + match
			+ "&event=" + event
			+ "&team=" + team
			+ "&size=" + size
			+ "&x=" + xpos
			+ "&y=" + ypos
			+ "&ts=" + ts4qs();

		Cache.push(qs);
	}

	updateScore();
}

function clickStar(clicked, star)
{
	var arrField=clicked.split("_");
	if(clicked && (document.getElementById('spotEnable').value==0 || document.getElementById('spotEnable').value==1))
	{
		var field = document.getElementById((document.getElementById('spotEnable').value==0 ? "s" : "t") + "_" + arrField[1] + "_" + (parseInt(document.getElementById('spotStart').value)+parseInt(arrField[2])));
		if(field.value)
		{
			if(star)
			{
				if(field.value.indexOf("*")=="-1")
				{
					field.value += "*";
					updateScore(field.id);
				}
				else
				{
					field.value = field.value.replace("*","");
					updateScore(field.id);
				}
			}
			else
			{
				field.value = "";
				updateScore(field.id);
			}
		}
	}
}

function showOptions()
{
	document.getElementById('options').hidden=!document.getElementById('options').hidden;
}

function setStarter(obj) {
	var tmp=obj.split('_');
	var tmpOpp=obj.split('_');
	if(tmp[1]/2==Math.floor(tmp[1]/2)) tmpOpp[1]=parseInt(tmp[1])+1;
	else tmpOpp[1]=parseInt(tmp[1])-1;

	var objOpp=tmpOpp.join('_');

	if(document.getElementById(obj).value=='' && document.getElementById(objOpp).value=='') ShootFirst=tmp[1];
	document.getElementById(obj).select();
}