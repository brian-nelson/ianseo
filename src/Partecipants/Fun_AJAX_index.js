/*
													- Fun_AJAX_index.js -
	Contiene le funzioni ajax che riguardano la pagina index.php e Partecipants.php
*/ 		

	
/*
	Nota Bene:
	Anche se fa schifo avere globale questa stringa, per comodità l'ho definita così.
	DEVE Essere presente nei file che richiamano questo javascript
	<?php
	print 'var StrLoading = "' . get_text('Loading','Tournament')
	?> 
*/
var debug=0;

var CacheMatr = new Array();	// cache per l'update sulla matricola
var CacheField = new Array();	// cache per l'update generico dei campi
var CacheCountryCode = new Array();	 // cache per l'update del codice nazione
var CacheCountryName = new Array();	 // cache per l'update del nome nazione

var lastSession='0';


/*
	- DblClickOnTextBox(e)
	La funzione gestisce il doppio click sulle caselle di testo e apre il popup.
	Si aggancia all' ondblclick della riga.
	e � l'evento e non deve essere passato quando la funzione viene chiamata perch�
	ci pensa il browser.
	
	(in partecipants l'evento viene agganciato tramite SetOnTextBox in Fun_JS.js alla fine del caricamento della pagina
*/


function DblClickOnTextBox(e)
{
	e=(!e ? window.event : e);
	var target = (!e.target ? e.srcElement : e.target);
	
	//alert(target.parentNode.id.split('_')[1]);
	if (target.type=='text')
	{
	/*
		L'id di ritorno lo trovo nel nome dell'id di riga.
		Dato che se sono qui significa che ho cliccato su una textbox, 
		allora so che il suo parent è una cella e il parent di questa è una riga.
		Potrei fermarmi alla cella ma preferisco risalire ancora.
	*/
		if (!document.getElementById('chk_BlockAutoSave') || !document.getElementById('chk_BlockAutoSave').checked)
		{
			var IdRet = target.parentNode.parentNode.id.split('_')[1];
			if (!IdRet)
				IdRet='';
			OpenPopup('FindArcher.php?Id=' + IdRet,'FindArcher',900,600);
		}
	}
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

function blurTextBoxName(e)
{
	e=(!e ? window.event : e);
	var target = (!e.target ? e.srcElement : e.target);
	
	if (target.type=="text")
	{
		UpdateField(target.id);
	}
}

function blurCountryName(e)
{
	e=(!e ? window.event : e);
	var target = (!e.target ? e.srcElement : e.target);
	
	if (target.type=="text")
	{
		var tmp=target.id.split('_');
		UpdateCountryName(tmp[3]);
	}
}


/*
	- GetStatus(Id)
	Invia la post a GetStatus.php che ritorna gli stati dei vari atleti.
	Se Id no è vuota la pagina filtrerà solo su quell'id
*/
function GetStatus(Id)
{
	try
	{	
		if (!document.getElementById('chk_BlockAutoSave').checked)
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var MyId=encodeURIComponent(Id);
				XMLHttp.open("POST","GetStatus.php?Id=" + MyId,true);
				//document.getElementById('idOutput').innerHTML="GetStatus.php?Id=" + MyId;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=GetStatus_StateChange;
				XMLHttp.send(null);
			}
		}
	}
	catch (e)
	{
		//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
	}
}

function GetStatus_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				GetStatus_Response();
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

function GetStatus_Response()
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
		var Arr_Id = XMLRoot.getElementsByTagName('id');
		var Arr_Status = XMLRoot.getElementsByTagName('status');
		
		for (var i=0; i < Arr_Id.length; ++i)
		{
			var NewStyle='';
			var x=Arr_Status.item(i).firstChild.data;
			
			switch(x)
			{
				case '0':
					NewStyle = '';
					break;
				case '1':
					NewStyle = 'CanShoot';
					break;
				case '5':
					NewStyle = 'UnknownShoot';
					break;
				case '8':
					NewStyle = 'CouldShoot';
					break;
				case '9':
					NewStyle = 'NoShoot';
					break;
			}
			document.getElementById('Row_' + Arr_Id.item(i).firstChild.data).className=NewStyle;
		}
	}
	
	XMLHttp = CreateXMLHttpRequestObject();
}

/*
	- GetRows(Id,OrderBy,Check)
	Esegue la POST a GetRows.
	Se Check vale true viene controllato lo stato di 'chk_BlockAutoSave'
*/
function GetRows(Id,OrderBy,Check)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				if ((Check && !document.getElementById('chk_BlockAutoSave').checked) || !Check)
				{
					var MyId = encodeURIComponent(Id);
					var MyOrderBy = encodeURIComponent(OrderBy);
					XMLHttp.open("POST","GetRows.php?Id=" + MyId + "&OrderBy=" + MyOrderBy,true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="GetRows.php?Id=" + MyId + "&OrderBy=" + MyOrderBy + '<br>';
					XMLHttp.onreadystatechange=GetRows_StateChange;
					XMLHttp.send(null);
				}
				
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
		}
	}
}


function GetRows_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				document.getElementById('tdStatus').className="Bold FontMedium";
				document.getElementById('idStatus').innerHTML='&nbsp;';
				GetRows_Response();
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
		document.getElementById('tdStatus').className="Bold FontMedium Medium";
		document.getElementById('idStatus').innerHTML= StrLoading;
	}
}


function GetRows_Response()
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
			var Divisions = XMLRoot.getElementsByTagName('div_id');
			var SubClasses = XMLRoot.getElementsByTagName('subcl_id');
			var Sessions = XMLRoot.getElementsByTagName('session_num');
			
			var AllClasses = XMLRoot.getElementsByTagName('cl_id');
			
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
			var Arr_Div = XMLRoot.getElementsByTagName('division');
			var Arr_Cl = XMLRoot.getElementsByTagName('class');
			var Arr_AgeCl = XMLRoot.getElementsByTagName('ageclass');
			var Arr_GoodCl = XMLRoot.getElementsByTagName('classes');
			var Arr_SubCl = XMLRoot.getElementsByTagName('subclass');
			var Arr_Editable = XMLRoot.getElementsByTagName('editable');
			var Arr_TargetFace = XMLRoot.getElementsByTagName('targetface');
			
			var ConfirmMsg1 = XMLRoot.getElementsByTagName('confirm_msg1').item(0).firstChild.data;
			var ConfirmMsg2 = XMLRoot.getElementsByTagName('confirm_msg2').item(0).firstChild.data;
			var ConfirmMsg3 = XMLRoot.getElementsByTagName('confirm_msg3').item(0).firstChild.data;
			var ConfirmMsg4 = XMLRoot.getElementsByTagName('confirm_msg4').item(0).firstChild.data;
	
			var tbody = document.getElementById('idAthList').getElementsByTagName("tbody").item(0);

			for (var i=0; i<Arr_Id.length;++i)
			{
				var ComboDiv 
					= '<select name="d_e_EnDivision_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnDivision_' + Arr_Id.item(i).firstChild.data + '" onBlur="javascript:UpdateField(\'d_e_EnDivision_' + Arr_Id.item(i).firstChild.data + '\');">' + "\n"
					+ '<option value="">--</option>' + "\n";
				for (j=0;j<Divisions.length;++j)
					ComboDiv+= '<option value="' + Divisions.item(j).firstChild.data + '"' + (Arr_Div.item(i).firstChild.data==Divisions.item(j).firstChild.data ? ' selected' : '') + '>' + Divisions.item(j).firstChild.data + '</option>' + "\n";
				ComboDiv+= '</select>' + "\n";
			
			// genero la combo delle ageclass usando AllClasses
				var ComboAgeCl 
					= '<select name="d_e_EnAgeClass_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnAgeClass_' + Arr_Id.item(i).firstChild.data + '"' + (Arr_Editable.item(i).firstChild.data==0 ? ' disabled' : '') + ' onChange="javascript:SelectAgeClass(' + Arr_Id.item(i).firstChild.data + ');" onBlur="javascript:UpdateClass(' + Arr_Id.item(i).firstChild.data + ');" onFocus="GetClassesByGender(' + Arr_Id.item(i).firstChild.data + ');">' + "\n"
					+ '<option value="">--</option>' + "\n";
				
				
				for (var j=0;j<AllClasses.length;++j)
				{
					ComboAgeCl+= '<option value="' + AllClasses.item(j).firstChild.data + '"' + (Arr_AgeCl.item(i).firstChild.data==AllClasses.item(j).firstChild.data ? ' selected' : '') + '>' + AllClasses.item(j).firstChild.data + '</option>' + "\n";
				}
				ComboAgeCl+= '</select>' + "\n";
								
			// genero la combo delle classi buone
				var GoodCl = new Array();

				if (Arr_GoodCl.item(i).firstChild.data!='#')
				{
					var Classes = Arr_GoodCl.item(i).firstChild.data.split(',');
					for (var k=0;k<Classes.length;++k)
					{
						if (Classes[k]!='#')
							GoodCl.push(Classes[k]);
					}				
				}
				
				var ComboCl 
					= '<select name="d_e_EnClass_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnClass_' + Arr_Id.item(i).firstChild.data + '" onBlur="javascript:UpdateClass(' + Arr_Id.item(i).firstChild.data + ');">'
					+ '<option value="">--</option>' + "\n";
					
				if (GoodCl.length>0)
				{
					for (k=0;k<GoodCl.length;++k)
					{
						ComboCl+= '<option value="' + GoodCl[k] + '"' + (GoodCl[k]==Arr_Cl.item(i).firstChild.data ? ' selected' : '') + '>' + GoodCl[k] + '</option>' + "\n";
					}
				}
				
				ComboCl+= '</select>' + "\n";
												
				var ComboSubCl
					= '<select name="d_e_EnSubClass_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnSubClass_' + Arr_Id.item(i).firstChild.data + '" onBlur="javascript:UpdateField(\'d_e_EnSubClass_' + Arr_Id.item(i).firstChild.data + '\');">' + "\n"
					+ '<option value="">--</option>' + "\n";
				for (j=0;j<SubClasses.length;++j)
					ComboSubCl+= '<option value="' + SubClasses.item(j).firstChild.data + '"' + (SubClasses.item(j).firstChild.data==Arr_SubCl.item(i).firstChild.data ? ' selected' : '') + '>' + SubClasses.item(j).firstChild.data + '</option>' + "\n";
				ComboSubCl+= '</select>' + "\n";
				
				var ComboSes
					= '<select name="d_q_QuSession_' + Arr_Id.item(i).firstChild.data + '" id="d_q_QuSession_' + Arr_Id.item(i).firstChild.data + '" onBlur="javascript:UpdateSession(\'d_q_QuSession_' + Arr_Id.item(i).firstChild.data + '\');">' + "\n"
					+ '<option value="0">--</option>' + "\n";
				for (j=0;j<Sessions.length;++j)
					ComboSes+= '<option value="' + Sessions.item(j).firstChild.data + '"' + (Sessions.item(j).firstChild.data==Arr_Session.item(i).firstChild.data ? ' selected' : '') + '>' + Sessions.item(j).firstChild.data + '</option>' + "\n";
				ComboSes+= '</select>' + "\n";
				
			// combo del sesso
				var ComboSex
					= '<select name="d_e_EnSex_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnSex_' + Arr_Id.item(i).firstChild.data + '" onChange="javascript:UpdateCtrlCode(' + Arr_Id.item(i).firstChild.data + ')">' + "\n"
						+ '<option value="0"' + (Arr_SexId.item(i).firstChild.data==0 ? ' selected' : '') + '>' + StrShortMale + '</option>' + "\n"
						+ '<option value="1"' + (Arr_SexId.item(i).firstChild.data==1 ? ' selected' : '') + '>' + StrShortFemale + '</option>' + "\n"
					+ '</select>';
				
			// combo of Targetfaces
				var ComboTf
					= '<select name="d_e_EnTargetFace_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnTargetFace_' + Arr_Id.item(i).firstChild.data + '" onChange="javascript:UpdateCtrlCode(' + Arr_Id.item(i).firstChild.data + ')">' + "\n"
						+ '<option value="0">--</option>' + "\n";
					Div=Arr_Div.item(i).firstChild.data;
					Clas=Arr_Cl.item(i).firstChild.data;
					Tf=Arr_TargetFace.item(i).firstChild.data;
					if(TargetFaces[Div][Clas]) {
						for(n in TargetFaces[Div][Clas]) {
							ComboTf += '<option value="'+n+'"'+(n==Tf?' selected="selected"':'')+'>'+TargetFaces[Div][Clas][n]+'</option>';
						}
					}
					ComboTf += '</select>';
				
			// Nuova riga
				var NewRow = document.createElement("TR");
				NewRow.id = 'Row_' + Arr_Id.item(i).firstChild.data;
				NewRow.ondblclick = DblClickOnTextBox;
				NewRow.oncontextmenu=FindByContext;
					
			// Colonne
				var TD_Session = document.createElement("TD");
				TD_Session.id='Col_Session_' + Arr_Id.item(i).firstChild.data;
				TD_Session.className='Center';
				TD_Session.innerHTML=ComboSes;
				//document.getElementById('idOutput').innerHTML='qui';	
				
				var TD_TargetNo = document.createElement("TD");
				TD_TargetNo.className='Bold';
				TD_TargetNo.innerHTML=(Arr_TargetNo.item(i).firstChild.data!='#' ? Arr_TargetNo.item(i).firstChild.data : '&nbsp;');
				
				var TD_Code = document.createElement("TD");
				TD_Code.id='Col_Code_' + Arr_Id.item(i).firstChild.data;
				TD_Code.className='Center';
				TD_Code.innerHTML 
					= '<input type="hidden" name="CanComplete_' + Arr_Id.item(i).firstChild.data +  '" id="CanComplete_' + Arr_Id.item(i).firstChild.data + '" value="0">'
					+ '<input type="hidden" name="d_e_EnStatus_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnStatus_' + Arr_Id.item(i).firstChild.data + '" value="' + Arr_Status.item(0).firstChild.data + '">'
					+ '<input type="text" size="5" maxlength="9" name="d_e_EnCode_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'id="d_e_EnCode_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'value="' + (Arr_Code.item(i).firstChild.data!='#' ? Arr_Code.item(i).firstChild.data : '') + '" '
					+ 'onFocus="javascript:SetCompleteFlag(' + Arr_Id.item(i).firstChild.data + ');" '
					+ 'onKeyUp="javascript:CercaMatr(\'d_e_EnCode_' + Arr_Id.item(i).firstChild.data + '\',' + Arr_Id.item(i).firstChild.data + ');" '
					+ 'onBlur="javascript:UpdateField(\'d_e_EnCode_' + Arr_Id.item(i).firstChild.data + '\'); SetCompleteFlag(' + Arr_Id.item(i).firstChild.data + ');">';
				
				var TD_FirstName = document.createElement("TD");
				TD_FirstName.id='Col_FirstName_' + Arr_Id.item(i).firstChild.data;
				TD_FirstName.className='Center';
				
				/*TD_FirstName.innerHTML 
					= '<input type="text"  size="25" maxlength="30" name="d_e_EnFirstName_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'id="d_e_EnFirstName_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'value="' + (Arr_FirstName.item(i).firstChild.data!='#' ? Arr_FirstName.item(i).firstChild.data : '') + '" '
					+ 'onBlur="javascript:UpdateField(\'d_e_EnFirstName_' + Arr_Id.item(i).firstChild.data +'\');">'
					//+ 'onDblClick="javascript:OpenPopup(\'FindArcher.php?Id=' + Arr_Id.item(i).firstChild.data + '\',\'FindArcher\',900,600);">';
				*/
				
				var txt = document.createElement("INPUT");
				txt.type="text";
				txt.maxLength=30;
				txt.size=20;
				txt.name='d_e_EnFirstName_' + Arr_Id.item(i).firstChild.data;
				txt.id='d_e_EnFirstName_' + Arr_Id.item(i).firstChild.data;
				txt.value= (Arr_FirstName.item(i).firstChild.data!='#' ? Arr_FirstName.item(i).firstChild.data : '');
				txt.onblur=blurTextBoxName;
				txt.ondblclick=DblClickOnTextBox;
				//txt.onmousedown=ClickOnTextBox;
				
				TD_FirstName.appendChild(txt);
				
				var TD_Name = document.createElement("TD");
				TD_Name.id='Col_Name_' + Arr_Id.item(i).firstChild.data;
				TD_Name.className='Center';
				/*
				TD_Name.innerHTML 
					= '<input type="text"  size="25" maxlength="30" name="d_e_EnName_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'id="d_e_EnName_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'value="' + (Arr_Name.item(i).firstChild.data!='#' ? Arr_Name.item(i).firstChild.data : '') + '" '
					+ 'onBlur="javascript:UpdateField(\'d_e_EnName_' + Arr_Id.item(i).firstChild.data +'\');">'
					//+ 'onDblClick="javascript:OpenPopup(\'FindArcher.php?Id=' + Arr_Id.item(i).firstChild.data + '\',\'FindArcher\',900,600);">';
				*/
				
				var txt = document.createElement("INPUT");
				txt.type="text";
				txt.maxLength=30;
				txt.size=20;
				txt.name='d_e_EnName_' + Arr_Id.item(i).firstChild.data;
				txt.id='d_e_EnName_' + Arr_Id.item(i).firstChild.data;
				txt.value= (Arr_Name.item(i).firstChild.data!='#' ? Arr_Name.item(i).firstChild.data : '');
				txt.onblur=blurTextBoxName;
				txt.ondblclick=DblClickOnTextBox;
				//txt.onmousedown=ClickOnTextBox;
				
				TD_Name.appendChild(txt);
				
				if (debug)
					console.debug( (Arr_FirstName.item(i).firstChild.data!='#' ? Arr_FirstName.item(i).firstChild.data : '') + " " +(Arr_Name.item(i).firstChild.data!='#' ? Arr_Name.item(i).firstChild.data : '') );
				/*
				var TD_Sex = document.createElement("TD");
				TD_Sex.className='Center';
				TD_Sex.innerHTML 
					= '<select name="d_e_EnSex_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnSex_' + Arr_Id.item(i).firstChild.data + '">' + "\n"
					+ '<option value="0"' + (Arr_Sex.item(i).firstChild.data==0 ? ' selected' : '') + '>M</option>' + "\n"
					+ '<option value="1"' + (Arr_Sex.item(i).firstChild.data==1 ? ' selected' : '') + '>F</option>' + "\n"
					+ '</select>' + "\n";
				*/
					
				var TD_CtrlCode = document.createElement("TD");
				TD_CtrlCode.id='Col_CtrlCode_' + Arr_Id.item(i).firstChild.data;
				TD_CtrlCode.className='Center';
				TD_CtrlCode.innerHTML 
					= '<input type="text"  size="10" maxlength="16" name="d_e_EnCtrlCode_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'id="d_e_EnCtrlCode_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'value="' + (Arr_Dob.item(i).firstChild.data!='#' ? Arr_Dob.item(i).firstChild.data : '') + '" '
					+ 'onBlur="javascript:UpdateCtrlCode(' + Arr_Id.item(i).firstChild.data + ');">';
				
				var TD_Sex = document.createElement("TD");
				TD_Sex.id='Col_Sex_' + Arr_Id.item(i).firstChild.data;
				TD_Sex.className='Center';
				TD_Sex.innerHTML=ComboSex;
				
				var TD_CountryCode = document.createElement("TD");
				TD_CountryCode.id='Col_CountryCode_' + Arr_Id.item(i).firstChild.data;
				TD_CountryCode.className='Center';
				TD_CountryCode.innerHTML 
					= '<input type="hidden" name="d_e_EnCountry_' + Arr_Id.item(i).firstChild.data + '" id="d_e_EnCountry_' + Arr_Id.item(i).firstChild.data + '" value="' + Arr_CountryId.item(i).firstChild.data + '">'
					+ '<input type="text" size="5" maxlength="5" name="d_c_CoCode_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'id="d_c_CoCode_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'value="' + (Arr_CountryCode.item(i).firstChild.data!='#' ? Arr_CountryCode.item(i).firstChild.data : '') + '" '
					+ 'onBlur="javascript:UpdateCountryCode(\'' +  Arr_Id.item(i).firstChild.data + '\');" '
					+ 'onKeyUp="javascript:SelectCountryCode(\'' +  Arr_Id.item(i).firstChild.data + '\');">';
					
	
				var TD_CountryName = document.createElement("TD");
				TD_CountryName.id='Col_CountryName_' + Arr_Id.item(i).firstChild.data;
				TD_CountryName.className='Center';
				/*
				TD_CountryName.innerHTML 
					= '<input type="text"  size="30" maxlength="30" name="d_c_CoName_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'id="d_c_CoName_' + Arr_Id.item(i).firstChild.data + '" '
					+ 'value="' + (Arr_CountryName.item(i).firstChild.data!='#' ? Arr_CountryName.item(i).firstChild.data : '') + '" '	
					+ 'onBlur="javascript:UpdateCountryName(\'' +  Arr_Id.item(i).firstChild.data + '\');">';
				*/
				
				var txt = document.createElement("INPUT");
				txt.type="text";
				txt.maxLength=30;
				txt.size=20;
				txt.name='d_c_CoName_' + Arr_Id.item(i).firstChild.data;
				txt.id='d_c_CoName_' + Arr_Id.item(i).firstChild.data;
				txt.value=  (Arr_CountryName.item(i).firstChild.data!='#' ? Arr_CountryName.item(i).firstChild.data : '');
				txt.onblur=blurCountryName;
				
				TD_CountryName.appendChild(txt);
			
				var TD_Div = document.createElement("TD");
				TD_Div.id='Col_Div_' + Arr_Id.item(i).firstChild.data;
				TD_Div.className='Center';
				TD_Div.innerHTML = ComboDiv;
				
				var TD_AgeCl = document.createElement("TD");
				TD_AgeCl.id='Col_AgeCl_' + Arr_Id.item(i).firstChild.data;
				TD_AgeCl.className='Center';
				TD_AgeCl.innerHTML = ComboAgeCl;
				
				var TD_Cl = document.createElement("TD");
				TD_Cl.id='Col_Cl_' + Arr_Id.item(i).firstChild.data;
				TD_Cl.className='Center';
				TD_Cl.innerHTML = ComboCl;
				
				var TD_SubCl = document.createElement("TD");
				TD_SubCl.id='Col_SubCl_' + Arr_Id.item(i).firstChild.data;
				TD_SubCl.className='Center';
				TD_SubCl.innerHTML = ComboSubCl;
				
				var TD_Tf = document.createElement("TD");
				TD_Tf.id='Col_Tf_' + Arr_Id.item(i).firstChild.data;
				TD_Tf.className='Left';
				TD_Tf.innerHTML = ComboTf;
				
				var TD_Command = document.createElement("TD");
				TD_Command.id='Col_Command_' + Arr_Id.item(i).firstChild.data;
				TD_Command.className='Center';
				TD_Command.innerHTML = '<a class="Link" href="javascript:DeleteRow(' + Arr_Id.item(i).firstChild.data + ',\'' + ConfirmMsg1 + '\',\'' + ConfirmMsg2 + '\',\'' + ConfirmMsg3 + '\',\'' + ConfirmMsg4 + '\');"><img border="0" src="../Common/Images/drop.png" alt="#" title=""></a>';
				
				
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
					case '5':
						RowStyle='UnknownShoot';
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
	- CercaMatr(IdFrom,IdReturn)
	Invia la POST a Matr_FindOnEdit.php passando Matr=<Id.value > & IdReturn=<Ret>
	Va agganciata all'evento onKeyUp di d_e_EnCode_(Id)
*/
function CercaMatr(Id,Ret)
{
/* 
	ricreo l'oggetto XMLHttp perch� se si apre il popup del cerca va tutto a ramengo ;(
*/
	XMLHttp = CreateXMLHttpRequestObject();
	
	if (XMLHttp)
	{
		if (Id && Ret)
		{
			var dt = new Date();
			
			var Matr = encodeURIComponent(document.getElementById(Id).value);
			var IdReturn =  encodeURIComponent(Ret);	
			CacheMatr.push("Matr=" + Matr + "&IdReturn=" + Ret + "&dt=" + dt.getTime());
			
			var SplittedId = new Array();
			SplittedId=Id.split('_');
		}
		
		try
		{	
			if (!document.getElementById('chk_BlockAutoSave').checked && document.getElementById('CanComplete_' + SplittedId[3]).value==1)
			{
				if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && CacheMatr.length>0)
				{
					var FromCache = CacheMatr.shift();
					XMLHttp.open("POST","Matr_FindOnEdit.php",true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="Matr_FindOnEdit.php?"  + FromCache;
					XMLHttp.onreadystatechange=CercaMatr_StateChange;
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

function CercaMatr_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CercaMatr_Response();
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

function CercaMatr_Response()
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
		var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
		var Session = XMLRoot.getElementsByTagName('session').item(0).firstChild.data;
		var Status = XMLRoot.getElementsByTagName('status').item(0).firstChild.data;
		var Code = XMLRoot.getElementsByTagName('code').item(0).firstChild.data;
		var FirstName = XMLRoot.getElementsByTagName('firstname').item(0).firstChild.data;
		var Name = XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
		var CtrlCode = XMLRoot.getElementsByTagName('ctrl_code').item(0).firstChild.data;
		var Dob = XMLRoot.getElementsByTagName('dob').item(0).firstChild.data;
		var Sex = XMLRoot.getElementsByTagName('sex_id').item(0).firstChild.data;
		var CountryId = XMLRoot.getElementsByTagName('country_id').item(0).firstChild.data;
		var CountryCode = XMLRoot.getElementsByTagName('country_code').item(0).firstChild.data;
		var CountryName = XMLRoot.getElementsByTagName('country_name').item(0).firstChild.data;
		var Division = XMLRoot.getElementsByTagName('division').item(0).firstChild.data;
		var Class = XMLRoot.getElementsByTagName('class').item(0).firstChild.data;
		var SubClass = XMLRoot.getElementsByTagName('subclass').item(0).firstChild.data;
		var AgeClass = XMLRoot.getElementsByTagName('ageclass').item(0).firstChild.data;
		var Editable = XMLRoot.getElementsByTagName('editable').item(0).firstChild.data;
		var Arr_GoodCl = XMLRoot.getElementsByTagName('classes').item(0).firstChild.data;
		
		var AllClasses = XMLRoot.getElementsByTagName('cl_id');
		
		if (Code=='#') Code='';
		if (Name=='#') Name='';
		if (FirstName=='#') FirstName='';
		if (Dob=='#') Dob='';
		if (CtrlCode=='#') CtrlCode='';
		if (Division=='#') Division='';
		if (AgeClass=='#') AgeClass='';
		if (SubClass=='#') SubClass='';
		if (CountryId=='#') CountryId='';
		if (CountryCode=='#') CountryCode='';
		if (CountryName=='#') CountryName='';
		if (Status=='#') Status='';
		
		
	// genero la combo delle ageclass usando AllClasses
		var ComboAgeCl 
			= '<select name="d_e_EnAgeClass_' + Id + '" id="d_e_EnAgeClass_' + Id + '"' + (Editable==0 ? ' disabled' : '') + ' onChange="javascript:SelectAgeClass(' + Id + ');" onBlur="javascript:UpdateClass(' + Id + ');" onFocus="GetClassesByGender(' + Id + ');">' + "\n"
			+ '<option value="">--</option>' + "\n";
		
		
		for (var j=0;j<AllClasses.length;++j)
		{
			ComboAgeCl+= '<option value="' + AllClasses.item(j).firstChild.data + '"' + (AgeClass==AllClasses.item(j).firstChild.data ? ' selected' : '') + '>' + AllClasses.item(j).firstChild.data + '</option>' + "\n";
		}
		ComboAgeCl+= '</select>' + "\n";
						
	// genero la combo delle classi buone
		var GoodCl = new Array();
		
		if (Arr_GoodCl!='#')
		{
			var Classes = Arr_GoodCl.split(',');
			for (var k=0;k<Classes.length;++k)
			{
				if (Classes[k]!='#')
					GoodCl.push(Classes[k]);
			}				
		}
		
		var ComboCl 
			= '<select name="d_e_EnClass_' + Id + '" id="d_e_EnClass_' + Id + '" onBlur="javascript:UpdateClass(' + Id + ');">'
			+ '<option value="">--</option>' + "\n";
			
		if (GoodCl.length>0)
		{
			for (k=0;k<GoodCl.length;++k)
			{
				ComboCl+= '<option value="' + GoodCl[k] + '"' + (GoodCl[k]==Class ? ' selected' : '') + '>' + GoodCl[k] + '</option>' + "\n";
			}
		}
		
		ComboCl+= '</select>' + "\n";
		
		document.getElementById('d_e_EnName_' + Id).value=Name;
		document.getElementById('d_e_EnFirstName_' + Id).value=FirstName;
		document.getElementById('d_e_EnSex_' + Id).value=Sex;
		document.getElementById('d_e_EnCtrlCode_' + Id).value=Dob;
		document.getElementById('d_e_EnCountry_' + Id).value=CountryId;
		document.getElementById('d_c_CoCode_' + Id).value=CountryCode;
		document.getElementById('d_c_CoName_' + Id).value=CountryName;
		document.getElementById('d_e_EnDivision_' + Id).value=Division;
		
		/*document.getElementById('d_e_EnClass_' + Id).value=Class;
		document.getElementById('d_e_EnSubClass_' + Id).value=SubClass;
		if (document.getElementById('idAgeClass_' + Id))
			document.getElementById('idAgeClass_' + Id).innerHTML=AgeClass;
		else if (document.getElementById('d_e_EnAgeClass_' + Id))
			document.getElementById('d_e_EnAgeClass_' + Id).value=AgeClass;*/
			
		document.getElementById('Col_AgeCl_' + Id).innerHTML=ComboAgeCl;
		document.getElementById('Col_Cl_' + Id).innerHTML=ComboCl;
		
		document.getElementById('d_e_EnSubClass_' + Id).value=SubClass;
		document.getElementById('d_e_EnStatus_' + Id).value=Status;
				
	}
	
	GetStatus(Id);
	// per scaricare la cache degli update	
	setTimeout("CercaMatr()",1000);
}

function old_CercaMatr_Response()
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
		var Id = XMLRoot.getElementsByTagName('id_ret').item(0).firstChild.data;

		var Code = XMLRoot.getElementsByTagName('code').item(0).firstChild.data;
		if (Code=='#') Code='';
		
		var Name = XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
		if (Name=='#') Name='';
		
		var FirstName = XMLRoot.getElementsByTagName('first_name').item(0).firstChild.data;
		if (FirstName=='#') FirstName='';
		
		var CtrlCode = XMLRoot.getElementsByTagName('ctrl_code').item(0).firstChild.data;
		if (CtrlCode=='#') CtrlCode='';
		
		var Sex = XMLRoot.getElementsByTagName('sex').item(0).firstChild.data;
		if (Sex=='#') Sex='';
		
		var Div = XMLRoot.getElementsByTagName('div').item(0).firstChild.data;
		if (Div=='#') Div='';
		
		var Class = XMLRoot.getElementsByTagName('class').item(0).firstChild.data;
		if (Class=='#') Class='';
		
		var AgeClass = XMLRoot.getElementsByTagName('age_class').item(0).firstChild.data;
		if (AgeClass=='#') AgeClass='&nbsp;';
		
		var SubClass = XMLRoot.getElementsByTagName('sub_class').item(0).firstChild.data;
		if (SubClass=='#') SubClass='';
		
		var IdCountry = XMLRoot.getElementsByTagName('id_country').item(0).firstChild.data;
		if (IdCountry=='#') IdCountry='';
		
		var Country = XMLRoot.getElementsByTagName('country').item(0).firstChild.data;
		if (Country=='#') Country='';
		
		var Nation = XMLRoot.getElementsByTagName('nation').item(0).firstChild.data;
		if (Nation=='#') Nation='';
		
		var Status = XMLRoot.getElementsByTagName('status').item(0).firstChild.data;
		if (Status=='#') Status='';
		
		document.getElementById('idOutput').innerHTML
			+= '<br>' + Id + '<br>' 
			 + CtrlCode + '<br>'
			 + IdCountry + '<br>'
			 + Country + '<br>'
			 + Nation + '<br>'
			 + Div + '<br>'
			 + AgeClass + '<br>'
			 + Class + '<br>'
			 + SubClass + ' <br>'
			 + Status;
			 
			 
		//document.getElementById('d_e_EnCode_' + Id).value=Code;
		document.getElementById('d_e_EnName_' + Id).value=Name;
		document.getElementById('d_e_EnFirstName_' + Id).value=FirstName;
		//document.getElementById('d_e_EnSex_' + Id).value=Sex;
		document.getElementById('d_e_EnCtrlCode_' + Id).value=CtrlCode;
		document.getElementById('d_e_EnCountry_' + Id).value=IdCountry;
		document.getElementById('d_c_CoCode_' + Id).value=Country;
		document.getElementById('d_c_CoName_' + Id).value=Nation;
		document.getElementById('d_e_EnDivision_' + Id).value=Div;
		document.getElementById('d_e_EnClass_' + Id).value=Class;
		document.getElementById('d_e_EnSubClass_' + Id).value=SubClass;
		if (document.getElementById('idAgeClass_' + Id))
			document.getElementById('idAgeClass_' + Id).innerHTML=AgeClass;
		else if (document.getElementById('d_e_EnAgeClass_' + Id))
			document.getElementById('d_e_EnAgeClass_' + Id).value=AgeClass;
		document.getElementById('d_e_EnStatus_' + Id).value=Status;
	}
	
	//GetStatus(Id);
	// per scaricare la cache degli update	
	setTimeout("CercaMatr()",1000);
}

/*
	- UpdateField(Field)
	Invia la POST a UpdateField.php il campo Field da aggiornare
	Va agganciata all'evento onBlur di tutti i campi tranne d_e_EnCode_(id), d_c_CoCode,d_c_CoName
	(In caso di <select> si pu� agganciare all'evento onChange)
*/
function UpdateField(Field)
{
	if (XMLHttp)
	{
		if (Field)
		{
			var FieldName = encodeURIComponent(Field);
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			CacheField.push(FieldName + "=" + FieldValue);
		}
		
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && CacheField.length>0)
				{
					var FromCache = CacheField.shift();
					XMLHttp.open("POST","UpdateField.php",true);
					//document.getElementById('idOutput').innerHTML="UpdateField.php?" + FieldName + "=" + FieldValue;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=UpdateField_StateChange;
					XMLHttp.send(FromCache);
				}
			}
			else
				CacheField.shift();
		}
		catch (e)
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateField_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateField_Response();
			}
			catch(e)
			{
				//document.getElementById('idStatus').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idStatus').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function UpdateField_Response()
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
	var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
	
	if (Error==1)
	{
		SetStyle(Which,'error');
	}
	else
	{
		var value = XMLRoot.getElementsByTagName('value').item(0).firstChild.data;
		document.getElementById(Which).value=value;
		SetStyle(Which,'');
	}
	
		
// per scaricare la cache degli update	
	setTimeout("UpdateField()",1000);
}

/*
	- SelectCountryCode(Id).
	Esegue la GET a SelectCountryCode.php
*/
function SelectCountryCode(Id)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave') || !document.getElementById('chk_BlockAutoSave').checked)
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var IdEntry = encodeURIComponent(Id);
					var Code = encodeURIComponent(document.getElementById('d_c_CoCode_' + Id).value);
					XMLHttp.open("GET","SelectCountryCode.php?IdEntry=" + IdEntry + "&Code=" + Code);
					//document.getElementById('idOutput').innerHTML="SelectCountryCode.php?IdEntry=" + IdEntry + "&Code=" + Code;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=SelectCountryCode_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function SelectCountryCode_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectCountryCode_Response();
			}
			catch(e)
			{
				//	document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function SelectCountryCode_Response()
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
	
	var Name=XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
	var Id=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	var IdRet=XMLRoot.getElementsByTagName('id_ret').item(0).firstChild.data;
	
	if (Error==0)
	{
		document.getElementById('d_c_CoName_' + IdRet).value=(Name!='#' ? Name : '');
		document.getElementById('d_e_EnCountry_' + IdRet).value=Id;
		SetStyle('d_c_CoCode_' + IdRet,'');
		SetStyle('d_c_CoName_' + IdRet,'');
	}
	else
	{
		SetStyle('d_c_CoCode_' + IdRet,'error');
		SetStyle('d_c_CoName_' + IdRet,'error');
	}

}



function SelectCountryCode2(Id)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave') || !document.getElementById('chk_BlockAutoSave').checked)
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var IdEntry = encodeURIComponent(Id);
					var Code = encodeURIComponent(document.getElementById('d_c_CoCode2_' + Id).value);
					XMLHttp.open("GET","SelectCountryCode.php?IdEntry=" + IdEntry + "&Code=" + Code);
					//document.getElementById('idOutput').innerHTML="SelectCountryCode.php?IdEntry=" + IdEntry + "&Code=" + Code;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=SelectCountryCode2_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function SelectCountryCode2_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectCountryCode2_Response();
			}
			catch(e)
			{
				//	document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function SelectCountryCode2_Response()
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
	
	var Name=XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
	var Id=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	var IdRet=XMLRoot.getElementsByTagName('id_ret').item(0).firstChild.data;
	
	if (Error==0)
	{
		document.getElementById('d_c_CoName2_' + IdRet).value=(Name!='#' ? Name : '');
		document.getElementById('d_e_EnCountry2_' + IdRet).value=Id;
		SetStyle('d_c_CoCode2_' + IdRet,'');
		SetStyle('d_c_CoName2_' + IdRet,'');
	}
	else
	{
		SetStyle('d_c_CoCode2_' + IdRet,'error');
		SetStyle('d_c_CoName2_' + IdRet,'error');
	}

}




/*
	- UpdateCountryCode(Id).
	Esegue la POST a UpdateCountryCode.php
*/
function UpdateCountryCode(Id)
{
	if (XMLHttp)
	{
		if (Id)
		{
			var IdEntry = encodeURIComponent(Id);
			var Code = encodeURIComponent(document.getElementById('d_c_CoCode_' + Id).value);
			CacheCountryCode.push("IdEntry=" + IdEntry + "&Code=" + Code);
		}
		
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && CacheCountryCode.length>0)
				{
					if (document.getElementById('d_c_CoCode_' + Id).className=='')
					{
						var FromCache = CacheCountryCode.shift();
						IdEntry = encodeURIComponent(Id);
						Code = encodeURIComponent(document.getElementById('d_c_CoCode_' + Id).value);
						XMLHttp.open("POST","UpdateCountryCode.php");
						//document.getElementById('idOutput').innerHTML="UpdateCountryCode.php?IdEntry=" + IdEntry + "&Code=" + Code + '<br>';
						XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
						XMLHttp.onreadystatechange=UpdateCountryCode_StateChange;
						XMLHttp.send(FromCache);
					}
				}
			}
			else
				CacheCountryCode.shift();
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateCountryCode_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateCountryCode_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function UpdateCountryCode_Response()
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
	var CoName=XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
	var CoId=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	var Id=XMLRoot.getElementsByTagName('id_ret').item(0).firstChild.data;
	
	if (Error==0)
	{	
		document.getElementById('d_c_CoName_' + Id).value=(CoName!='#' ? CoName : '');
		document.getElementById('d_e_EnCountry_' + Id).value=CoId;
		
		SetStyle('d_c_CoCode_' + Id,'');
		SetStyle('d_c_CoName_' + Id,'');
	}
	else
	{
		SetStyle('d_c_CoCode_' + Id,'error');
		SetStyle('d_c_CoName_' + Id,'error');
	}
	
	// per scaricare la cache degli update	
	setTimeout("UpdateCountryCode()",1000);

}

/*
	- UpdateCountryName(Id).
	Esegue la GET a UpdateCountryName.php
*/
function UpdateCountryName(Id)
{
	if (XMLHttp)
	{
		if (Id)
		{
			var Code = encodeURIComponent(document.getElementById('d_c_CoCode_' + Id).value);
			var Name = encodeURIComponent(document.getElementById('d_c_CoName_' + Id).value);
			CacheCountryName.push("Code=" + Code + "&Name=" + Name);
		}
		
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					if (document.getElementById('d_c_CoName_' + Id).className=='')
					{
						var FromCache = CacheCountryName.shift();
						//XMLHttp.open("POST","UpdateCountryName.php?Name=" + Name + "&Code=" + Code,true);
						XMLHttp.open("POST","UpdateCountryName.php",true);
 						//document.getElementById('idOutput').innerHTML="UpdateCountryName.php?Name=" + Name + "&Code=" + Code +'<br>';
						XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
						XMLHttp.onreadystatechange=UpdateCountryName_StateChange;
						//XMLHttp.send(null);
						XMLHttp.send(FromCache);
					}
				}
			}
			else
				CacheCountryName.shift();
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateCountryName_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateCountryName_Response();
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString();
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}


function UpdateCountryName_Response()
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
	var Code=XMLRoot.getElementsByTagName('code').item(0).firstChild.data;
	var Name=XMLRoot.getElementsByTagName('name').item(0).firstChild.data;	
	if (Error==0)
	{
		if (Name!='#')
		{
			var NewName=XMLRoot.getElementsByTagName('new_name').item(0).firstChild.data;
		
			if (NewName==1)	// aggiorno le textbox dei nomi nazione che hanno il codice uguale a quello ritornato
			{
				var Objs = document.Frm.elements;
				for (i=0; i<Objs.length; ++i)
				{
					if (Objs[i].name.substr(0,11)=='d_c_CoCode_' && Objs[i].value==Code)
					{
						Id = Objs[i].name.substr(11);
						document.getElementById('d_c_CoName_' + Id).value=Name;
						
						SetStyle('d_c_CoCode_' + Id,'');
						SetStyle('d_c_CoName_' + Id,'');
					}
					else
						continue;
				}
			}
		}
	}
	else
	{
		var Objs = document.Frm.elements;
		for (i=0; i<Objs.length; ++i)
		{
			if (Objs[i].name.substr(0,11)=='d_c_CoCode_' && Objs[i].value==Code)
			{
				Id = Objs[i].name.substr(11);
				SetStyle('d_c_CoCode_' + Id,'error');
				SetStyle('d_c_CoName_' + Id,'error');
			}
			else
				continue;
		}
	}
		
	// per scaricare la cache degli update	
	setTimeout("UpdateCountryName()",1000);
}

/*
	- UpdateCtrlCode(Id,Code).
	Aggiorna il codice fiscale di Id con Code
*/

function UpdateCtrlCode(Id)
{//alert(Id);
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				var MyId = encodeURIComponent(Id);
				var MyCode = encodeURIComponent(document.getElementById('d_e_EnCtrlCode_' + Id).value);
				var MySex= encodeURIComponent(document.getElementById('d_e_EnSex_' + Id).value);
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("POST","UpdateCtrlCode.php?EnId=" + MyId + "&d_e_EnCtrlCode=" + MyCode + '&EnSex=' + MySex);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="UpdateCtrlCode.php?EnId=" + MyId + "&d_e_EnCtrlCode=" + MyCode;
					XMLHttp.onreadystatechange=UpdateCtrlCode_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			//alert(e.toString());
		}
	}
}

function UpdateCtrlCode_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateCtrlCode_Response();
			}
			catch(e)
			{
				//alert(e.toString());
			}
		}
		else
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' +XMLHttp.statusText;
		}
	}
}

function UpdateCtrlCode_Response()
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
	var Id=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	//alert(Id);
	if (Error==0)
	{
		SetStyle('d_e_EnCtrlCode_' + Id,'');
		
	// devo rigestire le tendine delle classi
		
		var AllClasses = XMLRoot.getElementsByTagName('cl_id');
		var Arr_Cl = XMLRoot.getElementsByTagName('class').item(0).firstChild.data;
		var Arr_AgeCl = XMLRoot.getElementsByTagName('ageclass').item(0).firstChild.data;
		var Arr_GoodCl = XMLRoot.getElementsByTagName('classes').item(0).firstChild.data;
		var Arr_Editable = XMLRoot.getElementsByTagName('editable').item(0).firstChild.data;
		
	// genero la combo delle ageclass usando AllClasses
		var ComboAgeCl 
			= '<select name="d_e_EnAgeClass_' + Id + '" id="d_e_EnAgeClass_' + Id + '"' + (Arr_Editable==0 ? ' disabled' : '') + ' onChange="javascript:SelectAgeClass(' + Id + ');" onBlur="javascript:UpdateClass(' + Id + ');" onFocus="GetClassesByGender(' + Id + ');">' + "\n"
			+ '<option value="">--</option>' + "\n";
		
		
		for (j=0;j<AllClasses.length;++j)
		{
			ComboAgeCl+= '<option value="' + AllClasses.item(j).firstChild.data + '"' + (Arr_AgeCl==AllClasses.item(j).firstChild.data ? ' selected' : '') + '>' + AllClasses.item(j).firstChild.data + '</option>' + "\n";
		}
		ComboAgeCl+= '</select>' + "\n";
						
	// genero la combo delle classi buone
		var GoodCl = new Array();
		
		if (Arr_GoodCl!='#')
		{
			var Classes = Arr_GoodCl.split(',');
			for (k=0;k<Classes.length;++k)
			{
				GoodCl.push(Classes[k]);
			}				
		}
		
		var ComboCl 
			= '<select name="d_e_EnClass_' + Id + '" id="d_e_EnClass_' + Id + '" onBlur="javascript:UpdateClass(' + Id + ');">' + "\n"
			+ '<option value="">--</option>' + "\n";
			
		if (GoodCl.length>0)
		{
			for (k=0;k<GoodCl.length;++k)
			{
				ComboCl+= '<option value="' + GoodCl[k] + '"' + (GoodCl[k]==Arr_Cl ? ' selected' : '') + '>' + GoodCl[k] + '</option>' + "\n";
			}
		}
		
		ComboCl+= '</select>' + "\n";		
		
		document.getElementById('Col_AgeCl_' + Id).innerHTML=ComboAgeCl;
		document.getElementById('Col_Cl_' + Id).innerHTML=ComboCl;
		
		UpdateClass(Id);
	}
	else
	{
		SetStyle('d_e_EnCtrlCode_' + Id,'error');
	}
}

/*
	- SelectAgeClass(Id)
	Aggiorna la classe e la classe gara
	Id � l'id di riga
*/

function SelectAgeClass(Id)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				var MyId = encodeURIComponent(Id);
				var AgeClass = encodeURIComponent(document.getElementById('d_e_EnAgeClass_' + Id).value);
				
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("POST","SelectAgeClass.php?EnId=" + MyId + "&d_e_EnAgeClass=" + AgeClass);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="SelectAgeClass.php?EnId=" + MyId + "&d_e_EnAgeClass=" + AgeClass + '<br>';
					XMLHttp.onreadystatechange=SelectAgeClass_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
		}
	}
}

function SelectAgeClass_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectAgeClass_Response();
			}
			catch(e)
			{
				document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
			}
		}
		else
		{
			document.getElementById('idOutput').innerHTML+='Errore: ' +XMLHttp.statusText + '<br>';
		}
	}
}

function SelectAgeClass_Response()
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
	var Id=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	
	//alert(Error);
	if (Error==0)
	{
		SetStyle('d_e_EnAgeClass_' + Id,'');
		
		var Arr_GoodCl = XMLRoot.getElementsByTagName('classes');
		var Class=XMLRoot.getElementsByTagName('class').item(0).firstChild.data;
		
	// genero la combo delle classi buone
		var GoodCl = new Array();
		
		var Classes = Arr_GoodCl.item(0).firstChild.data.split(',');
		for (k=0;k<Classes.length;++k)
		{
			if (Classes[k]!='#')
				GoodCl.push(Classes[k]);
		}				
		
		var ComboCl 
			= '<select name="d_e_EnClass_' + Id + '" id="d_e_EnClass_' + Id + '" onBlur="javascript:UpdateClass(' + Id + ');">'
			+ '<option value="">--</option>' + "\n";
			
		if (GoodCl.length>0)
		{
			for (k=0;k<GoodCl.length;++k)
			{
				ComboCl+= '<option value="' + GoodCl[k] + '"' + (GoodCl[k]==Class ? ' selected' : '') + '>' + GoodCl[k] + '</option>' + "\n";
			}
		}
		
		ComboCl+= '</select>' + "\n";
		
		document.getElementById('Col_Cl_' + Id).innerHTML=ComboCl;
		
	}
	else
	{
		SetStyle('d_e_EnAgeClass_' + Id,'error');
	}
}

/*
	- UpdateClass(Id)
	Aggiorna la classe e la classe gara
*/

function UpdateClass(Id)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked && document.getElementById('d_e_EnCtrlCode_' + Id).className=='')
			{
				var MyId = encodeURIComponent(Id);
				var Class = encodeURIComponent(document.getElementById('d_e_EnClass_' + Id).value);
				var AgeClass = encodeURIComponent(document.getElementById('d_e_EnAgeClass_' + Id).value);
				
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("POST","UpdateClass.php?EnId=" + MyId + "&d_e_EnAgeClass=" + AgeClass + "&d_e_EnClass=" + Class);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="UpdateClass.php?EnId=" + MyId + "&d_e_EnAgeClass=" + AgeClass + "&d_e_EnClass=" + Class;
					XMLHttp.onreadystatechange=UpdateClass_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateClass_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateClass_Response();
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

function UpdateClass_Response()
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
	var Id=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
	//alert(Error);
	if (Error==1)
	{
		SetStyle('d_e_EnAgeClass_' + Id,'error');
		SetStyle('d_e_EnClass_' + Id,'error');
	}
	else
	{
		SetStyle('d_e_EnAgeClass_' + Id,'');
		SetStyle('d_e_EnClass_' + Id,'');
	}
}

/*
	- UpdateSession(Field)
	Invia la POST a UpdateSession.php il campo Field da aggiornare
*/
function UpdateSession(Field,forceValue)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				var FieldName = encodeURIComponent(Field);
				var FieldValue=0;
				if (forceValue)
				{
					FieldValue=forceValue;
				}
				else
				{
					FieldValue=encodeURIComponent(document.getElementById(Field).value);
				}
				//var FieldValue= encodeURIComponent(document.getElementById(Field).value);
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("POST","UpdateSession.php?" + FieldName + "=" + FieldValue,true);
					//document.getElementById('idOutput').innerHTML="UpdateSession.php?" + FieldName + "=" + FieldValue;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=UpdateSession_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function UpdateSession_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateSession_Response();
			}
			catch(e)
			{
				//alert('Errore: ' + e.toString());
			}
		}
		else
		{
			//alert('Errore: ' +XMLHttp.statusText);
		}
	}
}

function UpdateSession_Response()
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
		var Session = XMLRoot.getElementsByTagName('session').item(0).firstChild.data;
		
		var Troppi = XMLRoot.getElementsByTagName('troppi').item(0).firstChild.data;
		
		var Id = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
		if (Troppi==1)
		{
			document.getElementById('d_q_QuSession_' + Id).value=0;
			lastSession=0;
			var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;
			alert(Msg);
		}
		else
		{
			document.getElementById('d_q_QuSession_' + Id).value=Session;
			lastSession=Session;
		}
	}
	
	//alert(lastSession);
}

/*
	- AddRow()
	Esegue la  get a AddRow.php per aggiungere la nuova riga.
	Va agganciata al comando di Aggiunta di un nuovo partecipante
*/
function AddRow()
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					XMLHttp.open("GET","AddRow.php",true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddRow_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function AddRow_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				AddRow_Response();
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

function AddRow_Response()
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
	
	var Error=XMLRoot.getElementsByTagName('error')[0].firstChild.data;
	
	if (Error==1)
		return;
	
	var NewId=XMLRoot.getElementsByTagName('new_id')[0].firstChild.data;
	
	var ConfirmMsg1 = XMLRoot.getElementsByTagName('confirm_msg1').item(0).firstChild.data;
	var ConfirmMsg2 = XMLRoot.getElementsByTagName('confirm_msg2').item(0).firstChild.data;
	var ConfirmMsg3 = XMLRoot.getElementsByTagName('confirm_msg3').item(0).firstChild.data;
	var ConfirmMsg4 = XMLRoot.getElementsByTagName('confirm_msg4').item(0).firstChild.data;
/* 
	aggiungo la riga alla tabella
*/
	var tbody = document.getElementById('idAthList').getElementsByTagName("tbody").item(0);

// Nuova riga
	var NewRow = document.createElement("TR");

// Creo le colonne
	var Arr_Ses = XMLRoot.getElementsByTagName('sessions');
	ComboSes 
		= '<select name="d_q_QuSession_' + NewId + '" id="d_q_QuSession_' + NewId + '" onBlur="javascript:UpdateSession(\'d_q_QuSession_' + NewId + '\');">'
		+ '<option value="0">--</option>\n';
	for (i=0;i<Arr_Ses.length;++i)
	{
		ComboSes += '<option value="' + Arr_Ses.item(i).firstChild.data + '">' + Arr_Ses.item(i).firstChild.data + '</option>\n';
	}
	ComboSes += '</select>\n';	
	
	var TD_Session = document.createElement("TD");
	TD_Session.id='Col_Session_' + NewId;
	TD_Session.className='Center';
	TD_Session.innerHTML=ComboSes;
	
	var TD_TargetNo = document.createElement("TD");
	TD_TargetNo.className='Bold';
	TD_TargetNo.innerHTML='&nbsp;';
	
	var TD_Code = document.createElement("TD");
	TD_Code.id='Col_Code_' + NewId;
	TD_Code.className='Center';
	TD_Code.innerHTML='<input type="hidden" name="CanComplete_' + NewId + '" id="CanComplete_' + NewId + '" value="0"><input type="hidden" name="d_e_EnStatus_' + NewId + '" id="d_e_EnStatus_' + NewId + '" value="0"><input type="text" size="9" maxlength="9" name="d_e_EnCode_' + NewId + '" id="d_e_EnCode_' + NewId + '" value="" onFocus="javascript:SetCompleteFlag(' + NewId + ');" onKeyUp="javascript:CercaMatr(\'d_e_EnCode_' + NewId + '\',' + NewId + ');" onBlur="javascript:UpdateField(\'d_e_EnCode_' + NewId + '\'); SetCompleteFlag(' + NewId + ');">'
	
	var TD_FirstName = document.createElement("TD");
	TD_FirstName.id='Col_FirstName_' + NewId;
	TD_FirstName.className='Center';
	TD_FirstName.innerHTML
		= '<input type="text" size="25" maxlength="30" name="d_e_EnFirstName_' + NewId + '" id="d_e_EnFirstName_' + NewId + '" '
		+ 'value="" onBlur="javascript:UpdateField(\'d_e_EnFirstName_' + NewId + '\');">'
		//+ 'onDblClick="javascript:OpenPopup(\'FindArcher.php?Id=' + NewId + '\',\'FindArcher\',900,600);">';

	var TD_Name = document.createElement("TD");
	TD_Name.id='Col_Name_' + NewId;
	TD_Name.className='Center';
	TD_Name.innerHTML
		= '<input type="text" size="25" maxlength="30" name="d_e_EnName_' + NewId + '" id="d_e_EnName_' + NewId + '" '
		+ 'value="" onBlur="javascript:UpdateField(\'d_e_EnName_' + NewId + '\');">'
		//+ 'onDblClick="javascript:OpenPopup(\'FindArcher.php?Id=' + NewId + '\',\'FindArcher\',900,600);">';
	/*var TD_Sex = document.createElement("TD");
	TD_Sex.className='Center';
	TD_Sex.innerHTML
		= '<select name="d_e_EnSex_' + NewId + '" id="d_e_EnSex_' + NewId + '" onBlur="javascript:UpdateField(\'d_e_EnSex_' + NewId + '\');">\n'
		+ '<option value="0">M</option>\n'
		+ '<option value="1">F</option>\n'
		+ '</select>\n';*/
		
	var TD_CtrlCode = document.createElement("TD");
	TD_CtrlCode.id='Col_CtrlCode_' + NewId;
	TD_CtrlCode.className='Center';
	TD_CtrlCode.innerHTML='<input type="text" size="20" maxlength="16" name="d_e_EnCtrlCode_' + NewId + '" id="d_e_EnCtrlCode_' + NewId + '" value="" onBlur="javascript:UpdateCtrlCode(' + NewId + ');">'
	
	var ComboSex
		= '<select name="d_e_EnSex_' + NewId + '" id="d_e_EnSex_' + NewId + '" onChange="javascript:UpdateCtrlCode(' + NewId + ')">' + "\n"
			+ '<option value="0">' + StrShortMale + '</option>' + "\n"
			+ '<option value="1">' + StrShortFemale + '</option>' + "\n"
		+ '</select>';
	
	var TD_Sex = document.createElement("TD");
	TD_Sex.id='Col_CtrlCode_' + NewId;
	TD_Sex.className='Center';
	TD_Sex.innerHTML=ComboSex;
	
	var TD_CountryCode = document.createElement("TD");
	TD_CountryCode.id='Col_CountryCode_' + NewId;
	TD_CountryCode.className='Center';
	TD_CountryCode.innerHTML
		= '<input type="hidden" name="d_e_EnCountry_' + NewId + '" id="d_e_EnCountry_' + NewId + '" value="0">'
		+ '<input type="text" size="9" maxlength="9" name="d_c_CoCode_' + NewId + '" id="d_c_CoCode_' + NewId + '" value=""  onBlur="javascript:UpdateCountryCode(' + NewId + ');" onKeyUp="javascript:SelectCountryCode(' + NewId  + ');">';
		
	var TD_CountryName = document.createElement("TD");
	TD_CountryName.id='Col_CountryName_' + NewId;
	TD_CountryName.className='Center';
	TD_CountryName.innerHTML='<input type="text" size="30" maxlength="30" name="d_c_CoName_' + NewId + '" id="d_c_CoName_' + NewId + '" value="" onBlur="javascript:UpdateCountryName(' + NewId + ');">'

	var Arr_Div = XMLRoot.getElementsByTagName('divisions');
	var Arr_Cl = XMLRoot.getElementsByTagName('classes');
	var Arr_SubCl = XMLRoot.getElementsByTagName('sub_classes');
	
	ComboDiv 
		= '<select name="d_e_EnDivision_' + NewId + '" id="d_e_EnDivision_' + NewId + '" onBlur="javascript:UpdateField(\'d_e_EnDivision_' + NewId + '\');">\n'
		+ '<option value="">--</option>\n';
	for (i=0;i<Arr_Div.length;++i)
	{
		ComboDiv += '<option value="' + Arr_Div.item(i).firstChild.data + '">' + Arr_Div.item(i).firstChild.data + '</option>\n';
	}
	ComboDiv += '</select>\n';
	
	ComboCl
		= '<select name="d_e_EnClass_' + NewId + '" id="d_e_EnClass_' + NewId + '" onBlur="javascript:UpdateClass(' + NewId + ');">\n'
		+ '<option value="">--</option>\n';
	ComboCl += '</select>\n';
	
	ComboSubCl
		= '<select name="d_e_EnSubClass_' + NewId + '" id="d_e_EnSubClass_' + NewId + '" onBlur="javascript:UpdateField(\'d_e_EnSubClass_' + NewId + '\');">\n'
		+ '<option value="">--</option>\n';
	for (i=0;i<Arr_SubCl.length;++i)
	{
		ComboSubCl += '<option value="' + Arr_SubCl.item(i).firstChild.data + '">' + Arr_SubCl.item(i).firstChild.data + '</option>\n';
	}
	ComboSubCl += '</select>\n';
	
	ComboAgeCl
		= '<select name="d_e_EnAgeClass_' + NewId + '" id="d_e_EnAgeClass_' + NewId + '" onChange="javascript:SelectAgeClass(' + NewId + ');" onBlur="javascript:UpdateClass(' + NewId + ');">\n'
		+ '<option value="">--</option>\n';
	for (i=0;i<Arr_Cl.length;++i)
	{
		ComboAgeCl += '<option value="' + Arr_Cl.item(i).firstChild.data + '">' + Arr_Cl.item(i).firstChild.data + '</option>\n';
	}
	ComboAgeCl += '</select>\n';
	
	var TD_Division = document.createElement("TD");
	TD_Division.id='Col_Div_' + NewId;
	TD_Division.className='Center';
	TD_Division.innerHTML = ComboDiv;
	
	var TD_AgeClass = document.createElement("TD");
	TD_AgeClass.id='Col_AgeCl_' + NewId;
	TD_AgeClass.className='Center';
	TD_AgeClass.innerHTML = ComboAgeCl;
	
	var TD_Class = document.createElement("TD");
	TD_Class.id='Col_Cl_' + NewId;
	TD_Class.className='Center';
	TD_Class.innerHTML = ComboCl;
	
	var TD_SubClass = document.createElement("TD");
	TD_SubClass.id='Col_SubCl_' + NewId;
	TD_SubClass.className='Center';
	TD_SubClass.innerHTML = ComboSubCl;
	
	var TD_Command = document.createElement("TD");
	TD_Command.id='Col_Command_' + NewId;
	TD_Command.className='Center';
	TD_Command.innerHTML = '<a class="Link" href="javascript:DeleteRow(' + NewId + ',\'' + ConfirmMsg1 + '\',\'' + ConfirmMsg2 + '\',\'' + ConfirmMsg3 + '\',\'' + ConfirmMsg4 + '\');"><img border="0" src="../Common/Images/drop.png" alt="" title=""></a>';
	
// Aggiungo le colonne alla riga
	NewRow.id='Row_' + NewId;
	NewRow.ondblclick = DblClickOnTextBox;
		
	
	NewRow.appendChild(TD_Session);
	NewRow.appendChild(TD_TargetNo);
	NewRow.appendChild(TD_Code);
	NewRow.appendChild(TD_FirstName);
	NewRow.appendChild(TD_Name);
	NewRow.appendChild(TD_CtrlCode);
	NewRow.appendChild(TD_Sex);
	NewRow.appendChild(TD_CountryCode);
	NewRow.appendChild(TD_CountryName);
	NewRow.appendChild(TD_Division);
	NewRow.appendChild(TD_AgeClass);
	NewRow.appendChild(TD_Class);
	NewRow.appendChild(TD_SubClass);
	NewRow.appendChild(TD_Command);
	
// Aggiungo alla tabella la nuova riga
	tbody.appendChild(NewRow);
	
	XMLHttp = CreateXMLHttpRequestObject();
	UpdateSession('d_q_QuSession_'+NewId,lastSession);
}

/*
	- DeleteRow(Id,Msg1,Msg2,Msg3,Msg4).
	Esegue la GET a DeleteRow.php
	Id � l'id del tizio da eliminare
	Msg1,2,3,4 sono i messaggi nel testo di conferma
*/
function DeleteRow(Id,Msg1,Msg2,Msg3,Msg4)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave') || !document.getElementById('chk_BlockAutoSave').checked)
			{
				var GetConfirm=false;
				var Elimina=true;
				
				if (document.getElementById('d_e_EnFirstName_' + Id).value.length>0)
					GetConfirm=true;
					
				if (GetConfirm)
				{
					var StrMsg //book
						= Msg1 + ': ' + document.getElementById('d_e_EnFirstName_' + Id).value + '  ' + document.getElementById('d_e_EnName_' + Id).value + "\n"
						+ Msg2 + ': ' + document.getElementById('d_c_CoCode_' + Id).value + "\n"
						+ Msg3 + "\n"
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
	- AddManyRows(Num,Warning,Msg))
	Esegue get a AddManyRows.php?Num=
	Aggiunge Num righe
	Se Warning è 1 chiede conferma usando Msg come messaggio
*/
function AddManyRows(Num,Warning,Msg)
{
	if (XMLHttp)
	{
		try
		{
			if (!document.getElementById('chk_BlockAutoSave').checked)
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var Fai=true;
					
					if (Warning==1)
						Fai=confirm(Msg.replace(/\+/g," "));
								
					if (Fai)
					{
						XMLHttp.open("GET","AddManyRows.php?Num=" + Num,true);
						//document.getElementById('idOutput').innerHTML="AddManyRows.php?Num=" + Num;
						XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
						XMLHttp.onreadystatechange=AddManyRows_StateChange;
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

function AddManyRows_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{	
				AddManyRows_Response();
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

function AddManyRows_Response()
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
		window.location.reload();
	}
}

function GetClassesByGender(Id)
{
	//return;
	var XMLHttp2=CreateXMLHttpRequestObject();
	if (XMLHttp2)
	{
	//	console.debug('ui');
		try
		{	
			var sex = encodeURIComponent(document.getElementById('d_e_EnSex_' + Id).value);
	
			if ((XMLHttp2.readyState==XHS_COMPLETE || XMLHttp2.readyState==XHS_UNINIT) )
			{
				XMLHttp2.open("GET","GetClassesByGender.php?sex=" + sex ,false);
				XMLHttp2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				
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
					var combo=document.getElementById("d_e_EnAgeClass_" + Id);
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