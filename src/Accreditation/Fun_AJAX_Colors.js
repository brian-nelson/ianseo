/*
														- Fun_AJAX_Colors.js -
	Contiene le funzioni ajax che riguardano la pagina Colors.php
	NOTA BENE: deve essere invlusa la stringa
	<?php 
	print 'var StrConfirm="' . get_text('MsgAreYouSure') . '";';
	?> 				
*/ 		
function resetArea0(obj) {
	if((obj.id=='d_Area0' && obj.checked) || (obj.id=='d_Area0Star' && obj.checked)) {
		document.getElementById('d_Area1').checked=false;
		document.getElementById('d_Area1Star').checked=false;
		document.getElementById('d_Area2').checked=false;
		document.getElementById('d_Area3').checked=false;
		document.getElementById('d_Area4').checked=false;
		document.getElementById('d_Area5').checked=false;
		document.getElementById('d_Area6').checked=false;
		document.getElementById('d_Area7').checked=false;	
	}  else if(obj.id.substr(6,1)>0 && obj.checked) {
		document.getElementById('d_Area0').checked=false;
		document.getElementById('d_Area0Star').checked=false;
	}
	if(obj.id=='d_Area0' && obj.checked) {
		document.getElementById('d_Area0Star').checked=false;
	}
	if(obj.id=='d_Area0Star' && obj.checked) {
		document.getElementById('d_Area0').checked=false;
	}
	if(obj.id=='d_Area1' && obj.checked) {
		document.getElementById('d_Area1Star').checked=false;
	}
	if(obj.id=='d_Area1Star' && obj.checked) {
		document.getElementById('d_Area1').checked=false;
	}
}

function resetInput() {
	SetStyle('d_Classes','');
	SetStyle('d_Color','');
	SetStyle('d_TitleReverse','');
	SetStyle('Square','');
	SetStyle('d_Ath','');
	SetStyle('d_Area0','');
	SetStyle('d_Area0Star','');
	SetStyle('d_Area1','');
	SetStyle('d_Area1Star','');
	SetStyle('d_Area2','');
	SetStyle('d_Area3','');
	SetStyle('d_Area4','');
	SetStyle('d_Area5','');
	SetStyle('d_Area6','');
	SetStyle('d_Area7','');
	SetStyle('d_Transport','');
	SetStyle('d_Accomodation','');
	SetStyle('d_Meals','');
	
	document.getElementById('d_rowId').value=-1;
	document.getElementById('d_Classes').value='';
	document.getElementById('d_Color').value='';
	document.getElementById('d_TitleReverse').value=0;
	document.getElementById('Square').style.backgroundColor='#ffffff';
	document.getElementById('d_Ath').value=1;
	document.getElementById('d_Area0').checked=false;
	document.getElementById('d_Area0Star').checked=false;
	document.getElementById('d_Area1').checked=false;
	document.getElementById('d_Area1Star').checked=false;
	document.getElementById('d_Area2').checked=false;
	document.getElementById('d_Area3').checked=false;
	document.getElementById('d_Area4').checked=false;
	document.getElementById('d_Area5').checked=false;
	document.getElementById('d_Area6').checked=false;
	document.getElementById('d_Area7').checked=false;
	document.getElementById('d_Transport').checked=false;
	document.getElementById('d_Accomodation').checked=false;
	document.getElementById('d_Meals').checked=false;
}

function save()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				var queryString
					= 'rowid=' + encodeURIComponent(document.getElementById('d_rowId').value)	
					+ '&cl=' + encodeURIComponent(document.getElementById('d_Classes').value)
					+ '&ath=' + encodeURIComponent(document.getElementById('d_Ath').value)
					+ '&col=' + encodeURIComponent(document.getElementById('d_Color').value)
					+ '&titlereverse=' + encodeURIComponent(document.getElementById('d_TitleReverse').value)
					+ '&area0=' + encodeURIComponent(document.getElementById('d_Area0').checked || document.getElementById('d_Area0Star').checked ? 1:0)
					+ '&area1=' + encodeURIComponent(document.getElementById('d_Area1').checked || document.getElementById('d_Area1Star').checked ? 1:0)
					+ '&area2=' + encodeURIComponent(document.getElementById('d_Area2').checked ? 1:0)
					+ '&area3=' + encodeURIComponent(document.getElementById('d_Area3').checked ? 1:0)
					+ '&area4=' + encodeURIComponent(document.getElementById('d_Area4').checked ? 1:0)
					+ '&area5=' + encodeURIComponent(document.getElementById('d_Area5').checked ? 1:0)
					+ '&area6=' + encodeURIComponent(document.getElementById('d_Area6').checked ? 1:0)
					+ '&area7=' + encodeURIComponent(document.getElementById('d_Area7').checked ? 1:0)
					+ '&areastar=' + encodeURIComponent((document.getElementById('d_Area1Star').checked || document.getElementById('d_Area0Star').checked) ? 1:0)
					+ '&transport=' + encodeURIComponent(document.getElementById('d_Transport').value)
					+ '&accomodation=' + encodeURIComponent(document.getElementById('d_Accomodation').value)
					+ '&meal=' + encodeURIComponent(document.getElementById('d_Meals').value);
					
				XMLHttp.open("POST","SaveColor.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				//document.getElementById('idOutput').innerHTML="SaveDists.php?" + queryString;
				XMLHttp.onreadystatechange=save_StateChange;
				XMLHttp.send(queryString);
			}
		}
		catch(e)
		{
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function save_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				save_Response();
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

function save_Response()
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
	
	if (Error==0)
	{
		var tbody=document.getElementById('tbody');
		
		var rowid=XMLRoot.getElementsByTagName('rowid').item(0).firstChild.data;
		var cl=XMLRoot.getElementsByTagName('cl').item(0).firstChild.data;
		var col=XMLRoot.getElementsByTagName('col').item(0).firstChild.data;
		var titlereverse=XMLRoot.getElementsByTagName('titlereverse').item(0).firstChild.data;
		var ath=XMLRoot.getElementsByTagName('ath').item(0).firstChild.data;
		var area=XMLRoot.getElementsByTagName('area').item(0).firstChild.data;
		var transport=XMLRoot.getElementsByTagName('transport').item(0).firstChild.data;
		var accomodation=XMLRoot.getElementsByTagName('accomodation').item(0).firstChild.data;
		var meal=XMLRoot.getElementsByTagName('meal').item(0).firstChild.data;
		
		
		var numRows=tbody.rows.length;
		var rows=tbody.rows;
		
		var row=rows[numRows-1].id.substr(4);
		++row;

		var TR;
		if(rowid==-1)
		{
			TR=document.createElement('tr');
			TR.id='row_' +row;
		}
		else
		{
			TR=document.getElementById('row_' + rowid);
			while (TR.childNodes[0]) {
				TR.removeChild(TR.childNodes[0]);
			}

			TR.innerHTML='';
			TR.id='row_' + rowid;
		}
		
		var TD=document.createElement('td');
		TD.className='Center';
		var tmp ='<a href="javascript:editRule(\'' +  rowid + '\',\'' + cl + '\',\'#' + col+ '\',\''+ titlereverse + '\',';
		for(i=0; i<=7; i++)
			tmp += '\'' + (area.indexOf(i)==-1 ? '0':'1') + '\', ';
		tmp += '\'' + (area.indexOf('*')==-1 ? '0':'1') + '\',\'' + transport + '\',\'' + accomodation + '\',\'' + meal + '\')">';
		tmp += cl + '</a>';
		TD.innerHTML =tmp;
		TR.appendChild(TD);
		
		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= '<input type="text" readonly="readonly" size="1" style="background-color:#' + col + '" />&nbsp;#'+ col;
		TR.appendChild(TD);

		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= titlereverse;
		TR.appendChild(TD);
		
		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= ath;
		TR.appendChild(TD);
		
		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= area;
		TR.appendChild(TD);
		
		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= transport;
		TR.appendChild(TD);
		
		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= accomodation;
		TR.appendChild(TD);
		
		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= meal;
		TR.appendChild(TD);
		
		TD=document.createElement('td');
		TD.className='Center';
		TD.innerHTML= TD.innerHTML= '<img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow(' + row + ',\'' + cl + '\');">';
		TR.appendChild(TD);
		
		if(rowid==-1)
		{
			tbody.appendChild(TR);
		}
		
		resetInput();
	}
	else
	{
		SetStyle('d_Classes','yellow');
		SetStyle('d_Color','yellow');
		SetStyle('d_TitleReverse','yellow');
		SetStyle('Square','yellow');
		SetStyle('d_Ath','yellow');
		SetStyle('d_Area0','yellow');
		SetStyle('d_Area0Star','yellow');
		SetStyle('d_Area1','yellow');
		SetStyle('d_Area1Star','yellow');
		SetStyle('d_Area2','yellow');
		SetStyle('d_Area3','yellow');
		SetStyle('d_Area4','yellow');
		SetStyle('d_Area5','yellow');
		SetStyle('d_Area6','yellow');
		SetStyle('d_Area7','yellow');
		SetStyle('d_Transport','yellow');
		SetStyle('d_Accomodation','yellow');
		SetStyle('d_Meals','yellow');
		
	}
}

function deleteRow(row,cl)
{
	if (confirm(StrConfirm))
	{
		if (XMLHttp)
		{
			try
			{
				if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
				{
					var queryString
						= 'cl=' + cl
						+ '&row=' + row;
					
					XMLHttp.open("POST","DeleteColor.php",true);
					XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					//document.getElementById('idOutput').innerHTML="DeleteDists.php?" + queryString;
					XMLHttp.onreadystatechange=deleteRow_StateChange;
					XMLHttp.send(queryString);
				}
			}
			catch(e)
			{
				//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
			}
		}
	}
}

function deleteRow_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				deleteRow_Response();
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

function deleteRow_Response()
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
	var row = XMLRoot.getElementsByTagName('row').item(0).firstChild.data;
	
	if (Error==0)
	{
		var tbody=document.getElementById('tbody');

		var row2del=document.getElementById('row_' + row);
		
		if (row2del)
			tbody.removeChild(row2del);
	}
}

function editRule(RowId, DivCl, Color, NegTitle, Area0, Area1, Area2, Area3, Area4, Area5, Area6, Area7, AreaStar, Trasport, Accomodation, Meal)
{
	document.getElementById('d_rowId').value=RowId;
	document.getElementById('d_Classes').value=DivCl;
	document.getElementById('d_Color').value=Color;
	document.getElementById('Square').style.backgroundColor=Color;
	document.getElementById('d_TitleReverse').value=NegTitle;
	document.getElementById('d_Area0').checked=(AreaStar!=1 && Area0==1 ? true : false);
	document.getElementById('d_Area0Star').checked=(AreaStar==1 && Area0==1 ? true : false);
	document.getElementById('d_Area1').checked=(AreaStar!=1 && Area1==1 ? true : false);
	document.getElementById('d_Area1Star').checked=(AreaStar==1 && Area1==1 ? true : false);
	document.getElementById('d_Area2').checked=(Area2==1 ? true : false);
	document.getElementById('d_Area3').checked=(Area3==1 ? true : false);
	document.getElementById('d_Area4').checked=(Area4==1 ? true : false);
	document.getElementById('d_Area5').checked=(Area5==1 ? true : false);
	document.getElementById('d_Area6').checked=(Area6==1 ? true : false);
	document.getElementById('d_Area7').checked=(Area7==1 ? true : false);
	document.getElementById('d_Transport').value=Trasport;
	document.getElementById('d_Accomodation').value=Accomodation;
	document.getElementById('d_Meals').value=Meal;
}



