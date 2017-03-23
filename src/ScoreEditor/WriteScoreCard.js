var Cache = new Array();
var ShootFirst = '';
var need2Change = 'd';
var callChangeEventPhase = false;

/*
 * - ChangeEvent(TeamEvent); Invia la post a ChangeEvent.php. Se TeamEvent=1
 * l'evento è di squadra
 */
function ChangeEvent(TeamEvent, whichForm, call) {
	// No phases to fetch... See Score.class.php
	if(PhpChangeEvent=='') return;

	if (call != null) {
		callChangeEventPhase = call;
	}

	if (whichForm != null) {
		need2Change = whichForm;
	}

	try {
		var XMLHttp=new XMLHttpRequest();

		if (XMLHttp.readyState != XHS_COMPLETE && XMLHttp.readyState != XHS_UNINIT) return;

		var Ev = document.getElementById(need2Change + '_Event').value;
		// alert(Ev);

		XMLHttp.open("POST", PhpChangeEvent + "?Ev=" + Ev + "&TeamEvent=" + TeamEvent, true);
		XMLHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		XMLHttp.onreadystatechange = function() {
			if (XMLHttp.readyState != XHS_COMPLETE || XMLHttp.status != 200) return;

			// leggo l'xml
			var XMLResp = XMLHttp.responseXML;
			// intercetto gli errori di IE e Opera
			if (!XMLResp || !XMLResp.documentElement)
				throw (XMLResp.responseText);

			// Intercetto gli errori di Firefox
			var XMLRoot;
			if ((XMLRoot = XMLResp.documentElement.nodeName) == "parsererror")
				throw ("");

			XMLRoot = XMLResp.documentElement;

			var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
			// alert(Error);
			if (Error == 0) {
				var SetPoints = XMLRoot.getElementsByTagName('set_points').item(0).firstChild.data;

				var Combo = document.getElementById(need2Change + '_Phase');
				var CmbSP = document.getElementById(need2Change + '_SetPoint');

				if (Combo) {
					var Arr_Code = XMLRoot.getElementsByTagName('code');
					var Arr_Name = XMLRoot.getElementsByTagName('name');

					// Pulisco la select (tenendo conto del solito problema di IE e
					// Konqueror con innerHTML)
					for (var i = Combo.length - 1; i >= 0; --i) {
						Combo.remove(i);
					}

					// aggiungo gli elementi
					for (var i = 0; i < Arr_Code.length; ++i) {
						Combo.options[i] = new Option(Arr_Name.item(i).firstChild.data,
								Arr_Code.item(i).firstChild.data);
					}
				}

				if (CmbSP) {
					if (SetPoints == 0) {
						CmbSP.selectedIndex = 1;
						CmbSP.disabled = true;
					} else {
						CmbSP.disabled = false;
					}
				}

				if (callChangeEventPhase) {
					var team = XMLRoot.getElementsByTagName('team').item(0).firstChild.data;

					ChangePhase(team);
				}
			}
		};
		XMLHttp.send(null);
	} catch (e) {
		// console.debug('Errore: ' + e.toString());
	}
}

function ChangePhase(TeamEvent, whichForm) {
	// No matches to fetch... See Score.class.php
	if(PhpChangePhase=='') return;

	if (whichForm != null) {
		need2Change = whichForm;
	}

	try {
		var XMLHttp=new XMLHttpRequest();

		if (XMLHttp.readyState != XHS_COMPLETE && XMLHttp.readyState != XHS_UNINIT) return;

		var Ev = document.getElementById(need2Change + '_Event').value;
		var Ph = document.getElementById(need2Change + '_Phase').value;

		XMLHttp.open("POST", PhpChangePhase + "?Ev=" + Ev + "&Ph=" + Ph + "&TeamEvent=" + TeamEvent, true);
		XMLHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		XMLHttp.onreadystatechange = function() {
			if (XMLHttp.readyState != XHS_COMPLETE || XMLHttp.status != 200) return;

			// leggo l'xml
			var XMLResp = XMLHttp.responseXML;
			// intercetto gli errori di IE e Opera
			if (!XMLResp || !XMLResp.documentElement)
				throw (XMLResp.responseText);

			// Intercetto gli errori di Firefox
			var XMLRoot;
			if ((XMLRoot = XMLResp.documentElement.nodeName) == "parsererror")
				throw ("");

			XMLRoot = XMLResp.documentElement;

			var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
			// alert(Error);
			if (Error == 0) {
				var Combo = document.getElementById(need2Change + '_Match');
				var i;

				if (Combo) {
					// Pulisco la select (tenendo conto del solito problema di IE e
					// Konqueror con innerHTML)
					for (i = Combo.length - 1; i >= 0; --i)
						Combo.remove(i);

					var matchs = XMLRoot.getElementsByTagName('match');
					var matchNo1s = XMLRoot.getElementsByTagName('matchno1');
					var names1 = XMLRoot.getElementsByTagName('name1');
					var names2 = XMLRoot.getElementsByTagName('name2');

					for (i = 0; i < matchs.length; ++i) {
						Combo.options[i] = new Option(names1.item(i).firstChild.data
								+ ' - ' + names2.item(i).firstChild.data, matchNo1s
								.item(i).firstChild.data);
					}
				}
			}
		};

		XMLHttp.send(null);
	} catch (e) {
		// console.debug('Errore: ' + e.toString());
	}
}

function makeScore(TeamEvent) {
	if(PhpChangeMatch=='') return;

	try {
		var XMLHttp=new XMLHttpRequest();

		var chunk = document.getElementById('outputChunk');

		chunk.innerHTML = '';

		if (XMLHttp.readyState != XHS_COMPLETE && XMLHttp.readyState != XHS_UNINIT) return;

		var ev = document.getElementById('d_Event').value;
		var mm = document.getElementById('d_Match').value;
		var mode = document.getElementById('d_Modes').value;
		var Ph = document.getElementById(need2Change + '_Phase').value;

		XMLHttp.open("GET", PhpChangeMatch + "?d_Event=" + ev
				+ "&Ph=" + Ph
				+ "&d_Match=" + mm
				+ "&d_Team=" + TeamEvent
				+ "&d_Mode=" + mode
				+ "&ts=" + ts4qs(), true);
		// document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev="
		// + Ev + "&TeamEvent=" + TeamEvent;
		XMLHttp.setRequestHeader("Content-Type",
				"application/x-www-form-urlencoded");
		XMLHttp.onreadystatechange = function() {
			if (XMLHttp.readyState != XHS_COMPLETE || XMLHttp.status != 200) return;

			var resp = XMLHttp.responseText;
			var chunk = document.getElementById('outputChunk');
			chunk.innerHTML = resp;

			var MoveNext=document.getElementById('buttonMove2Next')
			if(MoveNext) MoveNext.disabled = (document.getElementById('d_Modes').value == 2);
		};
		XMLHttp.send(null);
	} catch (e) {
		// console.debug('Errore: ' + e.toString());
	}
}

function updateScore(obj) {
	if(PhpUpdateCard=='') return;

	var qs = '';

	var event = document.getElementById('event').value;
	var team = document.getElementById('team').value;
	var alternate = '';
	if (document.getElementById('alternate') && document.getElementById('alternate').checked)
		alternate = '1';

	// prendo tutte le frecce della volee o del tie
	qs = "name=" + obj.id
		+ "&alternate=" + alternate
		+ "&matchfirst=" + ShootFirst
		+ "&arrow=" + obj.value
		+ "&event=" + event
		+ "&team=" + team
		+ "&ts=" + ts4qs();

	try {
		var XMLHttp= new XMLHttpRequest();

		if (XMLHttp.readyState != XHS_COMPLETE && XMLHttp.readyState != XHS_UNINIT) return;

		XMLHttp.open("POST", PhpUpdateCard + "?" + qs, true);
		XMLHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		XMLHttp.onreadystatechange = function() {
			if (XMLHttp.readyState != XHS_COMPLETE || XMLHttp.status != 200) return;

			// leggo l'xml
			var XMLResp = XMLHttp.responseXML;

			// intercetto gli errori di IE e Opera
			if (!XMLResp || !XMLResp.documentElement) {
				throw ("XML not valid" + XMLResp.responseText);
			}

			// Intercetto gli errori di Firefox
			var XMLRoot;
			if ((XMLRoot = XMLResp.documentElement.nodeName) == "parsererror") {
				throw ("XML not valid");
			}

			XMLRoot = XMLResp.documentElement;

			var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

			var spotEnable = XMLRoot.getElementsByTagName('spot_enable').item(0).firstChild.data;
			var spotStart = XMLRoot.getElementsByTagName('spot_start').item(0).firstChild.data;
			var spotEnd = XMLRoot.getElementsByTagName('spot_end').item(0).firstChild.data;
			var nextArrow = XMLRoot.getElementsByTagName('alternate').item(0).firstChild.data;

			document.getElementById('spotEnable').value = spotEnable;
			document.getElementById('spotStart').value = spotStart;
			document.getElementById('spotEnd').value = spotEnd;

			if (Error == 0) {
				// Get all rows to modify
				var Rows = XMLRoot.getElementsByTagName('row');
				for (var i = 0; i < Rows.length; ++i) {
					document.getElementById(Rows.item(i).getAttribute('id')).innerHTML=Rows.item(i).getAttribute('value');
				}

				// Get all fields to update
				var Rows = XMLRoot.getElementsByTagName('field');
				for (var i = 0; i < Rows.length; ++i) {
					document.getElementById(Rows.item(i).getAttribute('id')).value=Rows.item(i).getAttribute('value');
				}
			}

			if (document.getElementById('dispDanage')) {
				if (document.getElementById('dispDanage').checked)
					updateDanageDisplay();
			}

			if (nextArrow) {
				document.getElementById(nextArrow).focus();
			}

		};
		XMLHttp.send(null);
	} catch (e) {
		// console.debug('Errore: ' + e.toString());
	}
}













function move2nextPhase(ev, match, team) {
	try {
		if (ev == '')
			return;

		if (XMLHttp.readyState == XHS_COMPLETE
				|| XMLHttp.readyState == XHS_UNINIT) {
			XMLHttp.open("GET", WebDir + "Final/Move2NextPhase.php?event=" + ev
					+ "&match=" + match + "&team=" + team + "&ts=" + ts4qs(),
					true);
			// document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev="
			// + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type",
					"application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange = move2nextPhase_StateChange;
			XMLHttp.send(null);
		}
	} catch (e) {
		console.debug('Errore: ' + e.toString());
	}
}

function move2nextPhase_StateChange() {
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState == XHS_COMPLETE) {
		// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status == 200) {
			try {
				move2nextPhase_Response();
			} catch (e) {
				// document.getElementById('idOutput').innerHTML='Errore: ' +
				// e.toString();
			}
		} else {
			// document.getElementById('idOutput').innerHTML='Errore: '
			// +XMLHttp.statusText;
		}
	}
}

function move2nextPhase_Response() {
	// leggo l'xml
	var XMLResp = XMLHttp.responseXML;

	// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw ("XML non valido:\n" + XMLResp.responseText);

	// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName) == "parsererror")
		throw ("XML non valido:\n");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	var msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	alert(msg);
}


function updateScore_Response() {
}

function setLive(TeamEvent) {
	try {
		if (XMLHttp.readyState == XHS_COMPLETE
				|| XMLHttp.readyState == XHS_UNINIT) {
			var ev = document.getElementById('d_Event').value;
			var mm = document.getElementById('d_Match').value;

			XMLHttp.open("GET", WebDir + "Final/UpdateLive.php?d_Event=" + ev
					+ "&d_Match=" + mm + "&d_Team=" + TeamEvent, true);
			// document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev="
			// + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type",
					"application/x-www-form-urlencoded");
			XMLHttp.onreadystatechange = setLive_StateChange;
			XMLHttp.send(null);
		}
	} catch (e) {
		// console.debug('Errore: ' + e.toString());
	}
}

function setLive_StateChange() {
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState == XHS_COMPLETE) {
		// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status == 200) {
			try {
				setLive_Response();
			} catch (e) {
				// document.getElementById('idOutput').innerHTML='Errore: ' +
				// e.toString();
			}
		} else {
			// document.getElementById('idOutput').innerHTML='Errore: '
			// +XMLHttp.statusText;
		}
	}
}

function setLive_Response() {
	// leggo l'xml
	var XMLResp = XMLHttp.responseXML;

	// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw ("XML non valido:\n" + XMLResp.responseText);

	// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName) == "parsererror")
		throw ("XML non valido:\n");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	var msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	if (Error == 0) {
		var live = XMLRoot.getElementsByTagName('live').item(0).firstChild.data;
		var livemsg = XMLRoot.getElementsByTagName('livemsg').item(0).firstChild.data;
		document.getElementById('liveButton').value = livemsg;
		if (live == 1)
			SetStyle('liveButton', 'error');
		else
			SetStyle('liveButton', '');
	} else {
		document.getElementById('msg').innerHTML = msg;
	}
}

function saveCommentary(TeamEvent) {
	try {
		if (XMLHttp.readyState == XHS_COMPLETE
				|| XMLHttp.readyState == XHS_UNINIT) {
			var params = "";

			params += "d_Event=" + document.getElementById('d_Event').value;
			params += "&d_Match=" + document.getElementById('d_Match').value;
			params += "&d_Team=" + TeamEvent;
			params += "&Review1=" + document.getElementById('Lang1').value;
			params += "&Review2=" + document.getElementById('Lang2').value;

			XMLHttp.open("POST", WebDir + "Final/UpdateCommentary.php", true);
			// document.getElementById('idOutput').innerHTML="../ChangeEvent.php?Ev="
			// + Ev + "&TeamEvent=" + TeamEvent;
			XMLHttp.setRequestHeader("Content-Type",
					"application/x-www-form-urlencoded");
			XMLHttp.setRequestHeader("Content-length", params.length);
			XMLHttp.setRequestHeader("Connection", "close");
			XMLHttp.onreadystatechange = saveCommentary_StateChange;
			XMLHttp.send(params);
		}
	} catch (e) {
		// console.debug('Errore: ' + e.toString());
	}
}

function saveCommentary_StateChange() {
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState == XHS_COMPLETE) {
		// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status == 200) {
			try {
				saveCommentary_Response();
			} catch (e) {
				// document.getElementById('idOutput').innerHTML='Errore: ' +
				// e.toString();
			}
		} else {
			// document.getElementById('idOutput').innerHTML='Errore: '
			// +XMLHttp.statusText;
		}
	}
}

function saveCommentary_Response() {
	// leggo l'xml
	var XMLResp = XMLHttp.responseXML;

	// intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement)
		throw ("XML non valido:\n" + XMLResp.responseText);

	// Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName) == "parsererror")
		throw ("XML non valido:\n");

	XMLRoot = XMLResp.documentElement;

	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	var msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

	if (Error == 0) {
		SetStyle('Lang1', '');
		SetStyle('Lang2', '');
	} else {
		SetStyle('Lang1', 'error');
		SetStyle('Lang2', 'error');
	}
}

function targetClick(side, size, event) {

	if (!event)
		var event = document.event;

	var xpos = (event.offsetX ? event.offsetX : (event.layerX ? event.layerX
			: 0));
	var ypos = (event.offsetY ? event.offsetY : (event.layerY ? event.layerY
			: 0));
	// alert("Mouse X: " + xpos + "\nMouse Y: " + ypos);

	var match = side;
	var event = document.getElementById('event').value;
	var team = document.getElementById('team').value;

	if (size != 0 && xpos != 0 && ypos != 0) {
		qs = "match=" + match + "&event=" + event + "&team=" + team + "&size="
				+ size + "&x=" + xpos + "&y=" + ypos + "&ts=" + ts4qs();

		Cache.push(qs);
	}

	updateScore();
}

function clickStar(clicked, star) {
	var arrField = clicked.split("_");
	if (clicked
			&& (document.getElementById('spotEnable').value == 0 || document
					.getElementById('spotEnable').value == 1)) {
		var field = document
				.getElementById((document.getElementById('spotEnable').value == 0 ? "s"
						: "t")
						+ "_"
						+ arrField[1]
						+ "_"
						+ (parseInt(document.getElementById('spotStart').value) + parseInt(arrField[2])));
		if (field.value) {
			if (star) {
				if (field.value.indexOf("*") == "-1") {
					field.value += "*";
					updateScore(field.id);
				} else {
					field.value = field.value.replace("*", "");
					updateScore(field.id);
				}
			} else {
				field.value = "";
				updateScore(field.id);
			}
		}
	}
}

function showOptions() {
	document.getElementById('options').hidden = !document
			.getElementById('options').hidden;
}

function setStarter(obj) {
	var tmp = obj.split('_');
	var tmpOpp = obj.split('_');
	if (tmp[1] / 2 == Math.floor(tmp[1] / 2))
		tmpOpp[1] = parseInt(tmp[1]) + 1;
	else
		tmpOpp[1] = parseInt(tmp[1]) - 1;

	var objOpp = tmpOpp.join('_');

	if (document.getElementById(obj).value == ''
			&& document.getElementById(objOpp).value == '')
		ShootFirst = tmp[1];
	document.getElementById(obj).select();
}

//calcola il timestamp da accodare alla querystring per bypassare la cache del
//browser
function ts4qs() {
	var date = new Date();

	return (date.getFullYear()
		+ '-' + date.getMonth()
		+ '-' + date.getDay()
		+ '-' + date.getHours()
		+ '-' + date.getMinutes()
		+ '-' + date.getSeconds()
		+ '-' + date.getMilliseconds());
}

