/*
													- Fun_AJAX_FindArcher.js -
	Contiene le funzioni ajax che riguardano la pagina FindArcher.php 
*/ 		

var Puntini=0;
var Righe=0;

/*
	- SearchArcher()
	Invia la Post a SearchArcher.php
*/
function SearchArcher()
{
	if (XMLHttp)
	{
 		var d_e_EnCode = document.getElementById('d_e_EnCode').value;
// 		var d_e_EnFirstName = document.getElementById('d_e_EnFirstName').value;
//		var d_e_EnName = document.getElementById('d_e_EnName').value;
		var d_e_Archer = document.getElementById('d_e_Archer').value;
		var d_c_CoCode = document.getElementById('d_c_CoCode').value;
		var d_e_Div = document.getElementById('d_e_EnDivision').value;
		var d_e_Class = document.getElementById('d_e_EnClass').value;
		var d_e_SubCl = document.getElementById('d_e_EnSubClass').value;
		try
		{	
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				XMLHttp.open("POST","SearchArcher.php?d_e_EnCode=" + d_e_EnCode + "&d_e_Archer=" + d_e_Archer + "&d_c_CoCode=" + d_c_CoCode+"&d_e_Div="+d_e_Div+"&d_e_Class="+d_e_Class+"&d_e_SubCl="+d_e_SubCl,true);
				//document.getElementById('idOutput').innerHTML="SearchArcher.php?d_e_EnCode=" + d_e_EnCode + "&d_e_Archer=" + d_e_Archer + "&d_c_CoCode=" + d_c_CoCode + '<br>';
			//	XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SearchArcher_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function SearchArcher_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SearchArcher_Response();
			}
			catch(e)
			{
				document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString();
				//document.getElementById('idOutput').innerHTML+='...';
			}
		}
		else
		{
			document.getElementById('idOutput').innerHTML+='Errore: ' +XMLHttp.statusText;
			//document.getElementById('idOutput').innerHTML+='...';
		}
	}
	else
	{
		document.getElementById('idOutput').innerHTML+='...';
		if (++Puntini%20==0)
			document.getElementById('idOutput').innerHTML='';
	}
}

function SearchArcher_Response()
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
	//alert(Error);
	
	if (Error==0)
	{
		document.getElementById('idOutput').innerHTML='';
		Puntini=0;
		var Arr_Code = XMLRoot.getElementsByTagName('code');
		var Arr_FirstName = XMLRoot.getElementsByTagName('firstname');
		var Arr_Name = XMLRoot.getElementsByTagName('name');
		var Arr_CountryCode = XMLRoot.getElementsByTagName('country_code');
		var Arr_CountryName = XMLRoot.getElementsByTagName('country_name');
		var Arr_Division = XMLRoot.getElementsByTagName('division');
		var Arr_AgeClass = XMLRoot.getElementsByTagName('ageclass');
		var Arr_SubClass = XMLRoot.getElementsByTagName('subclass');
		var Arr_Status = XMLRoot.getElementsByTagName('status');
		
		var StrTitle = XMLRoot.getElementsByTagName('title').item(0).firstChild.data;
		
		var StrHeadCode = XMLRoot.getElementsByTagName('head_code').item(0).firstChild.data;
		var StrHeadArcher = XMLRoot.getElementsByTagName('head_archer').item(0).firstChild.data;
		var StrHeadCountry = XMLRoot.getElementsByTagName('head_country').item(0).firstChild.data;
		var StrHeadDiv = XMLRoot.getElementsByTagName('head_div').item(0).firstChild.data;
		var StrHeadAgeCl = XMLRoot.getElementsByTagName('head_agecl').item(0).firstChild.data;
		var StrHeadSubCl = XMLRoot.getElementsByTagName('head_subcl').item(0).firstChild.data;
		
			
		var tbody = document.getElementById('idResults').getElementsByTagName('tbody').item(0);
		
		//tbody.innerHTML='';
		
	/*
		Dato che IE e Konqueror sono browser scritti da dementi, non posso usare la riga precedente per pulire la tabella.
		Devo invece cancellare riga per riga usando il DOM!!!!!
		Sarebbe bello riuscire ad usare tfoot invece di tbody perchè in teoria esiste tfoot.deleteTFoot!
	*/
		var MyRow=document.getElementById('Row_Title');
		if (MyRow)
			tbody.removeChild(MyRow);
		
		var MyRow=document.getElementById('Row_Head');
		if (MyRow)
			tbody.removeChild(MyRow);
						
		for (i=0;i<=Righe;++i)
		{
			var MyRow=document.getElementById('Row_' + i);
			if (MyRow)
				tbody.removeChild(MyRow);
		}
		
	// titolo
		var NewRow = document.createElement("TR");
		NewRow.id="Row_Title";
		
		var Title = document.createElement("TH");
		Title.className = "Title";
		Title.colSpan="7";	
		Title.innerHTML = StrTitle;
		NewRow.appendChild(Title);
		tbody.appendChild(NewRow);
		
	// header
		var NewRow = document.createElement("TR");
		NewRow.id="Row_Head";
		var Head_Code = document.createElement("TD");
		Head_Code.width="10%";
		Head_Code.className='Title';
		Head_Code.innerHTML=StrHeadCode;
		
		var Head_Archer = document.createElement("TD");
		Head_Archer.width="30%";		
		Head_Archer.className='Title';
		Head_Archer.innerHTML=StrHeadArcher;
		
		var Head_Country = document.createElement("TD");
		Head_Country.width="30%";
		Head_Country.className='Title';
		Head_Country.colSpan="2";
		Head_Country.innerHTML=StrHeadCountry;
		
		var Head_Div = document.createElement("TD");
		Head_Div.width="10%";
		Head_Div.className='Title';
		Head_Div.innerHTML=StrHeadDiv;
		
		var Head_AgeCl = document.createElement("TD");
		Head_AgeCl.width="10%";
		Head_AgeCl.className='Title';
		Head_AgeCl.innerHTML=StrHeadAgeCl;
		
		var Head_SubCl = document.createElement("TD");
		Head_SubCl.width="10%";
		Head_SubCl.className='Title';
		Head_SubCl.innerHTML=StrHeadSubCl;
		
		NewRow.appendChild(Head_Code);
		NewRow.appendChild(Head_Archer);
		NewRow.appendChild(Head_Country);
		NewRow.appendChild(Head_Div);
		NewRow.appendChild(Head_AgeCl);
		NewRow.appendChild(Head_SubCl);
		
		tbody.appendChild(NewRow);
		
		for (var i=0;i<Arr_Code.length;++i)
		{
			NewRow = document.createElement("TR");
			NewRow.id='Row_' + i;
			
						
			var TD_Code = document.createElement("TD");
			TD_Code.className='Center';
			TD_Code.innerHTML
				= (Arr_Code.item(i).firstChild.data!='#' ? '<a class="Link" href="javascript:Send2Row(\'' + Arr_Code.item(i).firstChild.data + '\');">' + Arr_Code.item(i).firstChild.data + '</a>' : '&nbsp;');
						
			var TD_Ath = document.createElement("TD");
			TD_Ath.innerHTML=(Arr_FirstName.item(i).firstChild.data + ' ' + Arr_Name.item(i).firstChild.data != '# #' ? Arr_FirstName.item(i).firstChild.data + ' ' + Arr_Name.item(i).firstChild.data : '&nbsp;');
			
			var TD_CountryCode = document.createElement("TD");
			TD_CountryCode.width="5%";
			TD_CountryCode.className='Center';
			TD_CountryCode.innerHTML=(Arr_CountryCode.item(i).firstChild.data!='#' ? Arr_CountryCode.item(i).firstChild.data : '&nbsp;');
			
			var TD_CountryName = document.createElement("TD");
			TD_CountryName.width="15%";
			TD_CountryName.innerHTML=(Arr_CountryName.item(i).firstChild.data!='#' ? Arr_CountryName.item(i).firstChild.data : '&nbsp;');
			
			var TD_Division = document.createElement("TD");
			TD_Division.className='Center';
			TD_Division.innerHTML=(Arr_Division.item(i).firstChild.data!='#' ? Arr_Division.item(i).firstChild.data : '--');
			
			var TD_AgeCl = document.createElement("TD");
			TD_AgeCl.className='Center';
			TD_AgeCl.innerHTML=(Arr_AgeClass.item(i).firstChild.data!='' ? Arr_AgeClass.item(i).firstChild.data : '--');
			
			var TD_SubCl = document.createElement("TD");
			TD_SubCl.className='Center';
			TD_SubCl.innerHTML=(Arr_SubClass.item(i).firstChild.data!='' ? Arr_SubClass.item(i).firstChild.data : '--');
			
			var x=Arr_Status.item(i).firstChild.data;
			
			var RowStyle='';
			
			switch(x)
			{
				case '0':
					RowStyle='';
					break;
				case '1':
					RowStyle='CanShoot';
					break;
				case '8':
					RowStyle='CouldShoot';
					break;
				case '9':
					RowStyle='NoShoot';
					break;
			}
			
			NewRow.appendChild(TD_Code);
			NewRow.appendChild(TD_Ath);
			NewRow.appendChild(TD_CountryCode);
			NewRow.appendChild(TD_CountryName);
			NewRow.appendChild(TD_Division);
			NewRow.appendChild(TD_AgeCl);
			NewRow.appendChild(TD_SubCl);
			
			tbody.appendChild(NewRow);
			document.getElementById('Row_' + i).className=RowStyle;
			Righe=i;
		}
	}
}

/*
	- Send2Row(Code2Send)
	Invia la matricola nella casella di testo d_e_EnCode_(Id)
	e poi richiama le funzioni di ricerca della stessa.
	BUG:
		In Firefox se due finestre condividono lo stesso oggetto XMLHttpdRequest si hanno dei problemi sugli eventi
		perchè può succedere che la finestra di cui si richiamino le funzioni non riesca a farle girare nel proprio contesto.
		In questo caso abbiamo che se facciamo una ricerca per nome,selezionamo una matricola e poi richiudiamo il popup, quando 
		rieseguiamo una ricerca per nome sulla stessa riga senza ricaricare la pagina,questa non funziona.
	
*/
function Send2Row(Code2Send)
{
	var OpenerRow = document.getElementById('Id').value;

// sblocco la riga per l'update sulla matricola
	opener.document.getElementById('CanComplete_' + OpenerRow).value=1;
		
	opener.document.getElementById('d_e_EnCode_' + OpenerRow).value=Code2Send;
	
	if (OpenerRow!='')	// Provengo da index.php
	{	
	// Eseguo a mano le chiamate a CercaMatr e a UpdateField
		opener.CercaMatr('d_e_EnCode_' + OpenerRow,OpenerRow);
		opener.UpdateField('d_e_EnCode_' + OpenerRow);
	}
	else  // provendo da Partecipants.php
	{
	// Eseguo a mano le chiamate a CercaMatr_Par()
		opener.CercaMatr_Par();
	}
	
// blocco la riga per l'update sulla matricola
	opener.document.getElementById('CanComplete_' + OpenerRow).value=0;

	//window.close();
	
}