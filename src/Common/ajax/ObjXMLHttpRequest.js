/*
													- ObjXMLHttpRequest.js -
	Serve a gestire la creazione dell'oggetto XMLHttpRequest.
	Versione: 2007.03.08
*/

/*
	XMLHttp.
	Conterrà l'istanza dell'oggetto XMLHttpRequestObject.
*/
var XMLHttp = CreateXMLHttpRequestObject();

/*
	Le 5 var che seguono sono costanti che rappresentano gli stati
	della richiesta.
	XHS sta per: Xml Html Status
*/
var XHS_UNINIT		= 0;	// Uninitialized
var XHS_LOADING		= 1;	// Loading
var XHS_LOADED		= 2;	// Loaded
var XHS_INTER		= 3;	// Interactive
var XHS_COMPLETE	= 4;	// Complete

/*
	CreateXMLHttpRequestObject().
	Funzione per creare l'istanza di XMLHttpRequest.

	Parametri Ricevuti:
		Nessuno

	Valori Ritornati:
		false:
			In caso di errore.
		l'istanza di XMLHttpRequest:
			In caso di successo
*/
function CreateXMLHttpRequestObject()
{
	var XMLHttp = false;

/*
	Provo a creare l'istanza ipotizzando un browser diverso da IE6 o precedente.
	Nel caso scatti, gestisco l'eccezione e provo con la famiglia IE6 o precedente.
	Non provo subito con IE perch� IE7 usa l'oggetto XMLHttpRequest come gli altri browser
	oppure come activex e quindi se si pu�, evito l'activex.
*/
	try
	{
		XMLHttp = new XMLHttpRequest();
	}
	catch(e)
	{
	// Elenco dei possibili Prog ID
		var XMLHttpVers = new Array
		(
			'MSXML2.XMLHTTP.6.0',
			'MSXML2.XMLHTTP.5.0',
			'MSXML2.XMLHTTP.4.0',
			'MSXML2.XMLHTTP.3.0',
			'MSXML2.XMLHTTP',
			'Microsoft.XMLHTTP'

		);
	//  Cerco il Prog ID corretto
		for (var i=0;i<XMLHttpVers.length;++i)
		{
			try
			{
				XMLHttp = new ActiveXObject(XMLHttpVers[i]);
			}
			catch(e)	// Devo ignorare gli errori
			{
			}
		}
	}


	if (!XMLHttp)
		alert("L'oggetto XMLHttpRequest non pu� essere creato!");
	else
		return XMLHttp;
}