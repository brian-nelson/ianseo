Ext.ns('speaker');

speaker.ViewBBar=Ext.extend(speaker.View,
{		
	vscroll: 100
	,hscroll: 100
	
// @override
	,initComponent: function()
	{
		var me=this;
		
		Ext.applyIf(me,
		{
			bbar: 
			[
			 	{
			 		text: 'left'
			 		,handler: function()
			 		{
			 			var v=me.getView();
			 			
			 			//console.debug(v.scroller);
			 			v.scroller.dom.scrollLeft-=me.hscroll;
			 		}
			 	}
			 	,'-'
			 	,{
			 		text: 'right'
			 		,handler: function()
			 		{
			 			var v=me.getView();
			 			
			 			//console.debug(v.scroller);
			 			v.scroller.dom.scrollLeft+=me.hscroll;
			 		}
			 	}
			 	,'-'
			 	,{
			 		text: 'down'
			 		,handler: function()
			 		{
			 			var v=me.getView();
			 			
			 			//console.debug(v.scroller);
			 			v.scroller.dom.scrollTop+=me.vscroll;
			 		}
			 	}
			 	,'-'
			 	,{
			 		text: 'up'
			 		,handler: function()
			 		{
			 			var v=me.getView();
			 			
			 			//console.debug(v.scroller);
			 			v.scroller.dom.scrollTop-=me.vscroll;
			 		}
			 	}
			]
		});
		
		speaker.ViewBBar.superclass.initComponent.call(me);
	}
	

});