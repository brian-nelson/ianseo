Ext.ns('partecipantsApp');

partecipantsApp.Photo=function(p)
{
	var parent=p;
	
	var win=null;
	
	var row=null;
	
	function setupWin()
	{
		win=new Ext.Window
		(
			{
				title: '<div align="center">' + StrPhoto + '<div>',
				constrainHeader:true,
				width:220,
				height:350,
				closable:true,
				resizable: false,
				border:true,
				autoLoad: null,
				listeners:
				{
					destroy:function()
					{
						parent.checkPhoto(row);
						parent.setWinPhotoNull();
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
	
	this.beforeChangeContent=function()
	{
		parent.checkPhoto(row);	
	}
	
	this.loadIFrame=function(id,r)
	{
		row=r;
		win.load({url: 'iframes/photoManager.php',params: {id: id}});
	}
	
	this.bootstrap=function(id,r)
	{
		setupWin();
		win.show();
		this.loadIFrame(id,r);		
	}
}