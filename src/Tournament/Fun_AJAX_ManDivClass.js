
/*
													- Fun_AJAX_ManDivClass.js -
	Contiene le funzioni ajax usate da ManDivClass.php
*/ 	

/*
	- UpdateField(Tab,Field)
	esegue la post a UpdateManDivClassField.php.
	Tab è la tabella usata per l'update
	Field è l'id del campo da usare
*/
function UpdateField(Tab,Field)
{
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString = 'Tab=' + Tab;
									
				var FieldValue = encodeURIComponent(document.getElementById(Field).value);
				
				QueryString+= '&' + Field + '=' + FieldValue;
				
				XMLHttp.open("POST","UpdateManDivClassField.php",true);
				//document.getElementById('idOutput').innerHTML="UpdateManDivClassField.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateField_StateChange;
				XMLHttp.send(QueryString);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
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
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		}
		else
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function UpdateField_Response()
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
	var Which=XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
	
	if (Error==1)
	{
		if (Which!='#')
		{
			SetStyle(Which,'error');
		}
	}
	else
	{
		if (Which!='#')
		{
			SetStyle(Which,'');
		}
	}
}

/*
	- DeleteRow(Tab,Id,Msg)
	esegue la post a DeleteManDivClassField.php.
	Tab è la tabella usata per l'update
	Id è il valore da eliminare
	Msg è il messaggio di conferma
*/
function DeleteRow(Tab,Id,Msg)
{
	if (XMLHttp)
	{	
		try
		{
			if (confirm(Msg.replace(/\+/g," ")))
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var QueryString = 'Tab=' + Tab + '&Id=' + Id;
					
					XMLHttp.open("POST","DeleteManDivClassField.php",true);
					//document.getElementById('idOutput').innerHTML="DeleteManDivClassField.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=DeleteRow_StateChange;
					XMLHttp.send(QueryString);
				}
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
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
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		}
		else
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function DeleteRow_Response()
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
	var Which=XMLRoot.getElementsByTagName('which').item(0).firstChild.data;
	var Tab=XMLRoot.getElementsByTagName('tab').item(0).firstChild.data;
	if (Error==0)
	{
		if (Which!='#')
		{
			var tbody=null;
		
			var Row=null;
			
			switch (Tab)
			{
				case 'D':
					tbody=document.getElementById('tbody_div');
					Row=document.getElementById('Div_' + Which);
					break;
				case 'C':
					tbody=document.getElementById('tbody_cl');
					Row=document.getElementById('Cl_' + Which);
					break;
				case 'SC':
					tbody=document.getElementById('tbody_subclass');
					Row=document.getElementById('SubClass_' + Which);
					break;
			}

			if (Row)
				tbody.removeChild(Row);
		}
	}
}

/*
	- AddDiv(ErrMsg)
	esegue la post a AddDiv.php per aggiungere una divisione.
	ErrMsg è il messaggio di errore
*/
function AddDiv(ErrMsg)
{
	if (XMLHttp)
	{	
		try
		{
			if (document.getElementById('New_DivId').value!='' && 
				document.getElementById('New_DivDescription').value!='' &&
				document.getElementById('New_DivViewOrder').value!='')
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var QueryString 
						= 'New_DivId=' + encodeURIComponent(document.getElementById('New_DivId').value)
						+ '&New_DivDescription=' + encodeURIComponent(document.getElementById('New_DivDescription').value)
						+ '&New_DivAthlete=' + encodeURIComponent(document.getElementById('New_DivAthlete').value)
						+ '&New_DivViewOrder=' + encodeURIComponent(document.getElementById('New_DivViewOrder').value);
					
					XMLHttp.open("POST","AddDiv.php",true);
					//document.getElementById('idOutput').innerHTML="AddDiv.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddDiv_StateChange;
					XMLHttp.send(QueryString);
				}
			}
			else
				alert(ErrMsg.replace(/\+/g," "));
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function AddDiv_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				AddDiv_Response();
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
}

function AddDiv_Response()
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
		var NewDivId = XMLRoot.getElementsByTagName('new_divid').item(0).firstChild.data;
		var NewDivDescr = XMLRoot.getElementsByTagName('new_divdescr').item(0).firstChild.data;
		var NewDivProgr = XMLRoot.getElementsByTagName('new_divprogr').item(0).firstChild.data;
		var NewDivAthlete = XMLRoot.getElementsByTagName('new_divathlete').item(0).firstChild.data;
		var NewDivAthleteYes = XMLRoot.getElementsByTagName('new_divathleteyes').item(0).firstChild.data;
		var NewDivAthleteNo = XMLRoot.getElementsByTagName('new_divathleteno').item(0).firstChild.data;
		var ConfirmMsg = XMLRoot.getElementsByTagName('confirm_msg').item(0).firstChild.data;
		
		var NewRow = document.createElement('TR');
		NewRow.id='Div_' + NewDivId;
		
		var TD_DivId = document.createElement('TD');
		TD_DivId.className='Bold Center';
		TD_DivId.innerHTML=NewDivId;
		
		var TD_DivDescr = document.createElement('TD');
		TD_DivDescr.innerHTML
			= '<input type="text" name="d_DivDescription_' + NewDivId + '" id="d_DivDescription_' + NewDivId + '" size="56" maxlength="32" value="' + NewDivDescr + '" onBlur="javascript:UpdateField(\'D\',\'d_DivDescription_' + NewDivId + '\');">';
		
		var TD_DivAthlete = document.createElement('TD');
		TD_DivAthlete.className='Center';
		TD_DivAthlete.innerHTML 
			= '<select name="d_DivAthlete_' + NewDivId + '" id="d_DivAthlete_' + NewDivId + '"  onBlur="javascript:UpdateField(\'D\',\'d_DivAthlete_' + NewDivId + '\');"><option value="0"' + (NewDivAthlete==0?' selected':'') + '>' + NewDivAthleteNo + '</option><option value="1"' + (NewDivAthlete!=0?' selected':'') + '>' + NewDivAthleteYes + '</option></select>';
		
		var TD_DivProgr = document.createElement('TD');
		TD_DivProgr.className='Center';
		TD_DivProgr.innerHTML 
			= '<input type="text" name="d_DivViewOrder_' + NewDivId + '" id="d_DivViewOrder_' + NewDivId + '" size="3" maxlength="3" value="' + NewDivProgr + '" onBlur="javascript:UpdateField(\'D\',\'d_DivViewOrder_' + NewDivId + '\');">';
		
		var TD_Delete = document.createElement('TD');
		TD_Delete.className='Center';
		TD_Delete.innerHTML
			= '<a href="javascript:DeleteRow(\'D\',\'' + NewDivId + '\',\'' + ConfirmMsg + '\');"><img src="../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
	
		NewRow.appendChild(TD_DivId);
		NewRow.appendChild(TD_DivDescr);
		NewRow.appendChild(TD_DivAthlete);
		NewRow.appendChild(TD_DivProgr);
		NewRow.appendChild(TD_Delete);
		
		var tbody=document.getElementById('tbody_div');
		
		tbody.insertBefore(NewRow,document.getElementById('NewDiv'));
		
		// resetto i dati nella riga di inserimento
		
		document.getElementById('New_DivId').value='';
		document.getElementById('New_DivDescription').value='';
		document.getElementById('New_DivAthlete').value='';
		document.getElementById('New_DivViewOrder').value='';
	}
}


/*
	- UpdateClassAge(Id,FromTo)
	Esegue la post a UpdateClassAge.php.
	Se FromTo vale 'From' significa che si sta aggiornando un AgeFrom.
	Se FromTo vale 'to'  significa che si sta aggiornando un AgeTo
*/

function UpdateClassAge(Id,FromTo) {
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString 
					= 'ClId=' + Id 
					+ '&FromTo=' + FromTo 
					+ '&Age=' + encodeURIComponent(document.getElementById('d_ClAge' + FromTo + '_' + Id).value)
					+ '&AlDivs=' + encodeURIComponent(document.getElementById('d_ClValidDivision_' + Id).value);
				XMLHttp.open("POST","UpdateClassAge.php",true);
				//document.getElementById('idOutput').innerHTML="UpdateClassAge.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateClassAge_StateChange;
				XMLHttp.send(QueryString);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function UpdateClassAge_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateClassAge_Response();
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
}

function UpdateClassAge_Response()
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
	
	var ClId=XMLRoot.getElementsByTagName('clid').item(0).firstChild.data;
	var FromTo=XMLRoot.getElementsByTagName('fromto').item(0).firstChild.data;
	var ObjId = 'd_ClAge' + FromTo + '_' + ClId;
	
	if (Error==1)
		SetStyle(ObjId,'error');
	else
		SetStyle(ObjId,'');
}

/*
	- UpdateValidClass(Id)
*/
function UpdateValidClass(Id)
{
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString 
					= 'ClId=' + Id 
					+ '&ClList=' + encodeURIComponent(document.getElementById('d_ClValidClass_' + Id).value);
				XMLHttp.open("POST","UpdateValidClass.php",true);
				//document.getElementById('idOutput').innerHTML="UpdateClassAge.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateValidClass_StateChange;
				XMLHttp.send(QueryString);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function UpdateValidClass_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				UpdateValidClass_Response();
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
}

function UpdateValidClass_Response()
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
	var ClId=XMLRoot.getElementsByTagName('clid').item(0).firstChild.data;
	var ObjId = 'd_ClValidClass_' + ClId;
	
	if (Error==1)
		SetStyle(ObjId,'error');
	else	
	{
		SetStyle(ObjId,'');
		
		var Valid=XMLRoot.getElementsByTagName('valid').item(0).firstChild.data;
		
		document.getElementById(ObjId).value=Valid;
	}
}

/*
- UpdateValidDivision(Id)
*/
function UpdateValidDivision(Id) {
	if (XMLHttp) {	
		try {
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
				var QueryString 
					= 'ClId=' + Id 
					+ '&ClList=' + encodeURIComponent(document.getElementById('d_ClValidDivision_' + Id).value);
				XMLHttp.open("POST","UpdateValidDivision.php",true);
				//document.getElementById('idOutput').innerHTML="UpdateClassAge.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=UpdateValidDivision_StateChange;
				XMLHttp.send(QueryString);
			}
		} catch (e) {
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function UpdateValidDivision_StateChange() {
	// se lo stato è Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE) {
	// se lo status di HTTP è ok vado avanti
		if (XMLHttp.status==200) {
			try {
				UpdateValidDivision_Response();
			} catch(e) {
				//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
			}
		} else {
				//document.getElementById('idOutput').innerHTML='Error: ' +XMLHttp.statusText;
		}
	}
}

function UpdateValidDivision_Response() {
	// leggo l'xml
	var XMLResp=XMLHttp.responseXML;
	
	//intercetto gli errori di IE e Opera
	if (!XMLResp || !XMLResp.documentElement) throw("XML non valido:\n"+XMLResp.responseText);
	
	//Intercetto gli errori di Firefox
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("XML non valido:\n");
	
	XMLRoot = XMLResp.documentElement;	
	
	var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	var ClId=XMLRoot.getElementsByTagName('clid').item(0).firstChild.data;
	var ObjId = 'd_ClValidDivision_' + ClId;
	
	if (Error==1) {
		SetStyle(ObjId,'error');
	} else {
		SetStyle(ObjId,'');
		
		var Valid=XMLRoot.getElementsByTagName('valid').item(0).firstChild.data;
		
		document.getElementById(ObjId).value=Valid;
	}
}

/*
	- AddCl(ErrMsg)
	esegue la post a AddCl.php per aggiungere una classe
	ErrMsg è il messaggio di errore
*/
function AddCl(ErrMsg)
{
	if (XMLHttp)
	{	
		try
		{
			if (document.getElementById('New_ClId').value!='' && 
				document.getElementById('New_ClDescription').value!='' &&
				document.getElementById('New_ClViewOrder').value!='' &&
				document.getElementById('New_ClAgeFrom').value!='' &&
				document.getElementById('New_ClAgeTo').value!='' &&
				document.getElementById('New_ClValidClass').value!='')
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var QueryString 
						= 'New_ClId=' + encodeURIComponent(document.getElementById('New_ClId').value)
						+ '&New_ClDescription=' + encodeURIComponent(document.getElementById('New_ClDescription').value)
						+ '&New_ClAthlete=' + encodeURIComponent(document.getElementById('New_ClAthlete').value)
						+ '&New_ClViewOrder=' + encodeURIComponent(document.getElementById('New_ClViewOrder').value)
						+ '&New_ClAgeFrom=' + encodeURIComponent(document.getElementById('New_ClAgeFrom').value)
						+ '&New_ClAgeTo=' + encodeURIComponent(document.getElementById('New_ClAgeTo').value)
						+ '&New_ClValidClass=' + encodeURIComponent(document.getElementById('New_ClValidClass').value)
						+ '&New_ClSex=' + encodeURIComponent(document.getElementById('New_ClSex').value)
						+ '&New_ClValidDivision=' + encodeURIComponent(document.getElementById('New_ClValidDivision').value);
					
					XMLHttp.open("POST","AddCl.php",true);
					//document.getElementById('idOutput').innerHTML="AddCl.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddCl_StateChange;
					XMLHttp.send(QueryString);
				}
			}
			else
				alert(ErrMsg.replace(/\+/g," "));
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function AddCl_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				AddCl_Response();
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
}

function AddCl_Response()
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
		var NewClId = XMLRoot.getElementsByTagName('new_clid').item(0).firstChild.data;
		var NewClSex = XMLRoot.getElementsByTagName('new_clsex').item(0).firstChild.data;
		var NewClDescr = XMLRoot.getElementsByTagName('new_cldescr').item(0).firstChild.data;
		var NewClAthlete = XMLRoot.getElementsByTagName('new_clathlete').item(0).firstChild.data;
		var NewClAthleteYes = XMLRoot.getElementsByTagName('new_clathleteyes').item(0).firstChild.data;
		var NewClAthleteNo = XMLRoot.getElementsByTagName('new_clathleteno').item(0).firstChild.data;
		var NewClProgr = XMLRoot.getElementsByTagName('new_clprogr').item(0).firstChild.data;
		var NewClAgeFrom = XMLRoot.getElementsByTagName('new_clagefrom').item(0).firstChild.data;
		var NewClAgeTo = XMLRoot.getElementsByTagName('new_clageto').item(0).firstChild.data;
		var NewClValidClass = XMLRoot.getElementsByTagName('new_clvalid').item(0).firstChild.data;
		var NewClValidDivision = XMLRoot.getElementsByTagName('new_clvaliddiv').item(0).firstChild.data;
		var ConfirmMsg = XMLRoot.getElementsByTagName('confirm_msg').item(0).firstChild.data;
		var Male = XMLRoot.getElementsByTagName('male').item(0).firstChild.data;
		var Female = XMLRoot.getElementsByTagName('female').item(0).firstChild.data;
		var Unisex = XMLRoot.getElementsByTagName('unisex').item(0).firstChild.data;
		
		var NewRow = document.createElement('TR');
		NewRow.id='Cl_' + NewClId;
		
		var TD_ClId = document.createElement('TD');
		TD_ClId.className='Bold Center';
		TD_ClId.innerHTML=NewClId;
		
		var TD_ClSex = document.createElement('TD');
		TD_ClSex.innerHTML
			= '<select name="d_ClSex_' + NewClId + '" id="d_ClSex_' + NewClId + '">' + "\n"
			+ '<option value="0"' + (NewClSex==0 ? ' selected' : '') + '>' + Male + '</option>' + "\n"
			+ '<option value="1"' + (NewClSex==1 ? ' selected' : '') + '>' + Female + '</option>' + "\n"
			+ '<option value="-1"' + (NewClSex==-1 ? ' selected' : '') + '>' + Unisex + '</option>' + "\n"
			+ '</select>' + "\n";
		
		var TD_ClDescr = document.createElement('TD');
		TD_ClDescr.innerHTML
			= '<input type="text" name="d_ClDescription_' + NewClId + '" id="d_ClDescription_' + NewClId + '" size="56" maxlength="32" value="' + NewClDescr + '" onBlur="javascript:UpdateField(\'C\',\'d_ClDescription_' + NewClId + '\');">';
		
		var TD_ClAthlete = document.createElement('TD');
		TD_ClAthlete.className='Center';
		TD_ClAthlete.innerHTML 
			= '<select name="d_ClAthlete_' + NewClId + '" id="d_ClAthlete_' + NewClId + '"  onBlur="javascript:UpdateField(\'C\',\'d_ClAthlete_' + NewClId + '\');"><option value="0"' + (NewClAthlete==0?' selected':'') + '>' + NewClAthleteNo + '</option><option value="1"' + (NewClAthlete==1?' selected':'') + '>' + NewClAthleteYes + '</option></select>';
		
		var TD_ClProgr = document.createElement('TD');
		TD_ClProgr.className='Center';
		TD_ClProgr.innerHTML 
			= '<input type="text" name="d_ClViewOrder_' + NewClId + '" id="d_ClViewOrder_' + NewClId + '" size="3" maxlength="3" value="' + NewClProgr + '" onBlur="javascript:UpdateField(\'C\',\'d_ClViewOrder_' + NewClId + '\');">';
			
		var TD_ClAgeFrom = document.createElement('TD');
		TD_ClAgeFrom.className='Center';
		TD_ClAgeFrom.innerHTML 
			= '<input type="text" name="d_ClAgeFrom_' + NewClId + '" id="d_ClAgeFrom_' + NewClId + '" size="3" maxlength="3" value="' + NewClAgeFrom + '" onBlur="javascript:UpdateClassAge(\'' + NewClId + '\',\'From\');">';
						
		var TD_ClAgeTo = document.createElement('TD');
		TD_ClAgeTo.className='Center';
		TD_ClAgeTo.innerHTML 
			= '<input type="text" name="d_ClAgeTo_' + NewClId + '" id="d_ClAgeTo_' + NewClId + '" size="3" maxlength="3" value="' + NewClAgeTo + '" onBlur="javascript:UpdateClassAge(\'' + NewClId + '\',\'To\');">';						
			
		var TD_ClValidClass = document.createElement('TD');
		TD_ClValidClass.className='Center';
		TD_ClValidClass.innerHTML 
			= '<input type="text" name="d_ClValidClass_' + NewClId + '" id="d_ClValidClass_' + NewClId + '" size="8" maxlength="16" value="' + NewClValidClass + '" onBlur="javascript:UpdateValidClass(\'' + NewClId + '\');">';						
		
		var TD_ClValidDivision = document.createElement('TD');
		TD_ClValidDivision.className='Center';
		TD_ClValidDivision.innerHTML 
			= '<input type="text" name="d_ClValidDivision_' + NewClId + '" id="d_ClValidDivision_' + NewClId + '" size="8" maxlength="16" value="' + NewClValidDivision + '" onBlur="javascript:UpdateValidDivision(\'' + NewClId + '\');">';						
		
		var TD_Delete = document.createElement('TD');
		TD_Delete.className='Center';
		TD_Delete.innerHTML
			= '<a href="javascript:DeleteRow(\'C\',\'' + NewClId + '\',\'' + ConfirmMsg + '\');"><img src="../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
	
		NewRow.appendChild(TD_ClId);
		NewRow.appendChild(TD_ClSex);
		NewRow.appendChild(TD_ClDescr);
		NewRow.appendChild(TD_ClAthlete);
		NewRow.appendChild(TD_ClProgr);
		NewRow.appendChild(TD_ClAgeFrom);
		NewRow.appendChild(TD_ClAgeTo);
		NewRow.appendChild(TD_ClValidClass);
		NewRow.appendChild(TD_ClValidDivision);
		NewRow.appendChild(TD_Delete);
		
		var tbody=document.getElementById('tbody_cl');
		
		tbody.insertBefore(NewRow,document.getElementById('NewCl'));
		
		// resetto i dati nella riga di inserimento
		document.getElementById('New_ClId').value='';
		document.getElementById('New_ClSex').selectedIndex=0;
		document.getElementById('New_ClDescription').value='';
		document.getElementById('New_ClAthlete').selectedIndex=0;
		document.getElementById('New_ClViewOrder').value='';
		document.getElementById('New_ClAgeFrom').value='';
		document.getElementById('New_ClAgeTo').value='';
		document.getElementById('New_ClValidClass').value='';
		document.getElementById('New_ClValidDivision').value='';
	} else if(Error==2) {
		alert('Duplicate Entry');
	}
}

function AddSubClass(ErrMsg)
{
	if (XMLHttp)
	{	
		try
		{
			if (document.getElementById('New_ScId').value!='' && 
				document.getElementById('New_ScDescription').value!='' &&
				document.getElementById('New_ScViewOrder').value!='')
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var QueryString 
						= 'New_ScId=' + encodeURIComponent(document.getElementById('New_ScId').value)
						+ '&New_ScDescription=' + encodeURIComponent(document.getElementById('New_ScDescription').value)
						+ '&New_ScViewOrder=' + encodeURIComponent(document.getElementById('New_ScViewOrder').value);
					
					XMLHttp.open("POST","AddSubCl.php",true);
					//document.getElementById('idOutput').innerHTML="AddDiv.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddSubClass_StateChange;
					XMLHttp.send(QueryString);
				}
			}
			else
				alert(ErrMsg.replace(/\+/g," "));
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function AddSubClass_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				AddSubClass_Response();
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
}

function AddSubClass_Response()
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
		var NewScId = XMLRoot.getElementsByTagName('new_scid').item(0).firstChild.data;
		var NewScDescr = XMLRoot.getElementsByTagName('new_scdescr').item(0).firstChild.data;
		var NewScProgr = XMLRoot.getElementsByTagName('new_scprogr').item(0).firstChild.data;
		var ConfirmMsg = XMLRoot.getElementsByTagName('confirm_msg').item(0).firstChild.data;
		
		var NewRow = document.createElement('TR');
		NewRow.id='SubClass_' + NewScId;
		
		var TD_ScId = document.createElement('TD');
		TD_ScId.className='Bold Center';
		TD_ScId.innerHTML=NewScId;
		
		var TD_ScDescr = document.createElement('TD');
		TD_ScDescr.innerHTML
			= '<input type="text" name="d_ScDescription_' + NewScId + '" id="d_ScDescription_' + NewScId + '" size="56" maxlength="32" value="' + NewScDescr + '" onBlur="javascript:UpdateField(\'SC\',\'d_ScDescription_' + NewScId + '\');">';
		
		var TD_ScProgr = document.createElement('TD');
		TD_ScProgr.className='Center';
		TD_ScProgr.innerHTML 
			= '<input type="text" name="d_ScViewOrder_' + NewScId + '" id="d_ScViewOrder_' + NewScId + '" size="3" maxlength="3" value="' + NewScProgr + '" onBlur="javascript:UpdateField(\'SC\',\'d_ScViewOrder_' + NewScId + '\');">';
		
		var TD_Delete = document.createElement('TD');
		TD_Delete.className='Center';
		TD_Delete.innerHTML
			= '<a href="javascript:DeleteRow(\'SC\',\'' + NewScId + '\',\'' + ConfirmMsg + '\');"><img src="../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
	
		NewRow.appendChild(TD_ScId);
		NewRow.appendChild(TD_ScDescr);
		NewRow.appendChild(TD_ScProgr);
		NewRow.appendChild(TD_Delete);
		
		var tbody=document.getElementById('tbody_subclass');
		
		tbody.insertBefore(NewRow,document.getElementById('NewSubCl'));
		
		// resetto i dati nella riga di inserimento
		document.getElementById('New_ScId').value='';
		document.getElementById('New_ScDescription').value='';
		document.getElementById('New_ScViewOrder').value='';
	}
}

