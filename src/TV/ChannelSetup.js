function deleteChannel(obj) {
	$.confirm({
		title:'',
		content:MsgDelChannel,
		boxWidth: '50%',
		useBootstrap: false,
		type:'red',
		buttons: {
			cancel: {
				text: CmdCancel,
				btnClass: 'btn-blue' // class for the button
			},
			unset: {
				text: CmdConfirm,
				btnClass: 'btn-red', // class for the button
				action: function () {
					$.getJSON("ChannelsUpdate.php?act=delchannel&id="+$(obj).closest('tr').attr('refid'),
						function(data) {
							if(data.error==0) {
								location.reload();
							}
						});
				}
			}
		},
		escapeKey: true,
		backgroundDismiss: true
	});
}

function deleteSplit(obj) {
	$.confirm({
		title:'',
		content:MsgDelSplit,
		boxWidth: '50%',
		useBootstrap: false,
		type:'red',
		buttons: {
			cancel: {
				text: CmdCancel,
				btnClass: 'btn-blue' // class for the button
			},
			unset: {
				text: CmdConfirm,
				btnClass: 'btn-red', // class for the button
				action: function () {
					$.getJSON("ChannelsUpdate.php?act=delsplit&id="+$(obj).closest('tr').attr('refid')+"&side="+$(obj).closest("tr").attr('refside'),
						function(data) {
							if(data.error==0) {
								location.reload();
							}
						});
				}
			}
		},
		escapeKey: true,
		backgroundDismiss: true
	});
}

function AddSplit(obj) {
	$.confirm({
		title:'',
		content:MsgAddSplit,
		boxWidth: '50%',
		type:'red',
		useBootstrap: false,
		buttons: {
			cancel: {
				text: CmdCancel,
				btnClass: 'btn-blue' // class for the button
			},
			unset: {
				text: CmdConfirm,
				btnClass: 'btn-red', // class for the button
				action: function () {
					$.getJSON("ChannelsUpdate.php?act=newsplit&id="+$(obj).closest('tr').attr('refid'),
						function(data) {
							if(data.error==0) {
								location.reload();
							}
						});
				}
			}
		},
		escapeKey: true,
		backgroundDismiss: true
	});
}

function AddChannel() {
	$.confirm({
		title:'',
		content:MsgAddChannel,
		boxWidth: '50%',
		useBootstrap: false,
		type:'red',
		buttons: {
			cancel: {
				text: CmdCancel,
				btnClass: 'btn-blue' // class for the button
			},
			unset: {
				text: CmdConfirm,
				btnClass: 'btn-red', // class for the button
				action: function () {
					$.getJSON("ChannelsUpdate.php?act=newchannel",
						function(data) {
							if(data.error==0) {
								location.reload();
							}
						});
				}
			}
		},
		escapeKey: true,
		backgroundDismiss: true
	});
}

function update(obj, field) {
	RefId=$(obj).closest('tr').attr('refid');
	RefSide=$(obj).closest('tr').attr('refside');
	$.getJSON("ChannelsUpdate.php?act=update&fld="+field+"&val="+encodeURIComponent(obj.value)+'&id='+RefId+'&side='+RefSide,
		function(data) {
			if(data.error==0) {
				if(data.NewOrder!=undefined) {
					$('[refid='+RefId+'][refside='+RefSide+']').attr('refside', data.NewOrder)
				}
				if(data.TVRules!=undefined) {
					// we have TV rules to update!
					var Select=$(obj).closest('tr').find('.TvoRule');
					Select.empty();
					$.each(data.TVRules, function (i, item) {
						Select.append($('<option>', {
					        value: i,
					        text : item
					    }));
					});
				}
			} else {
				$.alert({
					title:'',
					type:'red',
					content:data.msg,
					escapeKey: true,
					boxWidth: '50%',
					useBootstrap: false,
					backgroundDismiss: true
				});
				if(obj.defaultValue!=undefined) {
					$(obj).val(obj.defaultValue);
				}
			}
		});
}
