function confUpdate(obj) {
	$(obj).closest('td').css('backgroundColor','');
	$.getJSON('./configure-updateWinners.php?item='+$(obj).attr('item')+'&cat='+$(obj).attr('cat')+'&pos='+$(obj).attr('pos')+'&club='+(obj.type=='checkbox' ? (obj.checked ? 1 : 0) : obj.value), function(data) {
		if(data.reload==1) {
			location.reload();
			return;
		}
		$(obj).closest('td').css('backgroundColor', data.error==0 ? 'green' : 'red');
	});
}

function alertUpdate(obj) {
	$.confirm({
		content: MsgConfirm,
		boxWidth: '50%',
		useBootstrap: false,
		title: '',
		buttons: {
			cancel: {
				text: CmdCancel,
				btnClass: 'btn-blue' // class for the button
			},
			unset: {
				text: CmdConfirm,
				btnClass: 'btn-red', // class for the button
				action: function () {
					confUpdate(obj);
				}
			}
		},
		escapeKey: true,
		backgroundDismiss: true
	})
}
