Ext.ns('speaker');

speaker.View=Ext.extend(Ext.grid.GridPanel,
{
	oFilter: null
	,record: null
	,store: null
	
	
	,nameRenderer: function(nameIndex) 
	{
		return function(value, meta, record)
		{		
			if (record.get('read')==1)
			{
				meta.css="read-col";
			}
			else if (record.get('finished')==2)
			{
				meta.css="finished-now-col";
			}
			
			return String.format(
					'<span class="name">{0}</span><br/>{1}',record.get('name'+nameIndex),record.get('countryName'+nameIndex)
			);
		}
	}	

	,setPointsRenderer: function(value, meta, record)
	{
		if (record.get('read')==1)
		{
			meta.css="read-col";
		}
		else if (record.get('finished')==2)
		{
			meta.css="finished-now-col";
		}
		
		var s1=record.get('setPoints1').replace(/\|/g,' ');
		var s2=record.get('setPoints2').replace(/\|/g,' ');
		
		return String.format(
				'<span class="setPoints">{0}</span><br/><span class="name">{1}</span>',s1,s2
		);
	}
	
	,scoreRenderer: function(value, meta, record)
	{
		if (record.get('read')==1)
		{
			meta.css="read-col";
		}
		else if (record.get('finished')==2)
		{
			meta.css="finished-now-col";
		}
		
		return '<span class="score">' + value + '</span>';
	}
	
	,standardRenderer: function(value, meta, record)
	{
		if (record.get('read')==1)
		{
			meta.css="read-col";
		}
		else if (record.get('finished')==2)
		{
			meta.css="finished-now-col";
		}
		
		return '<span class="big">' + value + '</span>';
	}
	
	,statusRenderer: function(me)
	{
		return function(value, meta, record)
		{
			var sd=Ext.getCmp('serverDate').getValue();
			var lu=record.get('lastUpdate');
			
			var v=Math.abs(sd-lu);
			
			/*var t0=Ext.getCmp('timer_0').getValue();
			var t1=Ext.getCmp('timer_1').getValue();
			var t2=Ext.getCmp('timer_2').getValue();
			var t3=Ext.getCmp('timer_3').getValue();*/
			
			var timers=me.oFilter.getTimers();
			var colors=me.oFilter.getColors();
			
			var t0=timers[0];
			var t1=timers[1];
			var t2=timers[2];
			var t3=timers[3];
			
			var c0=colors[0];
			var c1=colors[1];
			var c2=colors[2];
			
			if (v>=0 && v<=t0)
			{
				//return '<div style="background-color: #' + Ext.getCmp('color_0').getValue() + ';"> </div>';
				meta.attr='style="background-color: #' + c0 + ';"';
			}
			else if (v>t0 && v<=t1)
			{
				//return '<div style="background-color: #' + Ext.getCmp('color_1').getValue() + ';"> </div>';
				meta.attr='style="background-color: #' + c1 + ';"';
			}
			else if (v>t1 && v<=t2)
			{
				//return '<div style="background-color: #' + Ext.getCmp('color_2').getValue() + ';"> </div>';
				meta.attr='style="background-color: #' + c2 + ';"';
			}	
			else
			{
				//return '';
				meta.css='';
			}
			
		}
	}
	
	,readRenderer: function(value, meta, record)
	{
		var img='';
		var id='read_'+record.get('id');
	
		switch (value)
		{
			case '0':
				img='drop.png';
				break;
			case '1':
				img='status-ok.gif'; 
				break;
		}
		
		if (value==1)
		{
			meta.css="read-col";
		}
		else if (record.get('finished')==2)
		{
			meta.css="finished-now-col";
		}
		
		var html
			= '<div class="control-btn">'
				+ '<img id="'  + id + '" src="'+WebDir+'Common/Images/' + img + '"  title="" width="16" height="16">'
			+ '</div>';
			
		return html;
	}
			
// @override
	,initComponent: function()
	{
		var me=this;
		
		/*me.record=Ext.data.Record.create(
		[
		 	{name: 'target', mapping: 't'},
		 	{name: 'event', mapping: 'ev'},
		 	{name: 'eventName', mapping: 'evn'},
		 	{name: 'name1', mapping: 'n1'},
		 	{name: 'countryName1', mapping: 'cn1'},
		 	{name: 'name2', mapping: 'n2'},
		 	{name: 'countryName2', mapping: 'cn2'},
		 	{name: 'score', mapping: 's'},
		 	{name: 'setPoints1', mapping: 'sp1'},
		 	{name: 'setPoints2', mapping: 'sp2'},
		 	{name: 'lastUpdate', mapping: 'lu'}
		]);*/
		
		/*me.store=new Ext.data.Store(
		{
			storeId: 'viewStore'
			,reader: new Ext.data.XmlReader(
			{
				record: 'm'
			},me.record)
		});*/
		
		me.store=new speaker.XmlStore({storeId: 'viewStore'});
		
		Ext.apply(me,
		{
			id: 'view'
			,sm: new Ext.grid.RowSelectionModel({singleSelect: true})
			,cm: new Ext.grid.ColumnModel(
			{
				columns:
				[
				 	{header: '', id: 'colRead', dataIndex: 'read',width: 50, resizable: false, renderer: me.readRenderer},
				 	{header: StrStatus, id: 'colStatus', dataIndex: null,width: 50, resizable: false, renderer: me.statusRenderer(me)},
				 	{header: StrEvent, id: 'colEvent', dataIndex: 'event', width: 60, resizable: true, renderer: me.standardRenderer },
				 	{header: StrTarget, id: 'colTarget', dataIndex: 'target', width: 90, resizable: true, renderer: me.standardRenderer},
				 	{header: '', id: 'colName1', dataIndex: 'name1', width: 180, resizable: true, renderer: me.nameRenderer(1)},
				 	{header: '', id: 'colCountryName1', dataIndex: 'countryName1', hidden: true},	// hidden perchè in realtà non è in colonna
				 	{header: StrScore, id: 'colScore', dataIndex: 'score', width: 80, resizable: true, renderer: me.scoreRenderer},
				 	{header: StrSetPoints, id: 'colSetPoints', width: 150, resizable: true, renderer: me.setPointsRenderer},	
				 	{header: '', id: 'colSetPoints1', dataIndex: 'setPoints1', hidden: true},	// hidden perchè in realtà non è in colonna
				 	{header: '', id: 'colSetPoints2', dataIndex: 'setPoints2', hidden: true}, 	// hidden perchè in realtà non è in colonna
				 	{header: '', id: 'colName2', dataIndex: 'name2', width: 180, resizable: true, renderer: me.nameRenderer(2)},
				 	{header: '', id: 'colCountryName2', dataIndex: 'countryName2', hidden: true}	// hidden perchè in realtà non è in colonna
				 	
				 	/*{header: '', id: 'colRead', dataIndex: null,width: 30, sizeble: true}*/
				]
			})
			,store: me.store
			,viewConfig:
			{
				//forceFit: true,
				//,enableRowBody:true		// per il doppio tr
			// questo mi serve per mantenere lo scroll
				onLoad: Ext.emptyFn
				,listeners: 
				{
					beforerefresh: function(v) 
					{
            			v.scrollTop = v.scroller.dom.scrollTop;
            			v.scrollHeight = v.scroller.dom.scrollHeight;
					}
					,refresh: function(v) 
					{
						v.scroller.dom.scrollTop = v.scrollTop +(v.scrollTop == 0 ? 0 : v.scroller.dom.scrollHeight - v.scrollHeight);
					}
				}
		// questo mi serve per cambiare lo stile delle righe lette
				/*,getRowClass: function(record,index)
				{
					var x=record.get('read');
					if (x==1)
						return 'read-row';
					else
						return '';
				}*/
			}
			,stripeRows: true
			,width: 600
			,height: 670
			,title: ''
			,tbar:
			[
			 	{
			 		xtype: 'checkbox'
			 		,id: 'stopRefresh'
			 		,boxLabel: StrStopRefresh
			 		,checked: false
			 		,value: 0
			 	}
			]
			
		});
		
		speaker.View.superclass.initComponent.call(me);
	}
	
// @Override
	,initEvents: function()
	{
		var me=this;
		
		speaker.View.superclass.initEvents.call(me);
		
		me.on('click',me.on_Click,me);
	}
	
	,on_Click: function(e)
	{
		var me=this;
		
		var btn = e.getTarget('.control-btn');
		var row=0;
		var sm=me.getSelectionModel();
		
		if (btn)
		{
		/*
		 * Lo stop e il restart del task mi migliora un po' le
		 * prestazioni del cambio immagine (un po' troppo lento)
		 */
			Ext.TaskMgr.stop(task);
			var rec=sm.getSelected();

			me.changeRead(rec);
			task.reads['r_' + rec.get('id')].value=rec.get('read');
				
			Ext.TaskMgr.start(task);
		}        
	}
	
	,changeRead: function(rec)
	{
		var me=this;
		
		var read=rec.get('read');
		
		if (read==0) 
			read=1;
		else
			read=0;
			
		rec.set('read',read);
		
	}
	
	,bindFilter: function(filter)
	{
		var me=this;
		
		me.oFilter=filter;
	}
	
	
});