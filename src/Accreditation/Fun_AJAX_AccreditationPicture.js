function showOptions() {
	document.getElementById('options').hidden=!document.getElementById('options').hidden;
}

function selectedAthlete(clickObj) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AccreditationPictureImage.php?Id="+clickObj.id,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
						
						if(Error==0) {
							document.getElementById("selId").value = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
							document.getElementById("selAth").innerHTML = XMLRoot.getElementsByTagName('ath').item(0).firstChild.data;
							document.getElementById("selTeam").innerHTML = XMLRoot.getElementsByTagName('team').item(0).firstChild.data;
							document.getElementById("selCat").innerHTML = XMLRoot.getElementsByTagName('cat').item(0).firstChild.data;
							document.getElementById("athPic").src = XMLRoot.getElementsByTagName('pic').item(0).firstChild.data;
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data)
								document.getElementById("delete-button").style.display='';
							else
								document.getElementById("delete-button").style.display='none';
							
							if(document.getElementById("stop-button").style.display != '')
								document.getElementById("start-button").style.display='';
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function searchAthletes() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				var srcString = document.getElementById("x_Search").value;
				var srcCountry = (document.getElementById("x_Country").checked ? 1 : 0);
				var srcAthlete = (document.getElementById("x_Athlete").checked ? 1 : 0);
				var srcNoPhoto = (document.getElementById("x_noPhoto").checked ? 1 : 0);
				XMLHttp.open("GET","AccreditationPictureList.php?search="+encodeURIComponent(srcString)+"&country="+srcCountry+"&athlete="+srcAthlete+"&nophoto="+srcNoPhoto ,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
						
						document.getElementById("ListBody").innerHTML="";
						if(Error==0) {
							var Arr_id = XMLRoot.getElementsByTagName('id');
							var Arr_Ath = XMLRoot.getElementsByTagName('ath');
							var Arr_Team = XMLRoot.getElementsByTagName('team');
							var Arr_Cat = XMLRoot.getElementsByTagName('cat');
							var Arr_Pic = XMLRoot.getElementsByTagName('pic');
							

							for (i=0; i<Arr_id.length; i++) {
								var newRow = document.createElement('tr');
								newRow.id = Arr_id.item(i).firstChild.data;
								newRow.onclick = function() {selectedAthlete(this)};
								
								var td = document.createElement('td');
								var img = document.createElement('img');
								img.src = ROOT_DIR+'Common/Images/Enabled'+ Arr_Pic.item(i).firstChild.data +'.png';
								img.height='20';
								td.appendChild(img);
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=Arr_Ath.item(i).firstChild.data;
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=Arr_Cat.item(i).firstChild.data;
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=Arr_Team.item(i).firstChild.data;
								newRow.appendChild(td);
								
								document.getElementById("ListBody").appendChild(newRow);
							}
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function sendPicture(encodedPict) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				var srcAthlete = document.getElementById("selId").value;
				XMLHttp.open("POST","AccreditationPictureImage.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
						
						if(Error==0) {
							document.getElementById("athPic").src = XMLRoot.getElementsByTagName('pic').item(0).firstChild.data;
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data)
								document.getElementById("delete-button").style.display='';
							else
								document.getElementById("delete-button").style.display='none';
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send("Id=" + srcAthlete + "&picEncoded=" + encodedPict);
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function deletePicture() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				var srcAthlete = document.getElementById("selId").value;
				XMLHttp.open("GET","AccreditationPictureImage.php?Id=" + srcAthlete + "&picDelete=1",true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
						
						if(Error==0) {
							document.getElementById("athPic").src = XMLRoot.getElementsByTagName('pic').item(0).firstChild.data;
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data)
								document.getElementById("delete-button").style.display='';
							else
								document.getElementById("delete-button").style.display='none';						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}