Ext.ns('partecipantsApp');

partecipantsApp.EventsAccess=function(p)
{
	var parent=p;
	
	var win=null;
	
	var row=null;
	
	function setupWin()
	{
		win=new Ext.Window
		(
			{
				title: '<div align="center">' + StrEventAccess + '<div>',
				constrainHeader:true,
				width:220,
				height:230,
				closable:true,
				resizable: false,
				border:true,
				autoLoad: null,
				listeners:
				{
					destroy:function()
					{
						parent.setWinEventsAccessNull();
					}
				},
				buttons:
				[
					{	
						text: StrClose,
						handler: function()
						{
							win.destroy();
						}
					}
				]
			}
		);
	}
	
	this.loadIFrame=function(id,r)
	{
		row=r;
		win.load({url: 'iframes/eventsAccess.php',params: {id: id}});
	}
	
	this.bootstrap=function(id,r)
	{
		setupWin();
		win.show();
		this.loadIFrame(id,r);		
	}
}