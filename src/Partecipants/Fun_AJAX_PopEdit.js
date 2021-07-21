var CacheMatr = new Array();	// cache per la ricerca sulla matricola

/*
	le seguenti var valgono true se il campo corrispettivo contiene un errore.
	Solo se sono tutte a false sarà possibile salvare
*/
var CtrlCode_Error = false;
var TargetNo_Error = false;
var SubTeam_Error=false;

var GetOldsFromFinder=false;

function SetCompleteFlag()
{
	if (document.getElementById('d_e_EnFirstName_').value.length==0 &&
		document.getElementById('d_e_EnName_').value.length==0 &&
		document.getElementById('d_e_EnCtrlCode_').value.length==0 &&
		document.getElementById('d_c_CoCode_').value.length==0 &&
		document.getElementById('d_c_CoName_').value.length==0)
	{
		document.getElementById('CanComplete_').value=1;
	}
	else
	{
		document.getElementById('CanComplete_').value=0;
	}

}


function loadRecord()
{
	document.getElementById('d_e_EnId_').value = record['id'];
	document.getElementById('d_e_EnSex_').value = record['sex_id'];
	document.getElementById('d_q_QuSession_').value = record['session'];
	document.getElementById('d_q_QuTargetNo_').value = record['targetno'];
	document.getElementById('d_e_EnCode_').value = record['code'];
	document.getElementById('d_e_EnFirstName_').value = record['firstname'];
	document.getElementById('d_e_EnName_').value = record['name'];
	document.getElementById('d_e_EnCtrlCode_').value = record['dob'];
	document.getElementById('d_e_EnSex_').value = record['sex_id'];
	document.getElementById('d_e_EnCountry_').value = record['country_id'];
	document.getElementById('d_c_CoCode_').value = record['country_code'];
	document.getElementById('d_c_CoName_').value = record['country_name'];

	document.getElementById('d_e_EnSubTeam_').value = record['sub_team'];
	document.getElementById('d_e_EnCountry2_').value = record['country_id2'];
	document.getElementById('d_c_CoCode2_').value = record['country_code2'];
	document.getElementById('d_c_CoName2_').value = record['country_name2'];

	document.getElementById('d_e_EnCountry3_').value = record['country_id3'];
	document.getElementById('d_c_CoCode3_').value = record['country_code3'];
	document.getElementById('d_c_CoName3_').value = record['country_name3'];

	document.getElementById('d_e_EnDivision_').value = record['division'];
	document.getElementById('d_e_EnAgeClass_').value = record['ageclass'];
	document.getElementById('d_e_EnClass_').value = record['class'];
	document.getElementById('d_e_EnSubClass_').value = record['subclass'];
	document.getElementById('d_e_EnStatus_').value=record['status'];
	//document.getElementById('d_e_EnTargetFace_').value=record['targetface'];

	document.getElementById('d_e_EnIndClEvent_').value = record['indcl'];
	document.getElementById('d_e_EnTeamClEvent_').value = record['teamcl'];
	document.getElementById('d_e_EnIndFEvent_').value = record['indfin'];
	document.getElementById('d_e_EnTeamFEvent_').value = record['teamfin'];
	document.getElementById('d_e_EnTeamMixEvent_').value=record['mixteamfin'];
	document.getElementById('d_e_EnWChair_').value=record['wc'];
	document.getElementById('d_e_EnDoubleSpace_').value=record['double'];
	document.getElementById('d_ed_EdEmail_').value=record['email'];


	CheckCtrlCode();
	manageIndTeamParticipation('1');
}

function CheckCtrlCode(obj) {
	var d_e_EnCtrlCode = document.getElementById('d_e_EnCtrlCode_').value;
	var d_e_EnSex = document.getElementById('d_e_EnSex_').value;
	var d_e_EnDiv = document.getElementById('d_e_EnDivision_').value;
	var d_e_EnAge = document.getElementById('d_e_EnAgeClass_').value;

	$.getJSON("CheckCtrlCode.php?d_e_EnAgeClass=" + d_e_EnAge + "&d_e_EnCtrlCode=" + d_e_EnCtrlCode + '&d_e_EnSex=' + d_e_EnSex + '&d_e_EnDiv=' + d_e_EnDiv, function(data) {
		if(data.error==0) {
			CtrlCode_Error=false;

			if(data.dob!='') {
				$('#d_e_EnCtrlCode_').val(data.dob);
				SetStyle('d_e_EnCtrlCode_','');
			}

			// Gestisco le tendine delle classi
			var Divisions = data.div;
			var AgeClass = data.age;
			var Classes = data.clas;

			var oldDiv='';
			var oldCl='';
			var oldAgeCl='';

			if (!GetOldsFromFinder) {
				oldDiv=document.getElementById('d_e_EnDivision_').value;
				oldCl=document.getElementById('d_e_EnClass_').value;
				oldAgeCl=document.getElementById('d_e_EnAgeClass_').value;
			} else {
				var matr=document.getElementById('d_e_EnCode_').value;

				if (matr!='')
				{
					var ioc=document.getElementById('LupSelect').value;

					var ref=matr+'_'+ioc;

					if (document.getElementById('fdiv_'+ref))
					{
						oldDiv=document.getElementById('fdiv_'+ref).value;
					}

					if (document.getElementById('fcl_'+ref))
					{
						oldCl=document.getElementById('fcl_'+ref).value;
						oldAgeCl=oldCl;
					}
				}

				GetOldsFromFinder=false;
			}

			var Id = document.getElementById('d_e_EnId_').value;

			// rebuild menus
			rebuildDivClass('d_e_EnDivision_', Divisions, oldDiv);
			rebuildDivClass('d_e_EnClass_', Classes, oldCl);
			rebuildDivClass('d_e_EnAgeClass_', AgeClass, (oldAgeCl ? oldAgeCl : oldCl));
			if (document.getElementById('fscl_'+ref)) {
				document.getElementById('d_e_EnSubClass_').value = document.getElementById('fscl_' + ref).value;
			}
			CheckTargetFaces();
		} else {
			SetStyle('d_e_EnCtrlCode_','error');
			CtrlCode_Error=true;
		}
	});
}

var XMLHttpAgeClass = CreateXMLHttpRequestObject();
function SelectAgeClass()
{
	if (XMLHttpAgeClass)
	{
		try
		{
			var AgeClass = document.getElementById('d_e_EnAgeClass_').value;
			var Division = document.getElementById('d_e_EnDivision_').value;

		// non capisco perchè se c'è -- mi mette vuoto
			if (AgeClass=='')
				AgeClass='--';

			if (XMLHttpAgeClass.readyState==XHS_COMPLETE || XMLHttpAgeClass.readyState==XHS_UNINIT)
			{
				XMLHttpAgeClass.open("GET","SelectAgeClass.php?d_e_EnAgeClass=" + AgeClass+'&d_e_EnDivision=' + Division+'&NoCheckEntry=',true);
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
	// se lo stato è Complete vado avanti
	if (XMLHttpAgeClass.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttpAgeClass.status==200)
		{
			try
			{
				SelectAgeClass_Response();
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

function SelectAgeClass_Response()
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
		var MyClass = XMLRoot.getElementsByTagName('class').item(0).firstChild.data;

		rebuildDivClass('d_e_EnClass_', Classes, MyClass);

		var athlete=XMLRoot.getElementsByTagName('athlete').item(0).firstChild.data;
		manageIndTeamParticipation(athlete);
	}
	else
	{
		SetStyle('d_e_EnAgeClass_','error');
	}
	CheckTargetFaces();
}

function GetClassesByGender()
{
	var XMLHttp2=CreateXMLHttpRequestObject();
	if (XMLHttp2)
	{
	//	console.debug('ui');
		try
		{
			var sex = encodeURIComponent(document.getElementById('d_e_EnSex_').value);
			var div = encodeURIComponent(document.getElementById('d_e_EnDivision_').value);
			var age = encodeURIComponent(document.getElementById('d_e_EnCtrlCode_').value);

			if ((XMLHttp2.readyState==XHS_COMPLETE || XMLHttp2.readyState==XHS_UNINIT) )
			{
				XMLHttp2.open("GET","GetClassesByGender.php?sex=" + sex + '&div=' + div + '&age=' + age, false);
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
					var Classes=XMLRoot.getElementsByTagName('class');

					// destroy Menus for AgeClass and Class
					rebuildDivClass('d_e_EnClass_', Classes, '');
					//rebuildDivClass('d_e_EnAgeClass_', AgeClass, oldAgeCl);

					var athlete=XMLRoot.getElementsByTagName('athlete').item(0).firstChild.data;
					manageIndTeamParticipation(athlete);
				}
			}
		}
		catch (e)
		{
			//alert(e.toString());
		}
	}
}

function SelectCountryCode(which)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var Code = encodeURIComponent(document.getElementById('d_c_CoCode' + which + '_').value);
				XMLHttp.open("GET","SelectCountryCode.php?Code=" + Code + '&which='+which);
				//document.getElementById('idOutput').innerHTML="SelectCountryCode.php?IdEntry=" + IdEntry + "&Code=" + Code;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SelectCountryCode_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//alert('Errore: ' + e.toString());
		}
	}
}

function SelectCountryCode_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectCountryCode_Response();
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
	var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
	//alert(Error);

	var Name=XMLRoot.getElementsByTagName('name').item(0).firstChild.data;
	var Id=XMLRoot.getElementsByTagName('id').item(0).firstChild.data;

	if (Error==0)
	{
		document.getElementById('d_c_CoName' + Which + '_').value=Name;
		document.getElementById('d_e_EnCountry' + Which + '_').value=Id;
		SetStyle('d_c_CoCode' + Which + '_','');
		SetStyle('d_c_CoName' + Which + '_','');
	}
	else
	{
		SetStyle('d_c_CoCode' + Which + '_','error');
		SetStyle('d_c_CoName' + Which + '_','error');
	}

}

XMLHttp2 = CreateXMLHttpRequestObject();
function CercaMatr(add2Cache)
{
/*
	ricreo l'oggetto XMLHttp perchè se si apre il popup del cerca va tutto a ramengo ;(
*/


	if (XMLHttp2)
	{
//		if(document.getElementById('d_e_EnCode_').value=='') return;

		if (add2Cache)
		{
			var Matr = encodeURIComponent(document.getElementById('d_e_EnCode_').value);
			var LupCode = encodeURIComponent(document.getElementById('LupSelect').value);
			CacheMatr.push("Matr=" + Matr + "&Noc=" + LupCode);
		}


		try
		{
			if (document.getElementById('CanComplete_').value==1)
			{
				if ((XMLHttp2.readyState==XHS_COMPLETE || XMLHttp2.readyState==XHS_UNINIT) && CacheMatr.length>0)
				{
					var FromCache = CacheMatr.shift();
					XMLHttp2.open("POST","Matr_FindOnEdit.php",true);
					XMLHttp2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="Matr_FindOnEdit_Par.php?Matr="  + Matr;
					XMLHttp2.onreadystatechange=CercaMatr_StateChange;
					XMLHttp2.send(FromCache);
				}
			}
			else
				CacheMatr.shift();
		}
		catch (e)
		{
			//alert('Errore1: ' + e.toString());
		}
	}
}

function CercaMatr_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp2.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp2.status==200)
		{
			try
			{
				CercaMatr_Response();
			}
			catch(e)
			{
				//alert('Errore2: ' + e.toString());
			}
		}
		else
		{
			//alert('Errore3: ' +XMLHttp.statusText);
		}
	}
}

function CercaMatr_Response()
{
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
	/*
	 * questo mi serve per avere sempre le tendine div, cl e agecl piene
	 * in modo che poi le funz di chris che ricalcano i valori partano sempre
	 * dallo stesso insieme di dati e anche perchè così qui quando avviene l'impostazione
	 * di questi valori nelle tendine, sicuramente sono presenti.
	 *
	 */
		resetDivCl();

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
		var CountryId2 = XMLRoot.getElementsByTagName('idcountry2').item(0).firstChild.data;
		var CountryCode2 = XMLRoot.getElementsByTagName('country2').item(0).firstChild.data;
		var CountryName2 = XMLRoot.getElementsByTagName('nation2').item(0).firstChild.data;
		var Division = XMLRoot.getElementsByTagName('division').item(0).firstChild.data;
		var Class = XMLRoot.getElementsByTagName('class').item(0).firstChild.data;
		var SubClass = XMLRoot.getElementsByTagName('subclass').item(0).firstChild.data;
		var AgeClass = XMLRoot.getElementsByTagName('ageclass').item(0).firstChild.data;


		document.getElementById('d_e_EnName_').value=Name;
		document.getElementById('d_e_EnFirstName_').value=FirstName;
		document.getElementById('d_e_EnSex_').value=Sex;
		//document.getElementById('d_e_EnCtrlCode_').value=CtrlCode;
		document.getElementById('d_e_EnCtrlCode_').value=Dob;
		document.getElementById('d_e_EnCountry_' ).value=CountryId;
		document.getElementById('d_c_CoCode_' ).value=CountryCode;
		document.getElementById('d_c_CoName_').value=CountryName;
		document.getElementById('d_e_EnDivision_').value=Division;

		document.getElementById('d_c_CoCode2_' ).value=CountryCode2;
		document.getElementById('d_c_CoName2_').value=CountryName2;

		document.getElementById('d_e_EnAgeClass_').value=AgeClass;
		document.getElementById('d_e_EnClass_').value=Class;

		document.getElementById('d_e_EnSubClass_').value=SubClass;
		document.getElementById('d_e_EnStatus_').value=Status;

		CheckCtrlCode();
		CheckTargetFaces();
		manageIndTeamParticipation('1');
	}

	//GetStatus(Id);
	// per scaricare la cache delle ricerche
	if (CacheMatr.length>0)
		setTimeout("CercaMatr(false)",500);
}

function CheckTargetNo()
{
	if (XMLHttp)
	{
		try
		{
			var d_q_QuSession = encodeURIComponent(document.getElementById('d_q_QuSession_').value);
			var d_q_QuTargetNo = encodeURIComponent(document.getElementById('d_q_QuTargetNo_').value);

			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) )
			{
				XMLHttp.open("GET","CheckTargetNo.php?d_q_QuSession=" + d_q_QuSession + "&d_q_QuTargetNo=" + d_q_QuTargetNo,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="CheckTargetNo_Par.php?d_q_QuSession=" + d_q_QuSession + "&d_q_QuTargetNo=" + d_q_QuTargetNo;
				XMLHttp.onreadystatechange=CheckTargetNo_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function CheckTargetNo_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				CheckTargetNo_Response();
			}
			catch(e)
			{
				//alert('Errore2: ' + e.toString());
			}
		}
		else
		{
			//alert('Errore3: ' +XMLHttp.statusText);
		}
	}
}

function CheckTargetNo_Response()
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
		document.getElementById('d_q_QuTargetNo_').value=(TargetNo=='000' || TargetNo=='' ? '' : TargetNo);
	}
}

function SelectSession()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var Session = document.getElementById('d_q_QuSession_').value;
				var Id = document.getElementById('d_e_EnId_').value;
				XMLHttp.open("GET","CheckSession.php?Session=" + Session + '&Id=' + Id);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="CheckSession_Par.php?Session=" + Session + '&Id=' + Id;
				XMLHttp.onreadystatechange=SelectSession_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
			//document.getElementById('idOutput').innerHTML+='Errore: ' + e.toString() + '<br>';
		}
	}

}

function SelectSession_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SelectSession_Response();
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

function SelectSession_Response()
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

function CheckSubTeam()
{
	var v=document.getElementById('d_e_EnSubTeam_').value;

	SubTeam_Error=false;

	if (!v.match(/[0-9]+/))
	{
		SubTeam_Error=true;
	}
	else
	{
		if (v<0)
		{
			SubTeam_Error=true;
		}
	}

	if (SubTeam_Error)
	{
		SetStyle('d_e_EnSubTeam_','error');
	}
	else
	{
		SetStyle('d_e_EnSubTeam_','');
	}
}

function CheckTargetFaces()
{
	var Div=document.getElementById('d_e_EnDivision_').value;
	var Clas=document.getElementById('d_e_EnClass_').value;
	var Tf=document.getElementById('d_e_EnTargetFace_');

	// resets the Tf Select
	while(Tf.length>1) Tf.remove(1);

	if (Div!='' && Clas!='')
	{
		if(TargetFaces[Div]!==undefined && TargetFaces[Div][Clas]) {
			var Id = document.getElementById('d_e_EnId_').value;
			var oldValue= (Id!=0 ? record['targetface'] : '');
			var chkValue=false;
			for(n in TargetFaces[Div][Clas]) {
				var opt = document.createElement('option');
				opt.text=TargetFaces[Div][Clas][n];
				opt.value=n.substring(1);
				if(n==oldValue) {
					opt.selected=true;
					chkValue=true;
				}

				try
				{
					Tf.add(opt,null); // standard
				}
				catch(ex)
				{
					Tf.add(opt); // IE di ....
				}
			}


			if(!chkValue) {
				document.getElementById('d_e_EnTargetFace_').selectedIndex = 1;
			}

			if (Id!=0)
			{
				document.getElementById('d_e_EnTargetFace_').value=record['targetface'];
			}
		}
	}
}

function FindArchers()
{
	if (XMLHttp)
	{
		try
		{
			var res=document.getElementById('results');
			res.innerHTML='';

			var qs
				= "?findCode=" + document.getElementById('findCode').value
				+ "&findAth=" + document.getElementById('findAth').value
				+ "&findIocCode=" + document.getElementById('LupSelect').value
				+ "&findCountry=" + document.getElementById('findCountry').value
				+ "&findDiv=" + document.getElementById('findDiv').value
				+ "&findCl=" + document.getElementById('findCl').value
				+ "&findSubCl=" + document.getElementById('findSubCl').value;

			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) )
			{

				XMLHttp.open("GET","HtmlFindArchers.php"+qs,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="CheckCtrlCode_Par.php?d_e_EnCtrlCode=" + d_e_EnCtrlCode;
				XMLHttp.onreadystatechange=FindArchers_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{

			//alert('Errore1: ' + e.toString());
		}
	}
}

function FindArchers_StateChange()
{
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				FindArchers_Response();
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

function FindArchers_Response()
{
	// leggo l'xml
	var html=XMLHttp.responseText;

	var res=document.getElementById('results');
	res.innerHTML=html;
	var ids=getElementsByClassName('btn');
	for (var i=0;i<ids.length;++i) {
		ids[i].href="javascript: GetOldsFromFinder=true; " +
			"document.getElementById('CanComplete_').value=1;" +
			"document.getElementById('d_e_EnCode_').value='"+ids[i].id+"';" +
            "document.getElementById('fdiv_"+ids[i].id+"_"+ids[i].name+"').value='"+ids[i].getAttribute('ianseoDiv')+"';"+
            "document.getElementById('fscl_"+ids[i].id+"_"+ids[i].name+"').value='"+ids[i].getAttribute('ianseoSCl')+"';"+
			"selectIocCode('"+ids[i].name+"'); " +
			"CercaMatr(true);";
	}
}

function selectIocCode(code)
{
	myCombo = document.getElementById('LupSelect').options;
	for(i=0;i<myCombo.length;i++)
	{
		if(myCombo[i].value==code)
			myCombo[i].selected=true;
	}
}

function manageIndTeamParticipation(doEnable)
{
	var iQ = document.getElementById("d_e_EnIndClEvent_").options[1].selected;
	var tQ = document.getElementById("d_e_EnTeamClEvent_").options[1].selected;
	var iF = document.getElementById("d_e_EnIndFEvent_").options[1].selected;
	var tF = document.getElementById("d_e_EnTeamFEvent_").options[1].selected;
	var tM = document.getElementById("d_e_EnTeamMixEvent_").options[1].selected;
	if(doEnable=="1" && !(iQ || tQ || iF || tF || tM))
	{
		document.getElementById("d_e_EnIndClEvent_").options[1].selected=true;
		document.getElementById("d_e_EnTeamClEvent_").options[1].selected=true;
		document.getElementById("d_e_EnIndFEvent_").options[1].selected=true;
		document.getElementById("d_e_EnTeamFEvent_").options[1].selected=true;
		document.getElementById("d_e_EnTeamMixEvent_").options[1].selected=true;
	}
	else if(doEnable=="0" && (iQ && tQ && iF && tF && tM))
	{
		document.getElementById("d_e_EnIndClEvent_").options[0].selected=true;
		document.getElementById("d_e_EnTeamClEvent_").options[0].selected=true;
		document.getElementById("d_e_EnIndFEvent_").options[0].selected=true;
		document.getElementById("d_e_EnTeamFEvent_").options[0].selected=true;
		document.getElementById("d_e_EnTeamMixEvent_").options[0].selected=true;
	}
}

function resetDivCl()
{
	var selectDiv=document.getElementById('d_e_EnDivision_');
	var selectCl=document.getElementById('d_e_EnClass_');
	var selectAgeCl=document.getElementById('d_e_EnAgeClass_');

// secco
	for (var i=selectDiv.length-1;i>=0;i--)
		selectDiv.remove(i);

	for (var i=selectCl.length-1;i>=0;i--)
		selectCl.remove(i);

	for (var i=selectAgeCl.length-1;i>=0;i--)
		selectAgeCl.remove(i);

	var opt = document.createElement('option');
	opt.text='--';
	opt.value='--';
	try
	{
		selectDiv.add(opt,null); // standard
	}
	catch(ex)
	{
		selectDiv.add(opt); // IE di ....
	}

	var opt = document.createElement('option');
	opt.text='--';
	opt.value='--';
	try
	{
		selectCl.add(opt,null); // standard
	}
	catch(ex)
	{
		selectCl.add(opt); // IE di ....
	}

	var opt = document.createElement('option');
	opt.text='--';
	opt.value='--';
	try
	{
		selectAgeCl.add(opt,null); // standard
	}
	catch(ex)
	{
		selectAgeCl.add(opt); // IE di ....
	}

// ricarico tutto temporaneamente
	for (var i=0;i<allDivs.length;++i)
	{
		var opt = document.createElement('option');
		opt.text=allDivs[i];
		opt.value=allDivs[i];
		try
		{
			selectDiv.add(opt,null); // standard
		}
		catch(ex)
		{
			selectDiv.add(opt); // IE di ....
		}
	}

	for (var i=0;i<allCls.length;++i)
	{
		var opt = document.createElement('option');
		opt.text=allCls[i];
		opt.value=allCls[i];
		try
		{
			selectCl.add(opt,null); // standard
		}
		catch(ex)
		{
			selectCl.add(opt); // IE di ....
		}
	}

	for (var i=0;i<allAgeCls.length;++i)
	{
		var opt = document.createElement('option');
		opt.text=allAgeCls[i];
		opt.value=allAgeCls[i];
		try
		{
			selectAgeCl.add(opt,null); // standard
		}
		catch(ex)
		{
			selectAgeCl.add(opt); // IE di ....
		}
	}
}


function rebuildDivClass(Id, Specs, oldSpec) {
	$('#'+Id).empty();
	$('#'+Id).append('<option value="">--</option>');

	$(Specs).each(function() {
		$('#'+Id).append('<option value="'+this+'">'+this+'</option>');
	});

	if(Specs.length==1) {
		oldSpec=Specs[0];
	}
	$('#'+Id).val(oldSpec);
}
