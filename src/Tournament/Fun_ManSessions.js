Ext.onReady(function()
{
	var dq=Ext.DomQuery;
	
	var oldKey=Ext.get('oldKey');
	var d_SesOrder=Ext.get('d_SesOrder');
	var d_SesType=Ext.get('d_SesType');
	var d_SesName=Ext.get('d_SesName');
	var d_SesTar4Session=Ext.get('d_SesTar4Session');
	var d_SesAth4Target=Ext.get('d_SesAth4Target');
	var d_SesFirstTarget=Ext.get('d_SesFirstTarget');
	var d_SesFollow=Ext.get('d_SesFollow');
	
	var orderInEdit=Ext.get('orderInEdit');
		
	var frmSave=Ext.get('frmSave');
	var btnSave=Ext.get('btnSave');
	var btnCancel=Ext.get('btnCancel');
	var btnDels=Ext.get(Ext.query('img[class^=del-]'));
	var sesRows=Ext.get(Ext.query('tr[id^=row-]'));
	var sesLinks=Ext.get(Ext.query('a[id^=link-]'));
	
// cambia gli stati delle caselle di input a seconda del tipo di sessione
	function updateStatus()
	{
		var v=d_SesType.dom.value;
		
		if (v=='Q' || v=='E')
		{
			d_SesFollow.dom.value=0;
			d_SesFollow.dom.readOnly=true;
			d_SesFollow.dom.className='disabled';
			
			d_SesTar4Session.dom.readOnly=false;
			d_SesAth4Target.dom.readOnly=false;
			d_SesFirstTarget.dom.readOnly=false;
			d_SesTar4Session.dom.className='';
			d_SesAth4Target.dom.className='';
			d_SesFirstTarget.dom.className='';
		}
		else if (v=='F')
		{
			d_SesFollow.dom.readOnly=false;
			d_SesFollow.dom.className='';
			
			d_SesTar4Session.dom.value=0;
			d_SesAth4Target.dom.value=0;
			d_SesFirstTarget.dom.value=1;
			
			d_SesTar4Session.dom.readOnly=true;
			d_SesTar4Session.dom.className='disabled';
			d_SesAth4Target.dom.readOnly=true;
			d_SesAth4Target.dom.className='disabled';
			d_SesFirstTarget.dom.readOnly=true;
			d_SesFirstTarget.dom.className='disabled';
		}
	}

// inizializza gli eventi
	function initEvents()
	{
	// il click su salva esegue il submit
		btnSave.on('click',function()
		{
			frmSave.dom.submit();
		});
		
	// il click su annulla fa resettare la form 
		btnCancel.on('click',function()
		{
			oldKey.dom.value='';
			d_SesOrder.dom.value=0;
			d_SesType.dom.value='Q';
			d_SesName.dom.value='';
			d_SesTar4Session.dom.value=0;
			d_SesAth4Target.dom.value=0;
			d_SesFirstTarget.dom.value=1;
			d_SesFollow.dom.value=0;
			orderInEdit.update('');
			
			updateStatus();
		});
		
	// il click sulla 'X' cancella la sessione
		btnDels.on('click',function()
		{
			//console.debug(this.className);return;
			var row=this.className;
			
			var id=row.split('-')[1];
			
			if (confirm(StrMsgAreYouSure))
			{
				window.location='ManSessions.php?Command=DEL&id=' + id;
			}
		});
		
		d_SesType.on('change',updateStatus)
			
	// handler per l'edit
		function editRow()
		{
			var row=this.id;
			
			var id=row.split('-')[1];
			
			oldKey.dom.value=id;
			d_SesOrder.dom.value=Ext.get('order-' + id).getValue();
			d_SesType.dom.value=id.split('_')[1];
			d_SesName.dom.value=Ext.get('name-' + id).getValue();
			d_SesTar4Session.dom.value=Ext.get('tar4session-'+id).getValue();
			d_SesAth4Target.dom.value=Ext.get('ath4target-'+id).getValue();
			d_SesFirstTarget.dom.value=Ext.get('firstTarget-'+id).getValue();
			d_SesFollow.dom.value=Ext.get('follow-'+id).getValue();
			
			orderInEdit.update(Ext.get('order-' + id).getValue() );
			
			updateStatus();
		}
		
	// il doppio click sulla riga oppure il click sul numero di sessione caricano i dati nelle caselle in alto
		sesLinks.on('click',editRow);
		sesRows.on('dblclick',editRow);
	}

// start
	updateStatus();
	initEvents();
}
,window);