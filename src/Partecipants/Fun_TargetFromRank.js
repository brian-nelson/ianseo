Ext.onReady(function()
{
	var s=Ext.get('startSession');
	var d=Ext.get('endSession');
	
	s.on('change',function()
	{
		d.dom.value=s.dom.value;
	});
	
// check
	var btnOk=Ext.get('btnOk');
	btnOk.on('click',function()
	{
		var msg='';
		
	// sessione finale ok
		if (d.dom.value==0)
			msg=StrError;
		
	// il filtro non può essere vuoto
		if (Ext.get('filter').getValue()=='')
			msg=StrError;
		
	// rank iniziale ok
		if (!Ext.get('sourceRankFrom').getValue().match(/^[0-9]+$/))
			msg=StrError;
		
	// destinazione 
		if (!Ext.get('destFrom').getValue().match(/^[0-9]+$/))
			msg=StrError;
	
		if (!Ext.get('destTo').getValue().match(/^[0-9]+$/))
			msg=StrError;	
		
		if (msg!='')
			alert(msg);
		else
			Ext.get('frm').dom.submit();
	});
}
,window);