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
		
	// destinazione 
//		if (Ext.get('destFrom').getValue()=='')
//			msg=StrError;
	// sorgente
//		if (Ext.get('sourceFrom').getValue()=='')
//			msg=StrError;	
		
		if (msg!='')
			alert(msg);
		else
			Ext.get('frm').dom.submit();
	});
}
,window);