function showOptions() {
	document.getElementById('options').hidden=!document.getElementById('options').hidden;
}

function takePicture() {
	if(document.getElementById("athPic").src!='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==') {
		alert(msgPictureThere);
	} else {
		snapshot();
	}

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
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data!='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==') {
								document.getElementById("ManBlock").style.display='';
							} else {
								document.getElementById("ManBlock").style.display='none';
								document.getElementById("confirm-button").style.display='none';
							}

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
				var queryString='?search='+encodeURIComponent(document.getElementById("x_Search").value)
					+"&country="+(document.getElementById("x_Country").checked ? 1 : 0)
					+"&athlete="+(document.getElementById("x_Athlete").checked ? 1 : 0)
					+"&noprint="+(document.getElementById("x_NoPrint").checked ? 1 : 0)
					+"&nophoto="+(document.getElementById("x_noPhoto").checked ? 1 : 0);
				var srcTours=document.querySelectorAll('.x_Tours');
				if(srcTours.length>0) {
					for(var i=0; i< srcTours.length; i++) {
						if(srcTours[i].checked) {
							queryString+='&'+srcTours[i].id+'='+srcTours[i].value;
						}
					}
				}
				var srcTours=document.querySelectorAll('.x_Sessions');
				if(srcTours.length>0) {
					for(var i=0; i< srcTours.length; i++) {
						if(srcTours[i].checked) {
							queryString+='&'+srcTours[i].id+'='+srcTours[i].value;
						}
					}
				}
				XMLHttp.open("GET","AccreditationPictureList.php"+queryString ,true);
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
							var Arr_Row = XMLRoot.getElementsByTagName('athlete');
							// var Arr_id = XMLRoot.getElementsByTagName('id');
							// var Arr_Ath = XMLRoot.getElementsByTagName('ath');
							// var Arr_Team = XMLRoot.getElementsByTagName('team');
							// var Arr_Cat = XMLRoot.getElementsByTagName('cat');
							// var Arr_Pic = XMLRoot.getElementsByTagName('pic');
							// var Arr_Prn = XMLRoot.getElementsByTagName('prn');

							var Missing=XMLRoot.getAttribute('missing');
							document.getElementById('missingPhotos').innerHTML=Missing;

							for (i=0; i<Arr_Row.length; i++) {
							    var XmlRow=Arr_Row[i];
								var newRow = document.createElement('tr');
								newRow.id = XmlRow.getAttribute('id');
								newRow.onclick = function() {selectedAthlete(this)};
								if(XmlRow.getAttribute('prn')==1) {
									newRow.className='Reverse';
								}

								var td = document.createElement('td');
								var img = document.createElement('img');
								img.src = ROOT_DIR+'Common/Images/Enabled'+ XmlRow.getAttribute('pic') +'.png';
								img.height='20';
								td.appendChild(img);
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('ath');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('cat');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('team');
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
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data) {
								document.getElementById("ManBlock").style.display='';
							} else {
								document.getElementById("ManBlock").style.display='none';
								document.getElementById("confirm-button").style.display='none';
							}
							searchAthletes();
						} else {
							alert('NO PICTURE SAVED!');
						}

					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send("Id=" + srcAthlete + "&picEncoded=" + encodeURIComponent(encodedPict));
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
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data) {
								document.getElementById("ManBlock").style.display='';
							} else {
								document.getElementById("ManBlock").style.display='none';
								document.getElementById("confirm-button").style.display='none';
							}
							searchAthletes();
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

function printAccreditation() {
	var CardNumber=document.getElementById("accreditation-number").value;

	window.open('CardCustom.php?CardType=A&CardNumber='+CardNumber+'&Entries[]='+document.getElementById("selId").value);
	document.getElementById("confirm-button").style.display='';
}

function ConfirmPrinted() {
	var CardNumber=document.getElementById("accreditation-number").value;
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("POST",'ConfirmPrinted.php?CardType=A&CardNumber='+CardNumber+'&Entries[]='+document.getElementById("selId").value, true);
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

						var Error=XMLRoot.getElementsByTagName('error').item(0).value;
						if(Error) {
							alert('Error');
						} else {
							document.getElementById("confirm-button").style.display='none';
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