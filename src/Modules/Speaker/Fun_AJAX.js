/*
													- Fun_AJAX.js -
	Contiene le funzioni ajax che riguardano la speaker view 
*/
var t;
var mRead = new Array();
var mUpdate = new Array();

function GetEvents()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				document.getElementById('lu').value=0;
				mRead = new Array();
				mUpdate = new Array();
				clearTimeout(t);
				var schedule = document.getElementById('x_Schedule').value;
				XMLHttp.open("GET","GetEvents.php?schedule="+schedule,true);
				XMLHttp.onreadystatechange=GetEvents_StateChange;
				XMLHttp.send(null);
			}
			
		}
		catch (e) { }
	}
}

function GetEvents_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				GetEvents_Response();
			}
			catch(e) { }
		}
	}
}

function GetEvents_Response()
{
	var XMLResp=XMLHttp.responseXML;
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	if (Error==0)
	{
		var Combo = document.getElementById('x_Events');
		
		if (Combo)
		{
			var Arr_Code = XMLRoot.getElementsByTagName('code');
			var Arr_Name = XMLRoot.getElementsByTagName('name');
			
			for (i = Combo.length - 1; i>=0; --i) 
				Combo.remove(i);

			for (i=0;i<Arr_Code.length;++i)
				Combo.options[i] = new Option(Arr_Name.item(i).firstChild.data,Arr_Code.item(i).firstChild.data);
		}
	}
	XMLHttp = CreateXMLHttpRequestObject();
	GetMatches();
}



function GetSchedule() 
{
	if (XMLHttp)
	{
		try
		{

			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				document.getElementById('lu').value=0;
				mRead = new Array();
				mUpdate = new Array();
				clearTimeout(t);
				var useHHT = (document.getElementById('useHHT').checked==true ? 1:0);
				var onlyToday = (document.getElementById('onlyToday').checked==true ? 1:0);
				XMLHttp.open("GET","GetSchedule.php?useHHT="+useHHT+"&onlyToday="+onlyToday,true);
				XMLHttp.onreadystatechange=GetSchedule_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e) { }
	}
}

function GetSchedule_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				GetSchedule_Response();
			}
			catch(e) { }
		}
	}
}

function GetSchedule_Response()
{
	var XMLResp=XMLHttp.responseXML;
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	if (Error==0)
	{
		var Combo = document.getElementById('x_Schedule');
		
		if (Combo)
		{
			var Arr_Code = XMLRoot.getElementsByTagName('val');
			var Arr_Name = XMLRoot.getElementsByTagName('display');
			
			for (i = Combo.length - 1; i>=0; --i) 
				Combo.remove(i);

			for (i=0;i<Arr_Code.length;++i)
				Combo.options[i] = new Option(Arr_Name.item(i).firstChild.data,Arr_Code.item(i).firstChild.data);
		}
		XMLHttp = CreateXMLHttpRequestObject();
		GetEvents();
	}
}


function GetMatches() 
{
	if (XMLHttp)
	{
		try
		{

			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				clearTimeout(t);
				var schedule = document.getElementById('x_Schedule').value;
				var serverDate = document.getElementById('lu').value;
				
				var events = '';
				var evList = document.getElementById('x_Events').options;
				for (i=0;i<evList.length;++i)
				{
					if(evList[i].selected)
						events=events + "&events[]="+evList[i].value;
				}
				
				XMLHttp.open("GET","GetMatches.php?schedule="+schedule+"&serverDate="+serverDate+events,true);
				XMLHttp.onreadystatechange=GetMatches_StateChange;
				XMLHttp.send(null);
			}
		}
		catch (e) {
			t = setTimeout("GetMatches()",UpdateTimeout);
		}
	}
}

function GetMatches_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				GetMatches_Response();
			}
			catch(e) 
			{ 
				t = setTimeout("GetMatches()",UpdateTimeout);
			}
		}
	}
}

function GetMatches_Response()
{
	var XMLResp=XMLHttp.responseXML;
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;
	var tbody=document.getElementById('tbody');
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	if (Error==0)
	{
		var Arr_id = XMLRoot.getElementsByTagName('id');
		var Arr_ev = XMLRoot.getElementsByTagName('ev');
		var Arr_ph = XMLRoot.getElementsByTagName('ph');
		var Arr_t = XMLRoot.getElementsByTagName('t');
		var Arr_n1 = XMLRoot.getElementsByTagName('n1');
		var Arr_cn1 = XMLRoot.getElementsByTagName('cn1');
		var Arr_ar1 = XMLRoot.getElementsByTagName('ar1');
		var Arr_sar1 = XMLRoot.getElementsByTagName('sar1');
		var Arr_n2 = XMLRoot.getElementsByTagName('n2');
		var Arr_cn2 = XMLRoot.getElementsByTagName('cn2');
		var Arr_ar2 = XMLRoot.getElementsByTagName('ar2');
		var Arr_sar2 = XMLRoot.getElementsByTagName('sar2');
		var Arr_sp1 = XMLRoot.getElementsByTagName('sp1');
		var Arr_sp2 = XMLRoot.getElementsByTagName('sp2');
		var Arr_s = XMLRoot.getElementsByTagName('s');
		var Arr_lu = XMLRoot.getElementsByTagName('lu');
		var Arr_f = XMLRoot.getElementsByTagName('f');
		
		
		if(Arr_id.length!=0)
		{
			for (i = tbody.rows.length - 2; i>=0; --i) 
				tbody.deleteRow(i);
			
			for (i=0;i<Arr_id.length;++i)
			{
				var NewRow = document.createElement('TR');
				NewRow.id='_' + Arr_ev.item(i).firstChild.data + '_' + Arr_id.item(i).firstChild.data;
				NewRow.onclick=isRead;
				
				var TD_Status = document.createElement('TD');
				TD_Status.id='Status' + NewRow.id;
				TD_Status.className='Center';
				TD_Status.innerHTML='&nbsp;'+
					'<input type="hidden" id="lu' + NewRow.id + '" value="' + Arr_lu.item(i).firstChild.data + '">' +
					'<input type="hidden" id="f' + NewRow.id + '" value="' + Arr_f.item(i).firstChild.data + '">' +
					Math.min(Arr_ar1.item(i).firstChild.data,Arr_ar2.item(i).firstChild.data) +
					(Math.max(Arr_sar1.item(i).firstChild.data,Arr_sar2.item(i).firstChild.data)>0 ? '+'+Math.min(Arr_sar1.item(i).firstChild.data,Arr_sar2.item(i).firstChild.data):'');
				NewRow.appendChild(TD_Status);
				
				var TD_Event = document.createElement('TD');
				TD_Event.className='Center';
				TD_Event.innerHTML=Arr_ev.item(i).firstChild.data + " - " + Arr_ph.item(i).firstChild.data;
				NewRow.appendChild(TD_Event);
				
				var TD_Tgt = document.createElement('TD');
				TD_Tgt.className='Center';
				TD_Tgt.innerHTML=Arr_t.item(i).firstChild.data;
				NewRow.appendChild(TD_Tgt);
	
				var TD_N1 = document.createElement('TD');
				TD_N1.innerHTML='<span class="big">' + Arr_n1.item(i).firstChild.data + '</span><br>' + Arr_cn1.item(i).firstChild.data;
				NewRow.appendChild(TD_N1);
				
				var TD_s = document.createElement('TD');
				TD_s.innerHTML=Arr_s.item(i).firstChild.data;
				TD_s.className='score';
				NewRow.appendChild(TD_s);
				
				var TD_sp = document.createElement('TD');
				TD_sp.innerHTML=Arr_sp1.item(i).firstChild.data + '<br>' + Arr_sp2.item(i).firstChild.data;
				NewRow.appendChild(TD_sp);
				
				var TD_N2 = document.createElement('TD');
				TD_N2.innerHTML='<span class="big">' + Arr_n2.item(i).firstChild.data + '</span><br>' + Arr_cn2.item(i).firstChild.data;
				NewRow.appendChild(TD_N2);
	
				tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
				
				if(mRead[NewRow.id]==1 && mUpdate[NewRow.id]==Arr_lu.item(i).firstChild.data)
					document.getElementById(NewRow.id).className='read-row';
				else
					mRead[NewRow.id]=0;
			}
		}
		else if (document.getElementById('lu').value==0)
		{
			for (i = tbody.rows.length - 2; i>=0; --i) 
				tbody.deleteRow(i);
		}
		document.getElementById('lu').value=XMLRoot.getElementsByTagName('serverDate').item(0).firstChild.data;
	}
	showTimeout();	
	t = setTimeout("GetMatches()",UpdateTimeout);
}

function showTimeout()
{
	var tbody=document.getElementById('tbody');
	for (i = tbody.rows.length - 2; i>=0; --i) 
	{
		if(document.getElementById("f" + tbody.rows.item(i).id).value==2) {
			document.getElementById(tbody.rows.item(i).id).className='finished-now-col';
		} else if(document.getElementById("f" + tbody.rows.item(i).id).value==3) {
			document.getElementById(tbody.rows.item(i).id).className='shootoff-now-col';
		} else if(document.getElementById("f" + tbody.rows.item(i).id).value==1) {
			document.getElementById(tbody.rows.item(i).id).className='read-row';
			mRead[tbody.rows.item(i).id] = 1;
			mUpdate[tbody.rows.item(i).id] = document.getElementById("lu" + tbody.rows.item(i).id).value;
		} else if(mRead[tbody.rows.item(i).id]==0) {
			updateTime=document.getElementById('lu').value-document.getElementById('lu' + tbody.rows[i].id).value;

			if(updateTime<30)
				document.getElementById("Status" + tbody.rows.item(i).id).className='Update0 Center';
			else if(updateTime<60)
				document.getElementById("Status" + tbody.rows.item(i).id).className='Update1 Center';
			else if(updateTime<90)
				document.getElementById("Status" + tbody.rows.item(i).id).className='Update2 Center';
			else
				document.getElementById("Status" + tbody.rows.item(i).id).className='Center';
		}
	}
}

function isRead()
{
	if(mRead[this.id]!=1)
	{
		document.getElementById(this.id).className='read-row';
		document.getElementById("Status" + this.id).className='';
		mRead[this.id]=1;
		mUpdate[this.id]=document.getElementById("lu" + this.id).value;
	}
	else
	{
		document.getElementById(this.id).className='';
		mRead[this.id]=0;
	}
	showTimeout();
}

function pauseRefresh()
{
	var isPaused=document.getElementById('pauseUpdate').checked;
	if(isPaused)
		clearTimeout(t);
	else
		GetMatches();
}

function showOptions()
{
	document.getElementById('options').hidden=!document.getElementById('options').hidden;
}

function SelectAllOpt(Sel)
{
	var Opt = document.getElementById('x_Events').options;
	for (i=0;i<Opt.length;++i)
		Opt[i].selected=true;
	document.getElementById('lu').value=0;
	if(document.location.href.indexOf('qualification.php')>-1)
		GetResults();
	else
		GetMatches();

}

function GetQualEvents()
{
	if (XMLHttp)
	{
		try
		{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
			{
				document.getElementById('lu').value=0;
				mRead = new Array();
				mUpdate = new Array();
				clearTimeout(t);
				var isEvent = (document.getElementById('isEvent1').checked ? 1 : 0);
				var viewTeam = (document.getElementById('viewTeam').checked ? 1 : 0);
				var viewInd = (document.getElementById('viewInd').checked ? 1 : 0);
				var viewSnap = (document.getElementById('viewIndSnap').checked ? 1 : 0);
				XMLHttp.open("GET","GetQualificationEvents.php?isEvent="+isEvent+"&viewTeam="+viewTeam+"&viewInd="+viewInd+"&viewSnap="+viewSnap,true);
				XMLHttp.onreadystatechange=GetQualEvents_StateChange;
				XMLHttp.send(null);
			}
			
		}
		catch (e) { }
	}
}

function GetQualEvents_StateChange()
{
	if (XMLHttp.readyState==XHS_COMPLETE)
	{
		if (XMLHttp.status==200)
		{
			try
			{
				GetQualEvents_Response();
			}
			catch(e) { }
		}
	}
}

function GetQualEvents_Response()
{
	var XMLResp=XMLHttp.responseXML;
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;
	
	var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	if (Error==0)
	{
		var Combo = document.getElementById('x_Events');
		
		if (Combo)
		{
			var Arr_Code = XMLRoot.getElementsByTagName('code');
			var Arr_Name = XMLRoot.getElementsByTagName('name');
			
			for (i = Combo.length - 1; i>=0; --i) 
				Combo.remove(i);

			for (i=0;i<Arr_Code.length;++i)
				Combo.options[i] = new Option(Arr_Name.item(i).firstChild.data,Arr_Code.item(i).firstChild.data);
		}
	}
	XMLHttp = CreateXMLHttpRequestObject();
	GetResults();
}

function GetResults() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try	{
			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
				clearTimeout(t);
				var isEvent = (document.getElementById('isEvent1').checked ? 1 : 0);
				var numPlaces = document.getElementById('numPlaces').value;
				var serverDate = document.getElementById('lu').value;
				var tmpEvents = document.getElementById('x_Events');
				var viewTeam = (document.getElementById('viewTeam').checked ? 1 : 0);
				var viewInd = (document.getElementById('viewInd').checked ? 1 : 0);
				var viewSnap = (document.getElementById('viewIndSnap').checked ? 1 : 0);
				var comparedTo = document.getElementById('comparedTo').value;
				var evtList = '';
				for(var i=0; i<tmpEvents.length; i++) {
					if(tmpEvents.options[i].selected==1)
						evtList += (evtList!='' ? '|' : '') + tmpEvents.options[i].value;
				}
				XMLHttp.open("GET","GetQualificationResults.php?isEvent="+isEvent+"&evtList="+evtList+"&numPlaces="+numPlaces+"&serverDate="+serverDate+"&viewTeam="+viewTeam+"&viewInd="+viewInd+"&viewSnap="+viewSnap+"&comparedTo="+comparedTo,true);
				XMLHttp.send(null);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						if (!XMLResp || !XMLResp.documentElement)
							throw(XMLResp.responseText);
						
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
							throw("");

						XMLRoot = XMLResp.documentElement;

						var tbody=document.getElementById('tbody');
						
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
						if (Error==0) {
							var Arr_st = XMLRoot.getElementsByTagName('st');
							var Arr_sc = XMLRoot.getElementsByTagName('sc');
							var Arr_sn = XMLRoot.getElementsByTagName('sn');
							var Arr_slu = XMLRoot.getElementsByTagName('slu');
							var Arr_id = XMLRoot.getElementsByTagName('id');
							var Arr_itgt = XMLRoot.getElementsByTagName('itgt');
							var Arr_irk = XMLRoot.getElementsByTagName('irk');
							var Arr_oldrk = XMLRoot.getElementsByTagName('oldrk');
							var Arr_ia = XMLRoot.getElementsByTagName('ia');
							var Arr_icn = XMLRoot.getElementsByTagName('icn');
							var Arr_is = XMLRoot.getElementsByTagName('is');
							var Arr_isg = XMLRoot.getElementsByTagName('isg');
							var Arr_isx = XMLRoot.getElementsByTagName('isx');
							var Arr_ish = XMLRoot.getElementsByTagName('ish');
							var Arr_isnote = XMLRoot.getElementsByTagName('isnote');
							
							if(Arr_sc.length!=0) {
								for (i = tbody.rows.length - 2; i>=0; --i) 
									tbody.deleteRow(i);
								
								var lastCat = Arr_st.item(0).firstChild.data+Arr_sc.item(0).firstChild.data;
								for (i=0;i<Arr_sc.length;++i) {
									if(lastCat!=Arr_st.item(i).firstChild.data+Arr_sc.item(i).firstChild.data) {
										var NewRow = document.createElement('TR');
										NewRow.class='Divider'
										var td_Divider=document.createElement('TH');
										td_Divider.colspan="12";
										td_Divider.innerHTML='<input type="hidden" id="lu' + NewRow.id + '" value="' + Arr_slu.item(i).firstChild.data + '">'+
											'<input type="hidden" id="f' + NewRow.id + '" value="0">';
										NewRow.appendChild(td_Divider);
										tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
										lastCat = Arr_st.item(i).firstChild.data+Arr_sc.item(i).firstChild.data;
									}
									var NewRow = document.createElement('TR');
									NewRow.id='_' + Arr_sc.item(i).firstChild.data + '_' + Arr_id.item(i).firstChild.data;
									NewRow.onclick=isRead;
									NewRow.style.lineHeight = '24px';
									
									var TD_Status = document.createElement('TD');
									TD_Status.id='Status' + NewRow.id;
									TD_Status.className='Center';
									TD_Status.innerHTML='&nbsp;'+
										'<input type="hidden" id="lu' + NewRow.id + '" value="' + Arr_slu.item(i).firstChild.data + '">'+
										'<input type="hidden" id="f' + NewRow.id + '" value="0">';
									NewRow.appendChild(TD_Status);
									
									var TD_Event = document.createElement('TD');
									TD_Event.className = 'Center';
									TD_Event.innerHTML = Arr_sc.item(i).firstChild.data + '-' + Arr_sn.item(i).firstChild.data;
									NewRow.appendChild(TD_Event);
									
									var TD_Tgt = document.createElement('TD');
									TD_Tgt.className='Center';
									TD_Tgt.innerHTML=Arr_itgt.item(i).firstChild.data;
									NewRow.appendChild(TD_Tgt);
									
									var TD_Rank = document.createElement('TD');
									TD_Rank.className='Right big';
									TD_Rank.colSpan=(comparedTo!=0 ? 1:2);
									TD_Rank.innerHTML=Arr_irk.item(i).firstChild.data;
									NewRow.appendChild(TD_Rank);
									
									if(comparedTo!=0) {
										var TD_oldRank = document.createElement('TD');
										TD_oldRank.className='Center ';
										if(Arr_oldrk.item(i).firstChild.data == 0) {
											TD_oldRank.innerHTML = '&nbsp;';
										} else {
											var arrImg = 'Minus.png';
											TD_oldRank.innerHTML = '&nbsp;&nbsp;';
											if(Arr_oldrk.item(i).firstChild.data != Arr_irk.item(i).firstChild.data) {
												TD_oldRank.innerHTML = Arr_oldrk.item(i).firstChild.data;
												if(Arr_oldrk.item(i).firstChild.data > Arr_irk.item(i).firstChild.data) 
													arrImg = 'Up.png';
												else
													arrImg = 'Down.png';
											}
											TD_oldRank.style.background = 'url('+RootDir+'Common/Images/' + arrImg + ')';
											TD_oldRank.style.backgroundRepeat = 'no-repeat';
											TD_oldRank.style.backgroundPosition = 'center';
											//TD_oldRank.style.backgroundSize= 'contain';
											TD_oldRank.style.color= '#FFFFFF';
											TD_oldRank.style.fontWeight= 'bold';
										}
										NewRow.appendChild(TD_oldRank);
									} 
									
									var TD_Ath = document.createElement('TD');
									TD_Ath.className='big';
									TD_Ath.innerHTML=Arr_ia.item(i).firstChild.data;
									NewRow.appendChild(TD_Ath);
									
									var TD_team = document.createElement('TD');
									TD_team.innerHTML=Arr_icn.item(i).firstChild.data;
									NewRow.appendChild(TD_team);
									
									var TD_s = document.createElement('TD');
									TD_s.innerHTML=Arr_is.item(i).firstChild.data;
									TD_s.className='score Right';
									NewRow.appendChild(TD_s);
									
									var TD_sg = document.createElement('TD');
									TD_sg.innerHTML=Arr_isg.item(i).firstChild.data;
									TD_sg.className='Right';
									NewRow.appendChild(TD_sg);
									
									var TD_sx = document.createElement('TD');
									TD_sx.innerHTML=Arr_isx.item(i).firstChild.data;
									TD_sx.className='Right';
									NewRow.appendChild(TD_sx);
									
									var TD_sh = document.createElement('TD');
									TD_sh.innerHTML='(' + Arr_ish.item(i).firstChild.data + ')';
									TD_sh.className='Right';
									NewRow.appendChild(TD_sh);
									
									var TD_Note = document.createElement('TD');
									TD_Note.className='big Center';
									TD_Note.innerHTML=Arr_isnote.item(i).firstChild.data;
									NewRow.appendChild(TD_Note);
									
									tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
									
									if(mRead[NewRow.id]==1 && mUpdate[NewRow.id]==Arr_slu.item(i).firstChild.data)
										document.getElementById(NewRow.id).className='read-row';
									else
										mRead[NewRow.id]=0;
								}
							}
							else if (document.getElementById('lu').value==0) {
								for (i = tbody.rows.length - 2; i>=0; --i) 
									tbody.deleteRow(i);
							}
							document.getElementById('lu').value=XMLRoot.getElementsByTagName('serverDate').item(0).firstChild.data;
						}
						showTimeout();	
						t = setTimeout("GetResults()",UpdateTimeout);
					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}
				};
				XMLHttp.send(null);
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

