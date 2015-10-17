Ext.ns('speaker');

speaker.Filter=Ext.extend(Ext.FormPanel,
{
	timers: new Array()
	,colors: new Array()

	,timersSet: null
	,colorsSet: null
	,eventsSet: null
	,colorSelectorsSet: null
	
	,scheduleCombo: null
	,scheduleRecord: null
	,scheduleStore: null
	
/**
 * Preleva i parametri e li mette nelle caselle di testo
 * 
 * @access private
 */
	,populateParameters: function()
	{
		var me=this;
		
		var i;
		
		for (i=0;i<me.timers.length;++i)
		{
			Ext.getCmp('timer_' + i).setValue(me.timers[i])
		}
		
		for (i=0;i<me.colors.length;++i)
		{
			Ext.getCmp('color_' + i).setValue(me.colors[i])
		}
	}

/**
 * Prepara i fieldset
 * 
 * @access: private
 */
	,initSets: function()
	{
		var me=this;
		
	// timer
		me.timersSet=
		{	
		 	columnWidth: .45
		 	,xtype: 'fieldset'
		 	,labelWidth: 60
		 	//,defaultType: 'textfield'
		 	,defaults:
		 	{
	 			xtype: 'textfield'
	 			,width: '50px'
		 	}
			,autoHeight: true
			,border: false
			,items:
	 		[
				{
					id: 'timer_0'
					,fieldLabel: 'T1 (sec)'
					,value: ''
				}
				,{
					id: 'timer_1'
					,fieldLabel: 'T2 (sec)'
					,value: ''
				}
				,{
					id: 'timer_2'
				 	,fieldLabel: 'T3 (sec)'
				 	,value: ''
				}
				,{
					id: 'timer_3'
				 	,fieldLabel: 'T4 (sec)'
				 	,value: ''
				}
	 		]
	 	};
		
	// colori
		me.colorsSet=
		{	
		 	columnWidth: .30
		 	,xtype: 'fieldset'
		 	,labelWidth: 30
	 		//,defaultType: 'textfield'
		 	,defaults:
		 	{
	 			xtype: 'textfield'
	 			,width: '50px'
		 	}
			,autoHeight: true
			,border: false
			,items:
	 		[
				{
			 		id: 'color_0'
					,fieldLabel: 'C1'
					,value: ''
			 	}
				,{
					id: 'color_1'
					,fieldLabel: 'C2'
					,value: ''
				}
				,{
					id: 'color_2'
				 	,fieldLabel: 'C3'
				 	,value: ''
				}
				
	 		]
	 	};
		
		var img=WebDir + 'Common/Images/sel.gif';
	// selettori colori
		me.colorSelectorSet=
		{
			columnWidth: .25
		 	,xtype: 'fieldset'
		 	,autoHeight: true
		 	,border: false
		 	,labelWidth: 0
	 		,labelSeparator: ''
	 		,items:
	 		[
	 		 	{
	 		 		html: '<div style="height: 25px; padding-top: 3px;"><img id="sel_0" src="' + img + '"/></div>'
	 		 	}
	 		 	,{
	 		 		html: '<div style="height: 25px; padding-top: 3px;"><img id="sel_1" src="' + img + '"/></div>'
	 		 	}
	 		 	,{
	 		 		html: '<div style="height: 25px; padding-top: 3px;"><img id="sel_2" src="' + img + '"/></div>'
	 		 	}
	 		]
		};
		
	// eventi
		/*me.eventsSet=
		{
			xtype: 'fieldset'
			,labelWidth: 0	
			,labelSeparator: ''
			//,columns: 1
			,items: [{hidden: true}]
		}*/
		
		me.eventsSet=new Ext.form.FieldSet(
		{
			
			//,columns: 1
			autoHeight: true
			,items: [{hidden: true}]
		});
	}
	
/**
 * Distrugge le checkbox degli eventi
 * 
 * @access private
 */
	,resetEventsSet: function()
	{
		var me=this;
		
		var checkboxes=Ext.query('*[id^=ev_]');
		var labels=Ext.query('*[for^=ev_]');
		
		//console.debug(checkboxes);
	// ranco le checkbox
		if (checkboxes.length>0)
		{
			Ext.each(checkboxes,function(c)
			{
				//console.debug(c.id);
				var cmp=Ext.getCmp(c.id);
				
				me.eventsSet.remove(cmp);
				
				
			});
		}
	// ranco le label	
		if (labels.length>0)
		{
			Ext.each(labels,function(c)
			{
				//console.debug(c.id);
				var cmp=Ext.removeNode(c);
			});
		}
		
		me.eventsSet.doLayout();
	}	
	
// @override
	,initComponent: function()
	{
		var me=this;
		
		me.scheduleRecord=Ext.data.Record.create(
		[
		 	{name: 'val', mapping: 'val'},
		 	{name: 'display', mapping: 'display'}
		]);
		
		me.scheduleStore=new Ext.data.Store(
		{
			storeId: 'scheduleStore'
			,reader: new Ext.data.XmlReader(
			{
				record: 'schedule'
				,id: 'val'
			},me.scheduleRecord)
		});
			
		me.scheduleCombo=
		{
			xtype: 'combo'
			,id: 'schedule'
			,fieldLabel: StrSchedule
			,width: 230
            ,triggerAction: 'all'
            ,mode: 'local'
            ,editable: false
            ,store: me.scheduleStore
            ,valueField: 'val'
            ,displayField: 'display'
            ,listeners:
            {
				select: function(combo,record,index)
				{
					me.resetEventsSet();
					
					Ext.Ajax.request(
					{
						url: 'actions/xmlGetEventsInSchedule.php'
						,method: 'POST'
						,params: 
						{
							schedule: combo.getValue()
						}
						,success: function(response)
						{
							var xml=response.responseXML;
							var dq=Ext.DomQuery;
							
							var error=dq.selectNode('error',xml).firstChild.data;
							if (error==0)
							{
								var team=dq.selectNode('team',xml).firstChild.data;
								Ext.getCmp('team').setValue(team);
								
								var events=dq.select('event',xml);
								//console.debug(events);
								
							/*
							 * Creo le checkbox degli eventi e le aggiungo a me.eventsSet
							 */
								Ext.each(events,function(ev)
								{
									//console.debug(ev.firstChild.data);
									var c=new Ext.form.Checkbox(
									{
										id: 'ev_' + ev.firstChild.data
										,fieldLabel: ev.firstChild.data
										,inputValue: ev.firstChild.data
									});
									c.checked=true;
									
									me.eventsSet.add(c);
									
									//console.debug(me.eventsSet);
								});
								
								me.eventsSet.doLayout();
							}
						}
					});
				}
            }
		};
		
		me.initSets();
			
		Ext.apply(this,
		{
			frame: true
			
			,width: 350
			
			,items:
			[
			 	{
				 	layout: 'column'
				 	,items:
				 	[
				 	 	me.timersSet
				 	 	,me.colorsSet
				 	 	,me.colorSelectorSet
				 	]
			 	}
			 	
			 	,me.scheduleCombo
			 	,me.eventsSet
			 	,{
			 		xtype: 'hidden'
			 		,id: 'team'
			 		,value: 0
			 	}
			 	,{
			 		xtype: 'hidden'
			 		,id: 'serverDate'
			 		,value: 0
			 	}
			]
			,buttons:
			[
			 	{
			 		text: StrOk
			 		,handler: function()
			 		{
			 			if(Ext.getCmp('schedule').getValue()=='')
			 				return;
			 			
			 		
			 			var checkboxes=Ext.query('*[id^=ev_]');
			 			
			 		/* parametri per la request */	
			 			
			 			var events=new Array();
			 			Ext.each(checkboxes,function(c)
			 			{
			 				//console.debug(c.id + ' ' + c.checked);
			 				if (c.checked)
			 					events.push(c.value)
			 			});
			 			
			 			//console.debug(events);
			 			
			 			var team=Ext.getCmp('team').getValue();
			 			
			 			var schedule=Ext.getCmp('schedule').getValue().substr(1);
			 			
			 			var serverDate=0;
			 			
			 			var parameters='';
			 			var ts=Ext.select('*[id^=timer_]').elements;
			 			var cs=Ext.select('*[id^=color_]').elements;
			 			//console.debug(ts.elements);
			 			Ext.each(ts,function(item)
			 			{
			 				var t=item.value;
			 				var c='#';
			 				
			 				var idx=item.id.split('_')[1];
			 				
			 				if (Ext.get('color_' + idx))
			 				{
			 					c=cs[idx].value;
			 				}
			 				
			 				parameters+=t + '|' + c + ';';
			 				
			 				me.timers[idx]=t;
			 				me.colors[idx]=c;
			 			});
			 			
			 			parameters=parameters.substr(0,parameters.length-1);
			 			//console.debug(parameters);
			 			//return;
			 			
			 			if (task.taskRunCount>0)
			 				Ext.TaskMgr.stop(task);
			 			
			 			task.params['events[]']=events;
			 			task.params.team=team;
			 			task.params.schedule=schedule;
			 			task.params.serverDate=serverDate;
			 			task.params.parameters=parameters;
			 			
			 		/*
			 		 * voglio questo per permettere al task di considerarla una richiesta con l'Ok
			 		 */
			 			task.reads=null;
			 			
			 			Ext.TaskMgr.start(task);
			 			
			 			/*Ext.Ajax.request(
			 			{
			 				url: 'actions/xmlGetMatches.php'
			 				,method: 'POST'
			 				,params: 
			 				{
			 					'events[]': events
			 					,team: team
			 					,schedule: schedule
			 					,parameters: parameters
			 				}
			 				,success: function(response)
			 				{
			 					
			 				}
			 			});*/
			 		}
			 	}
			]
		});
		
		speaker.Filter.superclass.initComponent.call(this);
		
	}

// @override
/**
 * Inizializza gli eventi
 */
	,initEvents: function()
	{
		var me=this;
		
		speaker.Filter.superclass.initEvents.call(me);
		
		me.populateParameters();	//TODO da gestire usando gli eventi
		
	}
	
/**
 * Imposta i parameteri
 * 
 * @access: public
 */
	,setParameters: function(timers,colors)
	{
		var me=this;
		
		me.timers=timers;
		me.colors=colors;
	}
	
	,getTimers: function()
	{
		var me=this;
		
		return me.timers; 
	}
	
	,getColors: function()
	{
		var me=this;
		
		return me.colors; 
	}
});

