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

function updateSpot(obj) {
    updateScore(obj)
    obj.select();
}

function makeScore(TeamEvent) {
	try {
		var chunk=document.getElementById('outputChunk');

		chunk.innerHTML='';

		if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
			var ev=document.getElementById('d_Event').value;
			var mm=document.getElementById('d_Match').value;
			var mode=document.getElementById('d_Modes').value;


			XMLHttp.open("GET",WebDir+"Final/ScoreCardChunk.php?d_Event=" + ev + "&d_Match="  +  mm + "&d_Team=" + TeamEvent + "&d_Mode=" + mode + "&ts=" + ts4qs(),true);
			XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange=function() {
				if (XMLHttp.readyState==XHS_COMPLETE && XMLHttp.status==200) {
					var resp=XMLHttp.responseText;
					var chunk=document.getElementById('outputChunk');
					chunk.innerHTML=resp;

					if(mm==128 && ElimPool==1) {
					    var MoveTd=document.getElementById('buttonMove2Next').parentNode;
					    MoveTd.innerHTML='<select id="buttonMove2Next" onchange="move2nextPhase(document.getElementById(\'d_Event\').value,document.getElementById(\'d_Match\').value,0, this.value);"><option value="0">'+Select+'</option><option value="A">'+MoveWinner2PoolA+'</option><option value="B">'+MoveWinner2PoolB+'</option></select>';
                    } else {
					    var MoveTd=document.getElementById('buttonMove2Next').parentNode;
                        MoveTd.innerHTML='<input type="button" id="buttonMove2Next" value="'+MoveWinner2NextPhase+'" onclick="move2nextPhase(document.getElementById(\'d_Event\').value,document.getElementById(\'d_Match\').value,'+TeamEvent+');"/>';
                    }

					document.getElementById('buttonMove2Next').disabled = (document.getElementById('d_Modes').value == 2)

					for(var i=0; i<=document.getElementById('rows').value; i++) {
						var shootsFirst1=document.getElementById('first['+document.getElementById('team').value+']['+document.getElementById('event').value+']['+document.getElementById('match1').value+']['+i+']');
						var shootsFirst2=document.getElementById('first['+document.getElementById('team').value+']['+document.getElementById('event').value+']['+document.getElementById('match2').value+']['+i+']');
						if(shootsFirst1 && shootsFirst1.checked) {
							setShootingFirst(shootsFirst1);
						} else if(shootsFirst2 && shootsFirst2.checked) {
							setShootingFirst(shootsFirst2);
						}
					}

				}
			};
			XMLHttp.send(null);
		}
	} catch (e) {
		//console.debug('Errore: ' + e.toString());
	}
}

function updateScore(obj) {
/*
 * which è nella forma:
 * [st]_match_row
 *
 * con row la riga dello score
 *
 * which è il nome e non l'id e indica un gruppo di textbox
 */
	var XMLHttp=CreateXMLHttpRequestObject(); // private ajax value, overrides the global and eliminates the need for a queue management!

	if (XMLHttp) {
		var qs='';
		var index=0;
		var match=-1;
		var what='s';
		var event=document.getElementById('event').value;
		var team=document.getElementById('team').value;

		if(obj !== undefined) {
			var tmp = obj.id;
			var split=tmp.split('_');

			what = split[0];
			match = split[1];
			index = split[2];

			qs = "what=" + what
			+ "&matchfirst=" + ShootFirst
			+ "&match=" + match
			+ "&index=" + index
			+ "&arrow=" + obj.value
			+ "&event=" + event
			+ "&team=" + team
			+ "&ts=" + ts4qs();

			Cache.push(qs);
		}

		if(Cache.length>0) {
			qs=Cache.shift();

			try {
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {

					XMLHttp.open("POST",WebDir+"Final/UpdateScoreCard.php",true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=function() {
						if (XMLHttp.readyState==XHS_COMPLETE && XMLHttp.status==200) {
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

							document.getElementById('spotEnable').value = spotEnable;
							document.getElementById('spotStart').value = spotStart;
							document.getElementById('spotEnd').value = spotEnd;

							if (Error==0) {
								if(XMLRoot.getAttribute('stat1')=='2') {
									document.getElementById('confirm['+XMLRoot.getAttribute('match1')+']').disabled=false;
								} else {
									document.getElementById('confirm['+XMLRoot.getAttribute('match1')+']').disabled=true;
								}
								if(XMLRoot.getAttribute('stat2')=='2') {
									document.getElementById('confirm['+XMLRoot.getAttribute('match2')+']').disabled=false;
								} else {
									document.getElementById('confirm['+XMLRoot.getAttribute('match2')+']').disabled=true;
								}
								document.getElementById('msg').innerHTML='';

								// tiro fuori tutti i nodi figli sotto arrows
								var arr=XMLRoot.getElementsByTagName('arrows').item(0);

								for (i=0;i<arr.childNodes.length;++i) {
									var node=arr.childNodes[i];

									// se è un nodo valido (no spazi bianchi)
									if (node.nodeType==1) {
										if (document.getElementById(node.nodeName)!=null) {
											SetStyle(node.nodeName,'');
											if(node.firstChild) {
												document.getElementById(node.nodeName).value = node.firstChild.data;
												if(document.getElementById(node.nodeName) == document.activeElement) document.getElementById(node.nodeName).select();
											} else if(document.getElementById(node.nodeName).value) {
												SetStyle(node.nodeName,'error');
											}

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
										if (document.getElementById(node.nodeName)!=null) {
											document.getElementById(node.nodeName).innerHTML=node.firstChild.data;
										}
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

							if(Cache.length>0) updateScore();
						}
					};
					XMLHttp.send(qs);
				}

			}
			catch (e)
			{
				//console.debug('Errore: ' + e.toString());
			}
		}

	}
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
			XMLHttp.onreadystatechange=function() {
				if (XMLHttp.readyState==XHS_COMPLETE && XMLHttp.status==200) {
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
			};
			XMLHttp.send(null);
		}
	}
	catch (e)
	{
		//console.debug('Errore: ' + e.toString());
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
					updateScore(field);
				}
				else
				{
					field.value = field.value.replace("*","");
					updateScore(field);
				}
			}
			else
			{
				field.value = "";
				updateScore(field);
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

function ToggleAlternate(obj) {
	// sets in ajax who is shooting first
	// if even always left...
	var ShootFirst=document.querySelectorAll('.ShootFirst');
	for(var i=0; i<ShootFirst.length; i++) {
		ShootFirst[i].classList.toggle('hidden');
	}
}

function setShootingFirst(obj) {
	var XMLHttp=CreateXMLHttpRequestObject(); // private ajax value, overrides the global and eliminates the need for a queue management!
	if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
		var ev=document.getElementById('d_Event').value;
		var mm=document.getElementById('d_Match').value;
		var mode=document.getElementById('d_Modes').value;


		XMLHttp.open("GET", WebDir+"Final/SetShootingFirst.php?" + obj.id + "="  +  (obj.checked ? 'y' : 'n'),true);
		XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		XMLHttp.onreadystatechange=function() {
			if (XMLHttp.readyState==XHS_COMPLETE && XMLHttp.status==200) {
				try {
					// leggo l'xml
					var XMLResp=XMLHttp.responseXML;

					// intercetto gli errori di IE e Opera
					if (!XMLResp || !XMLResp.documentElement)
						throw("XML not valid:\n"+XMLResp.responseText);

					// Intercetto gli errori di Firefox
					var XMLRoot;
					if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
						throw("XML not valid:\n");

					XMLRoot = XMLResp.documentElement;

					if (XMLRoot.getAttribute('error')==0) {
						// sets the tabindex values of the next end!
						var Ids=XMLRoot.getElementsByTagName('t');
						var IdToFocus;
						for(var i=0; i<Ids.length; i++) {
							var TabId =Ids[i].getAttribute('id');
							var TabVal=Ids[i].getAttribute('val');
							document.getElementById(TabId).tabIndex=TabVal;
							if(i==0) IdToFocus=TabId;
						}
						document.getElementById(IdToFocus).focus();
					} else {
					}
				} catch(e) {
					//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
				}
			}
		};
		XMLHttp.send(null);
	}
}

function ConfirmEnd(obj) {
	var XMLHttp=CreateXMLHttpRequestObject(); // private ajax value, overrides the global and eliminates the need for a queue management!
	if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
		XMLHttp.open("GET", WebDir+'Final/SetConfirmation.php?mode=' + document.getElementById('matchMode').value + '&team=' + document.getElementById('team').value + '&event=' + document.getElementById('event').value + '&' + obj.id + "=y" ,true);
		XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		XMLHttp.onreadystatechange=function() {
			if (XMLHttp.readyState==XHS_COMPLETE && XMLHttp.status==200) {
				try {
					// leggo l'xml
					var XMLResp=XMLHttp.responseXML;

					// intercetto gli errori di IE e Opera
					if (!XMLResp || !XMLResp.documentElement) {
						throw("XML not valid:\n"+XMLResp.responseText);
					}

					// Intercetto gli errori di Firefox
					var XMLRoot;
					if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
						throw("XML not valid:\n");
					}

					XMLRoot = XMLResp.documentElement;

					if (XMLRoot.getAttribute('error')==0) {
						// sets the shooting first selector
						if(XMLRoot.getAttribute('start')) document.getElementById(XMLRoot.getAttribute('start')).checked=true;
						// sets the the confirmation!
						obj.disabled=true;
						if(XMLRoot.getAttribute('winner')>'0') {
							// match is over, asks confirmation
							document.getElementById('confirmMatch').disabled=false;
						} else {
							document.getElementById('confirmMatch').disabled=true;
						}

						// resets the tabindex values for next arrows
						var Ids=XMLRoot.getElementsByTagName('t');
						var IdToFocus;
						if(Ids.length>0) {
							for(var i=0; i<Ids.length; i++) {
								var TabId =Ids[i].getAttribute('id');
								var TabVal=Ids[i].getAttribute('val');
								document.getElementById(TabId).tabIndex=TabVal;
								if(i==0) IdToFocus=TabId;
							}
							document.getElementById(IdToFocus).focus();
						}
					} else {
					}
				} catch(e) {
					//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
				}
			}
		};
		XMLHttp.send(null);
	}
}

function ConfirmMatch(obj) {
	var XMLHttp=CreateXMLHttpRequestObject(); // private ajax value, overrides the global and eliminates the need for a queue management!
	if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
		XMLHttp.open("GET", WebDir+'Final/SetConfirmationMatch.php?match=' + document.getElementById('match1').value + '&team=' + document.getElementById('team').value + '&event=' + document.getElementById('event').value,true);
		XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		XMLHttp.onreadystatechange=function() {
			if (XMLHttp.readyState==XHS_COMPLETE && XMLHttp.status==200) {
				try {
					// leggo l'xml
					var XMLResp=XMLHttp.responseXML;

					// intercetto gli errori di IE e Opera
					if (!XMLResp || !XMLResp.documentElement) {
						throw("XML not valid:\n"+XMLResp.responseText);
					}

					// Intercetto gli errori di Firefox
					var XMLRoot;
					if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
						throw("XML not valid:\n");
					}

					XMLRoot = XMLResp.documentElement;

					if (XMLRoot.getAttribute('error')==0) {
						// sets the winner
						document.getElementById(XMLRoot.getAttribute('winner')).style.border='2px solid green';
						document.getElementById(XMLRoot.getAttribute('loser')).style.border='';


					} else {
					}
				} catch(e) {
					//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
				}
			}
		};
		XMLHttp.send(null);
	}

}
