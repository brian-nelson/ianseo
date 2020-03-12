/*
													- Fun_AJAX_Partecipants.js -
	Contiene le funzioni ajax che riguardano la pagina Partecipants.php
*/ 		
	
/*
	Nota Bene:
	Anche se fa schifo avere globale questa stringa, per comodità l'ho definita così.
	DEVE Essere presente nei file che richiamano questo javascript
	<?php
	print 'var StrCancel = "' . get_text('CmdCancel') . '";';
	?>
*/


/*
	XMLHttpLocal è molto importante.
	Per un bug di firefox, condividendo l'oggetto XMLHttpRequest con più finestre otteniamo un'eccezione di accesso
	della memoria. Questa var è usata per creare le chiamate asincrone da parte di Save_Par per evitare questo problema sull'apertura del
	popup di ricerca per nome.
*/
var XMLHttpLocal = CreateXMLHttpRequestObject();

var CacheMatr = new Array();	// cache per la ricerca sulla matricola

/*
	le seguenti var valgono true se il campo corrispettivo contiene un errore.
	Solo se sono tutte a false sarà possibile salvare
*/
var CtrlCode_Error = false;	
var TargetNo_Error = false;	

var DEBUG=0;

/*
	- DblClickOnRow(e)
	La funzione gestisce il doppio click sulle righe della tabella in Partecipants.php
	e è l'evento e non deve essere passato quando la funzione viene chiamata perchè
	ci pensa il browser.
*/
function DblClickOnRow(e)
{
	e=(!e ? window.event : e);
	var target = (!e.target ? e.srcElement : e.target);
						
/*
	L'id di ritorno lo trovo nel nome dell'id di riga.
	Dato che se sono qui significa che ho cliccato su una textbox, 
	allora so che il suo parent è una cella e il parent di questa è una riga.
	Potrei fermarmi alla cella ma preferisco risalire ancora.
*/
	
	var IdRet = target.parentNode.parentNode.id.split('_')[1];
	
	EditRow(IdRet);
}

/*
	Richiama DblOnClick se si clicca con il destro
*/
function FindByContext(e)
{
	e=(!e ? window.event : e);
	var target = (!e.target ? e.srcElement : e.target);
	
	if (target.type=='text')
	{
		
		if (!window.event)
		{
			e.stopPropagation();
			e.preventDefault();
		}
		else
		{
			e.cancelBubble=true;
			e.returnValue=false;
		}
		
		DblClickOnTextBox(e);
		
	}
}

/*
	- EditRow(Id)
	Prepara i dati nella riga di edit
*/
function EditRow(Id)
{
	if (Id)
	{
		ResetInput();
		
		//window.location.href="Partecipants.php#Edit";
		var ref = 	window.location.href;
		
	/* 
		Aggiungo #Edit solo se nell'href non c'� gi�.
		La concatenazione all'href attuale mi serve per mantenere l'orderby se � settato
	*/
		if (!ref.match(/#Edit/))
			window.location.href+="#Edit";
		
		document.getElementById('d_q_QuSession_').focus();
		
		document.getElementById('d_e_EnId_').value = Id;
		//document.getElementById('d_e_EnSex_').value = document.getElementById('d_e_EnSex_' + Id).value;
		document.getElementById('d_q_QuSession_').value = document.getElementById('d_q_QuSession_' + Id).innerHTML;
		document.getElementById('d_q_QuTargetNo_').value = trim((document.getElementById('d_q_QuTargetNo_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_q_QuTargetNo_' + Id).innerHTML : ''));
		document.getElementById('d_e_EnCode_').value = trim((document.getElementById('d_e_EnCode_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_e_EnCode_' + Id).innerHTML : ''));
		document.getElementById('d_e_EnFirstName_').value = trim((document.getElementById('d_e_EnFirstName_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_e_EnFirstName_' + Id).innerHTML : ''));
		document.getElementById('d_e_EnName_').value = trim((document.getElementById('d_e_EnName_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_e_EnName_' + Id).innerHTML : ''));
		document.getElementById('d_e_EnCtrlCode_').value = trim((document.getElementById('d_e_EnCtrlCode_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_e_EnCtrlCode_' + Id).innerHTML : ''));
		//console.debug(document.getElementById('d_e_EnSex_' + Id).value);
		document.getElementById('d_e_EnSex_').value = document.getElementById('d_e_EnSex_' + Id).value;
		document.getElementById('d_e_EnCountry_').value = document.getElementById('d_e_EnCountry_' + Id).value;
		document.getElementById('d_c_CoCode_').value = trim((document.getElementById('d_c_CoCode_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_c_CoCode_' + Id).innerHTML : ''));
		document.getElementById('d_c_CoName_').value = trim((document.getElementById('d_c_CoName_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_c_CoName_' + Id).innerHTML : ''));
		
		document.getElementById('d_e_EnSubTeam_').value = trim((document.getElementById('d_e_EnSubTeam_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_e_EnSubTeam_' + Id).innerHTML : ''));
		document.getElementById('d_e_EnCountry2_').value = document.getElementById('d_e_EnCountry2_' + Id).value;
		document.getElementById('d_c_CoCode2_').value = trim((document.getElementById('d_c_CoCode2_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_c_CoCode2_' + Id).innerHTML : ''));
		document.getElementById('d_c_CoName2_').value = trim((document.getElementById('d_c_CoName2_' + Id).innerHTML!='&nbsp;' ? document.getElementById('d_c_CoName2_' + Id).innerHTML : ''));
		
		document.getElementById('d_e_EnDivision_').value = document.getElementById('d_e_EnDivision_' + Id).innerHTML;
		document.getElementById('d_e_EnAgeClass_').value = document.getElementById('d_e_EnAgeClass_' + Id).innerHTML;
		document.getElementById('d_e_EnClass_').value = document.getElementById('d_e_EnClass_' + Id).innerHTML;
		document.getElementById('d_e_EnSubClass_').value = document.getElementById('d_e_EnSubClass_' + Id).innerHTML;
		var x= document.getElementById('d_e_EnStatus_' + Id).value;
		document.getElementById('d_e_EnStatus_').value=x;
		switch(x)
		{
			case '0':
				NewStyle = '';
				break;
			case '1':
				NewStyle = 'CanShoot';
				break;
			case '8':
				NewStyle = 'CouldShoot';
				break;
			case '6':
			case '7':
			case '9':
				NewStyle = 'NoShoot';
				break;
		}
		document.getElementById('EditRow').className=NewStyle;
		
		CheckCtrlCode_Par();
		
		document.getElementById('d_e_EnTargetFace_').value = document.getElementById('d_e_EnTargetFaceId_' + Id).value;
		//SelectAgeClass_Par(Id)
	}
}

/*
	- ResetInput()
	Resetta la riga di input
*/
function ResetInput()
{
// resetto gli errori	
	CtrlCode_Error = false;	
	TargetNo_Error = false;	

// Reset degli stili
	document.getElementById('EditRow').className='';
	SetStyle('d_q_QuSession_','');
	SetStyle('d_q_QuTargetNo_','');
	SetStyle('d_e_EnCode_','');
	SetStyle('d_e_EnSex_','');
	SetStyle('d_e_EnFirstName_','');
	SetStyle('d_e_EnName_','');
	SetStyle('d_e_EnCtrlCode_','');
	SetStyle('d_c_CoCode_','');
	SetStyle('d_c_CoName_','');
	SetStyle('d_e_EnSubTeam_','');
	SetStyle('d_c_CoCode2_','');
	SetStyle('d_c_CoName2_','');
	SetStyle('d_e_EnDivision_','');
	SetStyle('d_e_EnAgeClass_','');
	SetStyle('d_e_EnClass_','');
	SetStyle('d_e_EnSubClass_','');
	
// Reset dei valori
	
	// la combo della classe gara va distrutta
	document.getElementById('d_e_EnClass_').innerHTML = '<option value="">--</option>';
	document.getElementById('CanComplete_').value=0;
	document.getElementById('d_e_EnId_').value = 0
 	document.getElementById('d_q_QuSession_').value =0
	document.getElementById('d_q_QuTargetNo_').value = ''
	document.getElementById('d_e_EnCode_').value = '';
	document.getElementById('d_e_EnFirstName_').value = '';
	document.getElementById('d_e_EnName_').value = '';
	document.getElementById('d_e_EnCtrlCode_').value = '';
	document.getElementById('d_e_EnSex_').value = 0
	document.getElementById('d_e_EnCountry_').value = '0';
	document.getElementById('d_c_CoCode_').value = '';
	document.getElementById('d_c_CoName_').value = '';
	
	document.getElementById('d_e_EnSubTeam_').value = '';
	document.getElementById('d_e_EnCountry2_').value = '0';
	document.getElementById('d_c_CoCode2_').value = '';
	document.getElementById('d_c_CoName2_').value = '';
	
	document.getElementById('d_e_EnDivision_').value = '';
	document.getElementById('d_e_EnAgeClass_').value = '';
	document.getElementById('d_e_EnAgeClass_').disabled = false;
	document.getElementById('d_e_EnClass_').value = '';
	document.getElementById('d_e_EnSubClass_').value = '';
}

/*
	- GetRows_Par(Id,OrderBy)
	Esegue la POST a GetRows.
	E' la versione di GetRows usata da Partecipants
*/
function GetRows_Par(Id,OrderBy)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var MyId = encodeURIComponent(Id);
				var MyOrderBy = encodeURIComponent(OrderBy);
				XMLHttp.open("POST","GetRows.php?Id=" + MyId + "&OrderBy=" + MyOrderBy,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="GetRows.php?Id=" + MyId + "&OrderBy=" + MyOrderBy + '<br>';
				XMLHttp.onreadystatechange=GetRows_Par_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
		}
	}
}

function GetRows_Par_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				document.getElementById('tdStatus').className="Bold FontMedium";
				document.getElementById('idStatus').innerHTML='&nbsp;';
				GetRows_Par_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText + '<br>';
		}
	}
	else
	{
		if(document.getElementById('tdStatus') != null) document.getElementById('tdStatus').className="Bold FontMedium Medium";
		if(document.getElementById('idStatus') != null) document.getElementById('idStatus').innerHTML= StrLoading;
	}
}

function GetRows_Par_Response()
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
		var RowsError = XMLRoot.getElementsByTagName('rows_error').item(0).firstChild.data;

		if (RowsError==0)
		{
			var FilterOn = XMLRoot.getElementsByTagName('filteron').item(0).firstChild.data;
			
			var Arr_Id = XMLRoot.getElementsByTagName('id');
			var Arr_Status = XMLRoot.getElementsByTagName('status');
			var Arr_Session = XMLRoot.getElementsByTagName('session');
			var Arr_TargetNo = XMLRoot.getElementsByTagName('targetno');
			var Arr_Code = XMLRoot.getElementsByTagName('code');
			var Arr_FirstName = XMLRoot.getElementsByTagName('firstname');
			var Arr_Name = XMLRoot.getElementsByTagName('name');
			var Arr_SexId = XMLRoot.getElementsByTagName('sex_id');
			var Arr_Sex = XMLRoot.getElementsByTagName('sex');
			var Arr_CtrlCode = XMLRoot.getElementsByTagName('ctrl_code');
			var Arr_Dob = XMLRoot.getElementsByTagName('dob');
			var Arr_CountryId = XMLRoot.getElementsByTagName('country_id');
			var Arr_CountryCode = XMLRoot.getElementsByTagName('country_code');
			var Arr_CountryName = XMLRoot.getElementsByTagName('country_name');
			
			var Arr_CountryId2 = XMLRoot.getElementsByTagName('country_id2');
			var Arr_CountryCode2 = XMLRoot.getElementsByTagName('country_code2');
			var Arr_CountryName2 = XMLRoot.getElementsByTagName('country_name2');
			var Arr_SubTeam = XMLRoot.getElementsByTagName('sub_team');
			
			var Arr_Div = XMLRoot.getElementsByTagName('division');
			var Arr_Cl = XMLRoot.getElementsByTagName('class');
			var Arr_AgeCl = XMLRoot.getElementsByTagName('ageclass');
			var Arr_GoodCl = XMLRoot.getElementsByTagName('classes');
			var Arr_SubCl = XMLRoot.getElementsByTagName('subclass');
			var Arr_TargetFace = XMLRoot.getElementsByTagName('targetface');
			var Arr_Editable = XMLRoot.getElementsByTagName('editable');
			
			var ConfirmMsg1 = XMLRoot.getElementsByTagName('confirm_msg1').item(0).firstChild.data;
			var ConfirmMsg2 = XMLRoot.getElementsByTagName('confirm_msg2').item(0).firstChild.data;
			var ConfirmMsg3 = XMLRoot.getElementsByTagName('confirm_msg3').item(0).firstChild.data;
			var ConfirmMsg4 = XMLRoot.getElementsByTagName('confirm_msg4').item(0).firstChild.data;
	
			var tbody = document.getElementById('idAthList').getElementsByTagName("tbody").item(0);
			
			for (var i=0; i<Arr_Id.length;++i)
			{
			// Nuova riga
				var NewRow = document.createElement("TR");
				NewRow.id = 'Row_' + Arr_Id.item(i).firstChild.data;
				NewRow.ondblclick = DblClickOnRow;
				

			// Colonne
				var TD_Session = document.createElement("TD");
				TD_Session.className='Right';
				TD_Session.innerHTML
					= '<a name="Row_' + Arr_Id.item(i).firstChild.data + '"></a>'
					+ '<div id="d_q_QuSession_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_Session.item(i).firstChild.data!='0' ? Arr_Session.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_TargetNo = document.createElement("TD");
				TD_TargetNo.className='Right';
				TD_TargetNo.innerHTML 
					= '<div id="d_q_QuTargetNo_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_TargetNo.item(i).firstChild.data!='#' ? Arr_TargetNo.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_Code = document.createElement("TD");
				TD_Code.className='Right';
				TD_Code.innerHTML 
					= '<input type="hidden" id="d_e_EnStatus_' + Arr_Id.item(i).firstChild.data + '" value="' +Arr_Status.item(i).firstChild.data  + '">'
					+ '<div id="d_e_EnCode_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_Code.item(i).firstChild.data!='#' ? Arr_Code.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
									
				var TD_FirstName = document.createElement("TD");
				TD_FirstName.className='';
				TD_FirstName.innerHTML 
					= '<div id="d_e_EnFirstName_' +  Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_FirstName.item(i).firstChild.data!='#' ? Arr_FirstName.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
					
				var TD_Name = document.createElement("TD");
				TD_Name.className='';
				TD_Name.innerHTML 
					= '<div id="d_e_EnName_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_Name.item(i).firstChild.data!='#' ? Arr_Name.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_CtrlCode = document.createElement("TD");
				TD_CtrlCode.className='';
				TD_CtrlCode.innerHTML 
					= '<div id="d_e_EnCtrlCode_' + Arr_Id.item(i).firstChild.data + '">'
					//+ (Arr_CtrlCode.item(i).firstChild.data!='#' ? Arr_CtrlCode.item(i).firstChild.data : '&nbsp;')
					+ (Arr_Dob.item(i).firstChild.data!='#' ? Arr_Dob.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				
				var TD_Sex = document.createElement("TD");
				TD_Sex.className='';
				TD_Sex.innerHTML 
					= '<input type="hidden" id="d_e_EnSex_' + Arr_Id.item(i).firstChild.data + '" value="' + Arr_SexId.item(i).firstChild.data + '">'
					+ '<div id="SexName_' + Arr_Id.item(i).firstChild.data + '">' + Arr_Sex.item(i).firstChild.data	+ '</div>';
				
				var TD_CountryCode = document.createElement("TD");
				TD_CountryCode.className='Right';
				TD_CountryCode.innerHTML 
					= '<input type="hidden" id="d_e_EnCountry_' + Arr_Id.item(i).firstChild.data + '" value="' + Arr_CountryId.item(i).firstChild.data + '">'
					+ '<div id="d_c_CoCode_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_CountryCode.item(i).firstChild.data!='#' ? Arr_CountryCode.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_CountryName = document.createElement("TD");
				TD_CountryName.className='';
				TD_CountryName.innerHTML 
					= '<div id="d_c_CoName_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_CountryName.item(i).firstChild.data!='#' ? Arr_CountryName.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_SubTeam = document.createElement("TD");
				TD_SubTeam.className='';
				TD_SubTeam.innerHTML 
					= '<div id="d_e_EnSubTeam_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_SubTeam.item(i).firstChild.data!='#' ? Arr_SubTeam.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_CountryCode2 = document.createElement("TD");
				TD_CountryCode2.className='Right';
				TD_CountryCode2.innerHTML 
					= '<input type="hidden" id="d_e_EnCountry2_' + Arr_Id.item(i).firstChild.data + '" value="' + Arr_CountryId2.item(i).firstChild.data + '">'
					+ '<div id="d_c_CoCode2_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_CountryCode2.item(i).firstChild.data!='#' ? Arr_CountryCode2.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_CountryName2 = document.createElement("TD");
				TD_CountryName2.className='';
				TD_CountryName2.innerHTML 
					= '<div id="d_c_CoName2_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_CountryName2.item(i).firstChild.data!='#' ? Arr_CountryName2.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				
				var TD_Div = document.createElement("TD");
				TD_Div.className='Center';
				TD_Div.innerHTML 
					= '<div id="d_e_EnDivision_' +  Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_Div.item(i).firstChild.data!='#' ? Arr_Div.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_AgeCl = document.createElement("TD");
				TD_AgeCl.className='Center';
				TD_AgeCl.innerHTML 
					= '<div id="d_e_EnAgeClass_'  +  Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_AgeCl.item(i).firstChild.data!='#' ? Arr_AgeCl.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_Cl = document.createElement("TD");
				TD_Cl.className='Center';
				TD_Cl.innerHTML 
					= '<div id="d_e_EnClass_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_Cl.item(i).firstChild.data!='#' ? Arr_Cl.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_SubCl = document.createElement("TD");
				TD_SubCl.className='Center';
				TD_SubCl.innerHTML 
					= '<div id="d_e_EnSubClass_' + Arr_Id.item(i).firstChild.data + '">'
					+ (Arr_SubCl.item(i).firstChild.data!='#' ? Arr_SubCl.item(i).firstChild.data : '&nbsp;')
					+ '</div>';
				
				var TD_Tf = document.createElement("TD");
				TD_Tf.className='Left';
				TD_Tf.innerHTML 
					= '<div id="d_e_EnTargetFace_' + Arr_Id.item(i).firstChild.data + '">'
					+ '<input type="hidden" id="d_e_EnTargetFaceId_' + Arr_Id.item(i).firstChild.data + '" value="'+Arr_TargetFace.item(i).firstChild.data+'"/>'
					+ (Arr_TargetFace.item(i).firstChild.data>0 ? TargetFaces[Arr_Div.item(i).firstChild.data][Arr_Cl.item(i).firstChild.data][Arr_TargetFace.item(i).firstChild.data] : '&nbsp;')
					+ '</div>';
				
				var TD_Command = document.createElement("TD");
				TD_Command.className='Center';
				TD_Command.innerHTML = '<a class="Link" href="javascript:DeleteRow_Par(' + Arr_Id.item(i).firstChild.data + ',\'' + ConfirmMsg1 + '\',\'' + ConfirmMsg2 + '\',\'' + ConfirmMsg3 + '\',\'' + ConfirmMsg4 + '\');"><img border="0" src="../Common/Images/drop.png" alt="#" title=""></a>';
	
			// Aggiungo le colonne alla riga
				NewRow.appendChild(TD_Session);	
				NewRow.appendChild(TD_TargetNo);
				NewRow.appendChild(TD_Code);
				NewRow.appendChild(TD_FirstName);
				NewRow.appendChild(TD_Name);
				NewRow.appendChild(TD_CtrlCode);
				NewRow.appendChild(TD_Sex);
				NewRow.appendChild(TD_CountryCode);
				NewRow.appendChild(TD_CountryName);
				NewRow.appendChild(TD_SubTeam);
				NewRow.appendChild(TD_CountryCode2);
				NewRow.appendChild(TD_CountryName2);
				NewRow.appendChild(TD_Div);
				NewRow.appendChild(TD_AgeCl);
				NewRow.appendChild(TD_Cl);
				NewRow.appendChild(TD_SubCl);
				NewRow.appendChild(TD_Tf);
				NewRow.appendChild(TD_Command);
				
			// Aggiungo alla tabella la nuova riga
				tbody.appendChild(NewRow);
				
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
					case '6':
					case '7':
					case '9':
						RowStyle='NoShoot';
						break;
				}
				
				document.getElementById('Row_' + Arr_Id.item(i).firstChild.data).className=RowStyle;
				
			}
		}
	}
}

/*
	- CercaMatr_Par()
	Invia la GEt a Matr_FindOnEdit_Par.php passando Matr=matricola
	Va agganciata all'evento onKeyUp di d_e_EnCode_
*/
function CercaMatr_Par()
{
/* 
	ricreo l'oggetto XMLHttp perch� se si apre il popup del cerca va tutto a ramengo ;(
*/
	XMLHttp = CreateXMLHttpRequestObject();
	
	if (XMLHttp)
	{
		var Matr = encodeURIComponent(document.getElementById('d_e_EnCode_').value);
		CacheMatr.push("Matr=" + Matr);
		
		try
		{	
			if (document.getElementById('CanComplete_').value==1)
			{
				if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && CacheMatr.length>0)
				{
					var FromCache = CacheMatr.shift();
					XMLHttp.open("POST","Matr_FindOnEdit_Par.php",true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="Matr_FindOnEdit_Par.php?Matr="  + Matr;
					XMLHttp.onreadystatechange=CercaMatr_Par_StateChange;
					XMLHttp.send(FromCache);
				}
			}	
			else
				CacheMatr.shift();		
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function CercaMatr_Par_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CercaMatr_Par_Response();
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

function CercaMatr_Par_Response()
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
		var Status = XMLRoot.getElementsByTagName('status').item(0).firstChild.data;
		var Code = XMLRoot.getElementsByTagName('code').item(0).firstChild.data;
		var FirstName = XMLRoot.getElementsByTagName('firstname').item(0).firstChild.data;
		var Name = XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
		var Sex = XMLRoot.getElementsByTagName('sex').item(0).firstChild.data;
		var CtrlCode = XMLRoot.getElementsByTagName('ctrl_code').item(0).firstChild.data;
		var Dob = XMLRoot.getElementsByTagName('dob').item(0).firstChild.data;
		var CountryId = XMLRoot.getElementsByTagName('idcountry').item(0).firstChild.data;
		var CountryCode = XMLRoot.getElementsByTagName('country').item(0).firstChild.data;
		var CountryName = XMLRoot.getElementsByTagName('nation').item(0).firstChild.data;
		var Division = XMLRoot.getElementsByTagName('division').item(0).firstChild.data;
		var Class = XMLRoot.getElementsByTagName('class').item(0).firstChild.data;
		var SubClass = XMLRoot.getElementsByTagName('subclass').item(0).firstChild.data;
		var AgeClass = XMLRoot.getElementsByTagName('ageclass').item(0).firstChild.data;
	
		if (Code=='#') Code='';
		if (Name=='#') Name='';
		if (FirstName=='#') FirstName='';
		if (CtrlCode=='#') CtrlCode='';
		if (Dob=='#') Dob='';
		if (Division=='#') Division='';
		if (AgeClass=='#') AgeClass='';
		if (SubClass=='#') SubClass='';
		if (CountryId=='#') CountryId='';
		if (CountryCode=='#') CountryCode='';
		if (CountryName=='#') CountryName='';
		if (Status=='#') Status='';
		
		
		
		document.getElementById('d_e_EnName_').value=Name;
		document.getElementById('d_e_EnFirstName_').value=FirstName;
		document.getElementById('d_e_EnSex_').value=Sex;
		//document.getElementById('d_e_EnCtrlCode_').value=CtrlCode;
		document.getElementById('d_e_EnCtrlCode_').value=Dob;
		document.getElementById('d_e_EnCountry_' ).value=CountryId;
		document.getElementById('d_c_CoCode_' ).value=CountryCode;
		document.getElementById('d_c_CoName_').value=CountryName;
		document.getElementById('d_e_EnDivision_').value=Division;
		
		document.getElementById('d_e_EnAgeClass_').value=AgeClass;
		document.getElementById('d_e_EnClass_').value=Class;
					
		document.getElementById('d_e_EnSubClass_').value=SubClass;
		document.getElementById('d_e_EnStatus_').value=Status;
		
		var RowStyle='';
			
		switch(Status)
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
			case '6':
			case '7':
			case '9':
				RowStyle='NoShoot';
				break;
		}
		
		document.getElementById('EditRow').className=RowStyle;
		CheckCtrlCode_Par();			
	}
	
	//GetStatus(Id);
	// per scaricare la cache delle ricerche
	if (CacheMatr.length>0)
		setTimeout("CercaMatr_Par()",500);
}

/*
	- CheckCtrlCode_Par()
	Invia la GEt a CheckCtrlCode_Par.php
*/
function CheckCtrlCode_Par()
{

	if (XMLHttp)
	{
		try
		{	
			//var d_e_EnCtrlCode = encodeURIComponent(document.getElementById('d_e_EnCtrlCode_').value);
			var d_e_EnCtrlCode = trim(document.getElementById('d_e_EnCtrlCode_').value);
			var d_e_EnSex = document.getElementById('d_e_EnSex_').value;
	
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) )
			{
			
				XMLHttp.open("GET","CheckCtrlCode_Par.php?d_e_EnCtrlCode=" + d_e_EnCtrlCode + '&d_e_EnSex=' + d_e_EnSex,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="CheckCtrlCode_Par.php?d_e_EnCtrlCode=" + d_e_EnCtrlCode;
				XMLHttp.onreadystatechange=CheckCtrlCode_Par_StateChange;
				XMLHttp.send(null);
			}			
		}
		catch (e)
		{
		
			//document.getElementById('idOutput').innerHTML='Errore1: ' + e.toString();
		}
	}
}

function CheckCtrlCode_Par_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CheckCtrlCode_Par_Response();
			}
			catch(e)
			{
			
			//	document.getElementById('idOutput').innerHTML='Errore2: ' + e.toString();
			}
		}
		else
		{
		
			//document.getElementById('idOutput').innerHTML='Errore3: ' +XMLHttp.statusText;
		}
	}
}

function CheckCtrlCode_Par_Response()
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
	
	document.getElementById('d_e_EnAgeClass_').disabled=false;
	
	//alert(Error);
	if (Error==1)
	{
		SetStyle('d_e_EnCtrlCode_','error');
		CtrlCode_Error=true;
		
	}
	else
	{
		CtrlCode_Error=false;		
		
		SetStyle('d_e_EnCtrlCode_','');
		
	// Gestisco le tendine delle classi
		var AgeClass = XMLRoot.getElementsByTagName('ageclass').item(0).firstChild.data;
		var Classes = XMLRoot.getElementsByTagName('classes').item(0).firstChild.data;
		
		// Distruggo la tendina 
		//document.getElementById('d_e_EnAgeClass_').value = '';
		document.getElementById('d_e_EnClass_').innerHTML = '<option value="">--</option>';
	// Se l'ageclass � buona allora blocco la tendina (significa che posso calcolare dal codice fiscale
		
		if (AgeClass!='')
		{
			document.getElementById('d_e_EnAgeClass_').value=AgeClass;
			document.getElementById('d_e_EnAgeClass_').disabled=true;
		
			
		// posso generare la tendina delle classi gara buone
		
			var ValidClass = new Array();
			if (Classes!='')
			{
			
				
				ValidClass=Classes.split(',');
				
				if (DEBUG==1)
					document.getElementById('idOutput').innerHTML+=ValidClass+'<br>';
					
				for (var i=0;i<ValidClass.length;++i)
				{
				/*
					Genero le classi valide.
					Se l'id di insert � !=0 significa che sto facendo un edit e posso quindi
					prelevare il valore della classe gara che proviene dal div della riga corrispondente all'id.
					Se sto inserendo una nuova riga setto la classe gara uguale alla classe
				*/
					var Id = document.getElementById('d_e_EnId_').value;
					
					document.getElementById('d_e_EnClass_').innerHTML
						+='<option value="' + ValidClass[i] + '"' 
						+ ( (Id!=0 && document.getElementById('d_e_EnClass_'+Id).innerHTML==ValidClass[i]) ||  (Id==0 && ValidClass[i]== document.getElementById('d_e_EnAgeClass_').value) ? ' selected' : '')
						+ '>' + ValidClass[i] + '</option>';
				}
			}
		}

		SelectAgeClass_Par('');
	}
}


/*
	- DeleteRow_Par(Id,Msg1,Msg2,Msg3,Msg4).
	Esegue la GET a DeleteRow.php
	Id � l'id del tizio da eliminare
	Msg1,2,3,4 sono i messaggi nel testo di conferma
	
	La propriet� onreadystatechange � quella di DeleteRow (in Fun_AJAX_index.js)
*/
function DeleteRow_Par(Id,Msg1,Msg2,Msg3,Msg4)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave') || !document.getElementById('chk_BlockAutoSave').checked)
			{
				var GetConfirm=false;
				var Elimina=true;
				
				if (document.getElementById('d_e_EnFirstName_' + Id).innerHTML!='&nbsp;')
					GetConfirm=true;
					
				if (GetConfirm)
				{
					var StrMsg //book
						= Msg1 + ': ' + document.getElementById('d_e_EnFirstName_' + Id).innerHTML + '  ' + document.getElementById('d_e_EnName_' + Id).innerHTML
						+ Msg2 + ': ' + document.getElementById('d_c_CoCode_' + Id).innerHTML
						+ Msg3
						+ Msg4;
						
					if (!confirm(StrMsg))
						Elimina=false;
				}
				
				if (Elimina)
				{
					var IdDel = encodeURIComponent(Id);
					if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
					{
						XMLHttp.open("GET","DeleteRow.php?Id=" + IdDel);
						XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
						//document.getElementById('idOutput').innerHTML="DeleteRow.php?Id=" + IdDel;
						XMLHttp.onreadystatechange=DeleteRow_StateChange;
						XMLHttp.send(null);
					}
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function DeleteRow_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				DeleteRow_Response();
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


function DeleteRow_Response()
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
		var Id=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	
		var tbody = document.getElementById('idAthList').getElementsByTagName("tbody").item(0);
		var Row=document.getElementById('Row_' + Id);
		if (Row)
			tbody.removeChild(Row);
	}
}

/*
	- SelectAgeClass_Par(Id)
	Aggiorna la classe e la classe gara
*/

var XMLHttpAgeClass = CreateXMLHttpRequestObject();
function SelectAgeClass_Par(Id)
{
	if (XMLHttpAgeClass)
	{
		try
		{
			var MyId = encodeURIComponent(Id);
			var AgeClass = encodeURIComponent(document.getElementById('d_e_EnAgeClass_' + MyId).value);
			
			if (XMLHttpAgeClass.readyState==XHS_COMPLETE || XMLHttpAgeClass.readyState==XHS_UNINIT)
			{
				XMLHttpAgeClass.open("GET","SelectAgeClass.php?EnId=" + MyId + "&d_e_EnAgeClass=" + AgeClass+'&NoCheckEntry=',true);
				XMLHttpAgeClass.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="SelectAgeClass.php?EnId=" + MyId + "&d_e_EnAgeClass=" + AgeClass+'&NoCheckEntry=';
				XMLHttpAgeClass.onreadystatechange=AgeClass_StateChange;
				XMLHttpAgeClass.send();
			}
		}
		catch (e)
		{
			//alert(e.toString());
		}
	}
}

function AgeClass_StateChange()
{
	//console.debug(XMLHttp.readyState);
	// se lo stato � Complete vado avanti
	if (XMLHttpAgeClass.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttpAgeClass.status==200)
		{
			try
			{
				SelectAgeClass_Par_Response();
			}
			catch(e)
			{
				//alert(e.toString());
			}
		}
		else
		{
			//alert(XMLHttp.statusText);
		}
	}
}

function SelectAgeClass_Par_Response()
{
	//console.debug('qui');
	// leggo l'xml
	var XMLResp=XMLHttpAgeClass.responseXML;
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
		SetStyle('d_e_EnAgeClass_','');
		var Classes = XMLRoot.getElementsByTagName('classes').item(0).firstChild.data;
		
		var ValidClass=new Array();
		

		for (var i=document.getElementById("d_e_EnClass_").length-1;i>=0;i--)
			document.getElementById("d_e_EnClass_").remove(i);
		var opt = document.createElement('option');
		opt.text='--';
		opt.value='';
		try
		{
			document.getElementById("d_e_EnClass_").add(opt,null); // standard
		}
		catch(ex)
		{
			document.getElementById("d_e_EnClass_").add(opt); // IE di ....
		}				


		if (Classes!='#' && Classes!='')
		{		
			ValidClass=Classes.split(',');
			for (var i=0;i<ValidClass.length;++i)
			{
				var Id = document.getElementById('d_e_EnId_').value;
				var opt = document.createElement('option');
				opt.text=ValidClass[i];
				opt.value=ValidClass[i];
				if((Id!=0 && document.getElementById('d_e_EnClass_'+Id).innerHTML==ValidClass[i]) ||  (Id==0 && ValidClass[i]== document.getElementById('d_e_EnAgeClass_').value))
					opt.selected=true
				try
				{
					document.getElementById("d_e_EnClass_").add(opt,null); // standard
				}
				catch(ex)
				{
					document.getElementById("d_e_EnClass_").add(opt); // IE di ....
				}				
			}
		}
	}
	else
	{
		SetStyle('d_e_EnAgeClass_','error');
	}
	CheckTargetFaces();
}

/*
	- SelectSession_Par()
	Invia la get a CheckSession_Par.php
*/
function SelectSession_Par()
{
	if (XMLHttp)
	{
		try
		{			
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var Session = document.getElementById('d_q_QuSession_').value;
				var Id = document.getElementById('d_e_EnId_').value;
				XMLHttp.open("GET","CheckSession_Par.php?Session=" + Session + '&Id=' + Id);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="CheckSession_Par.php?Session=" + Session + '&Id=' + Id;
				XMLHttp.onreadystatechange=SelectSession_Par_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
		}
	}
	
}

function SelectSession_Par_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectSession_Par_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML+='Errore: ' +XMLHttp.statusText + '<br>';
		}
	}
}

function SelectSession_Par_Response()
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
		var Troppi = XMLRoot.getElementsByTagName('troppi').item(0).firstChild.data;
		
		if (Troppi==1)
		{
			document.getElementById('d_q_QuSession_').value=0;
			var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;
			alert(Msg);
		}
	}
}



/*
	- Save_Par()
	Invia la post a Save_Par.php
*/
function Save_Par()
{
// se non c'� la matricola non faccio nulla oppure mi fermo se ho degli errori
	if (document.getElementById('d_e_EnCode_').value.length>0 && !CtrlCode_Error && !TargetNo_Error)
	{
		if (XMLHttpLocal)
		{
			try
			{			
				if (XMLHttpLocal.readyState==XHS_COMPLETE || XMLHttpLocal.readyState==XHS_UNINIT)
				{
					var QueryString = '';

					QueryString
						+= 'd_e_EnId=' +encodeURIComponent(document.getElementById('d_e_EnId_').value)
						+ '&d_q_QuSession=' +encodeURIComponent(document.getElementById('d_q_QuSession_').value)
						+ '&d_q_QuTargetNo=' +encodeURIComponent(document.getElementById('d_q_QuTargetNo_').value)
						+ '&d_e_EnCode=' +encodeURIComponent(document.getElementById('d_e_EnCode_').value)
						+ '&d_e_EnName=' +encodeURIComponent(document.getElementById('d_e_EnName_').value)	
						+ '&d_e_EnFirstName=' +encodeURIComponent(document.getElementById('d_e_EnFirstName_').value)
						+ '&d_e_EnCtrlCode=' +encodeURIComponent( document.getElementById('d_e_EnCtrlCode_').value)
						+ '&d_e_EnSex=' + encodeURIComponent(document.getElementById('d_e_EnSex_').value)
						+ '&d_c_CoCode=' +encodeURIComponent( document.getElementById('d_c_CoCode_').value)
						+ '&d_c_CoName=' + encodeURIComponent(document.getElementById('d_c_CoName_').value)
						
						+ '&d_e_EnSubTeam=' +encodeURIComponent( document.getElementById('d_e_EnSubTeam_').value)
						+ '&d_c_CoCode2=' +encodeURIComponent( document.getElementById('d_c_CoCode2_').value)
						+ '&d_c_CoName2=' + encodeURIComponent(document.getElementById('d_c_CoName2_').value)
						
						+ '&d_e_EnDivision=' + encodeURIComponent(document.getElementById('d_e_EnDivision_').value)
						+ '&d_e_EnClass=' + encodeURIComponent(document.getElementById('d_e_EnClass_').value)
						+ '&d_e_EnAgeClass=' + encodeURIComponent(document.getElementById('d_e_EnAgeClass_').value)
						+ '&d_e_EnSubClass=' +encodeURIComponent( document.getElementById('d_e_EnSubClass_').value)
						+ '&d_e_EnStatus=' +encodeURIComponent( document.getElementById('d_e_EnStatus_').value)
						+ '&d_e_EnTargetFace=' +encodeURIComponent( document.getElementById('d_e_EnTargetFace_').value);
					
					XMLHttpLocal.open("POST","Save_Par.php",true);
					XMLHttpLocal.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="Save_Par.php?" + QueryString + '<br>';
					XMLHttpLocal.onreadystatechange=Save_Par_StateChange;
					XMLHttpLocal.send(QueryString);
				}
			}
			catch (e)
			{
				alert(e.toString());
			}
		}
	}
}

function Save_Par_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttpLocal.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttpLocal.status==200)
		{
			try
			{
				Save_Par_Response();
			}
			catch(e)
			{
				alert(e.toString());
			}
		}
		else
		{
			alert(e.toString());
		}
	}
}

function Save_Par_Response()
{

	// leggo l'xml
	var XMLResp=XMLHttpLocal.responseXML;
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
		var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
		var Op = XMLRoot.getElementsByTagName('op').item(0).firstChild.data;
		
		var ConfirmMsg1 = XMLRoot.getElementsByTagName('confirm_msg1').item(0).firstChild.data;
		var ConfirmMsg2 = XMLRoot.getElementsByTagName('confirm_msg2').item(0).firstChild.data;
		var ConfirmMsg3 = XMLRoot.getElementsByTagName('confirm_msg3').item(0).firstChild.data;
		var ConfirmMsg4 = XMLRoot.getElementsByTagName('confirm_msg4').item(0).firstChild.data;
		
	// se l'operazione � 'Ins' devo aggiungere in testa la riga altrimenti devo modificare quella selezionata
		if (Op=='Ins')
		{
		// Creo una riga con i campi inizializzati a niente
			var NewRow = document.createElement('TR');
			NewRow.id = 'Row_' + Id;
			NewRow.ondblclick = DblClickOnRow;
			
			var TD_Session = document.createElement("TD");
			TD_Session.className='Right';
			TD_Session.innerHTML
				= '<a name="Row_' + Id + '">'
				+ '<div id="d_q_QuSession_' + Id + '">&nbsp;</div>';
			
			var TD_TargetNo = document.createElement("TD");
			TD_TargetNo.className='Right';
			TD_TargetNo.innerHTML 
				= '<div id="d_q_QuTargetNo_' +Id + '">&nbsp;</div>';
			
			var TD_Code = document.createElement("TD");
			TD_Code.className='Right';
			TD_Code.innerHTML 
				= '<input type="hidden" id="d_e_EnStatus_' + Id + '" value="0">'
			//	+ '<input type="hidden" id="d_e_EnSex_' + Id + '" value="0">'
				+ '<div id="d_e_EnCode_' +Id + '">&nbsp;</div>';
								
			var TD_FirstName = document.createElement("TD");
			TD_FirstName.className='';
			TD_FirstName.innerHTML 
				= '<div id="d_e_EnFirstName_' +  Id + '">&nbsp;</div>';
				
			var TD_Name = document.createElement("TD");
			TD_Name.className='';
			TD_Name.innerHTML 
				= '<div id="d_e_EnName_' + Id + '">&nbsp;</div>';
			
			var TD_CtrlCode = document.createElement("TD");
			TD_CtrlCode.className='';
			TD_CtrlCode.innerHTML 
				= '<div id="d_e_EnCtrlCode_' + Id + '">&nbsp;</div>';
			
			var TD_Sex = document.createElement("TD");
			TD_Sex.className='';
			TD_Sex.innerHTML 
				= '<input type="hidden" id="d_e_EnSex_' + Id + '" value="0">'
				+ '<div id="SexName_' + Id + '">&nbsp;</div>';
			
			var TD_CountryCode = document.createElement("TD");
			TD_CountryCode.className='Right';
			TD_CountryCode.innerHTML 
				= '<input type="hidden" id="d_e_EnCountry_' +Id + '" value="0">'
				+ '<div id="d_c_CoCode_' +Id + '">&nbsp;</div>';
			
			var TD_CountryName = document.createElement("TD");
			TD_CountryName.className='';
			TD_CountryName.innerHTML 
				= '<div id="d_c_CoName_' + Id + '">&nbsp;</div>';
			
			var TD_SubTeam = document.createElement("TD");
			TD_SubTeam.className='';
			TD_SubTeam.innerHTML 
				= '<div id="d_e_EnSubTeam_' + Id + '">&nbsp;</div>';
			
			var TD_CountryCode2 = document.createElement("TD");
			TD_CountryCode2.className='Right';
			TD_CountryCode2.innerHTML 
				= '<input type="hidden" id="d_e_EnCountry2_' +Id + '" value="0">'
				+ '<div id="d_c_CoCode2_' +Id + '">&nbsp;</div>';
			
			var TD_CountryName2 = document.createElement("TD");
			TD_CountryName2.className='';
			TD_CountryName2.innerHTML 
				= '<div id="d_c_CoName2_' + Id + '">&nbsp;</div>';
			
			var TD_Div = document.createElement("TD");
			TD_Div.className='Center';
			TD_Div.innerHTML 
				= '<div id="d_e_EnDivision_' +  Id + '">&nbsp;</div>';
			
			var TD_AgeCl = document.createElement("TD");
			TD_AgeCl.className='Center';
			TD_AgeCl.innerHTML 
				= '<div id="d_e_EnAgeClass_'  +  Id + '">&nbsp;</div>';
			
			var TD_Cl = document.createElement("TD");
			TD_Cl.className='Center';
			TD_Cl.innerHTML 
				= '<div id="d_e_EnClass_' + Id + '">&nbsp;</div>';
			
			var TD_SubCl = document.createElement("TD");
			TD_SubCl.className='Center';
			TD_SubCl.innerHTML 
				= '<div id="d_e_EnSubClass_' + Id + '">&nbsp;</div>';
			
			var TD_Tf = document.createElement("TD");
			TD_Tf.className='Left';
			TD_Tf.innerHTML 
				= '<div id="d_e_EnTargetFace_' + Id + '">&nbsp;</div>';
			
			var TD_Command = document.createElement("TD");
			TD_Command.className='Center';
			TD_Command.innerHTML = '<a class="Link" href="javascript:DeleteRow_Par(' + Id + ',\'' + ConfirmMsg1 + '\',\'' + ConfirmMsg2 + '\',\'' + ConfirmMsg3 + '\',\'' + ConfirmMsg4 + '\');"><img border="0" src="../Common/Images/drop.png" alt="#" title=""></a>';

		// Aggiungo le colonne alla riga
			NewRow.appendChild(TD_Session);	
			NewRow.appendChild(TD_TargetNo);
			NewRow.appendChild(TD_Code);
			NewRow.appendChild(TD_FirstName);
			NewRow.appendChild(TD_Name);
			NewRow.appendChild(TD_CtrlCode);
			NewRow.appendChild(TD_Sex);
			NewRow.appendChild(TD_CountryCode);
			NewRow.appendChild(TD_CountryName);
			NewRow.appendChild(TD_SubTeam);
			NewRow.appendChild(TD_CountryCode2);
			NewRow.appendChild(TD_CountryName2);
			NewRow.appendChild(TD_Div);
			NewRow.appendChild(TD_AgeCl);
			NewRow.appendChild(TD_Cl);
			NewRow.appendChild(TD_SubCl);
			NewRow.appendChild(TD_Tf);
			NewRow.appendChild(TD_Command);
			
			InsertAfter(NewRow,document.getElementById('EditRowSep'));
		}
		
	// Scrivo i valori nei campi
		var Status = XMLRoot.getElementsByTagName('status').item(0).firstChild.data;
		var Session = XMLRoot.getElementsByTagName('session').item(0).firstChild.data;
		var TargetNo = XMLRoot.getElementsByTagName('targetno').item(0).firstChild.data;;
		var Code = XMLRoot.getElementsByTagName('code').item(0).firstChild.data;
		var FirstName = XMLRoot.getElementsByTagName('firstname').item(0).firstChild.data;;
		var Name = XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
		var SexId = XMLRoot.getElementsByTagName('sex_id').item(0).firstChild.data;
		var Sex = XMLRoot.getElementsByTagName('sex').item(0).firstChild.data;
		//console.debug(SexId + '-'+Sex);
		var CtrlCode = XMLRoot.getElementsByTagName('dob').item(0).firstChild.data;
		var CountryId = XMLRoot.getElementsByTagName('country_id').item(0).firstChild.data;
		var CountryCode = XMLRoot.getElementsByTagName('country_code').item(0).firstChild.data;
		var CountryName = XMLRoot.getElementsByTagName('country_name').item(0).firstChild.data;
		
		var SubTeam = XMLRoot.getElementsByTagName('sub_team').item(0).firstChild.data;
		var CountryId2 = XMLRoot.getElementsByTagName('country_id2').item(0).firstChild.data;
		var CountryCode2 = XMLRoot.getElementsByTagName('country_code2').item(0).firstChild.data;
		var CountryName2 = XMLRoot.getElementsByTagName('country_name2').item(0).firstChild.data;
		
		var Div = XMLRoot.getElementsByTagName('division').item(0).firstChild.data;
		var Cl = XMLRoot.getElementsByTagName('class').item(0).firstChild.data;
		var AgeCl = XMLRoot.getElementsByTagName('ageclass').item(0).firstChild.data;
		var SubCl = XMLRoot.getElementsByTagName('subclass').item(0).firstChild.data;
		var Tf = XMLRoot.getElementsByTagName('targetface').item(0).firstChild.data;
		
		document.getElementById('d_q_QuSession_'+Id).innerHTML=(Session!='0' ? Session : '&nbsp;');
		document.getElementById('d_q_QuTargetNo_'+Id).innerHTML=(TargetNo!='#' ? TargetNo : '&nbsp;')
		document.getElementById('d_e_EnStatus_'+Id).value=Status;
		document.getElementById('d_e_EnCode_'+Id).innerHTML=(Code!='#' ? Code : '&nbsp;');
		document.getElementById('d_e_EnFirstName_'+Id).innerHTML=(FirstName!='#' ? FirstName : '&nbsp;');
		document.getElementById('d_e_EnName_'+Id).innerHTML=(Name!='#' ? Name : '&nbsp;');
		document.getElementById('d_e_EnCtrlCode_'+Id).innerHTML=(CtrlCode!='#' ? CtrlCode : '&nbsp;');
		document.getElementById('d_e_EnSex_'+Id).value=SexId;
		document.getElementById('SexName_'+Id).innerHTML=Sex;
		document.getElementById('d_e_EnCountry_'+Id).value=(CountryId!='#' ? CountryId : '0');
		document.getElementById('d_e_EnCountry2_'+Id).value=(CountryId2!='#' ? CountryId2 : '0');
		document.getElementById('d_e_EnSubTeam_'+Id).innerHTML=SubTeam;
		document.getElementById('d_c_CoCode_'+Id).innerHTML=(CountryCode!='#' ? CountryCode : '&nbsp;');
		document.getElementById('d_c_CoCode2_'+Id).innerHTML=(CountryCode2!='#' ? CountryCode2 : '&nbsp;');
		document.getElementById('d_c_CoName_'+Id).innerHTML=(CountryName!='#' ? CountryName : '&nbsp;');
		document.getElementById('d_c_CoName2_'+Id).innerHTML=(CountryName2!='#' ? CountryName2 : '&nbsp;');
		document.getElementById('d_e_EnDivision_'+Id).innerHTML=(Div!='#' ? Div : '&nbsp;');
		document.getElementById('d_e_EnClass_'+Id).innerHTML=(Cl!='#' ? Cl : '&nbsp;');
		document.getElementById('d_e_EnAgeClass_'+Id).innerHTML=(AgeCl!='#' ? AgeCl : '&nbsp;');
		document.getElementById('d_e_EnSubClass_'+Id).innerHTML=(SubCl!='#' ? SubCl : '&nbsp;');
		document.getElementById('d_e_EnTargetFace_'+Id).innerHTML=(Tf>0 ? '<input type="hidden" id="d_e_EnTargetFaceId_'+Id+'" value="'+Tf+'"/>'+TargetFaces[Div][Cl][Tf] : '&nbsp;');
		
		var RowStyle='';
			
		switch(Status)
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
			case '6':
			case '7':
			case '9':
				RowStyle='NoShoot';
				break;
		}
		
		document.getElementById('Row_' + Id).className=RowStyle;
		
	// Aggiorno il nome della nazione per gli appartenenti a quella
		var Arr_OtherEn = XMLRoot.getElementsByTagName('other_en');
		
		if (Arr_OtherEn.length>0)
		{
		/*
			Devo cercare tutti gli id nella forma d_c_CoName_(Id) con (Id) l'id del tizio.
			-) In un vettore temporaneo metto gli id che vanno cambiati
			-) Converto in stringa
			-) per ogni div della pagina che corrisponde al pattern d_c_CoName_(Id) verifico se il suo id � fra quelli da cambiare e se s�, cambio
		*/
			var Arr_Tmp = new Array();
			
			for (var i=0;i<Arr_OtherEn.length;++i)
			{
				Arr_Tmp.push( Arr_OtherEn[i].firstChild.data);
			}
						
			var Arr2Str = Arr_Tmp.toString();
		
			var Arr_Div = document.getElementsByTagName('div');
			
			for (var i=0;i<Arr_Div.length;++i)
			{
				if (Arr_Div[i].id.match(/d_c_CoName_[0-9]+/))	// un div con id che corrisponde al pattern
				{					
					var ss =Arr_Div[i].id.split('_');
				
				// ho trovato una corrispondeza tra l'id del pattern e il vettore di riferimento			
					if (Arr2Str.search(ss[3])!=-1)	
					{
						document.getElementById(Arr_Div[i].id).innerHTML = (CountryName!='#' ? CountryName : '&nbsp;');
					}
				}
			}
		}
		
		
	// Resetto l'input
		ResetInput();
	}
}

/*
	- CheckTargetNo_Par()
	Invia la GEt a CheckTargetNo_Par.php
*/

function CheckTargetNo_Par()
{
	if (XMLHttp)
	{
		try
		{	
			var d_q_QuSession = encodeURIComponent(document.getElementById('d_q_QuSession_').value);
			var d_q_QuTargetNo = encodeURIComponent(document.getElementById('d_q_QuTargetNo_').value);
	
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) )
			{
				XMLHttp.open("GET","CheckTargetNo_Par.php?d_q_QuSession=" + d_q_QuSession + "&d_q_QuTargetNo=" + d_q_QuTargetNo,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="CheckTargetNo_Par.php?d_q_QuSession=" + d_q_QuSession + "&d_q_QuTargetNo=" + d_q_QuTargetNo;
				XMLHttp.onreadystatechange=CheckTargetNo_Par_StateChange;
				XMLHttp.send(null);
			}			
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function CheckTargetNo_Par_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CheckTargetNo_Par_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML+='Errore: ' +XMLHttp.statusText + '<br>';
		}
	}
}

function CheckTargetNo_Par_Response()
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
	if (Error==1)
	{
		SetStyle('d_q_QuTargetNo_','error');
		TargetNo_Error=true;
	}
	else
	{
		TargetNo_Error=false;		
		SetStyle('d_q_QuTargetNo_','');
		
		var TargetNo = XMLRoot.getElementsByTagName('targetno').item(0).firstChild.data;
		document.getElementById('d_q_QuTargetNo_').value=(TargetNo=='000' || TargetNo=='#' ? '' : TargetNo);
	}
}

function GetClassesByGender_Par()
{
	//return;
	var XMLHttp2=CreateXMLHttpRequestObject();
	if (XMLHttp2)
	{
	//	console.debug('ui');
		try
		{	
			var sex = encodeURIComponent(document.getElementById('d_e_EnSex_').value);
	
			if ((XMLHttp2.readyState==XHS_COMPLETE || XMLHttp2.readyState==XHS_UNINIT) )
			{
				XMLHttp2.open("GET","GetClassesByGender.php?sex=" + sex ,false);
				XMLHttp2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="CheckTargetNo_Par.php?d_q_QuSession=" + d_q_QuSession + "&d_q_QuTargetNo=" + d_q_QuTargetNo;
				//XMLHttp.onreadystatechange=GetClassByGender_Par_StateChange;
				XMLHttp2.send(null);
				
				// leggo l'xml
				var XMLResp=XMLHttp2.responseXML;
			// intercetto gli errori di IE e Opera
				if (!XMLResp || !XMLResp.documentElement)
					throw(XMLResp.responseText);
				
			// Intercetto gli errori di Firefox
				var XMLRoot;
				if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
					throw("");
				
				XMLRoot = XMLResp.documentElement;
				
				var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
					
				if (Error==0)
				{
					var combo=document.getElementById("d_e_EnAgeClass_");
					for (var i=combo.length-1;i>=0;i--)
						combo.remove(i);
					
					var opt = document.createElement('option');
					opt.text='--';
					opt.value='';
					try
					{
						combo.add(opt,null); // standard
					}
					catch(ex)
					{
						
						combo.add(opt); // IE di ....
					}	
					
					var classes=XMLRoot.getElementsByTagName('class');
					
					for (var i=0;i<classes.length;++i)
					{
						var opt = document.createElement('option');
						opt.text=classes.item(i).firstChild.data;
						opt.value=classes.item(i).firstChild.data;
						
						try
						{
							combo.add(opt,null); // standard
						}
						catch(ex)
						{
							combo.add(opt); // IE di ....
						}				
					}
				}
			}			
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
}

function CheckTargetFaces() {
	var Div=document.getElementById('d_e_EnDivision_').value;
	var Clas=document.getElementById('d_e_EnClass_').value;
	var Tf=document.getElementById('d_e_EnTargetFace_');
	
	// resets the Tf Select
	while(Tf.length>1) Tf.remove(1);
	
	if(TargetFaces[Div][Clas]) {
		for(n in TargetFaces[Div][Clas]) {
			var opt = document.createElement('option');
			opt.text=TargetFaces[Div][Clas][n];
			opt.value=n;
			
			try
			{
				Tf.add(opt,null); // standard
			}
			catch(ex)
			{
				Tf.add(opt); // IE di ....
			}				
		}

		var Id = document.getElementById('d_e_EnId_').value;
		
		if(Id!=0) {
			document.getElementById('d_e_EnTargetFace_').value = document.getElementById('d_e_EnTargetFaceId_' + Id).value;
		}
	}
}