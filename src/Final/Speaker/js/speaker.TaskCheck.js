Ext.ns('speaker');


var task=
{
	/*taskEvents: null
	,taskTeam: null
	,taskSchedule: null
	,taskServerDate: null
	,taskParameters: null*/
	params: 
	{
		'events[]': null
		,team: null
		,schedule: null
		,serverDate: null
		,parameters: ''
	}
	
/*
 * All'inizio è null.
 * Se il task viene eseguito la prima volta (si è cliccato "Ok") allora
 * l'oggetto viene popolato con dei literal che conterranno lo stato della riga (l'id è il contatore)
 */
	,reads: null

	,itemFn: function(item)
	{
		var xml='\
			<m>\
				<id>' + item.data.id + '</id>\
				<f>' + item.data.finished + '</f>\
				<r>' + item.data.read + '</r>\
				<ev>' + item.data.event + '</ev>\
				<evn>' + item.data.eventName + '</evn>\
				<t>' + item.data.target + '</t>\
				<n1>' + item.data.name1 + '</n1>\
				<n2>' + item.data.name2 + '</n2>\
				<cn1>' + item.data.countryName1 + '</cn1>\
				<cn2>' + item.data.countryName2 + '</cn2>\
				<sp1>' + item.data.setPoints1 + '</sp1>\
				<sp2>' + item.data.setPoints2 + '</sp2>\
				<s>' + item.data.score + '</s>\
				<lu>' + item.data.lastUpdate + '</lu>\
			</m>\
		';
		
		return xml;
	}

	,run: function()
	{
		var me=this;
		
		var stopRefresh=Ext.getCmp('stopRefresh').checked;
		
		if (stopRefresh)
			return;
			
	/*	var tmp=new Array();
		tmp.push({key: 'xx', value: 'x'},{key: 'yy', value: 'y'});
		
		var p={
			'events[]': me.taskEvents
			,team: me.taskTeam
			,schedule: me.taskSchedule
			,serverDate: me.taskServerDate
			,parameters: me.taskParameters
		};
		
		Ext.each(tmp,function(item)
		{
			p[item.key]=item.value;
		});
		
		console.debug(p);*/
		
		/*var params=
		{
			'events[]': me.taskEvents
			,team: me.taskTeam
			,schedule: me.taskSchedule
			,serverDate: me.taskServerDate
			,parameters: me.taskParameters
		};*/
		
	/*
	 * Dinamicamente aggiungo all'oggetto params le proprietà ottenute
	 * dai flag di lettura; avrò una request con tante var quante sono le righe dello store
	 * più i parametri definiti qui sopra
	 */
		if (me.reads!=null)
		{
			for (r in me.reads)
			{
				if (r!='remove')
				{
					//console.debug(me.reads[r]);
					me.params[me.reads[r].key]=me.reads[r].value;
				}
			}
		}
		
	//	console.debug(params);
		
	/*
	 * non è un "ok" quindi non devo salvare i parametri
	 */
		if (me.taskRunCount>1)
			me.params.parameters='';
		
		Ext.Ajax.request(
		{
			url: 'actions/xmlGetMatches.php'
			,method: 'POST'
			,params: me.params
			
			,success: function(response)
			{
				var xml=response.responseXML;
				
				var dq=Ext.DomQuery;
				
				var error=dq.selectNode('error',xml).firstChild.data;
				if (error==0)
				{
					var store=Ext.StoreMgr.get('viewStore');
					
					var sd=dq.selectNode('serverDate',xml).firstChild.data;
					me.params.serverDate=sd;
					Ext.getCmp('serverDate').setValue(sd);
				
					var reset=dq.selectNode('reset',xml).firstChild.data;
					
					if (reset==1)
					{
						store.removeAll();
					}
					else
					{
						var num=dq.select('m',xml).length;
						
					/*
					 * Devo caricare lo store
					 */
						if (num>0)
						{
							//console.debug('nuovo');
							//me.oldXml=xml;
							
							store.loadData(xml);
							
							//console.debug(store.data);
							
							//console.debug(me.oldXml);
							
						/* 
						 * se me.reads è null provengo da un "Ok" quindi popolo l'array
						 */
							if (me.reads===null)
							{
								me.reads=new Object();
								var records=store.getRange(0,store.getCount()-1);
								
								Ext.each(records,function(r)
								{
									me.reads['r_'+r.get('id')]=
									{
											key: 'r_'+r.get('id')
											,value: r.get('read')	// 0
									};
									//console.debug(me.reads['r_'+r.get('id')]);
								});
							}
						
						}
						else
						{
							oldXml=Ext.util.storeData2Xml(store.data,me.itemFn);
							
							store.loadData(oldXml);							
						}
					}
				}
			}
		});
	}
	,interval: 5000	// sec
}

