$(function() {

	$('.Irm-5 input').prop('disabled', 'disabled')
	$('.Irm-10 input').prop('disabled', 'disabled')
	$('.Irm-15 input').prop('disabled', 'disabled')
	$('.Irm-20 input').prop('disabled', 'disabled')

});

function IrmSet(obj) {
	if($(obj).attr('ref')==0) {
		var buttons={
			cancel: {
				text: TxtCancel,
				btnClass: 'btn-blue', // class for the button
			},
			dns: {
				text: TxtIrmDns,
				btnClass: 'btn-orange', // class for the button
				action: function () {
					$.getJSON('index-action.php?act=dns&id='+$(obj).closest('tr').attr('id').substr(4), function(data) {
						if(data.error==0) {
							$(obj).closest('tr').removeClass('Irm-0 Irm-5 Irm-10 Irm-15 Irm-20').addClass(data.class);
							$(obj).html(data.btn);
							$(obj).attr('ref', 10);
							$('.'+data.class+' input').prop('disabled', true);
						}
					});
				}
			},
			dnf: {
				text: TxtIrmDnf,
				btnClass: 'btn-green', // class for the button
				action: function () {
					$.getJSON('index-action.php?act=dnf&id='+$(obj).closest('tr').attr('id').substr(4), function(data) {
						if(data.error==0) {
							$(obj).closest('tr').removeClass('Irm-0 Irm-5 Irm-10 Irm-15 Irm-20').addClass(data.class);
							$(obj).html(data.btn);
							$(obj).attr('ref', 5);
							$('.'+data.class+' input').prop('disabled', true);
						}
					});
				}
			},
		};
	} else {
		var buttons={
			cancel: {
				text: TxtCancel,
				btnClass: 'btn-blue', // class for the button
			},
			unset: {
				text: TxtIrmUnset,
				btnClass: 'btn-dark', // class for the button
				action: function () {
					$.getJSON('index-action.php?act=unset&id='+$(obj).closest('tr').attr('id').substr(4), function(data) {
						if(data.error==0) {
							$(obj).closest('tr').removeClass('Irm-0 Irm-5 Irm-10 Irm-15 Irm-20').addClass(data.class);
							$(obj).html(data.btn);
							$(obj).attr('ref', 0);
							$('.'+data.class+' input').prop('disabled', false);
						}
					});
				}
			},
		};
	}
	$.confirm({
		content:'',
		boxWidth: '50%',
		useBootstrap: false,
		title:'',
		buttons: buttons,
		escapeKey: true,
		backgroundDismiss: true,

	});
}

