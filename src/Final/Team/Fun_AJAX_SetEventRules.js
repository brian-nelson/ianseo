/*
													- Fun_AJAX_SetEventRules.js -
	Contiene le funzioni ajax usate da SetEventRules.php
*/
var Cache = new Array();	// cache per l'update
/*
	Invia la get a AddEventRule.php
	per aggiungere una coppia DivClass
	Event è l'evento a cui aggiungere la coppia
*/
function AddEventRule(Event)
{
	if (XMLHttp) {
		try {
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
				var Num=document.getElementById('New_EcNumber').value;
				var OptDiv=document.getElementById('New_EcDivision').options;
				var OptCl=document.getElementById('New_EcClass').options;
                var OptSubCl=document.getElementById('New_EcSubClass').options;

				if (OptDiv.selectedIndex>=0 && OptCl.selectedIndex>=0 && Num.search(/[1-9]{1}[0-9]{0,2}/)!=-1 && (document.getElementById('New_EcSubClass').disabled || OptSubCl.selectedIndex>=0)) {
					var QueryString = 'EvCode=' + Event + '&EcNumber=' + Num;

					for (i=0;i<OptDiv.length;++i) {
                        if (OptDiv[i].selected) {
                            QueryString += '&New_EcDivision[]=' + OptDiv[i].value;
                        }
                    }

					for (i=0;i<OptCl.length;++i) {
                        if (OptCl[i].selected) {
                            QueryString += '&New_EcClass[]=' + OptCl[i].value;
                        }
                    }

                    if(document.getElementById('New_EcSubClass').disabled) {
                        QueryString += '&New_EcSubClass[]=';
                    } else {
                        for (i = 0; i < OptSubCl.length; ++i) {
                            if (OptSubCl[i].selected) {
                                QueryString += '&New_EcSubClass[]=' + OptSubCl[i].value;
                            }
                        }
                    }

					XMLHttp.open("GET","AddEventRule.php?" + QueryString,true);
// 					document.getElementById('idOutput').innerHTML="AddEventRule.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddEventRule_StateChange;
					XMLHttp.send(null);
				}
			}
		} catch (e) {
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function AddEventRule_StateChange() {
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE) {
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200) {
			try {
				AddEventRule_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		} else {
 			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function AddEventRule_Response() {
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

	if (Error==0) {
		var EvCode = XMLRoot.getElementsByTagName('evcode').item(0).firstChild.data;
		var New_Group = XMLRoot.getElementsByTagName('new_group').item(0).firstChild.data;
		var New_EcNumber = XMLRoot.getElementsByTagName('new_number').item(0).firstChild.data;
		var Arr_Rules = XMLRoot.getElementsByTagName('new_rule');
		var ConfirmMsg = XMLRoot.getElementsByTagName('confirm_msg').item(0).firstChild.data;
		var tbody=document.getElementById('tbody');

	/*
		aggiungo le nuove regole
	*/

	// divider
		if (New_Group!=1) {
			var NewRow = document.createElement('tr');
			NewRow.id='Div_' + EvCode + '_' + New_Group;

			var TD_Divider = document.createElement('td');
			TD_Divider.colSpan=6;

			NewRow.appendChild(TD_Divider);
			tbody.appendChild(NewRow);
		}

		for (i=0;i<Arr_Rules.length;++i) {
			var Div='';
			var Cl='';
			var DivCl=Arr_Rules.item(i).firstChild.data.split('|')

			NewDiv=DivCl[0];
			NewCl=DivCl[1];
            NewSubCl=DivCl[2];

			var NewRow = document.createElement('TR');
			NewRow.id='Row_' + EvCode + '_' + New_Group + '_' + NewDiv + '_' + NewCl + '_' + NewSubCl;
// 			document.getElementById('idOutput').innerHTML+=NewRow.id + '<br>';

			if (i==0) {
				var TD_Number = document.createElement('TD');
				TD_Number.rowSpan=Arr_Rules.length;
				TD_Number.className='Center';
				TD_Number.innerHTML=New_EcNumber;

				NewRow.appendChild(TD_Number);
			}

			var TD_Div = document.createElement('TD');
			TD_Div.className='Center';
			TD_Div.innerHTML=NewDiv;

			var TD_Cl = document.createElement('TD');
			TD_Cl.className='Center';
			TD_Cl.innerHTML=NewCl;

            var TD_SubCl = document.createElement('TD');
            TD_SubCl.className='Center';
            TD_SubCl.innerHTML=NewSubCl;

			var TD_DelCl = document.createElement('TD');
			TD_DelCl.className='Center';
			TD_DelCl.innerHTML='<a href="' + window.location.href.split('?')[0] + '?EvCode=' + EvCode + '&DelRow=' + NewCl + '~' + NewDiv + '~' + NewSubCl + '"><img src="../../Common/Images/drop.png" border="0" alt="#" title="#"></a>';

			NewRow.appendChild(TD_Div);
			NewRow.appendChild(TD_Cl);
            NewRow.appendChild(TD_SubCl);
			NewRow.appendChild(TD_DelCl);


			if (i==0) {
				var TD_Delete = document.createElement('TD');
				TD_Delete.rowSpan=Arr_Rules.length;
				TD_Delete.className='Center';
				TD_Delete.innerHTML
					= '<a href="javascript:DeleteEventRule(\'' + EvCode + '\',' + New_Group + ');"><img src="../../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
				NewRow.appendChild(TD_Delete);
			}


			tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
		}

		document.getElementById('New_EcDivision').selectedIndex=-1;
		document.getElementById('New_EcClass').selectedIndex=-1;
        document.getElementById('New_EcSubClass').selectedIndex=-1;
		document.getElementById('New_EcNumber').value='';
	}
}

/*
	Invia la get a DeleteEventRule.php
	Elimina la regola identificata dai parametri
*/
function DeleteEventRule(Event,DelGroup)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString
					= 'EvCode=' + Event + '&'
					+ 'DelGroup=' + DelGroup;

				XMLHttp.open("GET","DeleteEventRule.php?" + QueryString,true);
// 				document.getElementById('idOutput').innerHTML="DeleteEventRule.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=DeleteEventRule_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
  			//alert('Error: ' + e.toString());
		}
	}
}

function DeleteEventRule_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				DeleteEventRule_Response();
			}
			catch(e)
			{
				//alert('Error: ' + e.toString());
			}
		}
		else
		{
			//alert('Error: ' +XMLHttp.statusText);
		}
	}
}

function DeleteEventRule_Response()
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

	if (Error==0)
	{
		var Event=XMLRoot.getElementsByTagName('event').item(0).firstChild.data;
		var Group=XMLRoot.getElementsByTagName('group').item(0).firstChild.data;

		var tbody=document.getElementById('tbody');

		var Arr_Rows = tbody.getElementsByTagName('tr');

// 		for (var i=0;i<Arr_Rows.length;++i)
// 		{
// 			var id=Arr_Rows.item(i).id;
// 			document.getElementById('idOutput').innerHTML+=id + '+<br>';
// 		}

		for (var i=Arr_Rows.length-1;i>=0;--i)
		{
			var id=Arr_Rows.item(i).id;
// 			document.getElementById('idOutput').innerHTML+=id + '-<br>';
			if (id)
			{
				var SplittedId=id.split('_');

				if (SplittedId[1]==Event && SplittedId[2]==Group)
				{
 					tbody.removeChild(document.getElementById(id));

				}
			}
		}
	}
}

function SetPartialTeam(Event)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{

				var QueryString
					= 'EvCode=' + Event
					+ '&EvPartial=' + document.getElementById('d_EvPartialTeam').value;

				XMLHttp.open("GET","SetPartialTeam.php?" + QueryString,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SetPartialTeam_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function SetPartialTeam_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SetPartialTeam_Response();
			}
			catch(e)
			{
// 				document.getElementById('idOutput').innerHTML+='<br>Error: ' + e.toString();
			}
		}
		else
		{
//  			document.getElementById('idOutput').innerHTML+='<br>Error: ' +XMLHttp.statusText;
		}
	}
}

function SetPartialTeam_Response()
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

	if(Error==1)
		SetStyle('d_EvPartialTeam','warning');
	else
		SetStyle('d_EvPartialTeam','');
}

function SetMultiTeam(Event)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{

				var QueryString
					= 'EvCode=' + Event
					+ '&EvMulti=' + document.getElementById('d_EvMultiTeam').value;

				XMLHttp.open("GET","SetMultiTeam.php?" + QueryString,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SetMultiTeam_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function SetMultiTeam_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SetMultiTeam_Response();
			}
			catch(e)
			{
// 				document.getElementById('idOutput').innerHTML+='<br>Error: ' + e.toString();
			}
		}
		else
		{
//  			document.getElementById('idOutput').innerHTML+='<br>Error: ' +XMLHttp.statusText;
		}
	}
}

function SetMultiTeam_Response()
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

	if(Error==1)
		SetStyle('d_EvMultiTeam','warning');
	else
		SetStyle('d_EvMultiTeam','');
}

function SetMixedTeam(Event)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{

				var QueryString
					= 'EvCode=' + Event
					+ '&EvMixed=' + document.getElementById('d_EvMixedTeam').value;

				XMLHttp.open("GET","SetMixedTeam.php?" + QueryString,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SetMixedTeam_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function SetMixedTeam_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SetMixedTeam_Response();
			}
			catch(e)
			{
// 				document.getElementById('idOutput').innerHTML+='<br>Error: ' + e.toString();
			}
		}
		else
		{
//  			document.getElementById('idOutput').innerHTML+='<br>Error: ' +XMLHttp.statusText;
		}
	}
}

function SetMixedTeam_Response()
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

	if(Error==1)
		SetStyle('d_EvMixedTeam','warning');
	else
		SetStyle('d_EvMixedTeam','');
}


function SetTeamCreationMode(Event)
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{

				var QueryString
					= 'EvCode=' + Event
					+ '&EvTeamCreationMode=' + document.getElementById('d_EvTeamCreationMode').value;

				XMLHttp.open("GET","SetTeamCreationMode.php?" + QueryString,true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=SetTeamCreationMode_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function SetTeamCreationMode_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				SetTeamCreationMode_Response();
			}
			catch(e)
			{
// 				document.getElementById('idOutput').innerHTML+='<br>Error: ' + e.toString();
			}
		}
		else
		{
//  			document.getElementById('idOutput').innerHTML+='<br>Error: ' +XMLHttp.statusText;
		}
	}
}

function SetTeamCreationMode_Response()
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

	if(Error==1)
		SetStyle('d_EvTeamCreationMode','warning');
	else
		SetStyle('d_EvTeamCreationMode','');
}



function UpdateParamsField(Field)
{
	if (XMLHttp)
	{
		if (Field && document.getElementById(Field) != null)
		{
			var FieldName = encodeURIComponent(Field);
			var FieldValue= encodeURIComponent(document.getElementById(Field).value);
			Cache.push(FieldName + "=" + FieldValue);
		}

		try
		{

			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) && Cache.length>0)
			{
				//var FieldValue = encodeURIComponent(document.getElementById(Field).value);
				var FromCache = Cache.shift();

				//XMLHttp.open("GET","UpdateFieldEventList.php?" + encodeURIComponent(Field) + "=" + FieldValue,true);
				XMLHttp.open("POST","UpdateFieldEventList.php",true);
				//document.getElementById('idOutput').innerHTML="UpdateFieldEventList.php?" + Field + "=" + FieldValue;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateParamsField_StateChange;
				XMLHttp.send(FromCache);
			}

		}
		catch (e)
		{
		//	alert('Error: ' + e.toString());
		}
	}
}

function UpdateParamsField_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateParamsField_Response();
			}
			catch(e)
			{
				//alert('Error: ' + e.toString());
			}
		}
		else
		{
			//alert('Error: ' +XMLHttp.statusText);
		}
	}
}

function UpdateParamsField_Response()
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
	var Which = XMLRoot.getElementsByTagName('which').item(0).firstChild.data;

	if (Error==1)
	{
		SetStyle(Which,'error');
	}
	else
	{
		SetStyle(Which,'');


	}

	// per scaricare la cache degli update
	setTimeout("UpdateParamsField()",500);
}

function enableSubclass(obj) {
    document.getElementById('New_EcSubClass').disabled = !obj.checked;
}

function showAdvanced() {
    $('#Advanced').css({'display':'table-row-group'});
    $('#AdvancedButton').css({'display':'none'});

}

function UpdateData(obj) {
    $.getJSON('../UpdateRuleParam.php?'+obj.id+'&val='+$(obj).val(), function(data) {
        if (data.error!=0) {
            alert(data.msg);
        }
    });
}
