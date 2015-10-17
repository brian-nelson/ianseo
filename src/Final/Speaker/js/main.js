Ext.onReady(function()
{
// evito che il backspace torni indietro nella cronologia',
	var keyMap=new Ext.KeyMap(document,
	{
		key: Ext.EventObject.BACKSPACE
		,fn: function(keyCode,e)
		{
			e.preventDefault();
			return;
		}
		,scope: document
	});
	
	var f=new speaker.Filter();
	f.setParameters(timers,colors);
	//f.render('panel');
	
	var v=null;
	
	if (parseInt(isMobile)==0)
	{
		//alert(isMobile);return
		v=new speaker.View({region: 'center'});
	}
	else
	{
		v=new speaker.ViewBBar({region: 'center'});
	}
	
	v.bindFilter(f);
	
/*
 * uso un pannello invece del viewport per poter renderizzare in un div invece che
 * essere costretto nel body
 */
	var vp=new Ext.Viewport(
	{
		layout: 'border'
		,height: 680
		,defaults:{ autoScroll:true }
		,items:
		[
		 	{
		 		xtype: 'panel'
		 		,title: ' '
		 		,region: 'west'
		 		,collapsible: true
		 		,width: 370
		 		,border: false
		 		,frame: true
		 		,items: [f]
		 	}
		 	,v
		]
	});
	vp.render(Ext.getBody());
	//console.debug(v.getGridEl().id);
	
	//Ext.get(v.getGridEl().id).applyStyles("-webkit-box-sizing:border-box;");
	
//XXX da portare assolutamente dall'estensione della form!!!!!!!!!
	Ext.select('img[id^=sel_]').on('click',function()
	{
		var id=this.id.split('_')[1];
		
		var w=winSelColor=new Ext.Window(
		{
			width: 160
			,height: 130
			,frame: true
			,title: 'C' + (parseInt(id)+1)
			,resizable: false
			,items:
			[
			 	new Ext.ColorPalette(
			 	{
			 		listeners:
			 		{
			 			select: function(palette,selColor)
			 			{
			 				Ext.getCmp('color_' + id).setValue(selColor);
			 			}
			 		}
			 	})
			]
		});
		
		w.show();
		
	
		
	});
	
// preparo lo store della combo dello scheduling
	Ext.Ajax.request(
	{
		url: 'actions/xmlGetFinScheduling.php'
		,method: 'POST'
		,success: function(response)
		{
			var xml=response.responseXML;
			
			var store=Ext.StoreMgr.get('scheduleStore').loadData(xml)
		}
	});
	
	

	
},window);