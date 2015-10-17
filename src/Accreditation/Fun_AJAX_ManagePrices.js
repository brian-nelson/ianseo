/*
													- Fun_AJAX_ManagePrices.js -
	Contiene le funzioni ajax usate da ManagePrices.php
*/ 	

/*
	Invia la get a AddPrice.php
*/
function AddPrice()
{
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var OptDiv=document.getElementById('New_Division').options;
				var OptCl=document.getElementById('New_Class').options;
				var Price=document.getElementById('New_Price').value;
				
				if (OptDiv.selectedIndex>=0 && OptCl.selectedIndex>=0 && Price.length>0)
				{
					var QueryString = 'New_Price=' + Price;
				
					for (i=0;i<OptDiv.length;++i)
						if (OptDiv[i].selected)
							QueryString+= '&New_Division[]=' + OptDiv[i].value;
							
					for (i=0;i<OptCl.length;++i)
						if (OptCl[i].selected)
							QueryString+= '&New_Class[]=' + OptCl[i].value;
						
					XMLHttp.open("GET","AddPrice.php?" + QueryString,true);
					//document.getElementById('idOutput').innerHTML="AddPrice.php?" + QueryString;
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					XMLHttp.onreadystatechange=AddPrice_StateChange;
					XMLHttp.send(null);
				}
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function AddPrice_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				AddPrice_Response();
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

function AddPrice_Response()
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
		var Arr_Rules = XMLRoot.getElementsByTagName('new_rule');
		var NewPrice = XMLRoot.getElementsByTagName('new_price').item(0).firstChild.data;
		
		var ConfirmMsg = XMLRoot.getElementsByTagName('confirm_msg').item(0).firstChild.data;
		
		var tbody=document.getElementById('tbody');
		
	// Prima dell'ultima riga aggiungo le nuove regole
		
		for (i=0;i<Arr_Rules.length;++i)
		{
			var NewDivCl=Arr_Rules.item(i).firstChild.data;
			
			var NewRow = document.createElement('TR');
			NewRow.id='Row_' + NewDivCl;
			
			var TD_DivCl = document.createElement('TD');
			TD_DivCl.className='Center';
			TD_DivCl.colSpan='2';
			TD_DivCl.innerHTML=NewDivCl;
			
			var TD_Price = document.createElement('TD');
			TD_Price.className='Right';
			TD_Price.innerHTML=NewPrice + '&nbsp;&euro';
				
			var TD_Delete = document.createElement('TD');
			TD_Delete.className='Center';
			TD_Delete.innerHTML
				= '<a href="javascript:DeletePrice(\'' + NewDivCl + '\');"><img src="../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
			
			NewRow.appendChild(TD_DivCl);
			NewRow.appendChild(TD_Price);
			NewRow.appendChild(TD_Delete);
			
			tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
		}
		
		document.getElementById('New_Division').selectedIndex=-1;
		document.getElementById('New_Class').selectedIndex=-1;
		document.getElementById('New_Price').value='';
		
	}
}

/*
	Invia la get a DeletePrice.php
	per eliminare  il prezzo di DelDivClass
*/
function DeletePrice(DelDivCl)
{	
	if (XMLHttp)
	{	
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var QueryString
					= 'DelDivCl=' + DelDivCl;
					
				XMLHttp.open("GET","DeletePrice.php?" + QueryString,true);
				//document.getElementById('idOutput').innerHTML="DeletePrice.php?" + QueryString;
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=DeletePrice_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e)
		{
 			//document.getElementById('idOutput').innerHTML='Error: ' + e.toString();
		}
	}
}

function DeletePrice_StateChange()
{
	// se lo stato � Complete vado avanti
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
	// se lo status di HTTP � ok vado avanti
		if (XMLHttp.status==200)
		{
			try
			{
				DeletePrice_Response();
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

function DeletePrice_Response()
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
		var DivCl=XMLRoot.getElementsByTagName('divcl').item(0).firstChild.data;
		
		var tbody=document.getElementById('tbody');
		
		var Row=document.getElementById('Row_' + DivCl);
		if (Row)
			tbody.removeChild(Row);
	}
}