function RequestCode() {
	if (XMLHttp) {
		try {
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
				var Email=document.getElementById('Email').value;
				var Password=document.getElementById('Password').value;
				var Nation=document.getElementById('ToNation').value;

				if(Email=='' || Password=='') return;

			    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			    if(!re.test(Email)) {
			    	alert('Email Not Valid!');
			    	return;
			    }


				var QueryString = IanseoRequestCodeURI
					+ '?Code=' + encodeURIComponent(document.getElementById('ToCode').innerHTML)
					+ '&Name=' + encodeURIComponent(document.getElementById('ToName').innerHTML)
					+ '&ComCode=' + encodeURIComponent(document.getElementById('ToCommitee').innerHTML)
					+ '&ComName=' + encodeURIComponent(document.getElementById('ToComDescr').innerHTML)
					+ '&Where=' + encodeURIComponent(document.getElementById('ToWhere').innerHTML)
					+ '&From=' + encodeURIComponent(document.getElementById('ToWhenFrom').innerHTML)
					+ '&To=' + encodeURIComponent(document.getElementById('ToWhenTo').innerHTML)
					+ '&Nation=' + encodeURIComponent(Nation)
					+ '&Password=' + encodeURIComponent(Password)
					+ '&Email=' + encodeURIComponent(Email)
					+ '&Google=' + encodeURIComponent(document.getElementById('GoogleMap').value)

				XMLHttp.open("GET", QueryString, true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState==XHS_COMPLETE) {
						// se lo status di HTTP è ok vado avanti
						if (XMLHttp.status==200) {
							try {
								// leggo l'xml
								var XMLResp=XMLHttp.responseXML;

								// intercetto gli errori di IE e Opera
								if (!XMLResp || !XMLResp.documentElement) throw("XML not valid:\n"+XMLResp.responseText);

								// Intercetto gli errori di Firefox
								var XMLRoot;
								if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("XML not valid:\n");

								XMLRoot = XMLResp.documentElement;

								var Error=XMLRoot.getAttribute('result');

								// the only time we need an alert with double text is for the yellow card
								var Message=window[Error];
								if(Error=='ErrYellowCard') Message=Message + "\n\n" + window['ErrNoError'];
								alert(Message);
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

				};
				XMLHttp.send(QueryString);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

